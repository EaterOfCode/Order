<?php

namespace Eater\Order\Util\ServiceProvider;

use Eater\Order\Util\ExecResult;
use Eater\Order\Util\ExecException;

class Debian {

    public function has($name)
    {
        return file_exists('/etc/init.d/' . $name);
    }

    public function isEnabled($name)
    {
        $result = ExecResult::createFromCommand("find /etc/rc*.d/ -type l -printf '%l\n' | grep '^\(..\|/etc\)/init.d/'" . escapeshellarg($name) . "'$' 2>&1");

        return $result->isSuccess();
    }

    public function isRunning($name)
    {
        $result = ExecResult::createFromCommand("service " . escapeshellarg($name) . " status 2>&1");

        return $result->isSuccess();
    }

    public function enable($name)
    {
        return ExecResult::createFromCommand('update-rc.d ' . escapeshellarg($name) . ' enable 2>&1');
    }

    public function disable($name)
    {
        return ExecResult::createFromCommand('update-rc.d ' . escapeshellarg($name) . ' disable 2>&1');
    }

    public function start($name)
    {
        return ExecResult::createFromCommand('service ' . escapeshellarg($name) . ' start 2>&1');
    }

    public function stop($name)
    {
        return ExecResult::createFromCommand('service ' . escapeshellarg($name) . ' stop 2>&1');
    }


    public function reload($name)
    {
        return ExecResult::createFromCommand('service ' . escapeshellarg($name) . ' reload 2>&1');
    }
}
