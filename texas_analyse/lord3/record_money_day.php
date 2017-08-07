<?php

$Q = $_REQUEST;
$timenow = time();
$apiname = 'record_money_day';
$apiword = '货币全局分析';
$apitable = 'lord_record_money_day';
$apitypes = array(
	'search'=>'搜索',
	'select'=>'列表',
);
$apitypei = array_keys($apitypes);
$apitype = isset($Q['apitype']) ? trim($Q['apitype']) : end($apitypei);
if ( !isset($apitypes[$apitype]) ) exit;
$searchs = array(
	'moneyid'	=>array('name'=>'货币','int'=>1,'typ'=>'select','rel'=>0,'all'=>1),
);
$moneyids = array_flip(array(//来自于class.record.php
	'乐豆'=>3, '乐券'=>2, '乐币'=>1, '乐钻'=>4
));
$moneyid = isset($Q['moneyid']) && isset($moneyids[$Q['moneyid']]) ? intval($Q['moneyid']) : 'all';

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
	$where = array();
	foreach ( $searchs as $k => $v ) {
		if ( $v['rel'] ) continue;
		switch ( $v['typ'] ) {
			case 'select':
				$val = isset($Q[$k]) ? $Q[$k] : ($v['all'] ? 'all' : ($v['int'] ? null :''));
				if ( $v['all'] && $val != 'all' ) { $where[]= "`$k` = ".($v['int'] ? intval($val) : ("'".$val."'")); }
				elseif ( $val != 'all' && !empty($val) ) { $where[]= "`$k` = ".($v['int'] ? intval($val) : ("'".$val."'")); }
				break;
			case 'input':
				$val = isset($Q[$k]) ? $Q[$k] : ($v['int'] ? 0 : '');
				if ( $val ) { $where[]= "`$k` = ".($v['int'] ? intval($val) : ("'".$val."'")); }
				break;
			default:
				$val = isset($Q[$k]) ? $Q[$k] : ($v['int'] ? 0 : '');
				if ( $val ) { $where[]= "`$k` = ".($v['int'] ? intval($val) : ("'".$val."'")); }
				break;
		}
	}
	$where = $where ? ('WHERE '.join(' AND ', $where)) : '';
	$sql = "SELECT * FROM `$apitable` {$where} ORDER BY `id` DESC LIMIT {$cur_page}, {$per_page}";
	$res = $db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
	$res = ( $res && is_array($res) ) ? $res : array();
	foreach ( $res as $k => $v )
	{
		$res[$k]['money'] = isset($moneyids[$v['moneyid']]) ? $moneyids[$v['moneyid']] : $v['moneyid'];
		$res[$k]['tmcr'] = $v['tmcr'] ? date("H:i:s", $v['tmcr']) : "&nbsp;";
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
.row .span2{position:relative;}
.row .span2 div{line-height: 30px;padding-left:5px;color:#ccc;}
.row .span2 input.span2{position:absolute;top:0;height:30px;background:none!important;text-indent:40px;}
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
			datahtml += "<td>"+o.money+"</td>";
			datahtml += "<td>"+o.transfers+"</td>";
			datahtml += "<td>"+o.transtimes+"</td>";
			datahtml += "<td>"+o.outgoings+"</td>";
			datahtml += "<td>"+o.outgotimes+"</td>";
			datahtml += "<td>"+o.incomings+"</td>";
			datahtml += "<td>"+o.incomtimes+"</td>";
			datahtml += "<td>"+o.earnings+"</td>";
			datahtml += "<td>"+o.holdings+"</td>";
			datahtml += "<td>"+o.hold1+"</td>";
			datahtml += "<td>"+o.hold2+"</td>";
			datahtml += "<td>"+o.hold3+"</td>";
			datahtml += "<td>"+o.hold4+"</td>";
			datahtml += "<td>"+o.hold5+"</td>";
			datahtml += "<td>"+o.hold6+"</td>";
			datahtml += "<td>"+o.hold7+"</td>";
			datahtml += "<td>"+o.hold8+"</td>";
			datahtml += "<td>"+o.hold9+"</td>";
			datahtml += "<td>"+o.hold10+"</td>";
			datahtml += "<td>"+o.hold0+"</td>";
			datahtml += "<td>"+o.tmcr+"</td>";
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
		<?php foreach ( $searchs as $var => $set ) { $varn = $set['name'].': '; if ($set['typ'] == 'select') { ?>
		<div class="span2"><select id="<?=$var?>" class="span2 finder">
			<?php if ($set['all']) {?><option value="all"><?=$varn?>全部</option><?php } ?>
			<?php foreach ( ${$var.'s'} as $k => $v ) { echo "<option value='$k'".($data && $data[$var]==$k ? " selected='selected'" : "").">{$varn}$v</option>"; } ?>
		</select></div>
		<?php } elseif ( $set['typ'] == 'input' ) { ?>
		<div class="span2"><div><?=$varn?></div><input id="<?=$var?>" value="" type="text" class="span2 finder" /></div>
		<?php } else { ?>
		<?php } } ?>
		<div span="span1" style="float:right;">
			<?php if ( isset($apitypes['search']) ) { ?><input class="btn" type="button" value="查&nbsp;&nbsp;询" onclick="finder()" /><?php } ?>
			<?php if ( isset($apitypes['create']) ) { ?><input class="btn" type="button" value="创&nbsp;&nbsp;建" onclick="linkto(this,'<?=$apiname?>.php?apitype=create')" /><?php } ?>
		</div>
	</div>
</fieldset>

<table class="table table-bordered table-condensed table-hover">
	<tr class="info">
		<th>编号</th>
		<th>日期</th>
		<th>货币</th>
		<th>流通量∓</th>
		<th>流通次数</th>
		<th>投放量∓</th>
		<th>投放次数</th>
		<th>回收量∓</th>
		<th>回收次数</th>
		<th>盈亏量∓</th>
		<th>持有量</th>
		<th>>=500W/5W/500</th>
		<th>>=200W/2W/200</th>
		<th>>=100W/1W/100</th>
		<th>>=50W/5K/50</th>
		<th>>=20W/2K/20</th>
		<th>>=10W/1K/10</th>
		<th>>=5W/500/5</th>
		<th>>=2W/200/2</th>
		<th>>=1W/100/1</th>
		<th>>0/0/0</th>
		<th>=0/0/0</th>
		<th>时间</th>
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
