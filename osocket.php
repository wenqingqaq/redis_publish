<?php
/**
 * Created by PhpStorm.
 * User: yanwenqing
 * Date: 2018/4/28
 * Time: 16:05
 */

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
do{

    /*接收客户端传过来的信息*/
    $accept_resource = socket_accept($socket);
    /*socket_accept的作用就是接受socket_bind()所绑定的主机发过来的套接流*/
    if($accept_resource !== false){
        /*读取客户端传过来的资源，并转化为字符串*/
        $string = trim(socket_read($accept_resource,1024));
        /*socket_read的作用就是读出socket_accept()的资源并把它转化为字符串*/
        echo mb_convert_encoding('服务端收到信息 :','GBK','UTF-8').$string.PHP_EOL;
        if($string == false) echo 'socket_read is fail';
        socket_close($accept_resource);
    }
}while(true);

socket_close($socket);