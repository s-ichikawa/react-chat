<?php
namespace Example\EventLoop;

require_once __DIR__ . '/../../vendor/autoload.php';

use React\EventLoop\Factory;

$loop = Factory::create();

$loop->run();
