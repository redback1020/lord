<?php
$api = 'prop';
header("Content-type: text/html; charset=utf-8");
require_once '../manage/checkPriv.php';
require_once '../include/database.class.php';
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
$states = array('0'=>'正常','1'=>'下线','2'=>'删除');//上线状态
$states = array('0'=>'正常','1'=>'下线');//上线状态
$ut_now = time();
$apiName = $api.'list';
$reqType = isset($_REQUEST['reqType']) ? trim($_REQUEST['reqType']) : 'add';//add modify delete
$ispost = isset($_REQUEST['ispost']) ? intval($_REQUEST['ispost']) : 0;
$id = isset($_REQUEST['id']) ? intval($_REQUEST['id']) : 0;
if ($reqType=='delete') {
	$sql = "DELETE FROM `lord_list_$api` WHERE `id` = $id";
	$res = $pdo->getDB(1)->exec($sql);
	if ( !$res ) {
		echo json_encode(array('errno'=>8, 'error'=>"查询错误。 $sql"));
		exit;
	}
	$res = apiGet($apiName, $reqType, array('id'=>$id));
		$res = array('errno'=>0, 'error'=>"操作成功");//预留接口
	echo json_encode($res);
	exit;
}
$data = array();
if ($reqType=='modify') {
	$sql = "SELECT * FROM `lord_list_$api` WHERE `id` = $id";
	$data = $db->query($sql)->fetch(PDO::FETCH_ASSOC);
	if ( !$data ) {
		echo "出错了。";
		exit;
	}
}
if ($ispost) {
	$cd = isset($_REQUEST['cd']) && isset($cds[intval($_REQUEST['cd'])]) ? intval($_REQUEST['cd']) : 2;
	$cate = $cds[$cd];
	$name = isset($_REQUEST['name']) ? trim($_REQUEST['name']) : '';
	$resume = isset($_REQUEST['resume']) ? trim($_REQUEST['resume']) : '';
	$fileId = isset($_REQUEST['fileId']) ? intval($_REQUEST['fileId']) : 1;
	$sex = isset($_REQUEST['sex']) && isset($sexs[intval($_REQUEST['sex'])]) ? intval($_REQUEST['sex']) : 0;
	$showin = isset($_REQUEST['showin']) && isset($showins[intval($_REQUEST['showin'])]) ? intval($_REQUEST['showin']) : 0;
	$overlay = isset($_REQUEST['overlay']) && isset($overlays[intval($_REQUEST['overlay'])]) ? intval($_REQUEST['overlay']) : 0;
	$mutex = isset($_REQUEST['mutex']) && isset($mutexs[intval($_REQUEST['mutex'])]) ? intval($_REQUEST['mutex']) : 0;
	$useby = isset($_REQUEST['useby']) && isset($usebys[intval($_REQUEST['useby'])]) ? intval($_REQUEST['useby']) : 0;
	$usedo = isset($_REQUEST['usedo']) && isset($usedos[intval($_REQUEST['usedo'])]) ? intval($_REQUEST['usedo']) : 0;
	$useup = isset($_REQUEST['useup']) && isset($useups[intval($_REQUEST['useup'])]) ? intval($_REQUEST['useup']) : 0;
	$create_time = $update_time = $ut_now;
	if ($reqType=='add') {
		$sql = "INSERT INTO `lord_list_$api` (`cd`,`cate`,`name`,`resume`,`fileId`,`sex`,`showin`,`overlay`,`mutex`,`useby`,`usedo`,`useup`,`create_time`,`update_time`)
		VALUES ($cd,'$cate','$name','$resume',$fileId,$sex,$showin,$overlay,$mutex,$useby,$usedo,$useup,$create_time,$update_time)";
		$res = $pdo->getDB(1)->exec($sql);
		$res = $id = $pdo->getDB(1)->lastInsertId();
	}
	if ($reqType=='modify') {
		$sql = "UPDATE `lord_list_$api` SET `cd`=$cd,`cate`='$cate',`name`='$name',`resume`='$resume',`fileId`=$fileId,`sex`=$sex,`showin`=$showin,`overlay`=$overlay,`mutex`=$mutex,`useby`=$useby,`usedo`=$usedo,`useup`=$useup,`update_time`=$update_time WHERE `id`=$id";
		$res = $pdo->getDB(1)->exec($sql);
	}
	if ( !$res ) {
		echo json_encode(array('errno'=>8, 'error'=>"查询错误。 $sql"));
		exit;
	}
	$res = apiGet($apiName, $reqType, array('id'=>$id));
		$res = array('errno'=>0, 'error'=>"操作成功");//预留接口
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
table.table{ font-size: 12px;width: 98%!important;}
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
<div style="position:relative;left:0;top:0;padding:0 10px;">

<fieldset>
	<legend>内置道具 - <?php if($reqType=='add'){?>创建<?php }else{?>编辑<?php }?> - 需严格谨慎，且需代码对照</legend>
	<form action="<?=$api?>Add.php" method="post">
		<input type="hidden" name="ispost" value="1" />
		<input type="hidden" name="reqType" value="<?=$reqType?>" />
		<input type="hidden" name="id" value="<?php if($data){echo $data['id'];} ?>" />
		<table class="table table-bordered table-condensed table-hover">
			<tr>
				<td class="tdth">分类归属:</td><?php $var = 'cd';?>
				<td><select name="<?=$var?>" class="span2"><?php foreach (${$var.'s'} as $k => $v) { echo "<option value='$k'".($data && $data[$var]==$k ? " selected='selected'" : "").">$v</option>"; } ?></select></td>
			</tr>
			<tr>
				<td class="tdth">道具名称:</td><?php $var = 'name';?>
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
				<td class="tdth">性别限制:</td><?php $var = 'sex';?>
				<td><select name="<?=$var?>" class="span2"><?php foreach (${$var.'s'} as $k => $v) { echo "<option value='$k'".($data && $data[$var]==$k ? " selected='selected'" : "").">$v</option>"; } ?></select></td>
			</tr>
			<tr>
				<td class="tdth">显示限制:</td><?php $var = 'showin';?>
				<td><select name="<?=$var?>" class="span2"><?php foreach (${$var.'s'} as $k => $v) { echo "<option value='$k'".($data && $data[$var]==$k ? " selected='selected'" : "").">$v</option>"; } ?></select></td>
			</tr>
			<tr>
				<td class="tdth">叠加方式:</td><?php $var = 'overlay';?>
				<td><select name="<?=$var?>" class="span2"><?php foreach (${$var.'s'} as $k => $v) { echo "<option value='$k'".($data && $data[$var]==$k ? " selected='selected'" : "").">$v</option>"; } ?></select></td>
			</tr>
			<tr>
				<td class="tdth">互斥方式:</td><?php $var = 'mutex';?>
				<td><select name="<?=$var?>" class="span2"><?php foreach (${$var.'s'} as $k => $v) { echo "<option value='$k'".($data && $data[$var]==$k ? " selected='selected'" : "").">$v</option>"; } ?></select></td>
			</tr>
			<tr>
				<td class="tdth">使用方式:</td><?php $var = 'useby';?>
				<td><select name="<?=$var?>" class="span2"><?php foreach (${$var.'s'} as $k => $v) { echo "<option value='$k'".($data && $data[$var]==$k ? " selected='selected'" : "").">$v</option>"; } ?></select></td>
			</tr>
			<tr>
				<td class="tdth">使用运算:</td><?php $var = 'usedo';?>
				<td><select name="<?=$var?>" class="span2"><?php foreach (${$var.'s'} as $k => $v) { echo "<option value='$k'".($data && $data[$var]==$k ? " selected='selected'" : "").">$v</option>"; } ?></select></td>
			</tr>
			<tr>
				<td class="tdth">用完处理:</td><?php $var = 'useup';?>
				<td><select name="<?=$var?>" class="span2"><?php foreach (${$var.'s'} as $k => $v) { echo "<option value='$k'".($data && $data[$var]==$k ? " selected='selected'" : "").">$v</option>"; } ?></select></td>
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
