<?php

namespace Example\Timer;

use React\EventLoop\Factory;
use React\EventLoop\TimerInterface;

require_once __DIR__ . '/../../vendor/autoload.php';

$loop = Factory::create();
$counter = 0;

$loop->addPeriodicTimer(2, function (TimerInterface $timer) use (&$counter, $loop) {
    $counter++;
    echo "$counter\n";

    if ($counter == 5) {
        $loop->cancelTimer($timer);
    }
});

$loop->run();
echo "Done\n";