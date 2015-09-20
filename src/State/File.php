<?php

namespace Eater\Order\State;

class File extends Desirable {

    private $contents;
    private $file;
    private $shouldExist;
    private $permissions;
    private $user;
    private $group;


    private function __construct($file, $shouldExist, $contents, $permissions, $user, $group)
    {
        $this->contents = $contents;
        $this->file     = $file;
        $this->shouldExist = $shouldExist;
        $this->permissions  = $permissions !== null ? $permissions & 0777 : null;
        $this->user = $user;
        $this->group = $group;
    }

    function getDiff()
    {
        $diff = [];

        if ($this->shouldExist === true || ($this->shouldExist === null && file_exists($this->file))) {
            if ($this->contents !== null) {
                if (file_exists($this->file)) {
                    $currentContents = file_get_contents($this->file);
                    if ($currentContents !== $this->contents) {
                        $diff[] = new TextDiff(Diff::change, 'File "' . $this->file . '": updated', [$currentContents, $this->contents]);
                    }
                } else {
                    $diff[] = new TextDiff(Diff::create, 'File "' . $this->file . '": created', ["", $this->contents]);
                    return $diff;
                }
            } else if (!file_exists($this->file)) {
                $diff[] = new Diff(Diff::create, 'File "' . $this->file . '" created (no content)');
                return $diff;
            }

            if ($this->permissions !== null) {
                $currentPerm = fileperms($this->file) & 0777;
                if ($currentPerm !== $this->permissions) {
                    $diff[] = new Diff(Diff::change, 'File permissions "' . $this->file . '" changed from ' . $currentPerm . " to " . $this->permissions);
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
                    $diff[] = new Diff(Diff::change, 'File owner "' . $this->file . '" changed from ' . $currentUserId . " to " . $newUserId);
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
                    $diff[] = new Diff(Diff::change, 'File group "' . $this->file . '" changed from ' . $currentGroupId . " to " . $newGroupId);
                }

            }
        } else if (file_exists($this->file)) {
            $diff[] = new TextDiff(Diff::delete, 'File "' . $this->file . '" deleted', [file_get_contents($this->file), ""]);
        }

        return $diff;
    }

    function apply()
    {
        if ($this->shouldExist === true || ($this->shouldExist === null && file_exists($this->file))) {
            if ($this->contents !== null) {
                if (!file_exists($this->file) || file_get_contents($this->file) !== $this->contents) {
                    file_put_contents($this->file, $this->contents);
                }
            } else if (!file_exists($this->file)) {
                touch($this->file);
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
        } else if (file_exists($this->file)) {
            unlink($this->file);
        }
    }

    public static function createFromSource($file, $source, $permissions = null, $user = null, $group = null)
    {
        // TODO: use flysystem for ftp,s3 and such calls
        return new static($file, true, file_get_contents($source), $permissions, $user, $group);
    }

    public static function createFromContents($file, $contents, $permissions = null, $user = null, $group = null)
    {
        return new static($file, true, $contents, $permissions, $user, $group);
    }

    public static function createFromArray($options)
    {
        return new static(
            $options['file'],
            isset($options['shouldExist']) ? $options['shouldExist'] : true,
            isset($options['source']) ? file_get_contents($options['source']) : (isset($options['contents']) ? $options['contents'] : null),
            isset($options['permissions']) ? $options['permissions'] : null,
            isset($options['user']) ? $options['user'] : null,
            isset($options['group']) ? $options['group'] : null
        );
    }
}
