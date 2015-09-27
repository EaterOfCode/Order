<?php

namespace Eater\Order\Util\ServiceProvider;

use Eater\Order\Util\ExecResult;
use Eater\Order\Util\ExecException;

class OpenRC {

    public function has($name)
    {
        return file_exists('/etc/init.d/' . $name);
    }

    public function isEnabled($name)
    {
        $result = ExecResult::createFromCommand("rc-status -s | awk '{print \$1}' | grep '^'" . escapeshellarg($name) . "'$' 2>&1");

        return $result->isSuccess();
    }

    public function isRunning($name)
    {
        $result = ExecResult::createFromCommand("rc-status -s | grep '\[  started  \]' | awk '{print \$1}' | grep '^'" . escapeshellarg($name) . "'$' 2>&1");

        return $result->isSuccess();
    }

    public function enable($name)
    {
        return ExecResult::createFromCommand('rc-update add ' . escapeshellarg($name) . ' 2>&1');
    }

    public function disable($name)
    {
        return ExecResult::createFromCommand('rc-update delete ' . escapeshellarg($name) . ' 2>&1');
    }

    public function start($name)
    {
        return ExecResult::createFromCommand(escapeshellarg('/etc/init.d/' . $name) . ' start 2>&1');
    }

    public function stop($name)
    {
        return ExecResult::createFromCommand(escapeshellarg('/etc/init.d/' . $name) . ' stop 2>&1');
    }


    public function reload($name)
    {
        return ExecResult::createFromCommand(escapeshellarg($name) . ' reload 2>&1');
    }
}
