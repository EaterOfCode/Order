<?php

namespace Eater\Order\Law\Wrapped;

use Eater\Order\Definition;
use Eater\Order\Law\Loader;

function file($path, $options = []) {
    $def = new Definition\File($path, $options);
    Loader::addDefinition($def);
    return $def;
}

function package($package, $options = [])
{
    $def = new Definition\Package($package, $options);
    Loader::addDefinition($def);
    return $def;
}

function which($which, $choices, $default = null)
{
    $result;
    foreach ($choices as $selector => $choice) {
        if ($choice instanceof Definition/Definition) {
            $choice->ignore();
        }

        if (strlen($selector) > 0 && $selector[0] == '/') {
            if (preg_match($selector, $which)) {
                $result = $choice;
            }
        } else if ($selector === $which) {
            $result = $choice;
        }
    }

    if ($result instanceof Definition/Definition) {
        $choice->notice();
    }

    if ($result === null) {
        if ($default !== null) {
            $result = $default;
        } elseif (isset($choices[0])) {
            $result = $choices[0];
        }
    }

    return $result;
}
