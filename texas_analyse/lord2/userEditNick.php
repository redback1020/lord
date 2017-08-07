<?php
require_once '../manage/checkPriv.php';
?>
<script type="text/javascript" src="../js/jquery.js"></script>
<link rel="stylesheet" type="text/css" href="../bootstrap/css/bootstrap.min.css" />
<body>
	<div style="padding:8px 10px;">
		<form action="userSaveNick.php" method="post">
			<fieldset>
				<legend>修改用户昵称</legend>
			</fieldset>
			<table class="table table-bordered table-condensed table-hover" style="font-size:12px;">
				<!--<tr>
					<td style="width:70px;font-size:14px;">类型:</td>
					<td><select name="type" class="span2">
						<option value="coins">赠送筹码</option> 
						<option value="gold">赠送金币</option> 
						</select>
					</td>
				</tr>-->
				<tr>
					<td style="width:70px;font-size:14px;">用户靓号:</td>
					<td><input type="text" name="uid"  class="span2" style="height:30px;margin:0;"/></td>
				</tr>
				<tr>
					<td style="width:70px;font-size:14px;">新的昵称:</td>
					<td><input type="text" name="val" class="span2" style="height:30px;margin:0;"/>&nbsp;&nbsp;不建议使用特殊符号</td>
				</tr>
				<tr>
					<td>&nbsp;</td>
					<td class="span1"><input type="submit" value="提交" class="btn" /></td>
				</tr>
			</table>
			<input type="hidden" name="isCoolNum" value="1" />
			<input type="hidden" name="channel" value="web" />
			<input type="hidden" name="sign" value="jlfsd87912hjk312h90f!@fsjdkl!23" />
		</form>
	</div>
</body>
