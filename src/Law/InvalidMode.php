<?php

namespace Eater\Order\Law;

class InvalidMode extends \Exception {
    public function __construct($mode, $file) {
        parent::__construct("Can't use mode '{$mode}' on a read-only stream, only 'r', 'rb' or 'rt' is allowed, on file '$file'");
    }
}
