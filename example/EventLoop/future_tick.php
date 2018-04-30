<?php
namespace Example\EventLoop;

require_once __DIR__ . '/../../vendor/autoload.php';

use React\EventLoop\Factory;
use React\Stream\WritableResourceStream;

$loop = Factory::create();

$writable = new WritableResourceStream(fopen('php://stdout', 'w'), $loop);
$writable->write("I/O\n");

$loop->addTimer(0, function() {
    echo "Timer\n";
});

$loop->futureTick(function () {
    echo "Future Tick1\n";
});

$loop->futureTick(function () {
    echo "Future Tick2\n";
});

echo "Loop starts\n";

$loop->run();

echo "Loop end\n";
