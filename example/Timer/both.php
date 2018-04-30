<?php

namespace Example\Timer;

use React\EventLoop\Factory;
use React\EventLoop\TimerInterface;

require_once __DIR__ . '/../../vendor/autoload.php';

$loop = Factory::create();
$counter = 0;

$periodicTimer = $loop->addPeriodicTimer(2, function () use (&$counter, $loop) {
    $counter++;
    echo "$counter\n";
});

$loop->addTimer(10, function () use ($periodicTimer, $loop) {
    $loop->cancelTimer($periodicTimer);
});

$loop->run();
echo "Done\n";