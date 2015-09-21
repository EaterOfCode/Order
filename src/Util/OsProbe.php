<?php

namespace Eater\Order\Util;

class OsProbe {
    public static function probe()
    {
        $os = exec("uname -o");
        if ($os !== 'GNU/Linux') {
            return strtolower($os);
        }

        $id = exec("cat /etc/*-release | grep '^ID='");

        return strtolower(trim(substr($id, 3), '"'));
    }
}
