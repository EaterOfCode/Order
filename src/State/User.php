<?php

namespace Eater\Order\State;

use Eater\Order\Runtime;

class User extends Desirable {

    private $name;
    private $shouldExist;
    private $groups;
    private $shell;
    private $home;
    private $password;
    private $provider;
    private $comment;

    function __construct($name, $shouldExist, $password, $groups, $shell, $home, $comment, $provider)
    {
        $this->name        = $name;
        $this->shouldExist = $shouldExist;
        $this->password    = $password;
        $this->groups      = $groups;
        $this->shell       = $shell;
        $this->home        = $home;
        $this->comment     = $comment;

        if ($provider === null) {
            $this->provider = Runtime::getCurrent()->getUserProvider()->getDefault();
        } else {
            $this->provider = Runtime::getCurrent()->getUserProvider()->getByName($provider);
        }
    }

    function getDiff()
    {
        $diff = [];

        $user = $this->provider->get($this->name);

        if ($user === false && $this->shouldExist) {
            $diff[] = new Diff(Diff::create, "Created user '{$this->name}'");
        } else {
            if ($this->shouldExist === false) {
                $diff[] = new Diff(Diff::delete, "Deleted user '{$this->name}'");
            } else {
                $userDiff = $this->createUserDiff($user);
                if (count($userDiff) > 0) {
                    $diff[] = new Diff(Diff::change, "Updated user '{$this->name}' (" . implode(", ", $userDiff) . ")");
                }
            }
        }

        return $diff;
    }

    private function createUserDiff($user)
    {
        $diff = [
            'password' => $this->password !== null && $this->password !== $user['passwd'],
            'home' => $this->home !== null && $this->home !== $user['home'],
            'shell' => $this->shell !== null && $this->shell !== $user['shell'],
            'group' => $this->groups !== null && count(array_diff($this->groups, $user['groups'])) > 0,
            'comment' => $this->comment !== null && $this->comment !== $user['gecos']
       ];

        return array_keys(array_filter($diff));
    }

    function apply()
    {
        $user = $this->provider->get($this->name);

        if ($user === false) {
            if ($this->shouldExist) {
                $this->handleExecResult($this->provider->create($this->name, $this->password, $this->groups, $this->shell, $this->home, $this->comment));
            }
        } else {
            if ($this->shouldExist === false) {
                $this->handleExecResult($this->provider->remove($this->name));
            } else {
                if(count($this->createUserDiff($user)) > 0) {
                    $this->handleExecResult($this->provider->update($this->name, $this->password, $this->groups, $this->shell, $this->home, $this->comment));
                }
            }
        }
    }
}
