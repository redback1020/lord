<?php
header("Content-type: text/html; charset=utf-8");
require_once '../manage/checkPriv.php';
require_once '../include/database.class.php';
require_once '../manage/getipCity.php';
$channels_durty = array("51vappFT","alitech","alizhibo","android","aostv","appstore","appvtion","banana","beimi","bianfeng","bitgames","boyakeno","cik","CMCC","dou","drpengvoice","duole","duwei-iqiyi","fanmi","gangfeng","gao","hangkeweiye","haoshi","henangd","hifly","huawei","huaweiconsumer","icntv","IMT","infoTM","iptvhebei","jingling","jinnuowei","jinruixian","jinya1","jinyatai","jiuzhou","kuaiyou","laimeng","landiankechuang","leyou","lianyi","nibiru","pengrunsen","qiwangldkc","qiwangyfcz","qpod","qvod","realplay","robotplugin","ruixiangtongxin","runhe","shitouer","the5","threelegsfrog","thtflingyue","tshifi","ujob","uprui","UTskd","vsoontech","wanhuatong","wanmei","wanweitron","whatchannel","wobo","xiaomi","xinhancommon","xinhantena","xinhanvsoontech","xinhanyixinte","xunlei","xunma","yangcong","youjoytest","zuoqi");
$ut_now = time();
if ( isset($_REQUEST['isAjax']) && intval($_REQUEST['isAjax']) )
{
	$pageSize = $_REQUEST['pageSize'];
	$pageIndex = $pageSize * $_REQUEST['pageIndex'];
	$where = "";
	$channel = trim($_REQUEST['channel']);
	if ( $channel && $channel != 'all' ) $where .= " AND u.channel = '$channel'";
	$uid = intval($_REQUEST['uid']);
	if ( $uid > 0 ) $where .= " AND u.uid = $uid";
	$cool_num = intval($_REQUEST['cool_num']);
	if ( $cool_num > 0 ) $where .= " AND u.cool_num = $cool_num";
	$nick = trim($_REQUEST['nick']);
	if ( $nick ) $where .= " AND u.nick = '$nick'";
	$start = trim($_REQUEST['start']);
	$end = trim($_REQUEST['end']);
	if ( $start && !$end ) $where .= " AND a.add_time>='$start'";
	elseif ( !$start && $end ) $where .= " AND a.add_time<='$end 23:59:59'";
	elseif ( $start && $end ) $where .= " AND a.add_time>='$start' AND a.add_time<='$end 23:59:59'";
	$last_start = trim($_REQUEST['last_start']);
	$last_end = trim($_REQUEST['last_end']);
	if ( $last_start && !$last_end ) $where .= " AND a.last_login >= '$last_start'";
	elseif ( !$last_start && $last_end ) $where .= " AND a.last_login <= '$last_end 23:59:59'";
	elseif ( $last_start && $last_end ) $where .= " AND a.last_login >= '$last_start' AND a.last_login <= '$last_end 23:59:59'";

	$sql = "select u.uid, u.cool_num, u.nick, u.gold, u.coins, u.coupon, u.lottery, u.channel, a.version, a.matches, a.win, a.add_time, a.last_login, a.last_ip FROM lord_game_user u LEFT JOIN lord_game_analyse a ON a.uid = u.uid WHERE 1=1 $where ORDER BY u.uid DESC LIMIT $pageIndex, $pageSize";
	$row = $db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
	$ipCity = new ipCity();
	foreach ( $row as $k => $v )
	{
		$row[$k]['last_ip'] = $v['last_ip'].' '.$ipCity->getCity($v['last_ip']);
	}
	$arrays['data'] = $row;

	if ( $start || $end || $last_start || $last_end ) {
		$sql = "select count(u.uid) as count FROM lord_game_user u LEFT JOIN lord_game_analyse a ON a.uid = u.uid WHERE 1=1 $where";
	} else {
		$sql = "SELECT count(uid) as count FROM lord_game_user u WHERE 1=1 $where";
	}
	$res = $db->query($sql)->fetch(PDO::FETCH_ASSOC);
	$arrays['count'] = $res['count'];
	echo json_encode($arrays);
	exit;
}
?>
<script type="text/javascript" src="../js/jquery.js"></script>
<script type="text/javascript" src="../js/My97DatePicker/WdatePicker.js"></script>
<link rel="stylesheet" type="text/css" href="../bootstrap/css/bootstrap.min.css" />
<style type="text/css">
table.table{ font-size: 12px;}
table.table th{ white-space: nowrap;}
</style>
<script>
var ut_now = <?=$ut_now?>;
var pageSize = 20; var pageIndex = 0;
$(function(){ query(); });
function query() { pageIndex = 0; queryByPage(pageIndex); }
function prePage() { if ( pageIndex==0 ) { alert("已经是第一页"); return; } pageIndex--; queryByPage(pageIndex); }
function nextPage() { pageIndex++; queryByPage(pageIndex, true); }
function queryByPage( index, isNext ) {
	$.post('userInfo.php?isAjax=1&pageSize='+pageSize+'&pageIndex='+index, {
		channel:$('#channel').val(),
		uid:$('#uid').val(),
		cool_num:$('#cool_num').val(),
		nick:$('#nick').val(),
		start:$('#start').val(),
		end:$('#end').val(),
		last_start:$('#last_start').val(),
		last_end:$('#last_end').val()
	}, function( result ) {
		if( result == null || result == "" ) return alert("获取数据失败！");
		var data = eval("("+result+")");
		var html = "";
		for( var i=0; i<data.data.length; i++ ) {
			var o = data.data[i];
			html += "<tr>";
			html += "<td><a href='userAccount.php?uid="+o.uid+"'>"+o.uid+"</a></td>";
			html += "<td>"+o.cool_num+"</td>";
			html += "<td>"+o.nick+"</td>";
			html += "<td>"+o.gold+"</td>";
			html += "<td>"+o.coins+"</td>";
			html += "<td>"+o.coupon+"</td>";
			html += "<td>"+o.lottery+"</td>";
			html += "<td>"+o.matches+"</td>";
			html += "<td>"+o.win+"</td>";
			html += "<td>"+o.channel+"</td>";
			html += "<td>"+o.version+"</td>";
			html += "<td>"+o.add_time+"</td>";
			html += "<td>"+o.last_login+"</td>";
			html += "<td>"+o.last_ip+"</td>";
			html += "</tr>";
		}
		$("#dataList").html(html);
		if ( html == "" && isNext ) { alert("已经是最后一页"); pageIndex--; }
		else { $("#pages").html(Math.ceil(data.count/pageSize)); $("#count").html(data.count); $("#dataList").html(html); $("#pageIndex").html(pageIndex+1); $("#pager").show(); }
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
<div style="position:absolute;top:0;left:0;margin-left:10px;width:98%;">

<fieldset>
	<legend>查询所有用户数据</legend>
	<div class="row">
		<div class="span2" >
			<label>渠道：</label>
			<select class="span2" id="channel" name="channel">
				<option value="all">不限</option>
				<?php
				$file = __DIR__ . "/data/cache_channel";
				if ( is_file($file) && mt_rand(0, 10) ) {
					$channels = json_decode(file_get_contents($file), 1);
				} else {
					$sql = "select `channel` from `lord_game_user` where `channel` != '' group by `channel`";
					$channels = $db->query($sql)->fetchAll();
					$res = file_put_contents($file, json_encode($channels));
				}
				foreach ($channels as $val) {
					if (in_array($val['channel'], $channels_durty)) { continue; }
					echo '<option value="'.$val['channel'].'">'.$val['channel'].'</option>';
				}
				?>
			</select>
		</div>
		<div class="span2">
			<label>UID：</label>
			<input class="span2" type="text" id="uid" name="uid" value="<?=isset($_GET['uid'])?$_GET['uid']:''?>" style="height:30px"/>
		</div>
		<div class="span2">
			<label>编号ID：</label>
			<input class="span2" type="text" id="cool_num" name="cool_num" style="height:30px"/>
		</div>
		<div class="span2">
			<label>昵称：</label>
			<input class="span2" type="text" id="nick" name="nick" style="height:30px"/>
		</div>
		<div class="span2">
			<label>注册时间：</label>
			<input style="height:30px;" class="span2" type="text" id="start" class="textbox dtime" readonly onclick="WdatePicker({dateFmt:'yyyy-MM-dd'})"/>
			<input style="height:30px;" class="span2" type="text" id="end" class="textbox dtime" readonly onclick="WdatePicker({dateFmt:'yyyy-MM-dd'})"/>
		</div>
		<div class="span2">
			<label>上次登陆：</label>
			<input style="height:30px;" class="span2" type="text" id="last_start" class="textbox dtime" readonly onclick="WdatePicker({dateFmt:'yyyy-MM-dd'})"/>
			<input style="height:30px;" class="span2" type="text" id="last_end" class="textbox dtime" readonly onclick="WdatePicker({dateFmt:'yyyy-MM-dd'})"/>
		</div>
		<div span="span1" style="float:right;">
			<label>&nbsp;</label>
			<input type="button" value="查&nbsp;&nbsp;询" onClick="query()" class="btn" />
		</div>
	</div>
</fieldset>

<table class="table table-bordered table-condensed table-hover">
	<tbody>
		<tr class="info">
			<td nowrap><strong>UID</strong></td>
			<td nowrap><strong>编号ID</strong></td>
			<td nowrap><strong>昵称</strong></td>
			<td nowrap><strong>乐币</strong></td>
			<td nowrap><strong>乐豆</strong></td>
			<td nowrap><strong>乐券</strong></td>
			<td nowrap><strong>抽奖数</strong></td>
			<td nowrap><strong>局数</strong></td>
			<td nowrap><strong>胜场</strong></td>
			<td nowrap><strong>渠道</strong></td>
			<td nowrap><strong>版本</strong></td>
			<td nowrap><strong>注册时间</strong></td>
			<td nowrap><strong>上次登录</strong></td>
			<td nowrap><strong>上次IP</strong></td>
		</tr>
	</tbody>
	<tbody id="dataList">
	</tbody>
</table>

<table width="900" border="0" cellpadding="5" cellspacing="0" align="center">
	<tr>
		<td height="25" id="pager" align="center" style="display:none;">
			共 <span id="count"></span>条 / <span id="pages"></span>页&nbsp;
			<div class="btn-group">
				<button class="btn" onclick="prePage()">前一页</button>
				<span><button class="btn" id="pageIndex"></button></span>
				<button class="btn" onclick="nextPage()">后一页</button>
			</div>
		</td>
	</tr>
</table>

</div>

</body>
