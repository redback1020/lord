<?php
$api = 'notice';//
header("Content-type: text/html; charset=utf-8");
require_once '../manage/checkPriv.php';
require_once '../include/database.class.php';
$ut_now = time();
$type = isset($_REQUEST['type']) ? trim($_REQUEST['type']) : 'add';//add delete modify
$ispost = isset($_REQUEST['ispost']) ? intval($_REQUEST['ispost']) : 0;
$id = isset($_REQUEST['id']) ? intval($_REQUEST['id']) : 0;
if ($type=='delete') {
	$sql = "UPDATE `lord_game_notice` SET `state` = 2 WHERE `id` = $id";
	$res = $pdo->getDB(1)->exec($sql);
	$res = $res ? apiGet($api, $type, array('id'=>$id)) : array('errno'=>8, 'error'=>"查询错误。 $sql");
	echo json_encode($res);
	exit;
}
if ($type=='offline') {
	$sql = "UPDATE `lord_game_notice` SET `state` = 1 WHERE `id` = $id";
	$res = $pdo->getDB(1)->exec($sql);
	$res = $res ? apiGet($api, $type, array('id'=>$id)) : array('errno'=>8, 'error'=>"查询错误。 $sql");
	echo json_encode($res);
	exit;
}
$data = array();
if ($type=='modify') {
	$sql = "SELECT * FROM `lord_game_notice` WHERE `id` = $id";
	$data = $db->query($sql)->fetch();
	if ( !$data ) {
		echo "出错了。";
		exit;
	}
}
if ($ispost) {
	$channel = isset($_REQUEST['channel'])?trim($_REQUEST['channel']):'all';
	$channel = $channel!='all'?$channel:'';
	$subject = isset($_REQUEST['subject'])?trim($_REQUEST['subject']):'';
	$content = isset($_REQUEST['content'])?trim($_REQUEST['content']):'';
	$content = str_replace("\r", "", $content);
	$start_time = isset($_REQUEST['start_time'])&&trim($_REQUEST['start_time'])?strtotime(trim($_REQUEST['start_time'])):0;
	$end_time = isset($_REQUEST['end_time'])&&trim($_REQUEST['end_time'])?strtotime(trim($_REQUEST['end_time'])):0;
	$sort = isset($_REQUEST['sort'])&&intval($_REQUEST['sort'])?intval($_REQUEST['sort']):99;
	$create_time = $update_time = $ut_now;
	if ($type=='add') {
		$sql = "INSERT INTO `lord_game_notice` (`channel`,`subject`,`content`,`start_time`,`end_time`,`sort`,`create_time`,`update_time`)
		VALUES ('$channel','$subject','$content',$start_time,$end_time,$sort,$create_time,$update_time)";
		$res = $pdo->getDB(1)->exec($sql);
		$res = $id = $pdo->getDB(1)->lastInsertId();
	}
	if ($type=='modify') {
		$sql = "UPDATE `lord_game_notice` SET `channel`='$channel',`subject`='$subject',`content`='$content',`start_time`=$start_time,`end_time`=$end_time,`sort`=$sort,`update_time`=$update_time WHERE `id`=$id";
		$res = $pdo->getDB(1)->exec($sql);
	}
	$errno = 0; $error = "";
	if ( $res ) {
		//security
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
		header('Location: noticeList.php');
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
	<legend>公告-<?php if($type=='add'){?>创建<?php }else{?>编辑<?php }?></legend>
	<form action="noticeAdd.php" method="post">
		<input type="hidden" name="ispost" value="1" />
		<input type="hidden" name="type" value="<?=$type?>" />
		<input type="hidden" name="id" value="<?php if($data){echo $data['id'];} ?>" />
		<table class="table table-bordered table-condensed table-hover">
			<tr>
				<td style="width:70px;font-size:14px;">专属渠道:</td>
				<td>
					<select class="span2" name="channel">
						<option value="all">全部</option>
						<?php
						$file = __DIR__ . "/data/cache_channel";
						if ( is_file($file) && mt_rand(0, 10) ) {
							$channels = json_decode(file_get_contents($file), 1);
						} else {
							$sql = "select `channel` from `lord_game_user` where `channel` != '' group by `channel`";
							$channels = $db->query($sql)->fetchAll();
							$res = file_put_contents($file, json_encode($channels));
						}
						foreach ($channels as $val) {
							if (in_array($val['channel'], $channels_durty)) {
								continue;
							}
							$selected = $data&&$data['channel']==$val['channel']?" selected='selected'":"";
							echo '<option value="'.$val['channel'].'"'.$selected.'>'.$val['channel'].'</option>';
						}
						?>
					</select>
				</td>
			</tr>
			<tr>
				<td style="width:70px;font-size:14px;">公告排序:</td>
				<td><input name="sort" value="<?php if($data){echo $data['sort'];} ?>" type="text" class="in_t" /> 越小越靠上，默认99</td>
			</tr>
			<tr>
				<td style="width:70px;font-size:14px;">公告标题:</td>
				<td><input name="subject" value="<?php if($data){echo $data['subject'];} ?>" type="text" class="in_t" /></td>
			</tr>
			<tr>
				<td style="width:70px;font-size:14px;">公告内容:</td>
				<td><textarea name="content" class="in_a"><?php if($data){echo $data['content'];} ?></textarea></td>
			</tr>
			<tr>
				<td style="width:70px;font-size:14px;">上线日期:</td>
				<td>
					<input name="start_time" value="<?php if($data){echo date('Y-m-d',$data['start_time']);} ?>" class="textbox dtime in_t2" type="text" readonly onclick="WdatePicker({dateFmt:'yyyy-MM-dd'})"/>
					- <input name="end_time" value="<?php if($data){echo date('Y-m-d',$data['end_time']);} ?>" class="textbox dtime in_t2" type="text" readonly onclick="WdatePicker({dateFmt:'yyyy-MM-dd'})"/>
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
