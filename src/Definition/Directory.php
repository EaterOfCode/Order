<?php

namespace Eater\Order\Definition;

use Eater\Order\State\Directory as StateDirectory;

class Directory extends Definition {

    protected $file;
    protected $type = 'file';
    protected $shouldExist = true;
    protected $permissions;
    protected $user;
    protected $group;
    protected $recursive = false;

    public function __construct($file, $options = [])
    {
        $this->file = $file;
        $this->setIdentifier($file);

        if (isset($options['user'])) {
            $this->user = $options['user'];
        }

        if (isset($options['permissions'])) {
            $this->permissions = $options['permissions'];
        }

        if (isset($options['exist'])) {
            $this->shouldExist = $options['exist'];
        }

        if (isset($options['group'])) {
            $this->group = $options['group'];
        }

        if (isset($options['recursive'])) {
            $this->recursive = $options['recursive'];
        }
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

    public function recursive($recursive = true)
    {
        $this->recursive = $recursive;
    }

    public function validate()
    {
        return [];
    }

    public function getDesirableState()
    {
        return new StateDirectory($this->file, $this->shouldExist, $this->permissions, $this->user, $this->group, $this->recursive);
    }
}
