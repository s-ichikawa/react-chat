<?php

namespace Example\SendGrid;

use GuzzleHttp\Client;

require_once __DIR__ . '/../../vendor/autoload.php';

$client = new Client();
for ($i = 0; $i < 1000; $i++) {
    $response = $client->get('http://127.0.0.1:8080');
    echo $i . ':' . $response->getStatusCode() . PHP_EOL;
}

