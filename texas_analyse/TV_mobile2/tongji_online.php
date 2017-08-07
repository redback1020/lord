  
 
  	<?php
	require_once '../include/database.class.php';
	require_once 'curl.php';  
	$obj = fetch_page('http://180.150.178.175:8200/onlines',array('sign'=>'jlfsd87912hjk312h90f!@fsjdkl!23','count'=>1));
	 
	$obj = $obj['data'];
	$inRoomUser = $obj['inRoomUser']; 
	$hall = $obj['hall']; 
	$num = $inRoomUser + $hall;
	$add_time = date("Y-m-d H:i:s",time());
	$pdo = new DB();
	$db = $pdo->getDB();
	$sql = "insert into mobile_online(add_time,num) values('".$add_time."','".$num."')";
	$id = $db -> query($sql);
	echo $db->lastInsertId(); 
	?>
	 
