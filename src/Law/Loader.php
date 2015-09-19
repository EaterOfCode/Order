<?php

namespace Eater\Order\Law;

use Eater\Order\Definition\Collection;

class Loader {

    static private $definitions;

    static public function load($path)
    {
        $path = realpath($path);

        static::clearDefinitions();
        // for the logic behind this see Stream.php
        include 'law://' . $path;
        return static::getDefinitions();
    }

    static public function registerFunctionFile($file)
    {
        // yolo
        include_once $file;
    }

    static public function addDefinition($definition)
    {
        static::$definitions->add($definition);
    }

    static private function getDefinitions()
    {
        return static::$definitions;
    }
    
    static private function clearDefinitions()
    {
        static::$definitions = new Collection();
    }

    static public function boot()
    {
        static::registerFunctionFile(__DIR__ . '/Wrapped/functions.php');
    }
}

Loader::boot();
