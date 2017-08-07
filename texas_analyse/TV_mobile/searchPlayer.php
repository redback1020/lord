<?php
require_once '../include/database.class.php';
  $pageIndex = 50*$_REQUEST['pageIndex'];
  $pageSize = $_REQUEST['pageSize'];
  $pdo = new DB();
  $db = $pdo->getDB();
  $sql = "SELECT a.uid, b.nick, a.num,a.max_coins
FROM mobile_match a, mobile_user b
WHERE a.uid = b.uid
AND b.is_robot =0 limit ".$pageIndex.",".$pageSize;
  $row = $db -> query($sql)-> fetchAll();
  
   $sql = "SELECT count(*) as cn
FROM mobile_match a, mobile_user b
WHERE a.uid = b.uid
AND b.is_robot =0  ";
  $res = $db -> query($sql)-> fetch();
	$array['data'] = $row;
	$array['cn'] = $res['cn'];
	echo json_encode($array);
	 
		 
?>
 
