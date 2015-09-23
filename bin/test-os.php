<?php

include __DIR__ . '/../vendor/autoload.php';

use Eater\Order\Util\OsProbe;
use Eater\Order\Util\PackageProvider\Wrapper;

Wrapper::load();

$pp = Wrapper::getPackageProvider();

echo "Current OS is: " . OsProbe::probe() . "\n";
echo "Package provider for this OS is: " . get_class($pp) . "\n";


if (isset($argv[1])) {
    $result = $pp->install($argv[1]);
    echo 'Tried to install ' . $argv[1] . ' (' . $result->getReturnCode() . ")\n";
    echo implode("\n", $result->getOutput()) . "\n";
}
