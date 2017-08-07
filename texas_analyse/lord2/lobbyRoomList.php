<?php
header("Content-type: text/html; charset=utf-8");
require_once '../manage/checkPriv.php';
require_once '../include/database.class.php';
$ut_now = time();
$isAjax = isset($_REQUEST['isAjax'])?intval($_REQUEST['isAjax']):0;
if ($isAjax) {
	$pageSize = $_REQUEST['pageSize'];
	$pageIndex = $pageSize * $_REQUEST['pageIndex'];
	$modelId = $_REQUEST['modelId'];
	$where = "1=1 ";
	if ($modelId != 'all') $where .= " AND `modelId`='".$modelId."'";
	$sql = "SELECT * FROM `lord_game_room` WHERE {$where} ORDER BY `sort`, `id` DESC LIMIT {$pageIndex}, {$pageSize}";
	// echo $sql;
	$res = $db->query($sql)->fetchAll();
	$res = ( $res && is_array($res) ) ? $res : array();
	foreach ( $res as $k => $v )
	{
		$res[$k]['showRules'] = $v['showRules'] ? json_decode($v['showRules'],1) : null;
		$apkurl = explode('/', $v['apkurl']);
		$res[$k]['apkname'] = $apkurl ? end($apkurl) : '';
		$res[$k]['create_date'] = $v['create_time'] ? date("Y-m-d H:i:s", $v['create_time']) : "";
		$res[$k]['update_date'] = $v['update_time'] ? date("Y-m-d H:i:s", $v['update_time']) : "";
	}
	$array['data'] = $res;
	$sql = "SELECT count(*) as cn FROM `lord_game_room` WHERE {$where} ";
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
table.table{ font-size: 12px;}
table.table th{ white-space: nowrap;}
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
		_html+= '<a href="http://gt2.youjoy.tv/ddzgamefile/lobby_room/'+id+'.png" target="_blank"><img src="http://gt2.youjoy.tv/ddzgamefile/lobby_room/'+id+'.png" style="height:100px;" /></a>';
		_html+= '　';
		_html+= '<a href="http://gt2.youjoy.tv/ddzgamefile/lobby_roompop/'+id+'.png" target="_blank"><img src="http://gt2.youjoy.tv/ddzgamefile/lobby_roompop/'+id+'.png" style="height:100px;" /></a>';
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
	var modelId = $('#modelId').val();
	$.post("lobbyRoomList.php",{
		pageSize: pageSize,
		pageIndex: index,
		modelId: modelId,
		isAjax: 1
	},function(result){
		if(result!=null && result!=""){
			var dataList=eval("("+result+")");
			var dataListHtml = "";
			for(var i=0;i<dataList.data.length;i++){
				var o = dataList.data[i];
				dataListHtml += "<tr class='table-body'>";
				dataListHtml += "<td>"+o.modelId+"</td>";
				dataListHtml += "<td>"+o.roomId+"</td>";
				dataListHtml += "<td>"+o.name+"</td>";
				dataListHtml += "<td>"+o.sort+"</td>";
				dataListHtml += "<td>";
				if (o.showRules) {
					//当前版本只处理单条规则，暂不处理多条规则并列的现象
					o.showRules = o.showRules[0];
					var rules = [];
					if (o.showRules.channel) rules.push("渠道: "+o.showRules.channel.join(' '));
					if (o.showRules.gold) rules.push("乐币: "+o.showRules.gold);
					if (o.showRules.coins) rules.push("乐豆: "+o.showRules.coins);
					if (o.showRules.mixtime) {
						var mtime = o.showRules.mixtime;
						for (var j=0; j<mtime.length; j++) {
							rules.push("时间: "+mtime[j]);
						}
					}
					dataListHtml += rules.join('<br>');
				} else {
					dataListHtml += "&nbsp;";
				}
				dataListHtml += "</td>";
				dataListHtml += "<td><a href='"+o.apkurl+"' target='_blank'>"+o.apkname+"</a></td>";
				dataListHtml += "<td>"+o.create_date+"<br>"+o.update_date+"</td>";
				dataListHtml += "<td>"+(o.is_del>0?"删除":"&nbsp;")+"</td>";
				dataListHtml += "<td><a href='#' onclick='ajaxto(this,\"lobbyRoomAdd.php?type=delete&id="+o.id+"\")'>删除</a>&nbsp;&nbsp;<a href='#' onclick='linkto(this,\"lobbyRoomAdd.php?type=modify&id="+o.id+"\")'>修改</a><br><a href='#' onclick='viewimg(this,"+o.roomId+")'>查看图片</a></td>";
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

<body>
<div style="position:absolute;left:0;top:0;padding:0 10px;">

<fieldset>
	<legend>大厅场次列表</legend>
	<div class="row">
		<div class="span2" >
			<label>场次模式：</label>
			<select class="span2" id="modelId" name="modelId">
				<option value="all">广告场</option>
			</select>
		</div>
		<div span="span1" style="float:right;">
			<label>&nbsp;</label>
			<input type="button" value="查&nbsp;&nbsp;询" onclick="query()" class="btn"/>
			<input type="button" value="创&nbsp;&nbsp;建" onclick="linkto(this,'lobbyRoomAdd.php')" class="btn"/>
		</div>
	</div>
</fieldset>

<table class="table table-bordered table-condensed table-hover">
	<tr class="info">
		<th>场次模式</th>
		<th>场次编号</th>
		<th>名称</th>
		<th>排序</th>
		<th>展现规则</th>
		<th>APK网址</th>
		<th>创建/变更</th>
		<th>状态</th>
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

</div>
</body>
