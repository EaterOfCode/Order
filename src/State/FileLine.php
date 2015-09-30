<?php

namespace Eater\Order\State;

class FileLine extends Desirable {

    private $contents;
    private $file;
    private $shouldExist;
    private $permissions;
    private $user;
    private $group;


    private function __construct($file, $shouldExist, $contents, $permissions, $user, $group)
    {
        $this->match  = $match;
        $this->file   = $file;
        $this->where  = $where;
        $this->line   = $line;
    }

    function getDiff()
    {
        $diff = [];

        return $diff;
    }

    function apply()
    {

    }

    public static function create()
    {
        return new static($file, $line, $where, $match);
    }
}
