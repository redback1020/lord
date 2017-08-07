<?php
include_once "checkPriv.php";
require_once '../include/database.class.php';
$ids = $_POST['ids'];
$pers = $_POST['data'];

	$sql = "update adm_user set data_priv = '".$pers."' where id in(".$ids.")";
	$pdo->getDB(1)->exec($sql);
	// $row = $db -> query($sql);
	header("location:roleList.php");

?>
