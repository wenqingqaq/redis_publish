<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/5/1
 * Time: 10:56
 */

$redis = new Redis();
$res = $redis->connect('127.0.0.1', 6379);
$res = $redis->publish('AI',$_GET['msg']);
$redis->close();
echo 'send '.$_GET['msg'].' success!';