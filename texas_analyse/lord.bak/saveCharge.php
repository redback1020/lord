 <link rel="stylesheet" type="text/css" href="../bootstrap/css/bootstrap.min.css" />

<?php
  
require_once '../include/database.class.php'; 
 
$type = $_POST['type'];
$val = $_POST['val'];
$time = time();
if($_POST['isCoolNum'] == 1){
	$sql = "select uid from lord_game_user where cool_num = '".$_POST['uid']."'";
	$row = $db -> query($sql) -> fetch();
	$uid = $row['uid'];
}else{
	$uid = $_POST['uid'];
}
	
	
$mac = md5("uid=".$uid."&".$type."=".$val."&time=".$time."&sign=HwhfYlEhdB978z30");
$sql= "select ".$type." from lord_game_user where uid = '".$uid."'"; 
$row = $db -> query($sql) -> fetch(); 
$code = file_get_contents("http://115.29.162.107/chargeBySYS.php?uid=".$uid."&".$type."=".$val."&time=".$time."&mac=".$mac);
  
if($code == "ok"){
	
	
	$operator = $_COOKIE['adm_username'];
	
	if($_POST['type'] == "coins")$msgs = "赠送筹码";
	else if($_POST['type'] == "gold")$msgs = "赠送金币";
	else if($_POST['type'] == "chargeCoins")$msgs = "充值筹码";
	else if($_POST['type'] == "chargeGold")$msgs = "充值金币";
	 $msg = $msgs." 成功, 当前值:".($val+$row[$type]).", 充值前值:".$row[$type];
	$sql = "insert into mobile_op_log(uid,type,value,add_time,operator,msg,ip,game) values
	('".$uid."','".$_POST['type']."','".$_POST['val']."','".date("Y-m-d H:i:s",time())."','".$operator."','".$msgs."','".getIp()."','斗地主')";
	//echo $sql;
	$db -> query($sql);
}  else{
	$msg = "失败, 请联系管理员";
}
function getIp(){
	$onlineip = "";
	if(getenv('HTTP_CLIENT_IP') && strcasecmp(getenv('HTTP_CLIENT_IP'), 'unknown')) {
	    $onlineip = getenv('HTTP_CLIENT_IP');
	} elseif(getenv('HTTP_X_FORWARDED_FOR') && strcasecmp(getenv('HTTP_X_FORWARDED_FOR'), 'unknown')) {
	    $onlineip = getenv('HTTP_X_FORWARDED_FOR');
	} elseif(getenv('REMOTE_ADDR') && strcasecmp(getenv('REMOTE_ADDR'), 'unknown')) {
	    $onlineip = getenv('REMOTE_ADDR');
	} elseif(isset($_SERVER['REMOTE_ADDR']) && $_SERVER['REMOTE_ADDR'] && strcasecmp($_SERVER['REMOTE_ADDR'], 'unknown')) {
	    $onlineip = $_SERVER['REMOTE_ADDR'];
	}
	return $onlineip;
}
  $time = time();
$key = "qwe!@#321";
$sign = md5($key.$time);
?>
 <body>
	 
  	<div class="container">
  	
	<div>
		<fieldset>
		<legend>系统提示</legend>	
		<div class="">
			<div class="">
				 
				<?=$msg?><a href="charge.php?time=<?=$time?>&sign=<?=$sign?>" style="font-size:20px;	">返回</a>继续操作
			</div>
			 
			
		</div>				
		</fieldset>
		 
		
	</div>
	  
  </body> 