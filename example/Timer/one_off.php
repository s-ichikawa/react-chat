<?php
namespace Example\Timer;

use React\EventLoop\Factory;

require_once __DIR__ . '/../../vendor/autoload.php';


$loop = Factory::create();
$loop->addTimer(2, function () use (&$counter) {
    echo "Hello world\n";
});

$loop->run();
echo "finished\n";