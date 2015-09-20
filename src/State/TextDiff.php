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
        $tokenToColorMap = [
            2 => [self::getStateColor(Diff::delete) . '- ', "\033[0m"],
            1 => [self::getStateColor(Diff::create) . '+ ', "\033[0m"],
            0 => ["  ",""]
        ];

        $prettyDiff = [];
        $differ = new Differ();

        $diffArr = $differ->diffToArray($this->fileChanges[0], $this->fileChanges[1]);

        $buffer = [];

        $lastMatchStart = 0;
        $hadMatch       = false;
        $lastOldStart   = 0;
        $lastOld        = false;
        $lastWritten    = 0;
        foreach ($diffArr as $i => $diffToken) {
            if ($diffToken[1] === 0 && !$lastOld) {
                $lastOld = true;
                $lastOldStart = $i;
            }

            if ($i - 3 === $lastOldStart && $hadMatch) {
                $lastWritten = $i;
                $buffer = array_merge($buffer, array_slice($diffArr, $i - 3, 3));
            }

            if ($diffToken[1] !== 0) {

                if ($lastOld) {
                    $scrollBack = min($i - max($lastOldStart, $lastWritten), 3);
                    if ($scrollBack > 0) {
                        $buffer = array_merge($buffer, array_slice($diffArr, $i - $scrollBack, $scrollBack));
                    }
                }

                $hadMatch = true;
                $buffer[] = $diffToken;
                if ($lastOld) {
                    $lastMatchStart = $i;
                }
                $lastOld = false;
            }
        }

        foreach ($buffer as $diffToken) {
            $prettyDiff[] = $tokenToColorMap[$diffToken[1]][0] . $diffToken[0] . $tokenToColorMap[$diffToken[1]][1];
        }

        return implode("\n", $prettyDiff);
    }
}
