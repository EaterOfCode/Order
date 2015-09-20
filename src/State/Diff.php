<?php

namespace Eater\Order\State;

class Diff {

    const create = 0;
    const change = 1;
    const delete = 2;

    static function getStateColor($state)
    {
        switch ($state)
        {
            case Diff::create:
                return "\033[32m";
            case Diff::delete:
                return "\033[31m";
            case Diff::change:
                return "\033[33m";
        }

        return "\033[0m";
    }

    public $state;
    public $description;

    function __construct($state, $description)
    {
        $this->state = $state;
        $this->description = $description;
    }

    public function getPretty()
    {
       return self::getStateColor($this->state) . $this->description . "\033[0m\n";
    }
}
