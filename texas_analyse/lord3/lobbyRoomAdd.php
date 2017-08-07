<?php
header("Content-type: text/html; charset=utf-8");
require_once '../manage/checkPriv.php';
require_once '../include/database.class.php';
$ut_now = time();
$type = isset($_REQUEST['type']) ? trim($_REQUEST['type']) : 'add';//add delete modify //close
$ispost = isset($_REQUEST['ispost']) ? intval($_REQUEST['ispost']) : 0;
$id = isset($_REQUEST['id']) ? intval($_REQUEST['id']) : 0;
if ($type=='delete') {
	$sql = "UPDATE `lord_game_room` SET `is_del` = 1 WHERE `id` = $id";
	$res = $pdo->getDB(1)->exec($sql);
	$errno = 0; $error = "";
	if ( $res ) {
		//security
		$api = 'room';//
		$type = $type;//
		$res = apiGet($api, $type, array('id'=>$id));
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
	$sql = "UPDATE `lord_game_room` SET `isOpen` = 0 WHERE `id` = $id";
	$res = $pdo->getDB(1)->exec($sql);
	$errno = 0; $error = "";
	if ( $res ) {
		//security
		$api = 'room';//
		$type = $type;//
		$res = apiGet($api, $type, array('id'=>$id));
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
	$sql = "SELECT * FROM `lord_game_room` WHERE `id` = $id";
	$data = $db->query($sql)->fetch();
	if ( !$data ) {
		echo "出错了。";
		exit;
	}
	$data['showRules'] = $data['showRules'] ? json_decode($data['showRules'],1):array();
	//当前版本只处理最后一个广告场
}
if ($ispost) {
	//当前版本只处理最后一个广告场
	$modelId = isset($_REQUEST['modelId'])?intval($_REQUEST['modelId']):91;
	$roomId = isset($_REQUEST['roomId'])?intval($_REQUEST['roomId']):1091;
	$name = isset($_REQUEST['name'])?trim($_REQUEST['name']):'';
	//当前版本只处理单条规则，暂不处理多条规则并列的现象
	$showRules = isset($_REQUEST['showRules'])?$_REQUEST['showRules']:array();
	$showRules['channel'] = isset($showRules['channel'])&&trim($showRules['channel'])?explode(" ",str_replace(array("\r","\n","  ")," ",trim($showRules['channel']))):array(); if ( !$showRules['channel'] ) unset($showRules['channel']);
	$showRules['channot'] = isset($showRules['channot'])&&trim($showRules['channot'])?explode(" ",str_replace(array("\r","\n","  ")," ",trim($showRules['channot']))):array(); if ( !$showRules['channot'] ) unset($showRules['channot']);
	$showRules['gold'] = isset($showRules['gold'])&&trim($showRules['gold'])?trim($showRules['gold']):''; if ( !$showRules['gold'] || strpos($showRules['gold'], '|')==false ) unset($showRules['gold']);
	$showRules['coins'] = isset($showRules['coins'])&&trim($showRules['coins'])?trim($showRules['coins']):''; if ( !$showRules['coins'] || strpos($showRules['coins'], '|')==false ) unset($showRules['coins']);
	$showRules['mixtime'] = isset($showRules['mixtime'])&&trim($showRules['mixtime'])?explode("/",str_replace(array("\r","\n"),"/",trim($showRules['mixtime']))):array(); if ( !$showRules['mixtime'] ) unset($showRules['mixtime']);
	$showRules = $db->quote($showRules ? json_encode(array($showRules)) : '');
	$apkurl = isset($_REQUEST['apkurl'])?trim($_REQUEST['apkurl']):'';
	$appid = isset($_REQUEST['appid'])?intval($_REQUEST['appid']):0;
	$ver = isset($_REQUEST['ver'])?trim($_REQUEST['ver']):'';
	$vercode = isset($_REQUEST['vercode'])?intval($_REQUEST['vercode']):0;
	$bytes = isset($_REQUEST['bytes'])?intval($_REQUEST['bytes']):0;
	$desc = isset($_REQUEST['desc'])?trim($_REQUEST['desc']):'';
	$md5 = isset($_REQUEST['md5'])?trim($_REQUEST['md5']):'';
	$package = isset($_REQUEST['package'])?trim($_REQUEST['package']):'';
	$sort = isset($_REQUEST['sort'])?intval($_REQUEST['sort']):0;
	$create_time = $update_time = $ut_now;
	if ($type=='add') {
		$sql = "INSERT INTO `lord_game_room` (`modelId`,`roomId`,`name`,`showRules`,`apkurl`,`appid`,`ver`,`vercode`,`bytes`,`desc`,`md5`,`package`,`sort`,`create_time`,`update_time`)
		VALUES ($modelId,$roomId,'$name',$showRules,'$apkurl',$appid,'$ver',$vercode,$bytes,'$desc','$md5','$package',$sort,$create_time,$update_time)";
		$res = $pdo->getDB(1)->exec($sql);
		$res = $id = $pdo->getDB(1)->lastInsertId();
	}
	if ($type=='modify') {
		$sql = "UPDATE `lord_game_room` SET `modelId`=$modelId,`roomId`=$roomId,`name`='$name',`showRules`=$showRules,`apkurl`='$apkurl',`appid`=$appid,`ver`='$ver',`vercode`=$vercode,`bytes`=$bytes,`desc`='$desc',`md5`='$md5',`package`='$package',`sort`=$sort,`update_time`=$update_time WHERE `id`=$id";
		$res = $pdo->getDB(1)->exec($sql);
	}
	$errno = 0; $error = "";
	if ( $res ) {
		//security
		$api = 'room';//
		$type = $type;//
		$res = apiGet($api, $type, array('id'=>$id));
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
		header('Location: lobbyRoomList.php');
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
	<legend>大厅场次-<?php if($type=='add'){?>创建<?php }else{?>编辑<?php }?></legend>
	<form action="lobbyRoomAdd.php" method="post">
		<input type="hidden" name="ispost" value="1" />
		<input type="hidden" name="type" value="<?=$type?>" />
		<input type="hidden" name="id" value="<?php if($data){echo $data['id'];} ?>" />
		<table class="table table-bordered table-condensed table-hover">
			<tr>
				<td style="width:70px;font-size:14px;">场次模式:</td>
				<td>
					<select class="span2" name="modelId">
						<option value="91">广告场</option>
					</select>
				</td>
			</tr>
			<tr>
				<td style="width:70px;font-size:14px;">场次编号:</td>
				<td><input name="roomId" value="<?php if($data){echo $data['roomId'];} ?>" type="text" class="in_t" /></td>
			</tr>
			<tr>
				<td style="width:70px;font-size:14px;">场次名称:</td>
				<td><input name="name" value="<?php if($data){echo $data['name'];} ?>" type="text" class="in_t" /> 只是一个用作管理识别的标记</td>
			</tr>
			<tr>
				<td style="width:70px;font-size:14px;">场次排序:</td>
				<td><input name="sort" value="<?php if($data){echo $data['sort'];} ?>" type="text" class="in_t" /> 越小越靠上，默认99</td>
			</tr>
			<tr>
				<td style="width:70px;font-size:14px;">展现规则:<br/>许可渠道</td>
				<td><textarea name="showRules[channel]" class="in_a"><?php if($data){echo isset($data['showRules'][0]['channel'])?join(' ',$data['showRules'][0]['channel']):'';} ?></textarea>渠道名＋英文空格/换行，不填则不限制，不建议过多</td>
			</tr>
			<tr>
				<td style="width:70px;font-size:14px;">展现规则:<br/>屏蔽渠道</td>
				<td><textarea name="showRules[channot]" class="in_a"><?php if($data){echo isset($data['showRules'][0]['channot'])?join(' ',$data['showRules'][0]['channot']):'';} ?></textarea>渠道名＋英文空格/换行，不填则不限制，不建议过多</td>
			</tr>
			<!-- <tr>
				<td style="width:70px;font-size:14px;">展现规则:<br/>限制乐币</td>
				<td><input name="showRules[gold]" value="<?php /*if($data){echo isset($data['showRules'][0]['gold'])?$data['showRules'][0]['gold']:'';}*/ ?>" type="text" class="in_t" />乐币范围，不填则不限制。举例，“0-5000”，用户乐币&gt;=0且&lt;5000时显现</td>
			</tr> -->
			<tr>
				<td style="width:70px;font-size:14px;">展现规则:<br/>限制乐豆</td>
				<td><input name="showRules[coins]" value="<?php if($data){echo isset($data['showRules'][0]['coins'])?$data['showRules'][0]['coins']:'';} ?>" type="text" class="in_t" />乐豆范围，不填则不限制。举例，“0-5000”，用户乐豆&gt;=0且&lt;5000时显现</td>
			</tr>
			<tr>
				<td style="width:70px;font-size:14px;">展现规则:<br/>限制时间</td>
				<td><textarea name="showRules[mixtime]" class="in_a"><?php if($data){echo isset($data['showRules'][0]['mixtime'])?join("\r\n",$data['showRules'][0]['mixtime']):'';} ?></textarea>每行的英文符号组合作为一个时间规则，符合任一行即显示，不填则不限制，不建议过多。<br>每行格式，“2015-05-01 09:00:00|2015-05-31 23:30:00|67”，代表着5月份内的周六周日的9点到23点半之间才显现</td>
			</tr>
			<tr>
				<td style="width:70px;font-size:14px;">&nbsp;</td>
				<td style="color:red;">下面为广告场专属信息</td>
			</tr>
			<tr>
				<td style="width:70px;font-size:14px;">广告场apk-apkurl:</td>
				<td><input name="apkurl" value="<?php if($data){echo $data['apkurl'];} ?>" type="text" class="in_t" />如果为空，则会弹出扫码图用于手机端，下面的不需要再填写，且需要再增加一张大厅广告场的对应roomId的图片；如果为有效apk，则下面的需要正确填写，且除了本房间用图片之外不需再增加弹窗图片</td>
			</tr>
			<tr>
				<td style="width:70px;font-size:14px;">广告场apk-appid:</td>
				<td><input name="appid" value="<?php if($data){echo $data['appid'];} ?>" type="text" class="in_t" /></td>
			</tr>
			<tr>
				<td style="width:70px;font-size:14px;">广告场apk-ver:</td>
				<td><input name="ver" value="<?php if($data){echo $data['ver'];} ?>" type="text" class="in_t" /></td>
			</tr>
			<tr>
				<td style="width:70px;font-size:14px;">广告场apk-vercode:</td>
				<td><input name="vercode" value="<?php if($data){echo $data['vercode'];} ?>" type="text" class="in_t" /></td>
			</tr>
			<tr>
				<td style="width:70px;font-size:14px;">广告场apk-bytes:</td>
				<td><input name="bytes" value="<?php if($data){echo $data['bytes'];} ?>" type="text" class="in_t" /></td>
			</tr>
			<tr>
				<td style="width:70px;font-size:14px;">广告场apk-desc:</td>
				<td><input name="desc" value="<?php if($data){echo $data['desc'];} ?>" type="text" class="in_t" /></td>
			</tr>
			<tr>
				<td style="width:70px;font-size:14px;">广告场apk-md5:</td>
				<td><input name="md5" value="<?php if($data){echo $data['md5'];} ?>" type="text" class="in_t" /></td>
			</tr>
			<tr>
				<td style="width:70px;font-size:14px;">广告场apk-package:</td>
				<td><input name="package" value="<?php if($data){echo $data['package'];} ?>" type="text" class="in_t" /></td>
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
