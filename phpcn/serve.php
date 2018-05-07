<?php

$sock = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
$data = include "common.php";
socket_bind($sock, "127.0.0.1", $data['port']);
socket_listen($sock, 511); 
socket_set_option($sock, SOL_SOCKET, SO_REUSEADDR, 1); 
socket_set_nonblock($sock); 
$clients = array($sock);
function hand($sock, $data) {
    if (preg_match("/Sec-WebSocket-Key: (.*)\r\n/", $data, $match)) {
        $response = base64_encode(sha1($match[1] . '258EAFA5-E914-47DA-95CA-C5AB0DC85B11', true));
        $upgrade  = "HTTP/1.1 101 Switching Protocol\r\n" .
            "Upgrade: websocket\r\n" .
            "Connection: Upgrade\r\n" .
            "Sec-WebSocket-Accept: " . $response . "\r\n\r\n";
        socket_write($sock, $upgrade, strlen($upgrade));
    }
}

$first = true;
$hand = array();
do{
    $read = $clients;
    socket_select($read, $ws, $es, null); //筛选socket
    //读取事件
    foreach($read as $k => $read_sock){
        if($read_sock == $sock){
           $cfd = socket_accept($sock); 
           socket_set_nonblock($cfd);
           $clients[] = $cfd;
           echo "new client coming, fd=$cfd\n";
           continue;
        }else{
            $msg = @socket_read($read_sock, 1024);
            $key = array_search($sock, $clients);
            echo 'c = '.count($read)."\n";
            if($first){
                echo "first \n";
                hand($read_sock,$msg);
                $first = false;
            }
            if(strlen($msg) <= 0){
                //echo "close \n";
                //unset($read[$k]);
            }else{                
                echo "on message, fd=$read_sock data=$msg\n";
                $msg .= "from serve";
                socket_write($read_sock,$msg,strlen($msg));
            }
        }
    }
    //写入事件
//    $msg = 'test';
//    foreach($clients as $read_sock){
//        if ($read_sock == $sock) continue;
//        echo "write_ing".$read_sock."\n";
//        socket_write($read_sock,$msg,strlen($msg));
//    }
}while(true);