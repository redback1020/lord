<?php
 $time = time();
	$key = "qwe!@#321";
	$sign = md5($key.$time); 
	$date = $_GET['date'];
	$year  = substr($date, 0, 4);
	$month = substr($date, 4, 2);
	$day   = substr($date, 6, 2);
	
	$file = "/data/log/match/match.$date.json";
	$content = file_get_contents($file);
	
	$array = json_decode($content, true);
			 
	foreach($array as $key=>$val){
	?>
		<tr class="table-body" >
			 
			<td><?=++$key?></td>
			<td><a href="userinfo.php?uid=<?=$val['uid']?>&time=<?=$time?>&sign=<?=$sign?>"><?=$val['uid']?></a></td>
			<td><?php if($val['r']==0)echo '<img src="../bootstrap/images/man.jpg">';?><?=$val['nick']?></td>
			<td ><?=$val['level']?></td>
			<td><?=$val['coins']?></td>
			 
		</tr>
	<?php
	}
?>