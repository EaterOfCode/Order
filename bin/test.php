<?php

include __DIR__ . '/../vendor/autoload.php';

use Eater\Order\Law\Stream;
use Eater\Order\Law\Loader;
use Eater\Order\Law\Storage;
use League\Flysystem\Filesystem;
use League\Flysystem\Adapter\Local;

Stream::register('law');

Stream::setStorage(
    new Storage(
        new Filesystem(
            new Local('/')
        ),
        ['']
    )
);

var_dump(Loader::load(__DIR__ . '/../storage/test.law.php'));

