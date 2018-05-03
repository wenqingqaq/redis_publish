<?php

$sock = socket_create(AF_INET, SOCK_STREAM, 0); 
socket_bind($sock, "0.0.0.0", 1234); 
socket_listen($sock, 511); 
socket_set_option($sock, SOL_SOCKET, SO_REUSEADDR, 1); 
socket_set_nonblock($sock); 
$clients = array($sock);

function hand($sock, $data) {
    if (preg_match("/Sec-WebSocket-Key: (.*)\r\n/", $data, $match)) {
        echo "hand \n";
        $response = base64_encode(sha1($match[1] . '258EAFA5-E914-47DA-95CA-C5AB0DC85B11', true));
        $upgrade  = "HTTP/1.1 101 Switching Protocol\r\n" .
            "Upgrade: websocket\r\n" .
            "Connection: Upgrade\r\n" .
            "Sec-WebSocket-Accept: " . $response . "\r\n\r\n";
        socket_write($sock, $upgrade, strlen($upgrade));
    }
}

do{ 
    $read = $clients;
    $es = array();
    $first = true;
    socket_select($read, $ws, $es, 3);
    //读取事件
    foreach($read as $read_sock){
        echo 'foreach';
        if($read_sock == $sock){
           $cfd = socket_accept($sock); 
           socket_set_nonblock($cfd);
           $clients[] = $cfd;
           echo "new client coming, fd=$cfd\n";
        }else{
            $msg = @socket_read($read_sock, 1024);
            if($first){
                echo "first \n";
                hand($read_sock,$msg);
                $first = false;
            }
            echo "msg = $msg \n";
            if(strlen($msg) <= 0){
                echo "close \n";
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