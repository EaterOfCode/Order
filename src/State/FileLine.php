<?php

namespace Eater\Order\State;

class FileLine extends Desirable {

    private $match;
    private $file;
    private $where;
    private $line;
    private $multiple;

    public function __construct($file, $line, $where, $match, $multiple)
    {
        $this->match    = $match;
        $this->file     = $file;
        $this->where    = $where;
        $this->line     = $line;
        $this->multiple = $multiple;
    }

    public function getDiff()
    {
        $diff = [];

        $contents = file_get_contents($this->file);
        $result   = $this->getResult($contents);

        if ($contents !== $result) {
            $diff[] = new TextDiff(Diff::create, sprintf("Added line to '%s'", $this->file), [$contents, $result]);
        }

        return $diff;
    }

    public function apply()
    {
        $contents = file_get_contents($this->file);
        $result   = $this->getResult($contents);

        file_put_contents($this->file, $result);
    }

    private function getResult($contents)
    {
        $original = explode("\n", $contents);
        $file     = $original;
        $match    = $this->match;
        $where    = $this->where;
        $multiple = $this->multiple;
        $matched  = false;

        for ($i = 0; $i < count($file); $i++) {
            $line = $file[$i];
            if ($this->doesMatch($match, $line)) {
                if ($multiple !== false || ($multiple === false && $match === false)) {
                    switch ($this->where) {
                        case 'on':
                        case null:
                            $file[$i] = $this->line;
                            var_dump($file);
                            break;
                        case 'after':
                            if ($file[$i + 1] !== $line) {
                                array_splice($file, $i + 1, 0, [$this->line]);
                            }

                            $i++;
                            break;
                        case 'before':
                            if ($file[$i - 1] !== $line) {
                                array_splice($file, $i, 0, [$this->line]);
                                $i++;
                            }

                            break;
                    }
                }

                $matched = true;

                if ($multiple === null) {
                    break;
                }

                if ($multiple === false && $matched) {
                    return implode("\n", $original);
                }
            }
        }

        if (!$matched)
        {
            $file[] = $this->line;
        }

        return implode("\n", $file);
    }

    public function doesMatch($match, $line)
    {
        if ($match[0] === '/') {
            if (preg_match($match, $line)) {
               return true;
            }
        } else {
            if ($match === $line) {
                return true;
            }
        }

        return false;
    }
}
