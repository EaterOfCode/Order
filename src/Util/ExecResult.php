<?php

namespace Eater\Order\Util;

class ExecResult {

    private $command;
    private $output;
    private $returnCode;

    public function __construct($command, $output, $returnCode)
    {
        $this->command = $command;
        $this->output  = $output;
        $this->returnCode = $returnCode;
    }

    public function getCommand()
    {
        return $this->command;
    }

    public function getOutput()
    {
        return $this->output;
    }

    public function getReturnCode()
    {
        return $this->returnCode;
    }

    public function isSuccess()
    {
        return $this->returnCode === 0;
    }

    public static function createFromCommand($command)
    {
        exec($command, $output, $returnCode);

        return new self($command, $output, $returnCode);
    }

}
