<?php

namespace Eater\Order\Law;

class Storage {
    
    private $flySystem;
    private $lawFolders = [];

    public function setLawFolders($lawFolders)
    {
        $this->lawFolders = $lawFolders;
    }

    public function getLawFolders()
    {
        return $this->lawFolders;   
    }

    public function addLawFolder($lawFolder)
    {
        $this->lawFolders[] = $lawFolder;
    }

    public function getFlySystem()
    {
        return $this->flySystem;
    }

    public function setFlySystem($flySystem)
    {
        $this->flySystem = $flySystem;
    }

    public function __construct($flySystem, $lawFolders)
    {
        $this->flySystem  = $flySystem;
        $this->lawFolders = $lawFolders;
    }

    public function hasLawFile($path)
    {
        $lawFolders = $this->getLawFolders();
        $flySystem  = $this->getFlySystem();
        foreach ($lawFolders as $lawFolder) {
            if ($flySystem->has($lawFolder . $path)) {
                return true;
            }
        }

        return false;
    }

    public function getLaw($path)
    {
        $lawFolders = $this->getLawFolders();
        $flySystem  = $this->getFlySystem();
        foreach ($lawFolders as $lawFolder) {

            $lawPath = $lawFolder . $path;
            if ($flySystem->has($lawPath)) {
                $lawFile = $flySystem->read($lawPath);
                $wrapped = $this->wrapLawFile($lawFile, $lawPath);

                return $wrapped;
            }
        }

        throw FileNotFoundException($path, $lawFolders);;
    }

    private function wrapLawFile($lawFile, $lawPath)
    {
        return new Wrapped($lawFile, $lawPath);
    }
}
