<?php
require_once '../include/priv.php';
?>
<script type="text/javascript" src="../js/jquery.js"></script>
<link rel="stylesheet" type="text/css" href="../bootstrap/css/bootstrap.min.css" />
<script>
function query(){
	$.ajax({
		url:'queryUserid.php?nick='+$('#nick').val(),  
		success:function(data){ 
			$("#dataList").html(data);
		}
	});
}
</script>
 <body>
  	<div class="container">
  
  	
	
	<div>
		<fieldset>
		<legend>查询用户的uid</legend>	
		<div class="row">
			<div class="span2">
				<label>用户nick：</label>
				<input class="span2" type="text" id="nick" style="height:30px;"/>
			</div>
			
			<div span="span1" style="float:right;">
				<label>&nbsp;</label>
				<input type="button" value="查&nbsp;&nbsp;询" onclick="query()" class="btn" />
			</div>
		</div>				
	</fieldset>
	</div>
	<?php
	
	?>
	<div>
		<table class="table table-bordered table-condensed table-hover" style="font-size:12px;">
			<tr class="info">
				<td><strong>uid</strong></td> 
				<td><strong>游戏昵称</strong></td>
				<td><strong>当前金币</strong></td>
				<td><strong>当前筹码</strong></td>
				<td><strong>未领取的金币数</strong></td>
				<td><strong>未领取的筹码数</strong></td>
				<td><strong>未使用的水果机次数</strong></td>
				<td><strong>VIP等级</strong></td>
				<td><strong>经验等级</strong></td>
				<td><strong>用户来源</strong></td> 
			</tr>
			<tbody id="dataList">
			</tbody>
		</table>
	</div>
	 
	
	</div>
  </body>
