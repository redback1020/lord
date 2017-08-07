<?php
header("Content-type: text/html; charset=utf-8");
require_once '../manage/checkPriv.php';
require_once '../include/database.class.php';
$cds = array('1'=>'服装穿戴','2'=>'辅助道具','3'=>'充值乐币','4'=>'乐币换豆','5'=>'乐券兑换','6'=>'预留扩展');//分类归属
$sexs = array('0'=>'男女通用','1'=>'男性专用','2'=>'女性专用');//性别限制
$showins = array('0'=>'不做限制','1'=>'只在背包');//显示限制
$overlays = array('0'=>'自动抛弃','1'=>'增加数量','2'=>'增加时效','3'=>'增加持久');//叠加方式
$overlays = array('0'=>'自动抛弃','2'=>'增加时效');//叠加方式
$presents = array('0'=>'不可赠送','1'=>'可以赠送');//可否赠送
$pauses = array('0'=>'不可暂停','1'=>'可以暂停');//可否暂停
$repairs = array('0'=>'不可修复','1'=>'可以修复');//可否修复
$mutexs = array('0'=>'不会互斥','1'=>'同类互斥');//互斥方式
$usebys = array('0'=>'拥有即用','1'=>'缺失即用','2'=>'手动使用');//使用方式
$usedos = array('0'=>'不降数值','1'=>'降低数量','2'=>'降低时效','3'=>'降低持久');//使用运算
$usedos = array('0'=>'不降数值','2'=>'降低时效');//使用运算
$useass = array('0'=>'没有用途','1'=>'改变状态','2'=>'增加乐币','3'=>'增加代币','4'=>'增加乐豆','5'=>'增加乐券','6'=>'增加抽奖数','8'=>'获得物品','9'=>'获得实物');//使用用途
$useups = array('0'=>'自动销毁','1'=>'不做处理','2'=>'状态：已用完','3'=>'状态：已坏掉','4'=>'状态：待销毁','5'=>'预留扩展');//用完处理
$states = array('0'=>'正常','1'=>'下线','2'=>'删除');//上线状态
$states = array('0'=>'正常','1'=>'下线');//上线状态
$pds = $pdata = array();
$sql = "SELECT * FROM `lord_list_prop`";
$res = $db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
if ( !$res ) $res = array();
foreach ( $res as $k => $v ) {
	$pds[$v['id']] = $v['name'];//内置道具
	$pdata[$v['id']] = $v;
}
$ut_now = time();
$api = 'item';
$subreq = isset($_REQUEST['subreq']) ? trim($_REQUEST['subreq']) : 'add';//add modify delete
$ispost = isset($_REQUEST['ispost']) ? intval($_REQUEST['ispost']) : 0;
$id = isset($_REQUEST['id']) ? intval($_REQUEST['id']) : 0;
if ( $subreq=='delete' ) {
	$sql = "UPDATE `lord_list_$api` SET `state` = 2 WHERE `id` = $id";
	$res = $pdo->getDB(1)->exec($sql);
	$res = $res ? apiGet($api, $subreq, array('id'=>$id)) : array('errno'=>8, 'error'=>"查询错误。 $sql");
	echo json_encode($res);
	exit;
}
$data = array();
if ($subreq=='modify') {
	$sql = "SELECT * FROM `lord_list_$api` WHERE `id` = $id";
	$data = $db->query($sql)->fetch(PDO::FETCH_ASSOC);
	if ( !$data ) {
		echo "查询错误。 $sql";
		exit;
	}
}
if ($ispost) {
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
	$create_time = $update_time = $ut_now;
	if ( $subreq=='add' )    $sql = "INSERT INTO `lord_list_$api` (`cd`,`pd`,`name`,`resume`,`fileId`,`number`,`second`,`points`,`present`,`pause`,`repair`,`useas`,`useto`,`state`,`sort`,`create_time`,`update_time`) VALUES ($cd, $pd, '$name', '$resume', $fileId, $number, $second, $points, $present, $pause, $repair, $useas, $useto, $state, $sort, $create_time, $update_time)";
	if ( $subreq=='modify' ) $sql = "UPDATE `lord_list_$api` SET `cd`=$cd,`pd`=$pd,`name`='$name',`resume`='$resume',`fileId`=$fileId,`number`=$number,`second`=$second,`points`=$points,`present`=$present,`pause`=$pause,`repair`=$repair,`useas`=$useas,`useto`=$useto,`state`=$state,`sort`=$sort,`update_time`=$update_time WHERE `id`=$id";
	$res = $pdo->getDB(1)->exec($sql);
	if ( $res ) {
		if ( $subreq=='add' ) $id = $pdo->getDB(1)->lastInsertId();
		$res = apiGet($api, $subreq, array('id'=>$id));
		if ( !$res['errno'] ) {
			header("Location: {$api}List.php");
		}
	} else {
		$res = array('errno'=>8, 'error'=>"查询错误。 $sql");
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
	<legend>物品 - <?php if($subreq=='add'){?>创建<?php }else{?>编辑<?php }?></legend>
	<form action="<?=$api?>Add.php" method="post">
		<input type="hidden" name="ispost" value="1" />
		<input type="hidden" name="subreq" value="<?=$subreq?>" />
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
				<td class="tdth">上线状态:</td><?php $var = 'state';?>
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
