<?php

namespace Eater\Order\Util\UserProvider;

use Eater\Order\Util\ExecResult;

class Useradd extends Posix {
    public function create($name, $password, $groups, $shell, $home, $comment)
    {
        $cmd = 'useradd ' . escapeshellarg($name) . ' -m ';

        if ($password !== null) {
            $cmd .= ' -p ' . escapeshellarg($password);
        }

        if (!empty($groups)) {
            $cmd .= ' -G ' . escapeshellarg(implode(',', $groups));
        }

        if ($shell !== null) {
            $cmd .= ' -s ' . escapeshellarg($shell);
        }

        if ($home !== null) {
            $cmd .= ' -d ' . escapeshellarg($home);
        }

        if ($comment !== null) {
            $cmd .= ' -c ' . escapeshellarg($comment);
        }

        return ExecResult::createFromCommand($cmd . ' 2>&1');
    }

    public function update($name, $password, $groups, $shell, $home, $comment)
    {
        $current = $this->get($name);

        $cmd = 'usermod ' . escapeshellarg($name);

        if ($password !== null && $current['passwd'] !== $password) {
            $cmd .= ' -p ' . escapeshellarg($password);
        }

        if ($comment !== null && $current['gecos'] !== $comment) {
            $cmd .= ' -c ' . escapeshellarg($comment);
        }

        $diff = array_diff($current['groups'], $groups);
        if (!empty($diff)) {
            $cmd .= ' -G ' . escapeshellarg(implode(',', $groups));
        }

        if ($shell !== null && $current['shell'] !== $shell) {
            $cmd .= ' -s ' . escapeshellarg($shell);
        }

        if ($home !== null && $current['dir'] !== $home) {
            $cmd .= ' -d ' . escapeshellarg($home);
        }

        return ExecResult::createFromCommand($cmd . ' 2>&1');
    }

    public function get($name)
    {
        $user = parent::get($name);

        $entries = file('/etc/shadow');
        foreach ($entries as $entry) {
            list($username, $passwd) = explode(':', $entry);
            if ($name === $username) {
                $user['passwd'] = $passwd;
                return $user;
            }
        }

        return $user;
    }

    public function remove($name)
    {
        return ExecResult::createFromCommand('userdel ' . escapeshellarg($name) . ' 2>&1');
    }
}
