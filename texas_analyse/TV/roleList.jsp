<%@ page contentType="text/html;charset=gb2312"%>
<%@ taglib uri="http://java.sun.com/jsp/jstl/core" prefix="c"%>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=gb2312" /> 
<meta http-equiv="EXPIRES" content="Wed, 08 Feb 2013 11:00:51 GMT" />
<meta http-equiv="pragma" content="cache" />   
<meta http-equiv="Cache-Control" content="cache">
<title>.</title>
<script src="/js/jquery.js" language="javascript"></script>
<script src="/js/jquery-ui.js" language="javascript"></script>
<link href="/css/j/jquery-ui.css" rel="stylesheet" type="text/css">
<link href="../css/default.css" rel="stylesheet" type="text/css">
<script language="javascript">
	var perArr = new Array(["a","游戏管理(北美)"]
							,["b","充值管理(北美)"]
							,["c","玩家信息(北美)"]
							,["d","市场广告(北美)"]
							,["e","运营工具(北美)"]
							,["f","龙之逆袭（运营管理）"]
							,["k","龙之逆袭（联运）"]
							,["g","运营工具(中国)"]
							,["i","运营工具(台湾)"]
							,["h","斗将三国（运营管理）"]
							,["m","电视游戏后台管理"]
							,["y","财务管理"]
							);
	function perList(){
		var str="";
		str +="<div><input type='checkbox' name='chkser' value='1'><span style='color: red;'>客服</span></div>";
		for(var i=0; i<perArr.length; i++){
			str +="<li class='perli'><input type='checkbox' name='chkper' value='"+perArr[i][0]+"'>"+perArr[i][1]+"</li>";
		}
		$("#perdiv01").html(str);
	}
	
	$(function(){
		var u = $(":checkbox[name='user_id']");
		for(var i=0; i<u.length; i++){
			var per_name="";
			var n = u.get(i).value;
			var s = $("#b"+n).val().split(',');
			for(var j=0; j<s.length; j++){
				for(var k=0;k<perArr.length; k++)
				if(perArr[k][0]==s[j]){
					per_name += perArr[k][1]+"&nbsp;&nbsp;";
					break;
				}
				$("#a"+n).html(per_name);
			}
		}
		
		$('#itemBox1').dialog({   
				width: 300,   
        height: 300, 
        modal: true,   
        autoOpen: false
    });
    
    $('#itemBox2').dialog({   
				width: 300,   
        height: 300, 
        modal: true,   
        autoOpen: false
    });
    
    if("${actionMessage}"!=""){
		alert("${actionMessage}");
	}
    
	});
		
	function addUserDiv(){	
		$("#itemBox1").dialog('open');		
	}
	
	function doAddUser(){
		var s1 = $("#name1").val();
		var p1 = $("#password1").val();
		if(s1.trim()==""){
			alert("用户名不能为空！");
			return false;
		}
		if(p1.trim()==""){
			alert("密码不能为空！");
			return false;
		}
		window.location.href="/admin/user/addAdminEdit.action?param0="+s1+"&param1="+p1;
	}
	
	
	function setPermissionDiv(){
		var u = $(":checkbox[name='user_id'][checked=true]");
		var str = "",curId;
		for(var i=0; i<u.length; i++){
				curId = u.get(i).value;
				str +=","+curId;
		}
		
		if(str == ""){
			alert("请选择至少一位用户！");
			return false;
		}
		$("#ids").val(str.substring(1));
		$("#itemBox2").dialog('open');
		perList();
		
		var s = $("#b"+curId).val();
		var p = $(":checkbox[name='chkper']");
		for(var i=0; i<p.length; i++){
			if(s.indexOf(p.get(i).value)>-1)p.get(i).checked=true;
		}
		var g = $("#u"+curId).val();
		if(g=="1002"){
			$("input:checkbox[name='chkser']").attr("checked",true);
		}
	}
	
	function setPermission(){
		var p = $(":checkbox[name='chkper'][checked=true]");
		var str = "";
		for(var i=0; i<p.length; i++){
			str += ","+p.get(i).value;
		}
		if(str != "")$("#pers").val(str.substring(1));
		if($("input:checkbox[name='chkser']").attr("checked")==true){
			document.form1.action = "/admin/role/roleSave.action?rad=1";
		}else{
			document.form1.action = "/admin/role/roleSave.action";
		}
		document.form1.submit();
	}
	
	function removePer(){
		if(!confirm("确定取消管理员吗?"))return;
		var u = $(":checkbox[name='user_id'][checked=true]");
		var str="";
		for(var i=0; i<u.length; i++){
				str += ","+u.get(i).value;
		}
		if(str == "")return;
		$("#ids").val(str.substring(1));
		document.form1.action = "/admin/role/roleSave.action";
		document.form1.submit();
}
	
</script>

<style type="text/css">
	.perli{
		list-style:none;
		float:left;
		width:130px;
		line-height:20px;
	}
</style>
</head>

<body>
<div id="itemBox1" style="display:none;">
	<div style="padding-top:20px;">用户名：<input type="text" id="name1" value="" maxlength="30" size="30" class="textbox"></div>
	<div style="padding-top:20px;">&nbsp;&nbsp;&nbsp;密码：<input type="text" id="password1" value="" maxlength="30" size="30" class="textbox"></div>
	<div style="padding-top:10px;text-align:center;"><input type="button" name="btn01" value="确 定" class="button" onclick="doAddUser()"></div>
</div>

<div id="itemBox2" style="display:none">
	<div style="padding-bottom:5px;">选择权限：</div>
	<div id="perdiv01"></div>
	<div style="clear:both;"></div>
	<div style="padding:5px;text-align:center;"><input type="button" name="btn02" value="确 定" class="button" onclick="setPermission()"></div>
</div>

<form name="form1" action="" method="post">
<table border="0" align="center" cellpadding="0" cellspacing="0" class="table1" class="table1">
<tr>
	<td class="table1-title">管理员列表</td>
</tr>
<tr>
<td>
			<table width="800" border="0" align="center" cellpadding="2" cellspacing="0" class="table">
				<tr class="table-head-left">
						<td width="5%" height="22"></td>
						<td width="30%" height="22">用户名</td>
						<td width="65%">权限</td>
				</tr>
					<c:forEach items="${objs}" var="o">
						<tr>
							<td><input type="checkbox" name="user_id" value="${o.userId}"></td>
							<td height="20">${o.userName}<c:if test="${o.userGroup==1002}">(客服)</c:if></td>
							<td>
									<div id="a${o.userId}">获取数据中...</div>
									<input type="hidden" id="b${o.userId}" value="${o.permission}" />
									<input type="hidden" id="u${o.userId}" value="${o.userGroup}" />
							</td>
						</tr>
					</c:forEach>
					<tr>
						<td height="50" colspan="10" align="center">
							<input type="button" value="添加管理员" class="button" onclick="addUserDiv();" >
							&nbsp;<input type="button" value="设置权限" class="button" onclick="setPermissionDiv();" >
							&nbsp;<input type="button" value="取消管理员" class="button" onclick="removePer()">
							<input type="hidden" name="param0" id="ids" value="">
							<input type="hidden" name="param1" id="pers" value="">
						</td>
					<tr>			
			</table>
</td>
<tr>
</table>
</form>

</body>
</html>
