<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=gb2312" />  
<meta http-equiv="pragma" content="cache" />   
<meta http-equiv="Cache-Control" content="cache">
<title></title>
<style>
td{ font-size:50px; font-weight:bold; color:#00C1FF}
span{ font-size:50px; font-weight:bold; color:#00C1FF}
</style>
<script type="text/javascript">
function getNameCookie(){
	var strCookie=document.cookie;
	var arrCookie=strCookie.split("; ");
	var name="";
	for(var i=0;i<arrCookie.length;i++){
		var arr=arrCookie[i].split("=");
		if("adm_username"==arr[0]){
			name=arr[1];
			break;
		}
	}
	if(name=='wangwei'||name=='wangjin'){
		name="王老板, ";
	}else if(name=='lixin'){
		name="李老板, ";
	}
	return name;
}
</script>
</head>

<body onLoad="document.getElementById('name').innerText=getNameCookie()">
	<table width="600" border="0" align="center" cellpadding="0" cellspacing="0" style="margin-top:150px;">  
		<tr>
			<td align="center" height="60">
				<span id="name"></span>欢迎进入
			</td>
		</tr>
		<tr>
			<td align="center" height="60">
				电视游戏后台管理系统
			</td>
		</tr>
	</table>
	<div style="margin:40px auto 0px; width:886px;"><img src="../image/default_bg.jpg" /></div>
</body>
</html>
