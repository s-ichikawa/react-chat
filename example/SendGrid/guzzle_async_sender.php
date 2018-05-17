<?php

namespace Example\SendGrid;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use function GuzzleHttp\Promise\all;
use Psr\Http\Message\ResponseInterface;

require_once __DIR__ . '/../../vendor/autoload.php';

$client = new Client();
for ($i = 0; $i < 2000; $i++) {
    $promise = $client->postAsync('https://api.sendgrid.com/v3/mail/send', [
        'headers' => [
            'Authorization' => 'Bearer ' . getenv('SENDGRID_API_KEY'),
            'Content-Type' => 'application/json',
        ],
        'json' => [
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
        ]
    ]);
    $promise->then(function (ResponseInterface $response) use ($i) {
        echo $i . ':' . $response->getStatusCode() . PHP_EOL;
    }, function (RequestException $exception) {
        echo $exception->getCode() . PHP_EOL;
    });
    $promises[] = $promise;
}
all($promises)->wait();

