<?php
require_once '../manage/checkPriv.php';
?>
<script src="../js/jquery.js"></script>
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
	$.post("searchPlayer.php",{
		pageSize: pageSize,
		pageIndex: index  
	},function(result){ 
		if(result!=null && result!=""){
			var dataList=eval("("+result+")");  
			var dataListHtml = "";
			for(var i=0;i<dataList.data.length;i++){
				var o = dataList.data[i];
				dataListHtml += "<tr class='table-body'>";
				dataListHtml += "<td><a href=\"userinfo.php?uid="+o.uid+"\">"+o.uid+"</a></td>"; 

				dataListHtml += "<td>"+o.nick+"</td>";
				dataListHtml += "<td>"+o.num+"</td>";
				dataListHtml += "<td>"+o.max_coins+"</td>"; 
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
  
  	
	
	<div>
		<fieldset>
		<legend>争霸赛中，真实玩家玩牌轮数</legend>	
		 			
	</fieldset>
	</div>

	<div>
		<table class="table table-bordered table-condensed table-hover">
			<tr class="info">			
				<td nowrap><strong>用户UID</strong></td>
				<td nowrap><strong>游戏昵称</strong></td>
				<td nowrap><strong>玩牌轮数</strong></td>
				<td nowrap><strong>最大筹码数</strong></td> 
			</tr>
			<tbody id="dataList">
			 
			</tbody>
		</table>
	</div>
	<table width="920" border="0" cellpadding="5" cellspacing="0" align="center">
	<tr><td height="25" id="pagination" align="center" style="display:none;"> 
		<div class="btn-group">
		  <button class="btn" onclick="prePage()">前一页</button>
		  <button class="btn" id="pageIndex"></button>
		  <button class="btn" onclick="nextPage()">后一页</button>
		</div>
		共<span id="count"></span>页
	</td></tr>
	</table>
	
	</div>
  </body>
