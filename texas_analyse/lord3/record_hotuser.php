<?php

//base
header("Content-type: text/html; charset=utf-8");
require_once '../manage/checkPriv.php';
require_once '../include/database.class.php';
$Q = $_REQUEST;
$timenow = time();

//conf
$apiname = 'record_hotuser';
$apiword = '每日热门用户排行榜';
$apitypes = array( 'search'=>'搜索', 'select'=>'列表' );
$apitypei = array_keys($apitypes);
$apitype = isset($Q['apitype']) ? trim($Q['apitype']) : end($apitypei);
if ( !isset($apitypes[$apitype]) ) exit;
$id = isset($Q['id']) ? intval($Q['id']) : 0;
$searchs = array(
	'dd'		=>array('name'=>'日期','int'=>1,'typ'=>'select','rel'=>0,'all'=>0),
	'orderby'	=>array('name'=>'排序','int'=>1,'typ'=>'select','rel'=>0,'all'=>0,'srt'=>1),
	'uid'		=>array('name'=>'UID','int'=>1,'typ'=>'input','rel'=>0,'all'=>1),
	'cool_num'	=>array('name'=>'编号ID','int'=>1,'typ'=>'input','rel'=>'uid','all'=>1),
);
$dds = array();
$day0 = strtotime(date("Y-m-d"))-86400;
$day0_15 = $day0 - 86400 * 15;
for ( $i = $day0_15; $i <= $day0; $i+=86400 ) {
	$dds[date("Ymd", $i)] = date("m-d", $i);
}
krsort($dds);
$dd = isset($Q['dd']) && isset($dds[$Q['dd']]) ? intval($Q['dd']) : intval(date("Ymd",time()-86400));
$orderbys = array('coins'=>'登出乐豆','coupon'=>'登出乐券','ddcoins'=>'当日得豆','ddcoupon'=>'当日得券','ddplay'=>'当日牌局','ddwin'=>'当日胜局','ddlogin'=>'当日登入','ddseconds'=>'当日时长');
$apitable= 'lord_record_hotuser';

//search
if ( $apitype == 'search' ) {
	$per_page = $Q['per_page'];
	$cur_page = $Q['cur_page'] * $per_page;
	foreach ( $searchs as $k => $v ) {
		if ( $v['rel'] && isset($Q[$k]) ) {
			if ( $v['int'] ) $Q[$k] = intval($Q[$k]);
			if ( !$Q[$k] ) continue;
			$sql = "SELECT `".$v['rel']."` FROM `lord_game_user` WHERE `$k` = ".$Q[$k];
			$res = $db->query($sql)->fetch(PDO::FETCH_ASSOC);
			if ( $res && is_array($res) && isset($searchs[$v['rel']]) ) {
				$Q[$v['rel']] = end($res);
				if ( $searchs[$v['rel']]['int'] ) $Q[$v['rel']] = intval($Q[$v['rel']]);
			}
		}
	}
	$where = array();
	$orderby = array();
	foreach ( $searchs as $k => $v ) {
		if ( $v['rel'] ) continue;
		if ( isset($v['srt']) && $v['srt'] ) {
			if ( isset($Q[$k]) && $Q[$k] ) {
				$orderby[]= substr_count($Q[$k],'SC') ? $Q[$k] : ('`'.$Q[$k].'` DESC');
			}
			continue;
		}
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
			case 'date':
				$val = isset($Q[$k]) && $Q[$k] ? str_replace('-','',$Q[$k]) : 0;
				if ( $val ) { $where[]= "`$k` = ".($v['int'] ? intval($val) : ("'".$val."'")); }
				break;
			default:
				$val = isset($Q[$k]) ? $Q[$k] : ($v['int'] ? 0 : '');
				if ( $val ) { $where[]= "`$k` = ".($v['int'] ? intval($val) : ("'".$val."'")); }
				break;
		}
	}
	$orderby = 'ORDER BY '.($orderby ? join(',',$orderby) : "`id` DESC");
	$where = $where ? ('WHERE '.join(' AND ', $where)) : '';
	$sql = "SELECT * FROM `$apitable` {$where} GROUP BY `uid` {$orderby} LIMIT {$cur_page}, {$per_page}";
	$res = $db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
	$res = ( $res && is_array($res) ) ? $res : array();
	foreach ( $res as $k => $v )
	{
		$res[$k]['tmcr'] = $v['tmcr'] ? date("m-d H:i:s", $v['tmcr']) : "&nbsp;";
	}
	$json['data_res'] = $res;
	$sql = "SELECT count(*) as data_num FROM `$apitable` {$where} GROUP BY `uid` {$orderby}";
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
$(function(){ finder(); $('.finder').change(function(){ finder(); }); });
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
			datahtml += "<td>"+o.dd+"</td>";
			datahtml += "<td><a href='userAccount.php?uid="+o.uid+"'>"+o.uid+"</a></td>";
			datahtml += "<td>"+o.reg+"</td>";
			datahtml += "<td>"+o.channel+"</td>";
			datahtml += "<td>"+o.vercode+"</td>";
			datahtml += "<td>"+o.ip+"</td>";
			datahtml += "<td>"+o.coins+"</td>";
			datahtml += "<td>"+o.coupon+"</td>";
			// datahtml += "<td>"+o.play+"</td>";
			// datahtml += "<td>"+o.win+"</td>";
			datahtml += "<td>"+o.ddcoins+"</td>";
			datahtml += "<td>"+o.ddcoupon+"</td>";
			datahtml += "<td>"+o.ddplay+"</td>";
			datahtml += "<td>"+o.ddwin+"</td>";
			datahtml += "<td>"+o.ddlogin+"</td>";
			datahtml += "<td>"+o.ddseconds+"</td>";
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
		<?php } elseif ( $set['typ'] == 'date' ) { ?>
		<div class="span2"><div><?=$varn?></div><input id="<?=$var?>" value="" type="text" class="span2 finder" readonly onclick="WdatePicker({dateFmt:'yyyy-MM-dd'})" /></div>
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
		<th>日期</th>
		<th>UID</th>
		<th>注册</th>
		<th>渠道</th>
		<th>版本</th>
		<th>IP</th>
		<th>登出乐豆</th>
		<th>登出乐券</th>
		<!-- <th>登出牌局</th>
		<th>登出胜局</th> -->
		<th>当日得豆</th>
		<th>当日得券</th>
		<th>当日牌局</th>
		<th>当日胜局</th>
		<th>当日登入</th>
		<th>当日时长</th>
		<th>统计时间</th>
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
