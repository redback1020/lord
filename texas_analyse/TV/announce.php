<?php
require_once '../include/priv.php';
?>
<script type="text/javascript" src="../bootstrap/js/bootstrap.min.js"></script>
<link rel="stylesheet" type="text/css" href="../bootstrap/css/bootstrap.min.css" />
<?php
$sysini = "../include/sys.ini";  //系统的配置文件
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
$data = file_get_contents($url); 
$obj = json_decode($data); 
$hall = str_replace("|","\n",$obj->hall);
$room = $obj->room;
$match = $obj->match;
 
?>
 <body>
	<form action="saveAnnounce.php" method="post">
  	<div class="container">
  	
	<div>
		<fieldset>
		<legend>大厅公告</legend>	
		<div class="row">
			<div class="span2">
				 
				<textarea rows="10" col="15" id="hall" name="hall" style="width:800px;"><?=$hall?></textarea>
			</div>
			 
			
		</div>				
		</fieldset>
		<fieldset>
		<legend>普通牌桌公告</legend>	
		<div class="row">
			<div class="span2">
				 
				<input type="text" id="room" name="room" style="width:800px;height:35px;" value="<?=$room?>">
			</div>
			 
			 
		</div>				
		<fieldset>
		<legend>争霸赛牌桌公告</legend>	
		<div class="row">
			<div class="span2">
				 
				<input type="text" id="match" name="match" style="width:800px;height:35px;" value="<?=$match?>">
			</div>
			 
			 
		</div>				
		</fieldset>
		
	</div>
	<div span="span1" style="float:left;">
			<label>&nbsp;</label>
			<input type="submit" value="保&nbsp;&nbsp;存" onclick="query()" class="btn" />
		</div>
	 
	 
	
	</div>
	</form>
  </body>
