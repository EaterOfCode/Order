<?php

namespace Eater\Order\Definition;

use Eater\Order\State\User as UserState;

class User extends Definition {

    protected $name;
    protected $shouldExist = true;
    protected $groups = [];
    protected $password = "";
    protected $shell;
    protected $home;
    protected $comment;
    protected $type = 'user';

    public function __construct($name, $options = [])
    {
        $this->name = $name;

        if (isset($options['create'])) {
            $this->shouldExist = $options['create'];
        } else if (isset($options['remove']) && $options['remove']) {
            $this->shouldExist = false;
        }

        if (isset($options['provider'])) {
            $this->provider = $options['provider'];
        }

        if (isset($options['groups'])) {
            $this->groups = $options['groups'];
        }

        if (isset($options['shell'])) {
            $this->shell = $options['shell'];
        }

        if (isset($options['home'])) {
            $this->home = $options['home'];
        }

        if (isset($options['password'])) {
            $this->password = $options['password'];
        }

        if (isset($options['comment'])) {
            $this->comment = $options['comment'];
        }

        $this->setIdentifier($this->name);
    }

    public function create()
    {
        $this->shouldExist = true;
        return $this;
    }

    public function remove()
    {
        $this->shouldExist = false;
        return $this;
    }

    public function groups($groups)
    {
        $this->groups = $groups;
        return $this;
    }

    public function group($group)
    {
        $this->group[] = $group;
        return $this;
    }

    public function password($password)
    {
        $this->password = $password;
        return $this;
    }

    public function shell($shell)
    {
        $this->shell = $shell;;
        return $this;
    }

    public function home($home)
    {
        $this->home = $home;
        return $this;
    }

    public function comment($comment)
    {
        $this->comment = $comment;
        return $this;
    }

    public function provider($provider)
    {
        $this->provider = $provider;
        return $this;
    }

    public function getDesirableState()
    {
        return new UserState($this->name, $this->shouldExist, $this->password, $this->groups, $this->shell, $this->home, $this->comment, $this->provider);
    }

    public function validate()
    {
        return [];
    }
}
