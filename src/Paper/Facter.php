<?php

namespace Eater\Order\Paper;

use Eater\Order\Util\ExecResult;

class Facter implements Dossier {

    private $facterData;

    public function getFacterData()
    {
        if ($this->facterData === null) {
            $facter = ExecResult::createFromCommand("facter --json");

            if (!$facter->isSuccess()) {
                throw new FacterFailed();
            }

            $this->facterData = json_decode(implode("\n", $facter->getOutput()), true);
        }

        return $this->facterData;
    }

    public function get($name)
    {
        $names = explode(".", $name);
        $current = $this->getFacterData();

        foreach ($names as $itemName) {
            if (!isset($current[$itemName])) {
                return null;
            }

            $current = $current[$itemName];
        }

        return $current;
    }

    public function has($name)
    {
        $names = explode(".", $name);

        $current = $this->getFacterData();

        foreach ($names as $itemName) {
            if (!isset($current[$itemName])) {
                return false;
            }

            $current = $current[$itemName];
        }

        return true;
    }

}
