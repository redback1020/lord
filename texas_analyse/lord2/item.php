<?php

//商品管理

$apiname = 'item';
$apiword = '物品';
$timenow = time();
$Q = $_REQUEST;
$apitype = isset($Q['apitype']) ? trim($Q['apitype']) : 'select';
$id = isset($Q['id']) ? intval($Q['id']) : 0;
$apitypes = array(/*'online'=>'上线','unline'=>'下线',*/'delete'=>'删除','modify'=>'修改','update'=>'更新','create'=>'创建','insert'=>'插入','select'=>'选择','search'=>'搜索');
if ( !isset($apitypes[$apitype]) ) exit;
$apitypei = array_keys($apitypes);
$searchs = array(
	'cd'		=>array('name'=>'分类',		'int'=>0, 'type'=>'select',	'col'=>1,	'all'=>1),
	// 'uid'		=>array('name'=>'系统UID',	'int'=>1, 'type'=>'input',	'col'=>1),
	// 'cool_num'	=>array('name'=>'编号ID',		'int'=>1, 'type'=>'input',	'col'=>'uid'),
);
$cds = array('1'=>'服装穿戴','2'=>'辅助道具','3'=>'充值乐币','4'=>'乐币换豆','5'=>'乐券兑换','6'=>'预留扩展');//分类归属
$sexs = array('0'=>'男女通用','1'=>'男性专用','2'=>'女性专用');//性别限制
$showins = array('0'=>'不做限制','1'=>'只在背包');//显示限制
$overlays = array('0'=>'自动抛弃','1'=>'增加数量','2'=>'增加时效','3'=>'增加持久');//叠加方式
$overlays = array('0'=>'自动抛弃','1'=>'增加数量','2'=>'增加时效');//叠加方式
$presents = array('0'=>'不可赠送','1'=>'可以赠送');//可否赠送
$pauses = array('0'=>'不可暂停','1'=>'可以暂停');//可否暂停
$repairs = array('0'=>'不可修复','1'=>'可以修复');//可否修复
$mutexs = array('0'=>'不会互斥','1'=>'同类互斥');//互斥方式
$usebys = array('0'=>'拥有即用','1'=>'缺失即用','2'=>'手动使用');//使用方式
$usedos = array('0'=>'不降数值','1'=>'降低数量','2'=>'降低时效','3'=>'降低持久');//使用运算
$usedos = array('0'=>'不降数值','1'=>'降低数量','2'=>'降低时效');//使用运算
$useass = array('0'=>'没有用途','1'=>'改变状态','2'=>'增加乐币','3'=>'增加代币','4'=>'增加乐豆','5'=>'增加乐券','6'=>'增加抽奖数','8'=>'获得物品','9'=>'获得实物');//使用用途
$useups = array('0'=>'自动销毁','1'=>'不做处理','2'=>'状态：已用完','3'=>'状态：已坏掉','4'=>'状态：待销毁','5'=>'预留扩展');//用完处理
$states = array('0'=>'正常',/*'1'=>'下线',*/'2'=>'删除');//当前状态
//base
header("Content-type: text/html; charset=utf-8");
require_once '../manage/checkPriv.php';
require_once '../include/database.class.php';
$pds = $pdata = array();
$sql = "SELECT * FROM `lord_list_prop`";
$res = $db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
if ( !$res ) $res = array();
foreach ( $res as $k => $v ) {
	$pds[$v['id']] = $v['name'];//内置道具
	$pdata[$v['id']] = $v;
}
//online //unline //delete
if ( $apitype == 'online' || $apitype == 'unline' || $apitype == 'delete' ) {
	$sqlp= $apitype == 'delete' ? ', `sort` = 255' : '';
	$sql = "UPDATE `lord_list_$apiname` SET `state` = ".array_search($apitype, array('online', 'unline', 'delete'))." $sqlp WHERE `id` = $id";
	$res = $pdo->getDB(1)->exec($sql);
	$res = $res ? apiGet($apiname, $apitype, array('id'=>$id)) : array('errno'=>8, 'error'=>"查询错误。 $sql");
	echo json_encode($res);
	exit;
}
//search
if ( $apitype == 'search' ) {
	$per_page = $Q['per_page'];
	$cur_page = $Q['cur_page'] * $per_page;
	foreach ( $searchs as $k => $v ) {
		if ( $v['col'] != 1 && isset($Q[$k]) ) {
			if ( $v['int'] ) $Q[$k] = intval($Q[$k]);
			if ( !$Q[$k] ) continue;
			$sql = "SELECT `".$v['col']."` FROM `lord_game_user` WHERE `$k` = ".$Q[$k];
			$res = $db->query($sql)->fetch(PDO::FETCH_ASSOC);
			if ( $res && is_array($res) && isset($searchs[$v['col']]) ) {
				$Q[$v['col']] = end($res);
				if ( $searchs[$v['col']]['int'] ) $Q[$v['col']] = intval($Q[$v['col']]);
			}
		}
	}
	$where = array();
	foreach ( $searchs as $k => $v ) {
		if ( $v['col'] != 1 ) continue;
		switch ( $v['type'] ) {
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
	$sql = "SELECT * FROM `lord_list_$apiname` {$where} ORDER BY `cd` ASC, `pd` ASC, `sort` ASC, `id` ASC LIMIT {$cur_page}, {$per_page}";
	$res = $db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
	$res = ( $res && is_array($res) ) ? $res : array();
	foreach ( $res as $k => $v )
	{
		$res[$k]['cd'] = $cds[$v['cd']];
		$res[$k]['overlay'] = $overlays[$v['overlay']];
		$res[$k]['number'] = $v['number'] ? $v['number'] : "不限";
		$res[$k]['second'] = $v['second'] ? (($v['second']/86400).'天') : "永久";
		$res[$k]['points'] = $v['points'] ? $v['points'] : "无损";
		$res[$k]['present'] = $presents[$v['present']];
		$res[$k]['pause'] = $pauses[$v['pause']];
		$res[$k]['repair'] = $repairs[$v['repair']];
		$res[$k]['useas'] = $useass[$v['useas']];
		$res[$k]['state'] = $v['state'];
		$res[$k]['state_'] = $states[$v['state']];
		$res[$k]['create_time'] = $v['create_time'] ? date("Y-m-d H:i:s", $v['create_time']) : "&nbsp;";
		$res[$k]['update_time'] = $v['update_time'] ? date("Y-m-d H:i:s", $v['update_time']) : "&nbsp;";
	}
	$json['data_res'] = $res;
	$sql = "SELECT count(*) as data_num FROM `lord_list_$apiname` {$where} ";
	$res = $db->query($sql)->fetch(PDO::FETCH_ASSOC);
	$json['data_num'] = $res ? $res['data_num'] : 0;
	echo json_encode($json);
	exit;
}
//update //insert
if ( $apitype == 'update' || $apitype == 'insert' ) {
	$pd = isset($_REQUEST['pd']) && isset($pds[intval($_REQUEST['pd'])]) ? intval($_REQUEST['pd']) : 2;
	$cd = $pdata[$pd]['cd'];
	$name = isset($_REQUEST['name']) ? trim($_REQUEST['name']) : $pdata[$pd]['name'];
	$resume = isset($_REQUEST['resume']) ? trim($_REQUEST['resume']) : $pdata[$pd]['resume'];
	$fileId = isset($_REQUEST['fileId']) ? intval($_REQUEST['fileId']) : 1;
	$number = isset($_REQUEST['number']) ? intval($_REQUEST['number']) : 0;
	$second = isset($_REQUEST['second']) ? intval($_REQUEST['second']) : 0;
	$points = isset($_REQUEST['points']) ? intval($_REQUEST['points']) : 0;
	$present = isset($_REQUEST['present']) && isset($presents[intval($_REQUEST['present'])]) ? intval($_REQUEST['present']) : 0;
	$pause = isset($_REQUEST['pause']) && isset($pauses[intval($_REQUEST['pause'])]) ? intval($_REQUEST['pause']) : 0;
	$repair = isset($_REQUEST['repair']) && isset($repairs[intval($_REQUEST['repair'])]) ? intval($_REQUEST['repair']) : 0;
	$useas = isset($_REQUEST['useas']) && isset($useass[intval($_REQUEST['useas'])]) ? intval($_REQUEST['useas']) : 0;
	$useto = isset($_REQUEST['useto']) ? intval($_REQUEST['useto']) : 0;
	$state = isset($_REQUEST['state']) && isset($states[intval($_REQUEST['state'])]) ? intval($_REQUEST['state']) : 0;
	$sort = isset($_REQUEST['sort']) && $_REQUEST['sort'] > 0 && $_REQUEST['sort'] < 255  ? intval($_REQUEST['sort']) : 99;
	$create_time = $update_time = $timenow;
	if ( $apitype == 'insert' ) $sql = "INSERT INTO `lord_list_$apiname` (`cd`,`pd`,`name`,`resume`,`fileId`,`number`,`second`,`points`,`present`,`pause`,`repair`,`useas`,`useto`,`state`,`sort`,`create_time`,`update_time`) VALUES ($cd, $pd, '$name', '$resume', $fileId, $number, $second, $points, $present, $pause, $repair, $useas, $useto, $state, $sort, $create_time, $update_time)";
	if ( $apitype == 'update' ) $sql = "UPDATE `lord_list_$apiname` SET `cd`=$cd,`pd`=$pd,`name`='$name',`resume`='$resume',`fileId`=$fileId,`number`=$number,`second`=$second,`points`=$points,`present`=$present,`pause`=$pause,`repair`=$repair,`useas`=$useas,`useto`=$useto,`state`=$state,`sort`=$sort,`update_time`=$update_time WHERE `id`=$id";
	$res = $pdo->getDB(1)->exec($sql);
	if ( $res ) {
		if ( $apitype == 'insert' ) $id = $pdo->getDB(1)->lastInsertId();
		$res = apiGet($apiname, $apitype, array('id'=>$id));
		if ( !$res['errno'] ) {
			header("Location: {$apiname}.php");
		}
	} else {
		$res = array('errno'=>8, 'error'=>"查询错误。 $sql");
	}
	echo json_encode($res);
	exit;
}
//modify
if ( $apitype == 'modify' ) {
	$sql = "SELECT * FROM `lord_list_$apiname` WHERE `id` = $id";
	$data = $db->query($sql)->fetch(PDO::FETCH_ASSOC);
	if ( !$data ) {
		echo "查询错误。 $sql";
		exit;
	}
}
//create
if ( $apitype == 'create' ) {
	$data = array();
}
//modify //create
if ( $apitype == 'modify' || $apitype == 'create' ) {
?>
<script type="text/javascript" src="../js/jquery.js"></script>
<script type="text/javascript" src="../js/My97DatePicker/WdatePicker.js"></script>
<link type="text/css" href="../bootstrap/css/bootstrap.min.css" rel="stylesheet" />
<style type="text/css">
.body{position:absolute;left:0;top:0;padding:0 0 0 10px;width:98%;}
legend{margin-bottom: 10px;}
table.table{ font-size: 12px;margin-bottom:8px;width: 100%!important;}
table.table th,table.table td{ white-space: nowrap;}
table.table td,table.table th{line-height:30px!important;}
table.table td select, table.table td input{height: 30px!important;margin:0!important;}
label{display: inline;}
.in_t{width: 600px;}
.in_t2{width: 100px;}
.in_a{width: 300px;height: 100px!important;margin:0!important;}
.tdth{width:70px;font-size:14px;white-space: nowrap;}
</style>
<script>
$(function(){
	//
});
</script>
<body>
<div class="body">

<fieldset>
	<legend><?=$apiword?> - <?=$apitypes[$apitype]?></legend>
	<form action="<?=$apiname?>.php" method="post">
		<input type="hidden" name="apitype" value="<?=$apitypei[array_search($apitype, $apitypei)+1]?>" />
		<input type="hidden" name="id" value="<?php if($data){echo $data['id'];} ?>" />
		<table class="table table-bordered table-condensed table-hover">
			<tr>
				<td class="tdth">内置道具:</td><?php $var = 'pd';?>
				<td><select name="<?=$var?>" class="span2"><?php foreach (${$var.'s'} as $k => $v) { echo "<option value='$k'".($data && $data[$var]==$k ? " selected='selected'" : "").">$v</option>"; } ?></select></td>
			</tr>
			<tr>
				<td class="tdth">物品名称:</td><?php $var = 'name';?>
				<td><input name="<?=$var?>" value="<?php if($data){echo $data[$var];} ?>" type="text" class="in_t2" /></td>
			</tr>
			<tr>
				<td class="tdth">简介描述:</td><?php $var = 'resume';?>
				<td><input name="<?=$var?>" value="<?php if($data){echo $data[$var];} ?>" type="text" class="in_t" /></td>
			</tr>
			<tr>
				<td class="tdth">图片编号:</td><?php $var = 'fileId';?>
				<td><input name="<?=$var?>" value="<?php if($data){echo $data[$var];} ?>" type="text" class="in_t2" /></td>
			</tr>
			<tr>
				<td class="tdth">叠加数量:</td><?php $var = 'number';?>
				<td><input name="<?=$var?>" value="<?php if($data){echo $data[$var];} ?>" type="text" class="in_t2" />(件) 0无限</td>
			</tr>
			<tr>
				<td class="tdth">可用时效:</td><?php $var = 'second';?>
				<td><input name="<?=$var?>" value="<?php if($data){echo $data[$var];} ?>" type="text" class="in_t2" />(秒) 0永久</td>
			</tr>
			<!-- <tr> -->
				<!-- <td class="tdth">可用持久:</td><?php $var = 'points';?> -->
				<!-- <td><input name="<?=$var?>" value="<?php if($data){echo $data[$var];} ?>" type="text" class="in_t2" />(点) 0不损</td> -->
			<!-- </tr> -->
			<!-- <tr> -->
				<!-- <td class="tdth">可否赠送:</td><?php $var = 'present';?> -->
				<!-- <td><select name="<?=$var?>" class="span2"><?php foreach (${$var.'s'} as $k => $v) { echo "<option value='$k'".($data && $data[$var]==$k ? " selected='selected'" : "").">$v</option>"; } ?></select></td> -->
			<!-- </tr> -->
			<!-- <tr> -->
				<!-- <td class="tdth">可否暂停:</td><?php $var = 'pause';?> -->
				<!-- <td><select name="<?=$var?>" class="span2"><?php foreach (${$var.'s'} as $k => $v) { echo "<option value='$k'".($data && $data[$var]==$k ? " selected='selected'" : "").">$v</option>"; } ?></select></td> -->
			<!-- </tr> -->
			<!-- <tr> -->
				<!-- <td class="tdth">可否修复:</td><?php $var = 'repair';?> -->
				<!-- <td><select name="<?=$var?>" class="span2"><?php foreach (${$var.'s'} as $k => $v) { echo "<option value='$k'".($data && $data[$var]==$k ? " selected='selected'" : "").">$v</option>"; } ?></select></td> -->
			<!-- </tr> -->
			<tr>
				<td class="tdth">使用用途:</td><?php $var = 'useas';?>
				<td><select name="<?=$var?>" class="span2"><?php foreach (${$var.'s'} as $k => $v) { echo "<option value='$k'".($data && $data[$var]==$k ? " selected='selected'" : "").">$v</option>"; } ?></select></td>
			</tr>
			<tr>
				<td class="tdth">使用效值:</td><?php $var = 'useto';?>
				<td><input name="<?=$var?>" value="<?php if($data){echo $data[$var];} ?>" type="text" class="in_t2" />使用用途的参数值</td>
			</tr>
			<tr>
				<td class="tdth">当前状态:</td><?php $var = 'state';?>
				<td><select name="<?=$var?>" class="span2"><?php foreach (${$var.'s'} as $k => $v) { echo "<option value='$k'".($data && $data[$var]==$k ? " selected='selected'" : "").">$v</option>"; } ?></select></td>
			</tr>
			<tr>
				<td class="tdth">显示顺序:</td><?php $var = 'sort';?>
				<td><input name="<?=$var?>" value="<?php if($data){echo $data[$var];} ?>" type="text" class="in_t2" />默认99</td>
			</tr>
			<tr>
				<td>&nbsp;</td>
				<td class="span1"><input type="submit" value="提交" class="btn" /></td>
			</tr>
		</table>
	</form>
</fieldset>

</div>
</body>
<?php
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
<script>
var per_page = 20;
var cur_page = 0;
$(function(){ finder(); $('select.finder').change(function(){ finder(); }); });
function linkto( o, to ) { var _a = to.split('/'); var _this = $(o); if ( _a[0] != "http" ) { var _b = self.location.href.split('/'); delete _b[_b.length-1]; to = _b.join('/') + to; } self.location.href=to; }
function ajaxto( o, to ) { var _a = to.split('/'); var _this = $(o); if ( _a[0] != "http" ) { var _b = self.location.href.split('/'); delete _b[_b.length-1]; to = _b.join('/') + to; } $.getJSON(to, function(data){ if ( data ) { if ( data.errno == 0 ) { alert("操作成功。"); finder(); } else { alert("操作失败["+data.errno+"]："+data.error); } } }); }
function previt() { if ( cur_page==0 ) { alert("已经是第一页"); return; } cur_page--; findit(); }
function nextit() { cur_page++; findit(1); }
function finder() { cur_page=0; findit(); }
function findit( is_next ) {
	var cd = $('#cd').val();
	$.post("<?=$apiname?>.php?apitype=search&per_page="+per_page+"&cur_page="+cur_page, {
		cd : cd
	}, function( data ) {
		if ( data == null || data == "" ) { alert("获取数据失败！"); return; }
		var datalist = eval("("+data+")");
		var datahtml = "";
		for ( var i=0; i<datalist.data_res.length; i++ ) {
			var o = datalist.data_res[i];
			datahtml += "<tr class='table-body'>";
			datahtml += "<td>"+o.id+"</td>";
			datahtml += "<td>"+(o.cd!=""?o.cd:"&nbsp;")+"</td>";
			datahtml += "<td>"+(o.name!=""?o.name:"&nbsp;")+"</td>";
			datahtml += "<td>"+o.number+"</td>";
			datahtml += "<td>"+o.second+"</td>";
			// datahtml += "<td>"+o.points+"</td>";
			// datahtml += "<td>"+o.present+"</td>";
			// datahtml += "<td>"+o.pause+"</td>";
			// datahtml += "<td>"+o.repair+"</td>";
			datahtml += "<td>"+o.useas+"</td>";
			datahtml += "<td>"+o.useto+"</td>";
			datahtml += "<td>"+o.state_+"</td>";
			datahtml += "<td>"+o.sort+"</td>";
			datahtml += "<td>"+o.create_time+"</td>";
			datahtml += "<td>"+o.update_time+"</td>";
			// datahtml += "<td>"+(o.state==2?"&nbsp;":("<a href='#' onclick='ajaxto(this,\"<?=$apiname?>.php?apitype=delete&id="+o.id+"\")'>删除</a>&nbsp;&nbsp;<a href='#' onclick='ajaxto(this,\"<?=$apiname?>.php?apitype="+(o.state>0?'online':'unline')+"&id="+o.id+"\")'>"+(o.state==1?'<span style="color:green;">发布</span>':'<span style="color:red;">撤销</span>')+"</a>&nbsp;&nbsp;<a href='#' onclick='linkto(this,\"<?=$apiname?>.php?apitype=modify&id="+o.id+"\")'>修改</a>"))+"</td>";
			datahtml += "<td>"+(o.state==2?"&nbsp;":("<a href='#' onclick='ajaxto(this,\"<?=$apiname?>.php?apitype=delete&id="+o.id+"\")'>删除</a>&nbsp;&nbsp;<a href='#' onclick='linkto(this,\"<?=$apiname?>.php?apitype=modify&id="+o.id+"\")'>修改</a>"))+"</td>";
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
		<?php foreach ( $searchs as $var => $set ) { $varn = $set['name'].': '; if ($set['type'] == 'select') { ?>
		<div class="span2"><select id="<?=$var?>" class="span2 finder">
			<?php if ($set['all']) {?><option value="all"><?=$varn?>全部</option><?php } ?>
			<?php foreach ( ${$var.'s'} as $k => $v ) { echo "<option value='$k'".($data && $data[$var]==$k ? " selected='selected'" : "").">{$varn}$v</option>"; } ?>
		</select></div>
		<?php } elseif ( $set['type'] == 'input' ) { ?>
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
		<th>当前状态</th>
		<th>显示顺序</th>
		<th>创建时间</th>
		<th>更新时间</th>
		<th>操作</th>
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
