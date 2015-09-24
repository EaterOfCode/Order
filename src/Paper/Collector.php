<?php

class Collector {

    private $dossiers = [];

    public addDossier($dossier)
    {
        $this->dossiers[] = $dossier;
    }

    public function get($name)
    {
        foreach ($this->dossiers as $dossier) {
            if ($dossier->has($name)) {
                return $this->get($name);
            }
        }

        return null;
    }
}
