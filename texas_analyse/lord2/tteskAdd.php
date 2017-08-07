<?php
$api = 'ttesk';//
header("Content-type: text/html; charset=utf-8");
require_once '../manage/checkPriv.php';
require_once '../include/database.class.php';
$ut_now = time();
$sql = "SELECT * FROM `lord_list_ttesksource` ORDER BY `sort`, `id`";
$res = $db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
if ( !$res ) $res = array();
$ttesks = array();
foreach ( $res as $k => $v )
{
	$ttesks[$v['id']] = $v['name'];
}
$typeids = array('0'=>'匹配目标','1'=>'误导目标');
$type = isset($_REQUEST['type']) ? trim($_REQUEST['type']) : 'add';//add delete modify
$ispost = isset($_REQUEST['ispost']) ? intval($_REQUEST['ispost']) : 0;
$id = isset($_REQUEST['id']) ? intval($_REQUEST['id']) : 0;
if ($type=='delete') {
	$sql = "DELETE FROM `lord_list_ttesk` WHERE `id` = $id";
	$res = $pdo->getDB(1)->exec($sql);
	$res = $res ? apiGet($api, $type, array('id'=>$id)) : array('errno'=>8, 'error'=>"查询错误。 $sql");
	echo json_encode($res);
	exit;
}
$data = array();
$conds1 = $conds2 = 0;
if ($type=='modify') {
	$sql = "SELECT * FROM `lord_list_ttesk` WHERE `id` = $id";
	$data = $pdo->getDB(1)->query($sql)->fetch(PDO::FETCH_ASSOC);
	if ( !$data ) {
		echo "出错了。";
		exit;
	}
	$conds = explode(' ', $data['conds']);
	$conds1 = isset($conds[0])?$conds[0]:0;
	$conds2 = isset($conds[1])?$conds[1]:0;
}
if ($ispost) {
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
	$typeid = isset($_REQUEST['typeid'])?intval($_REQUEST['typeid']):0;
	$conds = isset($_REQUEST['conds'])?$_REQUEST['conds']:array();
	if ($conds) $conds = array_unique($conds);
	foreach ( $conds as $k => $v )
	{
		if ( !$v ) unset($conds[$k]);
	}
	$conds = join(' ', $conds);
	$coins = isset($_REQUEST['coins'])?intval($_REQUEST['coins']):0;
	$coupon = isset($_REQUEST['coupon'])?intval($_REQUEST['coupon']):0;
	$prob = isset($_REQUEST['prob'])?intval($_REQUEST['prob']):0;
	$sort = isset($_REQUEST['sort'])&&intval($_REQUEST['sort'])?intval($_REQUEST['sort']):99;
	$create_time = $update_time = $ut_now;
	$errno = 0; $error = "";
	if ($type=='add') {
		$sql = "INSERT INTO `lord_list_ttesk` ";
		$sql.= "(`rooms`,`channels`,`users`,`typeid`,`conds`,`coins`,`coupon`,`prob`,`sort`,`create_time`,`update_time`) VALUES ";
		$sql.= "('$rooms','$channels','$users',$typeid,'$conds',$coins,$coupon, $prob, $sort, $create_time, $update_time)";
		$res = $pdo->getDB(1)->exec($sql);
		$res = $id = $pdo->getDB(1)->lastInsertId();
		if ( !$res ) {
			$errno = 1; $error = "查询错误： $sql";
		}
	}
	if ($type=='modify') {
		$sql = "UPDATE `lord_list_ttesk` SET `rooms`='$rooms',`channels`='$channels',`users`='$users',`typeid`=$typeid,";
		$sql.= "`conds`='$conds',`coins`=$coins,`coupon`=$coupon,`prob`=$prob,`sort`=$sort,`update_time`=$update_time WHERE `id`=$id";
		$res = $pdo->getDB(1)->exec($sql);
		if ( !$res ) {
			$errno = 1; $error = "查询错误： $sql";
		}
	}
	if ( !$errno ) {
		$sql = "SELECT * FROM `lord_list_ttesk` WHERE `id` = $id";
		$data = $db->query($sql)->fetch(PDO::FETCH_ASSOC);
		if ( $data && is_array($data) ) {
			$data['id'] = intval($data['id']);
			$data['typeid'] = intval($data['typeid']);
			$data['sort'] = intval($data['sort']);
			$data['create_time'] = intval($data['create_time']);
			$data['update_time'] = intval($data['update_time']);
		} else {
			$data = array();
		}
		//security
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
		header("Location: {$api}List.php");
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
	<legend>牌局任务 - <?php if($type=='add'){?>创建<?php }else{?>修改<?php }?></legend>
	<form action="tteskAdd.php" method="post">
		<input type="hidden" name="ispost" value="1" />
		<input type="hidden" name="type" value="<?=$type?>" />
		<input type="hidden" name="id" value="<?php if($data){echo $data['id'];} ?>" />
		<table class="table table-bordered table-condensed table-hover">
			<tr>
				<th>房间限制:</th>
				<td><input name="rooms" value="<?php if($data){echo $data['rooms'];} ?>" type="text" class="in_t" /></td>
				<td>房间编号id，以英文空格隔开</td>
			</tr>
			<tr>
				<th>渠道限制:</th>
				<td><input name="channels" value="<?php if($data){echo $data['channels'];} ?>" type="text" class="in_t" /></td>
				<td>渠道名称，以英文空格隔开</td>
			</tr>
			<tr>
				<th>用户限制:</th>
				<td><input name="users" value="<?php if($data){echo $data['users'];} ?>" type="text" class="in_t" /></td>
				<td>用户数据库里的UID(不是客户端看到的编号ID)，以英文空格隔开</td>
			</tr>
			<tr>
				<th>匹配类型:</th>
				<td>
					<select class="span2" name="typeid">
						<?php
						foreach ($typeids as $k => $v) {
							$sel = $data&&$data['typeid']==$k?" selected='selected'":"";
							echo "<option value='$k'$sel>$v</option>";
						}
						?>
					</select>
				</td>
				<td>必选。</td>
			</tr>
			<tr>
				<th>完成条件:</th>
				<td>
					<select class="span2" name="conds[]">
						<?php
						foreach ($ttesks as $k => $v) {
							$sel = $conds1==$k?" selected='selected'":"";
							echo "<option value='$k'$sel>$v</option>";
						}
						?>
					</select>
					且
					<select class="span2" name="conds[]">
						<option value='0'>无</option>
						<?php
						foreach ($ttesks as $k => $v) {
							$sel = $conds2==$k?" selected='selected'":"";
							echo "<option value='$k'$sel>$v</option>";
						}
						?>
					</select>
				</td>
				<td>至少选一，最多两个</td>
			</tr>
			<tr>
				<th>触发概率:</th>
				<td><input name="prob" value="<?php if($data){echo $data['prob'];} ?>" type="text" class="in_t" /></td>
				<td>数字</td>
			</tr>
			<tr>
				<th>奖励乐豆:</th>
				<td><input name="coins" value="<?php if($data){echo $data['coins'];} ?>" type="text" class="in_t" /></td>
				<td>数字</td>
			</tr>
			<tr>
				<th>奖励乐券:</th>
				<td><input name="coupon" value="<?php if($data){echo $data['coupon'];} ?>" type="text" class="in_t" /></td>
				<td>数字</td>
			</tr>
			<tr>
				<th>排序:</th>
				<td><input name="sort" value="<?php if($data){echo $data['sort'];} ?>" type="text" class="in_t" /></td>
				<td>选填。从小到大，默认99</td>
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
