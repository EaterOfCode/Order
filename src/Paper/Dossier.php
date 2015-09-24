<?php

namespace Eater\Order\Paper;

interface Dossier {

    public function get($name);
    public function has($name);
}
