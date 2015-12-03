<?php

namespace Eater\Order\Util;

class Provider {

    private $providers = [];
    private $providerByOs = [];
    private $logger;
    private $os;
    private $osVersion;
    private $default;

    public function __construct($logger, $providers, $os, $what)
    {
        $this->logger = $logger;
        $this->os     = $os;

        foreach ($providers as $providerName => $provider) {
            if (class_exists($provider['class'])) {
                $providerClass = new $provider['class'];

                $this->providers[$providerName] = $providerClass;
                $this->logger->addDebug(sprintf('Added %s provider "%s"', $what, $providerName), $provider);

                if (isset($provider['os']) && in_array($os, $provider['os'])) {
                    $this->logger->addDebug(sprintf('Selected "%s" as default %s provider by os: %s', $providerName, $what, $os));
                    $this->default = $providerClass;
                } elseif ($this->default === null && isset($provider['binary']) && ExecResult::createFromCommand('which '. escapeshellarg($provider['binary']))->isSuccess()) {
                    $this->logger->addDebug(sprintf('Selected "%s" as default %s provider by binary: %s', $providerName, $what, $provider['binary']));
                    $this->default = $providerClass;
                }
            } else {
                $this->logger->addWarning(sprintf('Class "%s" doesn\'t exist for %s provider "%s"', $provider['class'], $what, $providerName));
            }
        }

        if ($this->default === null) {
            $this->logger->addAlert(sprintf('No default %s provider found.', $what));
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
