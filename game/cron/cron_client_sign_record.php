<?php
/**
 * Created by Zend.
 * User: Tidel
 * Date: 16/12/19
 * Time: 17:32
 */

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

//打点统计

require("/data/sweety/conf/cron.php");

$redis = getRedis();
$mysql = getMysql();

$config = ['register','login','lobby','pop_rookie_gift','buy_rookie_gift','cancel_rookie_gift','pop_sign',
    'click_sign','close_sign','pop_month_gift','buy_month_gift','cancel_month_gift','pop_advertisement',
    'cancel_advertisement','enter_friut','enter_cow','click_advertisement','classic_lobby','joker_lobby',
    'match_lobby','enter_game','enter_game_success','enter_game_avg_time','play_game','first_cards',
    'first_send_card','first_settlement','pop_key_tip','close_key_tip'];

$dd = date("Ymd")-1;
$data = array();
$key = "client:sign:record";
$sign_record_data = $redis->redis->hgetall($key);
foreach ($sign_record_data as $k=>$v){
    $channel_type = explode("-", $k);
    $data[$channel_type[0]][$config[$channel_type[1]]] = $v;
    $data[$channel_type[0]]["dd"] = $dd;
    $data[$channel_type[0]]["channel"] = $channel_type[0];
}
$redis->redis->del($key);
$key = "client:sign:logintimes";
$sign_record_logintimes_data = $redis->redis->hgetall($key);
$redis->redis->del($key);
$key = "client:sign:time";
$sign_record_time_data = $redis->redis->hgetall($key);
$redis->redis->del($key);
foreach ($sign_record_time_data as $k=>$v){
    $channel_type = explode("-", $k);
    $data[$channel_type[0]]["enter_game_avg_time"] = intval($v/$sign_record_logintimes_data[$channel_type[0]."-login_times"]); 
}

foreach ($data as $channel=>$dd)
{
    $keys = array_keys($dd);
    $values = array_values($dd);
    $col = implode("`, `", $keys);
    $val = implode("', '", $values);
    $sql = "INSERT INTO `lord_client_sign_record` (`$col`) VALUES ('$val');";
    $mysql->runSql($sql);
}
