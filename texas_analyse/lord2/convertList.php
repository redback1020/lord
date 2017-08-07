<?php
header("Content-type: text/html; charset=utf-8");
require_once '../manage/checkPriv.php';
require_once '../include/database.class.php';//
$channels_durty = array("51vappFT","alitech","alizhibo","android","aostv","appstore","appvtion","banana","beimi","bianfeng","bitgames","boyakeno","cik","CMCC","dou","drpengvoice","duole","duwei-iqiyi","fanmi","gangfeng","gao","hangkeweiye","haoshi","henangd","hifly","huawei","huaweiconsumer","icntv","IMT","infoTM","iptvhebei","jingling","jinnuowei","jinruixian","jinya1","jinyatai","jiuzhou","kuaiyou","laimeng","landiankechuang","leyou","lianyi","nibiru","pengrunsen","qiwangldkc","qiwangyfcz","qpod","qvod","realplay","robotplugin","ruixiangtongxin","runhe","shitouer","the5","threelegsfrog","thtflingyue","tshifi","ujob","uprui","UTskd","vsoontech","wanhuatong","wanmei","wanweitron","whatchannel","wobo","xiaomi","xinhancommon","xinhantena","xinhanvsoontech","xinhanyixinte","xunlei","xunma","yangcong","youjoytest","zuoqi");
$itemtypes = array('coupon2mobifee'=>'乐券兑话费');
$ut_now = time();
$isAjax = isset($_REQUEST['isAjax'])?intval($_REQUEST['isAjax']):0;
if ($isAjax) {
	$pageSize = $_REQUEST['pageSize'];
	$pageIndex = $pageSize * $_REQUEST['pageIndex'];
	$channel = $_REQUEST['channel'];
	$state = $_REQUEST['state'];
	$where = "1=1 ";
	if ($channel != 'all') $where .= " AND `channel`='".$channel."'";
	if ($state == 'default') { $where .= " AND `state`<>2"; }
	elseif ($state == 'all') {}
	else{ $where .= " AND `state`={$state}"; }
	$sql = "SELECT * FROM `lord_list_convert` WHERE {$where} ORDER BY `sort`, `id` DESC LIMIT {$pageIndex}, {$pageSize}";
	// echo $sql;
	$res = $db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
	$res = ( $res && is_array($res) ) ? $res : array();
	foreach ( $res as $k => $v )
	{
		$res[$k]['file'] = "http://gt2.youjoy.tv/ddzgamefile/mall_exchange/".$v['fileId'].".png";
		$res[$k]['create_time'] = $v['create_time'] ? date("Y-m-d H:i:s", $v['create_time']) : "";
		$res[$k]['update_time'] = $v['update_time'] ? date("Y-m-d H:i:s", $v['update_time']) : "";
	}
	$array['data'] = $res;
	$sql = "SELECT count(*) as cn FROM `lord_list_convert` WHERE {$where} ";
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
var ut_now = <?=$ut_now?>;
var pageSize = 10; var pageIndex = 0;
$(function(){ query(); });
function query() { pageIndex = 0; queryByPage(pageIndex); }
function prePage() { if ( pageIndex==0 ) { alert("已经是第一页"); return; } pageIndex--; queryByPage(pageIndex); }
function nextPage() { pageIndex++; queryByPage(pageIndex, true); }
function queryByPage( index, isNext ) {
	var channel = $('#channel').val();
	var state = $('#state').val();
	$.post("convertList.php",{
		pageSize: pageSize,
		pageIndex: index,
		channel: channel,
		state: state,
		isAjax: 1
	},function(result){
		if(result!=null && result!=""){
			var dataList=eval("("+result+")"); var dataListHtml = "";
			for ( var i=0; i<dataList.data.length; i++ ) {
				var o = dataList.data[i];
				dataListHtml += "<tr class='table-body'>";
				dataListHtml += "<td>"+o.id+"</td>";
				dataListHtml += "<td>"+o.sort+"</td>";
				dataListHtml += "<td>"+'<a href="'+o.file+'" target="_blank"><img src="'+o.file+'" style="height:40px;" /></a>'+"</td>";
				dataListHtml += "<td>"+(o.channel!=""?o.channel:"&nbsp;")+"</td>";
				dataListHtml += "<td>"+o.title+"</td>";
				dataListHtml += "<td>"+o.value+(o.type=="coupon2mobifee"?"元话费":"？？")+"</td>";
				dataListHtml += "<td>"+o.price+(o.type=="coupon2mobifee"?"乐券":"？？")+"</td>";
				dataListHtml += "<td>"+(o.is_onsale==1?o.onsale:"&nbsp;")+"</td>";
				dataListHtml += "<td>"+(o.store==-1?"不限":o.store)+"</td>";
				dataListHtml += "<td>"+(o.state==2?"已删除":(o.state==1?"已下架":"正常"))+"</td>";
				// dataListHtml += "<td>"+o.create_time+"<br>"+o.update_time+"</td>";
				dataListHtml += "<td>"+o.update_time+"</td>";
				dataListHtml += "<td>"+(o.state!=2?"<a href='#' onclick='ajaxto(this,\"convertAdd.php?type=delete&id="+o.id+"\")'>删除</a>&nbsp;&nbsp;":"")+(o.state==0?"<a href='#' onclick='ajaxto(this,\"convertAdd.php?type=offline&id="+o.id+"\")'>下架</a>&nbsp;&nbsp;":"")+(o.state==1?"<a href='#' onclick='ajaxto(this,\"convertAdd.php?type=online&id="+o.id+"\")'>上架</a>&nbsp;&nbsp;":"")+"<a href='#' onclick='linkto(this,\"convertAdd.php?type=modify&id="+o.id+"\")'>修改</a></td>";
				dataListHtml += "</tr>";
			}
			if ( dataListHtml=="" && isNext ) { alert("已经是最后一页"); pageIndex--; }
			else { $("#pageNum").html(Math.ceil(dataList.count/pageSize)); $("#count").html(dataList.count); $("#dataList").html(dataListHtml); $("#pageIndex").html(pageIndex+1); $("#pagination").show(); }
		}
		else { alert("获取数据失败！"); }
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
<div style="position:absolute;left:0;top:0;padding:0 10px;">

<fieldset>
	<legend>兑换项 - 列表</legend>
	<div class="row">
		<div class="span2" >
			<label>专属渠道：</label>
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
				<option value="default">默认</option>
				<option value="all">全部</option>
				<option value="0">正常</option>
				<option value="1">已下架</option>
				<option value="2">已删除</option>
			</select>
		</div>
		<div span="span1" style="float:right;">
			<label>&nbsp;</label>
			<input type="button" value="查&nbsp;&nbsp;询" onclick="query()" class="btn"/>
			<input type="button" value="创&nbsp;&nbsp;建" onclick="linkto(this,'convertAdd.php')" class="btn"/>
		</div>
	</div>
</fieldset>

<table class="table table-bordered table-condensed table-hover">
	<tr class="info">
		<th>编号</th>
		<th>排序</th>
		<th>图片</th>
		<th>专属渠道</th>
		<th>名称</th>
		<th>兑换值</th>
		<th>价格值</th>
		<th>促销</th>
		<th>库存</th>
		<th>状态</th>
		<!-- <th>创建/更新</th> -->
		<th>更新</th>
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
