<?php

include __DIR__ . '/../vendor/autoload.php';

use Eater\Order\Runtime;



$runtime = new Runtime();
$collection = Loader::load($argv[1]);
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

$stateByIdentifier = [];
foreach ($actionChain as $definition) {
    $state = $definition->getDesirableState();
    $stateByIdentifier[$definition->getIdentifier()] = $state;

    $requires = [];
    foreach ($definition->getRequires() as $require) {
        $requires[] = $stateByIdentifier[$require->getIdentifier()];
    }

    $state->setRequires($requires);

    if ($state->areRequiresSatisfied()) {
        foreach($state->getDiff() as $diff)
        {
            echo $diff->getPretty();
        }

        $state->apply();
    } else {
        echo "Couldn't apply state: " . $definition->getIdentifier() . " because of failed dependecies\n";
    }
}
