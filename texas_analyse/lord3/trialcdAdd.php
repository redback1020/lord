<?php
$api = 'trialcd';//
header("Content-type: text/html; charset=utf-8");
require_once '../manage/checkPriv.php';
require_once '../include/database.class.php';
$channels_durty = array("51vappFT","alitech","alizhibo","android","aostv","appstore","appvtion","banana","beimi","bianfeng","bitgames","boyakeno","cik","CMCC","dou","drpengvoice","duole","duwei-iqiyi","fanmi","gangfeng","gao","hangkeweiye","haoshi","henangd","hifly","huawei","huaweiconsumer","icntv","IMT","infoTM","iptvhebei","jingling","jinnuowei","jinruixian","jinya1","jinyatai","jiuzhou","kuaiyou","laimeng","landiankechuang","leyou","lianyi","nibiru","pengrunsen","qiwangldkc","qiwangyfcz","qpod","qvod","realplay","robotplugin","ruixiangtongxin","runhe","shitouer","the5","threelegsfrog","thtflingyue","tshifi","ujob","uprui","UTskd","vsoontech","wanhuatong","wanmei","wanweitron","whatchannel","wobo","xiaomi","xinhancommon","xinhantena","xinhanvsoontech","xinhanyixinte","xunlei","xunma","yangcong","youjoytest","zuoqi");
$ut_now = time();
$type = isset($_REQUEST['type']) ? trim($_REQUEST['type']) : 'add';//add modify delete
$ispost = isset($_REQUEST['ispost']) ? intval($_REQUEST['ispost']) : 0;
$id = isset($_REQUEST['id']) ? intval($_REQUEST['id']) : 0;
if ($type=='delete') {
	$sql = "UPDATE `lord_list_trialcd` SET `is_del` = 1 WHERE `id` = $id";
	$res = $pdo->getDB(1)->exec($sql);
	$res = $res ? apiGet($api, $type, array('id'=>$id)) : array('errno'=>8, 'error'=>"查询错误。 $sql");
	echo json_encode($res);
	exit;
}
$data = array();
if ($type=='modify') {
	$sql = "SELECT * FROM `lord_list_trialcd` WHERE `id` = $id";
	$data = $db->query($sql)->fetch(PDO::FETCH_ASSOC);
	if ( !$data ) {
		echo "出错了。";
		exit;
	}
}
if ($ispost) {
	$channel = isset($_REQUEST['channel'])?trim($_REQUEST['channel']):'';
	$count = isset($_REQUEST['count'])?intval($_REQUEST['count']):0;
	$cooldown = isset($_REQUEST['cooldown'])?intval($_REQUEST['cooldown']):0;
	$sort = isset($_REQUEST['sort'])&&intval($_REQUEST['sort'])?intval($_REQUEST['sort']):99;
	$create_time = $update_time = $ut_now;
	if ($type=='add') {
		$sql = "INSERT INTO `lord_list_trialcd` (`channel`,`count`,`cooldown`,`is_del`,`sort`,`create_time`,`update_time`)
		VALUES ('$channel',$count,$cooldown,0,$sort,$create_time,$update_time)";
		$res = $pdo->getDB(1)->exec($sql);
	}
	if ($type=='modify') {
		$sql = "UPDATE `lord_list_trialcd` SET `channel`='$channel',`count`=$count,`cooldown`=$cooldown,`sort`=$sort,`update_time`=$update_time WHERE `id`=$id";
		$res = $pdo->getDB(1)->exec($sql);
	}
	if ( $res ) {
		if ( $type == 'add' ) $id = $pdo->getDB(1)->lastInsertId();
		$res = apiGet($api, $type, array('id'=>$id));
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
	<legend>救济冷却项 - <?php if($type=='add'){?>创建<?php }else{?>编辑<?php }?></legend>
	<form action="trialcdAdd.php" method="post">
		<input type="hidden" name="ispost" value="1" />
		<input type="hidden" name="type" value="<?=$type?>" />
		<input type="hidden" name="id" value="<?php if($data){echo $data['id'];} ?>" />
		<table class="table table-bordered table-condensed table-hover">
			<tr>
				<td style="width:70px;font-size:14px;">专属渠道:</td>
				<td>
					<select class="span2" name="channel">
						<option value="">全部</option>
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
				<td style="width:70px;font-size:14px;">救济次数:</td>
				<td><input name="count" value="<?php if($data){echo $data['count'];} ?>" type="text" class="in_t" /></td>
			</tr>
			<tr>
				<td style="width:70px;font-size:14px;">冷却秒数:</td>
				<td><input name="cooldown" value="<?php if($data){echo $data['cooldown'];} ?>" type="text" class="in_t" /></td>
			</tr>
			<tr>
				<td style="width:70px;font-size:14px;">排序:</td>
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
