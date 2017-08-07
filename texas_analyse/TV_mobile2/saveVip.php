 <link rel="stylesheet" type="text/css" href="../bootstrap/css/bootstrap.min.css" />
<?php

require_once '../include/database.class.php';

$uid = $_POST['uid'];
$start = $_POST['start'];
$end = $_POST['end']; 
$sql = "select * from mobile_gold_log where uid = '".$uid."' and type='GIVE' and misc = 'vip'"; 
$row = $db ->query($sql) -> fetch(); 
if($row){
 
	$sql = "update mobile_gold_log set add_time= '".$start."',channel = '".$end."' where id= '".$row['id']."'";
	$db -> query($sql);
	 
}else{
 
	$sql = "insert into mobile_gold_log (type,uid,channel,misc,add_time) values('GIVE','".$uid."','".$end."','vip','".$start."')";
	 
	$db -> query($sql);
}
$sql = "select * from mobile_user where uid = '".$uid."'";
$res = $db -> query($sql) -> fetch();$where = "";
if($res['exp_lv'] ==0){$where = ",vip_lv=1";}
$sql = "update mobile_user set vip_exp = '".$end."'".$where." where uid = '".$uid."'";$db -> query($sql); 
 // echo $sql;
  
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
				  
				  <a href="vipList.php?time=<?=$time?>&sign=<?=$sign?>&uid=<?=$uid?>" style="font-size:20px;	">返回</a>继续查看
			</div>
			 
			
		</div>				
		</fieldset>
		 
		
	</div>
	  
  </body>
