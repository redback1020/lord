<?php
require_once '../manage/checkPriv.php';
?>
<script type="text/javascript" src="../js/jquery.js"></script>
<link rel="stylesheet" type="text/css" href="../bootstrap/css/bootstrap.min.css" />
<script>
function showLayer(layer){
	if(layer == "inRoomUser"){
		$('#inRoomUser').show();
		$('#inRoomRobot').hide();
		$('#hall').hide();
	}else if(layer == "inRoomRobot"){
		$('#inRoomUser').hide();
		$('#inRoomRobot').show();
		$('#hall').hide();
	}else if(layer == "hall"){
		$('#inRoomUser').hide();
		$('#inRoomRobot').hide();
		$('#hall').show();
	}else {
		$('#inRoomUser').show();
		$('#inRoomRobot').show();
		$('#hall').show();
	}
}
</script>
 <body>
  	<div class="container">
  
  	<?php
	require_once 'curl.php'; 
	//$data = file_get_contents("onlines.txt");   
	$obj = fetch_page('http://180.150.178.175:8200/onlines',array('sign'=>'jlfsd87912hjk312h90f!@fsjdkl!23','count'=>1));
	 
	//$obj = json_decode($data); 
	$obj = $obj['data'];
	$inRoomUser = $obj['inRoomUser'];
	$inRoomRobot = $obj['inRoomRobot'];
	$hall = $obj['hall']; 
	$flag1 = $flag2 = $flag3 = 0;
	$obj = fetch_page('http://180.150.178.175:8200/onlines',array('sign'=>'jlfsd87912hjk312h90f!@fsjdkl!23'));
		 
	$datas = $obj['data']; // print_r($datas['inRoomUser']); 
	foreach($datas['inRoomUser'] as $val){
		if($val['isMatch'])$flag1++;
		else $flag2++;
	}
	?>
	
	<div>
		<fieldset>
		<legend>在线列表</legend>	
		<div class="row">
			 
			<div class="span2" style="width:700;">
				<label>牌桌类型：</label>
				<select class="span2" id="platformId" >
					<option value="all">全部</option>
					<option value="inRoomUser">普通牌桌</option>
					<option value="inRoomRobot">争霸赛牌桌</option>
					<option value="hall">大厅</option>
				</select>
				总在线:<?php echo ($inRoomUser+$hall);?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
				大厅:<?=$hall?>&nbsp;&nbsp;争霸赛牌桌:<?=$flag1?>&nbsp;&nbsp;普通牌桌<?=$flag2?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
				机器人数量:<?=$inRoomRobot?>
			</div>
			
			<div span="span1" style="float:right;">
				<label>&nbsp;</label>
				<input type="button" value="查&nbsp;&nbsp;询" onclick="showLayer($('#platformId').val())" class="btn" />
			</div>
		</div>				
	</fieldset>
	</div>

	<div>
		<table class="table table-bordered table-condensed table-hover" style="font-size:12px;">
			<tr class="info">			
				<td width="10%"><strong>类型</strong></td>
				<td width="10%"><strong>uid</strong></td>
				<td width="10%"><strong>游戏昵称</strong></td>
				<td width="10%"><strong>靓号</strong></td>
				<td width="10%"><strong>当前体验币</strong></td> 
				<td width="10%"><strong>当前筹码</strong></td>
				<td width="10%"><strong>类型</strong></td>
				<td width="10%"><strong> 锁定筹码/体验币</strong></td>
				<td width="10%"><strong>当前乐币</strong></td>
				<td width="10%"><strong>等级</strong></td>
				<td width="10%"><strong>vip</strong></td>
				<td width="10%"><strong>头像</strong></td> 
				<td width="10%"><strong>play</strong></td>
				<td width="10%"><strong>win</strong></td>
				<td width="10%"><strong>room</strong></td>
			</tr>
			<tbody id="inRoomUser">
			<?php
			  
			//$obj = fetch_page('http://180.150.178.175:8000/onlines',array('sign'=>'jlfsd87912hjk312h90f!@fsjdkl!23'));
			 
			//$obj = json_decode($data); 
			 
			//$code = $obj['code'];
			//$datas = $obj['data'];
			//var_dump($datas['inRoomUser);die;
		//	$flag1 = $flag2 = $flag3 = 0;
		//	$par = getPri();
			$room = array();
			foreach ($datas['inRoomUser'] as $user) {
				$room[] = $user['room'];
			}
			array_multisort($room, SORT_ASC, $datas['inRoomUser']);
			foreach($datas['inRoomUser'] as $val){
				if(!$val['isMatch']){
			?>
				<tr class="table-body" >
					<td nowrap>普通牌桌</td>
					<td><a href="userinfo.php?uid=<?=$val['uid']?>"><?=$val['uid']?></a></td>
					<td nowrap><?=$val['nick']?></td>
					<td><?=$val['coolNum']?></td>
					<td><?=$val['trialCoins']?></td> 
					<td><?=$val['coins']?></td>
					<td><?php
						if($val['isTrial'])echo "体验场";
						else if(!$val['isTrial']&&!$val['isMatch'])echo "筹码场";
					?></td>
					<td><?=$val['lockCoins']?></td>
					<td><?=$val['gold']?></td>
					<td><?=$val['level']?></td>
					<td><?=$val['vip']?></td>
					<td><?=$val['avatar']?></td>
					
					<td><?=$val['play']?></td>
					<td><?=$val['win']?></td>
					<td><?=$val['room']?></td>
				</tr>
			<?php
				}
			
			}
			echo '</tbody><tbody id="inRoomRobot">';
			
			
			foreach($datas['inRoomUser'] as $val){
				if($val['isMatch']){
			?>
				<tr class="table-body">
					<td nowrap>争霸赛牌桌</td>
					<td><a href="userinfo.php?uid=<?=$val['uid']?>"><?=$val['uid']?></a></td>
					<td nowrap><?=$val['nick']?></td>
					<td><?=$val['coolNum']?></td>
					<td><?=$val['trialCoins']?></td> 
					<td><?=$val['coins']?></td>
					<td><?php
						if($val['isTrial'])echo "体验场";
						else if($val['isTrial']&&!$val['isMatch'])echo "筹码场";
					?></td>
					<td><?=$val['lockCoins']?></td>
					<td><?=$val['gold']?></td>
					<td><?=$val['level']?></td>
					<td><?=$val['vip']?></td>
					<td><?=$val['avatar']?></td>
					
					<td><?=$val['play']?></td>
					<td><?=$val['win']?></td>
					<td><?=$val['room']?></td>
				</tr>
			<?php
			 
				}
			}
			echo '</tbody><tbody id="hall">';
			foreach($datas['hall'] as $val){
			?>
				<tr class="table-body">
					<td nowrap>大厅</td>
					<td><a href="userinfo.php?uid=<?=$val['uid']?>"><?=$val['uid']?></a></td>
					<td nowrap><?=$val['nick']?></td>
					<td><?=$val['coolNum']?></td>
					<td><?=$val['trialCoins']?></td> 
					<td><?=$val['coins']?></td>
					<td><?php
						if($val['is_trial'] == 1)echo "体验场";
						else if($val['is_trial'] == 0 &&$val['is_match']==0 )echo "筹码场";
					?></td>
					<td><?=$val['lockCoins']?></td>
					<td><?=$val['gold']?></td>
					<td><?=$val['level']?></td>
					<td><?=$val['vip']?></td>
					<td><?=$val['avatar']?></td>
					
					<td><?=$val['play']?></td>
					<td><?=$val['win']?></td>
					<td><?=$val['room']?></td>
				</tr>
			<?php
			 
			}			
			?>
			</tbody>
			<tr>
			<td>总计:</td>
			<td colspan="12">总在线:<?php echo ($inRoomUser+$hall);?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
				大厅:<?=$hall?>&nbsp;&nbsp;争霸赛牌桌:<?=$flag1?>&nbsp;&nbsp;普通牌桌<?=$flag2?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
				机器人数量:<?=$inRoomRobot?></td>
			</tr>
		</table>
	</div>
	  
	</div>
  </body>
