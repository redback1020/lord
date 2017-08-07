<?php
require_once '../include/priv.php';
require_once './global.php';
?>
<script type="text/javascript" src="../js/jquery.js"></script>
<script src="../js/My97DatePicker/WdatePicker.js" language="javascript"></script>
<link rel="stylesheet" type="text/css" href="../bootstrap/css/bootstrap.min.css" />
<script>
$(function(){ 
	 
	query();
});
var pageSize = 50;
var pageIndex = 0;
 
function query(a,str){
 
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
	$.post("searchChannelLog.php",{
		pageSize: pageSize,
		pageIndex: index, 
		status: status , 
		start: start , 
		end: end,
		sign:'<?=$sign?>',
		time:'<?=$time?>'
	},function(result){ 
		if(result!=null && result!=""){
			if(result == "-1"){alert("您已超时!");location.href="http://www.youjoy.com/admin/logout.action";return false;}
			else if(result =="-2"){alert("暂无权限!");location.href="http://www.youjoy.com/admin/logout.action";return false;}
			else{
				var dataList=eval("("+result+")");  
				var dataListHtml = "";
				for(var i=0;i<dataList.data.length;i++){
					var o = dataList.data[i];
					dataListHtml += "<tr class='table-body'>";
					 
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
					  
					dataListHtml += "<td>"+o.money+"</td>";
					dataListHtml += "<td>"+o.channel+"</td>";
					dataListHtml += "<td>"+o.add_time+"</td>"; 
				//	if(o.sts ==0)var status = "未完成";
				//	else if(o.sts == 1)var status = "完成";
				//	else if(o.sts == 2)var status = "失败";
				//	dataListHtml += "<td>"+status+"</td>"; 
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
			}
		}else{
			alert("获取数据失败！");
		}
	});
}
 
</script>
 <body>
  	<div class="container">
  
  	
	
	<div>
		<fieldset>
		<legend>用户充值记录 >> <?=$adm_username?> >> (试用版)</legend>	
		<div class="row">
			<!--<div class="span2" >
				<label>状态：</label>
				<select class="span2" id="status" >
					<option value="all">全部</option>
					<option value="0">未完成</option>
					<option value="1">已完成</option>
					<option value="2">失败</option>
					 
				</select>
				
			</div>  -->
			<div class="span4" style="width:400px;">
				<label>订单完成时间：</label>
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
	<?php
	
	?>
	<div>
		<table class="table table-bordered table-condensed table-hover">
			<tr class="info">			
				 
				<td nowrap><strong>实际金额</strong></td> 
				<td nowrap><strong>渠道名称</strong></td> 
				<td nowrap><strong>订单完成时间</strong></td> 
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
