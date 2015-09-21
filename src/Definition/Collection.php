<?php

namespace Eater\Order\Definition;

class Collection {

    private $definitions                     = [];
    private $definitionByIdentifier          = [];
    private $definitionByName                = [];
    private $requiresDefinitions             = [];
    private $actionChain                     = [];

    public function add(Definition $definition)
    {
        $this->definitions[] = $definition;
    }

    public function getDefinitions() {
        return $this->definitions;
    }

    protected function populateReferenceArrays()
    {
        $errors = [];
        $byName = [];
        $byId   = [];

        foreach ($this->definitions as $definition)
        {
            if ($definition->getIgnored()) {
                continue;
            }

            if ($definition->isRequire()) {
                $this->requiresDefinitions[] = $definition;
            } else {
                $id   = $definition->getIdentifier();

                if (!isset($byId[$id])) {
                    $byId[$id] = $definition;
                } else {
                    $errors[] = new NonUniqueIdentifier($id, $definition, $byId[$id]);
                }
            }
        }

        $this->definitionByIdentifier = $byId;

        return $errors;
    }

    public function validate()
    {
        $errors = $this->populateReferenceArrays();

        $actionChain          = [];
        $actionableDefinition = array_values($this->definitionByIdentifier);

        foreach ($actionableDefinition as $def) {

            if (in_array($def->getIdentifier(), $actionChain)) {
                continue;
            }

            list($chainPart, $newErrors) = $this->getRecursiveRequired($def);

            $actionChain = array_merge($actionChain, $chainPart);
            $errors      = array_merge($errors, $newErrors);
        }

        $this->actionChain = array_values(array_unique($actionChain));

        return $errors;
    }

    public function getActionChain()
    {
        return array_map(
            function($action){
                return $this->definitionByIdentifier[$action];
            },
            $this->actionChain
        );
    }

    private function getRecursiveRequired($def, $path = [])
    {

        $errors = $this->validateDefinition($def);

        if (!empty($errors)) {
            return [[], $errors];
        }

        $required = [];
        $requires = $def->getRequires();
        $path[]   = $def->getIdentifier();

        foreach ($requires as $requiredDef) {
            $id = $requiredDef->getIdentifier();

            if (in_array($id, $path)) {
                $errors[] = new CircularRequire($id, $def, $path);
                continue;
            }

            if (isset($this->definitionByIdentifier[$id])) {
                list($newRequired, $newErrors) = $this->getRecursiveRequired($this->definitionByIdentifier[$id], $path);

                $required = array_merge($required, $newRequired);
                $errors   = array_merge($errors, $newErrors);
            } else {
                $errors[] = new UnresolvedRequiredDefinition($id, $def);
            }
        }

        $required[] = $def->getIdentifier();
        array_unique($required);

        return [$required, $errors];
    }

    private function getIdentifiersFromDefinitions($arr)
    {
        return array_map(function($def){
            return $def->getIdentifier();
        }, $arr);
    }

    private function validateDefinition($def)
    {
        return array_map(
            function($error) use ($def) {
                return new InvalidConfiguration($error, $def);
            },
            $def->validate()
        );
    }
}
