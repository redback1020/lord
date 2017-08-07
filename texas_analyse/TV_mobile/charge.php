<?php
require_once '../include/priv.php';
?>
<script type="text/javascript" src="../js/jquery.js"></script>
<link rel="stylesheet" type="text/css" href="../bootstrap/css/bootstrap.min.css" />
 
 <body>
  	<div class="container">
  
  	
	
	<div>
		<fieldset>
		<legend>赠送充值</legend>	
					
	</fieldset>
	</div>
	<?php
	
	?>
	<form action="saveCharge.php" method="post">
	<div>
		<table class="table table-bordered table-condensed table-hover" style="font-size:12px;">
			<tr><td>类型:</td>
			
				<td><select name="type" class="span2">
					<option value="coins">赠送筹码</option>
					<option value="trialCoins">体验筹码</option>
					<option value="gold">赠送金币</option>
					<option value="chargeCoins">充值筹码</option>
					<option value="chargeGold">充值金币</option>
					</select>
				</td>
			</tr>
			<tr>
			<td>数值:</td>
			<td><input type="text" name="val" class="span2" style="height:30px;"/></td>
			</tr>
			<tr>
			<td>用户:</td>
			<td><input type="text" name="uid"  class="span2" style="height:30px;"/>&nbsp;&nbsp;&nbsp;&nbsp;<input type="checkbox" name="isCoolNum" value="1" checked="checked" style="margin-bottom:25px"/>&nbsp;&nbsp;靓号
			</tr>
			<input type="hidden" name="channel" value="web" /></td>
			 
			<input type="hidden" name="sign" value="jlfsd87912hjk312h90f!@fsjdkl!23" />
			<tr><td colspan="2" class="span1">
			<input type="submit" value="提交" class="btn" />
			</td></tr>

		</table>
	</div>
	</form> 
	
	</div>
  </body>
