<?php

//script head
define('CRONTAG', 'FIX_MONEY_ANALYSE_TYPE');
require("/alidata1/wwwroot/landlord/sweety/conf/cron.php");
$redis = getRedis();
$mysql = getMysql();
function gerr($log){
	return serr($log);
}

$date = '2016-06-18';
$datetime = strtotime($date);
$today = date("Y-m-d");
$todaytime = strtotime($today);
$data = '';
$data.= "dateid,uid,count,sum\n";
for ( $i=$datetime; $i<=$todaytime; $i+=86400 )
{
	$dd = date("Ymd",$i);
	$sql = "select $dd as dd, uid, count(*) as count, sum(moneynum) as sum from lord_record_money_$dd where typeid = 25 group by uid having count > 1";
	$ret = $mysql->getData($sql);
	if ( ! $ret || ! is_array($ret) ) $ret = array();
	foreach ( $ret as $k => $v )
	{
		$data.= "{$v['dd']},{$v['uid']},{$v['count']},{$v['sum']}\n";
	}
}
file_put_contents('user_error_xingyunpaiju_history.csv', $data);


exit;
