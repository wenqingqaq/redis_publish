<?php
error_reporting(E_ALL);
set_time_limit(0);
$data = require 'common.php';
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
$in = "wenqing_test_test测试 \n";
$in = utf8_encode($in);
if(!socket_write($socket, $in, strlen($in))) {
    echo "socket_write() failed: reason: " . socket_strerror($socket) . "\n";
}

//读取服务端返回来的套接流信息
$callback = socket_read($socket,1024);
echo 'server return message is:'.PHP_EOL.$callback;

//$callback = socket_read($socket,2048);
//echo 'server return message is:'.$callback.PHP_EOL;

socket_close($socket);

function frame($s){
    $a = str_split($s, 125);
    if (count($a) == 1){
        return "\x81" . chr(strlen($a[0])) . $a[0];
    }
    $ns = "";
    foreach ($a as $o){
        $ns .= "\x81" . chr(strlen($o)) . $o;
    }
    return $ns;
}