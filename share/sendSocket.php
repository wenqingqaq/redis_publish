<?php
/**
 * Created by PhpStorm.
 * User: yanwenqing
 * Date: 2018/4/28
 * Time: 16:26
 */

//测试项端口发送信息

$socket = socket_create(AF_INET,SOCK_STREAM,SOL_TCP);
$data = include 'common.php';
if(socket_connect($socket,'127.0.0.1',$data['port']) == false){
    echo 'connect fail massege:'.socket_strerror(socket_last_error());
}else{
    echo "send \n";
    $message = mb_convert_encoding('测试向'.$data['port'].'端口发送信息','GBK','UTF-8');
    //向服务端写入字符串信息
    if(socket_write($socket,$message,strlen($message)) == false){
        echo 'fail to write'.socket_strerror(socket_last_error());
    }
    while($callback = socket_read($socket,1024)){
        echo 'server return message is:'.PHP_EOL.$callback;
    }
}
socket_close($socket);//工作完毕，关闭套接流