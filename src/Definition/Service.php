<?php

namespace Eater\Order\Definition;

use Eater\Order\State\Service as ServiceState;

class Service extends Definition {

    private $enabled;
    private $service;
    protected $type = 'service';
    private $provider;
    private $name;

    public function __construct($service, $options = [])
    {
        $this->service = $service;
        $this->name = $service;

        if (isset($options['name'])) {
            $this->name = $options['name'];
        }

        if (isset($options['enabled'])) {
            $this->enabled = $options['enabled'];
        } else if (isset($options['disabled']) && $options['disabled']) {
            $this->enabled = false;
        }

        if (isset($options['provider'])) {
            $this->provider = $options['provider'];
        }

        $this->setIdentifier($this->name);
    }

    public function enable()
    {
        $this->enabled = true;

        return $this;
    }

    public function disable()
    {
        $this->enabled = false;

        return $this;
    }

    public function provider($provider)
    {
        $this->provider = $provider;

        return $this;
    }

    public function name($name)
    {
        $this->name = $name;
        $this->setIdentifier($name);

        return $this;
    }

    public function getDesirableState()
    {
        return new ServiceState($this->service, $this->enabled, $this->provider);
    }

    public function validate()
    {
        if ($this->enabled === null) {
            return ["Not specified if service should be enabled or disabled"];
        }

        return [];
    }
}
