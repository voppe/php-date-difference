<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Voppe\Models\VoppeDateModel;

$totalArguments = count($argv);

if (count($argv) === 3) {
    $voppeDate = new VoppeDateModel($argv[1], $argv[2]);
    var_dump($voppeDate->diff());
} else {
    echo "----------\n";
    echo "WRONG USAGE:\n";
    echo "Use Example: php bin/diff.php YYYY/MM/DD YYYY/MM/DD\n";
    echo "----------\n";
}
