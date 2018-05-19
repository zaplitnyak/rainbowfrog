<?php

function generateRandomString($length = 10) {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
   
    return $randomString;
}

header("Transfer-Encoding: chunked");
header("Content-Encoding: chunked");
header("Content-Type: text/html");
header("Connection: keep-alive");
flush();
ob_flush();

$stream = fopen('php://memory','r+');
$pos=0;
register_shutdown_function(function () use($stream) { fclose($stream); });

while (true) {
    $begin = "\033[2J\033[H"; 
    fwrite($stream, $begin);
    rewind($stream);
    echo stream_get_contents($stream, $pos += strlen($begin));
    $str = generateRandomString();
    fwrite($stream, $str);
    rewind($stream);
    echo stream_get_contents($stream, $pos, $pos += strlen($str));
    flush();
    ob_flush();
}
