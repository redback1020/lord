<link rel="stylesheet" type="text/css" href="../bootstrap/css/bootstrap.min.css" />
<?php

/*$obj = json_decode($data[0]); 
$hall = str_replace("|","\n",$obj->hall);
$room = $obj->room;
$match = $obj->match;*/
$file=$_GET['file'];
$key = $_GET['key'];
$sysini = "../include/sys_mobile.ini";  //系统的配置文件
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
$data = file_get_contents($url);  
$array = explode(",",$data);
foreach($array as $k => $val){
	if(substr_count($key.",",$k.',')>0){
		unset($array[$k]); 
	}
}
 

$str = implode(",",$array); 
$fp =  fopen($url,"w"); 
$p = fwrite($fp,$str);
fclose($fp);

?>
 <body>
	 
  	<div class="container">
  	
	<div>
		<fieldset>
		<legend>系统提示</legend>	
		<div class="">
			<div class="">
				 
				数据删除成功!<a href="enter.php?file=<?=$file?>" style="font-size:20px;	">返回</a>继续操作!
			</div>
			 
			
		</div>				
		</fieldset>
		 
		
	</div>
	  
  </body>
