<?php
session_start(); 
// echo $_SESSION['uu_auth'];
//echo $_SESSION['uu_auth'];echo phpinfo();
/*if(isset($_SESSION['uu_auth'])){
header("location:main.html");
}*/
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>登陆</title>
<link href="../css/login.css" rel="stylesheet" type="text/css"> 
<script src="../js/jquery.js"></script>
</head>
<body>
<form method='post' name="login" id="login" action="checkLogin.php" >
<div id="login-box">
	<div id="resultMsg"></div>
	<input type="text" name="admin_name" id="admin_name" />
	<input type="password" name="admin_pwd" id="admin_pwd" />
	<input type="text" name="verify" id="verify" />
	<img id="verifyImg" src="verifycode.php" align="absmiddle" alt="点击刷新验证码" title="点击刷新验证码" width="50" height="22">
	<input type="image" id="loginBtn" src="../images/login_btn.png" />
	<input type="hidden" name="ajax" value="1">
	
</div>
</form>
</body>

<script type="text/javascript">
var AJAX_LOADING = "Processing, please wait...";
var AJAX_ERROR = "An error is occurred for AJAX request";
jQuery(function($){
	document.getElementById('admin_name').focus();
	if(top.location != self.location)
	{
		top.location.href = self.location.href;
		return;
	}
	
	$("#verifyImg").click(function(){
		fleshVerify();
	});
	
	$(document).keypress(function(e){
		if(e.keyCode == 13)
		{
			login()
		}
	});
	
	$("#loginBtn").click(function(){
		login();
		return false;
	});
});

function login()
{
	if($('#admin_name').val()==''){
		$("#resultMsg").addClass('error').html('请输入用户名').show().fadeOut(3000);
		fleshVerify();
		return false;
	}
	if($('#admin_pwd').val()==''){
		$("#resultMsg").addClass('error').html('请输入密码').show().fadeOut(3000);
		fleshVerify();
		return false;
	}
	if($('#verify').val()==''){
		$("#resultMsg").addClass('error').html('请输入验证码').show().fadeOut(3000);
		fleshVerify();
		return false;
	} 
	$("#resultMsg").stop().removeClass('error').addClass('loading').html(AJAX_LOADING).show();
	
	$.ajax({
		url: "checkLogin.php",
		type:"POST",
		cache: false,
		data:$("#login").serialize(),
		dataType:"json",
		error: function(){
			//$("#resultMsg").addClass('error').html(AJAX_ERROR).show().fadeOut(3000);
		},
		success: function(result){
		   
			$("#resultMsg").hide();
			if(result==1)
				location.href = 'main.html';
			else if(result == -1){
				$("#resultMsg").addClass('error').html('验证码有误, 请重新输入').show().fadeOut(3000);
			}else if(result == -2){
				$("#resultMsg").addClass('error').html('用户不存在!').show().fadeOut(3000);
			}else if(result == -3){
				$("#resultMsg").addClass('error').html('用户名或密码错误!').show().fadeOut(3000);
			}else{	
				
				$("#resultMsg").addClass('error').html(result).show().fadeOut(3000);
				
			}
			fleshVerify();
			
		}
	});
}

function fleshVerify()
{
 
	$("#verifyImg").attr('src',"verifycode.php");
}
</script>
</html>