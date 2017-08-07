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
	$.post("searchPay.php",{
		pageSize: pageSize,
		pageIndex: index,
		channel: channel , 
		status: status , 
		card_type: $('#card_type').val() , 
		data: $.trim($('#data').val()) , 
		min: $.trim($('#min').val()) , 
		max: $.trim($('#max').val()) , 
		start: start , 
		end: end 		
	},function(result){ 
		if(result!=null && result!=""){
			var dataList=eval("("+result+")");  
			var dataListHtml = "";var fee = 0.996; 
			for(var i=0;i<dataList.data.length;i++){
				var o = dataList.data[i];
				dataListHtml += "<tr class='table-body'>";
				
				 
				dataListHtml += "<td><a href=\"userinfo.php?uid="+o.uid+"&<?=getPri()?>\">"+o.uid+"</a></td>"; 
				
				dataListHtml += "<td>"+o.nick+"</td>";
				dataListHtml += "<td>"+o.cool_num+"</td>";
				dataListHtml += "<td>"+o.gold+"</td>"; 
				dataListHtml += "<td>"+o.gold+"</td>";
				dataListHtml += "<td>"+o.gold*fee+"</td>";
				dataListHtml += "<td>"+o.channel+"</td>";
				dataListHtml += "<td>"+o.time+"</td>"; 
			 
				var status = "<span style='color:green'>完成</span>"; 
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
				$("#pay_fee").html(dataList.num*0.996); 
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
	$sql = "select * from  lord_game_charge where channel != '' group by channel";
	$row = $db -> query($sql)-> fetchAll();
	?>
	
	<div>
		<fieldset>
		<legend>充值记录查询</legend>	
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
					 
				</select>
				
			</div>
			<div class="span2" >
				<label>状态：</label>
				<select class="span2" id="status" >
					<option value="all">全部</option>
					<option value="1">成功</option>
					 
					 
				</select>
				
			</div>
			<div class="span2" >
				<label>支付方式</label>
				 
				<select class="span2" id="card_type" >
					<option value="all">全部</option>
					 
					<option value="ALIPAY">支付宝</option> 
					 
				</select>
				
			</div>
			<div class="span2">
				<label>帐号/昵称/靓号：</label>
				<input class="span2" type="text" id="data" name="data" style="height:30px"/>
			</div>
			<div class="span1" style="width:90px;" >
				<label>充值总额</label>
				<input style="height:30px;width:80px;" class="span1" type="text" id="min"/>
				<input style="height:30px;width:80px;" class="span1" type="text" id="max"/>
			</div>
			<div class="span4">
				<label>充值时间：</label>
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
		<div>总记录数:<span id="num"></span>&nbsp;&nbsp;&nbsp;&nbsp;充值总额:<span id="pay_num"></span>&nbsp;&nbsp;&nbsp;&nbsp;总收益:<span id="pay_fee"></span></div>
		<table class="table table-bordered table-condensed table-hover" style="font-size:12px;">
			<tr class="info">			
				
				 
				<td nowrap><strong>用户ID</strong></td>
				<td nowrap><strong>用户昵称</strong></td>
				<td nowrap><strong>用户靓号</strong></td>
				<td nowrap><strong>乐币</strong></td> 
				<td nowrap><strong>充值金额</strong></td>
				<td nowrap><strong>收益</strong></td>
				<td nowrap><strong>渠道号</strong></td>
				<td nowrap><strong>充值时间</strong></td> 
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
