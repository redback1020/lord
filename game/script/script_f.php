<?php

//cron head
require __DIR__."/cron.ini.php";
$redis = new redisCls;
$mysql = new db;
define('CRONTAG', 'GET_SOME_TOP_LIST_DATA');

$dateid_start = 20150413;
$dateid_end = 20150419;

$sql = "SELECT * FROM `lord_top_list` WHERE `dateid` >= $dateid_start and `dateid` <= $dateid_end and `name` = 'point'";
$list = $mysql->getData($sql);
$list = $list ? $list : array();
foreach ( $list as $k => $v )
{
	$filename = $v['model'] . '_' . $v['period'] . '_' . $v['name'] . '_' . $v['dateid'];
	$toplist = json_decode($v['list'], 1);
	$filedata = "排名,UID,编号,昵称,数值"."\n";
	$uids = array();
	foreach ( $toplist as $kk => $vv )
	{
		$uids[]= $vv['uid'];
	}
	$sql = "SELECT `uid`, `cool_num`, `nick` FROM `lord_game_user` WHERE `uid` IN (".join(', ', $uids).")";
	$list2 = $mysql->getData($sql);
	$list2 = $list2 ? $list2 : array();
	$uidinfo = array();
	foreach ( $list2 as $kk => $vv )
	{
		$uidinfo[$vv['uid']] = $vv;
	}
	foreach ( $toplist as $kk => $vv )
	{
		$filedata.= $vv['rank'].",".$vv['uid'].",".$uidinfo[$vv['uid']]['cool_num'].",".$uidinfo[$vv['uid']]['nick'].",".$vv['val']."\n";
	}
	$res = file_put_contents(__DIR__."/".$filename.".csv", $filedata);
}






