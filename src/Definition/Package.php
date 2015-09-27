<?php

namespace Eater\Order\Definition;

use Eater\Order\State\Package as PackageState;

class Package extends Definition {

    protected $package;
    protected $name;
    protected $install = true;
    protected $provider = null;
    protected $type = 'package';
    protected $special = [];

    public function __construct($package, $options = [])
    {
        $this->package = $package;
        $this->name    = $package;

        if (isset($options['name'])) {
            $this->name = $options['name'];
        }

        if (isset($options['install'])) {
            $this->install = $options['install'];
        } else if (isset($options['remove']) && $options['remove']) {
            $this->install = false;
        }

        if (isset($options['provider'])) {
            $this->provider = $options['provider'];
        }

        $this->special = array_diff($options, ["provider" => null, "install" => null, "remove" => null, "name" => null]);
        $this->setIdentifier($this->name);
    }

    public function name($name)
    {
        $this->name = $name;
        $this->setIdentifier($name);
    }

    public function install()
    {
        $this->install = true;
    }

    public function remove()
    {
        $this->install = false;
    }

    public function provider($provider)
    {
        $this->provider = $provider;
    }

    public function getDesirableState()
    {
        return PackageState::create($this->package, $this->install, $this->provider, $this->special);
    }

    public function validate()
    {
        return [];
    }
}
