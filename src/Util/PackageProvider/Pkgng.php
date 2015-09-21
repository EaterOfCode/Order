<?php

namespace Eater\Order\Util\PackageProvider;

use Eater\Order\Util\ExecResult;

class Pkgng {

    public function sync()
    {
        return ExecResult::createFromCommand('pkg update');
    }

    public function install($package)
    {
        return ExecResult::createFromCommand('pkg install -y ' . escapeshellarg($package));
    }

    public function remove($package)
    {
        return ExecResult::createFromCommand('pkg remove -y ' . escapeshellarg($package));
    }

    public function isInstalled()
    {
        exec('pkg info ' . escapeshellarg($package), $output, $returnCode);

        return $returnCode === 0;
    }
}
