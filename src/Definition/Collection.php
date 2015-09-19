<?php

namespace Eater\Order\Definition;

class Collection {

    private $definitions = [];

    public function register(Definition $definition)
    {
        $this->definitions[$definition->getIdentifier()] = $definition;
    }

    public function getDefinitions() {
        return array_values($this->definitions);
    }

    public function getDefinitionByIdentifier($identifier)
    {
        return isset($this->definitions[$identifier]) ? $this->definitions[$identifier] : null;
    }
}
