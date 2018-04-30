<?php

namespace Example\EventLoop;

require_once __DIR__ . '/../../vendor/autoload.php';

use React\EventLoop\Factory;
use React\Stream\ReadableResourceStream;

$loop = Factory::create();

$f = fopen(__DIR__ . "/../../jawiki-latest-all-titles", 'r');
$stream = new ReadableResourceStream($f, $loop);

$stream->on('data', function ($data) use ($stream, $loop) {
    echo "$data\n";

    $stream->pause();

    $loop->addTimer(1, function () use ($stream) {
        $stream->resume();
    });
});

$stream->on('end', function () {
    echo "finished\n";
});


$loop->run();

