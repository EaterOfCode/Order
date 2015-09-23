<?php

namespace Eater\Order\Definition;

use Eater\Order\State\File as StateFile;

class File extends Definition {

    protected $file;
    protected $contents;
    protected $type = 'file';
    protected $source;
    protected $shouldExist = true;
    protected $permissions;
    protected $user;
    protected $group;

    public function __construct($file, $options = [])
    {
        $this->file = $file;
        $this->setIdentifier($file);

        if (isset($options['contents'])) {
            $this->contents = $options['contents'];
        } else if (isset($options['source'])) {
            $this->source = $options['source'];
        }

        if (isset($options['user'])) {
            $this->user = $options['user'];
        }

        if (isset($options['permissions'])) {
            $this->permissions = $options['permissions'];
        }

        if (isset($options['exist'])) {
            $this->shouldExist = $options['exist'];
        }

        if (isset($options['user'])) {
            $this->group = $options['group'];
        }
    }

    public function contents($contents)
    {
        $this->contents = $contents;
        $this->source = null;
        return $this;
    }

    public function source($source)
    {
        $this->source = $source;
        $this->contents = null;

        return $this;
    }

    public function exist($should)
    {
        $this->shouldExist = $should;
        return $this;
    }

    public function rm()
    {
        $this->shouldExist = false;
        return $this;
    }

    public function maybe()
    {
        $this->shouldExist = null;
        return $this;
    }

    public function user($user)
    {
        $this->user = $user;
        return $this;
    }

    public function group($group)
    {
        $this->group = $group;
        return $this;
    }

    public function permissions($permissions)
    {
        $this->permissions = $permissions;
        return $this;
    }

    public function chmod($permissions)
    {
        $this->permissions = $permissions;
        return $this;
    }

    public function chown($user, $group = null)
    {
        $this->user = $user;
        if ($group !== null) {
            $this->group = $group;
        }

        return $this;
    }

    public function validate()
    {
        return [];
    }

    public function getDesirableState()
    {
        $optionArr = [
            "file"        => $this->file,
            "group"       => $this->group,
            "user"        => $this->user,
            "shouldExist" => $this->shouldExist,
            "permissions" => $this->permissions
        ];

        if ($this->contents !== null) {
            $optionArr['contents'] = $this->contents;
        } else if ($this->source !== null) {
            $optionArr['source'] = $this->source;
        }

        return StateFile::createFromArray($optionArr);
    }
}
