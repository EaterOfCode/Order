<?php

namespace Eater\Order\Law;

class Stream {

    /** stores all open files **/
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
    private $position;

    // functions taken from http://php.net/manual/en/stream.streamwrapper.example-1.php

    function stream_open($path, $mode, $options, &$opened_path)
    {
        $this->path = substr($path, strpos($path, '://') + 3);

        // do simple checking

        $this->position = 0;

        return true;
    }

    function stream_read($count)
    {
        $ret = substr($GLOBALS[$this->varname], $this->position, $count);
        $this->position += strlen($ret);
        return $ret;
    }

    function stream_write($data)
    {
        $left = substr($GLOBALS[$this->varname], 0, $this->position);
        $right = substr($GLOBALS[$this->varname], $this->position + strlen($data));
        $GLOBALS[$this->varname] = $left . $data . $right;
        $this->position += strlen($data);
        return strlen($data);
    }

    function stream_tell()
    {
        return $this->position;
    }

    function stream_eof()
    {
        return $this->position >= strlen($GLOBALS[$this->varname]);
    }

    function stream_seek($offset, $whence)
    {
        switch ($whence) {
        case SEEK_SET:
            if ($offset < strlen($GLOBALS[$this->varname]) && $offset >= 0) {
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
            if (strlen($GLOBALS[$this->varname]) + $offset >= 0) {
                $this->position = strlen($GLOBALS[$this->varname]) + $offset;
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
        if($option == STREAM_META_TOUCH) {
            $url = parse_url($path);
            $varname = $url["host"];
            if(!isset($GLOBALS[$varname])) {
                $GLOBALS[$varname] = '';
            }
            return true;
        }
        return false;
    }
}
