<?php

exit;
//cron head
require __DIR__."/cron.ini.php";
$redis = new redisCls;
$mysql = new db;
define('CRONTAG', 'CLEAR_LORD_USER_ANALYSE');

//清理lord_game_analyse表中的历史上的错误重复数据
$sql = "SELECT id, uid, count(*) b FROM `lord_game_analyse`  group by uid order by b desc limit 110";
$uidnum = $mysql->getData($sql);
$uidnum = $uidnum ? $uidnum : array();
$id = $uid = array();
foreach ( $uidnum as $k => $v )
{
	if ($v['b'] > 1) {
		$id[]=$v['id'];
		$uid[]=$v['uid'];
	}
}
if ( $id ) {
	$id = "( ".join(',',$id)." )";
	$sql = "DELETE FROM lord_game_analyse WHERE `id` IN $id";
	$res = $mysql->runSql($sql);
}







