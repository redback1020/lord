<?php

//cron head
require dirname(dirname(__DIR__))."/conf/cron.php";
$redis = getRedis();
// $mysql = getMysql();
define('CRONTAG', 'MODIFY_MODEL_GAME_CONFIG');

$keys  = $redis->keys('lord_model_gameplay_1_0_201601');
if ( ! $keys ) $keys  = array();
$i = 0;
foreach ( $keys as $k => $key )
{
	$redis->del($key);
	$i++;
}
echo $i;echo PHP_EOL;
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
