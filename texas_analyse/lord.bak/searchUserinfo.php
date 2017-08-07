<?php
require_once '../include/database.class.php';

  $pageIndex = 50*$_REQUEST['pageIndex'];
  $pageSize = $_REQUEST['pageSize'];
  $orderby = $_REQUEST['orderby']==""?'a.add_time':$_REQUEST['orderby']; 
  $by = $_REQUEST['by']==""?'desc':$_REQUEST['by'];
$where = "";
 
	
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

$where .= " and a.charge_money>='".$min."' and a.charge_money<='".$max."'";
$where .= " and a.add_time>='".$start."' and a.add_time<='".$end."'";
$where .= " and a.last_login>='".$last_start."' and a.last_login<='".$last_end."'";
$sql = "select m.uuid,u.*,a.matches,a.win,a.charge_money,a.last_login,a.add_time,a.coins_cost,a.coins_got,(a.coins_got-a.coins_cost) as dif	 from lord_game_user u,lord_game_analyse a,user_user m where a.uid = u.uid and u.uid = m.id ".$where." order by ".$orderby." ".$by." limit ".$pageIndex.",".$pageSize;
 //echo $sql;
 
$row = $db -> query($sql)-> fetchAll();
$arrays['data'] = $row;

$sql = "select count(*) as cn from lord_game_analyse a,lord_game_user u,user_user m where a.uid = u.uid and u.uid = m.id ".$where;
$res = $db -> query($sql) -> fetch();  
$arrays['cn'] = $res['cn'];

 
echo json_encode($arrays);
?>
  