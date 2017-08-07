<?php

require_once '../include/database.class.php';
require_once '../manage/checkPriv.php';
require_once '../manage/getipCity.php';

  $pageIndex = 50*$_REQUEST['pageIndex'];
  $pageSize = $_REQUEST['pageSize'];
  $orderby = $_REQUEST['orderby']==""?'a.add_time':$_REQUEST['orderby'];
  $by = $_REQUEST['by']==""?'desc':$_REQUEST['by'];
$where = "";
//var_dump($_SESSION);

$data = trim($_REQUEST['data']);
$min = $_REQUEST['min']==""?0:$_REQUEST['min'];
$max = $_REQUEST['max']==""?9999999:$_REQUEST['max'];
$start = $_REQUEST['start']==""?"1970-01-01":$_REQUEST['start'];
$end = $_REQUEST['end']==""?"2999-01-01":$_REQUEST['end']." 23:59:59";
$last_start = $_REQUEST['last_start']==""?"1970-01-01":$_REQUEST['last_start'];
$last_end = $_REQUEST['last_end']==""?"2999-01-01":$_REQUEST['last_end']." 23:59:59";
if($data!=''){$where .= " and (m.uuid = '".$data."' or u.nick='".$data."' or u.cool_num='".$data."' or u.uid='".$data."')";}


if($_REQUEST['type']!='all'){$where .= " and u.is_tv = '".$_REQUEST['type']."'";}
if($_REQUEST['channel']!='all'&&$_REQUEST['channel']!=''){$where .= " and u.channel = '".$_REQUEST['channel']."'";}
if($data_priv!='all'){substr_count($data_priv,",")>0?$where .= " and u.channel in (".$data_priv.")":$where .= " and u.channel = '".$data_priv."'";}
$where .= " and a.charge_money>='".$min."' and a.charge_money<='".$max."'";
$where .= " and a.add_time>='".$start."' and a.add_time<='".$end."'";
$where .= " and a.last_login>='".$last_start."' and a.last_login<='".$last_end."'";
$sql = "select m.uuid,u.*,a.matches,a.win,a.charge_money,a.last_login,a.last_ip as last_ip,a.add_time,a.coins_cost,a.coins_got,(a.coins_got-a.coins_cost) as dif	 from lord_game_user u,lord_game_analyse a,user_user m where a.uid = u.uid and u.uid = m.id ".$where." order by ".$orderby." ".$by." limit ".$pageIndex.",".$pageSize;
//echo $sql;

//shawn20140707start
$uids = array();
//shawn20140707end

$row = $db -> query($sql)-> fetchAll();
$ipCity = new ipCity();
$arrays['data'] = $row;
foreach ( $row as $k=>$v )
{
	$arrays['data'][$k]['last_ip'] = $v['last_ip'].' <br/>'.$ipCity->getCity($v['last_ip']);
	//shawn20140707start
	$uids[] = $v['uid'];
	//shawn20140707end
}
//shawn20140707start

$userchannel = array();
//echo "select * from (select * from lord_game_login where uid in (".join(',',$uids).") order by id desc) a group by uid ";
$channeldata = $db->query("select * from (select * from lord_game_login where uid in (".join(',',$uids).") order by id desc) a group by uid ")->fetchAll();

foreach ( $channeldata as $k=>$v )
{
	$userchannel[$v['uid']] = $v['channel'];
}
foreach ( $row as $k=>$v )
{
	$arrays['data'][$k]['channel'] = isset($userchannel[$v['uid']]) ? $userchannel[$v['uid']] : '';
}
//shawn20140707end


$sql = "select count(*) as cn from lord_game_analyse a,lord_game_user u,user_user m where a.uid = u.uid and u.uid = m.id ".$where;
$res = $db -> query($sql) -> fetch();
$arrays['cn'] = $res['cn'];


echo json_encode($arrays);
?>
