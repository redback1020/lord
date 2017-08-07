<?php
require_once '../include/priv.php';
?>
<script src="../js/jquery.js"></script> 
<script src="../js/My97DatePicker/WdatePicker.js" language="javascript"></script>
<link rel="stylesheet" type="text/css" href="../bootstrap/css/bootstrap.min.css" />
<script>
$(function(){ 
	query();
	
});
 
var pageSize = 50;
var pageIndex = 0;

function query(){
	pageIndex = 0;
	queryByPage(pageIndex);
}

function prePage(){
	if(pageIndex==0){
		alert("已经是第一页");
		return;
	}
	pageIndex--;
	queryByPage(pageIndex);
}

function nextPage(){
	pageIndex++;
	queryByPage(pageIndex,true);
}

function queryByPage(index,isNext){
	var channel = $('#channel').val();
	var start = $('#start').val();
	var end = $('#end').val();
	var status = $('#status').val();
	$.post("searchGameCharge.php",{
		pageSize: pageSize,
		pageIndex: index,
		channel: channel , 
		status: status , 
		start: start , 
		end: end 		
	},function(result){ 
		if(result!=null && result!=""){
			var dataList=eval("("+result+")");  
			var dataListHtml = "";
			for(var i=0;i<dataList.data.length;i++){
				var o = dataList.data[i];
				dataListHtml += "<tr class='table-body'>";
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
				dataListHtml += "<td><a href=\"userinfo.php?uid="+o.uid+"&<?=getPri()?>\">"+o.uid+"</a></td>"; 
				
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
			if(dataListHtml=="" && isNext){
				alert("已经是最后一页");
				pageIndex--;
			}else{
				$("#count").html(Math.ceil(dataList.cn/pageSize)); 
				$("#dataList").html(dataListHtml); 
				$("#pageIndex").html(pageIndex+1);
				 
				$("#pagination").show();
			}
		}else{
			alert("获取数据失败！");
		}
	});
}


</script>
 
 <body>
  	<div class="container">
  
  	<?php
	require_once '../include/database.class.php';
	$sql = "SELECT `channel` FROM `game_charge` WHERE channel !='' GROUP BY `channel`";
	$pdo = new DB();
	$db = $pdo->getDB();
	$row = $db -> query($sql)-> fetchAll();
	?>
	
	<div>
		<fieldset>
		<legend>充值卡记录查询</legend>	
		<div class="row">
			 
			<div class="span2" >
				<label>渠道号：</label>
				<select class="span2" id="channel" >
					<option value="all">全部</option>
					<?php
					foreach($row as $val){
						echo '<option value="'.$val['channel'].'">'.$val['channel'].'</option>';
					}
					?>
					<option value="0">无渠道号</option>
				</select>
				
			</div>
			<div class="span2" >
				<label>状态：</label>
				<select class="span2" id="status" >
					<option value="all">全部</option>
					<option value="0">未完成</option>
					<option value="1">已完成</option>
					<option value="2">失败</option>
					 
				</select>
				
			</div>
			<div class="span4" style="width:400px;">
				<label>订单生成时间：</label>
				<input style="height:30px;" class="span3" type="text" id="start" class="textbox dtime" readonly onclick="WdatePicker({dateFmt:'yyyy-MM-dd HH:mm:ss'})"/>
				<input style="height:30px;" class="span3" type="text" id="end" class="textbox dtime" readonly onclick="WdatePicker({dateFmt:'yyyy-MM-dd HH:mm:ss'})"/>
			</div>
			<div span="span1" style="float:right;">
				<label>&nbsp;</label>
				<input type="button" value="查&nbsp;&nbsp;询" onclick="query()" class="btn" />
			</div>
		</div>	 			
	</fieldset>
	</div>

	<div>
		<table class="table table-bordered table-condensed table-hover" style="font-size:12px;">
			<tr class="info">			
				<td nowrap><strong>订单号</strong></td>
				<td nowrap><strong>类型</strong></td>
				<td nowrap><strong>充值卡类型</strong></td>
				<td nowrap><strong>用户ID</strong></td>
				<td nowrap><strong>乐币</strong></td>
				<td nowrap><strong>面额</strong></td>
				<td nowrap><strong>实际金额</strong></td>
				<td nowrap><strong>渠道号</strong></td>
				<td nowrap><strong>订单生成时间</strong></td>
				<td nowrap><strong>订单更新时间</strong></td>
				<td nowrap><strong>状态</strong></td> 
			</tr>
			<tbody id="dataList">
			 
			</tbody>
		</table>
	</div>
	<table width="920" border="0" cellpadding="5" cellspacing="0" align="center">
	<tr><td height="25" id="pagination" align="center" style="display:none;"> 
		<div class="btn-group">
		  <button class="btn" onclick="prePage()">前一页</button>
		  <span id="page">
		  <button class="btn" id="pageIndex"></button>
		  </span>
		  <button class="btn" onclick="nextPage()">后一页</button>
		  
		</div>
		共<span id="count"></span>页
	</td></tr>
	</table>
	
	</div>
  </body>
