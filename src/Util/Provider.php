<?php

namespace Eater\Order\Util;

class Provider {

    private $providers = [];
    private $providerByOs = [];
    private $logger;
    private $os;
    private $default;

    public function __construct($logger, $providers, $os, $what)
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

                $this->logger->addDebug(sprintf('Added %s provider "%s"', $what, $providerName), $provider);
            } else {
                $this->logger->addWarning(sprintf('Class "%s" doesn\'t exist for %s provider "%s"', $provider['class'], $what, $providerName));
            }
        }

        if (isset($this->providerByOs[$os])) {
            $this->default = $this->providerByOs[$os];
            $name = array_search($this->default, $this->providers);

            $this->logger->addDebug(sprintf('Selected "%s" as default %s provider for this os: "%s"', $name, $what, $os));
        } else {
            $this->logger->addAlert(sprintf('No default %s provider for this os: "%s"', $what, $os));
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
