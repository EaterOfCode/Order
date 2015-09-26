<?php

namespace Eater\Order\Config;

class Combined {

    private $configs = [];
    private $logger;

    public function __construct($logger, $configFiles)
    {
        $this->logger = $logger;
        foreach ($configFiles as $configFile) {
            if (is_file($configFile)) {
                if (substr($configFile, strrpos($configFile, '.') + 1) == 'yaml') {
                    $this->configs[] = new Yaml($logger, $configFile);
                } else {
                    $this->configs[] = new Json($logger, $configFile);
                }
            } else if (is_file($configFile . '.json')) {
                $this->configs[] = new Json($logger, $configFile . '.json');
            } else if (is_file($configFile . '.yaml')) {
                $this->configs[] = new Yaml($logger, $configFile . '.yaml');
            } else {
                $logger->addDebug(sprintf('Can\'t locate file like "%s{,.yaml,.json}"', $configFile));
            }
        }

        // make sure last items in array get picked first
        $this->configs = array_reverse($this->configs);
    }

    public function has($name)
    {
        foreach ($this->configs as $config) {
            if ($config->has($name)) {
                return true;
            }
        }

        return false;
    }

    public function get($name)
    {
        foreach ($this->configs as $config) {
            if ($config->has($name)) {
                return $config->get($name);
            }
        }
    }
}
