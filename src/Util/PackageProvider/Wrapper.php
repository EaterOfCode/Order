<?php

namespace Eater\Order\Util\PackageProvider;

use \Eater\Order\Util\OsProbe;

class Wrapper {

    static private $repo   = [];
    static private $packageProviderByName = [];

    static public function register($name, $wrapper, $osList)
    {
        static::$packageProviderByName[$name] = $wrapper;

        foreach ($osList as $os) {
            static::$repo[$os] = $wrapper;
        }
    }

    static public function getPackgeProvider()
    {
        $os = OsProbe::probe();

        if (!isset(static::$repo[$os])) {
            throw UnknownPackageProvider(null, $os);
        }

        return static::$repo[$os];
    }

    static public function load()
    {
        static::register("xbps", new Xbps(), ["void"]);
        static::register("apt-get", new AptGet(), ["ubuntu", "debian"]);
        static::register("pkgng", new Pkgng(), ["freebsd"]);
    }
}
