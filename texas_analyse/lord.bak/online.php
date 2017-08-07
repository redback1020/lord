<?php
require_once '../include/priv.php';
?>
<script type="text/javascript" src="../js/jquery.js"></script>
<link rel="stylesheet" type="text/css" href="../bootstrap/css/bootstrap.min.css" />
<script>
$(document).ready(function(){
	$("#platformId").val("<?=isset($_GET['tt'])?$_GET['tt']:'all'?>");
});
</script>
 <body>
  	<div class="container">
  
  	<?php
	    
	$data = file_get_contents('http://115.29.162.107/log/onlinePlayer.log');	 
	$obj = json_decode($data); 
	   
	foreach($obj as $val){
				 
				if(!empty($val->roomId)){
					if(!empty($val->tableId)){
						 
						$flag1++;
					} 
					$flag2++;
				}else{
					$flag3 ++;
					 
				}
	}
	?>
	<form method="get">
	<div>
	
		<fieldset>
		<legend>在线列表</legend>	
		<div class="row">
			<input type="hidden" id="time" name="time" value="<?=$_GET['time']?>">
			<input type="hidden" id="sign" name="sign" value="<?=$_GET['sign']?>"> 
			<div class="span2" style="width:700;">
				<label>牌桌类型：</label>
				<select class="span2" id="platformId" name="tt">
					<option value="all">全部</option>
					<option value="table">牌桌</option>
					<option value="room">房间</option>
					<option value="hall">大厅</option>
				</select>
				总在线:<?php echo (count($obj));?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
			牌桌:<?=$flag1?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
			房间:<?=$flag2?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
			大厅:<?=$flag3?>
			</div>
			
			<div span="span1" style="float:right;">
				<label>&nbsp;</label>
				<input type="submit" value="查&nbsp;&nbsp;询"  class="btn" />
			</div>
		</div>				
	</fieldset>
	</div>
	</form>
	<div>
		<table class="table table-bordered table-condensed table-hover" style="font-size:12px;">
			<tr class="info">			
				 
				<td width="10%"><strong>房间号</strong></td>
				<td width="10%"><strong>牌桌号</strong></td>
				<td width="10%"><strong>uid</strong></td>
				<td width="10%"><strong>游戏昵称</strong></td>
				<td width="10%"><strong>靓号</strong></td>
				<td width="10%"><strong>性别</strong></td> 
				<td width="10%"><strong>当前筹码</strong></td>  
				<td width="10%"><strong>当前乐币</strong></td>
				<td width="10%"><strong>等级</strong></td> 
				<td width="10%"><strong>exp</strong></td>
				<td width="10%"><strong>win</strong></td>
				<td width="10%"><strong>matches</strong></td>
				 
			</tr>
			<tbody id="inRoomUser">
			<?php
			   
			$par = "";getPri();
			foreach($obj as $val){
				$type = "";$dis = "";
				if(!empty($val->roomId)){
					if(!empty($val->tableId)){
						$type = "table";
						 
					}else{
						$type = "room";
						
					}
					 
				}else{
					 
					$type = "hall";
				}
				if(isset($_GET['tt'])){
					if($_GET['tt'] == "table"){
						if($type != "table")$dis="none";
					}elseif($_GET['tt'] == "room"){
						if($type == "hall")$dis="none";
					}elseif($_GET['tt'] == "hall"){
						if($type != "hall")$dis="none";
					}
				}
			?>
				<tr class="table-body" style="display:<?=$dis?>">
					 
					<td><?=(!empty($val->roomId))?($val->roomId):"";?></td>
					<td><?=(!empty($val->tableId))?$val->tableId:"";?></td>
					<td><a href="userinfo.php?uid=<?=$val->uid?>&<?=$par?>"><?=$val->uid?></a></td>
					<td nowrap><?=$val->nick?></td>
					<td><?=$val->cool_num?></td> 
					<td><?=$val->sex==1?"男":"女";?></td>
					<td><?=$val->coins?></td> 
					<td><?=$val->gold?></td>
					<td><?=$val->level?></td>
					
					<td><?=$val->exp?></td>
					<td><?=$val->gameData->win?></td>
					<td><?=$val->gameData->matches?></td>
					  
				</tr>
			<?php
				 
			
			}
			echo '</tbody> ';
			
			 		
			?>
			</tbody>
			<tr>
			<td>总计:</td>
			<td colspan="12">总在线:<?php echo (count($obj));?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
			牌桌:<?=$flag1?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
			房间:<?=$flag2?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
			大厅:<?=$flag3?>
				 </td>
			</tr>
		</table>
	</div>
	  
	</div>
  </body>
