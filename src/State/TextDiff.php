<?php

namespace Eater\Order\State;

use SebastianBergmann\Diff\Differ;

class TextDiff extends Diff {

    private $fileChanges;

    public function __construct($state, $description, $fileChanges)
    {
        $this->state = $state;
        $this->description = $description;
        $this->fileChanges = $fileChanges;
    }

    public function getPretty()
    {
        $pretty = parent::getPretty();
        $pretty .= "===  Diff ===\n";
        $pretty .= $this->getPrettyDiff();
        $pretty .= "\n=== End diff ===";
        $pretty .= "\n";

        return $pretty;
    }

    public function getPrettyDiff()
    {
        $prettyDiff = [];
        $differ = new Differ();

        $diffArr = $differ->diffToArray($this->fileChanges[0], $this->fileChanges[1]);

        $buffer = [];
        $lastMutation = false;
        foreach ($diffArr as $i => $diffToken) {
            if ($lastMutation !== false && $i - 3 === $lastMutation) {
                $prettyDiff = array_merge($prettyDiff, $buffer);
                $buffer = [];
            }

            if ($diffToken[1] !== 0) {
                $prettyDiff = array_merge($prettyDiff, $buffer);
                $buffer = [];
                $prettyDiff[] = $this->getPrettyMutation($diffToken);
                $lastMutation = $i;
            } else {
                $buffer[] = $this->getPrettyMutation($diffToken);
            }

            $buffer = array_slice($buffer, -3);
        }

        return implode("\n", $prettyDiff);
    }

    public function getPrettyMutation($diffToken)
    {
        $tokenToColorMap = [
            2 => [self::getStateColor(Diff::delete) . '- ', "\033[0m"],
            1 => [self::getStateColor(Diff::create) . '+ ', "\033[0m"],
            0 => ["  ",""]
        ];

        return $tokenToColorMap[$diffToken[1]][0] . $diffToken[0] . $tokenToColorMap[$diffToken[1]][1];
    }
}
