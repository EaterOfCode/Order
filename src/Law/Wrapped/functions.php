<?php

namespace Eater\Order\Law\Wrapped;

use Eater\Order\Definition;
use Eater\Order\Law\Loader;

function file($path, $source = null) {
    $def = new Definition\File($path, $source);
    Loader::addDefinition($def);
    return $def;
}
