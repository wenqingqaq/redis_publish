<?php
/**
 * Created by PhpStorm.
 * User: yanwenqing
 * Date: 2018/4/28
 * Time: 16:05
 */


/**
 * 首次与客户端握手
 */
function hand($sock, $data) {
    if (preg_match("/Sec-WebSocket-Key: (.*)\r\n/", $data, $match)) {
        echo 'match';
        $response = base64_encode(sha1($match[1] . '258EAFA5-E914-47DA-95CA-C5AB0DC85B11', true));
        $upgrade  = "HTTP/1.1 101 Switching Protocol\r\n" .
            "Upgrade: websocket\r\n" .
            "Connection: Upgrade\r\n" .
            "Sec-WebSocket-Accept: " . $response . "\r\n\r\n";
        socket_write($sock, $upgrade, strlen($upgrade));
    }
}

//打开一个socket常驻服务，这样才可以让web端进行连接端口
$socket = socket_create(AF_INET,SOCK_STREAM,SOL_TCP);

/*绑定接收的套接流主机和端口,与客户端相对应*/
$data = include 'common.php';
socket_set_option($socket, SOL_SOCKET, SO_REUSEADDR, TRUE); //长连接使用
if(socket_bind($socket,'127.0.0.1',$data['port']) == false){
    echo 'server bind fail:'.socket_strerror(socket_last_error());
}
//监听套接流
if(socket_listen($socket,4)==false){
    echo 'server listen fail:'.socket_strerror(socket_last_error());
}
//让服务器无限获取客户端传过来的信息
$cycle = array();
$cycle[] = $socket;
$first = false;
do{
    echo "start\n";
    socket_select($cycle, $write, $except, null);
    foreach ($cycle as $k => $s){
        if($s == $socket){
            echo "in\n";
            $client = socket_accept($s);
            if($client < 0){
                echo "socket_accept() failed\n";
                continue;
            }else{
                $cycle[] = $client;
                $first = false;
            }
        }else{
            echo "not\n";
            $bytes = @socket_recv($s,$buffer,2048,0);
            echo "b = ".$bytes."\n";
            if($bytes == 0) return;
            if(!$first){
                echo "first\n";
                hand($s,$buffer);
                $first = true;
            }else{
                echo "send\n";
                echo $bytes."\n";
            }
        }
    }
}while(true);

socket_close($socket);