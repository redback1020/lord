<?php

//每小时统计一次(昨天的)频道运营数据
$channels = array();
$sql = "select `channel` from `lord_game_user` where `channel` != '' group by `channel`";
$row = $this->mysql->getData($sql);
if ( !$row ) { $row = array(); }
foreach ( $row as $val ) {
	$channels[]= $val['channel'];
}

$is_batch = 0;

//自动运算开始
// $is_batch = 1;
// $i = 0;
// while ( $i <= 250) {
// 	$i++;
//自动运算开始

$sql = "SELECT max(`dateid`) FROM `lord_total_channel`";
$dateid = intval($this->mysql->getVar($sql));
$dateid = max(20140605, $dateid);
//$date = substr_replace(substr_replace(strval($dateid), '-', 4, 0), '-', -2, 0);
if ( $dateid < intval(date("Ymd")) ) 
{
	$is_update = 0;
	//$dt_day = date("Y-m-d", strtotime($date) + 86400);//发现日期轮换，运算新一天的
	$dt_day = date("Y-m-d", strtotime("$dateid +1 day"));//发现日期轮换，运算新一天的
}
elseif ( 1 == intval(date("H")) && $dateid == intval(date("Ymd")) ) 
{
	$is_update = 1;
	//$dt_day = date("Y-m-d", strtotime($date) - 86400);//今天凌晨1点，再次运算昨天的
	$dt_day = date("Y-m-d", strtotime("$dateid -1 day"));//今天凌晨1点，再次运算昨天的
}
else
{
	$is_update = 1;
	$dt_day = date("Y-m-d");//今天其他时候，再次运算今天的
	if ( $is_batch ) {
		goto end;//自动运算的时候，算到今天就停止
	}
}
$ut_day0= strtotime($dt_day);
$start = $ut_day0;
$start2 = $end = $ut_day0 + 86400;
$start3 = $end2 = $ut_day0 + 86400 * 2;
$end3 = $ut_day0 + 86400 * 3;
$start7 = $ut_day0 + 86400 * 6;
$end7 = $ut_day0 + 86400 * 7;
$dateid = intval(str_replace('-', '', $dt_day));
/*
$dateid = intval(str_replace('-', '', $dt_day));
$start = date("Y-m-d 00:00:00", $ut_day0);
$end  = date("Y-m-d 00:00:00", $ut_day0 + 86400);
$start2 = $end;
$end2 = date("Y-m-d 00:00:00", $ut_day0 + 86400 * 2);
$start3 = $end2;
$end3 = date("Y-m-d 00:00:00", $ut_day0 + 86400 * 3);
$start7 = date("Y-m-d 00:00:00", $ut_day0 + 86400 * 6);
$end7 = date("Y-m-d 00:00:00", $ut_day0 + 86400 * 7);
*/
//当日注册用户
$DNU = array();
$sql = "select count(distinct m.uid) as cn, m.channel from lord_game_analyse a left join lord_game_user m on a.uid = m.uid where unix_timestamp(a.add_time) >= {$start} and unix_timestamp(a.add_time) < {$end} group by m.channel order by cn desc ";
$row = $this->mysql->getData($sql);
foreach ( $row as $val ) {
	$DNU[$val['channel']] = $val['cn'];
}

//次日留存用户
$DR1 = array();
$sql = "select count(distinct m.uid) as cn, m.channel from lord_game_login m left join lord_game_analyse a on m.uid = a.uid where unix_timestamp(m.login_time) >= {$start2} and unix_timestamp(m.login_time) < {$end2} and a.add_time >= '".$start."' and a.add_time < '".$end."' group by m.channel";
$row = $this->mysql->getData($sql);
foreach ( $row as $val ) {
	$DR1[$val['channel']] = $val['cn'];
}

//三日留存用户
$DR2 = array();
$sql = "select count(distinct m.uid) as cn, m.channel from lord_game_login m left join lord_game_analyse a on m.uid = a.uid where unix_timestamp(m.login_time) >= {$start3} and unix_timestamp(m.login_time) < {$end3} and unix_timestamp(a.add_time) >= {$start} and unix_timestamp(a.add_time) < {$end} group by m.channel";
$row = $this->mysql->getData($sql);
foreach ( $row as $val ) {
	$DR2[$val['channel']] = $val['cn'];
}

//七日留存用户
$DR6 = array();
$sql = "select count(distinct m.uid) as cn, m.channel from lord_game_login m left join lord_game_analyse a on m.uid = a.uid where unix_timestamp(m.login_time) >= {$start7} and unix_timestamp(m.login_time) < {$end7} and unix_timestamp(a.add_time) >= {$start} and unix_timestamp(a.add_time) < {$end} group by m.channel";
$row = $this->mysql->getData($sql);
foreach ( $row as $val ) {
	$DR6[$val['channel']] = $val['cn'];
}

if ( $is_update ) 
{
	//更新昨日的次日留存
	$_ut_day0= $ut_day0 - 86400 * 1;
	$_dateid = intval(date("Ymd", $_ut_day0));
	$_start = date("Y-m-d 00:00:00", $_ut_day0);
	$_end = date("Y-m-d 00:00:00", $_ut_day0 + 86400 * 1);
	$_start2 = date("Y-m-d 00:00:00", $_ut_day0 + 86400 * 1);
	$_end2 = date("Y-m-d 00:00:00", $_ut_day0 + 86400 * 2);
	$sql = "select count(distinct m.uid) as cn, m.channel from lord_game_login m left join lord_game_analyse a on m.uid = a.uid where m.login_time >= '".$_start2."' and m.login_time < '".$_end2."' and a.add_time >= '".$_start."' and a.add_time < '".$_end."' group by m.channel";
	$row = $this->mysql->getData($sql);
	foreach ( $row as $val )
	{
		$sql = "UPDATE `lord_total_channel` SET `DR1` = ".$val['cn']." WHERE `dateid` = $_dateid AND `channel` = '".$val['channel']."'";
		$res = $this->mysql->runSql($sql);
	}
	//更新前日的三日留存
	$_ut_day0= $ut_day0 - 86400 * 2;
	$_dateid = intval(date("Ymd", $_ut_day0));
	$_start = date("Y-m-d 00:00:00", $_ut_day0);
	$_end = date("Y-m-d 00:00:00", $_ut_day0 + 86400 * 1);
	$_start2 = date("Y-m-d 00:00:00", $_ut_day0 + 86400 * 2);
	$_end2 = date("Y-m-d 00:00:00", $_ut_day0 + 86400 * 3);
	$sql = "select count(distinct m.uid) as cn, m.channel from lord_game_login m left join lord_game_analyse a on m.uid = a.uid where m.login_time >= '".$_start2."' and m.login_time < '".$_end2."' and a.add_time >= '".$_start."' and a.add_time < '".$_end."' group by m.channel";
	$row = $this->mysql->getData($sql);
	foreach ( $row as $val )
	{
		$sql = "UPDATE `lord_total_channel` SET `DR2` = ".$val['cn']." WHERE `dateid` = $_dateid AND `channel` = '".$val['channel']."'";
		$res = $this->mysql->runSql($sql);
	}
	//更新大大大大前日的七日留存
	$_ut_day0= $ut_day0 - 86400 * 6;
	$_dateid = intval(date("Ymd", $_ut_day0));
	$_start = date("Y-m-d 00:00:00", $_ut_day0);
	$_end = date("Y-m-d 00:00:00", $_ut_day0 + 86400 * 1);
	$_start2 = date("Y-m-d 00:00:00", $_ut_day0 + 86400 * 6);
	$_end2 = date("Y-m-d 00:00:00", $_ut_day0 + 86400 * 7);
	$sql = "select count(distinct m.uid) as cn, m.channel from lord_game_login m left join lord_game_analyse a on m.uid = a.uid where m.login_time >= '".$_start2."' and m.login_time < '".$_end2."' and a.add_time >= '".$_start."' and a.add_time < '".$_end."' group by m.channel";
	$row = $this->mysql->getData($sql);
	foreach ( $row as $val )
	{
		$sql = "UPDATE `lord_total_channel` SET `DR6` = ".$val['cn']." WHERE `dateid` = $_dateid AND `channel` = '".$val['channel']."'";
		$res = $this->mysql->runSql($sql);
	}
}

//当日活跃用户
$DAU = array();
$sql = "select count(distinct m.uid) as cn, m.channel from lord_game_login m left join lord_game_analyse a on m.uid = a.uid where m.login_time >= '".$start."' and m.login_time < '".$end."' group by m.channel";
$row = $this->mysql->getData($sql);
foreach ( $row as $val ) {
	$DAU[$val['channel']] = $val['cn'];
}

//当日转化用户
$DTU = array();
$sql = "select count(distinct m.uid) as cn, m.channel from lord_game_login m left join lord_game_analyse a on m.uid = a.uid where m.login_time >= '".$start."' and m.login_time < '".$end."' and a.add_time>='".$start."' and a.add_time < '".$end."' group by m.channel";
$row = $this->mysql->getData($sql);
foreach ( $row as $val ) {
	$DTU[$val['channel']] = $val['cn'];
}

//当日注册付费用户
$DNPU = array();
$sql = "select count(distinct m.uid) as cn, m.channel from lord_game_charge c, lord_game_user m, lord_game_analyse a where c.uid = m.uid and m.uid = a.uid and a.add_time >= '".$start."' and a.add_time < '".$end."' and c.time >= '".$start."' and c.time < '".$end."' group by m.channel";
$row = $this->mysql->getData($sql);
foreach ( $row as $val ) {
	$DNPU[$val['channel']] = $val['cn'];
}

//当日注册付费转化率
$DNPR = array();
foreach ( $DNPU as $chn => $num )
{
	$DNPR[$chn] = round($num * 100 / $DNU[$chn], 2);
}

//当日所有付费用户
$DPU = array();
$sql = "select count(distinct m.uid) as cn, m.channel from lord_game_charge c, lord_game_user m where c.uid = m.uid and c.time >= '".$start."' and c.time < '".$end."' group by m.channel order by cn desc";
$row = $this->mysql->getData($sql);
foreach ( $row as $val ) {
	$DPU[$val['channel']] = $val['cn'];
}

//当日付费总额
$DPA = array();
$sql = "select sum(c.gold) as cn, m.channel from lord_game_charge c, lord_game_user m where c.uid = m.uid and c.time >= '".$start."' and c.time < '".$end."' group by m.channel";
$row = $this->mysql->getData($sql);
foreach ( $row as $val ) {
	$DPA[$val['channel']] = $val['cn'];
}

//平均每付费用户收入
$ARPPU = array();
foreach ( $DPA as $chn => $num )
{
	$ARPPU[$chn] = round($num / $DPU[$chn], 2);
}

$sql = array();
foreach ( $channels as $channel )
{
	$_dnu = isset($DNU[$channel]) ? $DNU[$channel] : 0;
	$_dr1 = isset($DR1[$channel]) ? $DR1[$channel] : 0;
	$_dr2 = isset($DR2[$channel]) ? $DR2[$channel] : 0;
	$_dr6 = isset($DR6[$channel]) ? $DR6[$channel] : 0;
	$_dau = isset($DAU[$channel]) ? $DAU[$channel] : 0;
	$_dtu = isset($DTU[$channel]) ? $DTU[$channel] : 0;
	$_dnpu = isset($DNPU[$channel]) ? $DNPU[$channel] : 0;
	$_dnpr = isset($DNPR[$channel]) ? $DNPR[$channel] : 0;
	$_dpu = isset($DPU[$channel]) ? $DPU[$channel] : 0;
	$_dpa = isset($DPA[$channel]) ? $DPA[$channel] : 0;
	$_arppu = isset($ARPPU[$channel]) ? $ARPPU[$channel] : 0;
	if ( $is_update ) {
		$upd = "UPDATE `lord_total_channel` SET `DNU` = $_dnu, `DR1` = $_dr1, `DR2` = $_dr2, `DR6` = $_dr6, `DAU` = $_dau, `DTU` = $_dtu, `DNPU` = $_dnpu, `DNPR` = $_dnpr, `DPU` = $_dpu, `DPA` = $_dpa, `ARPPU` = $_arppu WHERE `dateid` = $dateid AND `channel` = '$channel'";
		$res = $this->mysql->runSql($upd);
	}
	else{
		$sql[]= "( $dateid, '$channel', 1, $_dnu, $_dr1, $_dr2, $_dr6, $_dau, $_dtu, $_dnpu, $_dnpr, $_dpu, $_dpa, $_arppu )";
	}
}
if ( $sql ) 
{
	$sql = "INSERT INTO `lord_total_channel` ( `dateid`, `channel`, `is_tv`, `DNU`, `DR1`, `DR2`, `DR6`, `DAU`, `DTU`, `DNPU`, `DNPR`, `DPU`, `DPA`, `ARPPU` ) VALUES ".join(', ', $sql);
	$res = $this->mysql->runSql($sql);
}

//自动运算结束
// }
//自动运算结束

//
// goto end;

end:{}