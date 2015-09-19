<?php

namespace Eater\Order\Definition;

abstract class Definition {
    protected $require            = [];
    protected $type                = 'dummy';
    protected $identifier          = "";
    protected $isRequire           = false;

    public function requires(Definition $defintion)
    {
        $defintion->isRequire = true;
        $this->require[] = $defintion;
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
}
