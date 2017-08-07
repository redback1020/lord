<?php
header("Content-type: text/html; charset=utf-8");
require_once '../manage/checkPriv.php';
require_once '../include/database.class.php';
$ut_now = time();
$isAjax = isset($_REQUEST['isAjax'])?intval($_REQUEST['isAjax']):0;
if ($isAjax) {
	$pageSize = $_REQUEST['pageSize'];
	$pageIndex = $pageSize * $_REQUEST['pageIndex'];
	$where = "1=1 ";
	$sql = "SELECT * FROM `lord_game_surprise` WHERE {$where} ORDER BY `sort`,`id` LIMIT {$pageIndex}, {$pageSize}";
	$res = $db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
	if ( !$res || !is_array($res) ) $res = array();
	$teskidName = null;
	foreach ( $res as $k => $v )
	{
		$teskids = $v['teskids'] ? json_decode($v['teskids'],1) : array();
		$teskNames = "";
		if ( $teskids ) {
			if ( $teskidName === null ) {
				$sql = "SELECT `id`,`name` FROM `lord_game_tesk`";
				$tesklist = $db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
				if ( !$tesklist || !is_array($tesklist) ) $tesklist = array();
				$teskidName = array();
				foreach ( $tesklist as $kk => $vv )
				{
					$teskidName[$vv['id']] = $vv['name'];
				}
			}
			$teskNames = array();
			foreach ( $teskids as $kk => $vv )
			{
				if ( isset($teskidName[$vv]) ) $teskNames[] = $teskidName[$vv];
			}
			$teskNames = join("<br/>", $teskNames);
		}
		$res[$k]['teskNames'] = $teskNames ? $teskNames : "&nbsp;";
		$res[$k]['period'] = $v['periodName'] ? $v['periodName'] : '无';
		$res[$k]['chance'] = $v['chance'] ? (($v['chance']/100+0).'%') : 0;
		$res[$k]['is_grab'] = $v['is_grab'] ? '可抢' : '&nbsp;';
		$res[$k]['create'] = $v['create_time'] ? date("Y-m-d H:i:s", $v['create_time']) : "";
		$res[$k]['update'] = $v['update_time'] ? date("Y-m-d H:i:s", $v['update_time']) : "";
	}
	$array = array();
	$array['data'] = $res;
	$sql = "SELECT count(*) as cn FROM `lord_game_surprise` WHERE {$where} ";
	$res = $db->query($sql)->fetch();
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
				alert("操作成功。");
				_this.parents('td').prev('td').html(_this.html());
			} else {
				alert("操作失败["+data.errno+"]："+data.error);
			}
		}
	});
}
function viewimg( o, id ) {
	var _this = $(o);
	var _tr = _this.parents('tr');
	var _html = '<tr class="table-body viewimg"><td colspan="10">';
		_html+= '<a href="http://gt2.youjoy.tv/ddzgamefile/prize_small/'+id+'.png" target="_blank"><img src="http://gt2.youjoy.tv/ddzgamefile/prize_small/'+id+'.png" style="height:100px;" /></a>';
		_html+= '　';
		_html+= '<a href="http://gt2.youjoy.tv/ddzgamefile/prize_small/'+id+'.png" target="_blank"><img src="http://gt2.youjoy.tv/ddzgamefile/prize_small/'+id+'.png" style="height:100px;" /></a>';
		_html+= '</td></tr>';
	if ( _tr.next('.viewimg').size()>0 ) {
		if ( _tr.next('.viewimg:visible').size()>0) {
			_tr.next('.viewimg').hide();
		} else {
			_tr.next('.viewimg').show();
		}
	} else {
		$(_html).insertAfter(_tr);
	}
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
	$.post("surpriseList.php",{
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
				dataListHtml += "<td>"+o.teskNames+"</td>";
				dataListHtml += "<td>"+o.name+"</td>";
				dataListHtml += "<td>"+'<a href="http://gt2.youjoy.tv/ddzgamefile/prize_small/'+o.fileid+'.png" target="_blank"><img src="http://gt2.youjoy.tv/ddzgamefile/prize_small/'+o.fileid+'.png" style="height:40px;" /></a>'+"</td>";
				dataListHtml += "<td>"+o.keyName+"</td>";
				dataListHtml += "<td>"+o.keyVal+"</td>";
				dataListHtml += "<td>"+o.keyExt+"</td>";
				dataListHtml += "<td>"+o.period+"</td>";
				dataListHtml += "<td>"+o.times+"</td>";
				dataListHtml += "<td>"+o.chance+"</td>";
				dataListHtml += "<td>"+o.mailSubject+"</td>";
				dataListHtml += "<td>"+o.is_grab+"</td>";
				dataListHtml += "<td>"+o.sort+"</td>";
				dataListHtml += "<td><a href='#' onclick='linkto(this,\"surpriseAdd.php?type=modify&id="+o.id+"\")'>修改</a>"+(o.is_del>0?"":"&nbsp;&nbsp;<a href='#' onclick='ajaxto(this,\"surpriseAdd.php?type=delete&id="+o.id+"\")'>删除</a>")+"</td>";
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
	<legend>活动任务暴奖列表</legend>
	<div class="row">
		<div span="span1" style="float:right;margin-bottom:10px;">
			<label>&nbsp;</label>
			<input type="button" value="查&nbsp;&nbsp;询" onclick="query()" class="btn"/>
			<input type="button" value="创&nbsp;&nbsp;建" onclick="linkto(this,'surpriseAdd.php')" class="btn"/>
		</div>
	</div>
</fieldset>

<table class="table table-bordered table-condensed table-hover">
	<tr class="info">
		<th>编号</th>
		<th>专属活动</th>
		<th>奖品</th>
		<th>图片</th>
		<th>字段</th>
		<th>数值</th>
		<th>扩展值</th>
		<th>周期</th>
		<th>次数</th>
		<th>概率</th>
		<th>邮件</th>
		<th>争抢</th>
		<th>排序</th>
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
