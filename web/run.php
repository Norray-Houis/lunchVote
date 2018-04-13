<?php

require __DIR__ . '/react.phar';
require __DIR__ . '/nlf.phar';

use React\EventLoop\Factory;
use React\Socket\Server;
use React\Http\Request;
use React\Http\Response;

require 'vendor/autoload.php';

ob_start();
include 'index.php';
$string = ob_get_clean();

$loop = Factory::create();
$socket = new Server($loop);
$server = new \React\Http\Server($socket);
$server->on('request', function (Request $reques, Response $response) {

    ob_start();
    include 'index.php';
    $string = ob_get_clean();
    
    $response->writeHead(200, array('Content-Type' => 'text/plain'));
    
    $response->end($string);
});
$socket->listen(isset($argv[1]) ? $argv[1] : 0, '0.0.0.0');
echo 'Listening on ' . $socket->getPort() . PHP_EOL;
$loop->run();