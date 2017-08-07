<?php

//商品管理

//base
header("Content-type: text/html; charset=utf-8");
require_once '../manage/checkPriv.php';
require_once '../include/database.class.php';
$Q = $_REQUEST;
$timenow = time();

//conf
$apiname = 'record_money_recharge';
$apiword = '充值用户特征';
$apitypes = array( 'search'=>'搜索', 'select'=>'列表' );
$apitypei = array_keys($apitypes);
$apitype = isset($Q['apitype']) ? trim($Q['apitype']) : end($apitypei);
if ( !isset($apitypes[$apitype]) ) exit;
$id = isset($Q['id']) ? intval($Q['id']) : 0;
$searchs = array(
	'dateid'	=>array('name'=>'日期','int'=>1,'typ'=>'select','rel'=>0,'all'=>0),
);
$dateids = array();
$day0 = strtotime(date("Y-m-d"));
$day0_15 = $day0 - 86400 * 15;
for ( $i = $day0_15; $i <= $day0; $i+=86400 ) {
	$dateids[date("Ymd", $i)] = date("m-d", $i);
}
krsort($dateids);
$dateid = isset($Q['dateid']) && isset($dateids[$Q['dateid']]) ? intval($Q['dateid']) : intval(date("Ymd"));
$roomIds = array(
	0=>'大厅',
	//来自于class.record.php
	1000=>'经典新手',1001=>'经典初级',1002=>'经典中级',1003=>'经典高级',1006=>'经典无限',
	1007=>'赖子新手',1008=>'赖子初级',1009=>'赖子中级',1010=>'赖子高级',1011=>'赖子无限',
	1004=>'竞技初级',
	3001=>'一千乐券场',3002=>'三千乐券场',3003=>'两万乐券场',
	3011=>'咪咕热身赛',3012=>'咪咕大师赛',3013=>'咪咕总决赛',
);
$typeids = array_flip(array(//来自于class.record.php
	//低于50的为货币投放>=0 或=54
	'后台添加'=>1, '充值乐币'=>2, '币豆加豆'=>3, 'SDK买豆'=>4, '币买道具'=>54, '券豆加豆'=>5,
	'新手乐豆'=>6, '每日签到'=>7, '领取救济'=>8, '免费抽奖'=>9, '微信签到'=>10,
	'激活礼包'=>11, '参与活动'=>12, '牌局任务'=>13, '固定任务'=>14, '动态任务'=>15,
	'使用道具'=>16, '领取邮件'=>17, '领取俸禄'=>18, '竞技取消'=>19, '竞技场奖'=>20,
	'竞技周奖'=>21, '赌一中奖'=>22, '赌二中奖'=>23, '免责金牌'=>24, '幸运牌局'=>25,
	'新赛取消'=>26, '新赛发奖'=>27,
	//低于90的为货币回收<=0 且!=54
	'后台扣除'=>51, '牌局抽水'=>52, '币豆减币'=>53, '券豆减券'=>55,
	'券买道具'=>56, '券换实物'=>57, '豆买道具'=>58, '竞技报名'=>59, '赌一投币'=>60,
	'赌二投币'=>61, '新赛报名'=>62,
	//高于90的为牌局输赢转移
	'牌局赢豆'=>91, '牌局输豆'=>92,
));
$moneyids = array_flip(array(//来自于class.record.php
	'乐币'=>1, '乐券'=>2, '乐豆'=>3, '乐钻'=>4
));
$channels = array();
$sql = "SELECT * FROM `lord_game_channel` WHERE `is_del` = 0";
$ret = $db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
if ( !$ret ) $ret = array();
foreach ( $ret as $k => $v ) {
	$channels[$v['id']] = $v['channel'];
}
$apitable= 'lord_record_money_'.$dateid;

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
	$where = array("`uid` IN (SELECT `uid` FROM `lord_user_cost` WHERE `dateid` = $dateid) AND `typeid` IN (2,3,4,54)");
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
	$where = $where ? ('WHERE '.join(' AND ', $where)) : '';
	$sql = "SELECT * FROM `$apitable` {$where} ORDER BY `id` DESC LIMIT {$cur_page}, {$per_page}";
	$res = $db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
	$res = ( $res && is_array($res) ) ? $res : array();
	foreach ( $res as $k => $v )
	{
		$res[$k]['type'] = isset($typeids[$v['typeid']]) ? $typeids[$v['typeid']] : $v['typeid'];
		$res[$k]['money'] = isset($moneyids[$v['moneyid']]) ? $moneyids[$v['moneyid']] : $v['moneyid'];
		$res[$k]['room'] = isset($roomIds[$v['roomId']]) ? $roomIds[$v['roomId']] : $v['roomId'];
		$res[$k]['tableId'] = $v['tableId'] ? $v['tableId'] : "&nbsp;";
		$res[$k]['channel'] = isset($channels[$v['channelid']]) ? $channels[$v['channelid']] : $v['channelid'];
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
			datahtml += "<td>"+o.hourid+"</td>";
			datahtml += "<td>"+o.type+"</td>";
			datahtml += "<td>"+o.money+"</td>";
			datahtml += "<td><a href='userAccount.php?uid="+o.uid+"'>"+o.uid+"</a></td>";
			datahtml += "<td>"+(((o.typeid > 50 && o.typeid < 91 && o.typeid != 54 )||o.typeid == 92)?'-':'')+o.moneynum+"</td>";
			datahtml += "<td>"+o.moneynow+"</td>";
			datahtml += "<td>"+o.channel+"</td>";
			datahtml += "<td>"+o.room+"</td>";
			datahtml += "<td>"+o.tableId+"</td>";
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
		<th>序号</th>
		<th>日期</th>
		<th>小时</th>
		<th>类型</th>
		<th>货币</th>
		<th>UID</th>
		<th>增减</th>
		<th>当前</th>
		<th>渠道</th>
		<th>房间</th>
		<th>桌号</th>
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
