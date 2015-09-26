<?php

namespace Eater\Order\Util\PackageProvider;

use Eater\Order\Util\ExecResult;

class Xbps {
    public function sync()
    {
        return ExecResult::createFromCommand('xbps-install -S 2>&1');
    }

    public function install($package)
    {
        return ExecResult::createFromCommand('xbps-install -y ' . escapeshellarg($package) . ' 2>&1');
    }

    public function remove($package)
    {
        return ExecResult::createFromCommand('xbps-remove -y ' . escapeshellarg($package) . ' 2>&1');
    }

    public function isInstalled($package)
    {
        exec('xbps-query ' . escapeshellarg($package) . ' 2>&1', $output, $returnCode);
        return $returnCode === 0;
    }
}

