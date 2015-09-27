<?php

namespace Eater\Order\State;

use Eater\Order\Util\OsProbe;
use Eater\Order\Util\PackageProvider\Wrapper;
use Eater\Order\Runtime;

class Package extends Desirable {

    protected $provider;
    protected $package;
    protected $state;
    protected $currentState;
    protected $special;

    const INSTALLED = 0;
    const REMOVED = 1;

    public function __construct($package, $state, $provider, $special)
    {
        $this->package = $package;
        $this->state = $state;
        $this->special = $special;

        if ($provider === null) {
            $this->provider = Runtime::getCurrent()->getPackageProvider()->getDefault();
        } else {
            $this->provider = Runtime::getCurrent()->getPackageProvider()->getByName($provider);
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
        $result;

        if ($this->state !== $currentState) {
            switch ($this->state)
            {
                case Package::INSTALLED:
                    $result = $this->provider->install($this->package, $this->special);
                    break;
                case Package::REMOVED:
                    $result = $this->provider->remove($this->package, $this->special);
                    break;
            };

            if (!$result->isSuccess()) {
                $this->fail();
                echo "Package " . ($this->state === Package::INSTALLED ? 'install' : 'removal') . " failed with exit code ({$result->getReturnCode()})\n";
                echo "Executed: " . $result->getCommand() . "\nOutput:\n";
                echo implode("\n", $result->getOutput())  . "\n";
            }
        }
    }

    private function getCurrentState()
    {
        if ($this->currentState === null) {
            $this->currentState = $this->provider->isInstalled($this->package, $this->special) ? Package::INSTALLED : Package::REMOVED;
        }

        return $this->currentState;
    }

    public static function create($package, $install, $provider, $special)
    {
        return new Package($package, $install ? Package::INSTALLED : Package::REMOVED, $provider, $special);
    }
}
