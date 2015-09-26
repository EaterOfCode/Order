<?php

namespace Eater\Order\Util\PackageProvider;

use \Eater\Order\Util\OsProbe;

class Wrapper {

    private $providers = [];
    private $providerByOs = [];
    private $logger;
    private $os;
    private $default;

    public function __construct($logger, $providers, $os)
    {
        $this->logger = $logger;
        $this->os     = $os;

        foreach ($providers as $providerName => $provider) {
            if (class_exists($provider['class'])) {
                $providerClass = new $provider['class'];

                $this->providers[$providerName] = $providerClass;

                foreach ($provider['os'] as $defOs) {
                    $this->providerByOs[$defOs] = $providerClass;
                }

                $this->logger->addDebug(sprintf('Added package provider "%s"', $providerName), $provider);
            } else {
                $this->logger->addWarning(sprintf('Class "%s" doesn\'t exist for package provider "%s"', $provider['class'], $providerName));
            }
        }

        if (isset($this->providerByOs[$os])) {
            $this->default = $this->providerByOs[$os];
            $name = array_search($this->default, $this->providers);

            $this->logger->addDebug(sprintf('Selected "%s" as default package provider for this os: "%s"', $name, $os));
        } else {

        }
    }

    public function getByName($name)
    {
        return $this->providers[$name];
    }

    public function getDefault()
    {
        return $this->default;
    }
}
