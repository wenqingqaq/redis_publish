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
    $data = include 'common.php';
    if(socket_connect($socket,'127.0.0.1',$data['port']) == false){
        echo 'connect fail massege:'.socket_strerror(socket_last_error());
    }else{
        $message = mb_convert_encoding('AI频道发送信息: '.$msg,'GBK','UTF-8');
        //向服务端写入字符串信息
        if(socket_write($socket,$message,strlen($message)) == false){
            echo 'fail to write'.socket_strerror(socket_last_error());
        }else{
            echo 'client write success'.PHP_EOL;
            //读取服务端返回来的套接流信息
            while($callback = socket_read($socket,1024)){
                echo 'server return message is:'.PHP_EOL.$callback;
            }
        }
    }
    socket_close($socket);//工作完毕，关闭套接流
}