<?php
$api = 'tesk';
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
$types = array('0'=>'每日任务','1'=>'成长任务','2'=>'活动任务');
$gotos = array('0'=>'无','1'=>'去普通场','2'=>'去竞技场','3'=>'去充值中心');
$onoff = array('1'=>'已发布','0'=>'未发布');
$isAjax = isset($_REQUEST['isAjax'])?intval($_REQUEST['isAjax']):0;
if ($isAjax) {
	$pageSize = $_REQUEST['pageSize'];
	$pageIndex = $pageSize * $_REQUEST['pageIndex'];
	$acttag = $_REQUEST['acttag'];
	$type = $_REQUEST['type'];
	$is_online = $_REQUEST['is_online'];
	$where = "`is_del`=0 ";
	if ($acttag != 'all') $where .= " AND `acttag`='$acttag'";
	if ($type != 'all') $where .= " AND `type`=$type";
	if ($is_online != 'all') $where .= " AND `is_online`=$is_online";
	$sql = "SELECT * FROM `lord_game_{$api}` WHERE {$where} ORDER BY `is_online` desc,`sort`,`id` LIMIT {$pageIndex}, {$pageSize}";
	$res = $db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
	if ( !$res || !is_array($res) ) $res = array();
	$sql = "SELECT `id`, `name` FROM `lord_game_{$api}` WHERE `is_del` = 0";
	$res2 = $db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
	$tesks = array();
	foreach ( $res2 as $k => $v )
	{
		$tesks[$v['id']] = $v['name'];
	}
	foreach ( $res as $k => $v )
	{
		$res[$k]['type'] = isset($types[$v['type']]) ? $types[$v['type']] : '错误';
		$res[$k]['prev'] = $v['prev'] && isset($tesks[$v['prev']]) ? $tesks[$v['prev']] : '无';
		$res[$k]['goto'] = $v['goto'] && isset($gotos[$v['goto']]) ? $gotos[$v['goto']] : '无';
		$res[$k]['rooms'] = $v['rooms'] ? $v['rooms'] : '全部';
		$res[$k]['channels'] = $v['channels'] ? $v['channels'] : '全部';
		$res[$k]['users'] = $v['users'] ? $v['users'] : '全部';
		$res[$k]['source'] = isset($sidName[$v['sourceId']]) ? $sidName[$v['sourceId']] : '特殊规则';
		$res[$k]['start'] = $v['start_time'] ? date("Y-m-d", $v['start_time']) : "&nbsp;";
		$res[$k]['end'] = $v['end_time'] ? date("Y-m-d", $v['end_time']) : "&nbsp;";
		$res[$k]['period'] = $v['periodName'] ? $v['periodName'] : '无';
		$res[$k]['subject'] = sprintf($v['mailSubject'], $v['name']);
		$res[$k]['prizes'] = $v['prizes'] ? json_decode($v['prizes'],1) : null;
		$res[$k]['create'] = $v['create_time'] ? date("Y-m-d H:i:s", $v['create_time']) : "";
		$res[$k]['update'] = $v['update_time'] ? date("Y-m-d H:i:s", $v['update_time']) : "";
	}
	$array = array();
	$array['data'] = $res;
	$sql = "SELECT count(*) as cn FROM `lord_game_tesk` WHERE {$where} ";
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
				query();
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

var pageSize = 15;
var pageIndex = 0;
var ut_now = <?=$ut_now?>;

function query(){
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
	var type = $('#type').val();
	var is_online = $('#is_online').val();
	$.post("teskList.php",{
		pageSize: pageSize,
		pageIndex: index,
		acttag: acttag,
		type: type,
		is_online: is_online,
		isAjax: 1
	},function(result){
		if(result!=null && result!=""){
			var dataList=eval("("+result+")");
			var dataListHtml = "";
			for(var i=0;i<dataList.data.length;i++){
				var o = dataList.data[i];
				dataListHtml += "<tr class='table-body'>";
				dataListHtml += "<td>"+o.id+"</td>";
				dataListHtml += "<td>"+o.source+"</td>";
				dataListHtml += "<td>"+o.prev+"</td>";
				dataListHtml += "<td>"+o.type+"</td>";
				dataListHtml += "<td>"+o.name+"</td>";
				dataListHtml += "<td>"+o.channels+"</td>";
				dataListHtml += "<td>"+o.rooms+"</td>";
				dataListHtml += "<td>"+o.users+"</td>";
				dataListHtml += "<td>"+o.start+"</td>";
				dataListHtml += "<td>"+o.end+"</td>";
				dataListHtml += "<td>"+o.period+"</td>";
				// dataListHtml += "<td>"+o.subject+"</td>";
				dataListHtml += "<td>";
				// if (o.prizes) {
				// 	var prize = [];
				// 	if (o.prizes.gold) prize.push("乐币: "+o.prizes.gold+"个");
				// 	if (o.prizes.coupon) prize.push("乐券: "+o.prizes.coupon+"个");
				// 	if (o.prizes.coins) prize.push("乐豆: "+o.prizes.coins+"个");
				// 	if (o.prizes.lottery) prize.push("抽奖: "+o.prizes.lottery+"次");
				// 	if (o.prizes.propItems) {
				// 		var items = o.prizes.propItems;
				// 		for (var j=0; j<items.length; j++) {
				// 			prize.push("道具: "+items[j].name+(items[j].ext>0&&items[j].categoryId==1?("("+items[j].ext+"天)"):"")+" x "+items[j].num+"件");
				// 		}
				// 	}
				// 	if (o.prizes.other) prize.push("其他: "+o.prizes.other);
				// 	dataListHtml += prize.join('<br>');
				if (o.prizeName) {
					dataListHtml += o.prizeName;
				} else {
					dataListHtml += "&nbsp;";
				}
				dataListHtml += "</td>";
				dataListHtml += "<td>"+(o.is_surprise>0?'有':'&nbsp')+"</td>";
				dataListHtml += "<td>"+o.sort+"</td>";
				dataListHtml += "<td><a href='#' onclick='ajaxto(this,\"teskAdd.php?reqType="+(o.is_online>0?"offline":"online")+"&id="+o.id+"\")'>"+(o.is_online>0?"[撤销]":"[发布]")+"</a>&nbsp;&nbsp;<a href='#' onclick='linkto(this,\"teskAdd.php?reqType=modify&id="+o.id+"\")'>修改</a>"+(o.is_del>0?"":"&nbsp;&nbsp;<a href='#' onclick='ajaxto(this,\"teskAdd.php?reqType=delete&id="+o.id+"\")'>删除</a>")+"</td>";
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
	<legend>动态任务列表</legend>
	<div class="row">
		<div class="span2" >
			<label>用户行为统称：</label>
			<select class="span2" id="acttag" name="acttag">
				<option value="all">不限</option>
				<?php foreach ($actTags as $k => $v) { echo "<option value='$k'>$v</option>"; } ?>
			</select>
		</div>
		<div class="span2" >
			<label>任务类型：</label>
			<select class="span2" id="type" name="type">
				<option value="all">不限</option>
				<?php foreach ($types as $k => $v) { echo "<option value='$k'>$v</option>"; } ?>
			</select>
		</div>
		<div class="span2" >
			<label>发布状态：</label>
			<select class="span2" id="is_online" name="is_online">
				<option value="all">不限</option>
				<?php foreach ($onoff as $k => $v) { echo "<option value='$k'>$v</option>"; } ?>
			</select>
		</div>
		<div span="span1" style="float:right;">
			<label>&nbsp;</label>
			<input type="button" value="查&nbsp;&nbsp;询" onclick="query()" class="btn"/>
			<input type="button" value="创&nbsp;&nbsp;建" onclick="linkto(this,'teskAdd.php')" class="btn"/>
		</div>
	</div>
</fieldset>

<table class="table table-bordered table-condensed table-hover">
	<tr class="info">
		<th>编号</th>
		<th>用户行为</th>
		<th>前置任务</th>
		<th>任务类型</th>
		<th>任务名称</th>
		<th>专属渠道</th>
		<th>专属房间</th>
		<th>专属用户</th>
		<th>开始</th>
		<th>结束</th>
		<th>周期</th>
		<!-- <th>邮件</th> -->
		<th>固定奖励</th>
		<th>惊喜奖励</th>
		<th>排序</th>
		<th>操作</th>
	</tr>
	<tbody id="dataList">
	</tbody>
</table>

<table border="0" cellpadding="5" cellspacing="0" align="center">
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
