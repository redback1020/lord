<?php
header("Content-type: text/html; charset=utf-8");
require_once '../manage/checkPriv.php';
require_once '../include/database.class.php';
$time = time();
$type = isset($_REQUEST['type']) ? trim($_REQUEST['type']) : 'add';//add delete modify //close
$ispost = isset($_REQUEST['ispost']) ? intval($_REQUEST['ispost']) : 0;
$id = isset($_REQUEST['id']) ? intval($_REQUEST['id']) : 0;
$idata = array();
$sql = "SELECT * FROM `lord_list_item` WHERE `state` = 0";
$res = $db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
if ( !$res ) $res = array();
foreach ( $res as $k => $v ) {
	$idata[$v['id']] = $v;
}
$isOpens = array('0'=>'全局关闭','1'=>'全局开放');//是否开放
$isMobis = array('0'=>'手机关闭','1'=>'手机开放');//是否开放
$entryMoneys = array('coins'=>'乐豆报名','coupon'=>'乐券报名'/*待扩展*/);//报名货币
$entryOuts = array('0'=>'不可取消','1'=>'返还货币','2'=>'不返货币');//取消报名
$entryFulls = array('0'=>'直接开赛且新开一场','1'=>'直接开赛且不开新场','2'=>'不可开赛且不可报名');//开赛时间未到，报名人数达到上限
$tableRules = array('0'=>'排名凑桌','1'=>'随机凑桌');//组桌规则 没有产生排名时自动使用1随机凑桌

if ( $type == 'delete' ) {
	$sql = "SELECT * FROM `lord_game_room` WHERE `id` = $id";
	$room = $db->fetch(PDO::FETCH_ASSOC);
	if ( ! $room ) { echo json_encode(array('errno'=>9, 'error'=>"查询错误。")); exit; }
	$sql = "UPDATE `lord_game_room` SET `is_del` = 1 WHERE `id` = $id AND `modelId` = 3";
	$res = $pdo->getDB(1)->exec($sql);
	$errno = 0; $error = "";
	if ( $res ) {
		//security
		$api = 'room';//
		$type = $type;//
		$res = apiGet($api, $type, array('id'=>$room['roomId']));
		//respond
		if ( $res ) {
			$errno = $res['errno']; $error = $res['error'];
		} else {
			$errno = 8; $error = "接口错误。";
		}
	} else {
		$errno = 9; $error = "查询错误。";
	}
	echo json_encode(array('errno'=>$errno, 'error'=>$error));
	exit;
}
if ($type=='close') {
	$sql = "SELECT * FROM `lord_game_room` WHERE `id` = $id";
	$room = $db->fetch(PDO::FETCH_ASSOC);
	if ( ! $room ) { echo json_encode(array('errno'=>9, 'error'=>"查询错误。")); exit; }
	$sql = "UPDATE `lord_game_room` SET `isOpen` = 0 WHERE `id` = $id AND `modelId` = 3";
	$res = $pdo->getDB(1)->exec($sql);
	$errno = 0; $error = "";
	if ( $res ) {
		//security
		$api = 'room';//
		$type = $type;//
		$res = apiGet($api, $type, array('id'=>$room['roomId']));
		//respond
		if ( $res ) {
			$errno = $res['errno']; $error = $res['error'];
		} else {
			$errno = 8; $error = "接口错误。";
		}
	} else {
		$errno = 9; $error = "查询错误。";
	}
	echo json_encode(array('errno'=>$errno, 'error'=>$error));
	exit;
}
$data = array();
if ($type=='modify') {
	$sql = "SELECT * FROM `lord_game_room` WHERE `id` = $id AND `modelId` = 3";
	$data = $db->query($sql)->fetch(PDO::FETCH_ASSOC);
	if ( ! $data ) {
		echo "出错了。";
		exit;
	}
	if ( ! $data['start'] ) {//随时开赛
		$data['start'] = '';
	} elseif ( 0 < $data['start'] && $data['start'] <= 86400 ) {//每天定时开赛
		$data['start'] = date("H:i:s", strtotime(date("Y-m-d")) + $data['start']);
	} elseif ( 86400 * 1 < $data['start'] && $data['start'] <= 86400 * 8  ) {//每周定时开赛
		$data['start'] = intval($data['start']/86400)." ".date("H:i:s", strtotime(date("Y-m-d")) + $data['start']%86400);
	} elseif ( 86400 * 8 < $data['start'] && $data['start'] <= 86400 * 39 ) {//每月定时开赛
		$days = intval($data['start']/86400) - 7;
		if ( $days < 10 ) $days = '0'.$days;
		$data['start'] = $days." ".date("H:i:s", strtotime(date("Y-m-d")) + $data['start']%86400);
	} else {//指定日期开赛
		$data['start'] = date("Y-m-d H:i:s", $data['start']);
	}
	$data['showRules'] = $data['showRules'] ? json_decode($data['showRules'],1):array();
	$awardRule = $data['awardRule'] ? json_decode($data['awardRule'],1):array();
	$prizes = array();
	$i = 0;
	foreach ( $awardRule as $k => $v )
	{
		$prizes[$i] = $v;
		$prizes[$i]['rank'] = $k;
		$prizes[$i]['otherId'] = $v['other']['id'];
		$prizes[$i]['otherName'] = $v['other']['name'];
		$i++;
	}
	$data['prizes'] = $prizes;
}
if ( $ispost ) {
	$modelId = 3;
	$mode = isset($_REQUEST['mode'])?trim($_REQUEST['mode']):'';
	$isOpen = isset($_REQUEST['isOpen'])?intval($_REQUEST['isOpen']):0;
	$isMobi = isset($_REQUEST['isMobi'])?intval($_REQUEST['isMobi']):0;
	$verMin = isset($_REQUEST['verMin'])?intval($_REQUEST['verMin']):0;
	$roomId = isset($_REQUEST['roomId'])?intval($_REQUEST['roomId']):0;
	$room = $name = isset($_REQUEST['room'])?trim($_REQUEST['room']):'';
	//当前版本只处理单条规则，暂不处理多条规则并列的现象
	$showRules = isset($_REQUEST['showRules'])?$_REQUEST['showRules']:array();
	$showRules['channel'] = isset($showRules['channel'])&&trim($showRules['channel'])?explode(" ",str_replace(array("\r","\n","  ")," ",trim($showRules['channel']))):array(); if ( !$showRules['channel'] ) unset($showRules['channel']);
	$showRules['channot'] = isset($showRules['channot'])&&trim($showRules['channot'])?explode(" ",str_replace(array("\r","\n","  ")," ",trim($showRules['channot']))):array(); if ( !$showRules['channot'] ) unset($showRules['channot']);
	$showRules['gold'] = isset($showRules['gold'])&&trim($showRules['gold'])?trim($showRules['gold']):''; if ( !$showRules['gold'] || strpos($showRules['gold'], '|')==false ) unset($showRules['gold']);
	$showRules['coins'] = isset($showRules['coins'])&&trim($showRules['coins'])?trim($showRules['coins']):''; if ( !$showRules['coins'] || strpos($showRules['coins'], '|')==false ) unset($showRules['coins']);
	$showRules['mixtime'] = isset($showRules['mixtime'])&&trim($showRules['mixtime'])?explode("/",str_replace(array("\r","\n"),"/",trim($showRules['mixtime']))):array(); if ( !$showRules['mixtime'] ) unset($showRules['mixtime']);
	$showRules = $db->quote($showRules ? json_encode(array($showRules)) : '');
	$baseCoins = isset($_REQUEST['baseCoins'])?intval($_REQUEST['baseCoins']):20;
	$rate = isset($_REQUEST['rate'])?intval($_REQUEST['rate']):15;
	$rateMax = isset($_REQUEST['rateMax'])?intval($_REQUEST['rateMax']):2100000000;
	$limitCoins = isset($_REQUEST['limitCoins'])?intval($_REQUEST['limitCoins']):2100000000;
	$rake = isset($_REQUEST['rake'])?intval($_REQUEST['rake']):0;
	$enter = isset($_REQUEST['enter'])?trim($_REQUEST['enter']):'';
	$enterLimit = isset($_REQUEST['enterLimit'])?intval($_REQUEST['enterLimit']):0;
	$enterLimit_ = isset($_REQUEST['enterLimit_'])?intval($_REQUEST['enterLimit_']):2100000000;
	$gameBombAdd = isset($_REQUEST['gameBombAdd'])?intval($_REQUEST['gameBombAdd']):0;
	$brief = isset($_REQUEST['brief'])?trim($_REQUEST['brief']):'';
	$entry = isset($_REQUEST['entry'])?trim($_REQUEST['entry']):'';
	$tips = isset($_REQUEST['tips'])?trim($_REQUEST['tips']):'';
	$rules = str_replace("\r", "", isset($_REQUEST['rules'])?trim($_REQUEST['rules']):'');
	$start = isset($_REQUEST['start'])?trim($_REQUEST['start']):'';
	if ( ! $start ) {
		$start = 0;
	} elseif ( strlen($start) == 8  ) {
		$start = strtotime(date("Y-m-d $start")) - strtotime(date("Y-m-d"));
	} elseif ( strlen($start) == 10 ) {
		$_start = explode(' ', $start);
		$week = $_start[0];
		$start = $_start[1];
		$start = strtotime(date("Y-m-d $start")) - strtotime(date("Y-m-d")) + $week*86400;
	} elseif ( strlen($start) == 11 ) {
		$_start = explode(' ', $start);
		$days = $_start[0];
		$start = $_start[1];
		$start = strtotime(date("Y-m-d $start")) - strtotime(date("Y-m-d")) + $days*86400 + 7*86400;
	} else {
		$start = strtotime($start);
	}
	$entryMoney = isset($_REQUEST['entryMoney'])?trim($_REQUEST['entryMoney']):'';
	$entryCost = isset($_REQUEST['entryCost'])?intval($_REQUEST['entryCost']):0;
	$entryTime = isset($_REQUEST['entryTime'])?intval($_REQUEST['entryTime']):0;
	$entryOut = isset($_REQUEST['entryOut'])?intval($_REQUEST['entryOut']):0;
	$entryOsec = isset($_REQUEST['entryOsec'])?intval($_REQUEST['entryOsec']):0;
	$entryOmax = isset($_REQUEST['entryOmax'])?intval($_REQUEST['entryOmax']):0;
	$entryMax = isset($_REQUEST['entryMax'])?intval($_REQUEST['entryMax']):0;
	$entryMin = isset($_REQUEST['entryMin'])?intval($_REQUEST['entryMin']):0;
	$entryFull = isset($_REQUEST['entryFull'])?intval($_REQUEST['entryFull']):2;
	$entryMore = isset($_REQUEST['entryMore'])?intval($_REQUEST['entryMore']):0;
	$entryLess = isset($_REQUEST['entryLess'])?intval($_REQUEST['entryLess']):0;
	$scoreInit = isset($_REQUEST['scoreInit'])?intval($_REQUEST['scoreInit']):0;
	$scoreRate = isset($_REQUEST['scoreRate'])?($_REQUEST['scoreRate']+0):0;
	$rankRule = isset($_REQUEST['rankRule'])?intval($_REQUEST['rankRule']):0;
	$tableRule = isset($_REQUEST['tableRule'])?intval($_REQUEST['tableRule']):0;
	$outRule = isset($_REQUEST['outRule'])?intval($_REQUEST['outRule']):0;
	$outValue = isset($_REQUEST['outValue'])?trim($_REQUEST['outValue']):'';
	$arr = isset($_REQUEST['prizes'])&&is_array($_REQUEST['prizes'])?$_REQUEST['prizes']:array();
	$prize = array();
	foreach ( $arr as $k => $prizes )
	{
		$rank = trim($prizes['rank']);
		if ( ! $rank ) continue;
		$prizes['coins'] = intval($prizes['coins']); if ( !$prizes['coins'] ) unset($prizes['coins']);
		$prizes['coupon'] = intval($prizes['coupon']); if ( !$prizes['coupon'] ) unset($prizes['coupon']);
		$prizes['lottery'] = intval($prizes['lottery']); if ( !$prizes['lottery'] ) unset($prizes['lottery']);
		if ( intval($prizes['otherId']) && trim($prizes['otherName']) ) {
			$prizes['other'] = array('id'=>intval($prizes['otherId']), 'name'=>trim($prizes['otherName']));
		}
		unset($prizes['rank']); unset($prizes['otherId']); unset($prizes['otherName']);
		if ( isset($prizes['items']) ) {
			$items = array();
			foreach ( $prizes['items'] as $iid => $v )
			{
				if ( !isset($idata[$iid]) || !(isset($v['num'])&&$v['num']>0) ) continue;
				$items[$iid] = array('id'=>$iid, 'name'=>$idata[$iid]['name'], 'cd'=>$idata[$iid]['cd'], 'num'=>intval($v['num']),'ext'=>0);
			}
			if ( $items ) {
				$prizes['items'] = $items;
			} else {
				unset($prizes['items']);
			}
		}
		$prize[$rank] = $prizes;
	}
	$awardRule = $db->quote($prize ? json_encode($prize) : '');
	$sort = isset($_REQUEST['sort'])?intval($_REQUEST['sort']):$roomId;
	$create_time = $update_time = $time;
	if ( $type == 'add' ) {
		$sql = "INSERT INTO `lord_game_room` (";
		$sql.= "`isOpen`,`isMobi`,`verMin`,`modelId`,`mode`,`roomId`,`room`,`name`,`showRules`,`baseCoins`,`rate`,`rateMax`,";
		$sql.= "`limitCoins`,`rake`,`enter`,`enterLimit`,`enterLimit_`,`gameBombAdd`,`brief`,`entry`,`tips`,`rules`,`start`,";
		$sql.= "`entryMoney`,`entryCost`,`entryTime`,`entryOut`,`entryOsec`,`entryOmax`,`entryMax`,`entryMin`,`entryFull`,`entryMore`,";
		$sql.= "`entryLess`,`scoreInit`,`scoreRate`,`rankRule`,`tableRule`,`outRule`,`outValue`,`awardRule`,`sort`,`create_time`,`update_time`";
		$sql.= ") VALUES (";
		$sql.= "$isOpen,$isMobi,$verMin,$modelId,'$mode',$roomId,'$room','$name',$showRules,$baseCoins,$rate,$rateMax,";
		$sql.= "$limitCoins,$rake,'$enter',$enterLimit,$enterLimit_,$gameBombAdd,'$brief','$entry','$tips','$rules',$start,";
		$sql.= "'$entryMoney',$entryCost,$entryTime,$entryOut,$entryOsec,$entryOmax,$entryMax,$entryMin,$entryFull,$entryMore,";
		$sql.= "$entryLess,$scoreInit,$scoreRate,$rankRule,$tableRule,$outRule,'$outValue',$awardRule,$sort,$create_time,$update_time";
		$sql.= ")";
		$res = $pdo->getDB(1)->exec($sql);
		$res = $id = $pdo->getDB(1)->lastInsertId();
	}
	if ( $type == 'modify' ) {
		$sql = "UPDATE `lord_game_room` SET ";
		$sql.= "`isOpen`=$isOpen,`isMobi`=$isMobi,`verMin`=$verMin,`modelId`=$modelId,`mode`='$mode',`roomId`=$roomId,`room`='$room',";
		$sql.= "`name`='$name',`showRules`=$showRules,`baseCoins`=$baseCoins,`rate`=$rate,`rateMax`=$rateMax,`limitCoins`=$limitCoins,";
		$sql.= "`rake`=$rake,`enter`='$enter',`enterLimit`=$enterLimit,`enterLimit_`=$enterLimit_,`gameBombAdd`=$gameBombAdd,`brief`='$brief',";
		$sql.= "`entry`='$entry',`tips`='$tips',`rules`='$rules',`start`=$start,`entryMoney`='$entryMoney',`entryCost`=$entryCost,";
		$sql.= "`entryTime`=$entryTime,`entryOut`=$entryOut,`entryOsec`=$entryOsec,`entryOmax`=$entryOmax,`entryMax`=$entryMax,";
		$sql.= "`entryMin`=$entryMin,`entryFull`=$entryFull,`entryMore`=$entryMore,`entryLess`=$entryLess,`scoreInit`=$scoreInit,";
		$sql.= "`scoreRate`=$scoreRate,`rankRule`=$rankRule,`tableRule`=$tableRule,`outRule`=$outRule,`outValue`='$outValue',`awardRule`=$awardRule,";
		$sql.= "`sort`=$sort,`update_time`=$update_time WHERE `id`=$id AND `modelId` = 3";
		$res = $pdo->getDB(1)->exec($sql);
	}
	$errno = 0; $error = "";
	if ( $res ) {
		//security
		$api = 'room';//
		$type = $type;//
		$res = apiGet($api, $type, array('id'=>$roomId));
		//respond
		if ( $res ) {
			$errno = $res['errno']; $error = $res['error'];
		} else {
			$errno = 8; $error = "接口错误。";
		}
	} else {
		$errno = 9; $error = "查询错误。";
	}
	$res = json_encode(array('errno'=>$errno, 'error'=>$error));
	if ( !$errno ) {
		header('Location: lobbyMRoomList.php');
	}
	else {
		echo $res;
		exit;
	}
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
.in_a{width: 400px;height: 100px!important;margin:0!important;}
.tdth{width:70px!important;font-size:14px;}
.g{width:200px;float:left;}
</style>
<script>
$(function(){
	//
});
</script>

<body>
<div style="position:absolute;left:0;top:0;padding:0 10px;">

<fieldset>
	<legend>比赛场次 - <?php if($type=='add'){?>创建<?php }else{?>编辑<?php }?></legend>
	<form action="lobbyMRoomAdd.php" method="post">
		<input type="hidden" name="ispost" value="1" />
		<input type="hidden" name="type" value="<?=$type?>" />
		<input type="hidden" name="id" value="<?php if($data){echo $data['id'];} ?>" />
		<input type="hidden" name="modelId" value="3" />
		<table class="table table-bordered table-condensed table-hover">
			<tr>
				<td class="tdth">比赛名称:</td>
				<td><input name="mode" value="<?php if($data){echo $data['mode'];} ?>" type="text" class="in_t" /> 所有同场次的比赛名称必须同一</td>
			</tr>
			<tr>
				<td class="tdth">场次编号:</td>
				<td><input name="roomId" value="<?php if($data){echo $data['roomId'];} ?>" type="text" class="in_t" /></td>
			</tr>
			<tr>
				<td class="tdth">场次排序:</td>
				<td><input name="sort" value="<?php if($data){echo $data['sort'];} ?>" type="text" class="in_t" /> 越小越靠前，默认为场次编号</td>
			</tr>
			<tr>
				<td class="tdth">场次名称:</td>
				<td><input name="room" value="<?php if($data){echo $data['room'];} ?>" type="text" class="in_t" /></td>
			</tr>
			<tr>
				<td class="tdth">展现规则:<br/>全局开关:</td><?php $var = 'isOpen';?>
				<td><select name="<?=$var?>" class="span2"><?php foreach (${$var.'s'} as $k => $v) { echo "<option value='$k'".($data && $data[$var]==$k ? " selected='selected'" : "").">$v</option>"; } ?></select></td>
			</tr>
			<tr>
				<td class="tdth">展现规则:<br/>手机开关:</td><?php $var = 'isMobi';?>
				<td><select name="<?=$var?>" class="span2"><?php foreach (${$var.'s'} as $k => $v) { echo "<option value='$k'".($data && $data[$var]==$k ? " selected='selected'" : "").">$v</option>"; } ?></select></td>
			</tr>
			<tr>
				<td class="tdth">展现规则:<br/>最低版本:</td><?php $var = 'verMin';?>
				<td><input name="<?=$var?>" value="<?php if($data){echo $data[$var];} ?>" type="text" class="in_t" />填写数字化版本。10801=1.8.1</td>
			</tr>
			<tr>
				<td class="tdth">展现规则:<br/>许可渠道</td>
				<td><input name="showRules[channel]" value="<?php if($data){echo isset($data['showRules'][0]['channel'])?join(' ',$data['showRules'][0]['channel']):'';} ?>" type="text" class="in_t" />英文渠道名。英文空格分隔，不填则不限制，不建议过多</td>
			</tr>
			<tr>
				<td class="tdth">展现规则:<br/>屏蔽渠道</td>
				<td><input name="showRules[channot]" value="<?php if($data){echo isset($data['showRules'][0]['channot'])?join(' ',$data['showRules'][0]['channot']):'';} ?>" type="text" class="in_t" />英文渠道名。英文空格分隔，不填则不限制，不建议过多</td>
			</tr>
			<tr>
				<td class="tdth">展现规则:<br/>限制乐豆</td>
				<td><input name="showRules[coins]" value="<?php if($data){echo isset($data['showRules'][0]['coins'])?$data['showRules'][0]['coins']:'';} ?>" type="text" class="in_t" />乐豆范围，不填则不限制。举例，“0-5000”，用户乐豆&gt;=0且&lt;5000时显现</td>
			</tr>
			<tr>
				<td class="tdth">展现规则:<br/>限制时间</td>
				<td><textarea name="showRules[mixtime]" class="in_a"><?php if($data){echo isset($data['showRules'][0]['mixtime'])?join("\r\n",$data['showRules'][0]['mixtime']):'';} ?></textarea>每行的英文符号组合作为一个时间规则，符合任一行即显示，不填则不限制，不建议过多。<br>每行格式，“2015-05-01 09:00:00|2015-05-31 23:30:00|67”，代表着5月份内的周六周日的9点到23点半之间才显现</td>
			</tr>
			<tr>
				<td class="tdth">牌局底分:</td><?php $var = 'baseCoins';?>
				<td><input name="<?=$var?>" value="<?php if($data){echo $data[$var];} ?>" type="text" class="in_t" /></td>
			</tr>
			<tr>
				<td class="tdth">牌局加炸:</td><?php $var = 'gameBombAdd';?>
				<td><input name="<?=$var?>" value="<?php if($data){echo $data[$var];} ?>" type="text" class="in_t" /></td>
			</tr>
			<tr>
				<td class="tdth">开赛简介:</td><?php $var = 'brief';?>
				<td><input name="<?=$var?>" value="<?php if($data){echo $data[$var];} ?>" type="text" class="in_t" /></td>
			</tr>
			<tr>
				<td class="tdth">报名简介:</td><?php $var = 'entry';?>
				<td><input name="<?=$var?>" value="<?php if($data){echo $data[$var];} ?>" type="text" class="in_t" /></td>
			</tr>
			<tr>
				<td class="tdth">场次提示:</td><?php $var = 'tips';?>
				<td><input name="<?=$var?>" value="<?php if($data){echo $data[$var];} ?>" type="text" class="in_t" /></td>
			</tr>
			<tr>
				<td class="tdth">场次规则:</td><?php $var = 'rules';?>
				<td><textarea name="rules" class="in_a"><?php if($data){echo $data[$var];} ?></textarea></td>
			</tr>
			<tr>
				<td class="tdth">开赛时间:</td><?php $var = 'start';?>
				<td><input name="<?=$var?>" value="<?php if($data){echo $data[$var];} ?>" type="text" class="in_t" /><br/>“”留空随时开赛；“12:00:00”每天12点开赛；“1 12:00:00”每周一12点开赛；“01 12:00:00”每月1号12点开赛；“2016-08-12 12:00:00”固定日期开赛</td>
			</tr>
			<tr>
				<td class="tdth">报名货币:</td><?php $var = 'entryMoney';?>
				<td><select name="<?=$var?>" class="span2"><?php foreach (${$var.'s'} as $k => $v) { echo "<option value='$k'".($data && $data[$var]==$k ? " selected='selected'" : "").">$v</option>"; } ?></select></td>
			</tr>
			<tr>
				<td class="tdth">报名费用:</td><?php $var = 'entryCost';?>
				<td><input name="<?=$var?>" value="<?php if($data){echo $data[$var];} ?>" type="text" class="in_t" /> 0为免费报名</td>
			</tr>
			<tr>
				<td class="tdth">报名提前:</td><?php $var = 'entryTime';?>
				<td><input name="<?=$var?>" value="<?php if($data){echo $data[$var];} ?>" type="text" class="in_t" /> 开赛时间之前的n秒之内才可以报名</td>
			</tr>
			<tr>
				<td class="tdth">取消报名:</td><?php $var = 'entryOut';?>
				<td><select name="<?=$var?>" class="span2"><?php foreach (${$var.'s'} as $k => $v) { echo "<option value='$k'".($data && $data[$var]==$k ? " selected='selected'" : "").">$v</option>"; } ?></select></td>
			</tr>
			<tr>
				<td class="tdth">取消时间:</td><?php $var = 'entryOsec';?>
				<td><input name="<?=$var?>" value="<?php if($data){echo $data[$var];} ?>" type="text" class="in_t" /> 报名成功后，n秒内不可取消报名</td>
			</tr>
			<tr>
				<td class="tdth">取消人数:</td><?php $var = 'entryOmax';?>
				<td><input name="<?=$var?>" value="<?php if($data){echo $data[$var];} ?>" type="text" class="in_t" /> 报名人数满n人后，不可取消报名</td>
			</tr>
			<tr>
				<td class="tdth">报满人数:</td><?php $var = 'entryMax';?>
				<td><input name="<?=$var?>" value="<?php if($data){echo $data[$var];} ?>" type="text" class="in_t" />人</td>
			</tr>
			<tr>
				<td class="tdth">开赛下限:</td><?php $var = 'entryMin';?>
				<td><input name="<?=$var?>" value="<?php if($data){echo $data[$var];} ?>" type="text" class="in_t" />人</td>
			</tr>
			<tr>
				<td class="tdth">提前报满:</td><?php $var = 'entryFull';?>
				<td><select name="<?=$var?>" class="span2"><?php foreach (${$var.'s'} as $k => $v) { echo "<option value='$k'".($data && $data[$var]==$k ? " selected='selected'" : "").">$v</option>"; } ?></select> 只对定时开赛的场次有效</td>
			</tr>
			<tr>
				<td class="tdth">凑桌规则:</td><?php $var = 'tableRule';?>
				<td><select name="<?=$var?>" class="span2"><?php foreach (${$var.'s'} as $k => $v) { echo "<option value='$k'".($data && $data[$var]==$k ? " selected='selected'" : "").">$v</option>"; } ?></select> 首局默认使用随机组桌</td>
			</tr>
			<tr>
				<td class="tdth">初始积分:</td><?php $var = 'scoreInit';?>
				<td><input name="<?=$var?>" value="<?php if($data){echo $data[$var];} ?>" type="text" class="in_t" /> 分</td>
			</tr>
			<tr>
				<td class="tdth">积分缩水:</td><?php $var = 'scoreRate';?>
				<td><input name="<?=$var?>" value="<?php if($data){echo $data[$var];} ?>" type="text" class="in_t" /> 倍(默认为1，每局结束后不进行积分缩水)</td>
			</tr>
			<tr>
				<td class="tdth">淘汰次序:</td><?php $var = 'outValue';?>
				<td><input name="<?=$var?>" value="<?php if($data){echo $data[$var];} ?>" type="text" class="in_t" /> 英文逗号分隔</td>
			</tr>
			<tr>
				<td class="tdth">奖励配置:</td><?php $var = 'prizes';?>
				<td style="width:800px;">
					<div style="clear:both;">
						<div>奖品名次：<input name="<?=$var?>[0][rank]" value="<?php if($data&&isset($data[$var][0]['rank'])){echo $data[$var][0]['rank'];} ?>" type="text" class="in_t0" />
						奖品统称：<input name="<?=$var?>[0][text]" value="<?php if($data&&isset($data[$var][0]['text'])){echo $data[$var][0]['text'];} ?>" type="text" class="in_t" /></div>
						<div>实物图片：<input name="<?=$var?>[0][otherId]" value="<?php if($data&&isset($data[$var][0]['otherId'])){echo $data[$var][0]['otherId'];} ?>" type="text" class="in_t0" />
						实物名称：<input name="<?=$var?>[0][otherName]" value="<?php if($data&&isset($data[$var][0]['otherName'])){echo $data[$var][0]['otherName'];} ?>" type="text" class="in_t" /></div>
					</div>
					<div class="g"><input name="<?=$var?>[0][coins]" value="<?php if($data&&isset($data[$var][0]['coins'])){echo $data[$var][0]['coins'];} ?>" type="text" class="in_t0" />＊乐豆</div>
					<div class="g"><input name="<?=$var?>[0][coupon]" value="<?php if($data&&isset($data[$var][0]['coupon'])){echo $data[$var][0]['coupon'];} ?>" type="text" class="in_t0" />＊乐券</div>
					<?php foreach ( $idata as $k => $v ) { ?>
					<div class="g"><input name="<?=$var?>[0][items][<?=$k?>][num]" value="<?php if($data&&isset($data[$var][0]['items'][$k]['num'])){echo $data[$var][0]['items'][$k]['num'];} ?>" type="text" class="in_t0" />＊<?=$v['name']?></div>
					<?php } ?>
					<div style="clear:both;">
						<div>奖品名次：<input name="<?=$var?>[1][rank]" value="<?php if($data&&isset($data[$var][1]['rank'])){echo $data[$var][1]['rank'];} ?>" type="text" class="in_t0" />
						奖品统称：<input name="<?=$var?>[1][text]" value="<?php if($data&&isset($data[$var][1]['text'])){echo $data[$var][1]['text'];} ?>" type="text" class="in_t" /></div>
						<div>实物图片：<input name="<?=$var?>[1][otherId]" value="<?php if($data&&isset($data[$var][1]['otherId'])){echo $data[$var][1]['otherId'];} ?>" type="text" class="in_t0" />
						实物名称：<input name="<?=$var?>[1][otherName]" value="<?php if($data&&isset($data[$var][1]['otherName'])){echo $data[$var][1]['otherName'];} ?>" type="text" class="in_t" /></div>
					</div>
					<div class="g"><input name="<?=$var?>[1][coins]" value="<?php if($data&&isset($data[$var][1]['coins'])){echo $data[$var][1]['coins'];} ?>" type="text" class="in_t0" />＊乐豆</div>
					<div class="g"><input name="<?=$var?>[1][coupon]" value="<?php if($data&&isset($data[$var][1]['coupon'])){echo $data[$var][1]['coupon'];} ?>" type="text" class="in_t0" />＊乐券</div>
					<?php foreach ( $idata as $k => $v ) { ?>
					<div class="g"><input name="<?=$var?>[1][items][<?=$k?>][num]" value="<?php if($data&&isset($data[$var][1]['items'][$k]['num'])){echo $data[$var][1]['items'][$k]['num'];} ?>" type="text" class="in_t0" />＊<?=$v['name']?></div>
					<?php } ?>
					<div style="clear:both;">
						<div>奖品名次：<input name="<?=$var?>[2][rank]" value="<?php if($data&&isset($data[$var][2]['rank'])){echo $data[$var][2]['rank'];} ?>" type="text" class="in_t0" />
						奖品统称：<input name="<?=$var?>[2][text]" value="<?php if($data&&isset($data[$var][2]['text'])){echo $data[$var][2]['text'];} ?>" type="text" class="in_t" /></div>
						<div>实物图片：<input name="<?=$var?>[2][otherId]" value="<?php if($data&&isset($data[$var][2]['otherId'])){echo $data[$var][2]['otherId'];} ?>" type="text" class="in_t0" />
						实物名称：<input name="<?=$var?>[2][otherName]" value="<?php if($data&&isset($data[$var][2]['otherName'])){echo $data[$var][2]['otherName'];} ?>" type="text" class="in_t" /></div>
					</div>
					<div class="g"><input name="<?=$var?>[2][coins]" value="<?php if($data&&isset($data[$var][2]['coins'])){echo $data[$var][2]['coins'];} ?>" type="text" class="in_t0" />＊乐豆</div>
					<div class="g"><input name="<?=$var?>[2][coupon]" value="<?php if($data&&isset($data[$var][2]['coupon'])){echo $data[$var][2]['coupon'];} ?>" type="text" class="in_t0" />＊乐券</div>
					<?php foreach ( $idata as $k => $v ) { ?>
					<div class="g"><input name="<?=$var?>[2][items][<?=$k?>][num]" value="<?php if($data&&isset($data[$var][2]['items'][$k]['num'])){echo $data[$var][2]['items'][$k]['num'];} ?>" type="text" class="in_t0" />＊<?=$v['name']?></div>
					<?php } ?>
					<div style="clear:both;">
						<div>奖品名次：<input name="<?=$var?>[3][rank]" value="<?php if($data&&isset($data[$var][3]['rank'])){echo $data[$var][3]['rank'];} ?>" type="text" class="in_t0" />
						奖品统称：<input name="<?=$var?>[3][text]" value="<?php if($data&&isset($data[$var][3]['text'])){echo $data[$var][3]['text'];} ?>" type="text" class="in_t" /></div>
						<div>实物图片：<input name="<?=$var?>[3][otherId]" value="<?php if($data&&isset($data[$var][3]['otherId'])){echo $data[$var][3]['otherId'];} ?>" type="text" class="in_t0" />
						实物名称：<input name="<?=$var?>[3][otherName]" value="<?php if($data&&isset($data[$var][3]['otherName'])){echo $data[$var][3]['otherName'];} ?>" type="text" class="in_t" /></div>
					</div>
					<div class="g"><input name="<?=$var?>[3][coins]" value="<?php if($data&&isset($data[$var][3]['coins'])){echo $data[$var][3]['coins'];} ?>" type="text" class="in_t0" />＊乐豆</div>
					<div class="g"><input name="<?=$var?>[3][coupon]" value="<?php if($data&&isset($data[$var][3]['coupon'])){echo $data[$var][3]['coupon'];} ?>" type="text" class="in_t0" />＊乐券</div>
					<?php foreach ( $idata as $k => $v ) { ?>
					<div class="g"><input name="<?=$var?>[3][items][<?=$k?>][num]" value="<?php if($data&&isset($data[$var][3]['items'][$k]['num'])){echo $data[$var][3]['items'][$k]['num'];} ?>" type="text" class="in_t0" />＊<?=$v['name']?></div>
					<?php } ?>
					<div style="clear:both;">
						<div>奖品名次：<input name="<?=$var?>[4][rank]" value="<?php if($data&&isset($data[$var][4]['rank'])){echo $data[$var][4]['rank'];} ?>" type="text" class="in_t0" />
						奖品统称：<input name="<?=$var?>[4][text]" value="<?php if($data&&isset($data[$var][4]['text'])){echo $data[$var][4]['text'];} ?>" type="text" class="in_t" /></div>
						<div>实物图片：<input name="<?=$var?>[4][otherId]" value="<?php if($data&&isset($data[$var][4]['otherId'])){echo $data[$var][4]['otherId'];} ?>" type="text" class="in_t0" />
						实物名称：<input name="<?=$var?>[4][otherName]" value="<?php if($data&&isset($data[$var][4]['otherName'])){echo $data[$var][4]['otherName'];} ?>" type="text" class="in_t" /></div>
					</div>
					<div class="g"><input name="<?=$var?>[4][coins]" value="<?php if($data&&isset($data[$var][4]['coins'])){echo $data[$var][4]['coins'];} ?>" type="text" class="in_t0" />＊乐豆</div>
					<div class="g"><input name="<?=$var?>[4][coupon]" value="<?php if($data&&isset($data[$var][4]['coupon'])){echo $data[$var][4]['coupon'];} ?>" type="text" class="in_t0" />＊乐券</div>
					<?php foreach ( $idata as $k => $v ) { ?>
					<div class="g"><input name="<?=$var?>[4][items][<?=$k?>][num]" value="<?php if($data&&isset($data[$var][4]['items'][$k]['num'])){echo $data[$var][4]['items'][$k]['num'];} ?>" type="text" class="in_t0" />＊<?=$v['name']?></div>
					<?php } ?>
				</td>
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
