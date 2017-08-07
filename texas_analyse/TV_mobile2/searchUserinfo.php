<?php
require_once '../manage/checkPriv.php';
require_once '../include/database.class.php';
//require_once './global.php';
  $pageIndex = 50*$_REQUEST['pageIndex'];
  $pageSize = $_REQUEST['pageSize'];
  $orderby = $_REQUEST['orderby']==""?'a.add_time':$_REQUEST['orderby']; 
  $by = $_REQUEST['by']==""?'desc':$_REQUEST['by'];
$where = "";

	
$data = trim($_REQUEST['data']);
$min = $_REQUEST['min']==""?0:$_REQUEST['min'];
$max = $_REQUEST['max']==""?9999999:$_REQUEST['max'];
if(strtotime($_SESSION['time'])>strtotime($_REQUEST['start'])){
$start = $_SESSION['time'];
}else{
$start = $_REQUEST['start']==""?"1970-01-01":$_REQUEST['start'];
}  
$end = $_REQUEST['end']==""?"2999-01-01":$_REQUEST['end']." 23:59:59";
$last_start = $_REQUEST['last_start']==""?"1970-01-01":$_REQUEST['last_start'];
$last_end = $_REQUEST['last_end']==""?"2999-01-01":$_REQUEST['last_end']." 23:59:59";
if($data!=''){$where .= " and (m.uuid = '".$data."' or u.nick='".$data."' or u.cool_num='".$data."' or u.uid='".$data."')";}
	
if($where == ""){
	$where = " and u.is_robot=0";
}
if($_REQUEST['type']!='all'){$where .= " and u.is_tv = '".$_REQUEST['type']."'";}
if($_REQUEST['channel']!='all'&&$_REQUEST['channel']!=''){$where .= " and u.channel = '".$_REQUEST['channel']."'";}

 //if($data_priv!='all'&&substr_count($data_priv,",")>0){$where .= " and u.channel in (".$data_priv.")";}
if($data_priv!='all'){substr_count($data_priv,",")>0?$where .= " and u.channel in (".$data_priv.")":$where .= " and u.channel = '".$data_priv."'";}

$where .= " and a.charge_money>='".$min."' and a.charge_money<='".$max."'";
$where .= " and a.add_time>='".$start."' and a.add_time<='".$end."'";
$where .= " and a.last_time>='".$last_start."' and a.last_time<='".$last_end."'";
// $sql = "select m.uuid,u.*,a.play,a.win,a.charge_money,a.last_time,a.add_time,a.coins_cost,a.coins_got,(a.coins_got-a.coins_cost) as dif	 from mobile_user u,mobile_analyse a,user_user m where a.uid = u.uid and u.uid = m.id ".$where." order by ".$orderby." ".$by." limit ".$pageIndex.",".$pageSize;
$sql = "select m.uuid,u.*,a.play,a.win,a.charge_money,a.last_time,a.add_time,a.coins_cost,a.coins_got,(a.coins_got-a.coins_cost) as dif	 from mobile_user u left join mobile_analyse a on u.uid = a.uid left join user_user m on u.uid = m.id where 1=1 ".$where." order by ".$orderby." ".$by." limit ".$pageIndex.",".$pageSize;
 
$row = $db -> query($sql)-> fetchAll();
$arrays['data'] = $row;

// $sql = "select count(*) as cn from mobile_analyse a,mobile_user u,user_user m where a.uid = u.uid and u.uid = m.id ".$where;
$sql = "select count(*) as cn from mobile_user u left join mobile_analyse a on u.uid = a.uid left join user_user m on u.uid = m.id where 1=1 ".$where;
$res = $db -> query($sql) -> fetch();  
$arrays['cn'] = $res['cn'];

 
echo json_encode($arrays);
?>
  
