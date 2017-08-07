<?php

// exit;
//cron head
require __DIR__."/cron.ini.php";
$redis = new redisCls;
$mysql = new db;
define('CRONTAG', 'USER_CHECK');

$keys = $redis->keys('lord_user_model_*');
$i=0;
foreach ( $keys as $k => $v )
{
	$redis->del($v);
	$i++;
}
echo $i;
// $week = $redis->hget('lord_model_weeks_1','1_0_20141110');

// $weekplays = $redis->hgetall('lord_model_weekplay_1');
// $weekplays = $weekplays ? $weekplays : array();
// $weekRank = array();
// $_weekplays = array();
// foreach ( $weekplays as $k => $v )
// {
// 	$_weekplays[strval($v['weekPoint']*10000000+$v['uid'])] = $v;
// }
// krsort($_weekplays);
// $i = 1;
// foreach ( $_weekplays as $k => $v )
// {
// 	$nick = $mysql->getVar("SELECT `nick` from `lord_game_user` where `uid` = ".$v['uid']);
// 	$weekRank[]=array('rank'=>$i,'uid'=>$v['uid'],'nick'=>$nick,'point'=>$v['weekPoint']);
// 	$i++;
// }

// // print_r($weekRank);

// $week['weekRank'] = $weekRank;

// // print_r($week);

// $redis->hset('lord_model_weeks_1','1_0_20141110',$week);
