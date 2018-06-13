<?php

namespace Example\HttpRequest;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Pool;
use GuzzleHttp\Psr7\Request;
use Psr\Http\Message\ResponseInterface;

require_once __DIR__ . '/../../vendor/autoload.php';

$client = new Client();
/**
 * @var \Iterator $requests
 * @return \Generator
 */
$requests = function () {
    for ($i = 0; $i < 1000; $i++) {
        yield new Request('GET', 'http://127.0.0.1:8080');
    }
};

$pool = new Pool($client, $requests(), [
    'concurrency' => 100,
    'fulfilled' => function (ResponseInterface $response, $index) {
        echo $index . ':' . $response->getStatusCode() . PHP_EOL;
    },
    'reject' => function (RequestException $exception) {
        echo $exception->getCode() . PHP_EOL;
    },
]);

$promise = $pool->promise();
$promise->wait();


