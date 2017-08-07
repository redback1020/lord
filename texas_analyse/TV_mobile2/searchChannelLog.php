<?php
require_once '../include/database.class.php';
require_once '../manage/checkPriv.php';
 
  $pageIndex = 50*$_REQUEST['pageIndex'];
  $pageSize = $_REQUEST['pageSize']; 
  $start = $_REQUEST['start'];
  $end = $_REQUEST['end'];
  $status = $_REQUEST['status'];
  
  $wheretime = "";
  $wherestatus = "";
  $channel=$adm_username;
  if($channel!="all"){$wheretime .= " and channel = '".$channel."' ";}
  if($start != '')
	$wheretime .= " and add_time>='".$start."'";
  if($end != '')
	$wheretime .= " and add_time<='".$end."'";
  //if($status != 'all')
	$wherestatus .= " and sts='1'";
 
  $sql = "select * from(SELECT money,add_time,channel FROM `mobile_charge_log` where money>0  ".$wheretime." union all (select  in_money as money,last_time as add_time,channel from mobile_charge where 1 ".$wherestatus.$wheretime."))a order by add_time desc limit ".$pageIndex.",".$pageSize;
 
   $row = $db -> query($sql)-> fetchAll();
  $array['data'] = $row;
  
  $sql = "select count(*) as cn from(SELECT money,add_time,channel FROM `mobile_charge_log` where money>0  ".$wheretime." union all (select  in_money as money,last_time as add_time,channel from mobile_charge where 1 ".$wherestatus.$wheretime."))a ";
  $res = $db -> query($sql) -> fetch();
  $array['cn'] = $res['cn'];
	echo json_encode($array);
	 
		 
?>
 
