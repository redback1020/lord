<?php
header("Content-type: text/html; charset=utf-8");
require_once '../manage/checkPriv.php';
require_once '../include/database.class.php';
$ut_now = time();
$type = isset($_REQUEST['type']) ? trim($_REQUEST['type']) : 'add';//add delete modify
$ispost = isset($_REQUEST['ispost']) ? intval($_REQUEST['ispost']) : 0;
$id = isset($_REQUEST['id']) ? intval($_REQUEST['id']) : 0;

$idata = array();
$sql = "SELECT * FROM `lord_list_item` WHERE `state` = 0";
$res = $db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
if ( !$res ) $res = array();
foreach ( $res as $k => $v ) {
	$idata[$v['id']] = $v;
}

if ($type=='delete') {
	$sql = "DELETE FROM `lord_user_inbox` WHERE `id` = $id";
	$res = $pdo->getDB(1)->exec($sql);
	$errno = 0; $error = "";
	if ( !$res ) {
		$errno = 1; $error = "操作失败。";
	}
	echo json_encode(array('errno'=>$errno, 'error'=>$error));
	exit;
}
$data = array();
if ($type=='modify') {
	$sql = "SELECT * FROM `lord_user_inbox` WHERE `id` = $id";
	$data = $db->query($sql)->fetch(PDO::FETCH_ASSOC);
	if ( !$data ) {
		echo "查询出错，请稍后重试。";
		exit;
	}
	$sql = "SELECT `cool_num` FROM `lord_game_user` WHERE `uid` = ".$data['uid'];
	$res = $db->query($sql)->fetch(PDO::FETCH_ASSOC);
	if ( !$res ) {
		echo "查询出错，请稍后重试。";
		exit;
	}
	$data['cool_num'] = $res['cool_num'];
	if ( $data['items'] ) {
		$data['prizes'] = json_decode($data['items'], 1);
		$items = array();
		if (isset($data['prizes']['items'])) {
			foreach ( $data['prizes']['items'] as $k => $v )
			{
				$items[$v['id']]['num'] = $v['num'];
			}
			$data['prizes']['items'] = $items;
		}
	}
}
if ($ispost) {
	$fromuid = isset($_REQUEST['fromuid'])?intval($_REQUEST['fromuid']):0;
	$mailtype = intval(!$fromuid);
	if ($type=="add") {
		$uid = isset($_REQUEST['uid'])?explode(" ",str_replace(array("\r","\n","  ")," ",trim($_REQUEST['uid']))):array();
		$uids = array();
		foreach ( $uid as $k => $v ) {
			if (intval($v) > 0) $uids[]=intval($v);
		}
		$cool_num = isset($_REQUEST['cool_num'])?explode(" ",str_replace(array("\r","\n","  ")," ",trim($_REQUEST['cool_num']))):array();
		if ($cool_num) {
			$cool_nums = array();
			foreach ( $cool_num as $k => $v ) {
				if (intval($v) > 0) $cool_nums[]=intval($v);
			}
			$cool_nums && $cool_nums = array_unique($cool_nums);
			if ($cool_nums) {
				$sql = "SELECT `uid` FROM `lord_game_user` WHERE `cool_num` IN (".join(',',$cool_nums).")";
				$res = $db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
				$res = $res ? $res : array();
				foreach ( $res as $k => $v )
				{
					$uids[]=$v['uid'];
				}
			}
		}
		$uids && $uids = array_unique($uids);
	}
	$subject = isset($_REQUEST['subject'])?trim($_REQUEST['subject']):'';
	$content = isset($_REQUEST['content'])?trim($_REQUEST['content']):'';
	$content = str_replace("\r", "", $content);
	if ( !$uids || !$subject || !$content ) {
		echo "填写错误，请改后重试。";
		exit;
	}
	if ($type=="add"&&$fromuid&&$fromuid<10000) {
		$sql = "SELECT `prizes` FROM `lord_game_topic` WHERE `id` = $fromuid";
		$res = $db->query($sql)->fetch(PDO::FETCH_ASSOC);
		$res = $res ? $res : array('prizes'=>'');
		$prizes = $res['prizes'];
	} elseif ($type=="add"&&$fromuid&&$fromuid<20000) {
		$sql = "SELECT `prizes` as prizes FROM `lord_game_tesk` WHERE `id` = $fromuid";
		$res = $db->query($sql)->fetch(PDO::FETCH_ASSOC);
		$res = $res ? $res : array('prizes'=>'');
		$prizes = $res['prizes'];
	} else {
		$prizes = isset($_REQUEST['prizes'])?$_REQUEST['prizes']:array();
		$prizes['gold'] = intval($prizes['gold']); if ( !$prizes['gold'] ) unset($prizes['gold']);
		$prizes['golds'] = intval($prizes['golds']); if ( !$prizes['golds'] ) unset($prizes['golds']);
		$prizes['coins'] = intval($prizes['coins']); if ( !$prizes['coins'] ) unset($prizes['coins']);
		$prizes['coupon'] = intval($prizes['coupon']); if ( !$prizes['coupon'] ) unset($prizes['coupon']);
		$prizes['lottery'] = intval($prizes['lottery']); if ( !$prizes['lottery'] ) unset($prizes['lottery']);
		$prizes['other'] = trim($prizes['other']); if ( !$prizes['other'] ) unset($prizes['other']);
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
		$prizes = $prizes ? json_encode($prizes) : '';
	}
	$prizes = $prizes ? $db->quote($prizes) : "''";
	$fileid = isset($_REQUEST['fileid'])?intval($_REQUEST['fileid']):0;
	$sort = isset($_REQUEST['sort'])&&intval($_REQUEST['sort'])?intval($_REQUEST['sort']):99;
	$create_time = $update_time = $ut_now;
	$errno = 0; $error = "";
	if ($type=='add') {
		if (!$uids) {
			$errno = 1; $error = "无效用户。";
			$res = json_encode(array('errno'=>$errno, 'error'=>$error));
			echo $res;
			exit;
		}
		$sqlp = array();
		foreach ( $uids as $k => $v ) {
			$sqlp[]= "($mailtype, $fromuid, $v, '$subject', '$content', $fileid, $prizes, $sort, $create_time, $update_time)";
		}
		$sql = "INSERT INTO `lord_user_inbox` (`type`,`fromuid`,`uid`,`subject`,`content`,`fileid`,`items`,`sort`,`create_time`,`update_time`) VALUES ".join(', ', $sqlp);
		$res = $pdo->getDB(1)->exec($sql);
		$res = $id = $pdo->getDB(1)->lastInsertId();
		if ( $res ) {
			$sql = "SELECT `uid`,`id`,`subject`,`content`,`fileid`,`items`,`is_read`,`sort` FROM `lord_user_inbox` WHERE `create_time` = $ut_now AND `update_time` = $ut_now ORDER BY `id` DESC";
			$data = $db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
			$data = $data && is_array($data) ? $data : array();
			foreach ( $data as $k => $v )
			{
				$data[$k]['items'] = intval(!!$v['items']);
			}
			//security
			$api = 'mail';//
			$type = 'add';//
			$res = apiPost($api, $type, $data);
			if ( $res ) {
				$errno = $res['errno']; $error = $res['error'];
			} else {
				$errno = 8; $error = "接口错误。";
			}
		} else {
			$errno = 9; $error = "查询错误。";
		}
	}
	if ($type=='modify') {
		$sql = "UPDATE `lord_user_inbox` SET `type`=$mailtype, `fromuid`=$fromuid, `subject`='$subject', `content`='$content', `fileid`=$fileid, `items`=$prizes,  `sort`=$sort, `update_time`=$update_time WHERE `id`=$id";
		$res = $pdo->getDB(1)->exec($sql);
		if ( !$res ) {
			$errno = 2; $error = "操作失败。";
		}
	}
	$res = json_encode(array('errno'=>$errno, 'error'=>$error));
	if ( !$errno ) {
		header('Location: userInboxList.php');
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
.in_a{width: 300px;height: 100px!important;margin:0!important;}
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
	<legend>邮件 - <?php if($type=='add'){?>新发送<?php }else{?>修改<?php }?></legend>
	<form action="userMailAdd.php" method="post">
		<input type="hidden" name="ispost" value="1" />
		<input type="hidden" name="type" value="<?=$type?>" />
		<input type="hidden" name="id" value="<?php if($data){echo $data['id'];} ?>" />
		<table class="table table-bordered table-condensed table-hover">
			<tr>
				<td class="tdth">温馨提示:</td>
				<td style="color:red">
					所有邮件在发送前，必须给我们自己的测试帐号发送，来查看实际效果。</br>
					在确保实际效果满意时，再执行大批用户发送，且每次最多不可超过200个用户。</br>
					邮件的奖励内容不能超过3个，否则无效。(如果需要请到产品负责人处提需求)。</br>
				</td>
			</tr>
			<tr>
				<td class="tdth">发件人:</td>
				<td>
					<select class="span2" id="fromuid" name="fromuid">
						<option value="0">系统全局</option>
						<?php
						$sql = "SELECT `id`,`subject` FROM `lord_game_topic` ORDER BY `sort` ASC, `id` DESC";
						$topiclist = $db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
						foreach ($topiclist as $val) {
							echo '<option value="'.$val['id'].'">活动'.$val['id'].':'.$val['subject'].'</option>';
						}
						?>
					</select>
				</td>
			</tr>
			<tr>
				<td class="tdth">收件人UID:</td>
				<?php if ($type=="add") { ?>
				<td><textarea name="uid" class="in_a"></textarea>数字＋英文空格/换行，与收件人编号(ID)合计数量不建议超过150个</td>
				<?php } else { ?>
				<td><?php if($data){echo $data['uid'];} ?></td>
				<?php } ?>
			</tr>
			<tr>
				<td class="tdth">收件人编号(ID):</td>
				<?php if ($type=="add") { ?>
				<td><textarea name="cool_num" class="in_a"></textarea>数字＋英文空格/换行，需要考虑靓号重复因素</td>
				<?php } else { ?>
				<td><?php if($data){echo $data['cool_num'];} ?></td>
				<?php } ?>
			</tr>
			<tr>
				<td class="tdth">邮件标题:</td>
				<td><input name="subject" value="<?php if($data){echo $data['subject'];} ?>" type="text" class="in_t" />必填，否则用户无法收到</td>
			</tr>
			<tr>
				<td class="tdth">邮件内容:</td>
				<td><textarea name="content" class="in_a"><?php if($data){echo $data['content'];} ?></textarea>必填，否则用户无法收到</td>
			</tr>
			<tr>
				<td class="tdth">内含奖励:</td><?php $var = 'prizes';?>
				<td style="width:800px;">
					<?php if ($type=="add") { ?>
					<div style="color:red">发送新邮件时，如果已经选择了发件人为某个活动，则自动使用活动里的奖励，下面的内容编辑无效</div>
					<?php } ?>
					<!-- <div class="g"><input name="<?=$var?>[gold]" value="<?php if($data&&isset($data[$var]['gold'])){echo $data[$var]['gold'];} ?>" type="text" class="in_t0" />＊乐币</div> -->
					<!-- <div class="g"><input name="<?=$var?>[golds]" value="<?php if($data&&isset($data[$var]['golds'])){echo $data[$var]['golds'];} ?>" type="text" class="in_t0" />＊代币</div> -->
					<div class="g"><input name="<?=$var?>[coins]" value="<?php if($data&&isset($data[$var]['coins'])){echo $data[$var]['coins'];} ?>" type="text" class="in_t0" />＊乐豆</div>
					<div class="g"><input name="<?=$var?>[coupon]" value="<?php if($data&&isset($data[$var]['coupon'])){echo $data[$var]['coupon'];} ?>" type="text" class="in_t0" />＊乐券</div>
					<div class="g"><input name="<?=$var?>[lottery]" value="<?php if($data&&isset($data[$var]['lottery'])){echo $data[$var]['lottery'];} ?>" type="text" class="in_t0" />＊抽奖数</div>
					<?php foreach ( $idata as $k => $v ) { ?>
					<div class="g"><input name="<?=$var?>[items][<?=$k?>][num]" value="<?php if($data&&isset($data[$var]['items'][$k]['num'])){echo $data[$var]['items'][$k]['num'];} ?>" type="text" class="in_t0" />＊<?=$v['name']?></div>
					<?php } ?>
					<div class="g" style="width:auto!important;"><input name="<?=$var?>[other]" value="<?php if($data&&isset($data[$var]['other'])){echo $data[$var]['other'];} ?>" type="text" class="in_t" />：其它奖励内容文字(换行=\n)</div>
				</td>
			</tr>
			<tr>
				<td class="tdth">邮件用图ID:</td>
				<td><input name="fileid" value="<?php if($data){echo $data['fileid'];} ?>" type="number" class="in_t" />如果有，必须先在邮件图片列表中创建好，否则无效</td>
			</tr>
			<tr>
				<td class="tdth">排序权重:</td>
				<td><input name="sort" value="<?php if($data){echo $data['sort'];} ?>" type="text" class="in_t" /> 越小越靠上，默认99</td>
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
