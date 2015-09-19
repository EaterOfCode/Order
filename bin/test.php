<?php

include __DIR__ . '/../vendor/autoload.php';

use \Eater\Order\Law\Wrapped;

$reader = new Wrapped(__DIR__ . '/../storage/test.law.php');

$reader->execute();
