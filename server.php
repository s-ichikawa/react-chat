<?php
namespace ReactChat;

require_once './vendor/autoload.php';
require_once './ConnectionPool.php';

$loop = \React\EventLoop\Factory::create();

$host = getenv('HOSTNAME') ?: '127.0.0.1';
$socket = new \React\Socket\Server(gethostbyname($host) . ':8080', $loop);
$pool = new ConnectionPool();

$socket->on('connection', function (\React\Socket\ConnectionInterface $connection) use ($pool) {
    $pool->add($connection);
});

echo "Listening on {$socket->getAddress()}\n";

$loop->run();
