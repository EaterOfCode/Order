<?php

namespace Eater\Order\Util\PackageProvider;

use Eater\Order\Util\ExecResult;

class Xbps {
    public function sync()
    {
        return ExecResult::createFromCommand('xbps-install -S');
    }

    public function install($package)
    {
        return ExecResult::createFromCommand('xbps-install -y ' . escapeshellarg($package));
    }

    public function remove($package)
    {
        return ExecResult::createFromCommand('xbps-remove -y ' . escapeshellarg($package));
    }

    public function isInstalled($package)
    {
        exec('xbps-query ' . escapeshellarg($package), $output, $returnCode);
        return $returnCode === 0;
    }
}

