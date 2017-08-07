<?php
require_once '../include/database.class.php';

  $pageIndex = 50*$_REQUEST['pageIndex'];
  $pageSize = $_REQUEST['pageSize'];
  $orderby = $_REQUEST['orderby']==""?'a.add_time':$_REQUEST['orderby']; 
  $by = $_REQUEST['by']==""?'desc':$_REQUEST['by'];
$where = "";
if($_REQUEST['type']=='coolNum'){
	$where = " and u.cool_num = '".$_REQUEST['data']."'";
}else if($_REQUEST['type']=='uid')
	$where = " and u.uid = '".$_REQUEST['data']."'";
else if($_REQUEST['type'] == 'nickName')
	$where = " and u.nick = '".$_REQUEST['data']."'";
if($where == ""){
	$where = " and u.is_robot=0";
}
$sql = "select u.*,a.charge_money,a.last_time,a.add_time,a.coins_cost,a.coins_got,(a.coins_got-a.coins_cost) as dif	 from game_user u,game_analyse a where a.uid = u.uid ".$where." order by ".$orderby." ".$by." limit ".$pageIndex.",".$pageSize;
 
$pdo = new DB();
$db = $pdo->getDB();
$row = $db -> query($sql)-> fetchAll();
$arrays['data'] = $row;

$sql = "select count(*) as cn from game_user u where 1".$where;
$res = $db -> query($sql) -> fetch();  
$arrays['cn'] = $res['cn'];

 
echo json_encode($arrays);
?>
  