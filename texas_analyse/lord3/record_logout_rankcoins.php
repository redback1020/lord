<?php

//base
header("Content-type: text/html; charset=utf-8");
require_once '../manage/checkPriv.php';
require_once '../include/database.class.php';
$Q = $_REQUEST;
$timenow = time();

//conf
$apiname = 'record_logout_rankcoins';
$apiword = '日活用户乐豆榜';
$apitable= 'lord_user_logout';
$isDateTableName = 1;
$isDateTableColumn = 0;
$orderby = "ORDER BY `logout_coins` DESC";
$apitypes = array( 'search'=>'搜索', 'select'=>'列表' );
$apitypei = array_keys($apitypes);
$apitype = isset($Q['apitype']) ? trim($Q['apitype']) : end($apitypei);
if ( !isset($apitypes[$apitype]) ) exit;
$id = isset($Q['id']) ? intval($Q['id']) : 0;
$inputdate = isset($Q['dateid']) && $Q['dateid'] ? trim($Q['dateid']) : ($isDateTableName ? date("Y-m-d") : '');
if ( $isDateTableName ) $apitable.= '_'.str_replace('-','',$inputdate);
$searchs = array(
	'dateid'  =>array('name'=>'日期','int'=>1,'typ'=>'date','rel'=>0,'all'=>1),
);

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
	$where = array("`id` IN (SELECT MAX(`id`) FROM `$apitable` GROUP BY `uid`)");
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
				if ( ! $isDateTableColumn ) break;
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
	$sql = "SELECT * FROM `$apitable` {$where} {$orderby} LIMIT {$cur_page}, {$per_page}";
	// echo $sql; exit;
	$res = $db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
	$res = $res && is_array($res) ? $res : array();
	$uids = array();
	$rank = ($cur_page - 1) * $per_page;
	$ret = array();
	foreach ( $res as $k => $v )
	{
		$rank++;
		$ret[$v['uid']] = $v;
		$ret[$v['uid']]['rank'] = $rank;
		$ret[$v['uid']]['login_time'] = date('H:i:s', $v['login_time']);
		$ret[$v['uid']]['logout_time'] = date('H:i:s', $v['logout_time']);
		$ret[$v['uid']]['cool_num'] = 0;
		$ret[$v['uid']]['nick'] = '';
		$ret[$v['uid']]['add_time'] = '';
		$uids[]= $v['uid'];
	}
	$res = $ret;
	if ( $uids ) {
		$sql = "SELECT u.uid,u.cool_num,u.nick,a.add_time FROM `lord_game_user` u LEFT JOIN `lord_game_analyse` a ON u.uid = a.uid WHERE u.uid IN (".join(',',$uids).")";
		$ret = $db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
		$ret = $ret && is_array($ret) ? $ret : array();
		foreach ( $ret as $k => $v )
		{
			$res[$v['uid']]['cool_num'] = $v['cool_num'];
			$res[$v['uid']]['nick'] = $v['nick'];
			$res[$v['uid']]['add_time'] = $v['add_time'];
		}
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
table.table th{background:#eee;}
.row{margin-bottom:10px;}
.row .span2{position:relative;}
.row .span2 .bg{display:block;line-height:30px;padding-left:5px;color:#ccc;font-size:14px;}
.row .span2 input.span2,.row .span2 select.span2{position:absolute;top:0;height:30px;text-indent:32px;background:none!important;}
</style>
<script>
var per_page = 20;
var cur_page = 0;
$(function(){ finder(); $('.finder').change(function(){ finder(); }); });
function linkto( o, to ) { var _a = to.split('/'); var _this = $(o); if ( _a[0] != "http" ) { var _b = self.location.href.split('/'); delete _b[_b.length-1]; to = _b.join('/') + to; } self.location.href=to; }
function ajaxto( o, to ) { var _a = to.split('/'); var _this = $(o); if ( _a[0] != "http" ) { var _b = self.location.href.split('/'); delete _b[_b.length-1]; to = _b.join('/') + to; } $.getJSON(to, function(data){ if ( data ) { if ( data.errno == 0 ) { alert("操作成功。"); finder(); } else { alert("操作失败["+data.errno+"]："+data.error); } } }); }
function previt() { if ( cur_page==0 ) { alert("已经是第一页"); return; } cur_page--; findit(); }
function nextit() { cur_page++; findit(1); }
function finder() { cur_page=0; findit(); }
function findit( is_next ) {
	$.post("<?=$apiname?>.php?apitype=search&per_page="+per_page+"&cur_page="+cur_page, {
		<?php $postp = array(); foreach ( $searchs as $k => $v ) { $postp[]= $k." : \$('#{$k}').val()"; } echo $postp ? join(',', $postp) : ''; ?>
	}, function( data ) {
		if ( data == null || data == "" ) { alert("获取数据失败！"); return; }
		var datalist = eval("("+data+")");
		var datahtml = "";
		for ( var i=0; i<datalist.data_res.length; i++ ) {
			var o = datalist.data_res[i];
			datahtml += "<tr class='table-body'>";
			datahtml += "<td>"+o.rank+"</td>";
			datahtml += "<td><a href='userInfo.php?uid="+o.uid+"'>"+o.uid+"</a></td>";
			datahtml += "<td>"+o.cool_num+"</td>";
			datahtml += "<td>"+o.nick+"</td>";
			datahtml += "<td>"+o.login_channel+"</td>";
			datahtml += "<td>"+o.login_vercode+"</td>";
			datahtml += "<td>"+o.login_ip+"</td>";
			datahtml += "<td>"+o.add_time+"</td>";
			datahtml += "<td>"+o.login_coins+"</td>";
			datahtml += "<td>"+o.logout_coins+"</td>";
			datahtml += "<td>"+o.login_coupon+"</td>";
			datahtml += "<td>"+o.logout_coupon+"</td>";
			datahtml += "<td>"+o.login_time+"</td>";
			datahtml += "<td>"+o.logout_time+"</td>";
			datahtml += "<td>"+o.online_time+"</td>";
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
		<div class="span2"><span class="bg"><?=$varn?></span><select id="<?=$var?>" class="span2 finder">
			<?php if ($set['all']) {?><option value="all">全部</option><?php } ?>
			<?php foreach ( ${$var.'s'} as $k => $v ) { echo "<option value='$k'".($data && $data[$var]==$k ? " selected='selected'" : "").">$v</option>"; } ?>
		</select></div>
		<?php } elseif ( $set['typ'] == 'input' ) { ?>
		<div class="span2"><span class="bg"><?=$varn?></span><input id="<?=$var?>" value="" type="text" class="span2 finder" /></div>
		<?php } elseif ( $set['typ'] == 'date' ) { ?>
		<div class="span2"><span class="bg"><?=$varn?></span><input id="<?=$var?>" value="<?=$inputdate?>" type="text" class="span2 finder" readonly onclick="WdatePicker({dateFmt:'yyyy-MM-dd'})" /></div>
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
		<th>乐豆排名</th>
		<th>UID</th>
		<th>编号ID</th>
		<th>昵称</th>
		<th>当前渠道</th>
		<th>当前版本</th>
		<th>当前IP</th>
		<th>注册时间</th>
		<th>登入乐豆</th>
		<th>登出乐豆</th>
		<th>登入乐券</th>
		<th>登出乐券</th>
		<th>登入时间</th>
		<th>登出时间</th>
		<th>在线秒数</th>
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
