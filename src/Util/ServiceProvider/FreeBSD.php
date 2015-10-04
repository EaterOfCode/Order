<?php

namespace Eater\Order\Util\ServiceProvider;

use Eater\Order\Util\ExecResult;
use Eater\Order\Util\ExecException;
use Eater\Order\State\FileLine;

class FreeBSD {

    public function has($name)
    {
        return file_exists('/etc/rc.d/' . $name) || file_exists('/usr/local/etc/rc.d/' . $name);
    }

    public function isEnabled($name)
    {
        $config = file('/etc/rc.conf');

        foreach ($config as $line) {
            if (preg_match('/^ *' . $name . '_enable="YES"/', $line)) {
                return true;
            }
        }

        return false;
    }

    public function isRunning($name)
    {
        $status = "status";

        if (!$this->isEnabled($name)) {
            // you can't have a disabled service running in runit
            $status = "onestatus";
        }

        $result = ExecResult::createFromCommand('service '. escapeshellarg($name) . ' ' . $status .  ' 2>&1');

        return $result->isSuccess();
    }

    public function enable($name)
    {
        $file = new FileLine('/etc/rc.conf', $name . '_enable="YES"', 'on', '/^ *' . $name . '_enable="YES"/', true);
        $file->apply();

        return null;
    }

    public function disable($name)
    {
        $file = new FileLine('/etc/rc.conf', false, 'on', '/^ *' . $name . '_enable="YES"/', true);
        $file->apply();

        return null;
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
