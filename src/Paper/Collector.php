<?php

namespace Eater\Order\Paper;

class Collector {

    private $dossiers = [];

    public function addDossier($dossier)
    {
        $this->dossiers[] = $dossier;
    }

    public function get($name)
    {
        foreach ($this->dossiers as $dossier) {
            if ($dossier->has($name)) {
                return $dossier->get($name);
            }
        }

        return null;
    }
}
