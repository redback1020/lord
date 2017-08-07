<?php
require_once '../include/priv.php';
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
	$.post("searchResult.php",{
		pageSize: pageSize,
		pageIndex: index  
	},function(result){ 
		if(result!=null && result!=""){
			var dataList=eval("("+result+")");  
			var dataListHtml = "";
			for(var i=0;i<dataList.data.length;i++){
				var o = dataList.data[i];
				dataListHtml += "<tr class='table-body'>";
				dataListHtml += "<td><a href=\"userinfo.php?uid="+o.uid+"&<?=getPri()?>\">"+o.uid+"</a></td>"; 
				dataListHtml += "<td>"+o.nick+"</td>";
				dataListHtml += "<td>"+o.wechat_nickname+"</td>";
				dataListHtml += "<td>"+o.gold+"</td>";
				dataListHtml += "<td>"+o.coins+"</td>";
				dataListHtml += "<td>"+o.offline_gold+"</td>";
				dataListHtml += "<td>"+o.offline_coins+"</td>";
				dataListHtml += "<td>"+o.fruit_free+"</td>";
				dataListHtml += "<td>"+o.vip_lv+"</td>";
				dataListHtml += "<td>"+o.level+"</td>";
				dataListHtml += "<td>"+o.channel+"</td>";
				dataListHtml += "<td>"+o.add_time+"</td>";
				dataListHtml += "<td>"+o.last_login+"</td>";
				dataListHtml += "<td>"+o.reg_time+"</td>";
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
  	<div class="">
  
  	
	
	<div>
		<fieldset>
		<legend>绑定游戏ID的微信用户信息</legend>	
		 			
	</fieldset>
	</div>

	<div>
		<table class="table table-bordered table-condensed table-hover" style="font-size:12px;">
			<tr class="info">			
				<td nowrap><strong>用户UID</strong></td>
				<td nowrap><strong>游戏昵称</strong></td>
				<td nowrap><strong>微信昵称</strong></td>
				<td nowrap><strong>金币数</strong></td>
				<td nowrap><strong>筹码数</strong></td>
				<td nowrap><strong>未领取<br>金币数</strong></td>
				<td nowrap><strong>未领取<br>筹码数</strong></td>
				<td nowrap><strong>未使用<br>水果机数</strong></td>
				<td nowrap><strong>VIP等级</strong></td>
				<td nowrap><strong>经验等级</strong></td>
				<td nowrap><strong>用户来源</strong></td>
				<td nowrap><strong>加入微信时间</strong></td>
				<td nowrap><strong>上次登录时间</strong></td>
				<td nowrap><strong>注册时间</strong></td>
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
