<?php
header("Content-type: text/html; charset=utf-8");
// ini_set("display_errors","On");error_reporting(E_ALL);//E_ERROR | E_WARNING | E_PARSE
require_once '../manage/checkPriv.php';
require_once '../include/database.class.php';
$channels_durty = array("51vappFT","alitech","alizhibo","android","aostv","appstore","appvtion","banana","beimi","bianfeng","bitgames","boyakeno","cik","CMCC","dou","drpengvoice","duole","duwei-iqiyi","fanmi","gangfeng","gao","hangkeweiye","haoshi","henangd","hifly","huawei","huaweiconsumer","icntv","IMT","infoTM","iptvhebei","jingling","jinnuowei","jinruixian","jinya1","jinyatai","jiuzhou","kuaiyou","laimeng","landiankechuang","leyou","lianyi","nibiru","pengrunsen","qiwangldkc","qiwangyfcz","qpod","qvod","realplay","robotplugin","ruixiangtongxin","runhe","shitouer","the5","threelegsfrog","thtflingyue","tshifi","ujob","uprui","UTskd","vsoontech","wanhuatong","wanmei","wanweitron","whatchannel","wobo","xiaomi","xinhancommon","xinhantena","xinhanvsoontech","xinhanyixinte","xunlei","xunma","yangcong","youjoytest","zuoqi");
$ut_now = time();
$pathes = array(
	"global"=>"全局",
	"topic_index"=>"活动",
	"user_index"=>"用户",
	"mall_index"=>"商城",
	"topic_index"=>"活动",
	"task_index"=>"任务",
	"list_index"=>"榜单",
);
$maxversion = 0;
$sql = "SELECT max(`version`) as version FROM `lord_game_version` WHERE `name` = 'vertips' AND `is_done` = 0 LIMIT 1";
$res = $db->query($sql)->fetch();
$maxversion = $res ? $res['version'] : 1;
$type = isset($_REQUEST['type']) ? trim($_REQUEST['type']) : 'add';//add delete modify
$ispost = isset($_REQUEST['ispost']) ? intval($_REQUEST['ispost']) : 0;
$id = isset($_REQUEST['id']) ? intval($_REQUEST['id']) : 0;
if ($type=='delete') {
	$sql = "SELECT * FROM `lord_game_tips` WHERE `id` = $id AND `is_del` > 0";
	$res = $db->query($sql)->fetch();
	if ( $res ) {
		$errno = 1; $error = "已删除的不可再操作。";
		echo json_encode(array('errno'=>$errno, 'error'=>$error));
		exit;
	}
	$sql = "UPDATE `lord_game_tips` SET `version` = $maxversion, `ver_upd` = $maxversion, `ver_del` = $maxversion, `is_del` = 1, `update_time` = $ut_now WHERE `id` = $id";
	$res = $pdo->getDB(1)->exec($sql);
	if ( $res ) {
		//发布时，才会处理数据向redis同步
		$errno = 0; $error = "";
	} else {
		$errno = 9; $error = "查询错误。";
	}
	echo json_encode(array('errno'=>$errno, 'error'=>$error));
	exit;
}
$data = array();
if ($type=='modify') {
	$sql = "SELECT * FROM `lord_game_tips` WHERE `id` = $id AND `is_del` > 0";
	$res = $db->query($sql)->fetch();
	if ( $res ) {
		echo "已删除的不可再操作。";
		exit;
	}
	$sql = "SELECT * FROM `lord_game_tips` WHERE `id` = $id";
	$data = $db->query($sql)->fetch();
	if ( !$data ) {
		echo "出错了。";
		exit;
	}
}
if ($ispost) {
	$channel = isset($_REQUEST['channel'])?trim($_REQUEST['channel']):'all';
	$channel = $channel!='all'?$channel:'';
	$path = isset($_REQUEST['path']) ? trim($_REQUEST['path']) : '';
	$content = isset($_REQUEST['content']) ? trim($_REQUEST['content']) : '';
	$sort = isset($_REQUEST['sort']) && intval($_REQUEST['sort']) > 0 ? intval($_REQUEST['sort']) : 99;
	$version = $maxversion;
	$create_time = $update_time = $ut_now;
	if ($type=='add') {
		$sql = "INSERT INTO `lord_game_tips` (`channel`,`path`,`content`,`version`,`ver_ins`,`sort`,`create_time`,`update_time`)
		VALUES ('$channel','$path','$content',$version,$version,$sort,$create_time,$update_time)";
		$res = $pdo->getDB(1)->exec($sql);
		$res = $id = $pdo->getDB(1)->lastInsertId();
	}
	if ($type=='modify') {
		$sql = "SELECT * FROM `lord_game_tips` WHERE `id` = $id AND `is_del` > 0";
		$res = $db->query($sql)->fetch();
		if ( $res ) {
			$errno = 1; $error = "已删除的不可再操作。";
			echo json_encode(array('errno'=>$errno, 'error'=>$error));
			exit;
		}
		$sql = "UPDATE `lord_game_tips` SET `channel`='$channel',`path`='$path',`content`='$content',`version`=$version,`ver_upd`=$version,`sort`=$sort,`update_time`=$update_time WHERE `id`=$id";
		$res = $pdo->getDB(1)->exec($sql);
	}
	if ( $res ) {
		//发布时，才会处理数据向redis同步
		$errno = 0; $error = "";
	} else {
		$errno = 9; $error = "查询错误。";
	}
	$res = json_encode(array('errno'=>$errno, 'error'=>$error));
	if ( !$errno ) {
		header('Location: tipsList.php');
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
	<legend>底栏提示-<?php if($type=='add'){?>创建<?php }else{?>编辑<?php }?></legend>
	<form action="tipsAdd.php" method="post">
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
				<td style="width:70px;font-size:14px;">所属板块:</td>
				<td>
					<select class="span2" name="path">
						<?php
						foreach ($pathes as $k => $v) {
							$selected = $data&&$data['path']==$k?" selected='selected'":"";
							echo '<option value="'.$k.'"'.$selected.'>'.$v.'</option>';
						}
						?>
					</select>
				</td>
			</tr>
			<tr>
				<td style="width:70px;font-size:14px;">提示内容:</td>
				<td><input name="content" value="<?php if($data){echo $data['content'];} ?>" type="text" class="in_t" /></td>
			</tr>
			<tr>
				<td style="width:70px;font-size:14px;">提示排序:</td>
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
