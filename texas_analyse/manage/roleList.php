<?php
include_once "checkPriv.php";
require_once '../include/database.class.php';
?>
<!DOCTYPE html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta http-equiv="EXPIRES" content="Wed, 08 Feb 2013 11:00:51 GMT" />
<meta http-equiv="pragma" content="cache" />
<meta http-equiv="Cache-Control" content="cache">
<title>.</title>
<script src="../js/jquery.js" type="text/javascript"></script>
<script src="../js/jquery-ui.js" type="text/javascript"></script>
<link href="../css/j/jquery-ui.css" rel="stylesheet" type="text/css">
<link href="../css/default.css" rel="stylesheet" type="text/css">
<script type="text/javascript">
var perArr = [["a","有乐德州"]
	,["b","有乐德州(渠道)"]
	,["c","有乐地主"]
	,["d","有乐地主(渠道)"]
	,["e","有乐地主-测试"]
	,["y","个人信息设置"]
	,["z","后台权限管理"]
];
function perList(){
	var str="";
	for(var i=0; i<perArr.length; i++){
		str +="<li class='perli'><input type='checkbox' name='chkper' value='"+perArr[i][0]+"'>"+perArr[i][1]+"</li>";
	}
	$("#perdiv01").html(str);
}
$(function(){
	$('#itemBox1').dialog({ width: 300, height: 300, modal: true, autoOpen: false });
	$('#itemBox2').dialog({ width: 400, height: 300, modal: true, autoOpen: false });
	$('#itemBox3').dialog({ width: 500, height: 400, modal: true, autoOpen: false });
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
	window.location.href="addAdminEdit.php?admin_name="+s1+"&admin_pwd="+p1;
}

function setPermissionDiv(){
	var u = $(":checkbox[name='user_id'][checked=true]");
	var str = "";
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
function setDataDiv(){
	var u = $(":checkbox[name='user_id'][checked=true]");
	var str = "";
	var curId = "";
	for(var i=0; i<u.length; i++){
			curId = u.get(i).value;
			str +=","+curId;
	}

	if(str == ""){
		alert("请选择至少一位用户！");
		return false;
	}
	$("#ids").val(str.substring(1));
	$("#itemBox3").dialog('open');


	var s = $("#c"+curId).val();
	var p = $(":checkbox[name='chkdata']");
	p.attr("checked",false);
	for(var i=0; i<p.length; i++){
		if(s.indexOf(p.get(i).value)>-1)p.get(i).checked=true;
	}

}

function setPermission(){
	var p = $(":checkbox[name='chkper'][checked=true]");
	var str = "";
	for(var i=0; i<p.length; i++){
		str += ","+p.get(i).value;
	}
	if(str == ""){
		alert("请选择权限!");return false;
	}else{
		if(str != "")$("#pers").val(str.substring(1));

		document.form1.action = "roleSave.php";
		document.form1.submit();
	}

}
function setData(){
	var p = $(":checkbox[name='chkdata'][checked=true]");
	var str = "";
	for(var i=0; i<p.length; i++){
		str += ","+p.get(i).value;
	}
//	if(str == ""){
		//alert("请选择数据权限!");return false;
//	}else{
		$("#data").val(str.substring(1));

		document.form1.action = "dataSave.php";
		document.form1.submit();
//	}
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
	document.form1.action = "roleSave.php";
	document.form1.submit();
}
function setCheck(name,obj){
	if(obj.checked){
		$(":checkbox[class='"+name+"']").attr("checked", true);
	}else{
		$(":checkbox[class='"+name+"']").attr("checked", false);
	}
}
</script>

<style type="text/css">
.perli{ list-style:none; float:left; width:150px; line-height:20px; }
table{ font-size:12px; }
</style>

</head>

<body>
<div id="itemBox1" style="display:none;">
	<div style="padding-top:20px;">用户名：<input type="text" id="name1" value="" maxlength="30" size="30" class="textbox" style="height:25px;"></div>
	<div style="padding-top:20px;">&nbsp;&nbsp;&nbsp;密码：<input type="text" id="password1" value="" maxlength="30" size="30" class="textbox" style="height:25px;"></div>
	<div style="padding-top:10px;text-align:center;"><input type="button" name="btn01" value="确 定" class="button" onclick="doAddUser()"></div>
</div>

<div id="itemBox2" style="display:">
	<div style="padding-bottom:5px;">选择权限：</div>
	<div id="perdiv01"></div>
	<div style="clear:both;"></div>
	<div style="padding:5px;text-align:center;"><input type="button" name="btn02" value="确 定" class="button" onclick="setPermission()"></div>
</div>
<div id="itemBox3" style="display:">
	<div style="padding-bottom:5px;">选择数据权限：</div>
	<div id="perdiv02">
		<?php
		$sql = "select channel from lord_total_channel group by channel order by channel";
		$row = $db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
		foreach($row as $val){
			echo "<li class='perli'><input type='checkbox' name='chkdata' value='".$val['channel']."'>".$val['channel']."</li>";
		}
		?>
	</div>
	<div style="clear:both;"></div>
	<div style="padding:5px;text-align:center;"><input type="button" name="btn02" value="确 定" class="button" onclick="setData()"></div>
</div>

<form name="form1" action="" method="post">
<table border="0" align="center" cellpadding="0" cellspacing="0" class="table1" class="table1">
	<tr>
		<td class="table1-title">管理员列表</td>
	</tr>
	<tr>
		<td>
		<table width="1000" border="0" align="center" cellpadding="2" cellspacing="0" class="table">
			<tr class="table-head-left">
					<td width="5%" height="22"></td>
                    <td width="5%">编号</td>
					<td width="15%" height="22">用户名</td>
					<td width="40%">权限</td>
					<td width="35%">数据权限</td>

			</tr>
			<?php
			$i=0;
			$sql = "select * from adm_user";
			$row = $db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
			foreach($row as $val){
                $i++;
				$data = "";
				if($val['access_priv']!=""){
					$priv = explode(",",$val['access_priv']);
					foreach($priv as $v){$data.=$lang[$v].',';}
				}
			?>
				<tr>
					<td><input type="checkbox" name="user_id" value="<?=$val['id']?>"></td>
                    <td><?=$i?></td>
					<td height="20"><?=$val['admin_name']?></td>
					<td>
						<div id="a<?=$val['id']?>"><?=$data?></div>
						<input type="hidden" id="b<?=$val['id']?>" value="<?=$val['access_priv']?>" />
						<input type="hidden" id="c<?=$val['id']?>" value="<?=$val['data_priv']?>" />
					</td>
					<td><?=$val['data_priv']==""?"全部":$val['data_priv']?></td>
				</tr>
			<?php
			}
			?>
			<tr>
				<td height="50" colspan="10" align="center">
					<input type="button" value="添加管理员" class="button" onclick="addUserDiv();" >
					&nbsp;<input type="button" value="设置权限" class="button" onclick="setPermissionDiv();" >
					&nbsp;<input type="button" value="设置数据权限" class="button" onclick="setDataDiv();" >
					&nbsp;<input type="button" value="取消管理员" class="button" onclick="removePer()">
					<input type="hidden" name="ids" id="ids" value="">
					<input type="hidden" name="pers" id="pers" value="">
					<input type="hidden" name="data" id="data" value="">
				</td>
			<tr>
		</table>
	</td>
	<tr>
</table>
</form>

</body>
</html>
