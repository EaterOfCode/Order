<?php

namespace Eater\Order;

use Eater\Order\Definition\Collection;
use Eater\Order\Law\Stream;
use Eater\Order\Law\Storage;
use Eater\Order\Config\Combined;
use Eater\Order\Paper\Collector;
use Eater\Order\Util\Provider;
use League\Flysystem\Filesystem;
use League\Flysystem\Adapter\Local;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Bramus\Monolog\Formatter\ColoredLineFormatter;

class Runtime {
    static private $current;

    static function getCurrent()
    {
        return static::$current;
    }

    private $logger;
    private $config;
    private $orderConfig;
    private $collection;
    private $packageProvider;
    private $serviceProvider;
    private $userProvider;
    private $workingDirectory;
    private $dossier;

    public function __construct()
    {
        static::$current = $this;

        $this->logger = new Logger('Order');
        $handler = new StreamHandler('php://stdout', Logger::DEBUG);
        $handler->setFormatter(new ColoredLineFormatter(null, "[%level_name%] %message% %context% %extra%\n"));
        $this->logger->pushHandler($handler);

        $this->collection = new Collection($this->logger);
        $this->dossier = new Collector();

        $this->logger->addDebug('Runtime constructed');
    }

    public function init($workingDirectory)
    {
        if (\posix_getuid() !== 0) {
            $this->logger->addError('Order not running as root, some functionality may fail to work.');
        }

        $this->workingDirectory = $workingDirectory;

        $this->orderConfig = new Combined($this->logger, [__DIR__ . '/../config/order', $workingDirectory . '/order']);

        $dossiers = array_merge($this->orderConfig->get('order-dossier'), $this->orderConfig->has('dossier') ? $this->orderConfig->get('dossier') : []);

        foreach ($dossiers as $dossier) {
            $this->dossier->addDossier(new $dossier);
        }

        $packageProviders = array_merge($this->orderConfig->get('order-package-provider'), $this->orderConfig->get('package-provider') ? $this->orderConfig->get('package-provider') : []);
        $serviceProviders = array_merge($this->orderConfig->get('order-service-provider'), $this->orderConfig->get('service-provider') ? $this->orderConfig->get('service-provider') : []);
        $userProviders    = array_merge($this->orderConfig->get('order-user-provider'), $this->orderConfig->get('user-provider') ? $this->orderConfig->get('user-provider') : []);

        $os = $this->dossier->get('os.distribution');

        $this->packageProvider = new Provider($this->logger, $packageProviders, $os, 'package');
        $this->serviceProvider = new Provider($this->logger, $serviceProviders, $os, 'service');
        $this->userProvider = new Provider($this->logger, $userProviders, $os, 'user');

        foreach ($this->orderConfig->get('order-include') as $include) {
            include_once $include;
        }

        if ($this->orderConfig->has('include')) {
            foreach ($this->orderConfig->get('include') as $include) {
                include_once $workingDirectory . '/' . $include;
            }
        }

        Stream::register('law');
        Stream::setLogger($this->logger);
        Stream::setStorage(
            new Storage(
                new Filesystem(
                    new Local('/')
                ),
                ['']
            )
        );
    }

    public function addDefinition($defition)
    {
        $this->collection->add($defition);
    }

    public function load($file)
    {
        include 'law://' .  $file;
    }

    public function apply($commit = false)
    {
        $errors = $this->collection->validate();
        if (!empty($errors)) {
            return false;
        }

        $returnValue = true;

        $actionChain = $this->collection->getActionChain();

        $stateByIdentifier = [];
        foreach ($actionChain as $definition) {
            $state = $definition->getDesirableState();
            $stateByIdentifier[$definition->getIdentifier()] = $state;

            $requires = [];
            foreach ($definition->getRequires() as $require) {
                $requires[] = $stateByIdentifier[$require->getIdentifier()];
            }


            $state->setRequires($requires);

            if ($state->areRequiresSatisfied()) {
                $diff = $state->getDiff();
                foreach($diff as $partialDiff)
                {
                    echo $partialDiff->getPretty();
                }

                if ($state->failed()) {
                    $this->logger->addWarning(sprintf('Couldn\'t apply state "%s": %s', $definition->getIdentifier(), $state->getReason()), $state->getReasonExtra());
                    $returnValue = false;
                }

                if ($commit) {
                    $state->apply();

                    if ($state->failed()) {
                        $this->logger->addWarning(sprintf('Couldn\'t apply state "%s": %s', $definition->getIdentifier(), $state->getReason()), $state->getReasonExtra());
                        $returnValue = false;
                    }
                }

                if (!empty($diff)) {
                    foreach ($definition->getToNotify() as $notifyReceiver) {
                        $notify = $stateByIdentifier[$notifyReceiver->getIdentifier()];
                        echo 'Notifying "' . $notifyReceiver->getIdentifier() . '"' . "\n";

                        if ($commit) {
                            $notify->notify();
                        }
                    }
                }
            } else {
                $this->logger->addWarning("Couldn't apply state: " . $definition->getIdentifier() . " because of failed dependecies");
            }

        }

        return $returnValue;
    }

    public function getPackageProvider()
    {
        return $this->packageProvider;
    }

    public function getServiceProvider()
    {
        return $this->serviceProvider;
    }

    public function getUserProvider()
    {
        return $this->userProvider;
    }

    public function getDossier()
    {
        return $this->dossier;
    }
}
