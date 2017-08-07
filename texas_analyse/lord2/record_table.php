<?php

//商品管理

$Q = $_REQUEST;
$timenow = time();
$apiname = 'record_table';
$apiword = '牌桌记录';
$apitypes = array(
	'search'=>'搜索',
	'select'=>'列表',
);
$apitypei = array_keys($apitypes);
$apitype = isset($Q['apitype']) ? trim($Q['apitype']) : end($apitypei);
if ( !isset($apitypes[$apitype]) ) exit;
$id = isset($Q['id']) ? intval($Q['id']) : 0;
$searchs = array(
	'dateid'=>array('name'=>'日期','int'=>1,'sel'=>1,'all'=>0),
	'hourid'=>array('name'=>'小时','int'=>1,'sel'=>1,'all'=>1),
	'roomId'=>array('name'=>'房间','int'=>1,'sel'=>1,'all'=>1),
);
$dateids = array();
$day0 = strtotime(date("Y-m-d 00:00:00"));
$day0_15 = $day0 - 86400 * 15;
for ( $i = $day0_15; $i <= $day0; $i+=86400 ) {
	$dateids[date("Ymd", $i)] = date("m-d", $i);
}
krsort($dateids);
$dateid = isset($Q['dateid']) && isset($dateids[$Q['dateid']]) ? intval($Q['dateid']) : intval(date("Ymd"));
$apitable= 'lord_record_table_'.$dateid;
$hourids = array(
	'0'=>'A00','1'=>'A01','2'=>'A02','3'=>'A03','4'=>'A04','5'=>'A05',
	'6'=>'A06','7'=>'A07','8'=>'A08','9'=>'A09','10'=>'A10','11'=>'A11',
	'12'=>'P00','13'=>'P01','14'=>'P02','15'=>'P03','16'=>'P04','17'=>'P05',
	'18'=>'P06','19'=>'P07','20'=>'P08','21'=>'P09','22'=>'P10','23'=>'P11',
);
$hourid = isset($Q['hourid']) && isset($hourids[$Q['hourid']]) ? intval($Q['hourid']) : 'all';
$roomIds = array(
	//来自于class.record.php
	1000=>'经典新手',1001=>'经典初级',1002=>'经典中级',1003=>'经典高级',1006=>'经典无限',
	1007=>'赖子新手',1008=>'赖子初级',1009=>'赖子中级',1010=>'赖子高级',1011=>'赖子无限',
	1004=>'竞技初级',
	3001=>'一千乐券场',3002=>'三千乐券场',3003=>'两万乐券场',
	3011=>'咪咕热身赛',3012=>'咪咕大师赛',3013=>'咪咕总决赛',
);
$roomId = isset($Q['roomId']) && isset($roomIds[$Q['roomId']]) ? intval($Q['roomId']) : 'all';

// $cds = array('1'=>'充值乐币','2'=>'购买乐豆','3'=>'试衣间','4'=>'购买道具','5'=>'乐券兑换','6'=>'预留扩展');//分类归属
// $moneys = array('cny'=>'人民币','gold'=>'乐币','golds'=>'代币','coins'=>'乐豆','coupon'=>'乐券');//货币类型
// $is_onsales = array('0'=>'无促销','1'=>'有促销');//促销状态
// $is_recommends = array('0'=>'无推荐','1'=>'有推荐');//推荐状态
// $states = array('0'=>'正常','1'=>'离线','2'=>'删除');//上线状态
// $is_hides = array('0'=>'显示','1'=>'隐藏');//显隐状态
//base
header("Content-type: text/html; charset=utf-8");
require_once '../manage/checkPriv.php';
require_once '../include/database.class.php';
$channels = array();
$sql = "SELECT * FROM `lord_game_channel` WHERE `is_del` = 0";
$ret = $db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
if ( !$ret ) $ret = array();
foreach ( $ret as $k => $v ) {
	$channels[$v['id']] = $v['channel'];
}
//search
if ( $apitype == 'search' ) {
	$per_page = $Q['per_page'];
	$cur_page = $Q['cur_page'] * $per_page;
	$where = "WHERE 1=1";
	foreach ( $searchs as $k => $v ) {
		$val = isset($Q[$k]) ? $Q[$k] : ($v['all'] ? 'all' : ($v['int'] ? null :''));
		if ( $v['all'] && $val != 'all' ) { $where .= " AND `$k` = ".($v['int'] ? $val : ("'".$val."'")); }
		elseif ( $val != 'all' && !empty($val) ) { $where .= " AND `$k` = ".($v['int'] ? $val : ("'".$val."'")); }
	}
	$where = $where == 'WHERE 1=1' ? '' : str_replace(' 1=1 AND', '', $where);
	$sql = "SELECT * FROM `$apitable` {$where} ORDER BY `id` DESC LIMIT {$cur_page}, {$per_page}";
	$res = $db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
	$res = ( $res && is_array($res) ) ? $res : array();
	foreach ( $res as $k => $v )
	{
		$res[$k]['room'] = isset($roomIds[$v['roomId']]) ? $roomIds[$v['roomId']] : $v['roomId'];
		$res[$k]['channel0'] = isset($channels[$v['channelid0']]) ? $channels[$v['channelid0']] : $v['channelid0'];
		$res[$k]['channel1'] = isset($channels[$v['channelid1']]) ? $channels[$v['channelid1']] : $v['channelid1'];
		$res[$k]['channel2'] = isset($channels[$v['channelid2']]) ? $channels[$v['channelid2']] : $v['channelid2'];
		$res[$k]['create'] = $v['create'] ? date("H:i:s", $v['create']) : "&nbsp;";
		$res[$k]['starte'] = $v['starte'] ? date("H:i:s", $v['starte']) : "&nbsp;";
		$res[$k]['finish'] = $v['finish'] ? date("H:i:s", $v['finish']) : "&nbsp;";
	}
	$json['data_res'] = $res;
	$sql = "SELECT count(*) as data_num FROM `$apitable` {$where} ";
	$res = $db->query($sql)->fetch(PDO::FETCH_ASSOC);
	$json['data_num'] = $res ? $res['data_num'] : 0;
	echo json_encode($json);
	exit;
}
//select
if ( $apitype == 'select' ) {
?>
<script type="text/javascript" src="../js/jquery.js"></script>
<script type="text/javascript" src="../js/My97DatePicker/WdatePicker.js"></script>
<link type="text/css" href="../bootstrap/css/bootstrap.min.css" rel="stylesheet" />
<style type="text/css">
.body{position:absolute;left:0;top:0;padding:0 0 0 10px;width:98%;}
legend{margin-bottom: 10px;}
table.table{ font-size: 12px;margin-bottom:8px;width: 100%!important;}
table.table th,table.table td{ white-space: nowrap;}
</style>
<script type="text/javascript">
var per_page = 20;
var cur_page = 0;
$(function(){ finder(); $('select.finder').change(function(){ finder(); }); });
function linkto( o, to ) { var _a = to.split('/'); var _this = $(o); if ( _a[0] != "http" ) { var _b = self.location.href.split('/'); delete _b[_b.length-1]; to = _b.join('/') + to; } self.location.href=to; }
function ajaxto( o, to ) { var _a = to.split('/'); var _this = $(o); if ( _a[0] != "http" ) { var _b = self.location.href.split('/'); delete _b[_b.length-1]; to = _b.join('/') + to; } $.getJSON(to, function(data){ if ( data ) { if ( data.errno == 0 ) { alert("操作成功。"); finder(); } else { alert("操作失败["+data.errno+"]："+data.error); } } }); }
function previt() { if ( cur_page==0 ) { alert("已经是第一页"); return; } cur_page--; findit(); }
function nextit() { cur_page++; findit(1); }
function finder() { cur_page=0; findit(); }
function findit( is_next ) {
	<?php $ps_ = array(); foreach ( $searchs as $k => $v ) { $ps_[$k] = $k; } foreach ( $ps_ as $kk => $vv ) { echo "var {$vv} = \$('#{$vv}').val();\n"; $ps_[$kk] = "$vv : $vv"; } ?>
	$.post("<?=$apiname?>.php?apitype=search&per_page="+per_page+"&cur_page="+cur_page, {
		<?php echo join(",\n", $ps_); ?>
	}, function( data ) {
		if ( data == null || data == "" ) { alert("获取数据失败！"); return; }
		var datalist = eval("("+data+")");
		var datahtml = "";
		for ( var i=0; i<datalist.data_res.length; i++ ) {
			var o = datalist.data_res[i];
			datahtml += "<tr class='table-body'>";
			datahtml += "<td>"+o.id+"</td>";
			datahtml += "<td>"+o.dateid+"</td>";
			datahtml += "<td>"+o.hourid+"</td>";
			datahtml += "<td>"+o.room+"</td>";
			datahtml += "<td>"+o.tableId+"</td>";
			datahtml += "<td>"+o.rate+"</td>";
			datahtml += "<td>"+o.rake+"</td>";
			datahtml += "<td>"+o.coins+"</td>";
			datahtml += "<td>"+o.coupon+"</td>";
			datahtml += "<td><a href='userAccount.php?uid="+o.lord+"'>"+o.lord+"</a></td>";
			datahtml += "<td><a href='userAccount.php?uid="+o.uid0+"'>"+o.uid0+"</a></td>";
			datahtml += "<td>"+o.channel0+"</td>";
			datahtml += "<td>"+o.wcoins0+"</td>";
			datahtml += "<td>"+o.tcoupon0+"</td>";
			datahtml += "<td><a href='userAccount.php?uid="+o.uid1+"'>"+o.uid1+"</a></td>";
			datahtml += "<td>"+o.channel1+"</td>";
			datahtml += "<td>"+o.wcoins1+"</td>";
			datahtml += "<td>"+o.tcoupon2+"</td>";
			datahtml += "<td><a href='userAccount.php?uid="+o.uid2+"'>"+o.uid2+"</a></td>";
			datahtml += "<td>"+o.channel2+"</td>";
			datahtml += "<td>"+o.wcoins2+"</td>";
			datahtml += "<td>"+o.tcoupon2+"</td>";
			datahtml += "<td>"+o.create+"</td>";
			datahtml += "<td>"+o.starte+"</td>";
			datahtml += "<td>"+o.finish+"</td>";
			datahtml += "</tr>";
		}
		if ( datahtml=="" && is_next ) { alert("已到最后一页"); cur_page--; }
		else { $("#datalist").html(datahtml); $("#data_num").html(datalist.data_num); $("#page_num").html(Math.ceil(datalist.data_num/per_page)); $("#cur_page").html(cur_page+1); $("#pager").show(); }
	});
}
</script>

<body>
<div class="body">

<fieldset>
	<legend><?=$apiword?> - <?=$apitypes[$apitype]?></legend>
	<div class="row">
		<?php foreach ( $searchs as $var => $conf ) {?>
		<div class="span2">
			<?php $varn = $conf['name'].': '; ?><select id="<?=$var?>" class="span2 finder"><?php if ($conf['all']){ ?><option value="all"><?=$varn?>全部</option><?php }?><?php foreach (${$var.'s'} as $k => $v) { echo "<option value='$k'".($data && $data[$var]==$k ? " selected='selected'" : "").">{$varn}$v</option>"; } ?></select></td>
		</div>
		<?php }?>
		<div span="span1" style="float:right;">
			<!-- <input class="btn" type="button" value="查&nbsp;&nbsp;询" onclick="finder()" /> -->
			<!-- <input class="btn" type="button" value="创&nbsp;&nbsp;建" onclick="linkto(this,'<?=$apiname?>.php?apitype=create')" /> -->
		</div>
	</div>
</fieldset>

<table class="table table-bordered table-condensed table-hover">
	<tr class="info">
		<th>编号</th>
		<th>日期</th>
		<th>小时</th>
		<th>房间</th>
		<th>桌号</th>
		<th>倍率</th>
		<th>门票</th>
		<th>流通</th>
		<th>乐券</th>
		<th>地主</th>
		<th>UID0</th>
		<th>渠道0</th>
		<th>输赢0</th>
		<th>乐券0</th>
		<th>UID1</th>
		<th>渠道1</th>
		<th>输赢1</th>
		<th>乐券1</th>
		<th>UID2</th>
		<th>渠道2</th>
		<th>输赢2</th>
		<th>乐券2</th>
		<th>建桌</th>
		<th>开局</th>
		<th>结算</th>
	</tr>
	<tbody id="datalist">
	</tbody>
</table>

<table width="98%" border="0" cellpadding="0" cellspacing="0" align="left">
	<tr>
		<td height="25" id="pager" align="center" style="display:none;">
			共 <span id="data_num"></span>条 / <span id="page_num"></span>页&nbsp;
			<div class="btn-group">
				<button class="btn" onclick="previt()">前一页</button>
				<button class="btn" id="cur_page"></button>
				<button class="btn" onclick="nextit()">后一页</button>
			</div>
		</td>
	</tr>
</table>

</div>
</body>
<?php
}
?>
