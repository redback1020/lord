<?php
header("Content-type: text/html; charset=utf-8");
require_once '../manage/checkPriv.php';
require_once '../include/database.class.php';
$ut_now = time();
$sql = "SELECT `id`, `name`, `acttag`, `acname` FROM `lord_game_tesksource` ORDER BY `sort`";
$res = $db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
$sidName = $actTags = array();
foreach ( $res as $k => $v ) {
	$sidName[$v['id']] = $v['name'];
	$actTags[$v['acttag']] = $v['acname'];
}
$isAjax = isset($_REQUEST['isAjax'])?intval($_REQUEST['isAjax']):0;
if ($isAjax) {
	$pageSize = $_REQUEST['pageSize'];
	$pageIndex = $pageSize * $_REQUEST['pageIndex'];
	$acttag = $_REQUEST['acttag'];
	$where = "`is_del`=0 ";
	if ($acttag != 'all') $where .= " AND `acttag`='$acttag'";
	$sql = "SELECT * FROM `lord_game_tesksource` WHERE {$where} ORDER BY `sort`,`id` LIMIT {$pageIndex}, {$pageSize}";
	$res = $db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
	if ( !$res || !is_array($res) ) $res = array();
	foreach ( $res as $k => $v )
	{
		$res[$k]['accode'] = $v['accode'] ? $v['accode'] : '不限';
		$res[$k]['action'] = $v['action'] ? $v['action'] : '不限';
		$res[$k]['acttag'] = $v['acttag'] ? $v['acttag'] : '错误';
		$res[$k]['execut'] = $v['execut'] ? $v['execut'] : '&nbsp';
		$res[$k]['condit'] = $v['condit'] ? $v['condit'] : '&nbsp';
		$res[$k]['result'] = $v['result'] ? $v['result'] : '&nbsp';
		$res[$k]['create'] = $v['create_time'] ? date("Y-m-d H:i:s", $v['create_time']) : "";
		$res[$k]['update'] = $v['update_time'] ? date("Y-m-d H:i:s", $v['update_time']) : "";
	}
	$array = array();
	$array['data'] = $res;
	$sql = "SELECT count(*) as cn FROM `lord_game_tesksource` WHERE {$where} ";
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
		_html+= '<a href="http://gt2.youjoy.tv/ddzgamefile/tesk_index/'+id+'.png" target="_blank"><img src="http://gt2.youjoy.tv/ddzgamefile/tesk_index/'+id+'.png" style="height:100px;" /></a>';
		_html+= '　';
		_html+= '<a href="http://gt2.youjoy.tv/ddzgamefile/tesk_index/'+id+'.png" target="_blank"><img src="http://gt2.youjoy.tv/ddzgamefile/tesk_index/'+id+'.png" style="height:100px;" /></a>';
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

var pageSize = 10;
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
	var acttag = $('#acttag').val();
	$.post("sourceList.php",{
		pageSize: pageSize,
		pageIndex: index,
		acttag: acttag,
		isAjax: 1
	},function(result){
		if(result!=null && result!=""){
			var dataList=eval("("+result+")");
			var dataListHtml = "";
			for(var i=0;i<dataList.data.length;i++){
				var o = dataList.data[i];
				dataListHtml += "<tr class='table-body'>";
				dataListHtml += "<td>"+o.id+"</td>";
				dataListHtml += "<td>"+o.name+"</td>";
				// dataListHtml += "<td>"+o.accode+"</td>";
				dataListHtml += "<td>"+o.action+"</td>";
				dataListHtml += "<td>"+o.acttag+"</td>";
				dataListHtml += "<td class='word_break'>"+o.execut+"</td>";
				dataListHtml += "<td class='word_break'>"+o.condit+"</td>";
				dataListHtml += "<td class='word_break'>"+o.result+"</td>";
				dataListHtml += "<td>"+o.sort+"</td>";
				dataListHtml += "<td><a href='#' onclick='linkto(this,\"sourceAdd.php?type=modify&id="+o.id+"\")'>修改</a>&nbsp;&nbsp;<a href='#' onclick='ajaxto(this,\"sourceAdd.php?type=delete&id="+o.id+"\")'>删除</a></td>";
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
	<legend>动态任务源码列表</legend>
	<div class="row">
		<div class="span2" >
			<label>用户行为统称：</label>
			<select class="span2" id="acttag" name="acttag">
				<option value="all">不限</option>
				<?php foreach ($actTags as $acttag => $acname) { echo "<option value='$acttag'>$acname</option>"; } ?>
			</select>
		</div>
		<div span="span1" style="float:right;">
			<label>&nbsp;</label>
			<input type="button" value="查&nbsp;&nbsp;询" onclick="query()" class="btn"/>
			<input type="button" value="创&nbsp;&nbsp;建" onclick="linkto(this,'sourceAdd.php')" class="btn"/>
		</div>
	</div>
</fieldset>

<table class="table table-bordered table-condensed table-hover">
	<tr class="info">
		<th>编号</th>
		<th>行为</th>
		<!-- <th>协议号</th> -->
		<th>协议名</th>
		<th>标识符</th>
		<th>执行逻辑</th>
		<th>达成条件</th>
		<th>达成结果</th>
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
