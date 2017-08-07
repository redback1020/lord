<?php
header("Content-type: text/html; charset=utf-8");
require_once '../manage/checkPriv.php';
require_once '../include/database.class.php';
$time = time();
$entryMoneys = array('coins'=>'乐豆报名','coupon'=>'乐券报名'/*待扩展*/);//报名货币
$isAjax = isset($_REQUEST['isAjax'])?intval($_REQUEST['isAjax']):0;
if ( $isAjax ) {
	$pageSize = $_REQUEST['pageSize'];
	$pageIndex = $pageSize * $_REQUEST['pageIndex'];
	$modelId = $_REQUEST['modelId'];
	$where = "`modelId` = 3 ";
	$sql = "SELECT * FROM `lord_game_room` WHERE {$where} ORDER BY `sort`, `id` DESC LIMIT {$pageIndex}, {$pageSize}";
	$res = $db->query($sql)->fetchAll(PDO::FETCH_ASSOC); if ( ! $res ) $res = array();
	foreach ( $res as $k => $v )
	{
		$res[$k]['showRules'] = $v['showRules'] ? json_decode($v['showRules'],1) : null;
		if ( ! $v['start'] ) {//随时开赛
			$res[$k]['start'] = '人满随时开赛';
		} elseif ( 0 < $v['start'] && $v['start'] <= 86400 ) {//每天定时开赛
			$res[$k]['start'] = '每天 '.date("H:i:s", strtotime(date("Y-m-d")) + $v['start']);
		} elseif ( 86400 * 1 < $v['start'] && $v['start'] <= 86400 * 8  ) {//每周定时开赛
			$res[$k]['start'] = '每周'.intval($v['start']/86400)." ".date("H:i:s", strtotime(date("Y-m-d")) + $v['start']%86400);
		} elseif ( 86400 * 8 < $v['start'] && $v['start'] <= 86400 * 39 ) {//每月定时开赛
			$days = intval($v['start']/86400) - 7;
			if ( $days < 10 ) $days = '0'.$days;
			$res[$k]['start'] = '每月'.$days." ".date("H:i:s", strtotime(date("Y-m-d")) + $v['start']%86400);
		} else {//指定日期开赛
			$res[$k]['start'] = date("Y-m-d H:i:s", $v['start']);
		}
		$prizes = array();
		$res[$k]['awardRule'] = $v['awardRule'] = $v['awardRule'] ? json_decode($v['awardRule'],1) : array();
		foreach ( $v['awardRule'] as $kk => $vv )
		{
			$prizes[] = "$kk: ".$vv['text'];
		}
		$res[$k]['prizes'] = join("<br/>", $prizes);
		$res[$k]['money'] = isset($entryMoneys[$v['entryMoney']])?$entryMoneys[$v['entryMoney']]:'未知';
		$res[$k]['cost'] = $v['entryCost']>0?$v['entryCost']:'免费报名';
		$res[$k]['update_date'] = $v['update_time']?date("Y-m-d H:i:s", $v['update_time']):'&nbsp;';
	}
	$array['data'] = $res;
	$sql = "SELECT count(*) as cn FROM `lord_game_room` WHERE {$where} ";
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
var ut_now = <?=$time?>;

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
	$.post("lobbyMRoomList.php",{
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
				dataListHtml += "<td>"+o.roomId+"</td>";
				dataListHtml += "<td>"+o.mode+"<br/>"+o.name+"</td>";
				dataListHtml += "<td>全局"+(o.isOpen>0?'开启':'关闭')+"<br/>手机"+(o.isMobi>0?'开启':'关闭')+"<br/>最低版本"+o.verMin+"</td>";
				dataListHtml += "<td>";
				if (o.showRules) {
					//当前版本只处理单条规则，暂不处理多条规则并列的现象
					o.showRules = o.showRules[0];
					var rules = [];
					if (o.showRules.channel) rules.push("许可: "+o.showRules.channel.join(' '));
					if (o.showRules.channot) rules.push("屏蔽: "+o.showRules.channot.join(' '));
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
				dataListHtml += "<td>"+o.prizes+"</td>";
				dataListHtml += "<td>"+o.start+"</td>";
				dataListHtml += "<td>"+o.money+"</td>";
				dataListHtml += "<td>"+o.cost+"</td>";
				dataListHtml += "<td>"+o.entryMin+" / "+o.entryMax+"</td>";
				dataListHtml += "<td>"+o.outValue+"</td>";
				dataListHtml += "<td>"+o.update_date+"</td>";
				dataListHtml += "<td>"+(o.is_del>0?"删除":"&nbsp;")+"</td>";
				dataListHtml += "<td><a href='#' onclick='ajaxto(this,\"lobbyMRoomAdd.php?type=delete&id="+o.id+"\")'>删除</a>&nbsp;&nbsp;<a href='#' onclick='linkto(this,\"lobbyMRoomAdd.php?type=modify&id="+o.id+"\")'>修改</a><br><a href='#' onclick='viewimg(this,"+o.roomId+")'>查看图片</a></td>";
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
	<legend>比赛场次 - 列表</legend>
	<div class="row">
		<div span="span1" style="float:right;">
			<label>&nbsp;</label>
			<input type="button" value="创&nbsp;&nbsp;建" onclick="linkto(this,'lobbyMRoomAdd.php')" class="btn"/>
		</div>
	</div>
</fieldset>

<table class="table table-bordered table-condensed table-hover">
	<tr class="info">
		<th>房间编号</th>
		<th>赛事/房间</th>
		<th>全局/手机/版本</th>
		<th>渠道/乐豆/时间</th>
		<th>名次奖励</th>
		<th>报名时间</th>
		<th>报名货币</th>
		<th>报名费用</th>
		<th>报名人数</th>
		<th>淘汰次序</th>
		<th>更新时间</th>
		<th>状态</th>
		<th>操作</th>
	</tr>
	<tbody id="dataList"></tbody>
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
