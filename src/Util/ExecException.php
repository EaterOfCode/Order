<?php

namespace Eater\Order\Util;

class ExecException extends \Exception {

    private $command;
    private $output;
    private $returnCode;

    public function __construct($result)
    {
        $this->command    = $result->getCommand();
        $this->returnCode = $result->getReturnCode();
        $this->output     = $result->getOutput();

        parent::__construct(sprintf('Executing command "%s" failed (%d)', $this->command, $this->returnCode));
    }

}
