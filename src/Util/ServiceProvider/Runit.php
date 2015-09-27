<?php

namespace Eater\Order\Util\ServiceProvider;

use Eater\Order\Util\ExecResult;
use Eater\Order\Util\ExecException;

class Runit {

    public function has($name)
    {
        return file_exists('/etc/sv/' . $name);
    }

    public function isEnabled($name)
    {
        return file_exists('/var/service/' . $name);
    }

    public function isRunning($name)
    {
        if (!$this->isEnabled($name)) {
            // you can't have a disabled service running in runit
            return false;
        }

        $result = ExecResult::createFromCommand('sv status ' . escapeshellarg($name) . ' 2>&1');

        if (!$result->isSuccess()) {
            if (preg_match('/file does not exist$/', $result->getOutput()[0])) {
                return false;
            }

            throw new ExecException($result);
        }

        $output = $result->getOutput();

        return preg_match('/^run: /', $output[0]);
    }

    public function enable($name)
    {
        return ExecResult::createFromCommand('ln -s ' . escapeshellarg('/etc/sv/' . $name) . ' /var/service/ 2>&1');
    }

    public function disable($name)
    {
        return ExecResult::createFromCommand('rm ' . escapeshellarg('/var/service/' . $name) . ' 2>&1');
    }

    public function start($name)
    {
        return ExecResult::createFromCommand('sv start ' . escapeshellarg($name) . ' 2>&1');
    }

    public function stop($name)
    {
        return ExecResult::createFromCommand('sv stop ' . escapeshellarg($name) . ' 2>&1');
    }


    public function reload($name)
    {
        return ExecResult::createFromCommand('sv reload ' . escapeshellarg($name) . ' 2>&1');
    }
}
