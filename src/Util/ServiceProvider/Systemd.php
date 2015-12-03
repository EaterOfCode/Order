<?php

namespace Eater\Order\Util\ServiceProvider;

use Eater\Order\Util\ExecResult;
use Eater\Order\Util\ExecException;

class Systemd {

    public function has($name)
    {
        $result = ExecResult::createFromCommand('systemctl show ' . escapeshellarg($name) . ' --property LoadState | grep "not-found" 2>&1');
        return !$result->isSuccess();
    }

    public function isEnabled($name)
    {
        $result = ExecResult::createFromCommand('systemctl show ' . escapeshellarg($name) . ' --property UnitFileState | grep "enabled" 2>&1');
        return $result->isSuccess();
    }

    public function isRunning($name)
    {
        $result = ExecResult::createFromCommand('systemctl status ' . escapeshellarg($name) . ' 2>&1 1>/dev/null');

        return $result->isSuccess();
    }

    public function enable($name)
    {
        return ExecResult::createFromCommand('systemctl enable ' . escapeshellarg($name) . ' 2>&1');
    }

    public function disable($name)
    {
        return ExecResult::createFromCommand('systemctl disable ' . escapeshellarg($name) . ' 2>&1');
    }

    public function start($name)
    {
        return ExecResult::createFromCommand('systemctl start ' . escapeshellarg($name) . ' 2>&1');
    }

    public function stop($name)
    {
        return ExecResult::createFromCommand('systemctl stop ' . escapeshellarg($name) . ' 2>&1');
    }


    public function reload($name)
    {
        return ExecResult::createFromCommand('systemctl reload ' . escapeshellarg($name) . ' 2>&1');
    }
}
