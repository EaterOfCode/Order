<?php

namespace Eater\Order\Definition;

use Eater\Order\State\File as StateFile;

class File extends Definition {

    protected $file;
    protected $fileContents;
    protected $type = 'file';
    protected $fileSource;

    public function __construct($file, $source = null)
    {
        $this->file = $file;

        if ($source !== null) {
            $this->source = $source;
        }

        $this->setIdentifier($file);
    }

    public function contents($contents)
    {
        $this->fileContents = $contents;
        $this->fileSource = null;
        return $this;
    }

    public function source($source)
    {
        $this->fileSource = $source;
        $this->fileContents = null;

        return $this;
    }

    public function validate()
    {
        if ($this->fileSource === null && $this->fileContents === null) {
            return ["No content or source set for file: " . $this->file];
        }

        return [];
    }

    public function getDesirableState()
    {
        if ($this->fileSource !== null) {
            return StateFile::createFromSource($this->file, $this->fileSource);
        } else {
            return StateFile::createFromContents($this->file, $this->fileContents);
        }
    }
}
