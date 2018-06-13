<?php

namespace Example\EventLoop;

require_once __DIR__ . '/../../vendor/autoload.php';

use React\EventLoop\Factory;
use React\Stream\ReadableResourceStream;
use React\Stream\WritableResourceStream;

$loop = Factory::create();

$f = fopen(__DIR__ . "/jawiki-latest-all-titles", 'r');
$read = new ReadableResourceStream($f, $loop);
$write = new WritableResourceStream(STDOUT, $loop);

$read->on('data', function ($data) use ($read, $write, $loop) {
    $write->write($data . PHP_EOL);

    $read->pause();

    $loop->addTimer(1, function () use ($read) {
        $read->resume();
    });
});

$read->on('end', function () {
    echo "finished\n";
});


$loop->run();

