<?php

namespace Eater\Order\Definition;

class CircularRequire extends \Exception {
    public function __construct($identifier, $definition, $path)
    {
        parent::__construct('Found circular require for the following definitions: ' . implode(",", $path));
    }
}
