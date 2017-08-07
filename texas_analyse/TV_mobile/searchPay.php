<?php
require_once '../include/database.class.php';
require_once './global.php';
  $pageIndex = 50*$_REQUEST['pageIndex'];
  $pageSize = $_REQUEST['pageSize'];
  $channel = $_REQUEST['channel'];
  $start = $_REQUEST['start'];
  $end = $_REQUEST['end'];
  $status = $_REQUEST['status'];
  $status = $_REQUEST['status'];
  $data = $_REQUEST['data'];
  $card_type = $_REQUEST['card_type'];
  $min = $_REQUEST['min']==""?1:$_REQUEST['min'];
$max = $_REQUEST['max']==""?9999999:$_REQUEST['max'];
  $pdo = new DB();
  $db = $pdo->getDB();
  $where = "";
  if($channel != 'all') {
	//$where = " and channels='".$channel."'";
	substr_count($channel,",")>0?$where = " and channels in (".$channel.")":$where = " and channels = '".$channel."'";
  }
  if($adm_username!='all'){$where .= " and channels in (".$adm_username.")";}
  if($start != '')
	$where .= " and add_time>='".$start."'";
  if($end != '')
	$where .= " and add_time<='".$end."'";
	if($card_type != 'all')
	$where .= " and card_type='".$card_type."'";
	
	if($data != '')
	$where .= " and (m.uid = '".$data."' or m.nick='".$data."' or m.cool_num='".$data."' )";
	 
	
  if($status != 'all'){
	$where .= " and sts='".$status."'";
  }
	$where .= " and money>='".$min."' and money<='".$max."'" ;
	
$sql = "SELECT m.nick, m.cool_num, a . * 
FROM mobile_user m, 
(SELECT type , card_type, uid, value, money, channel as channels, add_time, last_time, sts FROM mobile_charge)a
WHERE m.uid = a.uid ".$where."	order by add_time desc limit ".$pageIndex.",".$pageSize;
  //echo $sql;
  $row = $db -> query($sql)-> fetchAll();
  $array['data'] = $row;
  
  $sql = "SELECT count(*) as cn 
FROM mobile_user m, 
(SELECT type , card_type, uid, value, money, channel  as channels, add_time, last_time, sts FROM mobile_charge)a
WHERE m.uid = a.uid ".$where;
  $res = $db -> query($sql) -> fetch();
  $array['cn'] = $res['cn'];
  
  $sql = "SELECT sum(a.money) as cn 
FROM mobile_user m, 
(SELECT type , card_type, uid, value, money, channel  as channels, add_time, last_time, sts FROM mobile_charge)a
WHERE m.uid = a.uid ".$where." ";
  $res = $db -> query($sql) -> fetch();
  $array['num'] = $res['cn'];
  
  
    
					 
					
  $sql = "SELECT sum(a.money) as cn 
FROM mobile_user m, 
(SELECT type , card_type, uid, value, money, channel  as channels, add_time, last_time, sts FROM mobile_charge)a
WHERE m.uid = a.uid ".$where." and (a.card_type='JUNNET' or a.card_type='ZHENGTU' or a.card_type='QQCARD' or a.card_type='TIANXIA' or a.card_type='SNDACARD' )";
  $res = $db -> query($sql) -> fetch();
  $a1 = $res['cn']*0.85;
  $sql = "SELECT sum(a.money) as cn 
FROM mobile_user m, 
(SELECT type , card_type, uid, value, money, channel  as channels, add_time, last_time, sts FROM mobile_charge)a
WHERE m.uid = a.uid ".$where." and (a.card_type='SZX' or a.card_type='UNICOM' or a.card_type='TELECOM')";
  $res = $db -> query($sql) -> fetch();
  $a2 = $res['cn']*0.95;
  $sql = "SELECT sum(a.money) as cn 
FROM mobile_user m, 
(SELECT type , card_type, uid, value, money, channel  as channels, add_time, last_time, sts FROM mobile_charge)a
WHERE m.uid = a.uid ".$where." and (a.card_type='ALIPAY' or a.card_type='YEEPAY' )";
  $res = $db -> query($sql) -> fetch();
  $a3 = $res['cn']*0.996;
   $sql = "SELECT sum(a.money) as cn 
FROM mobile_user m, 
(SELECT type , card_type, uid, value, money, channel  as channels, add_time, last_time, sts FROM mobile_charge)a
WHERE m.uid = a.uid ".$where." and (a.card_type='XIAOMI' )";
  $res = $db -> query($sql) -> fetch();
  $a4 = $res['cn']*0.6;
  
    $sql = "SELECT sum(a.money) as cn 
FROM mobile_user m, 
(SELECT type , card_type, uid, value, money, channel  as channels, add_time, last_time, sts FROM mobile_charge)a
WHERE m.uid = a.uid ".$where." and (a.card_type='taobao' )";
  $res = $db -> query($sql) -> fetch();
  $a5 = $res['cn'];
  
  $array['pay_fee'] = $a1+$a2+$a3+$a4+$a5;
  
	echo json_encode($array);
	 
		 
?>
 
