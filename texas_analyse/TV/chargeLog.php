<?php
require_once '../include/priv.php';
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
	$.post("searchCharge.php",{
		pageSize: pageSize,
		pageIndex: index , 
		channel: channel , 
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
				dataListHtml += "<td><a href=\"userinfo.php?uid="+o.uid+"&<?=getPri()?>\">"+o.uid+"</a></td>"; 
				dataListHtml += "<td>"+o.value+"</td>";
				dataListHtml += "<td>"+o.money+"</td>";
				dataListHtml += "<td>"+o.channel+"</td>";
				dataListHtml += "<td>"+o.misc+"</td>";
				dataListHtml += "<td>"+o.add_time+"</td>";
				dataListHtml += "</tr>";
			}
			if(dataListHtml=="" && isNext){
				alert("已经是最后一页");
				pageIndex--;
			}else{
				$("#num").html(dataList.cn); 
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
	$sql = "SELECT `channel` FROM `charge_log` WHERE channel !='' GROUP BY `channel`";
	$pdo = new DB();
	$db = $pdo->getDB();
	$row = $db -> query($sql)-> fetchAll();
	?>
	
	<div>
		<fieldset>
		<legend>充值记录</legend>	
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
			<div class="span4" style="width:400px;">
				<label>时间：</label>
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
				<td width="10%"><strong>类型</strong></td>
				<td width="10%"><strong>用户ID</strong></td>
				<td width="15%"><strong>筹码/乐币值</strong></td>
				<td width="10%"><strong>金额</strong></td>
				<td width="15%"><strong>渠道号</strong></td>
				<td width="10%"><strong>充值来路</strong></td>
				<td width="20%"><strong>时间</strong></td>
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
			共<span id="count"></span>页 &nbsp;&nbsp;&nbsp;&nbsp;共计<span id="num"></span>条
		</td></tr>
		</table>	
	</div>
  </body>
