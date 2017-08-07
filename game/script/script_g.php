<?php

//cron head
require dirname(dirname(__DIR__))."/conf/cron.php";
$redis = getRedis();
// $mysql = getMysql();
define('CRONTAG', 'DEL_USER_RECORD_LOTTERY');

$keys = $redis->keys('lord_user_lottery_*');
$keys = $keys ? $keys : array();
foreach ($keys as $key => $value) {
	$redis->del($key);
}

echo count($keys)."\n";


exit;




// while ( true ) {
// 	$file = 'lottery_uid.log';
// 	touch($file);
// 	$ud = file_get_contents($file);
// 	$ud = intval($ud);
// 	$i = 0;
// 	while ( $i < 2000 ) {
// 		$redis->del("lord_user_lottery_$ud");
// 		$i++;
// 		$ud++;
// 	}
// 	sleep(1);
// 	file_put_contents($file,$ud);
// 	echo $ud."\n";
// 	if ( $ud > 3610000 ) {
// 		exit;
// 	}
// }
//
//
// exit;



// define('CRONTAG', 'MODIFY_MODEL_GAME_CONFIG');
//
// $room = $redis->hget('lord_model_rooms_1', '1_1004');
// if ( isset($room['gamePrizeProps']['1-9']['2']) ) {
// 	$room['gamePrizeProps']['1-9']['2'] = '高手套装(1天)';
// }
// if ( isset($room['weekPrizeProps']['1-9']['3']) ) {
// 	$room['weekPrizeProps']['1-9']['3'] = '大师套装(7天)';
// }
// $redis->hset('lord_model_rooms_1', '1_1004', $room);
// $room = $redis->hget('lord_model_rooms_1', '1_1004');
// if ( isset($room['gamePrizeProps']['1-9']['2']) ) {
// 	echo $room['gamePrizeProps']['1-9']['2'];
// 	echo "\n";
// }
// if ( isset($room['weekPrizeProps']['1-9']['3']) ) {
// 	echo $room['weekPrizeProps']['1-9']['3'];
// 	echo "\n";
// }
// echo json_encode($room);
// echo "\n";
//
//
// exit;



// $tabletimer = $redis->hgetall('lord_table_timer');
// $time = time();
// foreach ( $tabletimer as $k => $v )
// {
// 	$table = $redis->hgetall('lord_table_info_'.$k);
// 	// if($table){var_dump($table);exit;}
// 	if (!$table) {
// 		$redis->hdel("lord_table_timer",$k);
// 	}
// }
