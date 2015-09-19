<?php

namespace Eater\Order\Law;

class InvalidSyntax extends \Exception {
    public function __construct($phpLOutput, $file)
    {
        parent::__construct('Syntax errors in ' . $file . "\n\n" . implode("\n", $output));
    }
}
