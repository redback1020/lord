<?php

// 注意⚠： 本脚本因为运行时间，内存占用等因素，不可以放在sweety中执行，只能直接使用linux-crontab
// 注意⚠： 50 2 * * * /usr/local/php/bin/php /data/sweety/game/cron/crontab_clean_dead_user.php

define('TAG_NAME', 'REMOVE_DEAD_USER');
require("/data/sweety/conf/cron.php");
$redis = getRedis();
$mysql = getMysql();
function gerr($log){
	return serr($log);
}

//每天清理一次死用户
$date = date("Y-m-d H:i:s");
$dateid = intval(date("Ymd"));
$dead = strtotime("2015-12-13 00:00:00");

//1.创建新dead表
$deadtables = array('lord_game_analyse','lord_game_user','lord_game_charge','lord_game_charge_sys','lord_game_login','user_analyse','user_login','user_user');
$cleantables= array('lord_user_inbox','lord_user_item','lord_user_message','lord_user_task','lord_user_taskrecord','lord_user_tesk','lord_user_teskrecord','lord_user_tesksurprise','lord_user_unbox');
$deadtables = array_flip($deadtables);
foreach ( $deadtables as $k => $v )
{
	$deadtables[$k] = $v = $k."_dead_".$dateid;
	$sql = "show create table $k";
	$data = $mysql->getLine($sql);
	if ( ! $data ) {
		gerr("1.建清理表错误 $sql");
		goto end;
	}
	$ddl = preg_replace('/AUTO_INCREMENT=\d+/i','AUTO_INCREMENT=1',str_replace(array('CREATE TABLE ',$k), array('CREATE TABLE IF NOT EXISTS ',$v), $data['Create Table']));
	$ret = $mysql->runSql($ddl);
	if ( ! $ret ) {
		gerr("1.建清理表错误 $ddl");
		goto end;
	}
}
serr("1.done.");
//2.从analyse表找到死用户
$id = 0;
$idstart = -1;
while ( $idstart != $id )
{
	$sql = "SELECT `id`, `uid`, `last_login` FROM `lord_game_analyse` WHERE `id` > $id ORDER BY `id` ASC LIMIT 0, 500";
	$data = $mysql->getData($sql);
	if ( ! $data ) $data = array();
	$idstart = $id;
	$uids = array();
	foreach ( $data as $k => $v )
	{
		$id = $v['id'];
		if ( strtotime($v['last_login']) > $dead ) continue;
		$uids[]= intval($v['uid']);
	}
	if ( ! $uids ) continue;
	serr("2.done. idstart=$idstart idto=$id deaduids=".json_encode($uids));
	foreach ( $deadtables as $k => $v )
	{
		$tag = 'uid';
		if ( $k == 'user_user' ) $tag = 'id';
		$sql = "INSERT INTO `$v` SELECT * FROM `$k` B WHERE B.`$tag` IN (".join(',', $uids).")";
		$ret = $mysql->runSql($sql);
		if ( ! $ret ) {
			gerr("3.批量转移错误 $sql");
			break;
		}
		$sql = "DELETE FROM `$k` WHERE `$tag` IN (".join(',', $uids).")";
		$ret = $mysql->runSql($sql);
		if ( ! $ret ) {
			gerr("3.批量删除错误 $sql");
			break;
		}
	}
	serr("3.done. idstart=$idstart idto=$id");
	foreach ( $cleantables as $k => $v )
	{
		$sql = "DELETE FROM `$v` WHERE `uid` IN (".join(',', $uids).")";
		$ret = $mysql->runSql($sql);
	}
	serr("4.done. idstart=$idstart idto=$id");
	// $sleep = 5;//监控一段时间后，就可以设置sleep时间为0了；
	// while ( $sleep )
	// {
	// 	serr("5.sleep. $sleep");
	// 	$sleep--;
	// 	sleep(1);
	// }
}


// $sql =




end:{}
