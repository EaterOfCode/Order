<?php

namespace Eater\Order\Util\PackageProvider;

use Eater\Order\Util\ExecResult;

class Emerge {
    public function sync()
    {
        return ExecResult::createFromCommand('eix-sync 2>&1');
    }

    public function install($package, $special)
    {
        $use = "";
        if (isset($special['emerge/use'])) {
            $use = 'USE=' . escapeshellarg($special['emerge/use']) . ' ';
        }

        return ExecResult::createFromCommand($use . 'emerge '  . escapeshellarg($package) . ' 2>&1');
    }

    public function remove($package)
    {
        return ExecResult::createFromCommand('emerge -C ' . escapeshellarg($package) . ' 2>&1');
    }

    public function isInstalled($package)
    {
        exec('eix -Ie ' . escapeshellarg($package) . ' 2>&1', $output, $returnCode);
        return $returnCode === 0;
    }
}

