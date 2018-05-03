<?php
/**
 * Created by PhpStorm.
 * User: yanwenqing
 * Date: 2018/4/28
 * Time: 15:53
 */



//订阅redis频道 这里是一个常驻的服务，可以及时返回后台发布的新闻
ini_set('default_socket_timeout',-1);

$redis = new Redis();
$res = $redis->pconnect('127.0.0.1', 6379);
$redis->subscribe(array('AI'), 'callback');

// 回调函数,这里写处理逻辑
function callback($instance, $channelName, $msg){
    //回调订阅到消息了，发送到websocket中，这样可以在网页显示出来
    $socket = socket_create(AF_INET,SOCK_STREAM,SOL_TCP);
    //接收套接流的最大超时时间1秒，后面是微秒单位超时时间，设置为零，表示不管它
    socket_set_option($socket, SOL_SOCKET, SO_RCVTIMEO, array("sec" => 1, "usec" => 0));
     //发送套接流的最大超时时间为6秒
    socket_set_option($socket, SOL_SOCKET, SO_SNDTIMEO, array("sec" => 6, "usec" => 0));
    $data = include 'common.php';
    if(socket_connect($socket,'127.0.0.1',$data['port']) == false){
        echo 'connect fail massege:'.socket_strerror(socket_last_error());
    }else{
        echo "redis receive msg : ".$msg."\n";
        $message = mb_convert_encoding('AI: '.$msg,'GBK','UTF-8');
        //向服务端写入字符串信息
        if(!socket_write($socket,$message,strlen($message))){
            echo 'fail to write'.socket_strerror(socket_last_error());
        }
        echo 'redis send socket success'."";
    }
    socket_close($socket);
}