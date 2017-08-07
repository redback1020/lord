<?php
 
require_once '../include/database.class.php';
header("Content-Type: application/vnd.ms-execl");
	header("Content-Disposition: attachment; filename=myExcel.xls");
	header("Pragma: no-cache");
	header("Expires: 0");
$sql = "select m.uuid,a.add_time  from mobile_analyse a,mobile_user u,user_user m where a.uid = u.uid and u.uid = m.id and LENGTH(m.uuid)<15 and  u.is_robot=0";
$pdo = new DB();
$db = $pdo->getDB();
$row = $db -> query($sql)-> fetchAll();
?>
 
  
		<table class="table table-bordered table-condensed table-hover">
			<tr class="info">			
				<td nowrap><strong>account</strong></td> 
				<td nowrap><strong>add_time</strong></td> 
				 
			</tr> 
			<?php
			foreach($row as $val){
			?>
			<tr class="info">			
				<td nowrap><?=$val['uuid']?></td> 
				<td nowrap><?=$val['add_time']?></td> 
				 
			</tr> 
			<?php
			}
			?>
		</table>
	 
  </body>
