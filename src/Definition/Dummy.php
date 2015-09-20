<?php

namespace Eater\Order\Definition;

class Dummy extends Definition {

    private $errors = [];

    public function __construct($identifier)
    {
        $this->setIdentifier($identifier);
    }

    public function setErrors($errors)
    {
        $this->errors = $errors;
    }

    public function validate()
    {
        return $this->errors;
    }
};
