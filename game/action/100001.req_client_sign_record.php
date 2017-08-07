<?php
/**
 * type
    0注册成功
    1登录成功
    2进入大厅
    3弹出新手礼包
    4点击购买新手礼包
    5点击取消购买新手礼包
    6弹出签到界面
    7点击领取签到
    8关闭签到
    9弹出包月礼包
    10购买包月礼包
    11取消包月礼包
    12弹出广告
    13关闭广告
    14进入水果机
    15进入牛牛
    16点击广告
    17点击进入经典场二级界面
    18点击进入癞子场二级界面
    19点击进入比赛场二级界面
    20点击进入游戏
    21成功进入游戏
    22点击进入游戏到成功进入游戏时长
    23点击开始游戏
    24第一次发牌
    25第一次出牌
    26第一次结算
    27弹出按键提示
    28关闭按键提示
*/


$type = $params['type'];
$channel = $params['channel'];
$extend  = $params['eid'];

$step = 1;
$redis = $this->model->redis;
$key = "client:sign:record";
$redis->hincrby($key, "$channel-$type", $step);
if($type == 20){//点击进入游戏
    debug("ROG1:$extend $type ");
    $sub_key = "client:sign:extends";
    $click_game_time = microtime(1);
    $dd =$redis->hset($sub_key, $extend, $click_game_time);
    debug("ROG:$extend $type ".$dd);
}
if($type == 21){//成功进入游戏
    $sub_key = "client:sign:extends";
    $click_game_time = $redis->hget($sub_key, $extend);
    $ustime = number_format(microtime(1) - $click_game_time, 3);
    $ustime = $ustime*1000;
    $key = "client:sign:logintimes";
    $redis->hincrby($key, "$channel-login_times", $step);
    $type=22;
    $key = "client:sign:time";
    $redis->hincrby($key, "$channel-$type", $ustime);
}
?>