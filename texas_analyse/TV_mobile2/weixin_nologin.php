<?php
require_once '../manage/checkPriv.php';
?>
<script src="../js/jquery.js"></script>
<script src="../js/My97DatePicker/WdatePicker.js" language="javascript"></script>
<link rel="stylesheet" type="text/css" href="../bootstrap/css/bootstrap.min.css" />
<script>

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
	$.post("searchNologin.php",{
		pageSize: pageSize,
		pageIndex: index  ,
		time: $('#time').val()
	},function(result){ 
		if(result!=null && result!=""){
			var dataList=eval("("+result+")");  
			var dataListHtml = "";
			for(var i=0;i<dataList.data.length;i++){
				var o = dataList.data[i];
				dataListHtml += "<tr class='table-body'>"; 
				dataListHtml += "<td><a href=\"userinfo.php?uid="+o.uid+"\">"+o.uid+"</a></td>"; 

				dataListHtml += "<td>"+o.nickname+"</td>";
				dataListHtml += "<td>"+o.lasttime+"</td>";
				dataListHtml += "<td>"+o.addtime+"</td>";
			 
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
		<legend>指定时间之后未登录游戏的微信玩家</legend>	
		<div class="row">
			
			<div class="span4">
				<label>时间：</label>
				<input style="height:30px;" class="span3" type="text" id="time" class="textbox dtime" readonly onclick="WdatePicker({dateFmt:'yyyy-MM-dd HH:mm:ss'})"/>
			</div>
			<div span="span1" style="float:right;">
				<label>&nbsp;</label>
				<input type="button" value="查&nbsp;&nbsp;询" onclick="query()" class="btn" />
			</div>
		</div>				
	</fieldset>
	</div>

	<div>
		<table class="table table-bordered table-condensed table-hover"  style="font-size:12px;">
			<tr class="info">			
				<td nowrap><strong>用户UID</strong></td>
				<td nowrap><strong>微信昵称</strong></td> 
				  
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
		  <button class="btn" id="pageIndex"></button>
		  <button class="btn" onclick="nextPage()">后一页</button>
		</div>
		共<span id="count"></span>页
	</td></tr>
	</table>
	
	</div>
  </body>
