<?php

namespace Eater\Order\Definition;

class UnresolvedRequiredDefinition extends \Exception {
    public function __construct($identifier, $definition)
    {
        parent::__construct('Failed to resolve the required definition "' . $identifier .  '"');
    }
}
