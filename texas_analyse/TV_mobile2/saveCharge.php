 <link rel="stylesheet" type="text/css" href="../bootstrap/css/bootstrap.min.css" />

<?php
require_once 'curl.php';   
require_once '../include/database.class.php';
$pdo = new DB();
  $db = $pdo->getDB();
$array = $_POST;
//$obj = fetch_page('http://112.124.4.59:9899/charge',$array);
$obj = fetch_page('http://180.150.178.175:9899/charge',$array);
$code = $obj['code'];

if($code == 0){
	$data = $obj['data']['coins'];
	$msg = $obj['msg'].",now value:".$data['val'].", old value:".$data['oldValue'];
	$operator = $_COOKIE['adm_username'];
	if($_POST['isCoolNum'] == 1){
		$sql = "select uid from mobile_user where cool_num = '".$_POST['uid']."'";
		$row = $db -> query($sql) -> fetch();
		$uid = $row['uid'];
	}else{
		$uid = $_POST['uid'];
	}
	if($_POST['type'] == "coins")$msgs = "赠送筹码";
	else if($_POST['type'] == "trialCoins")$msgs = "体验筹码";
	else if($_POST['type'] == "gold")$msgs = "赠送金币";
	else if($_POST['type'] == "chargeCoins")$msgs = "充值筹码";
	else if($_POST['type'] == "chargeGold")$msgs = "充值金币";
	 
	$sql = "insert into mobile_op_log(uid,type,value,add_time,operator,msg,ip,game) values
	('".$uid."','".$_POST['type']."','".$_POST['val']."','".date("Y-m-d H:i:s",time())."','".$operator."','".$msgs."','".getIp()."','德州扑克')";
	//echo $sql;
	$db -> query($sql);
}else{
	$msg = $obj['msg'];
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
