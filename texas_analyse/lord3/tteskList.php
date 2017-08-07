<?php
header("Content-type: text/html; charset=utf-8");
require_once '../manage/checkPriv.php';
require_once '../include/database.class.php';
$ut_now = time();
$sql = "SELECT * FROM `lord_list_ttesksource` ORDER BY `sort`, `id`";
$res = $db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
if ( !$res ) $res = array();
$ttesks = array();
foreach ( $res as $k => $v )
{
	$ttesks[$v['id']] = $v['name'];
}
$typeids = array('0'=>'匹配目标','1'=>'误导目标');
$isAjax = isset($_REQUEST['isAjax'])?intval($_REQUEST['isAjax']):0;
if ($isAjax) {
	$pageSize = $_REQUEST['pageSize'];
	$pageIndex = $pageSize * $_REQUEST['pageIndex'];
	$where = "1=1 ";
	$sql = "SELECT * FROM `lord_list_ttesk` WHERE {$where} ORDER BY `sort`,`id` LIMIT {$pageIndex}, {$pageSize}";
	$res = $db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
	if ( !$res || !is_array($res) ) $res = array();
	foreach ( $res as $k => $v )
	{
		$conds = explode(' ', $v['conds']);
		$conds_= array();
		foreach ( $conds as $kk => $vv )
		{
			if ( isset($ttesks[$vv]) ) $conds_[]= $ttesks[$vv];
		}
		$res[$k]['conds'] = join('且', $conds_);
		$res[$k]['typeid'] = isset($typeids[$v['typeid']])?$typeids[$v['typeid']]:$typeids['0'];
		$res[$k]['create'] = $v['create_time'] ? date("Y-m-d H:i:s", $v['create_time']) : "";
		$res[$k]['update'] = $v['update_time'] ? date("Y-m-d H:i:s", $v['update_time']) : "";
	}
	$array = array();
	$array['data'] = $res;
	$sql = "SELECT count(*) as cn FROM `lord_list_ttesksource` WHERE {$where} ";
	$res = $db->query($sql)->fetch(PDO::FETCH_ASSOC);
	$array['count'] = $res['cn'];
	echo json_encode($array);
	exit;
}
?>
<script type="text/javascript" src="../js/jquery.js"></script>
<script type="text/javascript" src="../js/My97DatePicker/WdatePicker.js"></script>
<link type="text/css" href="../bootstrap/css/bootstrap.min.css" rel="stylesheet" />
<style type="text/css">
legend{margin-bottom: 10px!important;}
table.table{ font-size: 12px;}
table.table th, table.table td a{ white-space: nowrap;}
table.table td.word_break{word-break:break-all;/*normal|keep-all|break-all*/word-wrap:break-word;/*normal|break-word*/white-space:normal;/*normal|nowrap*/}
</style>
<script>
$(function(){
	query(); 
});
function linkto( o, urlTo ) {
	var _a = urlTo.split('/');
	var _this = $(o);
	if ( _a[0] != "http" ) {
		var _b = self.location.href.split('/');
		delete _b[_b.length-1];
		urlTo = _b.join('/') + urlTo;
	}
	self.location.href=urlTo;
}
function ajaxto( o, urlTo ) {
	var _a = urlTo.split('/');
	var _this = $(o);
	if ( _a[0] != "http" ) {
		var _b = self.location.href.split('/');
		delete _b[_b.length-1];
		urlTo = _b.join('/') + urlTo;
	}
	$.getJSON(urlTo, function(data){
		if ( data ) {
			if ( data.errno == 0 ) {
				alert("操作成功。"); query();
			} else {
				alert("操作失败["+data.errno+"]："+data.error);
			}
		}
	});
}
var pageSize = 50;
var pageIndex = 0;
var ut_now = <?=$ut_now?>;
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
	$.post("tteskList.php",{
		pageSize: pageSize,
		pageIndex: index,
		isAjax: 1
	},function(result){
		if(result!=null && result!=""){
			var dataList=eval("("+result+")");
			var dataListHtml = "";
			for(var i=0;i<dataList.data.length;i++){
				var o = dataList.data[i];
				dataListHtml += "<tr class='table-body'>";
				dataListHtml += "<td>"+o.id+"</td>";
				dataListHtml += "<td>"+o.rooms+"</td>";
				dataListHtml += "<td>"+o.channels+"</td>";
				dataListHtml += "<td>"+o.users+"</td>";
				dataListHtml += "<td>"+o.typeid+"</td>";
				dataListHtml += "<td>"+o.conds+"</td>";
				dataListHtml += "<td>"+o.prob+"</td>";
				dataListHtml += "<td>"+o.coins+"</td>";
				dataListHtml += "<td>"+o.coupon+"</td>";
				dataListHtml += "<td>"+o.sort+"</td>";
				dataListHtml += "<td>"+o.create+"</td>";
				dataListHtml += "<td>"+o.update+"</td>";
				dataListHtml += "<td><a href='#' onclick='linkto(this,\"tteskAdd.php?type=modify&id="+o.id+"\")'>修改</a>"+(o.is_del>0?"":"&nbsp;&nbsp;<a href='#' onclick='ajaxto(this,\"tteskAdd.php?type=delete&id="+o.id+"\")'>删除</a>")+"</td>";
				dataListHtml += "</tr>";
			}
			if(dataListHtml=="" && isNext){
				alert("已经是最后一页");
				pageIndex--;
			}else{
				$("#pageNum").html(Math.ceil(dataList.count/pageSize));
				$("#count").html(dataList.count);
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

<body><div style="position:absolute;left:0;top:0;width:100%;"><div style="padding:0 10px;">

<fieldset>
	<legend>牌局任务 - 列表</legend>
	<div class="row">
		<div span="span1" style="float:right;margin-bottom:10px;">
			<label>&nbsp;</label>
			<input type="button" value="查&nbsp;&nbsp;询" onclick="query()" class="btn"/>
			<input type="button" value="创&nbsp;&nbsp;建" onclick="linkto(this,'tteskAdd.php')" class="btn"/>
		</div>
	</div>
</fieldset>

<table class="table table-bordered table-condensed table-hover">
	<tr class="info">
		<th>编号</th>
		<th>专属房间</th>
		<th>专属渠道</th>
		<th>专属用户</th>
		<th>匹配类型</th>
		<th>任务名称</th>
		<th>触发概率</th>
		<th>奖励乐豆</th>
		<th>奖励乐券</th>
		<th>排序</th>
		<th>创建时间</th>
		<th>更新时间</th>
		<th>操作</th>
	</tr>
	<tbody id="dataList">
	</tbody>
</table>

<table width="900" border="0" cellpadding="5" cellspacing="0" align="center">
	<tr>
		<td height="25" id="pagination" align="center" style="display:none;"> 
			共 <span id="count"></span>条 / <span id="pageNum"></span>页&nbsp;
			<div class="btn-group">
				<button class="btn" onclick="prePage()">前一页</button>
				<span id="page"><button class="btn" id="pageIndex"></button></span>
				<button class="btn" onclick="nextPage()">后一页</button>
			</div>
		</td>
	</tr>
</table>

</div></div></body>
