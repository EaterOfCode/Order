<?php

namespace Eater\Order\Util\UserProvider;

class Posix {
    public function get($name)
    {
        $data = posix_getpwnam($name);

        if ($data !== false) {
            $data['groups'] = array_map(function($group){
                $group = posix_getgrgid($group);
                return $group['name'];
            }, posix_getgroups());
        }

        return $data;
    }
}
