<?php
require_once '../include/database.class.php'; 
$pageIndex = 50*$_REQUEST['pageIndex'];
$pageSize = $_REQUEST['pageSize'];
$where = "";
$data = trim($_REQUEST['data']);
if($_REQUEST['type']!='all'){$where .= " and m.is_tv = '".$_REQUEST['type']."'";}
if($data!=''){$where .= " and (u.uuid = '".$data."' or m.nick='".$data."' or m.cool_num='".$data."' or m.uid = '".$data."')";}
 
 
$pdo = new DB();
$db = $pdo->getDB();
$sql = "select * from user_user u, mobile_user m  where m.vip_lv>0 and m.vip_exp!='0000-00-00 00:00:00' and u.id = m.uid ".$where."	order by vip_exp desc limit ".$pageIndex.",".$pageSize;// echo $sql;
$res = $db -> query($sql)-> fetchAll();
foreach($res as $val){
	$array[] = $val['uid'];
}
$str = implode("','",$array);
$sql = "select * from mobile_gold_log where type='GIVE' and misc = 'vip' and uid in('".$str."')";
$result = $db -> query($sql)-> fetchAll();
foreach($result as $val){
	$start[$val['uid']] = $val['add_time'];
	$end[$val['uid']] = $val['channel'];
}
foreach($res as $key =>$val){
	if(isset($start[$val['uid']])){
	 
		$start = $start[$val['uid']];
		$end = $end[$val['uid']];
		if(strtotime($end) - time()>0)
		$diff = floor((strtotime($end) - strtotime($start))/(3600*24));
		else
		$diff = "已过期";
		
	}else{
	 
		$start = "";
		$end = $val['vip_exp'];
		if(strtotime($end) - time()>0)
		$diff = floor((strtotime($end) - time())/(3600*24));
		else
		$diff = "已过期";
	}
	$arr['start'] = $start;
	$arr['end'] = $end;
	$arr['diff'] = $diff; 
	$arr['uuid'] = $val['uuid']; 
	$arr['cool_num'] = $val['cool_num']; 
	$arr['nick'] = $val['nick']; 
	$arrays[] = $arr;
	 
}
$sql = "select count(*) as cn from user_user u, mobile_user m  where  m.vip_lv>0 and m.vip_exp!='0000-00-00 00:00:00' and u.id = m.uid ".$where;
$res = $db -> query($sql)-> fetch();
 
$da['cn'] = $res['cn'];
$da['data'] = $arrays;
 echo json_encode($da);
 
?>
  