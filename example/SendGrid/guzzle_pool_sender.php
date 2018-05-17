<?php

namespace Example\SendGrid;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Pool;
use GuzzleHttp\Psr7\Request;
use Psr\Http\Message\ResponseInterface;

require_once __DIR__ . '/../../vendor/autoload.php';

$client = new Client();
/** @var \Iterator $requests */
$requests = function () {
    for ($i = 0; $i < 1000; $i++) {
        yield new Request('POST', 'https://api.sendgrid.com/v3/mail/send', [
            'Authorization' => 'Bearer ' . getenv('SENDGRID_API_KEY'),
            'Content-Type' => 'application/json',
        ], json_encode([
            'personalizations' => [
                [
                    'to' => [
                        [
                            'email' => 'ichikawa.shingo.0829+test1@gmail.com',
                            'name' => 's-ichikawa1',
                        ]
                    ],
                    'subject' => "send from react php[$i]"
                ],
            ],
            'from' => [
                'email' => 'dumy@example.com'
            ],
            'content' => [
                [
                    'type' => 'text/plain',
                    'value' => 'Hello World',
                ]
            ],
            'mail_settings' => [
                'sandbox_mode' => [
                    'enable' => true,
                ]
            ]
        ]));
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


