<?php
require_once '../include/priv.php';
?>
<script type="text/javascript" src="../js/jquery.js"></script>
<link rel="stylesheet" type="text/css" href="../bootstrap/css/bootstrap.min.css" />
<script>
$(function(){ 
	if("<?=$_GET['uid']?>"){
		$('#type').val('uid')
		$('#data').val("<?=$_GET['uid']?>");
		
	}
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
	$.ajax({
		url:'searchChargeChange.php',  
		data:{
			pageSize: pageSize,
			pageIndex: index,
			type:$('#type').val(),
			data:$('#data').val()
		},
		success:function(data){ 
			var dataList=eval("("+data+")");
			  
				var dataListHtml = "";
				for(var i=0;i<dataList.data.length;i++){
					var o = dataList.data[i]; 
					dataListHtml += "<tr>"; 
					dataListHtml += "<td>"+o.uid+"</td>";
				 
					dataListHtml += "<td>"+o.nick+"</td>"; 
					 			
					dataListHtml += "<td>"+o.cool_num+"</td>";
					dataListHtml += "<td></td>";
					dataListHtml += "<td>"+o.value+"</td>";  
					dataListHtml += "<td>"+o.type+"</td>";	
					dataListHtml += "<td>"+o.add_time+"</td>";
					dataListHtml += "<td>TV</td>";  
					 
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
  	<div class="">
  
  	
	
	<div>
		<fieldset>
		<legend>用户的详细信息</legend>	
		<div class="row">
			<div class="span2">
				<label>查询方式类型：</label>
				<select class="span2" id="type" >
					<option value="all">全部</option>
					<option value="uid">用户UID</option>
					<option value="coolNum">靓号</option> 
				</select>
			</div>
			<div class="span2">
				<label>用户信息：</label>
				<input class="span2" type="text" id="data" style="height:30px"/>
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
	<div>用户基础信息
		<table class="table table-bordered table-condensed table-hover">
			<tr class="info">			
				<td nowrap><strong>用户ID</strong></td> 
				<td nowrap><strong>昵称</strong></td>
				<td nowrap><strong>靓号</strong></td>
				<td nowrap><strong>类型</strong></td>
				<td nowrap><strong>数额</strong></td>
				<td nowrap><strong>备注</strong></td> 
				<td nowrap><strong>记录时间</strong></td>
				<td nowrap><strong>站点</strong></td>
			 
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
