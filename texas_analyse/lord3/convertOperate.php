<?php
header("Content-type: text/html; charset=utf-8");
require_once '../manage/checkPriv.php';
require_once '../include/database.class.php';
$itemtypes = array('coupon2mobifee'=>'乐券兑话费');
$states = array('0'=>'未处理', '1'=>'已发货');
$ut_now = time();
$type = isset($_REQUEST['type']) ? trim($_REQUEST['type']) : 'modify';//modify shipping
$ispost = isset($_REQUEST['ispost']) ? intval($_REQUEST['ispost']) : 0;
$id = isset($_REQUEST['id']) ? intval($_REQUEST['id']) : 0;
if ( $type == 'shipping' ) {
	$sql = "UPDATE `lord_record_convert` SET `state` = 1, `oid` = '".$_SESSION['admin_name']."', `update_time`=$ut_now WHERE `id` = $id";
	$res = $pdo->getDB(1)->exec($sql);
	$errno = 0; $error = "操作成功。";
	if ( !$res ) {
		$errno = 9; $error = "查询错误。";
	}
	echo json_encode(array('errno'=>$errno, 'error'=>$error));
	exit;
}
$data = array();
if ( $type=='modify' ) {
	$sql = "SELECT * FROM `lord_record_convert` WHERE `id` = $id";
	$data = $db->query($sql)->fetch(PDO::FETCH_ASSOC);
	if ( !$data ) {
		echo "出错了。";
		exit;
	}
}
if ( $ispost ) {
	$other = isset($_REQUEST['other'])?trim($_REQUEST['other']):'';
	if ($type=='modify') {
		$sql = "UPDATE `lord_record_convert` SET `other`='$other', `oid` = '".$_SESSION['admin_name']."', `update_time`=$ut_now WHERE `id`=$id";
		$res = $pdo->getDB(1)->exec($sql);
	}
	$errno = 0; $error = "操作成功。";
	if ( !$res ) {
		$errno = 9; $error = "查询错误。";
	}
	$res = json_encode(array('errno'=>$errno, 'error'=>$error));
	if ( !$errno ) {
		header('Location: convertRecord.php');
	} else {
		echo $res;
		exit;
	}
}
?>
<script type="text/javascript" src="../js/jquery.js"></script>
<script type="text/javascript" src="../js/My97DatePicker/WdatePicker.js"></script>
<link type="text/css" href="../bootstrap/css/bootstrap.min.css" rel="stylesheet" />
<style type="text/css">
table.table{ font-size: 12px;width: 95%!important;}
table.table th{ white-space: nowrap;}
label{display: inline;}
.in_t{width: 300px;height: 30px!important;margin: 0}
.in_t2{width: 100px;height: 30px!important;margin: 0}
.in_a{width: 300px;height: 100px!important;margin: 0}
</style>
<script>
$(function(){
	//
});
</script>

<body>
<div style="position:absolute;left:0;top:0;padding:0 10px;">

<fieldset>
	<legend>用户兑换记录 - <?php if($type=='add'){?>创建<?php }else{?>编辑<?php }?></legend>
	<form action="convertOperate.php" method="post">
		<input type="hidden" name="ispost" value="1" />
		<input type="hidden" name="type" value="<?=$type?>" />
		<input type="hidden" name="id" value="<?php if($data){echo $data['id'];} ?>" />
		<table class="table table-bordered table-condensed table-hover">
			<tr>
				<td style="width:70px;font-size:14px;">兑换备注:</td>
				<td><input name="other" value="<?php if($data){echo $data['other'];} ?>" type="text" class="in_t" /></td>
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
