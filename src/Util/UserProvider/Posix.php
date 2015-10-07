<?php

namespace Eater\Order\Util\UserProvider;

class Posix {
    public function get($name)
    {
        $data = posix_getpwnam($name);

        if ($data !== false) {

            posix_initgroups($name, $data['gid']);
            $data['groups'] = array_map(function($group){
                $group = posix_getpgid($group);
                return $group['name'];
            }, posix_getgroups());
        }

        return $data;
    }
}
