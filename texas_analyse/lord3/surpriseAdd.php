<?php
header("Content-type: text/html; charset=utf-8");
require_once '../manage/checkPriv.php';
require_once '../include/database.class.php';
$ut_now = time();
$type = isset($_REQUEST['type']) ? trim($_REQUEST['type']) : 'add';//add delete modify
$ispost = isset($_REQUEST['ispost']) ? intval($_REQUEST['ispost']) : 0;
$id = isset($_REQUEST['id']) ? intval($_REQUEST['id']) : 0;
if ($type=='delete') {
	$sql = "DELETE FROM `lord_game_surprise` WHERE `id` = $id";
	$res = $pdo->getDB(1)->exec($sql);
	$errno = 0; $error = "";
	if ( !$res ) {
		$errno = 1; $error = "查询错误： $sql";
	} else {
		//security
		$api = 'surprise';//
		$type = $type;//
		$res = apiGet($api, $type, array('id'=>$id));
		//respond
		if ( $res ) {
			$errno = $res['errno']; $error = $res['error'];
		} else {
			$errno = 9; $error = "接口错误。";
		}
	}
	echo json_encode(array('errno'=>$errno, 'error'=>$error));
	exit;
}
$data = array();
if ($type=='modify') {
	$sql = "SELECT * FROM `lord_game_surprise` WHERE `id` = $id";
	$data = $db->query($sql)->fetch(PDO::FETCH_ASSOC);
	if ( !$data ) {
		echo "出错了。";
		exit;
	}
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
	$name = isset($_REQUEST['name'])?trim($_REQUEST['name']):'';
	$teskids = isset($_REQUEST['teskids'])?trim($_REQUEST['teskids']):'';
	$teskids = $teskids ? json_encode(explode(' ', $teskids)) : '';
	$keyName = isset($_REQUEST['keyName'])?trim($_REQUEST['keyName']):'';
	$keyVal = isset($_REQUEST['keyVal'])?intval($_REQUEST['keyVal']):0;
	$keyExt = isset($_REQUEST['keyExt'])?intval($_REQUEST['keyExt']):0;
	$fileid = isset($_REQUEST['fileid'])?intval($_REQUEST['fileid']):0;
	$periodName = isset($_REQUEST['periodName'])?trim($_REQUEST['periodName']):'';
	$periodTime = isset($_REQUEST['periodTime'])?intval($_REQUEST['periodTime']):0;
	$periodStart = isset($_REQUEST['periodStart'])&&trim($_REQUEST['periodStart'])?strtotime(trim($_REQUEST['periodStart'])):0;
	$periodEnd = isset($_REQUEST['periodEnd'])&&trim($_REQUEST['periodEnd'])?strtotime(trim($_REQUEST['periodEnd'])):0;
	$times = isset($_REQUEST['times'])?intval($_REQUEST['times']):0;
	$chance = isset($_REQUEST['chance'])?intval($_REQUEST['chance']*100):0;
	$mailSubject = isset($_REQUEST['mailSubject'])?trim($_REQUEST['mailSubject']):'';
	$mailContent = str_replace("\r", "", isset($_REQUEST['mailContent'])?trim($_REQUEST['mailContent']):'');
	$mailFileid = isset($_REQUEST['mailFileid'])?intval($_REQUEST['mailFileid']):0;
	$is_grab = isset($_REQUEST['is_grab'])?intval($_REQUEST['is_grab']):0;
	$sort = isset($_REQUEST['sort'])&&intval($_REQUEST['sort'])?intval($_REQUEST['sort']):99;
	$create_time = $update_time = $ut_now;
	$errno = 0; $error = "";
	if ($type=='add') {
		$sql = "INSERT INTO `lord_game_surprise` ";
		$sql.= "(`teskids`,`name`,`keyName`,`keyVal`,`keyExt`,`fileid`,`periodName`,`periodTime`,`periodStart`,`periodEnd`,`times`,`chance`,`mailSubject`,`mailContent`,`mailFileid`,`is_grab`,`sort`,`create_time`,`update_time`) VALUES ";
		$sql.= "('$teskids','$name','$keyName',$keyVal,$keyExt,$fileid,'$periodName',$periodTime,$periodStart,$periodEnd,$times,$chance,'$mailSubject','$mailContent',$mailFileid,$is_grab,$sort,$create_time,$update_time)";
		$res = $pdo->getDB(1)->exec($sql);
		$res = $id = $pdo->getDB(1)->lastInsertId();
		if ( !$res ) {
			$errno = 1; $error = "查询错误： $sql";
		}
	}
	if ($type=='modify') {
		$sql = "UPDATE `lord_game_surprise` SET `teskids`='$teskids',`name`='$name',`keyName`='$keyName',`keyVal`=$keyVal,`keyExt`=$keyExt,`fileid`=$fileid,`periodName`='$periodName',`periodTime`=$periodTime,`periodStart`=$periodStart,`periodEnd`=$periodEnd,";
		$sql.= "`times`=$times,`chance`=$chance,`mailSubject`='$mailSubject',`mailContent`='$mailContent',`mailFileid`=$mailFileid,`is_grab`=$is_grab,`sort`=$sort,`update_time`=$update_time WHERE `id`=$id";
		$res = $pdo->getDB(1)->exec($sql);
		if ( !$res ) {
			$errno = 1; $error = "查询错误： $sql";
		}
	}
	if ( !$errno ) {
		$sql = "SELECT * FROM `lord_game_surprise` WHERE `id` = $id";
		$data = $db->query($sql)->fetch(PDO::FETCH_ASSOC);
		if ( $data && is_array($data) ) {
			$data['id'] = intval($data['id']);
			$data['teskids'] = $data['teskids']?json_decode($data['teskids'],1):array();
			$data['keyVal'] = intval($data['keyVal']);
			$data['keyExt'] = intval($data['keyExt']);
			$data['fileid'] = intval($data['fileid']);
			$data['periodTime'] = intval($data['periodTime']);
			$data['periodId'] = intval($data['periodId']);
			$data['periodStart'] = intval($data['periodStart']);
			$data['periodEnd'] = intval($data['periodEnd']);
			$data['times'] = intval($data['times']);
			$data['chance'] = intval($data['chance']);
			$data['mailFileid'] = intval($data['mailFileid']);
			$data['is_grab'] = intval($data['is_grab']);
			$data['is_del'] = intval($data['is_del']);
			$data['sort'] = intval($data['sort']);
			$data['create_time'] = intval($data['create_time']);
			$data['update_time'] = intval($data['update_time']);
		} else {
			$data = array();
		}
		//security
		$api = 'surprise';//
		$type = $type;//
		$res = apiPost($api, $type, $data);
		if ( $res ) {
			$errno = $res['errno']; $error = $res['error'];
		} else {
			$errno = 9; $error = "接口错误。";
		}
	}
	$res = json_encode(array('errno'=>$errno, 'error'=>$error));
	if ( !$errno ) {
		header('Location: surpriseList.php');
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
legend{margin-bottom: 10px!important;}
table.table{ font-size: 12px;width: 95%!important;}
table.table th{ white-space: nowrap;line-height:30px;width: 70px;font-size: 14px;}
label{display: inline;}
input[type="radio"], input[type="checkbox"] {margin: 0;}
select{margin:0;}
.in_t{width: 350px;height: 30px!important;margin: 0!important}
.in_t2{width: 50px;height: 30px!important;margin: 0!important}
.in_a{width: 350px;height: 100px!important;margin: 0!important}
</style>
<script>
$(function(){
	//
});
</script>

<body>
<div style="position:absolute;left:0;top:0;padding:0 10px;">

<fieldset>
	<legend>动态任务暴奖 - <?php if($type=='add'){?>创建<?php }else{?>修改<?php }?></legend>
	<form action="surpriseAdd.php" method="post">
		<input type="hidden" name="ispost" value="1" />
		<input type="hidden" name="type" value="<?=$type?>" />
		<input type="hidden" name="id" value="<?php if($data){echo $data['id'];} ?>" />
		<table class="table table-bordered table-condensed table-hover">
			<tr>
				<th>温馨提示:</th>
				<td style="color:red;white-space:nowrap;" colspan="2">
					所有新的动态任务发布，或旧的动态任务编辑修改，强烈建议在测试机上测试。当前没有时间做字段校验，请务必参照提示来输入。以后后台开发人来了，他会处理的。
				</td>
			</tr>
			<tr>
				<th>专属活动:</th>
				<td><input name="teskids" value="<?php if($data){echo $data['teskids'] ? join(' ',($teskids = json_decode($data['teskids'],1))?$teskids:array()) : '';} ?>" type="text" class="in_t" /></td>
				<td>选填。英文空格分割的活动id，只会在指定的(某些)活动中爆出。默认不填，可以在所有活动中暴出</td>
			</tr>
			<tr>
				<th>奖品名称:</th>
				<td><input name="name" value="<?php if($data){echo $data['name'];} ?>" type="text" class="in_t" /></td>
				<td>必填。将会直接输出到用户客户端界面，比如“富豪套装(108天)”，需要注意与下面三个相关内容框的数值匹配</td>
			</tr>
			<tr>
				<th>奖品字段:</th>
				<td><input name="keyName" value="<?php if($data){echo $data['keyName'];} ?>" type="text" class="in_t" /></td>
				<td>必填。乐豆:coins 乐券:coupon 抽奖:lottery 服装:propId 话费:tel_charge 其他:输入拼音即可</td>
			</tr>
			<tr>
				<th>奖品数值:</th>
				<td><input name="keyVal" value="<?php if($data){echo $data['keyVal'];} ?>" type="text" class="in_t" /></td>
				<td>必填。乐豆，乐券，抽奖次数，话费等，填入数量；服装类填入道具id，2高手套装3大师套装4富豪套装</td>
			</tr>
			<tr>
				<th>扩展数值:</th>
				<td><input name="keyExt" value="<?php if($data){echo $data['keyExt'];} ?>" type="text" class="in_t" /></td>
				<td>套装时必填。务必填入与奖品名称一致的套装天数</td>
			</tr>
			<tr>
				<th>奖品图片:</th>
				<td><input name="fileid" value="<?php if($data){echo $data['fileid'];} ?>" type="text" class="in_t" /></td>
				<td>必填。必须在素材版本控制中已经完成发布</td>
			</tr>
			<tr>
				<th>周期名称:</th>
				<td><input name="periodName" value="<?php if($data){echo $data['periodName'];} ?>" type="text" class="in_t" /></td>
				<td>仅用于简短标记当前的周期特点</td>
			</tr>
			<tr>
				<th>周期时长:</th>
				<td><input name="periodTime" value="<?php if($data){echo $data['periodTime'];} ?>" type="text" class="in_t" /></td>
				<td>任务的循环周期(秒)，1天=86400秒。如果为0则用不循环</td>
			</tr>
			<tr>
				<th>周期开始:</th>
				<td><input name="periodStart" value="<?php if($data){echo $data['periodStart'];} ?>" type="text" class="in_t" /></td>
				<td>周期开始时间。不填则没有周期</td>
			</tr>
			<tr>
				<th>周期结束:</th>
				<td><input name="periodEnd" value="<?php if($data){echo $data['periodEnd'];} ?>" type="text" class="in_t" /></td>
				<td>周期结束时间。不填则没有周期</td>
			</tr>
			<tr>
				<th>奖品总数:</th>
				<td><input name="times" value="<?php if($data){echo $data['times'];} ?>" type="text" class="in_t" /></td>
				<td>每个周期内的爆出次数上限。如果想总共就发一定数量，不设置周期即可。0则本周期内不限</td>
			</tr>
			<tr>
				<th>暴奖概率:</th>
				<td><input name="chance" value="<?php if($data){echo $data['chance']/100+0;} ?>" type="text" class="in_t2" />%</td>
				<td>0永不爆出</td>
			</tr>
			<tr>
				<th>邮件标题:</th>
				<td><input name="mailSubject" value="<?php if($data){echo $data['mailSubject'];} ?>" type="text" class="in_t" /></td>
				<td>不但在收件箱里展现，还在爆出后的界面顶部下拉渐出(第一行)</td>
			</tr>
			<tr>
				<th>邮件内容:</th>
				<td><textarea name="mailContent" class="in_a"><?php if($data){echo $data['mailContent'];} ?></textarea></td>
				<td>如果有[img]，将会自动使用下面的邮件用图id指向的图片，<br/>且这张图片需要用素材版本控制发布完成，否则客户端将无法显示</td>
			</tr>
			<tr>
				<th>邮件用图:</th>
				<td><input name="mailFileid" value="<?php if($data){echo $data['mailFileid'];} ?>" type="text" class="in_t" /></td>
				<td>上面有[img]时，必须要有，必须在素材版本控制中已经完成发布</td>
			</tr>
			<tr>
				<th>是否可抢:</th>
				<td>
					<input name="is_grab" <?php if($data){echo !$data['is_grab']?"checked='checked'":'';}else{echo "checked='checked'";} ?> id="is_grab0" type="radio" value="0" /><label for="is_grab0">不可抢</label>　　　
					<input name="is_grab" <?php if($data){echo $data['is_grab']?"checked='checked'":'';} ?> id="is_grab1" type="radio" value="1" /><label for="is_grab1">可抢</label>
				</td>
				<td>不在牌桌中时，如果随机出了可抢的奖品，将不会爆出</td>
			</tr>
			<tr>
				<th>排序权重:</th>
				<td><input name="sort" value="<?php if($data){echo $data['sort'];} ?>" type="text" class="in_t" /></td>
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
