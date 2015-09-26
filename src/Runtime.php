<?php

namespace Eater\Order;

use Eater\Order\Definition\Collection;
use Eater\Order\Law\Stream;
use Eater\Order\Law\Storage;
use Eater\Order\Config\Combined;
use Eater\Order\Paper\Collector;
use Eater\Order\Util\PackageProvider\Wrapper;
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
    private $packageWrapper;
    private $workingDirectory;
    private $dossier;

    public function __construct()
    {
        static::$current = $this;

        $this->collection = new Collection($this->logger);
        $this->dossier = new Collector();

        $this->logger = new Logger('Order');
        $handler = new StreamHandler('php://stdout', Logger::DEBUG);
        $handler->setFormatter(new ColoredLineFormatter(null, "[%level_name%] %message% %context% %extra%\n"));
        $this->logger->pushHandler($handler);
        $this->logger->addDebug('Runtime constructed');
    }

    public function init($workingDirectory)
    {
        $this->workingDirectory = $workingDirectory;

        $this->orderConfig = new Combined($this->logger, [__DIR__ . '/../config/order', $workingDirectory . '/order']);

        $dossiers = array_merge($this->orderConfig->get('order-dossier'), $this->orderConfig->has('dossier') ? $this->orderConfig->get('dossier') : []);

        foreach ($dossiers as $dossier) {
            $this->dossier->addDossier(new $dossier);
        }

        $packageProviders = array_merge($this->orderConfig->get('order-package-provider'), $this->orderConfig->get('package-provider') ? $this->orderConfig->get('package-provider') : []);

        $this->packageWrapper = new Wrapper($this->logger, $packageProviders, $this->dossier->get('os.distribution'));

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
                foreach($state->getDiff() as $diff)
                {
                    echo $diff->getPretty();
                }

                if ($commit) {
                    $state->apply();
                }
            } else {
                echo "Couldn't apply state: " . $definition->getIdentifier() . " because of failed dependecies\n";
            }
        }
    }

    public function getPackageProvider()
    {
        return $this->packageWrapper;
    }
}
