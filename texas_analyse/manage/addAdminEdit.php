<?php
require_once '../include/database.class.php';
$admin_name = trim($_GET['admin_name']);
$admin_pwd = md5(trim($_GET['admin_pwd']));


	$sql = "select * from adm_user where admin_name = '".$admin_name."' ";
	$row = $db -> query($sql) -> fetch();
	if($row){
		echo '<script>alert("用户'.$admin_name.'已存在!");history.go(-1);</script>';
	}else{
		$sql = "insert into adm_user(admin_name, admin_pwd)values('".$admin_name."','".$admin_pwd."')";
		$pdo->getDB(1)->exec($sql);
		// $db -> query($sql);
		header("location:roleList.php");
	}

?>
