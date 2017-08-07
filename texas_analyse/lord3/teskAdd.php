<?php
$api = 'tesk';
header("Content-type: text/html; charset=utf-8");
require_once '../manage/checkPriv.php';
require_once '../include/database.class.php';
$types = array('0'=>'每日任务','1'=>'成长任务','2'=>'活动任务');
$gotos = array('0'=>'无','1'=>'去普通场','2'=>'去竞技场','3'=>'去充值中心');
$ut_now = time();
$apiName = $api;
$reqType = isset($_REQUEST['reqType']) ? trim($_REQUEST['reqType']) : 'add';//add modify delete online offline
$ispost = isset($_REQUEST['ispost']) ? intval($_REQUEST['ispost']) : 0;
$id = isset($_REQUEST['id']) ? intval($_REQUEST['id']) : 0;

$idata = array();
$sql = "SELECT * FROM `lord_list_item` WHERE `state` = 0";
$res = $db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
if ( !$res ) $res = array();
foreach ( $res as $k => $v ) {
	$idata[$v['id']] = $v;
}

if ( in_array($reqType, array('delete','online','offline')) ) {
	if ( $reqType=='delete' ) {
		$sql = "UPDATE `lord_game_{$api}` SET `is_del` = 1, `sort` = 99 WHERE `id` = $id";
	} elseif ( $reqType=='online' ) {
		$sql = "UPDATE `lord_game_{$api}` SET `is_online` = 1, `sort` = 1 WHERE `id` = $id";
	} elseif ( $reqType=='offline' ) {
		$sql = "UPDATE `lord_game_{$api}` SET `is_online` = 0, `sort` = 11 WHERE `id` = $id";
	}
	$res = $pdo->getDB(1)->exec($sql);
	if ( !$res ) {
		echo json_encode(array('errno'=>8, 'error'=>"查询错误。 $sql"));
		exit;
	}
	$sql = "SELECT * FROM `lord_game_{$api}` WHERE `id` = $id";
	$data = $db->query($sql)->fetch(PDO::FETCH_ASSOC);
	$data['id'] = intval($data['id']);
	$data['type'] = intval($data['type']);
	$data['prev'] = intval($data['prev']);
	$data['goto'] = intval($data['goto']);
	$data['target'] = intval($data['target']);
	$data['rooms'] = $data['rooms'] ? explode(" ", $data['rooms']) : array();
	$data['channels'] = $data['channels'] ? explode(" ", $data['channels']) : array();
	$data['users'] = $data['users'] ? explode(" ", $data['users']) : array();
	$data['start_time'] = intval($data['start_time']);
	$data['end_time'] = $data['end_time'] ? ($data['end_time'] + 86400) : 0;//包含当天
	$data['periodTime'] = intval($data['periodTime']);
	$data['periodId'] = intval($data['periodId']);
	$data['periodStart'] = intval($data['periodStart']);
	$data['periodEnd'] = intval($data['periodEnd']);
	$data['sourceId'] = intval($data['sourceId']);
	$data['accode'] = intval($data['accode']);
	$data['execut'] = $data['execut'] ? json_decode($data['execut'], 1) : array();
	$data['condit'] = $data['condit'] ? json_decode($data['condit'], 1) : array();
	$data['result'] = $data['result'] ? json_decode($data['result'], 1) : array();
	$data['prizes'] = $data['prizes'] ? json_decode($data['prizes'], 1) : array();
	$data['mailFileid'] = intval($data['mailFileid']);
	$data['is_surprise'] = intval($data['is_surprise']);
	$data['is_online'] = intval($data['is_online']);
	$data['is_del'] = intval($data['is_del']);
	$data['sort'] = intval($data['sort']);
	$data['create_time'] = intval($data['create_time']);
	$data['update_time'] = intval($data['update_time']);
	$res = apiPost($apiName, $reqType, $data);
	echo json_encode($res);
	exit;
}
$data = array();
if ($reqType=='modify') {
	$sql = "SELECT * FROM `lord_game_{$api}` WHERE `id` = $id";
	$data = $db->query($sql)->fetch(PDO::FETCH_ASSOC);
	if ( !$data ) {
		echo "查询错误。 $sql";
		exit;
	}
	$data['start_time'] = $data['start_time'] ? date("Y-m-d H:i:s", $data['start_time']) : "";
	$data['end_time'] = $data['end_time'] ? date("Y-m-d H:i:s", $data['end_time']) : "";
	$data['periodStart'] = $data['periodStart'] ? date("Y-m-d H:i:s", $data['periodStart']) : "";
	$data['periodEnd'] = $data['periodEnd'] ? date("Y-m-d H:i:s", $data['periodEnd']) : "";
	if ( $data['prizes'] ) {
		$data['prizes'] = json_decode($data['prizes'], 1);
		$propItems = array();
		if (isset($data['prizes']['propItems'])) {
			foreach ( $data['prizes']['propItems'] as $k => $v )
			{
				$propItems[$v['id']]['id'] = $v['id'];
				$propItems[$v['id']]['num'] = $v['num'];
				$propItems[$v['id']]['ext'] = $v['ext'];
			}
			$data['prizes']['propItems'] = $propItems;
		}
	}
}
if ($ispost) {
	$type = isset($_REQUEST['type'])?intval($_REQUEST['type']):0;
	$name = isset($_REQUEST['name'])?trim($_REQUEST['name']):'';
	$prev = isset($_REQUEST['prev'])?intval($_REQUEST['prev']):0;
	$goto = isset($_REQUEST['goto'])?intval($_REQUEST['goto']):0;
	$target = isset($_REQUEST['target'])?intval($_REQUEST['target']):0;
	$rooms = isset($_REQUEST['rooms'])?trim($_REQUEST['rooms']):'';
	$rooms = explode(' ', $rooms);
	foreach ( $rooms as $k => $v ) {
		if (!intval($v)) unset($rooms[$k]);
	}
	$rooms = join(' ', $rooms);
	$channels = isset($_REQUEST['channels'])?trim($_REQUEST['channels']):'';
	$channels = explode(' ', $channels);
	foreach ( $channels as $k => $v ) {
		if (!$v) unset($channels[$k]);
	}
	$channels = join(' ', $channels);
	$users = isset($_REQUEST['users'])?trim($_REQUEST['users']):'';
	$users = explode(' ', $users);
	foreach ( $users as $k => $v ) {
		if (!intval($v)) unset($users[$k]);
	}
	$users = join(' ', $users);
	$start_time = isset($_REQUEST['start_time'])&&trim($_REQUEST['start_time'])?strtotime(trim($_REQUEST['start_time'])):0;
	$end_time = isset($_REQUEST['end_time'])&&trim($_REQUEST['end_time'])?strtotime(trim($_REQUEST['end_time'])):0;
	$periodName = isset($_REQUEST['periodName'])?trim($_REQUEST['periodName']):'';
	$periodTime = isset($_REQUEST['periodTime'])?intval($_REQUEST['periodTime']):0;
	$periodStart = isset($_REQUEST['periodStart'])&&trim($_REQUEST['periodStart'])?strtotime(trim($_REQUEST['periodStart'])):0;
	$periodEnd = isset($_REQUEST['periodEnd'])&&trim($_REQUEST['periodEnd'])?strtotime(trim($_REQUEST['periodEnd'])):0;
	$sourceId = isset($_REQUEST['sourceId'])?intval($_REQUEST['sourceId']):0;
	if ( !$type ) {
		if ( !$periodName ) $periodName = '每天';
		if ( !$periodTime ) $periodTime = 86400;
		if ( !$periodStart ) $periodStart = strtotime(date("Y-m-d 00:00:00"));
		if ( !$periodEnd ) $periodEnd = strtotime(date("Y-m-d 23:59:59"));
	}
	$sql = "SELECT * FROM `lord_game_{$api}source` WHERE `id` = $sourceId";
	$src = $db->query($sql)->fetch(PDO::FETCH_ASSOC);
	$accode = $src['accode'];
	$action = $src['action'];
	$acttag = $src['acttag'];
	$execut = $db->quote($src['execut']);
	$condit = $db->quote($src['condit']);
	$result = $db->quote($src['result']);
	$prizeName = isset($_REQUEST['prizeName']) ? trim($_REQUEST['prizeName']) : '';
	$prizes = isset($_REQUEST['prizes']) ? $_REQUEST['prizes'] : array();
	$prizes['gold'] = intval($prizes['gold']); if ( !$prizes['gold'] ) unset($prizes['gold']);
	$prizes['golds'] = intval($prizes['golds']); if ( !$prizes['golds'] ) unset($prizes['golds']);
	$prizes['coins'] = intval($prizes['coins']); if ( !$prizes['coins'] ) unset($prizes['coins']);
	$prizes['coupon'] = intval($prizes['coupon']); if ( !$prizes['coupon'] ) unset($prizes['coupon']);
	$prizes['lottery'] = intval($prizes['lottery']); if ( !$prizes['lottery'] ) unset($prizes['lottery']);
	$prizes['other'] = trim($prizes['other']); if ( !$prizes['other'] ) unset($prizes['other']);
	if ( isset($prizes['propItems']) ) {
		$items = array();
		foreach ( $prizes['propItems'] as $iid => $v )
		{
			if ( !isset($idata[$iid]) || !(isset($v['num'])&&$v['num']>0) ) continue;
			$items[$iid] = array('id'=>$iid, 'name'=>$idata[$iid]['name'], 'cd'=>$idata[$iid]['cd'], 'num'=>intval($v['num']),'ext'=>0);
		}
		if ( $items ) {
			$prizes['propItems'] = $items;
		} else {
			unset($prizes['propItems']);
		}
	}
	$prizes = $prizes ? json_encode($prizes) : '';
	$prizes = $prizes ? $db->quote($prizes) : '';
	$mailSubject = isset($_REQUEST['mailSubject'])?trim($_REQUEST['mailSubject']):'';
	$mailContent = str_replace("\r", "", isset($_REQUEST['mailContent'])?trim($_REQUEST['mailContent']):'');
	$mailFileid = isset($_REQUEST['mailFileid'])?intval($_REQUEST['mailFileid']):0;
	$is_surprise = isset($_REQUEST['is_surprise'])?intval($_REQUEST['is_surprise']):0;
	$sort = isset($_REQUEST['sort'])&&intval($_REQUEST['sort'])?intval($_REQUEST['sort']):99;
	$create_time = $update_time = $ut_now;
	$errno = 0; $error = "";
	if ($reqType=='add') {
		$sql = "INSERT INTO `lord_game_{$api}` ";
		$sql.= "(`type`,`prev`,`goto`,`target`,`name`,`rooms`,`channels`,`users`,`start_time`,`end_time`,`periodName`,`periodTime`,`periodStart`,`periodEnd`,`sourceId`,`accode`,`action`,`acttag`,`execut`,`condit`,`result`,`prizeName`,`prizes`,`mailSubject`,`mailContent`,`mailFileid`,`is_surprise`,`sort`,`create_time`,`update_time`) VALUES ";
		$sql.= "($type,$prev,$goto,$target,'$name','$rooms','$channels','$users',$start_time,$end_time,'$periodName',$periodTime,$periodStart,$periodEnd,$sourceId,$accode,'$action','$acttag',$execut,$condit,$result,'$prizeName',$prizes,'$mailSubject','$mailContent',$mailFileid,$is_surprise,$sort,$create_time,$update_time)";
		$res = $pdo->getDB(1)->exec($sql);
		if ( $res ) $id = $pdo->getDB(1)->lastInsertId();
	}
	if ($reqType=='modify') {
		$sql = "UPDATE `lord_game_{$api}` SET `type`=$type,`prev`=$prev,`goto`=$goto,`target`=$target,`name`='$name',`rooms`='$rooms',`channels`='$channels',`users`='$users',`start_time`=$start_time,`end_time`=$end_time,`periodName`='$periodName',`periodTime`=$periodTime,`periodStart`=$periodStart,`periodEnd`=$periodEnd,";
		$sql.= "`sourceId`=$sourceId,`accode`=$accode,`action`='$action',`acttag`='$acttag',`execut`=$execut,`condit`=$condit,`result`=$result,`prizeName`='$prizeName',`prizes`=$prizes,`mailSubject`='$mailSubject',`mailContent`='$mailContent',`mailFileid`=$mailFileid,";
		$sql.= "`is_surprise`=$is_surprise,`sort`=$sort,`update_time`=$update_time WHERE `id`=$id";
		$res = $pdo->getDB(1)->exec($sql);
	}
	if ( !$res ) {
		echo json_encode(array('errno'=>8, 'error'=>"查询错误。 $sql"));
		exit;
	}
	$sql = "SELECT * FROM `lord_game_{$api}` WHERE `id` = $id";
	$data = $pdo->getDB(1)->query($sql)->fetch(PDO::FETCH_ASSOC);
	if ( $data && is_array($data) ) {
		$data['id'] = intval($data['id']);
		$data['type'] = intval($data['type']);
		$data['prev'] = intval($data['prev']);
		$data['goto'] = intval($data['goto']);
		$data['target'] = intval($data['target']);
		$data['rooms'] = $data['rooms'] ? explode(" ", $data['rooms']) : array();
		$data['channels'] = $data['channels'] ? explode(" ", $data['channels']) : array();
		$data['users'] = $data['users'] ? explode(" ", $data['users']) : array();
		$data['start_time'] = intval($data['start_time']);
		$data['end_time'] = intval($data['end_time']);
		$data['periodTime'] = intval($data['periodTime']);
		$data['periodId'] = intval($data['periodId']);
		$data['periodStart'] = intval($data['periodStart']);
		$data['periodEnd'] = intval($data['periodEnd']);
		$data['sourceId'] = intval($data['sourceId']);
		$data['accode'] = intval($data['accode']);
		$data['execut'] = $data['execut'] ? json_decode($data['execut'], 1) : array();
		$data['condit'] = $data['condit'] ? json_decode($data['condit'], 1) : array();
		$data['result'] = $data['result'] ? json_decode($data['result'], 1) : array();
		$data['prizes'] = $data['prizes'] ? json_decode($data['prizes'], 1) : array();
		$data['mailFileid'] = intval($data['mailFileid']);
		$data['is_surprise'] = intval($data['is_surprise']);
		$data['is_online'] = intval($data['is_online']);
		$data['is_del'] = intval($data['is_del']);
		$data['sort'] = intval($data['sort']);
		$data['create_time'] = intval($data['create_time']);
		$data['update_time'] = intval($data['update_time']);
	} else {
		$data = array();
	}
	$res = apiPost($apiName, $reqType, $data);
	if ( !$res['errno'] ) {
		header("Location: {$api}List.php");
	}
	echo json_encode($res);
	exit;
}

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
.in_t0{width: 60px;}
.in_t1{width: 120px;}
.in_t2{width: 240px;}
.in_t3{width: 480px;}
.in_a{width: 300px;height: 100px!important;margin:0!important;}
.tdth{width:70px!important;font-size:14px;}
.g{width:200px;float:left;}
input[type="radio"], input[type="checkbox"] {margin: 0;}
.in_t4{width: 70px;}
</style>
<script>
$(function(){
	//
});
</script>

<body>
<div style="position:absolute;left:0;top:0;padding:0 10px;">

<fieldset>
	<legend>动态任务 - <?php if($reqType=='add'){?>创建<?php }else{?>修改<?php }?></legend>
	<form action="<?=$api?>Add.php" method="post">
		<input type="hidden" name="ispost" value="1" />
		<input type="hidden" name="reqType" value="<?=$reqType?>" />
		<input type="hidden" name="id" value="<?php if($data){echo $data['id'];} ?>" />
		<table class="table table-bordered table-condensed table-hover">
			<tr>
				<th>温馨提示:</th>
				<td style="color:red;" colspan="2">
					所有新的动态任务发布，或旧的动态任务编辑修改，强烈建议在测试机上测试。当前没有时间做字段校验，请务必参照提示来输入。以后后台开发人来了，他会处理的。
				</td>
				<!-- <td>&nbsp;</td> -->
			</tr>
			<tr>
				<th>任务类型:</th>
				<td>
					<select id="type" name="type">
						<?php foreach ($types as $k => $v) {$sel = $data && $data['type'] == $k ? ' selected="selected"' : ''; echo "<option value='$k'$sel>$v</option>"; } ?>
					</select>
				</td>
				<td>&nbsp;</td>
			</tr>
			<tr>
				<th>任务引导:</th>
				<td>
					<select id="goto" name="goto">
						<?php foreach ($gotos as $gotoid => $gotoname) {$sel = $data && $data['goto'] == $gotoid ? ' selected="selected"' : ''; echo "<option value='$gotoid'$sel>$gotoname</option>"; } ?>
					</select>
				</td>
				<td>&nbsp;</td>
			</tr>
			<tr>
				<th>前置任务:</th>
				<td><input name="prev" value="<?php if($data){echo $data['prev'];} ?>" type="text" class="in_t0" /></td>
				<td>填写前置任务的id</td>
			</tr>
			<tr>
				<th>任务名称:</th>
				<td><input name="name" value="<?php if($data){echo $data['name'];} ?>" type="text" class="in_t2" /></td>
				<td>如果遇见标题中有%s字样，会在邮件标题中展现。注意客户端上呈现过长问题</td>
			</tr>
			<tr>
				<th>房间限制:</th>
				<td><input name="rooms" value="<?php if($data){echo $data['rooms'];} ?>" type="text" class="in_t3" /></td>
				<td>房间编号id，以英文空格隔开</td>
			</tr>
			<tr>
				<th>渠道限制:</th>
				<td><input name="channels" value="<?php if($data){echo $data['channels'];} ?>" type="text" class="in_t3" /></td>
				<td>渠道名称，以英文空格隔开</td>
			</tr>
			<tr>
				<th>用户限制:</th>
				<td><input name="users" value="<?php if($data){echo $data['users'];} ?>" type="text" class="in_t3" /></td>
				<td>用户数据库里的UID(不是客户端看到的编号ID)，以英文空格隔开</td>
			</tr>
			<tr>
				<th>起至日期:</th>
				<td>
					<input name="start_time" value="<?php if($data){echo $data['start_time'];} ?>" class="textbox dtime in_t4" style="width:90px;" type="text" readonly onclick="WdatePicker({dateFmt:'yyyy-MM-dd'})"/>(零点) -
					<input name="end_time" value="<?php if($data){echo $data['end_time'];} ?>" class="textbox dtime in_t4" style="width:90px;" type="text" readonly onclick="WdatePicker({dateFmt:'yyyy-MM-dd'})"/>(零点)
				</td>
				<td>起至日期范围的优先级高于周期设置里面的日期</td>
			</tr>
			<tr>
				<th>周期名称:</th>
				<td><input name="periodName" value="<?php if($data){echo $data['periodName'];} ?>" type="text" class="in_t1" /></td>
				<td>仅用于简短标记当前的周期特点</td>
			</tr>
			<tr>
				<th>周期时长:</th>
				<td><input name="periodTime" value="<?php if($data){echo $data['periodTime'];} ?>" type="text" class="in_t1" /></td>
				<td>任务的循环周期(秒)，1天=86400秒。如果为0则用不循环</td>
			</tr>
			<tr>
				<th>周期开始:</th>
				<td><input name="periodStart" value="<?php if($data){echo $data['periodStart'];} ?>" type="text" class="in_t2" /></td>
				<td>周期开始时间。不填则没有周期</td>
			</tr>
			<tr>
				<th>周期结束:</th>
				<td><input name="periodEnd" value="<?php if($data){echo $data['periodEnd'];} ?>" type="text" class="in_t2" /></td>
				<td>周期结束时间。不填则没有周期</td>
			</tr>
			<tr>
				<th>任务源码:</th>
				<td>
					<select id="sourceId" name="sourceId">
						<?php
						$sql = "SELECT `id`,`name` FROM `lord_game_{$api}source` WHERE `is_del` = 0 ORDER BY `name`, `sort`";
						$sourcelist = $db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
						foreach ($sourcelist as $val) {
							echo '<option value="'.$val['id'].'"'.($val['id']==$data['sourceId']?' selected="selected"':'').'>'.$val['name'].'</option>';
						}
						?>
					</select>
				</td>
				<td>如果想新创建任务源码，但又不熟悉的话，可咨询服务端技术</td>
			</tr>
			<tr>
				<th>目标数值:</th>
				<td><input name="target" value="<?php if($data){echo $data['target'];} ?>" type="text" class="in_t0" /></td>
				<td>目标数值仅用来在客户端展现，不和任务源码里面的完成条件冲突，实际识别任务完成还是依据源码</td>
			</tr>
			<tr>
				<th>奖励统称:</th>
				<td><input name="prizeName" value="<?php if($data){echo $data['prizeName'];} ?>" type="text" class="in_t2" /></td>
				<td>是下面所有奖励的简洁统称，在用户完成任务时的界面顶部的下拉提示栏的第二行展现。尽量简洁有效</td>
			</tr>
			<tr>
				<td class="tdth">奖励设置:</td><?php $var = 'prizes';?>
				<td><div style="width:600px;">
					<!-- <div class="g"><input name="<?=$var?>[gold]" value="<?php if($data&&isset($data[$var]['gold'])){echo $data[$var]['gold'];} ?>" type="text" class="in_t0" />＊乐币</div> -->
					<!-- <div class="g"><input name="<?=$var?>[golds]" value="<?php if($data&&isset($data[$var]['golds'])){echo $data[$var]['golds'];} ?>" type="text" class="in_t0" />＊代币</div> -->
					<div class="g"><input name="<?=$var?>[coins]" value="<?php if($data&&isset($data[$var]['coins'])){echo $data[$var]['coins'];} ?>" type="text" class="in_t0" />＊乐豆</div>
					<div class="g"><input name="<?=$var?>[coupon]" value="<?php if($data&&isset($data[$var]['coupon'])){echo $data[$var]['coupon'];} ?>" type="text" class="in_t0" />＊乐券</div>
					<div class="g"><input name="<?=$var?>[lottery]" value="<?php if($data&&isset($data[$var]['lottery'])){echo $data[$var]['lottery'];} ?>" type="text" class="in_t0" />＊抽奖数</div>
					<?php foreach ( $idata as $k => $v ) { ?>
					<div class="g"><input name="<?=$var?>[propItems][<?=$k?>][num]" value="<?php if($data&&isset($data[$var]['propItems'][$k]['num'])){echo $data[$var]['propItems'][$k]['num'];} ?>" type="text" class="in_t0" />＊<?=$v['name']?></div>
					<?php } ?>
					<div class="g" style="width:auto!important;"><input name="<?=$var?>[other]" value="<?php if($data&&isset($data[$var]['other'])){echo $data[$var]['other'];} ?>" type="text" class="in_t" />：其它奖励内容文字(换行=\n)</div>
				</div></td>
				<td>“其他”，比如填写“2000罐红牛”，只会在领取时以文字弹出。其他都是系统中有的奖励</td>
			</tr>
			<tr>
				<th>邮件标题:</th>
				<td><input name="mailSubject" value="<?php if($data){echo $data['mailSubject'];} ?>" type="text" class="in_t2" /></td>
				<td>如果里面有%s字样，发邮件时将会自动把本活动的名称写到％s的位置，在用户完成任务时的界面顶部的下拉提示栏的第二行展现。注意客户端上呈现过长问题</td>
			</tr>
			<tr>
				<th>邮件内容:</th>
				<td><textarea name="mailContent" class="in_a"><?php if($data){echo $data['mailContent'];} ?></textarea></td>
				<td>如果有[img]，将会自动使用下面的邮件用图id指向的图片，且这张图片需要用素材版本控制发布完成，否则客户端将无法显示。注意客户端上呈现过长问题</td>
			</tr>
			<tr>
				<th>邮件用图:</th>
				<td><input name="mailFileid" value="<?php if($data){echo $data['mailFileid'];} ?>" type="text" class="in_t0" /></td>
				<td>上面有[img]时，必须要有，必须在素材版本控制中已经完成发布</td>
			</tr>
			<tr>
				<th>惊喜礼包:</th>
				<td>
					<input name="is_surprise" <?php if($data){echo !$data['is_surprise']?"checked='checked'":'';}else{echo "checked='checked'";} ?> id="is_surprise0" type="radio" value="0" /><label for="is_surprise0">就不爆</label>
					　　　<input name="is_surprise" <?php if($data){echo $data['is_surprise']?"checked='checked'":'';} ?> id="is_surprise1" type="radio" value="1" /><label for="is_surprise1">爆了好</label>
				</td>
				<td>自动随机出“暴奖列表中”的奖品</td>
			</tr>
			<tr>
				<th>排序权重:</th>
				<td><input name="sort" value="<?php if($data){echo $data['sort'];} ?>" type="text" class="in_t0" /></td>
				<td>越小越靠上，默认99</td>
			</tr>
			<tr>
				<td>&nbsp;</td>
				<td class="span1" colspan="2"><input type="submit" value="提交" class="btn" /></td>
			</tr>
		</table>
	</form>
</fieldset>

</div>
</body>
