<?php
require_once '../include/priv.php';
?>
<script type="text/javascript" src="../js/jquery.js"></script>
<link rel="stylesheet" type="text/css" href="../bootstrap/css/bootstrap.min.css" />
 <script src="../js/My97DatePicker/WdatePicker.js" language="javascript"></script>
<link rel="stylesheet" type="text/css" href="../bootstrap/css/bootstrap.min.css" />
 <body>
  	<div class="container">
  
  	
	
	<div>
		<fieldset>
		<legend>设置VIP</legend>	
					
	</fieldset>
	</div>
	<?php
	require_once '../include/database.class.php';
	$uid = $_GET['uid'];
	$pdo = new DB();
	$db = $pdo->getDB();
	$sql = "select u.uuid,m.* from user_user u, mobile_user m  where u.id = m.uid and m.uid = '".$uid."'";
	$row = $db -> query($sql)-> fetch();  
	if($row['vip_lv'] >0 && $row['vip_exp'] != '0000-00-00 00:00:00'){
		$sql = "select add_time from mobile_gold_log where uid = '".$uid."' and type='GIVE' and misc='vip'";
		$res = $db -> query($sql) -> fetch();
		$start = $res['add_time'];
		$end = $row['vip_exp'];
	}else{
		$start = "";
		$end = "";
	}
	
	?>
	<form action="saveVip.php" method="post">
	<div>
		<table class="table table-bordered table-condensed table-hover" style="font-size:12px;">
			<input type="hidden" name="uid" id="uid" value="<?=$uid?>">
			 
			<tr>
			<td>用户靓号: </td>
			<td><?=$row['cool_num']?></td>
			</tr>
			<tr>
			<td>用户昵称: </td>
			<td><?=$row['nick']?></td>
			</tr>
			<tr>
			<td>用户帐号: </td>
			<td><?=$row['uuid']?></td>
			</tr>
			<tr>
			<td>VIP开始生效时间: </td>
			<td><input value="<?=$start?>" class="span3" type="text" id="start" name="start" class="textbox dtime" readonly onclick="WdatePicker({dateFmt:'yyyy-MM-dd'})" style="height:30px;"/>
			</tr>
			<tr>
			<td>VIP结束时间: </td>
			<td><input value="<?=$end?>" class="span3" type="text" id="end" name="end" class="textbox dtime" readonly onclick="WdatePicker({dateFmt:'yyyy-MM-dd'})"  style="height:30px;"/>
			</tr>
			 
			<tr><td colspan="2" class="span1">
			<input type="submit" value="提交" class="btn" />
			</td></tr>

		</table>
	</div>
	</form> 
	
	</div>
  </body>
