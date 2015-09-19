<?php

namespace Eater\Order\Definition;

class Dummy extends Definition {
    public function __construct($identifier)
    {
        $this->setIdentifier($identifier);
    }

    public function validate()
    {
        return [];
    }
};
