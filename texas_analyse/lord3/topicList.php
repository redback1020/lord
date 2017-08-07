<?php
header("Content-type: text/html; charset=utf-8");
require_once '../manage/checkPriv.php';
require_once '../include/database.class.php';
$channels_durty = array("51vappFT","alitech","alizhibo","android","aostv","appstore","appvtion","banana","beimi","bianfeng","bitgames","boyakeno","cik","CMCC","dou","drpengvoice","duole","duwei-iqiyi","fanmi","gangfeng","gao","hangkeweiye","haoshi","henangd","hifly","huawei","huaweiconsumer","icntv","IMT","infoTM","iptvhebei","jingling","jinnuowei","jinruixian","jinya1","jinyatai","jiuzhou","kuaiyou","laimeng","landiankechuang","leyou","lianyi","nibiru","pengrunsen","qiwangldkc","qiwangyfcz","qpod","qvod","realplay","robotplugin","ruixiangtongxin","runhe","shitouer","the5","threelegsfrog","thtflingyue","tshifi","ujob","uprui","UTskd","vsoontech","wanhuatong","wanmei","wanweitron","whatchannel","wobo","xiaomi","xinhancommon","xinhantena","xinhanvsoontech","xinhanyixinte","xunlei","xunma","yangcong","youjoytest","zuoqi");
$ut_now = time();
$isAjax = isset($_REQUEST['isAjax'])?intval($_REQUEST['isAjax']):0;
if ($isAjax) {
	$pageSize = $_REQUEST['pageSize'];
	$pageIndex = $pageSize * $_REQUEST['pageIndex'];
	$channel = $_REQUEST['channel'];
	$state = $_REQUEST['state'];
	$where = "1=1 ";
	if ($channel != '') $where .= " AND `channel`='".$channel."'";
	if ($state != 'all') $where .= " AND `state`={$state}";
	$sql = "SELECT * FROM `lord_game_topic` WHERE {$where} ORDER BY `sort`, `id` DESC LIMIT {$pageIndex}, {$pageSize}";
	$res = $db->query($sql)->fetchAll(PDO::FETCH_ASSOC); if ( ! $res ) $res = array();
	foreach ( $res as $k => $v )
	{
		$res[$k]['channel'] = $v['channel'] ? str_replace(' ','<br/>',$v['channel']) : '&nbsp;';
		$res[$k]['channot'] = $v['channot'] ? str_replace(' ','<br/>',$v['channot']) : '&nbsp;';
		$res[$k]['content'] = $v['content'] ? str_replace("\n",'<br/>',$v['content']) : '&nbsp;';
		$res[$k]['prizes'] = $v['prizes'] ? json_decode($v['prizes'], 1) : null;
		$res[$k]['online_s'] = $v['start_time'] ? date("Y-m-d H:i:s", $v['start_time']) : "";
		$res[$k]['online_e'] = $v['end_time'] ? date("Y-m-d H:i:s", $v['end_time']) : "";
		$res[$k]['lobby_s'] = $v['start_lobby'] ? date("Y-m-d H:i:s", $v['start_lobby']) : "";
		$res[$k]['lobby_e'] = $v['end_lobby'] ? date("Y-m-d H:i:s", $v['end_lobby']) : "";
	}
	$array['data'] = $res;
	$sql = "SELECT count(*) as cn FROM `lord_game_topic` WHERE {$where} ";
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
		_html+= '<a href="http://gt2.youjoy.tv/ddzgamefile/topic_index/'+id+'.png" target="_blank"><img src="http://gt2.youjoy.tv/ddzgamefile/topic_index/'+id+'.png" style="height:100px;" /></a>';
		_html+= '　';
		_html+= '<a href="http://gt2.youjoy.tv/ddzgamefile/topic_lobby/'+id+'.png" target="_blank"><img src="http://gt2.youjoy.tv/ddzgamefile/topic_lobby/'+id+'.png" style="height:100px;" /></a>';
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
	var channel = $('#channel').val();
	var state = $('#state').val();
	$.post("topicList.php",{
		pageSize: pageSize,
		pageIndex: index,
		channel: channel,
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
				dataListHtml += "<td>"+o.channel+"</td>";
				dataListHtml += "<td>"+o.channot+"</td>";
				dataListHtml += "<td>"+o.subject+"</td>";
				dataListHtml += "<td>"+o.content+"</td>";
				dataListHtml += "<td>";
				if (o.prizes) {
					var prize = [];
					if (o.prizes.gold) prize.push("乐币: "+o.prizes.gold+"个");
					if (o.prizes.coupon) prize.push("乐券: "+o.prizes.coupon+"个");
					if (o.prizes.coins) prize.push("乐豆: "+o.prizes.coins+"个");
					if (o.prizes.lottery) prize.push("免费抽奖: "+o.prizes.lottery+"次");
					if (o.prizes.propItems) {
						var items = o.prizes.propItems;
						for (var j=0; j<items.length; j++) {
							prize.push("道具: "+items[j].name+" x "+items[j].num+"个");
						}
					}
					dataListHtml += prize.join('<br>');
				} else {
					dataListHtml += "&nbsp;";
				}
				dataListHtml += "</td>";
				dataListHtml += "<td>"+o.online_s+"<br>"+o.online_e+"</td>";
				dataListHtml += "<td>"+o.lobby_s+"<br>"+o.lobby_e+"</td>";
				dataListHtml += "<td>"+(o.state==2?"删除":(o.state==1?"下架":(o.start_time<ut_now&&ut_now<o.end_time?"在线":(ut_now>o.end_time?"过期":"待上"))))+(o.start_lobby<ut_now&&ut_now<o.end_lobby?"<br>热门":"")+"</td>";
				dataListHtml += "<td>"+o.sort+"</td>";
				dataListHtml += "<td><a href='#' onclick='ajaxto(this,\"topicAdd.php?type=delete&id="+o.id+"\")'>删除</a>&nbsp;&nbsp;<a href='#' onclick='ajaxto(this,\"topicAdd.php?type=offline&id="+o.id+"\")'>下架</a>&nbsp;&nbsp;<a href='#' onclick='linkto(this,\"topicAdd.php?type=modify&id="+o.id+"\")'>修改</a><br><a href='#' onclick='viewimg(this,"+o.id+")'>查看图片</a></td>";
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
	<legend>活动列表</legend>
	<div class="row">
		<div class="span2" >
			<label>许可渠道：</label>
			<select class="span2" id="channel" name="channel">
				<option value="">全部</option>
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
					if (in_array($val['channel'], $channels_durty)) {
						continue;
					}
					echo '<option value="'.$val['channel'].'">'.$val['channel'].'</option>';
				}
				?>
			</select>
		</div>
		<div class="span2">
			<label>状态：</label>
			<select class="span2" id="state">
				<option value="0">正常</option>
				<option value="1">下架</option>
				<option value="2">删除</option>
			</select>
		</div>
		<div span="span1" style="float:right;">
			<label>&nbsp;</label>
			<input type="button" value="查&nbsp;&nbsp;询" onclick="query()" class="btn"/>
			<input type="button" value="创&nbsp;&nbsp;建" onclick="linkto(this,'topicAdd.php')" class="btn"/>
		</div>
	</div>
</fieldset>

<table class="table table-bordered table-condensed table-hover">
	<tr class="info">
		<th>编号</th>
		<th>许可</th>
		<th>屏蔽</th>
		<th>标题</th>
		<th>内容</th>
		<th>奖励</th>
		<th>上线/下线</th>
		<th>热门/普通</th>
		<th>状态</th>
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

</div>
</body>
