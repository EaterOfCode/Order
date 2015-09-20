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

$collection = Loader::load(__DIR__ . '/../storage/test.law.php');
$errors = $collection->validate();
if (!empty($errors)) {
    echo "Errors occured: \n\n";
    echo implode(
        "\n",
        array_map(
            function ($ex) {
                return $ex->getMessage();
            },
            $errors
        )
    );
    die("\n");
}

$actionChain = $collection->getActionChain();

foreach ($actionChain as $definition) {
    $state = $definition->getDesirableState();

    foreach($state->getDiff() as $diff)
    {
        echo $diff->getPretty();
    }
}
