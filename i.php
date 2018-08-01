<?php

require __DIR__ . '/vendor/autoload.php';

use Psr\Http\Message\ServerRequestInterface;
use React\EventLoop\Factory;
use React\Http\Response;
use React\Http\Server;
use React\Stream\ReadableResourceStream;
use React\Stream\ThroughStream;

$loop = Factory::create();

$frameFiles = glob('./ansi/frame*\.ansi');
$frames=[];
foreach ($frameFiles as $frameFile) {
    $frames[] = @file_get_contents($frameFile);
}

$server = new Server(function (ServerRequestInterface $request) use ($loop, $frames) {
    if ($request->getMethod() !== 'GET' || $request->getUri()->getPath() !== '/') {
        return new Response(301, ['Location' => 'https://github.com/zaplitnyak/rainbowfrog']);
    }

    $frameIndex = 0;
    $stream = new ThroughStream();

    $loop->addPeriodicTimer(0.06, function () use ($stream, $frames, &$frameIndex) {
        $stream->write("\033[2J\033[H");
        if ($frameIndex === count($frames)) $frameIndex=0;
        $stream->write($frames[$frameIndex++]);
    });

    return new Response(
        200,
        [
            'Content-Type' => 'tzext/plain',
            'x-backend' => 'wwwR1',
        ],
        $stream
    );
});
$socket = new \React\Socket\Server(isset($argv[1]) ? $argv[1] : '127.0.0.1:9080', $loop);
$server->listen($socket);

echo 'Listening on ' . str_replace('tcp:', 'http:', $socket->getAddress()) . PHP_EOL;

$loop->addPeriodicTimer(5, function () {
    $memory = memory_get_usage() / 1024;
    $formatted = number_format($memory, 3).'K';
    echo "Current memory usage: {$formatted}" . PHP_EOL;
});

$loop->run();
