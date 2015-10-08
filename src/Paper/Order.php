<?php

namespace Eater\Order\Paper;

class Order implements Dossier {

    public function has($name)
    {
        $names = explode(".", $name);
        $function = "";

        foreach ($names as $i => $name) {
            if ($i === 0) {
                $function .= strtolower($name);
            } else {
                $function .= strtoupper($name[0]) . strtolower(substr($name, 1));
            }
        }

        return method_exists($this, $function);
    }

    public function get($name)
    {
        $names = explode(".", $name);
        $function = "";

        foreach ($names as $i => $name) {
            if ($i === 0) {
                $function .= strtolower($name);
            } else {
                $function .= strtoupper($name[0]) . strtolower(substr($name, 1));
            }
        }

        return $this->$function();
    }

    public function osDistribution()
    {
        $os = exec("uname -o");
        if ($os !== 'GNU/Linux') {
            return strtolower($os);
        }

        $id = exec("cat /etc/*-release | grep '^\(DISTRIB_\)\?ID='");

        return strtolower(trim(substr($id, strpos($id, "=") + 1), '"'));
    }
}
