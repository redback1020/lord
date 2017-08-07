<?php
session_start();
include_once "checkPriv.php";
require_once '../include/database.class.php';
$oldpass = $_POST['oldpass'];
$newpass = $_POST['newpass'];
$sql = "select * from adm_user where admin_name = '".$_SESSION['admin_name']."'";
$row = $db -> query($sql)->fetch();
if(md5($oldpass) == $row['admin_pwd']){
	$sql = "update adm_user set admin_pwd = '".md5($newpass)."' where admin_name = '".$_SESSION['admin_name']."'";
	$pdo->getDB(1)->exec($sql);
	// $row = $db -> query($sql);
	echo '<script>alert("更新成功!现退出重新登陆!");parent.location.href="logout.php"</script>';
}else{
	echo '<script>alert("密码错误, 请确认您的原始密码正确!");history.go(-1);</script>';
}



?>
