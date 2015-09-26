<?php

namespace Eater\Order\Config;

class Memory {

    protected $logger;
    protected $config;

    public function __construct($logger, $array)
    {
        $this->logger = $logger;
        $this->config = $array;
    }

    public function has($name)
    {
        $names = explode(".", $name);

        $current = $this->config;
        foreach ($names as $name) {
            if (isset($current[$name])) {
                $current = $current[$name];
            } else {
                return false;
            }
        }

        return true;
    }

    public function get($name)
    {
        $names = explode(".", $name);

        $current = $this->config;
        foreach ($names as $name) {
            if (isset($current[$name])) {
                $current = $current[$name];
            } else {
                return null;
            }
        }

        return $current;
    }
}
