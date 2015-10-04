<?php

namespace Eater\Order\State;

abstract class Desirable {

    private $requires    = [];
    private $fail        = false;
    private $reason      = "";
    private $reasonExtra = [];

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

    public function fail($reason = "No reason given", $reasonExtra = [])
    {
        $this->fail         = true;
        $this->reason       = $reason;
        $this->reasonExtra  = $reasonExtra;
    }

    public function failed()
    {
        return $this->fail;
    }

    public function getReason()
    {
        return $this->reason;
    }

    public function getReasonExtra()
    {
        return $this->reasonExtra;
    }

    public function isCurrentState() {
        throw new \RuntimeException("Not implemented");
    }

    public function apply()
    {
        throw new \RuntimeException("Not implemented");
    }

    public function handleExecResult($result)
    {
        if ($result !== null && !$result->isSuccess()) {
            $this->fail(sprintf('executing "%s" failed (%d)', $result->getCommand(), $result->getReturnCode()), $result->getOutput());
            return false;
        }

        return true;
    }
}
