<?php
include_once "checkPriv.php";
require_once '../include/database.class.php';
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=gb2312" /> 
<meta http-equiv="EXPIRES" content="Wed, 08 Feb 2013 11:00:51 GMT" />
<meta http-equiv="pragma" content="cache" />   
<meta http-equiv="Cache-Control" content="cache">
<title>.</title>
<script src="../js/jquery.js" language="javascript"></script> 
<link href="../css/default.css" rel="stylesheet" type="text/css">
<script language="javascript">
function checkForm(){
	if($("#newpass").val() == ""||$("#newpass1").val() == ""||$("#oldpass").val() == ""){
		alert("密码不能为空");
		return false;
	}
	if($("#newpass").val() != $("#newpass1").val()){
		alert("新密码与确认密码不一致,请重新输入!");
		return false;
	}
	if(checkpwd()){
		document.form1.submit();
	}
	
	
}
function checkpwd(){
	var pwd = document.getElementById("newpass1");
	 
	if(pwd.value=="" || pwd.value==""  ){
	  alert("密码不能为空");  
	  return false;
	}
	if(pwd.value.length<6||pwd.value.length>16){
	   alert("密码长度为6-16个字符");
	   return false;
	}
	for(var i=0;i<pwd.value.length;i++)
	   {
		  var charTest=pwd.value.toLowerCase().charAt(i);
	   if( (!(charTest>='0' && charTest<='9')) &&  (!(charTest>='a' && charTest<='z'))  && (charTest!='_') )
	   {
		  alert("密码包含非法字符，只能包括字母,数字和下划线");
		return false;
		}
	   }
 
	   return true;
}
	
</script>
 <style type="text/css">
	 
	table{
		font-size:12px;
	}
	.table td{height:50px;}
	input{height:30px;}
</style>
</head>

<body>
 

<form name="form1" action="updaSave.php" method="post">
<table border="0" align="center" cellpadding="0" cellspacing="0" class="table1" class="table1">
	<tr>
		<td class="table1-title">修改个人信息</td>
	</tr>
	<tr>
	<td>
		<table width="800" border="0" align="center" cellpadding="2" cellspacing="0" class="table">
			<tr class="table-head-left">
					<td  height="22" width="200">&nbsp;</td>
					<td  height="22">&nbsp;</td>
					 
			</tr>
			
			<tr>
				<td>原始密码:</td>
				<td height="25"><input type="password" id="oldpass" name="oldpass"></td>
			</tr>
			
			<tr>
				<td>新密码:</td>
				<td><input type="password" id="newpass" name="newpass">&nbsp;*密码只能包括字母,数字和下划线,长度为6-16个字符</td>
			</tr>
			<tr>
				<td>确认新密码:</td>
				<td><input type="password" id="newpass1" name="newpass1"></td>
			</tr>
			
			 
					
				
				 
			<tr>
				<td height="50" colspan="2" align="center">
					<input type="button" value="确&nbsp;&nbsp;认" class="button" onclick="checkForm();" >&nbsp;&nbsp;
					<input type="reset" value="重&nbsp;&nbsp;置" class="button" >
					 
				</td>
			<tr>			
		</table>
	</td>
	<tr>
</table>
</form>

</body>
</html>
