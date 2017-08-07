<?php
require_once '../manage/checkPriv.php';
$start = ( isset($_POST['start']) && $_POST['start'] != "" ) ? date("Ymd", strtotime($_POST['start'])) : date("Ymd", time());
$_POST['start'] = $start;
?>
<script src="../js/jquery.js"></script> 
<script src="../js/My97DatePicker/WdatePicker.js" language="javascript"></script>
<link rel="stylesheet" type="text/css" href="../bootstrap/css/bootstrap.min.css" />
<script>
$(function(){
    var start = '<?=$_POST['start']?>';
    $("#start").val(start);
    query();
});
var pageSize = 10;
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
	var type = $('#type').val();
	var cool_num = $('#cool_num').val();
	$.post("userLotteryHistorySearch.php",{
		pageSize: pageSize,
		pageIndex: index,
		type: type,
		cool_num: cool_num,
        start: start
	},function(result){
		if(result!=null && result!=""){
			var dataList=eval("("+result+")");
			$('#uid').val(dataList.uid);
			var dataListHtml = "";
			for(var i=0;i<dataList.data.length;i++){
				var o = dataList.data[i];
				dataListHtml += "<tr class='table-body'>";
                dataListHtml += "<td><a href=\"userInfo.php?uid="+o.uid+"\">"+o.uid+"</td>";
                dataListHtml += "<td>"+o.cool_num+"</td>";
                dataListHtml += "<td>"+o.nick+"</td>";
                dataListHtml += "<td>"+o.prizeid+"</td>";
                dataListHtml += "<td>"+o.coupon+"</td>";
                dataListHtml += "<td>"+o.coins+"</td>";
                dataListHtml += "<td>"+o.prop+"</td>";
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
function query2(){
    var uid = $('#uid').val();
	$.post("userLotteryAdd.php",{
        uid: uid
	},function(result){
		if(result!=null && result!=""){
			var dataList=eval("("+result+")");
			alert(dataList.result);
		}
	});
}

</script>

<body>
<fieldset>
	<legend>用户抽奖记录</legend>
	<div class="row">
		<div class="span4">
			<label>日期：</label>
			<input style="height:30px;" class="span3" type="text"  id="start" class="textbox dtime" readonly onclick="WdatePicker({dateFmt:'yyyyMMdd '})"/>
		</div>
		<div class="span2" >
			<label>抽中类型：</label>
			<select class="span2" id="type" >
				<option value="all">全部</option>
				<option value="coins">乐豆</option>
				<option value="coupon">奖券</option>
				<option value="propid">道具</option>
			</select>
		</div>
		<div class="span2">
			<label>用户编号(ID)：</label>
			<input class="span2" type="text" id="cool_num" name="cool_num" style="height:30px"/>
		</div>
		<div class="span2">
			<label>用户UID：</label>
			<input class="span2" type="text" id="uid" name="uid" style="height:30px"/>
		</div>
		<div span="span1" style="float:right;">
			<label>&nbsp;</label>
			<input type="button" value="查&nbsp;&nbsp;询" onclick="query()" class="btn" />
		</div>
		<div span="span1" style="float:right;">
			<label>&nbsp;</label>
			<input type="button" value="抽奖数＋1" onclick="query2()" class="btn" />
		</div>
	</div>
</fieldset>

<table class="table table-bordered table-condensed table-hover" style="font-size:12px;">
	<tr class="info">
		<td nowrap><strong>UID</strong></td>
		<td nowrap><strong>编号ID</strong></td>
		<td nowrap><strong>昵称</strong></td>
		<td nowrap><strong>奖品编号</strong></td>
		<td nowrap><strong>获得奖券</strong></td>
		<td nowrap><strong>获得乐豆</strong></td>
		<td nowrap><strong>获得道具</strong></td>
		<td nowrap><strong>日期</strong></td>
	</tr>
	<tbody id="dataList"></tbody>
</table>

<table width="920" border="0" cellpadding="5" cellspacing="0" align="center">
	<tr>
		<td height="25" id="pagination" align="center" style="display:none;"> 
			<div class="btn-group">
				<button class="btn" onclick="prePage()">前一页</button>
				<span id="page"><button class="btn" id="pageIndex"></button></span>
				<button class="btn" onclick="nextPage()">后一页</button>
			</div>
			共<span id="count"></span>页
		</td>
	</tr>
</table>

</body>
