<?php
require_once '../include/database.class.php';

  $pageIndex = 50*$_REQUEST['pageIndex'];
  $pageSize = $_REQUEST['pageSize']; 
$where = "";
if($_REQUEST['type']=='coolNum'){
	$where = " and u.cool_num = '".$_REQUEST['data']."'";
}else if($_REQUEST['type']=='uid')
	$where = " and u.uid = '".$_REQUEST['data']."'";
	
$sql = "select u.nick_name,u.cool_num,c.*	 from mobile_user u,mobile_coins_log_bak c where c.uid = u.uid ".$where." order by a.add_time desc limit ".$pageIndex.",".$pageSize;
 
$pdo = new DB();
$db = $pdo->getDB();
$row = $db -> query($sql)-> fetchAll();
$arrays['data'] = $row;



 
echo json_encode($arrays);
?>
  