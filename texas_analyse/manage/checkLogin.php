<?php
require_once '../include/database.class.php';
session_start(); 
$admin_name = trim($_POST['admin_name']);
$admin_pwd = md5(trim($_POST['admin_pwd']));
$verify = trim($_POST['verify']);
if(strtoupper($verify) == $_SESSION['code']) {
	$sql = "select * from adm_user where admin_name = '".$admin_name."' ";
	$row = $db -> query($sql) -> fetch();
	if($row){
		if($row['admin_pwd'] == $admin_pwd){
			$_SESSION['uu_auth'] = $row['id'];
			$_SESSION['admin_name'] = $admin_name;
			$_SESSION['access_priv'] = $row['access_priv'];
			$_SESSION['data_priv'] = $row['data_priv'];
			$_SESSION['time'] = $row['time'];
			$_SESSION['last_access'] = time();
			echo 1;
		}else{
			echo '-3';
		}
	}else{
		echo '-2';
	}
}else{
	echo -1;
	 
}
?>
