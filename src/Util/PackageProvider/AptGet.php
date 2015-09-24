<?php

namespace Eater\Order\Util\PackageProvider;

use Eater\Order\Util\ExecResult;

class AptGet {

    public function sync()
    {
        return ExecResult::createFromCommand('apt-get update -q 2>&1');
    }

    public function install($package)
    {
        return ExecResult::createFromCommand('apt-get install -qy ' . escapeshellarg($package) . ' 2>&1');
    }

    public function remove($package)
    {
        return ExecResult::createFromCommand('apt-get remove -yq ' . escapeshellarg($package) . '2>&1');
    }

    public function isInstalled($package)
    {
        exec("dpkg-query -Wf'\${db:Status-abbrev}' " . escapeshellarg($package) . " 2>/dev/null | grep -q '^i'" , $output, $returnCode);

        return $returnCode === 0;
    }
}
