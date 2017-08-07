<?php
/**
 * 系统探针脚本
 * Created by PhpStorm.
 * User: huangxiufeng
 * Date: 16/9/2
 * Time: 下午4:06
 */


define('SIGN_KEY', 'be9761dba879ddf1fb396c8db1db5125');

$phones = [
    '18201837035',
    '18601652288',
    '13120621616',
    '18756011262',
    '18616388842',
    '15021829660',
];

function SMS($phones = [], $msg = '')
{
    if (count($phones) == 0) {
        return;
    }
    foreach ($phones as $phone) {
        $request = [
            'phonenumber' => $phone,
            'content'     => $msg,
            'sign'        => md5($phone . SIGN_KEY),
        ];
        file_get_contents('http://sdk.youjoy.tv/sendsms.php?' . http_build_query($request));
    }
}

$now = time() - 120;

require("/data/sweety/conf/cron.php");


$redis = getRedis();
$mysql = getMysql();

$now_time_str = date('Y-m-d H:i:00', $now);
$one_minute_ago_str = date('Y-m-d H:i:00', $now - 60);
$five_minute_ago_str = date('Y-m-d H:i:00', $now - 300);

var_dump($now_time_str, $one_minute_ago_str, $five_minute_ago_str);

//1、每分钟监控一次在线用户，下降10%
$now_num = $mysql->getVar(" select num from lord_game_online where add_time>='$now_time_str' limit 1");
$one_minute_num = $mysql->getVar(" select num from lord_game_online where add_time>='$one_minute_ago_str' limit 1");

$rate = ($now_num - $one_minute_num) / $one_minute_num;

echo sprintf("当前在线:%d 一分钟前在线:%d 比率:%s", $now_num, $one_minute_num, $rate);

if ($rate <= -0.1) {
    SMS($phones, sprintf("服务器在线数 从[%s]的%d人 下降到 [%s]的%d人 ,请检查!", $one_minute_ago_str, $one_minute_num, $now_time_str, $now_num));
}

