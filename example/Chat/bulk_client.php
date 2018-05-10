<?php

namespace Example\Chat;

use Exception;
use React\EventLoop\Factory;
use React\Socket\ConnectionInterface;
use React\Socket\Connector;
use React\Stream\ReadableResourceStream;
use React\Stream\WritableResourceStream;

require_once __DIR__ . '/../../vendor/autoload.php';

set_error_handler(function (\Throwable $throwable) {
    echo $throwable->getMessage() . PHP_EOL;
});

set_exception_handler(function (Exception $exception) {
    echo $exception->getMessage() . PHP_EOL;
});

$ip = $argv[1] ?? '127.0.0.1';
$port = $argv[2] ?? '8888';

$loop = Factory::create();
$stdin = new ReadableResourceStream(STDIN, $loop);
$stdout = new WritableResourceStream(STDOUT, $loop);

$con_list = [];
for ($i = 0; $i < 50; $i++) {
    $con_list[$i] = (new Connector($loop))
        ->connect("$ip:$port")
        ->then(function (ConnectionInterface $conn) use ($stdin, $stdout) {
            $stdin->pipe($conn)->pipe($stdout);
        }, function (Exception $exception) use ($loop, $stdout) {
            $stdout->write($exception->getMessage() . PHP_EOL);
        });
}
$loop->run();

