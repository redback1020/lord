<?php

// exit;
//cron head
require __DIR__."/cron.ini.php";
$redis = new redisCls;
$mysql = new db;
define('CRONTAG', 'USER_CHECK');

$weekplayer = $redis->hgetall('lord_model_weekplay_1');
$uidnick = array();
foreach ( $weekplayer as $k => $v )
{
	$uid = $v['uid'];
	if (!isset($v['nick'])) 
	{
		$sql = "SELECT nick FROM `lord_game_user` where `uid` = $uid";
		$nick = $mysql->getVar($sql);
		$nick = $nick ? trim($nick) : ('新手'.$v['cool_num']);
		$v['nick'] = $nick;
		$uidnick[$uid] = $nick;
		$redis->hset('lord_model_weekplay_1',$k,$v);
	}
}
$weeks = $redis->hgetall('lord_model_weeks_1');
foreach ( $weeks as $k => $v )
{
	$week = $v;
	$rank = $week['weekRank'];
	foreach ( $rank as $kk => $vv )
	{
		if ( !$vv['nick'] ) 
		{
			$vv['nick'] = isset($uidnick[$vv['uid']]) ? $uidnick[$vv['uid']] : "新手?" ;
			$rank[$kk] = $vv;
		}
	}
	$v['weekRank'] = $rank;
	$weeks[$k] = $v;
	$redis->hset('lord_model_weeks_1',$k,$v);
}

print_r($weeks);

exit;

