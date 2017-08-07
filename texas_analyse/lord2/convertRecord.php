<?php
header("Content-type: text/html; charset=utf-8");
require_once '../manage/checkPriv.php';
require_once '../include/database.class.php';//
$itemtypes = array('coupon2mobifee'=>'乐券兑话费');
$states = array('0'=>'未处理', '1'=>'已发货');
$ut_now = time();
$isAjax = isset($_REQUEST['isAjax']) ? intval($_REQUEST['isAjax']) : 0;
if ( $isAjax ) {
	$pageSize = intval($_REQUEST['pageSize']);
	$pageIndex = $pageSize * intval($_REQUEST['pageIndex']);
	$start = trim($_REQUEST['start']) ? date("Ymd", strtotime($_REQUEST['start'])) : 0;
	$end = trim($_REQUEST['end']) ? date("Ymd", strtotime($_REQUEST['end'])) : 0;
	$state = trim($_REQUEST['state']) === 'all' ? 'all' : intval($_REQUEST['state']);
	$uid = intval($_REQUEST['uid']);
	$cool_num = intval($_REQUEST['cool_num']);
	$where = "1=1";
	if ( $start && !$end ) $where .= " AND `dateid` >= $start";
	elseif ( !$start && $end ) $where .= " AND `dateid` <= $end";
	elseif ( $start && $end ) $where .= " AND `dateid` >= $start AND `dateid` <= $end";
	if ( $state !== 'all') $where .= " AND `state` = $state";
	if ( $uid > 0 ) $where .= " AND `uid` = $uid";
	if ( $cool_num > 0 ) $where .= " AND `cool_num` = $cool_num";
	$sql = "SELECT * FROM `lord_record_convert` WHERE {$where} ORDER BY `id` DESC LIMIT {$pageIndex}, {$pageSize}";
	$res = $db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
	$res = ( $res && is_array($res) ) ? $res : array();
	$uids = array();
	foreach ( $res as $k => $v )
	{
		$uids[] = $v['uid'];
		$res[$k]['isRecharge'] = 0;
		$res[$k]['create_time'] = $v['create_time'] ? date("Y-m-d H:i:s", $v['create_time']) : "";
		$res[$k]['type'] = isset($itemtypes[$v['type']]) ? $itemtypes[$v['type']] : "&nbsp;";
		$res[$k]['state_'] = isset($states[$v['state']]) ? $states[$v['state']] : "&nbsp;";
		$res[$k]['oid'] = $v['oid'] ? $v['oid'] : "&nbsp;";
		$res[$k]['comments'] = $v['comments'] ? $v['comments'] : "&nbsp;";
		$res[$k]['update_time'] = $v['update_time'] ? date("Y-m-d H:i:s", $v['update_time']) : "&nbsp;";
	}
	if ( $uids ) {
		$sql = "SELECT distinct `uid` FROM `lord_user_cost` WHERE `uid` IN (".join(',',$uids).")";
		$ret = $db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
		$ret = ( $ret && is_array($ret) ) ? $ret : array();
		if ( $ret ) {
			$uids = array();
			foreach ( $ret as $k => $v ) { $uids[]=$v['uid']; }
		}
		foreach ( $res as $k => $v )
		{
			if ( in_array($v['uid'], $uids) ) {
				$res[$k]['isRecharge'] = 1;
			}
		}
	}
	$array['data'] = $res;
	$sql = "SELECT count(*) as total FROM `lord_record_convert` WHERE {$where}";
	$res = $db->query($sql)->fetch(PDO::FETCH_ASSOC);
	$array['total'] = $res['total'];
	echo json_encode($array);
	exit;
}
?>
<script type="text/javascript" src="../js/jquery.js"></script>
<script type="text/javascript" src="../js/My97DatePicker/WdatePicker.js"></script>
<link type="text/css" href="../bootstrap/css/bootstrap.min.css" rel="stylesheet" />
<style type="text/css">
.body{position:absolute;left:0;top:0;padding:0 0 0 10px;width:98%;}
legend{margin-bottom: 10px;}
table.table{ font-size: 12px;margin-bottom:8px;width: 100%!important;}
table.table th,table.table td{ white-space: nowrap;}
table.table th{background:#eee;}
table.table .bg_red td{background-color: yellow;}
</style>
<script>
var ut_now = <?=$ut_now?>;
var pageSize = 20;
var pageIndex = 0;
$(function(){ query(); });
function query() { pageIndex = 0; queryByPage(pageIndex); }
function prePage() { if ( pageIndex==0 ) { alert("已经是第一页"); return; } pageIndex--; queryByPage(pageIndex); }
function nextPage() { pageIndex++; queryByPage(pageIndex, true); }
function queryByPage( index, isNext ) {
	var state = $('#state').val();
	var start = $('#start').val();
	var end = $('#end').val();
	var uid = $('#uid').val();
	var cool_num = $('#cool_num').val();
	$.post("convertRecord.php", {
		pageSize: pageSize,
		pageIndex: index,
		state: state,
		start: start,
		end: end,
		uid: uid,
		cool_num: cool_num,
		isAjax: 1
	}, function( result ) {
		if( result == null || result == "" ) { alert("获取数据失败！"); return; }
		var dataList = eval("("+result+")");
		var dataListHtml = "";
		for ( var i=0; i < dataList.data.length; i++ ) {
			var o = dataList.data[i];
			dataListHtml += "<tr class='table-body"+(o.isRecharge?'':' bg_red')+"'>";
			dataListHtml += "<td><a href=\"userInfo.php?uid="+o.uid+"\">"+o.uid+"</a></td>";
			dataListHtml += "<td>"+o.cool_num+"</td>";
			dataListHtml += "<td>"+o.nick+"</td>";
			dataListHtml += "<td>"+o.channel+"</td>";
			dataListHtml += "<td>"+o.type+"</td>";
			dataListHtml += "<td>"+o.title+"</td>";
			dataListHtml += "<td>"+o.cost+"</td>";
			dataListHtml += "<td>"+o.after+"</td>";
			dataListHtml += "<td>"+o.other+"</td>";
			dataListHtml += "<td>"+o.create_time+"</td>";
			dataListHtml += "<td>"+o.state_+"</td>";
			dataListHtml += "<td>"+o.oid+"</td>";
			// dataListHtml += "<td>"+o.comments+"</td>";
			dataListHtml += "<td>"+o.update_time+"</td>";
			dataListHtml += "<td>"+(o.state==0?("<a href='#' onclick='ajaxto(this,\"convertOperate.php?type=shipping&id="+o.id+"\")'>发货</a>&nbsp;&nbsp;<a href='#' onclick='linkto(this,\"convertOperate.php?type=modify&id="+o.id+"\")'>修改</a>"):"&nbsp;")+"</td>";
			dataListHtml += "</tr>";
		}
		if ( dataListHtml == "" && isNext ) { alert("已经是最后一页"); pageIndex--; return; }
		else { $("#dataList").html(dataListHtml); $("#pages").html(Math.ceil(dataList.total/pageSize)); $("#total").html(dataList.total); $("#current").html(pageIndex+1); $("#pager").show(); }
	});
}
function linkto( o, urlTo ) { var _a = urlTo.split('/'); var _this = $(o); if ( _a[0] != "http" ) { var _b = self.location.href.split('/'); delete _b[_b.length-1]; urlTo = _b.join('/') + urlTo; } self.location.href=urlTo; }
function ajaxto( o, urlTo ) {
	var _a = urlTo.split('/'); var _this = $(o);
	if ( _a[0] != "http" ) { var _b = self.location.href.split('/'); delete _b[_b.length-1]; urlTo = _b.join('/') + urlTo; }
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
</script>

<body>
<div class="body">

<fieldset>
	<legend>兑换历史记录</legend>
	<div class="row">
		<div class="span2" >
			<label>状态：</label>
			<select class="span2" id="state" name="state" >
				<option value="all">不限</option>
				<?php foreach ($states as $k=>$v) echo "<option value='$k'>$v</option>"; ?>
			</select>
		</div>
		<div class="span2">
			<label>起始日期：</label>
			<input style="height:30px;" class="span2 dtime" type="text" id="start" name="start" readonly onclick="WdatePicker({dateFmt:'yyyy-MM-dd'})"/>
		</div>
		<div class="span2">
			<label>截止日期：</label>
			<input style="height:30px;" class="span2 dtime" type="text" id="end" name="end" readonly onclick="WdatePicker({dateFmt:'yyyy-MM-dd'})"/>
		</div>
		<div class="span2">
			<label>UID：</label>
			<input style="height:30px" class="span2" type="text" id="uid" name="uid"/>
		</div>
		<div class="span2">
			<label>编号ID：</label>
			<input style="height:30px" class="span2" type="text" id="cool_num" name="cool_num"/>
		</div>
		<div class="span2">
			<label style="background-color:yellow">[用户未曾充值]</label>
		</div>
		<div style="float:right;">
			<label>&nbsp;</label>
			<input style="height:28px" class="btn" type="button" value="查&nbsp;&nbsp;询" onclick="query()" />
		</div>
	</div>
</fieldset>

<table class="table table-bordered table-condensed table-hover">
	<tr class="info">
		<th>用户UID</th>
		<th>用户编号</th>
		<th>用户昵称</th>
		<th>用户渠道</th>
		<th>兑换方式</th>
		<th>物品名称</th>
		<th>消耗货币</th>
		<th>当前货币</th>
		<th>用户备注</th>
		<th>创建时间</th>
		<th>操作状态</th>
		<th>操作人</th>
		<!-- <th>操作备注</th> -->
		<th>操作时间</th>
		<th>操作</th>
	</tr>
	<tbody id="dataList">
	</tbody>
</table>

<table width="900" border="0" cellpadding="5" cellspacing="0" align="center">
	<tr>
		<td height="25" id="pager" align="center" style="display:none;">
			共 <span id="total"></span>条 / <span id="pages"></span>页&nbsp;
			<div class="btn-group">
				<button class="btn" onclick="prePage()">前一页</button>
				<span><button class="btn" id="current"></button></span>
				<button class="btn" onclick="nextPage()">后一页</button>
			</div>
		</td>
	</tr>
</table>

</div>
</body>
