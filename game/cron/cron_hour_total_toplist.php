<?php

//每小时排行榜单入库、清理上周之前的周记录、清理昨日之前的日记录
$dateid = intval(date("Ymd"));
$weekid = intval(date("Ymd", time() - (date("N") - 1) * 86400 ));	//本周id
$yesdid = intval(date("Ymd", time() - 86400));						//昨日id
$yeswid = intval(date("Ymd", time() - (date("N") - 1) * 86400 - 7 * 86400));//上周id

$is_update = 0;//本次是 新增/更新

$sql = "SELECT max(`dateid`) FROM `lord_top_list`";
$maxdid = intval($this->mysql->getVar($sql));
// $maxdid = date("Ymd", strtotime(($a=str_split($maxdid,2)) ? date($a[0].$a[1]."-".$a[2]."-".$a[3]) : "") + 86400) + 0;
$lists = $this->redis->keys("lord_list_*");
$lists = $lists ? $lists : array();
$keydids = array();
foreach ( $lists as $k => $key )
{	//lord_list_normal_day_earn_20150223
	$keys = explode("_", $key);
	if ( !isset($keys[5]) ) {
		// writelog("[error] key=$key");
		continue;
	}
	$keydid = intval($keys[5]);
	if ($keydid > $maxdid) {
		$keydids[]=$keydid;
	}
}
$maxdid = $keydids ? min($keydids) : $maxdid;

if ( $maxdid == $dateid && (date("H")+0) )
{	//每天的零点是新增数据库数据，且处理清理之前的数据
	$is_update = 1;
}

foreach ( $lists as $k => $key )
{
	$keys = explode("_", $key);
	if ( !$keys || !isset($keys[5]) ) {
		// writelog("[error] key=$key");
		continue;
	}
	$dateid = intval($keys[5]);
	$model = $keys[2];
	$period = $keys[3];
	$name = $keys[4];
	if ( !$is_update && $period == 'week' && $dateid < $yeswid ) 
	{	// 0点清理上周之前的周数据
		$this->redis->del($key);
		continue;
	}
	elseif ( !$is_update && $period == 'day' && $dateid < $yesdid ) 
	{	// 0点清理昨天之前的天数据
		$this->redis->del($key);
		continue;
	}
	elseif ( $period == 'week' && $dateid == $yeswid ) 
	{	//上周的周数据
		if ( !$is_update ) 
		{	// 0点写入/更新
			$sql = "SELECT `id` FROM `lord_top_list` WHERE `dateid` = $dateid AND `model` = '$model' AND `period` = '$period' AND `name` = '$name'";
			$id = $this->mysql->getVar($sql);
			if ( $id ) {
				$is_update = 1;
			}
		}
		else 
		{	// 其他时间不管
			continue;
		}
	}
	elseif ( $period == 'week' && $dateid == $weekid ) 
	{	// ?点写入/更新本周的周数据
		$sql = "SELECT `id` FROM `lord_top_list` WHERE `dateid` = $dateid AND `model` = '$model' AND `period` = '$period' AND `name` = '$name'";
		$id = $this->mysql->getVar($sql);
		if ( $id ) {
			$is_update = 1;
		}
	}
	elseif ( $dateid != $maxdid ) 
	{	//只继续处理今天的天数据
		continue;
	}
	$zlist = $this->redis->zlist($key, 50);//游戏中取前20，存入数据库时取前50
	$zlist = $zlist ? $zlist : array();
	$list = array();
	$i=0;
	foreach ( $zlist as $uid => $val )
	{
		$i++;
		$list[]= array('rank'=>$i, 'uid'=>$uid, 'val'=>$val);
	}
	if ( $is_update ) 
	{
		$sql = "UPDATE `lord_top_list` SET `list` = '".addslashes(json_encode($list))."' WHERE `dateid` = $dateid AND `model` = '$model' AND `period` = '$period' AND `name` = '$name'";
	}
	else
	{
		$sql = "INSERT INTO `lord_top_list` ( `dateid`, `model`, `period`, `name`, `list` ) VALUES ( $dateid, '$model', '$period', '$name', '".addslashes(json_encode($list))."' )";
	}
	$res = $this->mysql->runSql($sql);
}

//
// goto end;

end:{}
