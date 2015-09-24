<?php

namespace Eater\Order;

use Eater\Order\Definition\Collection;
use Eater\Order\Law\Stream;
use Eater\Order\Law\Storage;
use League\Flysystem\Filesystem;
use League\Flysystem\Adapter\Local;
use Eater\Order\Util\PackageProvider\Wrapper;

class Runtime {
    static private $current;

    static function getCurrent()
    {
        return static::$current;
    }

    private $config;
    private $collection;

    public function __construct()
    {
        static::$current = $this;
        $this->collection = new Collection();
    }

    public function init()
    {
        Wrapper::load();
        Stream::register('law');

        Stream::setStorage(
            new Storage(
                new Filesystem(
                    new Local('/')
                ),
                ['']
            )
        );
    }

    public function addDefinition($defition)
    {
        $this->collection->add($defition);
    }

    public function load($file)
    {
        include 'law://' .  $file;
    }

    public function apply($commit = false)
    {
        $errors = $this->collection->validate();
        if (!empty($errors)) {
            return false;
        }

        $actionChain = $this->collection->getActionChain();

        $stateByIdentifier = [];
        foreach ($actionChain as $definition) {
            $state = $definition->getDesirableState();
            $stateByIdentifier[$definition->getIdentifier()] = $state;

            $requires = [];
            foreach ($definition->getRequires() as $require) {
                $requires[] = $stateByIdentifier[$require->getIdentifier()];
            }

            $state->setRequires($requires);

            if ($state->areRequiresSatisfied()) {
                foreach($state->getDiff() as $diff)
                {
                    echo $diff->getPretty();
                }

                if ($commit) {
                    $state->apply();
                }
            } else {
                echo "Couldn't apply state: " . $definition->getIdentifier() . " because of failed dependecies\n";
            }
        }
    }
}
