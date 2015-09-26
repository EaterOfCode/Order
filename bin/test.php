<?php

include __DIR__ . '/../vendor/autoload.php';

use Eater\Order\Runtime;

$runtime = new Runtime();
$runtime->init(getcwd());
$runtime->load(getcwd() . '/' . $argv[1]);
$runtime->apply($argv[2] === 'commit');
