<?php

include __DIR__ . '/../vendor/autoload.php';

use Eater\Order\Runtime;

$runtime = new Runtime();
$runtime->init(getcwd());

$opts = getopt('c:d', ['config:', 'dry']);

$dry    = isset($opts['d']) || isset($opts['dry']);
$config = isset($opts['c']) ? $opts['c'] : (isset($opts['config']) ? $opts['config'] : getcwd() . '/order.yaml');

exit(intval($runtime->run($config, !$dry)));
