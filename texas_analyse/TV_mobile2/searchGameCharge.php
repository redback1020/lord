<?php
require_once '../include/database.class.php';
  $pageIndex = 50*$_REQUEST['pageIndex'];
  $pageSize = $_REQUEST['pageSize'];
  $channel = $_REQUEST['channel'];
  $start = $_REQUEST['start'];
  $end = $_REQUEST['end'];
  $status = $_REQUEST['status'];
   
  $where = "";
  if($channel != 'all') 
	$where .= " and channel='".$channel."'";
  if($start != '')
	$where .= " and add_time>='".$start."'";
  if($end != '')
	$where .= " and add_time<='".$end."'";
  if($status != 'all')
	$where .= " and sts='".$status."'";
  $sql = "select * from mobile_charge where 1 ".$where."	order by add_time desc limit ".$pageIndex.",".$pageSize;
   
   $row = $db -> query($sql)-> fetchAll();
  $array['data'] = $row;
  
  $sql="select count(*) as cn from mobile_charge where 1 ".$where;
  $res = $db -> query($sql) -> fetch();
  $array['cn'] = $res['cn'];
	echo json_encode($array);
	 
		 
?>
 
