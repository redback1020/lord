<?php
include_once "checkPriv.php";
require_once '../include/database.class.php';
$ids = $_POST['ids'];
$pers = $_POST['pers'];
	if($pers == ""){
		$sql = "delete from adm_user where id in(".$ids.")";
		$pdo->getDB(1)->exec($sql);
		// $row = $db -> query($sql);

	}else{
		$sql = "update adm_user set access_priv = '".$pers."' where id in(".$ids.")";
		$pdo->getDB(1)->exec($sql);
		// $row = $db -> query($sql);
	}

	header("location:roleList.php");

?>
