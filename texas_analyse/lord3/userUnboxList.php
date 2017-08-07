<?php
header("Content-type: text/html; charset=utf-8");
require_once '../manage/checkPriv.php';
require_once '../include/database.class.php';
function str_cut($str, $length, $start=0, $charset="utf-8", $suffix='') {
	if(function_exists("mb_substr")) {
		$slice = mb_substr($str, $start, $length, $charset);
	} elseif(function_exists('iconv_substr')) {
		$slice = iconv_substr($str, $start, $length, $charset);
		$slice = false === $slice ? '' : $slice;
	} else {
		$preg['utf-8']  = "/[\x01-\x7f]|[\xc2-\xdf][\x80-\xbf]|[\xe0-\xef][\x80-\xbf]{2}|[\xf0-\xff][\x80-\xbf]{3}/";
		$preg['gb2312'] = "/[\x01-\x7f]|[\xb0-\xf7][\xa0-\xfe]/";
		$preg['gbk']    = "/[\x01-\x7f]|[\x81-\xfe][\x40-\xfe]/";
		$preg['big5']   = "/[\x01-\x7f]|[\x81-\xfe]([\x40-\x7e]|\xa1-\xfe])/";
		preg_match_all($preg[$charset], $str, $match);
		$slice = join("",array_slice($match[0], $start, $length));
	}
	return $suffix ? $slice.$suffix : $slice;
}
$ut_now = time();
$froms = array();
$sql = "SELECT `id`,`subject` FROM `lord_game_topic` ORDER BY `sort` ASC, `id` DESC";
$topiclist = $db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
$topiclist = $topiclist ? $topiclist : array();
foreach ( $topiclist as $k => $v ) {
	$froms[$v['id']] = "[活动]".str_cut($v['subject'],10);
}
$sql = "SELECT `id`,`name` FROM `lord_game_tesk`";
$teskslist = $db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
$teskslist = $teskslist ? $teskslist : array();
foreach ( $teskslist as $k => $v ) {
	$froms[$v['id']] = "[任务]".str_cut($v['name'],10);
}
$isAjax = isset($_REQUEST['isAjax'])?intval($_REQUEST['isAjax']):0;
if ($isAjax) {
	$pageSize = $_REQUEST['pageSize'];
	$pageIndex = $pageSize * $_REQUEST['pageIndex'];
	$fromuid = $_REQUEST['fromuid'];
	$uid = intval($_REQUEST['uid']);
	$cool_num = intval($_REQUEST['cool_num']);
	// $subject = trim($_REQUEST['subject']);
	$state = intval($_REQUEST['state']);
	$where = "1=1";
	$order = "ORDER BY `id` DESC";
	if ($fromuid == 'all') $where .= "";//全部
	elseif ($fromuid == 0) $where .= " AND `fromuid`=0";//系统全局邮件，不可删除，展示7天
	elseif ($fromuid > 0) $where .= " AND `fromuid`=$fromuid";//活动邮件,fromuid为活动id
	if ($uid) {
		$where .= " AND `uid` = $uid";//某个用户的邮件，有uid时不考虑cool_num
		$order = "ORDER BY `sort` ASC";
	} elseif ($cool_num) {
		$sql = "SELECT `uid` FROM `lord_game_user` WHERE `cool_num` = $cool_num";
		$res = $db->query($sql)->fetchAll();
		$res = ( $res && is_array($res) ) ? $res : array();
		$uids = array();
		foreach ( $res as $k => $v )
		{
			$uids[]=intval($v['uid']);
		}
		if (count($uids)>1) {
			$where .= " AND `uid` IN (".join(',',$uids).")";//考虑重复的cool_num问题
			$order = "ORDER BY `uid` DESC, `sort` ASC";
		} else {
			$where .= " AND `uid` = ".($uids?$uids[0]:0);
			$order = "ORDER BY `sort` ASC";
		}
	}
	// if ($subject) $where .= " AND `subject` LIKE '%{$subject}%'";//标题
	if ($state ==0) $where .= "";//全部
	elseif ($state == 1) $where .= " AND `is_read` = 0";//未读
	elseif ($state == 2) $where .= " AND `is_read` = 1 AND `is_del` = 0";//未处理
	elseif ($state == 3) $where .= " AND `is_del` = 1";//已处理
	$sql = "SELECT * FROM `lord_user_unbox` WHERE {$where} {$order} LIMIT {$pageIndex}, {$pageSize}";
	// echo $sql;
	$res = $db->query($sql)->fetchAll();
	$res = ( $res && is_array($res) ) ? $res : array();
	foreach ( $res as $k => $v )
	{
		$res[$k]['from'] = isset($froms[$v['fromuid']]) ? $froms[$v['fromuid']] : '';
		$res[$k]['subject'] = $v['subject'] ? str_cut($v['subject'],18) : '&nbsp;';
		$res[$k]['content'] = $v['content'] ? str_cut($v['content'],24) : '&nbsp;';
		$items = $v['items'] ? json_decode($v['items'],1) : array();
		if ( isset($items['propItems']) ) { $items['propItems'] = array_values($items['propItems']); }
		elseif ( isset($items['props']) ) { $items['propItems'] = array_values($items['props']); unset($items['props']); }
		elseif ( isset($items['items']) ) { $items['propItems'] = array_values($items['items']); unset($items['items']); }
		$res[$k]['prizes'] = $items ? $items : null;
		$res[$k]['create_time'] = $v['create_time'] ? date("Y-m-d H:i:s", $v['create_time']) : '&nbsp;';
		$res[$k]['update_time'] = $v['update_time'] ? date("Y-m-d H:i:s", $v['update_time']) : '&nbsp;';
	}
	$array['data'] = $res;
	$sql = "SELECT count(*) as cn FROM `lord_user_unbox` WHERE {$where} ";
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
	var _html = '<tr class="table-body viewimg"><td colspan="11">';
		_html+= '<a href="http://gt2.youjoy.tv/ddzgamefile/user_inbox/'+id+'.png" target="_blank"><img src="http://gt2.youjoy.tv/ddzgamefile/user_inbox/'+id+'.png" style="height:100px;" /></a>';
		_html+= '　';
		_html+= '<a href="http://gt2.youjoy.tv/ddzgamefile/user_inbox/'+id+'.png" target="_blank"><img src="http://gt2.youjoy.tv/ddzgamefile/user_inbox/'+id+'.png" style="height:100px;" /></a>';
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
	var fromuid = $('#fromuid').val();
	var uid = $('#uid').val();
	var cool_num = $('#cool_num').val();
	var cool_num = $('#cool_num').val();
	// var subject = $('#subject').val();
	var state = $('#state').val();
	$.post("userUnboxList.php",{
		pageSize: pageSize,
		pageIndex: index,
		fromuid: fromuid,
		uid: uid,
		cool_num: cool_num,
		// subject: subject,
		state: state,
		isAjax: 1
	},function(result){
		if(result!=null && result!=""){
			var dataList=eval("("+result+")");
			var dataListHtml = "";
			for(var i=0;i<dataList.data.length;i++){
				var o = dataList.data[i];
				dataListHtml += "<tr class='table-body'>";
				dataListHtml += "<td>"+o.id+"</td>";
				dataListHtml += "<td>"+(o.fromuid==0?"系统全局":(o.fromuid>0&&o.fromuid<10000?("活动ID:"+o.fromuid):(o.fromuid>10000&&o.fromuid<20000?"任务ID:":o.fromuid)))+"</td>";
				// dataListHtml += "<td>"+(o.fromuid==0?"系统全局":o.from)+"</td>";
				dataListHtml += "<td>"+(o.uid>0?o.uid:"全部用户")+"</td>";
				dataListHtml += "<td>"+o.subject+"</td>";
				dataListHtml += "<td>"+o.content+"</td>";
				dataListHtml += "<td>";
				if (o.prizes) {
					var prize = [];
					if (o.prizes.gold) prize.push("乐币: "+o.prizes.gold+"个");
					if (o.prizes.coupon) prize.push("乐券: "+o.prizes.coupon+"个");
					if (o.prizes.coins) prize.push("乐豆: "+o.prizes.coins+"个");
					if (o.prizes.lottery) prize.push("抽奖: "+o.prizes.lottery+"次");
					if (o.prizes.propItems) {
						var items = o.prizes.propItems;
						for (var j=0; j<items.length; j++) {
							prize.push("道具: "+items[j].name+" x "+items[j].num+"个");
						}
					}
					if (o.prizes.other) prize.push("其它: "+o.prizes.other);
					dataListHtml += prize.join('<br>');
				} else {
					dataListHtml += "&nbsp;";
				}
				dataListHtml += "</td>";
				dataListHtml += "<td>"+o.sort+"</td>";
				dataListHtml += "<td>"+(o.is_read>0?"已读":"未读")+"<br>"+(o.is_del>0?"已处理":"未处理")+"</td>";
				dataListHtml += "<td>"+(o.type>0?"7天":"1月")+"</td>";
				dataListHtml += "<td>"+o.create_time+"<br>"+o.update_time+"</td>";
				dataListHtml += "<td>&nbsp;</td>";
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
	<legend>用户废件箱－邮件记录</legend>
	<div class="row">
		<div class="span2" >
			<label>发件人：</label>
			<select class="span2" id="fromuid" name="fromuid">
				<option value="all">全部</option>
				<option value="0">系统全局</option>
				<?php foreach ($froms as $key => $val) { echo '<option value="'.$key.'">'.$val.'</option>'; } ?>
			</select>
		</div>
		<div class="span2">
			<label>收件人UID：</label>
			<input id="uid" name="uid" value="" type="text" class="span2" />
		</div>
		<div class="span2">
			<label>收件人用户编号(ID)：</label>
			<input id="cool_num" name="cool_num" value="" type="text" class="span2" />
		</div>
		<!-- <div class="span2">
			<label>邮件标题：</label>
			<input id="subject" name="subject" value="" type="text" class="span2" />
		</div> -->
		<div class="span2">
			<label>邮件状态：</label>
			<select class="span2" id="state">
				<option value="0">全部</option>
				<option value="1">未读</option>
				<option value="2">未处理</option>
				<option value="3">已处理</option>
			</select>
		</div>
		<div span="span1" style="float:right;">
			<label>&nbsp;</label>
			<input type="button" value="查&nbsp;&nbsp;询" onclick="query()" class="btn"/>
			<input type="button" value="发&nbsp;&nbsp;件" onclick="linkto(this,'userMailAdd.php')" class="btn"/>
		</div>
	</div>
</fieldset>

<table class="table table-bordered table-condensed table-hover">
	<tr class="info">
		<th>编号</th>
		<th>发件人</th>
		<th>收件人UID</th>
		<th>标题</th>
		<th>内容</th>
		<th>奖励</th>
		<th>排序</th>
		<th>状态</th>
		<th>期限</th>
		<th>发件/变更</th>
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
