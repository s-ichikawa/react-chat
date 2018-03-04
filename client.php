<?php
require_once './vendor/autoload.php';

$loop = \React\EventLoop\Factory::create();
$connector = new \React\Socket\Connector($loop);
$stdin = new \React\Stream\ReadableResourceStream(STDIN, $loop);
$stdout = new \React\Stream\WritableResourceStream(STDOUT, $loop);

$ip = $argv[1] ?? '127.0.0.1';
$port = $argv[2] ?? '8888';
$connector
    ->connect("$ip:$port")
    ->then(function (\React\Socket\ConnectionInterface $conn) use ($stdin, $stdout) {
        $stdin->pipe($conn)->pipe($stdout);

    }, function (Exception $exception) use ($loop) {

    });
$loop->run();

