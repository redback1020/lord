<?php
require_once '../manage/checkPriv.php';
?>
<script type="text/javascript" src="../js/jquery.js"></script>
<link rel="stylesheet" type="text/css" href="../bootstrap/css/bootstrap.min.css" />
<body>
	<div style="padding:8px 10px;">
		<form action="userDataSave.php" method="post">
			<fieldset>
				<legend>用户货币增扣</legend>
			</fieldset>
			<table class="table table-bordered table-condensed table-hover" style="font-size:12px;">
				<tr>
					<td style="width:100px;font-size:14px;">增扣类型:</td>
					<td><select name="type" class="span2">
						<option value="coins">乐豆</option>
						<option value="coupon">乐券</option>
						<option value="lottery">抽奖数</option>
						</select> 暂不提供增扣“乐币”功能
					</td>
				</tr>
				<tr>
					<td style="width:100px;font-size:14px;">用户识别方式:</td>
					<td><select name="isCoolNum" class="span2">
						<option value="1">用户编号(ID)</option>
						<option value="0">用户UID</option>
						</select>
					</td>
				</tr>
				<tr>
					<td style="width:100px;font-size:14px;">编号(ID)/UID:</td>
					<td><input type="text" name="uid"  class="span2" style="height:30px;margin:0;"/></td>
				</tr>
				<tr>
					<td style="width:70px;font-size:14px;">增扣数字:</td>
					<td><input type="text" name="val" class="span2" style="height:30px;margin:0;"/>&nbsp;&nbsp;增加:1000 / 扣除:-1000</td>
				</tr>
				<tr>
					<td>&nbsp;</td>
					<td class="span1"><input type="submit" value="提交" class="btn" /></td>
				</tr>
			</table>
			<!-- <input type="hidden" name="channel" value="web" /> -->
			<!-- <input type="hidden" name="sign" value="jlfsd87912hjk312h90f!@fsjdkl!23" /> -->
		</form>
	</div>
</body>
