<?php
require_once '../include/database.class.php';
  $pageIndex = 50*$_REQUEST['pageIndex'];
  $pageSize = $_REQUEST['pageSize'];
  $time = $_REQUEST['time'];
  $pdo = new DB();
  $db = $pdo->getDB();
  $sql = "SELECT a.`wechat_nickname` as nickname , a.`poker_id` as uid , b.last_time as lasttime, b.add_time as addtime
	FROM  `t_wechat_user` a, mobile_analyse b
	WHERE a.poker_id = b.uid
	AND b.last_time <  '".$time."'
	ORDER BY b.last_time limit ".$pageIndex.",".$pageSize;
	//echo $sql;
   $row = $db -> query($sql)-> fetchAll();
   
   $sql = "SELECT count(*) as cn FROM  `t_wechat_user` a, mobile_analyse b
	WHERE a.poker_id = b.uid
	AND b.last_time <  '".$time."'";
	//echo $sql;
   $res = $db -> query($sql)-> fetch();
   
    $array['data'] = $row;
	$array['cn'] = $res['cn'];
	echo json_encode($array);
	 
		 
?>
 
