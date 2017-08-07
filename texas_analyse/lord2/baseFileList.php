<?php
header("Content-type: text/html; charset=utf-8");
// ini_set("display_errors","On");error_reporting(E_ALL);//E_ERROR | E_WARNING | E_PARSE
require_once '../manage/checkPriv.php';
require_once '../include/database.class.php';
$ut_now = time();
$pathes = array(
	"about_coins"=>"关于乐豆",
	"about_id"=>"关于账号",
	"about_us"=>"关于我们",
);
$maxversion = 0;
$sql = "SELECT max(`version`) as version FROM `lord_game_version` WHERE `name` = 'verfile' AND `is_done` = 0 LIMIT 1";
$res = $db->query($sql)->fetch(PDO::FETCH_ASSOC);
$maxversion = $res ? $res['version'] : 1;
$versions = array();
$sql = "SELECT `path`,`version` FROM `lord_game_file` GROUP BY `version` ORDER BY `version` DESC";
$res = $db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
$res = $res ? $res : array();
foreach ( $res as $k => $v )
{
	if ( in_array($v['path'], array_keys($pathes)) ) {
		$versions[]=$v['version'];
	}
}
$isAjax = isset($_REQUEST['isAjax'])?intval($_REQUEST['isAjax']):0;
if ($isAjax) {
	$pageSize = $_REQUEST['pageSize'];
	$pageIndex = $pageSize * $_REQUEST['pageIndex'];
	$path = $_REQUEST['path'];
	$version = $_REQUEST['version'];
	$where = "1=1 ";
	if ($path == 'all') $where .= " AND (f.`path`='about_coins' OR f.`path`='about_id' OR f.`path`='about_us')";
	if ($path != 'all') $where .= " AND f.`path`='".$path."'";
	if ($version != 'all') $where .= " AND f.`version`={$version}";
	$sql = "SELECT f.* FROM `lord_game_file` f WHERE {$where} ORDER BY f.`fileid` DESC LIMIT {$pageIndex}, {$pageSize}";
	$res = $db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
	$res = ( $res && is_array($res) ) ? $res : array();
	foreach ( $res as $k => $v )
	{
		$res[$k]['pathname'] = isset($pathes[$v['path']]) ? $pathes[$v['path']] : "";
		$res[$k]['create_date'] = $v['create_time'] ? date("Y-m-d H:i:s", $v['create_time']) : "";
		$res[$k]['update_date'] = $v['update_time'] ? date("Y-m-d H:i:s", $v['update_time']) : "";
	}
	$array['data'] = $res;
	$sql = "SELECT count(*) as cn FROM `lord_game_file` f WHERE {$where} ";
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
				_this.prev('label').find('span').html(data.maxversion);
			} else {
				alert("操作失败["+data.errno+"]："+data.error);
			}
		}
	});
}
function viewimg( o, path ) {
	var _this = $(o);
	var _tr = _this.parents('tr');
	var _html = '<tr class="table-body viewimg"><td colspan="11">';
		_html+= '<a href="http://gt2.youjoy.tv/ddzgamefile/'+path+'.png" target="_blank"><img src="http://gt2.youjoy.tv/ddzgamefile/'+path+'.png"/></a>';
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
	var path = $('#path').val();
	var version = $('#version').val();
	$.post("baseFileList.php",{
		pageSize: pageSize,
		pageIndex: index,
		path: path,
		version: version,
		isAjax: 1
	},function(result){
		if(result!=null && result!=""){
			var dataList=eval("("+result+")");
			var dataListHtml = "";
			for(var i=0;i<dataList.data.length;i++){
				var o = dataList.data[i];
				dataListHtml += "<tr class='table-body'>";
				dataListHtml += "<td>"+o.fileid+"</td>";
				dataListHtml += "<td>"+(o.channel!=""?o.channel:"&nbsp;")+"</td>";
				dataListHtml += "<td>"+(o.channot!=""?o.channot:"&nbsp;")+"</td>";
				dataListHtml += "<td>"+o.pathname+"</td>";
				dataListHtml += "<td>"+'<a href="http://gt2.youjoy.tv/ddzgamefile/'+o.path+'/'+o.fileid+'.png" target="_blank"><img src="http://gt2.youjoy.tv/ddzgamefile/'+o.path+'/'+o.fileid+'.png" style="height:40px;" /></a>'+"</td>";
				dataListHtml += "<td>"+o.version+"</td>";
				dataListHtml += "<td>"+o.ver_ins+"</td>";
				dataListHtml += "<td>"+o.ver_upd+"</td>";
				dataListHtml += "<td>"+o.ver_del+"</td>";
				dataListHtml += "<td>"+o.create_date+"<br/>"+o.update_date+"</td>";
				if(o.ver_del>0){
					dataListHtml += "<td><br><a href='#' onclick='viewimg(this,\""+o.path+"/"+o.fileid+"\")'>查看图片</a></td>";
				} else {
					dataListHtml += "<td><a href='#' onclick='ajaxto(this,\"baseFileAdd.php?type=delete&id="+o.id+"\")'>删除</a>&nbsp;&nbsp;<a href='#' onclick='linkto(this,\"baseFileAdd.php?type=modify&id="+o.id+"\")'>修改</a><br><a href='#' onclick='viewimg(this,\""+o.path+"/"+o.fileid+"\")'>查看图片</a></td>";
				}
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
	<legend>基础图片列表</legend>
	<div class="row">
		<div class="span2" >
			<label>图片类型：</label>
			<select class="span2" id="path" name="path">
				<option value="all">不限</option>
				<?php
				foreach ($pathes as $k=>$v) {
					echo '<option value="'.$k.'">'.$v.'</option>';
				}
				?>
			</select>
		</div>
		<div class="span2">
			<label>版本编号：</label>
			<select class="span2" id="version" id="version">
				<option value="all">不限</option>
				<option value="<?=$maxversion?>">待发版本：<?=$maxversion?></option>
				<?php
				foreach ($versions as $v) {
					if ($v!=$maxversion){
					echo '<option value="'.$v.'">已发：'.$v.'</option>';
					}
				}
				?>
			</select>
		</div>
		<div class="span2">
			<label>待发版本：<span style="color:red;font-weight:bold;font-size:14px;"><?=$maxversion?></span></label>
			<input type="button" value="发&nbsp;&nbsp;布" onclick="ajaxto(this,'versionAdd.php?type=verfile')" class="btn"/>
		</div>
		<div span="span1" style="float:right;">
			<label>&nbsp;</label>
			<input type="button" value="查&nbsp;&nbsp;询" onclick="query()" class="btn"/>
			<input type="button" value="创&nbsp;&nbsp;建" onclick="linkto(this,'baseFileAdd.php')" class="btn"/>
		</div>
	</div>
</fieldset>

<table class="table table-bordered table-condensed table-hover">
	<tr class="info">
		<th>编号</th>
		<th>许可</th>
		<th>屏蔽</th>
		<th>类型</th>
		<th>图片</th>
		<th>版本编号</th>
		<th>新增号</th>
		<th>更新号</th>
		<th>删除号</th>
		<th>创建/变更</th>
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
