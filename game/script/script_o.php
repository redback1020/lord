<?php

//script head
define('CRONTAG', 'RECORD_RECHARGE_USER_ROOM');
require("/data/sweety/conf/cron.php");
// $redis = getRedis();
$mysql = getMysql();
function gerr($log){
	return serr($log);
}
//
// $date = '2016-07-19';
// $datetime = strtotime($date);
// $dateid = intval(date("Ymd", $datetime));
//
// $sql = "SELECT `uid`, SUM(`gold`) as recharge, count(`gold`) as rechargetimes, `date` as rechargedate FROM `lord_user_cost` WHERE `dateid` = $dateid GROUP BY `uid` ORDER BY `id`";
// $uidgolds = $mysql->getData($sql);
// if ( ! is_array($uidgolds) ) $uidgolds = array();
// if ( ! $uidgolds ) echo $sql."\n";
// $roomIds = array(
// 	'0'=>'大厅',
// 	'1000'=>'经典新','1001'=>'经典初','1002'=>'经典中','1003'=>'经典高',
// 	'1007'=>'赖子新','1008'=>'赖子初','1009'=>'赖子中','1010'=>'赖子高',
// 	'1004'=>'竞技场',
// );
// error_log("dateid,uid,recharge,rechargetimes,rechargedate,room,coins,coupon,tmcr\n",3,'record_recharge_user_room.csv');
// foreach ( $uidgolds as $k => $v )
// {
// 	$uid = intval($v['uid']);
// 	$recharge = abs($v['recharge']);
// 	$rechargetimes = $v['rechargetimes'];
// 	$rechargedate = $v['rechargedate'];
// 	$sql = "SELECT `uid`,`roomId`,`coins`,`coupon`,`tmcr` FROM `lord_record_action_$dateid` WHERE `uid` = $uid";
// 	$ret = $mysql->getData($sql);
// 	if ( ! is_array($ret) ) $ret = array();
// 	if ( ! $ret ) echo $sql."\n";
// 	$tmcrs = array();
// 	foreach ( $ret as $kk => $vv )
// 	{
// 		$tmcrs[$kk] = $vv['tmcr'];
// 	}
// 	array_multisort($tmcrs,SORT_ASC,$ret);
// 	foreach ( $ret as $kk => $vv )
// 	{
// 		error_log("$dateid,$uid,$recharge,$rechargetimes,$rechargedate,".$roomIds[$vv['roomId']].",{$vv['coins']},{$vv['coupon']},".date("H:i:s",$vv['tmcr'])."\n",3,'record_recharge_user_room.csv');
// 	}
// }

$f1 = '/root/record_recharge_user_room.csv';
$f2 = '/root/record_recharge_user_room2.csv';
$h1 = fopen($f1,'rb+');
$h2 = fopen($f2,'wb+');
if ( $h1 ) {
	$tmp = '';
	$i = 0;
	while( !feof($h1) && $i < 3 )
	{
		// $i++;
		$line = fgets($h1);
		$t = explode(',',$line);
		$t8 = $t[0].','.$t[1].','.$t[2].','.$t[3].','.$t[4].','.$t[5].','.$t[6].','.$t[7];
		if ( $tmp !== $t8 ) {
			// fclose($h1);
			// fclose($h2);
			// var_dump($t8,$tmp);//exit;
			$tmp = $t8;
			fwrite($h2, $line);
		}
	}
}
fclose($h1);
fclose($h2);



exit;
