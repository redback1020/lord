<?php
require_once '../include/priv.php';
require_once './global.php';
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
	
	var start = $('#start').val();
	var end = $('#end').val();
	var status = $('#status').val();
	$.post("searchPay.php",{
		pageSize: pageSize,
		pageIndex: index,
		channel: "<?=$adm_username?>" , 
		status: status , 
		card_type: $('#card_type').val() , 
		data: '' , 
		min: $.trim($('#min').val()) , 
		max: $.trim($('#max').val()) , 
		start: start , 
		end: end 		
	},function(result){ 
		if(result!=null && result!=""){
			var dataList=eval("("+result+")");  
			var dataListHtml = "";
			for(var i=0;i<dataList.data.length;i++){
				var o = dataList.data[i];
				dataListHtml += "<tr class='table-body'>";
				
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
					case "XIAOMI":card_type = "小米";break;
					case "ALIPAY":card_type = "阿里";break;
					case "YEEPAY":card_type = "易宝网银";break;
					case "taobao":card_type = "淘宝";break; 
				}
				dataListHtml += "<td>"+card_type+"</td>";
			 
				dataListHtml += "<td>"+o.value+"</td>"; 
				dataListHtml += "<td>"+o.in_money+"</td>";
				dataListHtml += "<td>"+o.channels+"</td>";
				dataListHtml += "<td>"+o.add_time+"</td>";
				dataListHtml += "<td>"+o.last_time+"</td>";
				if(o.sts ==0)var status = "未完成";
				else if(o.sts == 1)var status = "<span style='color:green'>完成</span>";
				else if(o.sts == 2)var status = "<span style='color:red'>失败</span>";
				dataListHtml += "<td>"+status+"</td>"; 
				dataListHtml += "</tr>";
			}
			if(dataListHtml=="" && isNext){
				alert("已经是最后一页");
				pageIndex--;
			}else{
				$("#count").html(Math.ceil(dataList.cn/pageSize)); 
				$("#num").html(dataList.cn); 
				$("#pay_num").html(dataList.num); 
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
  	<div class="">
  
  	<?php
	require_once '../include/database.class.php';
	$sql = "select * from (SELECT `channel` FROM `mobile_charge` union  select channel from mobile_charge_log  GROUP BY `channel`)a where channel != '' ";
	$pdo = new DB();
	$db = $pdo->getDB();
	$row = $db -> query($sql)-> fetchAll();
	?>
	
	<div>
		<fieldset>
		<legend>充值记录查询</legend>	
		<div class="row">
			 
			 
			<div class="span2" >
				<label>状态：</label>
				<select class="span2" id="status" >
					<option value="all">全部</option>
					<option value="0">未完成</option>
					<option value="1">已完成</option>
					<option value="2">失败</option>
					 
				</select>
				
			</div>
			<div class="span2" >
				<label>支付方式</label>
				 
				<select class="span2" id="card_type" >
					<option value="all">全部</option>
					<option value="JUNNET">骏卡</option>
					<option value="SNDACARD">盛大卡</option>
					<option value="ZHENGTU">征途卡</option>
					<option value="QQCARD">Q币卡</option>
					<option value="NETEASE">网易卡</option>
					<option value="SZX">神州行</option>
					<option value="UNICOM">联通卡</option>
					<option value="TELECOM">电信卡</option>
					<option value="TIANXIA">天下一卡通</option>
					<option value="XIAOMI">小米</option>
					<option value="ALIPAY">阿里</option>
					<option value="YEEPAY">易宝网银</option> 
					<option value="taobao">淘宝</option> 
					 
				</select>
				
			</div>
		 
			<div class="span1" style="width:90px;" >
				<label>充值总额</label>
				<input style="height:30px;width:80px;" class="span1" type="text" id="min"/>
				<input style="height:30px;width:80px;" class="span1" type="text" id="max"/>
			</div>
			<div class="span4">
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
		<div>总记录数:<span id="num"></span>&nbsp;&nbsp;&nbsp;&nbsp;充值总额:<span id="pay_num"></span></div>
		<table class="table table-bordered table-condensed table-hover" style="font-size:12px;">
			<tr class="info">			
				
				<td nowrap><strong>类型</strong></td>
				<td nowrap><strong>支付方式</strong></td>
				 
				<td nowrap><strong>筹码/乐币</strong></td> 
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
