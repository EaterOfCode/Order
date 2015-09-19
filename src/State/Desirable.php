<?php

namespace \Eater\Order\State;

abstract class Desirable {

    public function isCurrentState() {
        throw new \RuntimeException("Not implemented");
    }

    public function apply()
    {
        throw new \RuntimeException("Not implemented");
    } 

}
