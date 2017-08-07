<?php

exit;
//cron head
require __DIR__."/cron.ini.php";
$redis = new redisCls;
$mysql = new db;
define('CRONTAG', 'MODELGAME_CHANGE');

$game = $redis->hget('lord_model_games_1','1_0_20141110_4');

$sql = "SELECT * FROM `lord_model_rooms` where `modelId` = 1";
$rooms = $mysql->getData($sql);
$room = array();
if ( $rooms )
{
	$data = array();
	foreach ( $rooms as $k=>$v )
	{
		foreach ( $v as $kk => $vv )
		{
			$v[$kk] = is_numeric($vv) ? intval($vv) : $vv;
		}
		$v['gameOpenSetting']= json_decode(stripslashes($v['gameOpenSetting']),1);
		$v['gamePrizeCoins'] = json_decode(stripslashes($v['gamePrizeCoins']),1);
		$v['gamePrizePoint'] = json_decode(stripslashes($v['gamePrizePoint']),1);
		$v['gamePrizeProps'] = json_decode(stripslashes($v['gamePrizeProps']),1);
		$v['weekPrizeCoins'] = json_decode(stripslashes($v['weekPrizeCoins']),1);
		$v['weekPrizeProps'] = json_decode(stripslashes($v['weekPrizeProps']),1);
		$data[$v['roomsId']] = $v;
	}
	$rooms = $data;
	$room = $data['1_1004'];
	unset($room['id']);
	unset($room['create_time']);
	unset($room['update_time']);
	$res = $redis->hmset("lord_model_rooms_1",$rooms);
	if ( !$res )
	{
		// return false;
	}
}
// print_r($game);
foreach ( $game as $k => $v )
{
	if ( isset($room[$k]) ) {
		$game[$k] = $room[$k];
	}
	else{
		$game[$k] = $v;
	}
}
// print_r($game);

$res = $redis->hset('lord_model_games_1','1_0_20141110_4', $game);


