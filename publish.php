<?php
/**
 * Created by PhpStorm.
 * User: yanwenqing
 * Date: 2018/4/28
 * Time: 15:49
 */

//想AI频道发布一条新闻
$redis = new Redis();
$res = $redis->connect('127.0.0.1', 6379);
$res = $redis->publish('AI','this is a message!');
$redis->close();