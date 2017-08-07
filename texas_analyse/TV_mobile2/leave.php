<script type="text/javascript" src="../bootstrap/js/bootstrap.min.js"></script>
<link rel="stylesheet" type="text/css" href="../bootstrap/css/bootstrap.min.css" />
<?php
$data = file("announce.txt"); 
$obj = json_decode($data[0]); 
$hall = str_replace("|","\n",$obj->hall);
$room = $obj->room;
$match = $obj->match;
require_once '../include/database.class.php';
  
 /* $pdo = new DB();
  $db = $pdo->getDB();
  $sql = "select count(*) as cn from tb_user where reg_time>'2013-10-10 1:00:30'";
	$row = $db -> query($sql)-> fetch(PDO::FETCH_NUM);*/ 
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
