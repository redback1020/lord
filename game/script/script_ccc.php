<?php

// exit;
//cron head
require __DIR__."/cron.ini.php";
$redis = new redisCls;
$mysql = new db;
define('CRONTAG', 'USER_CHECK');

// 		$sql = "SELECT * FROM `lord_model_rooms` where `modelId` = 1";
// 		$rooms = $mysql->getData($sql);
// 			$data = array();
// 		if ( $rooms )
// 		{
// 			foreach ( $rooms as $k=>$v )
// 			{
// 				$v['gameOpenSetting']= json_decode($v['gameOpenSetting'],1);
// 				$v['gamePrizeCoins'] = json_decode($v['gamePrizeCoins'],1);
// 				$v['gamePrizePoint'] = json_decode($v['gamePrizePoint'],1);
// 				$v['gamePrizeProps'] = json_decode($v['gamePrizeProps'],1);
// 				$v['weekPrizeCoins'] = json_decode($v['weekPrizeCoins'],1);
// 				$v['weekPrizeProps'] = json_decode($v['weekPrizeProps'],1);
// 				$data[$v['roomsId']] = $v;
// 			}
// 		}

// print_r($data);

$week = $redis->hget('lord_model_weeks_1','1_0_20141117');

print_r($week);


exit;

