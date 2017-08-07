 <link rel="stylesheet" type="text/css" href="../bootstrap/css/bootstrap.min.css" />
<?php
error_reporting(E_ALL);
//require_once '../include/database.class.php';
$chips = $_REQUEST['chips'];
$chips = intval($chips/100)*100;
 
$week = date("w");
 
if ($chips >= 10*10000) {
	if ($week==0 || $week==5 || $week==6) {
		$chips+=25000;
	} else {
		$chips+=35000;
	}
} else if (($chips >= 8*10000) && ($chips < 10*10000)) {
	if ($week==0 || $week==5 || $week==6) {
		$chips+=10000;
	} else {
		$chips+=20000;
	}
}
 
/*$pdo = new DB();
$db = $pdo->getDB();
$sql = "SELECT uid FROM `game_user` WHERE uid NOT IN(SELECT uid from `game_match`) AND is_robot = 1";
$rows = $db -> query($sql)-> fetch();
$uids = array();
foreach($rows as $row) {
	array_push($uids,$row["uid"]);
}
if(count($uids) == 0){
	$str = "没有未参赛机器人!";
}
$uid = $uids[rand(0,(count($uids)-1))];
if ($uid > 0) {
	$sql = "INSERT INTO `game_match` (uid,coins,last_time) VALUES ($uid,$chips,NOW()) ON DUPLICATE KEY UPDATE coins=$chips,last_time = NOW();";
	$db->exec($sql);
	$str = "影响行数:".$db->lastInsertId()."  >0表示成功";
}else{
	$str = "未参赛机器人不存在!";
}*/
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
				 <?php
				 $str = system("/usr/local/php/bin/php /data/script/matchInsert.php ".$chips." ".$chips." 0",$out);
				$array = explode(" ",$str);
				 ?>
				  <a href="addData.php?time=<?=$time?>&sign=<?=$sign?>" style="font-size:20px;	">返回</a>继续添加
			</div>
			 
			
		</div>				
		</fieldset>
		 
		
	</div>
	  
  </body>
