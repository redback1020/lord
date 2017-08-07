<?php
require_once '../include/database.class.php';

  $pageIndex = 50*$_REQUEST['pageIndex'];
  $pageSize = $_REQUEST['pageSize'];

  $where = "";
 
	
$data = trim($_REQUEST['data']); 
$start = $_REQUEST['start']==""?"1970-01-01":$_REQUEST['start'];
$end = $_REQUEST['end']==""?"2999-01-01":$_REQUEST['end']." 23:59:59"; 
if($data!=''){$where .= " and ( u.nick='".$data."' or u.cool_num='".$data."' or u.uid='".$data."')";}
	
if($where == ""){
	$where = " and u.is_robot=0";
}
if($_REQUEST['type']!='all'){$where .= " and u.is_tv = '".$_REQUEST['type']."'";}
if($_REQUEST['channel']!='all'&&$_REQUEST['channel']!=''){$where .= " and u.channel = '".$_REQUEST['channel']."'";}
 
$where .= " and m.add_time>='".$start."' and m.add_time<='".$end."'"; 
$sql = "select u.*,m.* from mobile_user u,mobile_broadcast_log m where  u.uid = m.uid ".$where." order by m.add_time desc limit ".$pageIndex.",".$pageSize;
// echo $sql;
 
$row = $db -> query($sql)-> fetchAll();
$arrays['data'] = $row;

$sql = "select count(*) from mobile_user u,mobile_broadcast_log m where  u.uid = m.uid ".$where;
$res = $db -> query($sql) -> fetch();  
$arrays['cn'] = $res['cn'];

 
echo json_encode($arrays);
?>
  