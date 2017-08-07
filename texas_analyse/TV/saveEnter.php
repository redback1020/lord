 <link rel="stylesheet" type="text/css" href="../bootstrap/css/bootstrap.min.css" />
<?php

/*$obj = json_decode($data[0]); 
$hall = str_replace("|","\n",$obj->hall);
$room = $obj->room;
$match = $obj->match;*/
$tips =  $_POST['tips']; 
$file = $_POST['file'];
$sysini = "../include/sys.ini";  //系统的配置文件
if (file_exists($sysini))
{
	$sysini_array = parse_ini_file($sysini);
	if(isset($sysini_array[$file]))
	{
	   $url = filter_var($sysini_array[$file],FILTER_SANITIZE_STRING); 
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

for($i=0;$i<count($tips);$i++){
	//$array[] = "tips:".$tips[$i];
	$array[] = $tips[$i];
} 
$str = implode(",",$array);
$fp =  fopen($url,"w"); 
$p = fwrite($fp,$str);
fclose($fp);
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
				 
				数据更新成功!<a href="enter.php?file=<?=$file?>&time=<?=$time?>&sign=<?=$sign?>" style="font-size:20px;	">返回</a>继续编辑
			</div>
			 
			
		</div>				
		</fieldset>
		 
		
	</div>
	  
  </body>
