<?php
require_once '../include/database.class.php';
require_once './global.php';
  $pageIndex = 50*$_REQUEST['pageIndex'];
  $pageSize = $_REQUEST['pageSize'];
  $orderby = $_REQUEST['orderby']==""?'a.add_time':$_REQUEST['orderby']; 
  $by = $_REQUEST['by']==""?'desc':$_REQUEST['by'];
$where = "";
/*if($_REQUEST['type']=='coolNum'){
	$where = " and u.cool_num = '".$_REQUEST['data']."'";
}else if($_REQUEST['type']=='uid')
	$where = " and u.uid = '".$_REQUEST['data']."'";
else if($_REQUEST['type'] == 'nickName')
	$where = " and u.nick = '".$_REQUEST['data']."'";*/
	
$data = trim($_REQUEST['data']);
$min = $_REQUEST['min']==""?0:$_REQUEST['min'];
$max = $_REQUEST['max']==""?9999999:$_REQUEST['max'];
$start = $_REQUEST['start']==""?"1970-01-01":$_REQUEST['start'];
$end = $_REQUEST['end']==""?"2999-01-01":$_REQUEST['end']." 23:59:59";
$last_start = $_REQUEST['last_start']==""?"1970-01-01":$_REQUEST['last_start'];
$last_end = $_REQUEST['last_end']==""?"2999-01-01":$_REQUEST['last_end']." 23:59:59";
if($data!=''){$where .= " and (m.uuid = '".$data."' or u.nick='".$data."' or u.cool_num='".$data."' or u.uid='".$data."')";}
	
if($where == ""){
	$where = " and u.is_robot=0";
}
if($_REQUEST['type']!='all'){$where .= " and u.is_tv = '".$_REQUEST['type']."'";}
if($_REQUEST['channel']!='all'&&$_REQUEST['channel']!=''){$where .= " and u.channel = '".$_REQUEST['channel']."'";}

 if($adm_username!='all'){$where .= " and u.channel in (".$adm_username.")";}

$where .= " and a.charge_money>='".$min."' and a.charge_money<='".$max."'";
$where .= " and a.add_time>='".$start."' and a.add_time<='".$end."'";
$where .= " and a.last_time>='".$last_start."' and a.last_time<='".$last_end."'";
$sql = "select m.uuid,u.*,a.play,a.win,a.charge_money,a.last_time,a.add_time,a.coins_cost,a.coins_got,(a.coins_got-a.coins_cost) as dif	 from mobile_user u,mobile_analyse a,user_user m where a.uid = u.uid and u.uid = m.id ".$where." order by ".$orderby." ".$by." limit ".$pageIndex.",".$pageSize;
 //echo $sql;
$pdo = new DB();
$db = $pdo->getDB();
$row = $db -> query($sql)-> fetchAll();
$arrays['data'] = $row;

$sql = "select count(*) as cn from mobile_analyse a,mobile_user u,user_user m where a.uid = u.uid and u.uid = m.id ".$where;
$res = $db -> query($sql) -> fetch();  
$arrays['cn'] = $res['cn'];

 
echo json_encode($arrays);
?>
  