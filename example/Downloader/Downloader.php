<?php

namespace Example\Downloader;

use React\EventLoop\Factory;
use React\EventLoop\LoopInterface;
use React\HttpClient\Client;
use React\HttpClient\Request;
use React\HttpClient\Response;
use React\Stream\ThroughStream;
use React\Stream\WritableResourceStream;

require_once __DIR__ . '/../../vendor/autoload.php';

class Downloader
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

    /**
     * @param array $files
     */
    public function download(array $files)
    {
        foreach ($files as $index => $file) {
            $this->initRequest($file, $index + 1);
        }

        echo str_repeat("\n", count($this->requests));

        $this->runRequests();
    }

    /**
     * @param $url
     * @param $position
     */
    protected function initRequest($url, $position)
    {
        $file_name = basename($url);
        $file = new WritableResourceStream(fopen(__DIR__ . '/../../tmp/' . $file_name, 'w'), $this->loop);

        $request = $this->client->request('GET', $url);
        $request->on('response', function (Response $response) use ($file, $file_name, $position) {
            $headers = $response->getHeaders();
            $size = $headers['Content-Length'];

            $progress = $this->makeProgressStream($size, $file_name, $position);

            $response->pipe($progress)->pipe($file);
        });

        $this->requests[] = $request;
    }

    /**
     * @param int $size
     * @param string $file_name
     * @param int $position
     * @return ThroughStream
     */
    protected function makeProgressStream($size, $file_name, $position)
    {
        $current_size = 0;

        $progress = new ThroughStream();
        $progress->on('data', function ($data) use ($size, &$current_size, $file_name, $position) {
            $current_size += strlen($data);
            $percent = number_format($current_size / $size * 100);

            echo str_repeat("\033[1A", $position), "$file_name: ", $percent, "%", str_repeat("\n", $position);
        });

        return $progress;
    }

    protected function runRequests()
    {
        foreach ($this->requests as $request) {
            $request->end();
        }

        $this->requests = [];

        $this->loop->run();
    }
}

$loop = Factory::create();
$client = new Client($loop);

$files = [
];

$downloader = new Downloader($client, $loop);
$downloader->download($files);
