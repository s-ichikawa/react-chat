<?php

namespace Example\SendGrid;

use Clue\React\Buzz\Browser;
use Psr\Http\Message\ResponseInterface;
use React\EventLoop\Factory;
use React\Socket\Connector;

require_once __DIR__ . '/../../vendor/autoload.php';

$loop = Factory::create();
$connector = new Connector($loop, array(
    'dns' => '8.8.8.8',
));
$browser = new Browser($loop, $connector);
for ($i = 0; $i < 1000; $i++) {
    $browser->post('https://api.sendgrid.com/v3/mail/send', [
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
    ]))
    ->then(function (ResponseInterface $response) use ($i) {
        echo $i . ':' . $response->getStatusCode() . PHP_EOL;
    });
}

$loop->run();