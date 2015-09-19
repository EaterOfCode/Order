<?php

namespace Eater\Order\Law;

class WrappedExecutor {

    static public $definitions;

    static public function execute($path)
    {
        $hash = md5($path);
        $file = basename($path);
        $tmpDir = sys_get_temp_dir() . '/order/';
        @mkdir($tmpDir);
        $tmpPhpFile = $tmpDir . $hash . '.' . $file; 

        $php = file_get_contents($path);
        $php = preg_replace('/\<\?(php)?/', '<?php namespace ' . __NAMESPACE__ . '\\Wrapped;', $php);
        file_put_contents($tmpPhpFile, $php);

        include $tmpPhpFile;
//        @unlink($tmpPhpFile);
    }

    static public function registerFunctionFile($file)
    {
        // yolo
        include_once $file;
    }

    static public function getDefinitions()
    {
        return static::$definitions;
    }
    
    static public function clearDefinitions()
    {
        static::$definitions = new DefinitionCollection();
    }

    static public function boot()
    {
        WrappedExecutor::registerFunctionFile(__DIR__ . '/Wrapped/functions.php');
        ini_set('include_path', sys_get_temp_dir() . '/order/:' . ini_get('include_path'));
    }
}

WrappedExecutor::boot();
