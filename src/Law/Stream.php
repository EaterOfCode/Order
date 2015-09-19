<?php

namespace Eater\Order\Law;

class Stream {

    private static $storage;

    public static function getStorage()
    {
        return static::$storage;
    }

    public static function setStorage($storage)
    {
        static::$storage = $storage;   
    }

    public static function register($name)
    {
        stream_wrapper_register($name, get_called_class());
    }

    private $path;
    private $law;
    private $position;
    private $wrappedContents;

    private function getContents()
    {
        if ($this->wrappedContents === null) {
            $this->wrappedContents = $this->law->getWrappedContents();
        }

        return $this->wrappedContents;
    }

    function stream_stat()
    {
        return [
            'size' => strlen($this->getContents())
        ];
    }

    // functions taken from http://php.net/manual/en/stream.streamwrapper.example-1.php

    function stream_open($path, $mode, $options, &$opened_path)
    {
        $this->path = substr($path, strpos($path, '://') + 3);
        $storage = static::getStorage();

        // Law Stream can only be read
        if (!in_array($mode, ['r', 'rb', 'rt'])) {
            throw new InvalidMode($mode, $path);
        }

        // do simple checking if this is reading only
        if (!$storage->hasLawFile($this->path)) {
             return false;
        }

        $this->law = $storage->getLaw($this->path);
        $this->position = 0;

        return true;
    }

    function stream_read($count)
    {
        $ret = substr($this->getContents(), $this->position, $count);
        $this->position += strlen($ret);
        return $ret;
    }

    function stream_write($data)
    {
        // this is a readonly stream
        return false;
    }

    function stream_tell()
    {
        return $this->position;
    }

    function stream_eof()
    {
        return $this->position >= strlen($this->getContents());
    }

    function stream_seek($offset, $whence)
    {
        switch ($whence) {
        case SEEK_SET:
            if ($offset < strlen($this->getContents()) && $offset >= 0) {
                $this->position = $offset;
                return true;
            } else {
                return false;
            }
            break;

        case SEEK_CUR:
            if ($offset >= 0) {
                $this->position += $offset;
                return true;
            } else {
                return false;
            }
            break;

        case SEEK_END:
            if (strlen($this->getContents()) + $offset >= 0) {
                $this->position = strlen($this->getContents()) + $offset;
                return true;
            } else {
                return false;
            }
            break;

        default:
            return false;
        }
    }

    function stream_metadata($path, $option, $var)
    {
        return false;
    }
}
