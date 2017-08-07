<?php
header("Content-type: text/html; charset=utf-8");
require_once '../manage/checkPriv.php';
require_once '../include/database.class.php';//
$cds = array('1'=>'服装穿戴','2'=>'辅助道具','3'=>'充值乐币','4'=>'乐币换豆','5'=>'乐券兑换','6'=>'预留扩展');//分类归属
$sexs = array('0'=>'男女通用','1'=>'男性专用','2'=>'女性专用');//性别限制
$showins = array('0'=>'不做限制','1'=>'只在背包');//显示限制
$overlays = array('0'=>'自动抛弃','1'=>'增加数量','2'=>'增加时效','3'=>'增加持久');//叠加方式
$overlays = array('0'=>'自动抛弃','2'=>'增加时效');//叠加方式
$presents = array('0'=>'不可赠送','1'=>'可以赠送');//可否赠送
$pauses = array('0'=>'不可暂停','1'=>'可以暂停');//可否暂停
$repairs = array('0'=>'不可修复','1'=>'可以修复');//可否修复
$mutexs = array('0'=>'不会互斥','1'=>'同类互斥');//互斥方式
$usebys = array('0'=>'拥有即用','1'=>'缺失即用','2'=>'手动使用');//使用方式
$usedos = array('0'=>'不降数值','1'=>'降低数量','2'=>'降低时效','3'=>'降低持久');//使用运算
$usedos = array('0'=>'不降数值','2'=>'降低时效');//使用运算
$useass = array('0'=>'没有用途','1'=>'改变状态','2'=>'增加乐币','3'=>'增加代币','4'=>'增加乐豆','5'=>'增加乐券','6'=>'增加抽奖数','8'=>'获得物品','9'=>'获得实物');//使用用途
$useups = array('0'=>'自动销毁','1'=>'不做处理','2'=>'状态：已用完','3'=>'状态：已坏掉','4'=>'状态：待销毁','5'=>'预留扩展');//用完处理
$states = array('0'=>'正常','1'=>'下线');//上线状态
$states = array('0'=>'正常','1'=>'下线','2'=>'删除');//上线状态
$pds = $pdata = array();
$sql = "SELECT * FROM `lord_list_prop`";
$res = $db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
if ( !$res ) $res = array();
foreach ( $res as $k => $v ) {
	$pds[$v['id']] = $v['name'];//内置道具
	$pdata[$v['id']] = $v;
}
$ut_now = time();
$api = 'item';
$isAjax = isset($_REQUEST['isAjax']) ? intval($_REQUEST['isAjax']) : 0;
if ( $isAjax ) {
	$pageSize = $_REQUEST['pageSize'];
	$pageIndex = $pageSize * $_REQUEST['pageIndex'];
	$cd = $_REQUEST['cd'];
	$where = "1=1 ";
	if ( $cd != 'all' ) { $where .= " AND `cd` = $cd"; }
	$sql = "SELECT * FROM `lord_list_$api` WHERE {$where} ORDER BY `cd` ASC, `sort` ASC LIMIT {$pageIndex}, {$pageSize}";
	$res = $db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
	$res = ( $res && is_array($res) ) ? $res : array();
	foreach ( $res as $k => $v )
	{
		$res[$k]['cate'] = $cds[$v['cd']];
		$res[$k]['overlay'] = $overlays[$v['overlay']];
		$res[$k]['number'] = $v['number'] ? $v['number'] : "不限";
		$res[$k]['second'] = $v['second'] ? (($v['second']/86400).'天') : "永久";
		$res[$k]['points'] = $v['points'] ? $v['points'] : "无损";
		$res[$k]['present'] = $presents[$v['present']];
		$res[$k]['pause'] = $pauses[$v['pause']];
		$res[$k]['repair'] = $repairs[$v['repair']];
		$res[$k]['useas'] = $useass[$v['useas']];
		$res[$k]['state'] = $states[$v['state']];
		$res[$k]['create_time'] = $v['create_time'] ? date("Y-m-d H:i:s", $v['create_time']) : "";
		$res[$k]['update_time'] = $v['update_time'] ? date("Y-m-d H:i:s", $v['update_time']) : "";
	}
	$json['data'] = $res;
	$sql = "SELECT count(*) as cn FROM `lord_list_$api` WHERE {$where} ";
	$res = $db->query($sql)->fetch(PDO::FETCH_ASSOC);
	$json['count'] = $res['cn'];
	echo json_encode($json);
	exit;
}
?>
<script type="text/javascript" src="../js/jquery.js"></script>
<script type="text/javascript" src="../js/My97DatePicker/WdatePicker.js"></script>
<link type="text/css" href="../bootstrap/css/bootstrap.min.css" rel="stylesheet" />
<style type="text/css">
.body{position:absolute;left:0;top:0;padding:0 0 0 10px;width:98%;}
legend{margin-bottom: 10px;}
table.table{ font-size: 12px;margin-bottom:8px;}
table.table th,table.table td{ white-space: nowrap;}
</style>
<script>
var ut_now = <?=$ut_now?>;
var pageSize = 20; var pageIndex = 0;
$(function(){ query(); });
function query() { pageIndex = 0; queryByPage(pageIndex); }
function prev() { if ( pageIndex==0 ) { alert("已经是第一页"); return; } pageIndex--; queryByPage(pageIndex); }
function next() { pageIndex++; queryByPage(pageIndex, true); }
function queryByPage( index, isNext ) {
	var cd = $('#cd').val();
	$.post("<?=$api?>List.php",{
		pageSize: pageSize,
		pageIndex: index,
		cd: cd,
		isAjax: 1
	},function(result){
		if(result!=null && result!=""){
			var dataList=eval("("+result+")"); var dataListHtml = "";
			for ( var i=0; i<dataList.data.length; i++ ) {
				var o = dataList.data[i];
				dataListHtml += "<tr class='table-body'>";
				dataListHtml += "<td>"+o.id+"</td>";
				dataListHtml += "<td>"+(o.cate!=""?o.cate:"&nbsp;")+"</td>";
				dataListHtml += "<td>"+(o.name!=""?o.name:"&nbsp;")+"</td>";
				dataListHtml += "<td>"+o.number+"</td>";
				dataListHtml += "<td>"+o.second+"</td>";
				// dataListHtml += "<td>"+o.points+"</td>";
				// dataListHtml += "<td>"+o.present+"</td>";
				// dataListHtml += "<td>"+o.pause+"</td>";
				// dataListHtml += "<td>"+o.repair+"</td>";
				dataListHtml += "<td>"+o.useas+"</td>";
				dataListHtml += "<td>"+o.useto+"</td>";
				dataListHtml += "<td>"+o.state+"</td>";
				dataListHtml += "<td>"+o.sort+"</td>";
				dataListHtml += "<td>"+o.create_time+"</td>";
				dataListHtml += "<td>"+o.update_time+"</td>";
				dataListHtml += "<td>"+"<a href='#' onclick='ajaxto(this,\"<?=$api?>Add.php?subreq=delete&id="+o.id+"\")'>删除</a>&nbsp;&nbsp;"+"<a href='#' onclick='linkto(this,\"<?=$api?>Add.php?subreq=modify&id="+o.id+"\")'>修改</a>"+"</td>";
				dataListHtml += "</tr>";
			}
			if ( dataListHtml=="" && isNext ) { alert("已经是最后一页"); pageIndex--; }
			else { $("#pageNum").html(Math.ceil(dataList.count/pageSize)); $("#count").html(dataList.count); $("#dataList").html(dataListHtml); $("#pageIndex").html(pageIndex+1); $("#pager").show(); }
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
<div class="body">

<fieldset>
	<legend>物品 - 列表</legend>
	<div class="row">
		<div class="span2">
			<label>分类归属</label><?php $var = 'cd';?>
			<select id="<?=$var?>" class="span2"><option value="all">全部</option><?php foreach (${$var.'s'} as $k => $v) { echo "<option value='$k'".($data && $data[$var]==$k ? " selected='selected'" : "").">$v</option>"; } ?></select></td>
		</div>
		<div span="span1" style="float:right;">
			<label>&nbsp;</label>
			<input class="btn" type="button" value="查&nbsp;&nbsp;询" onclick="query()" />
			<input class="btn" type="button" value="创&nbsp;&nbsp;建" onclick="linkto(this,'<?=$api?>Add.php')" />
		</div>
	</div>
</fieldset>

<table class="table table-bordered table-condensed table-hover">
	<tr class="info">
		<th>编号</th>
		<th>分类</th>
		<th>名称</th>
		<th>叠加数量</th>
		<th>可用时效</th>
		<!-- <th>可用持久</th> -->
		<!-- <th>可否赠送</th> -->
		<!-- <th>可否暂停</th> -->
		<!-- <th>可否修复</th> -->
		<th>使用用途</th>
		<th>使用效值</th>
		<th>上线状态</th>
		<th>显示顺序</th>
		<th>创建时间</th>
		<th>更新时间</th>
		<th>操作</th>
	</tr>
	<tbody id="dataList">
	</tbody>
</table>

<table width="900" border="0" cellpadding="0" cellspacing="0" align="center">
	<tr>
		<td height="25" id="pager" align="center" style="display:none;">
			共 <span id="count"></span>条 / <span id="pageNum"></span>页&nbsp;
			<div class="btn-group">
				<button class="btn" onclick="prev()">前一页</button>
				<span id="page"><button class="btn" id="pageIndex"></button></span>
				<button class="btn" onclick="next()">后一页</button>
			</div>
		</td>
	</tr>
</table>

</div>
</body>
