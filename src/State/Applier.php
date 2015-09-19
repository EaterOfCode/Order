<?php

namespace Eater\Order\State;

class Applier {
    static public function apply($states)
    {
        foreach ($states as $state) {
            if (! $state->isCurrentState() ) {
                $state->apply();
            }
        }
    }
}
