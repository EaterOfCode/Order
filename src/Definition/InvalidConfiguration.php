<?php

namespace Eater\Order\Definition;

class InvalidConfiguration extends \Exception {
    public function __construct($message, $definition)
    {
        parent::__construct("Definition '" . $definition->getIdentifier() . "' failed validating with the error: " . $message);
    }
}
