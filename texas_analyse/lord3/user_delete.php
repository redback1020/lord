<?php

//删除用户账号

//base
header("Content-type: text/html; charset=utf-8");
require_once '../manage/checkPriv.php';
require_once '../include/database.class.php';
$Q = $_REQUEST;
$timenow = time();
require_once '../manage/getipCity.php';
//conf
$apiname = 'user_delete';
$apiword = '删除用户！！！';
$apitypes = array( 'delete'=>'删除', 'search'=>'搜索', 'select'=>'列表' );
$apitypei = array_keys($apitypes);
$apitype = isset($Q['apitype']) ? trim($Q['apitype']) : end($apitypei);
if ( !isset($apitypes[$apitype]) ) exit;
$id = isset($Q['id']) ? intval($Q['id']) : 0;
$searchs = array(
	'uid'		=>array('name'=>'UID','int'=>1,'typ'=>'input','rel'=>0,'all'=>1),
	'cool_num'	=>array('name'=>'编号ID','int'=>1,'typ'=>'input','rel'=>'uid','all'=>1),
);
$apitable= 'lord_game_user';


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
	// $sql = "SELECT * FROM `$apitable` {$where} LIMIT {$cur_page}, {$per_page}";
	$uid = $Q['uid'];
	if ( $uid ) {
	$sql = "SELECT u.uid, u.cool_num, u.nick, u.gold, u.coins, u.coupon, u.lottery, u.channel, a.matches, a.win, a.add_time, a.last_login, a.last_ip FROM lord_game_user u LEFT JOIN lord_game_analyse a ON a.uid = u.uid WHERE u.uid = $uid";
	$res = $db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
	$res = ( $res && is_array($res) ) ? $res : array();
	$ipCity = new ipCity();
	foreach ( $res as $k => $v )
	{
		$res[$k]['last_ip'] = $v['last_ip'].' '.$ipCity->getCity($v['last_ip']);
		$ret = apiGet('online', 'getuser', array('uid'=>$v['uid']));
		$res[$k]['is_online'] = intval($ret && isset($ret['data']["lord_user_info_$uid"]) && $ret['data']["lord_user_info_$uid"]);
	}
	} else { $res = array(); }
	$json['data_res'] = $res;
	// $sql = "SELECT count(*) as data_num FROM `$apitable` {$where} ";
	// $res = $db->query($sql)->fetch(PDO::FETCH_ASSOC);
	$res = array('data_num'=>count($res));
	$json['data_num'] = $res ? $res['data_num'] : 0;
	echo json_encode($json);
	exit;
}


//delete
if ( $apitype == 'delete' ) {
	$uid = intval($Q['uid']);
	$sqls = array(
		"delete from lord_game_user where uid = $uid",
		"delete from lord_game_analyse where uid = $uid",
		"delete from lord_game_login where uid = $uid",
		"delete from user_analyse where uid = $uid",
		"delete from user_login where uid = $uid",
		"delete from user_user where id = $uid",
		"delete from lord_user_inbox where uid = $uid",
		"delete from lord_user_unbox where uid = $uid",
		"delete from lord_user_task where uid = $uid",
		"delete from lord_user_tesk where uid = $uid",
		"delete from lord_lucky_shake_log where uid = $uid",
		"delete from lord_user_item where uid = $uid",
		"delete from lord_user_taskrecord where uid = $uid",
	);
	$ress = array();
	foreach ( $sqls as $sql )
	{
		$ress[] = $pdo->getDB(1)->exec($sql);
	}
	$res = true ? array('errno'=>0, 'error'=>"操作成功。") : array('errno'=>8, 'error'=>"查询错误。 $sql");
	echo json_encode($res);
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
.row div.span2{height:40px;}
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
			datahtml += "<td><a href='userAccount.php?uid="+o.uid+"'>"+o.uid+"</a></td>";
			datahtml += "<td>"+o.cool_num+"</td>";
			datahtml += "<td>"+o.nick+"</td>";
			datahtml += "<td>"+o.gold+"</td>";
			datahtml += "<td>"+o.coins+"</td>";
			datahtml += "<td>"+o.coupon+"</td>";
			datahtml += "<td>"+o.lottery+"</td>";
			datahtml += "<td>"+o.matches+"</td>";
			datahtml += "<td>"+o.win+"</td>";
			datahtml += "<td>"+o.channel+"</td>";
			datahtml += "<td>"+o.add_time+"</td>";
			datahtml += "<td>"+o.last_login+"</td>";
			datahtml += "<td>"+o.last_ip+"</td>";
			datahtml += "<td>"+(o.is_online?"<span style='color:red'>在线</span>":("<a href='#' onclick='ajaxto(this,\"<?=$apiname?>.php?apitype=delete&uid="+o.uid+"\")'>删除</a>"))+"</td>";
			datahtml += "</tr>";
		}
		if ( datahtml=="" && is_next ) { alert("已到最后一页"); cur_page--; }
		else { $("#datalist").html(datahtml); $("#data_num").html(datalist.data_num); $("#page_num").html(Math.ceil(datalist.data_num/per_page)); $("#cur_page").html(cur_page+1); $("#pager").show(); }
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
		<th>UID</th>
		<th>编号ID</th>
		<th>昵称</th>
		<th>乐币</th>
		<th>乐豆</th>
		<th>乐券</th>
		<th>抽奖数</th>
		<th>局数</th>
		<th>胜场</th>
		<th>渠道</th>
		<th>注册时间</th>
		<th>上次登录</th>
		<th>上次IP</th>
		<th>删除</th>
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
