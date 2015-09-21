<?php

namespace Eater\Order\Util\PackageProvider;

use Eater\Order\Util\ExecResult;

class AptGet {

    public function sync()
    {
        return ExecResult::createFromCommand('apt-get update -q');
    }

    public function install($package)
    {
        return ExecResult::createFromCommand('apt-get install -qy ' . escapeshellarg($package));
    }

    public function remove($package)
    {
        return ExecResult::createFromCommand('apt-get remove -yq ' . escapeshellarg($package));
    }

    public function isInstalled()
    {
        exec('apt-cache pkgnames | grep \'^\'' . escapeshellarg($package) . '\'$\'', $output, $returnCode);

        return $returnCode === 0;
    }
}
