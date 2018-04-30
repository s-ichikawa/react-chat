<?php

namespace Example\EventLoop;

require_once __DIR__ . '/../../vendor/autoload.php';

use React\EventLoop\Factory;
use React\Stream\ReadableResourceStream;
use React\Stream\WritableResourceStream;

$loop = Factory::create();

$f = fopen('php://stdout', 'w');
$stream = new WritableResourceStream($f, $loop, 1);

var_dump($stream->write("Hello world\n"));

$stream->on('drain', function () use ($stream) {
    echo "The stream is drained\n";
    $stream->close();
});

$stream->on('close', function () {
    echo "Close\n";
});

echo "Start loop\n";

$loop->run();

echo "end loop\n";

