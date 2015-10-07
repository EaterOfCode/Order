<?php

namespace Eater\Order\Util\UserProvider;

class Pw extends Posix {
    public function create($name, $password, $groups, $shell, $home, $comment)
    {
        $cmd = 'pw useradd ' . escapeshellarg($name);

        $cmd .= 'echo ' . escapeshellarg($password) . ' | ' . $cmd . ' -H 0';

        if (!empty($group)) {
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

        return ExecResult::fromCommand($cmd);
    }

    public function update($name, $password, $groups, $shell, $home, $comment)
    {
        $current = $this->get($name);

        $cmd = 'pw usermod ' . $escapeshellarg($name);

        if ($current['password'] !== $password) {
            $cmd = 'echo ' . escapeshellarg($password) . ' | ' . $cmd . ' -H 0';
        }

        if ($current['gecos'] !== $comment) {
            $cmd .= ' -c ' . escapeshellarg($comment);
        }

        if (!empty(array_diff($current['groups'], $groups))) {
            $cmd .= ' -G ' . escapeshellarg(implode(',', $groups));
        }

        if ($current['shell'] !== $shell) {
            $cmd .= ' -s ' . escapeshellarg($shell);
        }

        if ($current['dir'] !== $home) {
            $cmd .= ' -d ' . escapeshellarg($home);
        }

        return ExecResult::fromCommand($cmd);
    }

    public function remove($name)
    {
        return ExecResult::fromCommand('pw userdel ' . escapeshellarg($name));
    }
}
