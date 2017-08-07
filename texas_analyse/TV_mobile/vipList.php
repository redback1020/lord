<?php
require_once '../include/priv.php';
?>
<script type="text/javascript" src="../js/jquery.js"></script>
<link rel="stylesheet" type="text/css" href="../bootstrap/css/bootstrap.min.css" />
<script>
$(function(){ 
	if("<?=isset($_GET['uid'])?>"){
		$('#type').val('uid')
		$('#data').val("<?=$_GET['uid']?>");
		
	}
	queryByPage(0);
});
var pageSize = 50;
var pageIndex = 0;
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
	$.ajax({
		url:'queryVip.php?type='+$('#type').val()+'&data='+$('#data').val()+"&pageIndex="+index+"&pageSize="+pageSize,  
		success:function(data){ 
			$("#dataList").html("");
			var dataList=eval("("+data+")");  
			var dataListHtml = "";
			for(var i=0;i<dataList.data.length;i++){
					var o = dataList.data[i]; 
					 
					dataListHtml += "<tr>"; 
					dataListHtml += "<td>"+o.uuid+"</td>";
					 
					dataListHtml += "<td>"+o.nick+"</td>"; 
					 			
					dataListHtml += "<td>"+o.cool_num+"</td>";
					dataListHtml += "<td>"+o.diff+"</td>";  
					dataListHtml += "<td>"+o.start+"</td>";	
					dataListHtml += "<td>"+o.end+"</td>";
					
					dataListHtml += "</tr>";
				}
 			
			$("#dataList").html(dataListHtml);
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
	});
}
</script>
 <body>
  	<div class="container">
  
  	
	
	<div>
		<fieldset>
		<legend>vip用户列表</legend>	
		<div class="row">
			<div class="span2">
				<label>站点：</label>
				<select class="span2" id="type" >
					<option value="all">全部</option>
					<option value="1">TV</option>
					<option value="0">MP</option> 
				</select>
			</div>
			<div class="span2">
				<label>帐号/昵称/靓号：</label>
				<input class="span2" type="text" id="data" name="data" style="height:30px"/>
			</div>
			 
			<div span="span1" style="float:right;">
				<label>&nbsp;</label>
				<input type="button" value="查&nbsp;&nbsp;询" onclick="queryByPage(0)" class="btn" />
			</div>
		</div>				
	</fieldset>
	</div>
	<?php
	
	?>
	<div>VIP用户信息
		<table class="table table-bordered table-condensed table-hover">
			<tr class="info">			
				<td nowrap><strong>用户帐号</strong></td> 
				<td nowrap><strong>昵称</strong></td>
				<td nowrap><strong>靓号</strong></td>
				<td nowrap><strong>剩余时长(天)</strong></td>
				<td nowrap><strong>VIP开始时间</strong></td>
				<td nowrap><strong>VIP结束时间</strong></td>
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
