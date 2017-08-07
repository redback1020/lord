 <link rel="stylesheet" type="text/css" href="../bootstrap/css/bootstrap.min.css" />
<?php
require_once 'curl.php';    
$obj = fetch_page('http://115.29.162.107/bCast.php',array('key'=>'9hFiMhYd2h9Th5U8GsbIuf37uD','msg'=>$_POST['msg']));
 
 
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
	require_once '../include/database.class.php';
	 
	$type = "sys";
	$operator = $_COOKIE['adm_username'];
	$msg = "系统消息:".$_POST['msg'];
	$ip = getIp(); 
	$sql = "insert into mobile_op_log(uid,type,value,add_time,operator,msg,ip,game) values ('0','".$type."','','".date("Y-m-d H:i:s",time())."','".$operator."','".$msg."','".$ip."','斗地主')";
 
	$db -> query($sql);
		
 
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
				 
				发送成功, 将在一分钟内显示<a href="sendmsg.php?time=<?=$time?>&sign=<?=$sign?>" style="font-size:20px;	">返回</a>继续操作
			</div>
			 
			
		</div>				
		</fieldset>
		 
		
	</div>
	  
  </body>
