<?php

//cron head
require dirname(dirname(__DIR__))."/conf/cron.php";
$redis = getRedis();
// $mysql = getMysql();
define('CRONTAG', 'REDIS_CLEANER');

$weekid = intval(date("Ymd",time()-(date("N")-1)*86400));
$lastweekid = intval(date("Ymd",time()-(date("N")-1)*86400-7*86400));
$dateid = intval(date("Ymd"));
$lastdateid = intval(date("Ymd",time()-86400));

// $keys = $redis->redis->keys('lord_stwin_20*');
// foreach ( $keys as $k => $key )
// {
// 	$vv = explode('_', $key);
// 	if ( count($vv) != 3 ) continue;
// 	if ( $vv[2] != $dateid ) {
// 		// var_dump($vv);
// 		sleep(2);
// 		$redis->del($key);
// 	}
// }
// $keys = $redis->redis->keys('lord_stlos_20*');
// foreach ( $keys as $k => $key )
// {
// 	$vv = explode('_', $key);
// 	if ( count($vv) != 3 ) continue;
// 	if ( $vv[2] != $dateid ) {
// 		// var_dump($vv);
// 		sleep(2);
// 		$redis->del($key);
// 	}
// }
// $keys = $redis->redis->keys('lord_gold_day_220*');
// foreach ( $keys as $k => $key )
// {
// 	$vv = explode('_', $key);
// 	if ( count($vv) != 4 ) continue;
// 	if ( $vv[3] != ('2'.$dateid) ) {
// 		// var_dump($vv);
// 		$redis->del($key);
// 	}
// }
// $keys = $redis->redis->keys('lord_trial_20*');
// foreach ( $keys as $k => $key )
// {
// 	$vv = explode('_', $key);
// 	if ( count($vv) != 3 ) continue;
// 	if ( $vv[2] != $dateid ) {
// 		// var_dump($vv);
// 		sleep(2);
// 		$redis->del($key);
// 	}
// }
// $keys = $redis->redis->keys('lord_list_*');
// foreach ( $keys as $k => $key )
// {
// 	$vv = explode('_', $key);
// 	if ( count($vv) != 6 || ! in_array($vv[2], array('normal','match')) || ! in_array($vv[3], array('day','week')) ) continue;
// 	if ( $vv[3] == 'day' && $vv[5] != $dateid ) {
// 		// var_dump($vv);
// 		sleep(2);
// 		$redis->del($key);
// 	}
// 	if ( $vv[3] == 'week' && !($vv[5] == $weekid || $vv[5] == $lastweekid) ) {
// 		// var_dump($vv);
// 		sleep(2);
// 		$redis->del($key);
// 	}
// }
// $keys = $redis->redis->hkeys('lord_model_games_1');
// foreach ( $keys as $k => $v )
// {
// 	$vv = explode('_', $v);
// 	if ( $vv[2] != $weekid ) {
// 		// var_dump($vv);
// 		$redis->hdel('lord_model_games_1', $v);
// 	}
// }
// $keys = $redis->redis->keys("lord_model_gameplay_*");
// foreach ( $keys as $k => $key )
// {
// 	$vv = explode('_', $key);
// 	if ( $vv[5] != $weekid ) {
// 		// var_dump($vv);
// 		$redis->del($key);
// 	}
// }
// $keys = $redis->redis->keys("lord_model_goonplay_*");
// foreach ( $keys as $k => $key )
// {
// 	$vv = explode('_', $key);
// 	if ( $vv[5] != $weekid ) {
// 		// var_dump($vv);
// 		$redis->del($key);
// 	}
// }
// $keys = $redis->redis->keys("lord_libao_2016*");
// foreach ( $keys as $k => $key )
// {
// 	$vv = explode('_', $key);
// 	if ( $vv[2] != $dateid ) {
// 		// var_dump($vv);
// 		sleep(2);
// 		$redis->del($key);
// 	}
// }
// $keys = $redis->redis->keys("lord_libao_xingyun*");
// foreach ( $keys as $k => $key )
// {
// 	$vv = explode('_', $key);
// 	if ( $vv[4] != $dateid ) {
// 		// var_dump($key);
// 		sleep(2);
// 		$redis->del($key);
// 	}
// }
// $keys = $redis->redis->keys("lord_libao_mianze*");
// foreach ( $keys as $k => $key )
// {
// 	$vv = explode('_', $key);
// 	if ( $vv[4] != $dateid ) {
// 		// var_dump($key);
// 		sleep(2);
// 		$redis->del($key);
// 	}
// }
// $keys = $redis->redis->keys("lord_libao_dznm_times_*");
// foreach ( $keys as $k => $key )
// {
// 	$vv = explode('_', $key);
// 	if ( $vv[4] != $dateid ) {
// 		// var_dump($key);
// 		sleep(2);
// 		$redis->del($key);
// 	}
// }

exit;


// $keys  = $redis->keys('lord_user_task_9*');
// if ( ! $keys ) $keys  = array();
// $i = $j = 0;
// foreach ( $keys as $k => $key )
// {
// 	$uid_ = explode('_', $key);
// 	$uid = $uid_[3];
// 	$key_user = "lord_user_info_$uid";
// 	$user = $redis->hgetall($key_user);
// 	usleep(10000);
// 	if ( ! $user ) {
// 		$redis->del($key);
// 		$i++;
// 	} else {
// 		$j++;
// 	}
// 	echo $i;
// 	echo PHP_EOL;
// 	echo $j;
// 	echo PHP_EOL;
// }
// echo count($keys);
// echo PHP_EOL;
// echo $i;
// echo PHP_EOL;
// echo $j;
// echo PHP_EOL;

exit;



// $room = $redis->hget('lord_model_rooms_1', '1_1004');
// if ( isset($room['gamePrizeCoins']) ) {
// 	$room['gamePrizeCoins'] = array('1' => 22000, '2-4' => 10000, '5-9' => 5000);
// }
// if ( isset($room['gamePrizeCoupon']) ) {
// 	$room['gamePrizeCoupon'] = array('1' => 188, '2-4' => 18, '5-9' => 8);
// }
// $redis->hset('lord_model_rooms_1', '1_1004', $room);
// $room = $redis->hget('lord_model_rooms_1', '1_1004');
// if ( isset($room['gamePrizeCoins']) ) {
// 	echo json_encode($room['gamePrizeCoins']);
// 	echo "\n";
// }
// if ( isset($room['gamePrizeCoupon']) ) {
// 	echo json_encode($room['gamePrizeCoupon']);
// 	echo "\n";
// }
// echo json_encode($room);
// echo "\n";


exit;
