<?php

namespace Eater\Order\Law;

class FileNotFound extends \Exception {
    public function __construct($file)
    {
        parent::__construct('Not able to find law file on ' . $file);
    }
}
