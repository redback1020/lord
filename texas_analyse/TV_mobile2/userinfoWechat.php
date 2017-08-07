<?php
require_once '../manage/checkPriv.php';
?>
<script type="text/javascript" src="../js/jquery.js"></script>
<link rel="stylesheet" type="text/css" href="../bootstrap/css/bootstrap.min.css" />
<script>
$(function(){ 
	if("<?=$_GET['uid']?>"){
		$('#type').val('uid')
		$('#data').val("<?=$_GET['uid']?>");
		query();
	}
});
function query(){
	$.ajax({
		url:'queryUser.php?type='+$('#type').val()+'&data='+$('#data').val(),  
		success:function(data){ 
			var jsonObj=eval("("+data+")");
			var wechat = jsonObj['wechat'];
			var charge = jsonObj['charge'];
			var game_charge = jsonObj['game_charge'];
			var uuid = jsonObj['uuid'];
			jsonObj = jsonObj['data'];
			
			if(jsonObj['code'] == 0){
				var o = jsonObj.data;
				var dataListHtml = "";
				 
				dataListHtml += "<tr>";
				dataListHtml += "<td width=\"13%\">用户ID:</td>";
				dataListHtml += "<td width=\"20%\">"+o.uid+"</td>";
				dataListHtml += "<td  width=\"13%\">靓号</td>";
				dataListHtml += "<td width=\"20%\">"+o.cool_num+"</td>";
				dataListHtml += "<td>昵称</td>";
				dataListHtml += "<td width=\"20%\">"+o.nick+"</td>";
				dataListHtml += "</tr><tr>";
				dataListHtml += "<td>乐币</td>";
				dataListHtml += "<td>"+o.gold+"</td>";
				dataListHtml += "<td>筹码</td>";
				dataListHtml += "<td>"+o.coins+"</td>";
				dataListHtml += "<td>锁定筹码</td>";
				dataListHtml += "<td>"+o.lock_coins+"</td>";
				dataListHtml += "</tr><tr>";
				dataListHtml += "<td>等级</td>";
				dataListHtml += "<td>"+o.level+"</td>";
				dataListHtml += "<td>VIP</td>";
				dataListHtml += "<td>"+o.vip_lv+"</td>";
				dataListHtml += "<td>渠道</td>";
				dataListHtml += "<td>"+o.channel+"</td>";
				dataListHtml += "</tr><tr>";
				dataListHtml += "<td>上次登陆</td>";
				dataListHtml += "<td>"+o.times.last+"</td>";
				dataListHtml += "<td>注册</td>";
				dataListHtml += "<td>"+o.times.reg+"</td>";
				dataListHtml += "<td>站点</td>";
				dataListHtml += "<td>"+(o.is_tv==1?"TV":"MP")+"</td>";
				dataListHtml += "</tr><tr>";
				dataListHtml += "<td>用户帐号</td>";
				dataListHtml += "<td colspan='6'>"+uuid+"</td>"; 
				 
				dataListHtml += "</tr>";
				$("#dataList").html(dataListHtml);
				
				if(wechat){
					var sex = "未知";
					if(o.wechat_sex==1) sex = "男";
					else if(o.wechat_sex==2) sex = "女"; 
					var dataListHtml = "";
					o = wechat;
					dataListHtml += "<tr>";
					dataListHtml += "<td width=\"13%\">uid:</td>";
					dataListHtml += "<td width=\"18%\">"+o.poker_id+"</td>";
					dataListHtml += "<td  width=\"13%\">加入公众号时间</td>";
					dataListHtml += "<td width=\"18%\">"+o.add_time+"</td>";
					dataListHtml += "<td>微信号与游戏号绑定时间</td>";
					dataListHtml += "<td width=\"18%\">"+o.binding_time+"</td>";
					dataListHtml += "</tr><tr>";
					dataListHtml += "<td>是否订阅</td>";
					dataListHtml += "<td>"+o.subscribe+"</td>";
					dataListHtml += "<td>fakeid</td>";
					dataListHtml += "<td>"+o.fakeid+"</td>";
					dataListHtml += "<td>微信上用户昵称</td>";
					dataListHtml += "<td>"+o.wechat_nickname+"</td>";
					dataListHtml += "</tr><tr>";
					dataListHtml += "<td>用户居住地</td>";
					dataListHtml += "<td>"+o.wechat_wherelive+"</td>";
					dataListHtml += "<td>性别</td>";
					dataListHtml += "<td>"+sex+"</td>";
					dataListHtml += "<td>私人微信号</td>";
					dataListHtml += "<td>"+o.wechat_username+"</td>";
					dataListHtml += "</tr><tr>";
					dataListHtml += "<td>用户资料</td>";
					dataListHtml += "<td>"+o.short_name=="undefined"?"":+o.short_name+"</td>";
					dataListHtml += "<td>&nbsp;</td>";
					dataListHtml += "<td>&nbsp;</td>";
					dataListHtml += "<td>&nbsp;</td>";
					dataListHtml += "<td>&nbsp;</td>";
				 
					dataListHtml += "</tr>";
					$("#wechat").html(dataListHtml);
				}else{
					$("#wechat").html('<tr><td>暂无记录!</td></tr>');
				}
				
				if(charge!=''){
					dataListHtml = "";
					dataListHtml += "<tr>";
					dataListHtml += "<td width=\"15%\"><strong>uid</strong></td>";
					dataListHtml += "<td width=\"15%\"><strong>类型</strong></td>";
					dataListHtml += "<td width=\"20%\"><strong>筹码/金币值</strong></td>";
					dataListHtml += "<td width=\"15%\"><strong>渠道号</strong></td>";
					dataListHtml += "<td width=\"15%\"><strong>充值来路</strong></td>";
					dataListHtml += "<td width=\"20%\"><strong>时间</strong></td>";
					dataListHtml += "</tr>";
					for(var i in charge){
						o = charge[i];
						dataListHtml += "<tr>";
						dataListHtml += "<td>"+o.uid+"</td>"; 
						dataListHtml += "<td>"+o.type+"</td>"; 
						dataListHtml += "<td>"+o.value+"</td>"; 
						dataListHtml += "<td>"+o.channel+"</td>"; 
						dataListHtml += "<td>"+o.misc+"</td>"; 
						dataListHtml += "<td>"+o.add_time+"</td>"; 
						dataListHtml += "</tr>";
					}
					
					$("#charge").html(dataListHtml);
				}else{
					$("#charge").html('<tr><td>暂无记录!</td></tr>');

				}
				
				if(game_charge!=''){
					dataListHtml = "";
					dataListHtml += "<tr>";
					dataListHtml += "<td><strong>uid</strong></td>";
					dataListHtml += "<td><strong>订单号</strong></td>";
					dataListHtml += "<td><strong>类型</strong></td>";
					dataListHtml += "<td><strong>充值卡类型</strong></td>";
					dataListHtml += "<td><strong>乐币</strong></td>";
					dataListHtml += "<td><strong>面额</strong></td>";
					dataListHtml += "<td><strong>实际金额</strong></td>";
					dataListHtml += "<td><strong>渠道号</strong></td>";
					dataListHtml += "<td><strong>订单生成时间</strong></td>";
					dataListHtml += "<td><strong>订单更新时间</strong></td>";
					dataListHtml += "<td><strong>状态</strong></td>";
					dataListHtml += "</tr>";
					for(var i in game_charge){
						var o = game_charge[i];
						dataListHtml += "<tr class='table-body'>";
						dataListHtml += "<td>"+o.uid+"</td>"; 
						dataListHtml += "<td>"+o.id+"</td>";
						dataListHtml += "<td>"+(o.type=="GOLD"?'乐币':'筹码')+"</td>";
						var card_type = "";
						switch(o.card_type) {
							case "JUNNET":card_type = "骏卡";break;
							case "SNDACARD":card_type = "盛大卡";break;
							case "ZHENGTU":card_type = "征途卡";break;
							case "QQCARD":card_type = "Q币卡";break;
							case "NETEASE":card_type = "网易卡";break;
							case "SZX":card_type = "神州行";break;
							case "UNICOM":card_type = "联通卡";break;
							case "TELECOM":card_type = "电信卡";break;
							case "TIANXIA":card_type = "天下一卡通";break;
						}
						dataListHtml += "<td>"+card_type+"</td>";
						
						dataListHtml += "<td>"+o.value+"</td>";
						dataListHtml += "<td>"+o.money+"</td>";
						dataListHtml += "<td>"+o.in_money+"</td>";
						dataListHtml += "<td>"+o.channel+"</td>";
						dataListHtml += "<td>"+o.add_time+"</td>";
						dataListHtml += "<td>"+o.last_time+"</td>";
						if(o.sts ==0)var status = "未完成";
						else if(o.sts == 1)var status = "完成";
						else if(o.sts == 2)var status = "失败";
						dataListHtml += "<td>"+status+"</td>"; 
						dataListHtml += "</tr>";
					}
					
					$("#game_charge").html(dataListHtml);
				}else{
					$("#game_charge").html('<tr><td>暂无记录!</td></tr>');

				}
			}else{
				alert(jsonObj['msg']);
			}
		}
	});
}
</script>
 <body>
  	<div class="container">
  
  	
	
	<div>
		<fieldset>
		<legend>用户的详细信息</legend>	
		 		<input type="hidden" id="data">
		 		<input type="hidden" id="type">
	</fieldset>
	</div>
	
	<div>用户基础信息
		<table class="table table-bordered table-condensed table-hover">
			 
			<tbody id="dataList">
			</tbody>
		</table>
	</div>
	<div>用户微信信息
		<table class="table table-bordered table-condensed table-hover">
			 
			<tbody id="wechat">
			</tbody>
		</table>
	</div>
	<div>用户充值记录<br/>
	1.淘宝充值
		<table class="table table-bordered table-condensed table-hover">
			 
			<tbody id="charge">
			</tbody>
		</table>
	2.点卡充值
		<table class="table table-bordered table-condensed table-hover">
			 
			<tbody id="game_charge">
			</tbody>
		</table>
	</div>	
	<?php
	$time = time();
	$key = "qwe!@#321";
	$sign = md5($key.$time);
	?>
		<div><a href="userinfo.php?time=<?=$time?>&sign=<?=$sign?>">返回</a>用户信息列表</div>
	</div>
  </body>
