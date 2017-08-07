<?php
$api = 'ttesksource';//
header("Content-type: text/html; charset=utf-8");
require_once '../manage/checkPriv.php';
require_once '../include/database.class.php';
$ut_now = time();
$types = array('1'=>'抓底','2'=>'打单牌','3'=>'打对子','4'=>'打三带','5'=>'打顺子','6'=>'打连对','7'=>'打飞机','8'=>'打炸弹','9'=>'倍数','10'=>'倍数');
$type = isset($_REQUEST['type']) ? trim($_REQUEST['type']) : 'add';//add delete modify
$ispost = isset($_REQUEST['ispost']) ? intval($_REQUEST['ispost']) : 0;
$id = isset($_REQUEST['id']) ? intval($_REQUEST['id']) : 0;
if ($type=='delete') {
	$sql = "DELETE FROM `lord_list_ttesksource` WHERE `id` = $id";
	$res = $pdo->getDB(1)->exec($sql);
	$res = $res ? apiGet($api, $type, array('id'=>$id)) : array('errno'=>8, 'error'=>"查询错误。 $sql");
	echo json_encode($res);
	exit;
}
$data = array();
if ($type=='modify') {
	$sql = "SELECT * FROM `lord_list_ttesksource` WHERE `id` = $id";
	$data = $db->query($sql)->fetch(PDO::FETCH_ASSOC);
	if ( !$data ) {
		echo "出错了。";
		exit;
	}
}
if ($ispost) {
	$typeid = isset($_REQUEST['typeid'])?intval($_REQUEST['typeid']):'';
	$name  = isset($_REQUEST['name'])?trim($_REQUEST['name']):'';
	$sort = 1;
	$create_time = $update_time = $ut_now;
	$errno = 0; $error = "";
	if ($type=='add') {
		$sql = "INSERT INTO `lord_list_ttesksource` ";
		$sql.= "(`typeid`,`type`,`name`,`sort`,`create_time`,`update_time`) VALUES ";
		$sql.= "($typeid, '".$types[$typeid]."', '$name', $sort, $create_time, $update_time)";
		$res = $pdo->getDB(1)->exec($sql);
		$res = $id = $pdo->getDB(1)->lastInsertId();
		if ( !$res ) {
			$errno = 1; $error = "查询错误： $sql";
		}
	}
	if ($type=='modify') {
		$sql = "UPDATE `lord_list_ttesksource` SET `typeid`=$typeid,`type`='".$types[$typeid]."',`name`='$name',`sort`=$sort,`update_time`=$update_time WHERE `id`=$id";
		$res = $pdo->getDB(1)->exec($sql);
		if ( !$res ) {
			$errno = 1; $error = "查询错误： $sql";
		}
	}
	if ( !$errno ) {
		$sql = "SELECT * FROM `lord_list_ttesksource` WHERE `id` = $id";
		$data = $db->query($sql)->fetch(PDO::FETCH_ASSOC);
		if ( $data && is_array($data) ) {
			$data['id'] = intval($data['id']);
			$data['typeid'] = intval($data['typeid']);
			$data['type'] = trim($data['type']);
			$data['name'] = trim($data['name']);
			$data['sort'] = intval($data['sort']);
			$data['create_time'] = intval($data['create_time']);
			$data['update_time'] = intval($data['update_time']);
		} else {
			$data = array();
		}
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
		header('Location: ttsourceList.php');
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
	<legend>牌局任务源码 - <?php if($type=='add'){?>创建<?php }else{?>修改<?php }?></legend>
	<form action="ttsourceAdd.php" method="post">
		<input type="hidden" name="ispost" value="1" />
		<input type="hidden" name="type" value="<?=$type?>" />
		<input type="hidden" name="id" value="<?php if($data){echo $data['id'];} ?>" />
		<table class="table table-bordered table-condensed table-hover">
			<tr>
				<th>类型:</th>
				<td>
					<select class="span2" name="typeid">
						<?php
						foreach ($types as $k => $v) {
							$selected = $data&&$data['typeid']==$k?" selected='selected'":"";
							echo '<option value="'.$k.'"'.$selected.'>'.$v.'</option>';
						}
						?>
					</select>
				</td>
				<td>必选。</td>
			</tr>
			<tr>
				<th>名称:</th>
				<td><input name="name" value="<?php if($data){echo $data['name'];} ?>" type="text" class="in_t" /></td>
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
