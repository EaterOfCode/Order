<?php

namespace Eater\Order\State;

use Eater\Order\Runtime;

class Service extends Desirable {

    private $service;
    private $enabled;
    private $provider;

    public function __construct($service, $enabled, $provider)
    {
        $this->service = $service;
        $this->enabled = $enabled;

        if ($provider !== null) {
            $this->provider = Runtime::getCurrent()->getServiceProvider()->getByName($provider);
        } else {
            $this->provider = Runtime::getCurrent()->getServiceProvider()->getDefault();
        }
    }

    public function getDiff()
    {
        if (!$this->provider->has($this->service)) {
            $this->fail(sprintf("Service %s doesn't exist", $this->service));
            return [];
        }

        $diff = [];
        $isEnabled = $this->provider->isEnabled($this->service);
        $isRunning = $this->provider->isRunning($this->service);
        if ($this->enabled) {
            if (!$isEnabled) {
                $diff[] = new Diff(Diff::create, sprintf('Enabled service "%s"', $this->service));
            }

            if (!$isRunning) {
                $diff[] = new Diff(Diff::create, sprintf('Started service "%s"', $this->service));
            }
        } else {
            if ($isRunning) {
                $diff[] = new Diff(Diff::delete, sprintf('Stopped service "%s"', $this->service));
            }

            if ($isEnabled) {
                $diff[] = new Diff(Diff::delete, sprintf('Disabled service "%s"', $this->service));
            }
        }

        return $diff;
    }

    public function apply()
    {
        if (!$this->provider->has($this->service)) {
            $this->fail(sprintf("Service %s doesn't exist", $this->service));
        }

        $isEnabled = $this->provider->isEnabled($this->service);
        if ($this->enabled) {
            if (!$isEnabled) {
                $result = $this->provider->enable($this->service);
                if(!$this->handleExecResult($result))
                {
                    return;
                }
            }

            $isRunning = $this->provider->isRunning($this->service);
            if (!$isRunning) {
                $result = $this->provider->start($this->service);
                if(!$this->handleExecResult($result))
                {
                    return;
                }
            }
        } else {
            $isRunning = $this->provider->isRunning($this->service);
            if ($isRunning) {
                $result = $this->provider->stop($this->service);
                if(!$this->handleExecResult($result))
                {
                    return;
                }

            }

            if ($isEnabled) {
                $result = $this->provider->disable($this->service);
                if(!$this->handleExecResult($result))
                {
                    return;
                }
            }
        }
    }

    public function notify()
    {
        $this->provider->reload($this->service);
    }
}
