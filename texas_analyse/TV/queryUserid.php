<?php

require_once '../include/database.class.php';
$pdo = new DB();
$db = $pdo->getDB();
$sql = "SELECT * FROM `game_user` WHERE nick = '".$_REQUEST['nick']."'"; 
$row = $db -> query($sql)-> fetchAll();
 $flag = 0;
foreach($row as $key =>$row){
$flag++;
?>
<tr>
	<td><?=$row['uid']?></td> 
	<td><?=$row['nick']?></td>
	<td><?=$row['gold']?></td>
	<td><?=$row['coins']?></td>
	<td><?=$row['offline_gold']?></td>
	<td><?=$row['offline_coins']?></td>
	<td><?=$row['fruit_free']?></td>
	<td><?=$row['vip_lv']?></td>
	<td><?=$row['level']?></td>
	<td><?=$row['channel']?></td> 
</tr>
<?php
};
if($flag==0){
echo '<tr><td colspan="10">暂无数据!</td></tr>';
}
?>
  