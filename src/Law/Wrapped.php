<?php

namespace Eater\Order\Law;

class Wrapped {

    private $path;
    private $contents;
    private $wrappedContents;

    public function __construct($contents, $path)
    {
        $this->contents = $contents;
        $this->path     = $path;
    }

    public function load()
    {
        $this->wrappedContents = preg_replace('/\<\?(php)?/', '<?php namespace ' . __NAMESPACE__ . '\\Wrapped;', $this->getContents(), 1);
    }

    public function getWrappedContents()
    {
        if ($this->wrappedContents === null) {
            $this->load();
        }

        return $this->wrappedContents;
    }

    public function getPath()
    {
        return $this->path;
    }

    public function getContents()
    {
        return $this->contents;
    }
}
