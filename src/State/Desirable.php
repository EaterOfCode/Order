<?php

namespace Eater\Order\State;

abstract class Desirable {

    private $requires = [];
    private $fail     = false;

    public function setRequires($requires)
    {
        $this->requires = $requires;
    }

    public function areRequiresSatisfied()
    {
        foreach ($this->requires as $require)
        {
            if ($require->failed()) {
                $this->fail();
                return false;
            }
        }

        return true;
    }

    public function fail()
    {
        $this->fail = true;
    }

    public function failed()
    {
        return $this->fail;
    }

    public function isCurrentState() {
        throw new \RuntimeException("Not implemented");
    }

    public function apply()
    {
        throw new \RuntimeException("Not implemented");
    }

}
