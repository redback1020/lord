<?php
require_once '../include/database.class.php';
  $pageIndex = 50*$_REQUEST['pageIndex'];
  $pageSize = $_REQUEST['pageSize'];
  $channel = $_REQUEST['channel'];
  $start = $_REQUEST['start'];
  $end = $_REQUEST['end'];
  $pdo = new DB();
  $db = $pdo->getDB();
  $where = "";
  if($channel != 'all') 
	$where .= " and channel='".$channel."'";
  if($start != '')
	$where .= " and add_time>='".$start."'";
  if($end != '')
	$where .= " and add_time<='".$end."'";
  $sql = "select * from mobile_charge_log where (misc='taobao' or misc='web') ".$where."	order by add_time desc limit ".$pageIndex.",".$pageSize;
   $row = $db -> query($sql)-> fetchAll();
  $array['data'] = $row;
  
  $sql="select count(*) as cn from mobile_charge_log where (misc='taobao' or misc='web') ".$where;
  $res = $db -> query($sql) -> fetch();
  $array['cn'] = $res['cn'];
	echo json_encode($array);
	 
		 
?>
 
