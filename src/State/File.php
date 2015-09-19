<?php

namespace Eater\Order\State;

class File extends Desirable {
    
    private $contents;
    private $file

    function isCurrentState()
    {
        if (!file_exists($this->file) || !is_file($this->file)) {
            return false;
        }

        $currentData = file_get_contents($file);

        return $currentData === $contents; 
    }

    function apply()
    {
        file_put_contents($this->file, $this->contents);
    }

}
