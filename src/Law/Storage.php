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

    public function getLawFile($path)
    {
        $lawFolders = $this->getLawFolders();
        $flySystem  = $this->getFlySystem();
        foreach ($lawFolders as $lawFolder) {
            if ($flySystem->has($lawFolder . $path)) {
                $lawFile = $flySystem->read($lawFolder . $path);

                return $this->wrapLawFile($lawFile);
            }
        }

        throw FileNotFoundException($path, $lawFolders);;
    }

    private function wrapLawFile($lawFile)
    {
        return preg_replace('/\<\?(php)?/', '<?php namespace ' . __NAMESPACE__ . '\\Wrapped;', $lawFile, 1);   
    }
}
