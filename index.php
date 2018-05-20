<?php

if (strpos($_SERVER['HTTP_USER_AGENT'], 'curl') === false) {
    echo '<img src="orig/RAINBOW_FROG.gif" alt="rainbow frog gif" /><br/><a href="https://github.com/zaplitnyak/rainbowfrog">https://github.com/zaplitnyak/rainbowfrog</a>';
    return 0;
}


ob_start();
header('Content-Encoding: chunked');
header('Content-Type: text/html');
header('Connection: keep-alive');
header('X-Accel-Buffering: no');
ob_flush();
flush();

$stream = fopen('php://memory','r+');
register_shutdown_function(function () use($stream) { fclose($stream); });

$frames = glob('./ansi/frame*\.ansi');
$frameCount = count($frames);
$frameIndex = 0;

while (true) {
    $frameIndex === $frameCount AND $frameIndex = 0;
    rewind($stream);
    $frameString = "\033[2J\033[H". file_get_contents($frames[$frameIndex++]);
    fwrite($stream, $frameString);
    rewind($stream);
    echo stream_get_contents($stream, strlen($frameString));
    ob_flush();
    flush();
    usleep(60000);
}
