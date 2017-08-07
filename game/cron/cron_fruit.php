<?php
/**
 * Created by PhpStorm.
 * User: huangxiufeng
 * Date: 16/9/2
 * Time: 下午5:02
 */

//水果机在线数统计

require("/data/sweety/conf/cron.php");

$redis = getRedis();
$mysql = getMysql();

$online_num = intval($redis->redis->sCard('fruit:system:online_id'));

$mysql->runSql(sprintf("INSERT INTO `lord_stat_fruit_online` (`time`,`logday`, `logtime`,`num`) VALUES ('%s','%s','%s','%s');", time(), date("Y-m-d"), date("H:i:s"), $online_num));


var_dump(  MY_HOST,MY_PORT, MY_USER,MY_PASS,MY_BASE, MY_CHAR);