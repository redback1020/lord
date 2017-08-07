<?php
require_once '../include/database.class.php';
$pdo = new DB();
$db = $pdo->getDB();
$sql = "SELECT * FROM  `mobile_game_login` WHERE id >15500 AND id <15643";
//$sql = " select a.add_time,a.uid from mobile_analyse a,mobile_game_login m where a.uid = m.uid and m.reg_time='0000-00-00 00:00:00' group by a.uid";
$row = $db -> query($sql) -> fetchAll();
foreach($row as $val){
	
	$sql = "update mobile_game_login set add_time = '2014-02-13 ".date("H:i:s",strtotime($val['add_time']))."' where id = '".$val['id']."'";
	$db -> query($sql);
	echo $sql.'<br>';
}
?>