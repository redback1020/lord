<?php

//script head
define('CRONTAG', 'FIX_MONEY_ANALYSE_TYPE');
require("/alidata1/wwwroot/landlord/sweety/conf/cron.php");
$redis = getRedis();
$mysql = getMysql();
function gerr($log){
	return serr($log);
}


require("/alidata1/wwwroot/landlord/sweety/game/class.record.php");
$record = record::inst($redis, $mysql);
$dates = array(20160707,20160708,20160709,20160710);

foreach ( $dates as $dateid )
{
	$record->moneyAnalyseType($dateid);
}


exit;
