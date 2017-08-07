<?php
session_start();
$time = $_REQUEST['time'];
$sign = $_REQUEST['sign'];
$key = "qwe!@#321";
$now = time();

if(!isset($_SESSION['time'])){
	$_SESSION['time'] = $time;
	
} 
 
if($now - $_SESSION['time']>10800){
	echo "对不起,您已超时, 请退出重新登陆!";
	 
	unset($_SESSION['time']);
	echo '<script>alert("对不起,您已超时, 请退出重新登陆!");top.window.location.href="http://www.youjoy.com/admin/logout.action"</script>';
	exit;
} else{
 
	$_SESSION['time'] = $now;
}
if($sign != md5($key.$time)){
	echo "对不起, 暂无权限, 请联系管理员!";
	exit;
}

function getPri(){
	$time = time();
	$key = "qwe!@#321";
	$sign = md5($key.$time);
	return "time=".$time."&sign=".$sign;
}