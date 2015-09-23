<?php

namespace Eater\Order\State;

use Eater\Order\Util\OsProbe;
use Eater\Order\Util\PackageProvider\Wrapper;

class Package extends Desirable {

    protected $provider;
    protected $package;
    protected $state;
    protected $currentState;

    const INSTALLED = 0;
    const REMOVED = 1;

    public function __construct($package, $state, $provider)
    {
        $this->package = $package;
        $this->state = $state;

            var_dump($provider);
        if ($provider === null) {
            $this->provider = Wrapper::getPackageProvider();
        } else {
            $this->provider = Wrapper::getPackageProviderByName($provider);
        }
    }


    public function getDiff()
    {
        $currentState = $this->getCurrentState();

        if ($this->state !== $currentState) {
            $diff = $currentState === Package::REMOVED ? Diff::create : Diff::delete;
            return [new Diff($diff, 'Package "' . $this->package . '" ' . ($currentState === Package::REMOVED ? 'installed' : 'removed'))];
        }

        return [];
    }

    public function apply()
    {
        $currentState = $this->getCurrentState();

        if ($this->state !== $currentState) {
            switch ($this->state)
            {
                case Package::INSTALLED:
                    $this->provider->install($this->package);
                    break;
                case Package::REMOVED:
                    $this->provider->remove($this->package);
                    break;
            };
        }
    }

    private function getCurrentState()
    {
        if ($this->currentState === null) {
            $this->currentState = $this->provider->isInstalled($this->package) ? Package::INSTALLED : Package::REMOVED;
        }

        return $this->currentState;
    }

    public static function create($package, $install, $provider)
    {
        return new Package($package, $install ? Package::INSTALLED : Package::REMOVED, $provider);
    }
}
