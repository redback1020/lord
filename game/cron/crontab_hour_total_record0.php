<?php

// 注意⚠： 本脚本因为运行时间，内存占用等因素，不可以放在sweety中执行，只能直接使用linux-crontab
// 注意⚠： 55 * * * * /usr/local/php/bin/php /alidata1/wwwroot/landlord/sweety/game/cron/crontab_hour_total_record.php

define('TAG_NAME', 'HOUR_TOTAL_RECORD');
require("/data/sweety/conf/cron.php");
$redis = getRedis();
$mysql = getMysql();
function gerr($log){
	return serr($log);
}

//每小时统计一次各种记录

require("/alidata1/wwwroot/landlord/sweety/game/class.record.php");
$record = record::inst($redis, $mysql);

$channels = array();
$sql = "select `id`, `channel` from `lord_game_channel` where `is_del` = 0";
$row = $mysql->getData($sql);
if ( ! $row ) { $row = array(); }
foreach ( $row as $val ) {
	$channels[$val['id']] = $val['channel'];
}



$dds = array(20160717);
foreach ( $dds as $k => $dd )
{
	$record->moneyAnalyseType($dd);
}

// //旧的数据写入
// $day0s = strtotime("2016-05-30");
// $day0e = strtotime(date("Y-m-d", time()-86400));
// for ( $i = $day0s; $i <= $day0e; $i+=86400 ) {
// 	$dd = intval(date("Ymd", $i));
// 	$record->moneyAnalyseType($dd);
// }
// //旧的数据写入
// $day0s = strtotime("2016-05-30");
// $day0e = strtotime(date("Y-m-d", time()));
// for ( $i = $day0s; $i <= $day0e; $i+=86400 ) {
// 	$dd = intval(date("Ymd", $i));
// 	$record->tableAnalyse($dd);
// }


end:{}
