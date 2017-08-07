<?php

//script head
define('CRONTAG', 'RECORD_RECHARGE_USER_ROOM');
require("/data/sweety/conf/cron.php");
$redis = getRedis();
$mysql = getMysql();
function gerr($log){
	return serr($log);
}

$f1 = '/root/redis_lord_trial_20160721_recharge.csv';
touch($f1);
$h1 = fopen($f1,'wb+');
fwrite($h1, "uid,times,next,costdate,costgold\n");
$trial = $redis->hgetall('lord_trial_20160721');
if ( $trial ) {
	foreach ( $trial as $uid => $v )
	{
		$vv = explode('_', $v);
		if ( $vv[0] > 4 ) {
			$sql = "SELECT * FROM `lord_user_cost` WHERE `uid` = $uid";
			$data = $mysql->getData($sql);
			if ( is_array($data) ) {
				foreach ( $data as $kk => $vvv )
				{
					fwrite($h1, "$uid,".$vv[0].",".date("H:i:s",$vv[1]).",".$vvv['date'].",".$vvv['gold']."\n");
				}
			}
		}
	}
}
fclose($h1);

exit;
