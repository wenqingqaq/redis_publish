<?php
error_reporting(E_ALL);
set_time_limit(0);
$data = include "common.php";
$port = $data['port'];//端口
$ip = "127.0.0.1";//ip
$socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
if ($socket < 0) {
    echo "socket_create() failed: reason: " . socket_strerror($socket) . "\n";
}
$result = socket_connect($socket, $ip, $port);
if ($result < 0) {
    echo "socket_connect() failed.\nReason: ($result) " . socket_strerror($result) . "\n";
}
$in = "send socket msg \n";
if(!socket_write($socket, $in, strlen($in))) {
    echo "socket_write() failed: reason: " . socket_strerror($socket) . "\n";
}

//读取服务端返回来的套接流信息
while($callback = socket_read($socket,1024)){
    echo 'server return message is:'.PHP_EOL.$callback;
}
//$callback = socket_read($socket,2048);
//echo 'server return message is:'.$callback.PHP_EOL;

socket_close($socket);