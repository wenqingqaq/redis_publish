<?php

$sock = socket_create(AF_INET, SOCK_STREAM, 0); 
socket_bind($sock, "0.0.0.0", 1234); 
socket_listen($sock, 511); 
socket_set_option($sock, SOL_SOCKET, SO_REUSEADDR, 1); 
socket_set_nonblock($sock); 
$clients = array($sock);
  
do{ 
    $read = $clients;
    $es = array(); 
    socket_select($read, $ws, $es, 3);
    //读取事件
    foreach($read as $read_sock){ 
        if($read_sock == $sock){
           $cfd = socket_accept($sock); 
           socket_set_nonblock($cfd);
           $clients[] = $cfd;
           echo "new client coming, fd=$cfd\n";
        }else{
            $msg = socket_read($read_sock, 1024);
            echo "start \n";
            if($msg <= 0){ 
                //close 
            }else{                
                echo "on message, fd=$read_sock data=$msg\n"; 
            }
            //$msg = "write_serve";
            //socket_write($read_sock,$data,strlen($msg));
        }
    }
   
    //写入事件
    $msg = 'test';
    foreach($clients as $read_sock){
        if ($read_sock == $sock) continue;
        echo "write_ing".$read_sock."\n";
        socket_write($read_sock,$msg,strlen($msg));
    }
}while(true);