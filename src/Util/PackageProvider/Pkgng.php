<?php

namespace Eater\Order\Util\PackageProvider;

use Eater\Order\Util\ExecResult;

class Pkgng {

    public function sync()
    {
        return ExecResult::createFromCommand('pkg update 2>&1');
    }

    public function install($package)
    {
        return ExecResult::createFromCommand('pkg install -y ' . escapeshellarg($package) . ' 2>&1');
    }

    public function remove($package)
    {
        return ExecResult::createFromCommand('pkg remove -y ' . escapeshellarg($package) . ' 2>&1');
    }

    public function isInstalled($package)
    {
        exec('pkg info ' . escapeshellarg($package) . ' 2>&1', $output, $returnCode);

        return $returnCode === 0;
    }
}
