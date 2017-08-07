<?php
require_once '../manage/checkPriv.php';
require_once '../include/database.class.php';
  $pageIndex = 50*$_REQUEST['pageIndex'];
  $pageSize = $_REQUEST['pageSize'];
  $channel = $_REQUEST['channel'];
 if(strtotime($_SESSION['time'])>strtotime($_REQUEST['start'])){
$start = $_SESSION['time'];
}else{
  $start = $_REQUEST['start'];
}  
  $end = $_REQUEST['end'];
  $status = $_REQUEST['status'];
  $status = $_REQUEST['status'];
  $data = $_REQUEST['data'];
  $card_type = $_REQUEST['card_type'];
  $min = $_REQUEST['min']==""?1:$_REQUEST['min'];
$max = $_REQUEST['max']==""?9999999:$_REQUEST['max'];
   
  $where = "";
  if($channel != 'all') 
	$where .= " and a.channel='".$channel."'";
	
	if($data_priv!='all'){substr_count($data_priv,",")>0?$where .= " and a.channel in (".$data_priv.")":$where .= " and a.channel = '".$data_priv."'";}
	
  if($start != '')
	$where .= " and time>='".$start."'";
  if($end != '')
	$where .= " and time<='".$end."'";
	//if($card_type != 'all')
	//$where .= " and card_type='".$card_type."'";
	
	if($data != '')
	$where .= " and (m.uid = '".$data."' or m.nick='".$data."' or m.cool_num='".$data."' )";
	 
	
 
	$where .= " and a.gold>='".$min."' and a.gold<='".$max."'" ;
	
$sql = "SELECT m.nick, m.cool_num, a . * 
FROM lord_game_user m, lord_game_charge a
WHERE m.uid = a.uid ".$where."	order by time desc limit ".$pageIndex.",".$pageSize;
// echo $sql;
  $row = $db -> query($sql)-> fetchAll();
  $array['data'] = $row;
  
  $sql = "SELECT count(*) as cn 
FROM lord_game_user m, lord_game_charge a
WHERE m.uid = a.uid ".$where;
  $res = $db -> query($sql) -> fetch();
  $array['cn'] = $res['cn'];
  
  $sql = "SELECT sum(a.gold) as cn 
FROM lord_game_user m, lord_game_charge a
WHERE m.uid = a.uid ".$where." ";
  $res = $db -> query($sql) -> fetch(); 
  $array['num'] = $res['cn'];
  
  
    	
   
  
	echo json_encode($array);
	 
		 
?>
 

