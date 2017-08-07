<?php
require_once '../manage/checkPriv.php';
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
	var type = $('#type').val();
    var dateid = $('#dateid').val();
	$.post("searchPay.php",{
		pageSize: pageSize,
		pageIndex: index,
		type: type,
        dateid: dateid


	},function(result){
		if(result!=null && result!=""){
			var dataList=eval("("+result+")");
			var dataListHtml = "";
			for(var i=0;i<dataList.data.length;i++){
				var o = dataList.data[i];
				dataListHtml += "<tr class='table-body'>";
                dataListHtml += "<td><a href=\"userinfo.php?uid="+o.uid+"\">"+o.uid+"</td>";
                dataListHtml += "<td>"+o.type+"</td>";
				dataListHtml += "<td>"+o.coins+"</td>";
                dataListHtml += "<td>"+o.after+"</td>";
				dataListHtml += "<td>"+o.date+"</td>";
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
		<legend>乐豆增减记录</legend>
		<div class="row">
			 
			<div class="span2" >
				<label>增减类型：</label>
				<select class="span2" id="type" >
					<option value="all">全部</option>

                    <option value="0">未知操作</option>
                    <option value="1">牌桌输赢</option>
                    <option value="2">自动补豆</option>
                    <option value="3">竞技报名</option>
                    <option value="4">竞技取消</option>
                    <option value="5">购买道具</option>
                    <option value="6">任务发奖</option>
                    <option value="7">活动直奖</option>
                    <option value="8">乐币兑换</option>
                    <option value="9">每日签到</option>
                    <option value="10">激活礼包</option>
                    <option value="11">领取邮件</option>
                    <option value="12">竞技发奖</option>
                    <option value="13">后台重设</option>























				</select>
				
			</div>



			<div class="span4">
				<label>增减时间：</label>
                <select class="span2" id="dateid" >
                    <option value="20150710">20150710</option>
                    <option value="20150710">20150713</option>
                    <option value="20150710">20150714</option>
                    <option value="20150710">20150715</option>
                    <option value="20150710">20150716</option>
                    <option value="20150710">20150717</option>







                </select>


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
				
				 
				<td nowrap><strong>用户ID</strong></td>
                <td nowrap><strong>类型</strong></td>
				<td nowrap><strong>乐豆加减</strong></td>
				<td nowrap><strong>当前乐豆</strong></td>
				<td nowrap><strong>时间</strong></td>

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
