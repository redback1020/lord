<?php
$api = 'tteskrate';//
header("Content-type: text/html; charset=utf-8");
require_once '../manage/checkPriv.php';
require_once '../include/database.class.php';
$ut_now = time();
$type = isset($_REQUEST['type']) ? trim($_REQUEST['type']) : 'add';//add delete modify
$ispost = isset($_REQUEST['ispost']) ? intval($_REQUEST['ispost']) : 0;
$id = isset($_REQUEST['id']) ? intval($_REQUEST['id']) : 0;
if ($type=='delete') {
	$sql = "DELETE FROM `lord_list_tteskrate` WHERE `id` = $id";
	$res = $pdo->getDB(1)->exec($sql);
	$res = $res ? apiGet($api, $type, array('id'=>$id)) : array('errno'=>8, 'error'=>"查询错误。 $sql");
	echo json_encode($res);
	exit;
}
$data = array();
if ($type=='modify') {
	$sql = "SELECT * FROM `lord_list_tteskrate` WHERE `id` = $id";
	$data = $db->query($sql)->fetch(PDO::FETCH_ASSOC);
	if ( !$data ) {
		echo "出错了。";
		exit;
	}
}
if ($ispost) {
	$times = isset($_REQUEST['times'])?intval($_REQUEST['times']):'';
	$prob  = isset($_REQUEST['prob'])?intval($_REQUEST['prob']):'';
	$miss  = isset($_REQUEST['miss'])?intval($_REQUEST['miss']):'';
	$sort = isset($_REQUEST['sort'])&&intval($_REQUEST['sort'])?intval($_REQUEST['sort']):99;
	$create_time = $update_time = $ut_now;
	$errno = 0; $error = "";
	if ($type=='add') {
		$sql = "INSERT INTO `lord_list_tteskrate` ";
		$sql.= "(`times`,`prob`,`miss`,`sort`,`create_time`,`update_time`) VALUES ";
		$sql.= "($times, $prob, $miss, $sort, $create_time, $update_time)";
		$res = $pdo->getDB(1)->exec($sql);
		$res = $id = $pdo->getDB(1)->lastInsertId();
		if ( !$res ) {
			$errno = 1; $error = "查询错误： $sql";
		}
	}
	if ($type=='modify') {
		$sql = "UPDATE `lord_list_tteskrate` SET `times`=$times,`prob`=$prob,`miss`=$miss,`sort`=$sort,`update_time`=$update_time WHERE `id`=$id";
		$res = $pdo->getDB(1)->exec($sql);
		if ( !$res ) {
			$errno = 1; $error = "查询错误： $sql";
		}
	}
	if ( !$errno ) {
		$sql = "SELECT * FROM `lord_list_tteskrate` WHERE `id` = $id";
		$data = $db->query($sql)->fetch(PDO::FETCH_ASSOC);
		if ( $data && is_array($data) ) {
			$data['id'] = intval($data['id']);
			$data['times'] = intval($data['times']);
			$data['prob'] = intval($data['prob']);
			$data['miss'] = intval($data['miss']);
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
		// header("Location: {$api}List.php");
		header('Location: ttrateList.php');
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
	<legend>牌局任务可行性控制 - <?php if($type=='add'){?>创建<?php }else{?>修改<?php }?></legend>
	<form action="ttrateAdd.php" method="post">
		<input type="hidden" name="ispost" value="1" />
		<input type="hidden" name="type" value="<?=$type?>" />
		<input type="hidden" name="id" value="<?php if($data){echo $data['id'];} ?>" />
		<table class="table table-bordered table-condensed table-hover">
			<tr>
				<th>每日第N次完成:</th>
				<td><input name="times" value="<?php if($data){echo $data['times'];} ?>" type="text" class="in_t" /></td>
				<td>必填。比如，用户每日实际完成2次牌局任务时，使用配置表中有的<=2时的出现概率和误导概率，来计算触发牌局任务</td>
			</tr>
			<tr>
				<th>正常任务权重:</th>
				<td><input name="prob" value="<?php if($data){echo $data['prob'];} ?>" type="text" class="in_t" /></td>
				<td>必填。</td>
			</tr>
			<tr>
				<th>故意误导权重:</th>
				<td><input name="miss" value="<?php if($data){echo $data['miss'];} ?>" type="text" class="in_t" /></td>
				<td>必填。</td>
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
