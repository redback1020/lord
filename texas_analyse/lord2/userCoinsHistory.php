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
    var end = '<?=$_POST['end']?>';
    $("#start").val(start);
    $("#end").val(end);
    query();
});
var pageSize = 20;
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
    var uid = $('#uid').val();
    var cool_num = $('#cool_num').val();
    var start = $('#start').val();
	$.post("userCoinsHistorySearch.php",{
		pageSize: pageSize,
		pageIndex: index,
		type: type,
        uid: uid,
        cool_num: cool_num,
        start: start
	},function(result){
		if(result!=null && result!=""){
			var dataList=eval("("+result+")");
			var dataListHtml = "";
			for(var i=0;i<dataList.data.length;i++){
				var o = dataList.data[i];
				dataListHtml += "<tr class='table-body'>";
                dataListHtml += "<td><a href=\"userInfo.php?uid="+o.uid+"\">"+o.uid+"</td>";
                dataListHtml += "<td>"+o.cool_num+"</td>";
                dataListHtml += "<td>"+o.nick+"</td>";
                dataListHtml += "<td>"+o.type+"</td>";
				dataListHtml += "<td>"+o.coins+"</td>";
                dataListHtml += "<td>"+o.after+"</td>";
				dataListHtml += "<td>"+o.date+"</td>";
                dataListHtml += "</tr>";
			}
			if(dataListHtml=="" && isNext){
				alert("已经是最后一页");
				pageIndex--;
            } else if (dataListHtml=="") {
                alert("没有数据");
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
                    <option value="14">兑换中心</option>
                    <option value="15">领取救济</option>
                    <option value="16">竞技周奖</option>
                    <option value="17">用豆抽奖</option>
                    <option value="18">买记牌器</option>

                </select>
            </div>
            <div class="span2">
                <label>UID：</label>
                <input class="span2" type="text" id="uid" name="uid" style="height:30px"/>
            </div>
            <div class="span2">
                <label>编号ID：</label>
                <input class="span2" type="text" id="cool_num" name="cool_num" style="height:30px"/>
            </div>
            <div class="span4">
				<label>纪录日期：</label>
                <input style="height:30px;" class="span3" type="text"  id="start" class="textbox dtime" readonly onclick="WdatePicker({dateFmt:'yyyyMMdd '})"/>


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
                <td nowrap><strong>UID</strong></td>
                <td nowrap><strong>编号ID</strong></td>
                <td nowrap><strong>昵称</strong></td>
                <td nowrap><strong>类型</strong></td>
				<td nowrap><strong>加减乐豆</strong></td>
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
