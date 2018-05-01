<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/5/1
 * Time: 10:56
 */

$redis = new Redis();
$res = $redis->connect('127.0.0.1', 6379);
if(!array_key_exists('msg', $_GET)){
	$msg = 'redis';
}else{
	$msg = $_GET['msg'];
}
$res = $redis->publish('AI',$msg);
$redis->close();
echo 'send redis '.$msg.' success!';