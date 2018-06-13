<?php

namespace Example\HttpRequest;

require_once __DIR__ . '/../../vendor/autoload.php';

use React\EventLoop\Factory;
use React\HttpClient\Client;
use React\HttpClient\Response;
use React\Socket\Connector;
use React\Stream\WritableResourceStream;


ini_set('memory_limit', '256M');

$loop = Factory::create();
$writable = new WritableResourceStream(STDOUT, $loop);
$connector = new Connector($loop, array(
    'dns' => '8.8.8.8',
));
$client = new Client($loop, $connector);


for ($i = 0; $i < 1000; $i++) {
    $request = $client->request('GET', 'http://127.0.0.1:8080');

    $request->on('response', function (Response $response) use ($i, $writable) {
        $writable->write($i . ':' . $response->getCode() . PHP_EOL);
    });

    $request->end();
}

$loop->run();

