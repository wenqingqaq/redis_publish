<?php

$sock = socket_create(AF_INET, SOCK_STREAM, 0);
$data = include 'common.php';
socket_bind($sock, "0.0.0.0", $data['port']);
socket_listen($sock, 511); 
socket_set_option($sock, SOL_SOCKET, SO_REUSEADDR, 1); 
socket_set_nonblock($sock); 
$clients = array($sock);

do{ 
    $read = $clients;
    $es = array();
    socket_select($read, $ws, $es, 3); //这里阻塞，一旦有输入就会执行循环
    //读取事件
    foreach($read as $read_sock){
        if($read_sock == $sock){
           $cfd = socket_accept($sock); 
           socket_set_nonblock($cfd);
           $clients[] = $cfd;
           echo "new client coming, fd=$cfd\n";
        }else{
            $msg = @socket_read($read_sock, 1024);
            if(strlen($msg) <= 0){
                //echo "close\n";
                //socket_close($read_sock);
            }else{
                echo "on message, fd=$read_sock data=$msg\n";
                $msg .= "from serve";
                socket_write($read_sock,$msg,strlen($msg));
            }
        }
    }
}while(true);