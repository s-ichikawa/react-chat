<?php
require_once __DIR__ . '/../../vendor/autoload.php';

$loop = \React\EventLoop\Factory::create();

$socket = new \React\Socket\Server(8080, $loop);

$server = (new \React\Http\Server(function () {
    return new \React\Http\Response(200, [
        'Content-Type' => 'text/plain'
    ], "Hello World\n");
}))->listen($socket);

$loop->run();


