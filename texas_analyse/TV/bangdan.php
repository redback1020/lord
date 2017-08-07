<?php
ini_set("display_errors","on");
require_once '../include/priv.php';
?>
<script type="text/javascript" src="../js/jquery.js"></script>
<link rel="stylesheet" type="text/css" href="../bootstrap/css/bootstrap.min.css" />
<script>
 
</script>
 <body>
  	<div class="container">
  
  	
	
	<div>
		<fieldset>
		<legend>争霸赛实时排名榜单</legend>	
		<div class="row">
			 
			
			 
		</div>				
	</fieldset>
	</div>

	<div>
	<p>注:有头像的为真实用户</p>
		<table class="table table-bordered table-condensed table-hover" style="font-size:12px;">
			<tr class="info">			
				<td width="10%"><strong>编号</strong></td>
				<td width="10%"><strong>uid</strong></td>
				<td width="10%"><strong>nick</strong></td>
				<td width="10%"><strong>等级</strong></td>
				<td width="20%"><strong>筹码</strong></td>
				 
			</tr>
			<tbody id="inRoomUser">
			<?php
			  
			$file = "/data/log/match/match.json";
			$content = file_get_contents($file);  
			$array = json_decode($content);
			foreach($array as $key=>$val){
			?>
				<tr class="table-body" >
					 
					<td><?=++$key?></td> 
					<td><a href="userinfo.php?uid=<?=$val->uid?>&<?=getPri()?>"><?=$val->uid?></a></td>

					<td><?php if($val->r==0)echo '<img src="../bootstrap/images/man.jpg">';?><?=$val->nick?></td>
					<td ><?=$val->level?></td>
					<td><?=$val->coins?></td>
					 
				</tr>
			<?php
			}
			?>
			</tbody>
		</table>
	</div>
	  
	</div>
  </body>
