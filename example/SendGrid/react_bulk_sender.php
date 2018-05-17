<?php

namespace Example\SendGrid;

use React\EventLoop\Factory;
use React\EventLoop\LoopInterface;
use React\HttpClient\Client;
use React\HttpClient\Request;
use React\HttpClient\Response;
use React\Socket\Connector;

require_once __DIR__ . '/../../vendor/autoload.php';

class ReactBulkSender
{
    /**
     * @var Client
     */
    private $client;
    /**
     * @var LoopInterface
     */
    private $loop;

    /**
     * @var Request[]
     */
    private $requests;

    /**
     * Downloader constructor.
     * @param Client $client
     * @param LoopInterface $loop
     */
    public function __construct(Client $client, LoopInterface $loop)
    {
        $this->client = $client;
        $this->loop = $loop;
    }

    public function send()
    {
        $this->runRequests();
    }

    protected function getRequest()
    {
        for ($i = 0; $i < 1000; $i++) {
            $payload = json_encode([
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
            ]);

            $request = $this->client->request('POST', 'https://api.sendgrid.com/v3/mail/send', [
                'Authorization' => 'Bearer ' . getenv('SENDGRID_API_KEY'),
                'Content-Type' => 'application/json',
                'Content-Length' => strlen($payload),
            ], '1.1');

            $request->on('response', function (Response $response) use ($i) {
                echo $i . ':' . $response->getCode() . PHP_EOL;
            });

            yield [
                'request' => $request,
                'payload' => $payload,
            ];
        }
    }

    protected function runRequests()
    {
        foreach ($this->getRequest() as $request) {
            $r = $request['request'];
            /** @var $r Request */
            $r->end($request['payload']);
        }

        $this->requests = [];

        $this->loop->run();
    }
}

$loop = Factory::create();
$connector = new Connector($loop, array(
    'dns' => '8.8.8.8',
));
$client = new Client($loop, $connector);

$bulkSender = new ReactBulkSender($client, $loop);
$bulkSender->send();

