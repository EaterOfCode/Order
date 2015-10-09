<?php

namespace Eater\Order\Definition;

abstract class Definition {
    protected $require             = [];
    protected $type                = 'dummy';
    protected $identifier          = "";
    protected $isRequire           = false;
    protected $isIgnored           = false;
    protected $toNotify            = [];

    public function requires(Definition $defintion)
    {
        $defintion->isRequire = true;
        $this->require[] = $defintion;
        return $this;
    }

    public function notify(Definition $toNotify)
    {
        $toNotify->ignore();
        $this->toNotify[] = $toNotify;
        return $this;
    }

    protected function setIdentifier($identifier)
    {
        $this->identifier = $this->type . ':' . $identifier;
    }

    public function getRequires()
    {
        return $this->require;
    }

    public function getIdentifier()
    {
        return $this->identifier;
    }

    public function getDesirableState()
    {
        throw new \RuntimeException("Not implemented");
    }

    public function isRequire()
    {
        return $this->isRequire;
    }

    public function ignore()
    {
        $this->isIgnored = true;

        return $this;
    }

    public function notice()
    {
        $this->isIgnored = false;

        return $this;
    }

    public function getIgnored()
    {
        return $this->isIgnored;
    }

    public function getToNotify()
    {
        return $this->toNotify;
    }
}
