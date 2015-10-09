<?php

namespace Eater\Order\State;

class Directory extends Desirable {

    private $contents;
    private $file;
    private $shouldExist;
    private $permissions;
    private $user;
    private $group;
    private $recursive;

    public function __construct($file, $shouldExist, $permissions, $user, $group, $recursive)
    {
        $this->file     = $file;
        $this->shouldExist = $shouldExist;
        $this->permissions  = $permissions !== null ? $permissions & 0777 : null;
        $this->user = $user;
        $this->group = $group;
        $this->recursive = $recursive;
    }

    function getDiff()
    {
        $diff = [];

        if ($this->shouldExist === true || ($this->shouldExist === null && file_exists($this->file))) {
            if (!file_exists($this->file)) {
                $diff[] = new Diff(Diff::create, 'Created directory "' . $this->file . '"');
            }

            if ($this->permissions !== null) {
                $currentPerm = fileperms($this->file) & 0777;
                if ($currentPerm !== $this->permissions) {
                    $diff[] = new Diff(Diff::change, 'Directory permissions "' . $this->file . '" changed from ' . $currentPerm . " to " . $this->permissions);
                }
            }

            if ($this->user !== null) {
                $currentUserId = fileowner($this->file);
                $newUserId     = $this->user;
                if (!is_numeric($this->user)) {
                    $userDatai = posix_getpwuid($currentUserId);
                    $currentUserId = $userData['name'];
                }

                if ($newUserId !== $currentUserId) {
                    $diff[] = new Diff(Diff::change, 'Directory owner "' . $this->file . '" changed from ' . $currentUserId . " to " . $newUserId);
                }
            }

            if ($this->group !== null) {
                $currentGroupId = filegroup($this->file);
                $newGroupId     = $this->group;
                if (!is_numeric($this->group)) {
                    $groupData  = posix_getgrgid($currentGroupId);
                    $currentGroupId = $groupData['name'];
                }

                if ($newGroupId !== $currentGroupId) {
                    $diff[] = new Diff(Diff::change, 'Directory group "' . $this->file . '" changed from ' . $currentGroupId . " to " . $newGroupId);
                }

            }
        } elseif (file_exists($this->file) && $this->shouldExist === false) {
            $diff[] = new Diff(Diff::create, 'Deleted directory "' . $this->file . '"');
        }

        return $diff;
    }

    function apply()
    {
        if ($this->shouldExist === true || ($this->shouldExist === null && file_exists($this->file))) {
            if (!file_exists($this->file)) {
                mkdir($this->file, 0777, $this->recursive);
            }

            if ($this->permissions !== null) {
                chmod($this->file, $this->permissions);
            }

            if ($this->user !== null) {
                chown($this->file, $this->user);
            }

            if ($this->group !== null) {
                chgrp($this->file, $this->group);
            }
        } else if (file_exists($this->file) && $this->shouldExist === false) {
            unlink($this->file);
        }
    }
}
