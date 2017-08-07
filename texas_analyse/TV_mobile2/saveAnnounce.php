<script type="text/javascript" src="../bootstrap/js/bootstrap.min.js"></script>
<link rel="stylesheet" type="text/css" href="../bootstrap/css/bootstrap.min.css" />
<?php
$sysini = "../include/sys_mobile.ini";  //系统的配置文件
if (file_exists($sysini))
{
	$sysini_array = parse_ini_file($sysini);
	if(isset($sysini_array['announce']))
	{
	   $url = filter_var($sysini_array['announce'],FILTER_SANITIZE_STRING); 
	}
	else 
	{
		echo "读取系统的配置文件有误"; 
		exit();       
	} 
}
else
{
	
	echo "数据库连接的配置文件不存在"; 
	exit("请与管理员联系");
}	

/*$obj = json_decode($data[0]); 
$hall = str_replace("|","\n",$obj->hall);
$room = $obj->room;
$match = $obj->match;*/
$hall=  $_POST['hall'];
$room=  $_POST['room'];
$match=  $_POST['match'];
$array['hall'] = str_replace("\n","|",$hall);
$array['room'] = $room;
$array['match'] = $match;
$str = json_encode($array); 
$fp =  fopen($url,"w"); 
$p = fwrite($fp,$str);
fclose($fp);

require_once '../include/database.class.php';

$type = "annouce";
$operator = $_COOKIE['adm_username'];
$msg = "发送公告";
$sql = "insert into mobile_op_log(uid,type,value,add_time,operator,msg,ip,game) values
	('0','".$type."','','".date("Y-m-d H:i:s",time())."','".$operator."','".$msg."','".getIp()."','德州扑克')";
$db -> query($sql);
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
				 
				数据更新成功!<a href="announce.php?time=<?=$time?>&sign=<?=$sign?>" style="font-size:20px;	">返回</a>继续编辑
			</div>
			 
			
		</div>				
		</fieldset>
		 
		
	</div>
	  
  </body>
