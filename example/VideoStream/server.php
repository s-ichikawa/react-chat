<?php
namespace Example\VideoStream;

use Psr\Http\Message\ServerRequestInterface;
use React\Http\Response;
use React\Http\Server;
use React\Stream\ReadableResourceStream;

require_once __DIR__ . '/../../vendor/autoload.php';

$loop = \React\EventLoop\Factory::create();

$server = new Server(function (ServerRequestInterface $request) use ($loop) {
    $params = $request->getQueryParams();
    $file = $params['video'] ?? '';

    if (empty($file)) {
        return new Response(200, ['Content-Type' => 'text/plain'], 'Video streaming server');
    }

    $file_path = __DIR__ . DIRECTORY_SEPARATOR . $file;
    $video = new ReadableResourceStream(fopen($file_path, 'r'), $loop);
    return new Response(200, ['Content-Type' => 'video/mp4'], $video);
});

$host = getenv('HOSTNAME') ?: '127.0.0.1';
$socket = new \React\Socket\Server(gethostbyname($host) . ':8888', $loop);

$server->listen($socket);

echo 'Listening on ' . str_replace('tcp:', 'http:', $socket->getAddress() . PHP_EOL);

$loop->run();