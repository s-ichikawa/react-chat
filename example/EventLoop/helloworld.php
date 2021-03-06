<?php
require_once __DIR__ . '/../../vendor/autoload.php';

$loop = \React\EventLoop\Factory::create();
$read = new \React\Stream\ReadableResourceStream(STDIN, $loop);
$write = new \React\Stream\WritableResourceStream(STDOUT, $loop);

$read->on('data', function ($chunk) use ($write) {
    $write->write("Hello World and $chunk");
});

$loop->run();

