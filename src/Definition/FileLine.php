<?php

namespace Eater\Order\Definition;

use Eater\Order\State\FileLine as StateFileLine;

class FileLine extends Definition {

    protected $file;
    protected $type = 'fileline';
    protected $multiple = true;
    protected $match;
    protected $line;
    protected $where = 'on';

    public function __construct($file, $options = [])
    {
        $this->file = $file;
        $this->setIdentifier($file);

        if (isset($options['line'])) {
            $this->line = $options['line'];
        }

        if (isset($options['match'])) {
            $this->match = $options['match'];
        } elseif ($this->line !== false) {
            $this->match = $this->line;
        }

        if (isset($options['where'])) {
            $this->where = $options['where'];
        }

        if (isset($options['multiple'])) {
            $this->multiple = $options['multiple'];
        }

        $this->fixIdentifier();
    }

    private function fixIdentifier()
    {
        $this->setIdentifier($this->match . ':' . $this->line);
    }

    public function line($line)
    {
        $this->line = $line;

        if ($this->match === null) {
            $this->match = $line;
        }

        $this->fixIdentifier();

        return $this;
    }

    public function remove()
    {
        $this->line = false;

        $this->fixIdentifier();

        return $this;
    }

    public function match($match)
    {
        $this->match = $match;

        $this->fixIdentifier();

        return $this;
    }

    public function where($where)
    {
        $this->where = $where;

        return $this;
    }

    public function after()
    {
        $this->where = 'after';

        return $this;
    }

    public function before()
    {
        $this->where = 'before';

        return $this;
    }

    public function on()
    {
        $this->where = 'on';

        return $this;
    }

    public function multiple($multiple)
    {
        $this->multiple = $multiple;

        return $this;
    }

    public function validate()
    {
        $errors = [];

        if ($this->line === null) {
            $errors[] = "No line defined";
        }

        if ($this->match === null) {
            $errors[] = "No match defined";
        }

        if (!in_array($this->where, ['on', 'after', 'before'])) {
            $errors[] = 'where can only be "on", "after" or "before", current value: "' . $this->where . '"';
        }

        if ($this->line === false && $this->match === null) {
            $errors[] = "Can't remove line, with no match set";
        }

        return $errors;
    }

    public function getDesirableState()
    {
        return new StateFileLine($this->file, $this->line, $this->where, $this->match, $this->multiple);
    }
}
