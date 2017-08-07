<?php
$api = 'topic';//
header("Content-type: text/html; charset=utf-8");
require_once '../manage/checkPriv.php';
require_once '../include/database.class.php';
$channels_durty = array("51vappFT","alitech","alizhibo","android","aostv","appstore","appvtion","banana","beimi","bianfeng","bitgames","boyakeno","cik","CMCC","dou","drpengvoice","duole","duwei-iqiyi","fanmi","gangfeng","gao","hangkeweiye","haoshi","henangd","hifly","huawei","huaweiconsumer","icntv","IMT","infoTM","iptvhebei","jingling","jinnuowei","jinruixian","jinya1","jinyatai","jiuzhou","kuaiyou","laimeng","landiankechuang","leyou","lianyi","nibiru","pengrunsen","qiwangldkc","qiwangyfcz","qpod","qvod","realplay","robotplugin","ruixiangtongxin","runhe","shitouer","the5","threelegsfrog","thtflingyue","tshifi","ujob","uprui","UTskd","vsoontech","wanhuatong","wanmei","wanweitron","whatchannel","wobo","xiaomi","xinhancommon","xinhantena","xinhanvsoontech","xinhanyixinte","xunlei","xunma","yangcong","youjoytest","zuoqi");
$ut_now = time();
$type = isset($_REQUEST['type']) ? trim($_REQUEST['type']) : 'add';//add delete offline modify
$ispost = isset($_REQUEST['ispost']) ? intval($_REQUEST['ispost']) : 0;
$id = isset($_REQUEST['id']) ? intval($_REQUEST['id']) : 0;
if ($type=='delete') {
	$sql = "UPDATE `lord_game_topic` SET `state` = 2 WHERE `id` = $id";
	$res = $pdo->getDB(1)->exec($sql);
	$res = $res ? apiGet($api, $type, array('id'=>$id)) : array('errno'=>8, 'error'=>"查询错误。 $sql");
	echo json_encode($res);
	exit;
}
if ($type=='offline') {
	$sql = "UPDATE `lord_game_topic` SET `state` = 1 WHERE `id` = $id";
	$res = $pdo->getDB(1)->exec($sql);
	$res = $res ? apiGet($api, $type, array('id'=>$id)) : array('errno'=>8, 'error'=>"查询错误。 $sql");
	echo json_encode($res);
	exit;
}
$data = array();
if ($type=='modify') {
	$sql = "SELECT * FROM `lord_game_topic` WHERE `id` = $id";
	$data = $db->query($sql)->fetch();
	if ( !$data ) {
		echo "出错了。";
		exit;
	}
	if ( $data['prizes'] ) {
		$data['prizes'] = json_decode($data['prizes'], 1);
		$propItems = array();
		if (isset($data['prizes']['propItems'])) {
			foreach ( $data['prizes']['propItems'] as $k => $v )
			{
				$propItems[$v['id']] = $v['num'];
			}
			$data['prizes']['propItems'] = $propItems;
		}
	}
}
if ($ispost) {
	$channel = isset($_REQUEST['channel']) ? trim($_REQUEST['channel']) : '';
	$channot = isset($_REQUEST['channot']) ? trim($_REQUEST['channot']) : '';
	$subject = isset($_REQUEST['subject']) ? trim($_REQUEST['subject']) : '';
	$content = isset($_REQUEST['content']) ? str_replace("\r", "", trim($_REQUEST['content'])) : '';
	$start_time = isset($_REQUEST['start_time'])&&trim($_REQUEST['start_time'])?strtotime(trim($_REQUEST['start_time'])):0;
	$end_time = isset($_REQUEST['end_time'])&&trim($_REQUEST['end_time'])?strtotime(trim($_REQUEST['end_time'])):0;
	$start_lobby = isset($_REQUEST['start_lobby'])&&trim($_REQUEST['start_lobby'])?strtotime(trim($_REQUEST['start_lobby'])):0;
	$end_lobby = isset($_REQUEST['end_lobby'])&&trim($_REQUEST['end_lobby'])?strtotime(trim($_REQUEST['end_lobby'])):0;
	$prizes = isset($_REQUEST['prizes'])?$_REQUEST['prizes']:array();
	$prizes['gold'] = intval($prizes['gold']); if ( !$prizes['gold']) unset($prizes['gold']);
	$prizes['coins'] = intval($prizes['coins']); if ( !$prizes['coins']) unset($prizes['coins']);
	$prizes['coupon'] = intval($prizes['coupon']); if ( !$prizes['coupon']) unset($prizes['coupon']);
	$prizes['lottery'] = intval($prizes['lottery']); if ( !$prizes['lottery']) unset($prizes['lottery']);
	if ( isset($prizes['propItems']) ) {
		$sql = "SELECT * FROM `lord_game_prop` WHERE `state` = 0";
		$items = $itemids = array();
		$itemall = $db->query($sql)->fetchAll();
		$itemall = $itemall ? $itemall : array();
		foreach ( $itemall as $k => $v )
		{
			$itemids[$v['id']] = $v;
		}
		foreach ( $prizes['propItems'] as $k => $v )
		{
			if ( !isset($itemids[$k]) ) continue;
			$items[] = array('id'=>$k, 'name'=>$itemids[$k]['name'], 'categoryId'=>$itemids[$k]['categoryId'], 'num'=>$v&&$v>0?intval($v):1);
		}
		if ( !$items ) {
			unset($prizes['propItems']);
		} else {
			$prizes['propItems'] = $items;
		}
	}
	$prizes = $prizes?json_encode($prizes):'';
	$prizes = $db->quote($prizes);
	$sort = isset($_REQUEST['sort'])&&intval($_REQUEST['sort'])?intval($_REQUEST['sort']):99;
	$create_time = $update_time = $ut_now;
	if ($type=='add') {
		$sql = "INSERT INTO `lord_game_topic` (`channel`,`channot`,`subject`,`content`,`start_time`,`end_time`,`start_lobby`,`end_lobby`,`prizes`,`sort`,`create_time`,`update_time`)
		VALUES ('$channel','$channot','$subject','$content',$start_time,$end_time,$start_lobby,$end_lobby,$prizes,$sort,$create_time,$update_time)";
	}
	if ($type=='modify') {
		$sql = "UPDATE `lord_game_topic` SET `channel`='$channel',`channot`='$channot',`subject`='$subject',`content`='$content',`start_time`=$start_time,`end_time`=$end_time,`start_lobby`=$start_lobby,`end_lobby`=$end_lobby,`prizes`=$prizes,`sort`=$sort,`update_time`=$update_time WHERE `id`=$id";
	}
	$res = $pdo->getDB(1)->exec($sql);
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
	<legend>活动-<?php if($type=='add'){?>创建<?php }else{?>编辑<?php }?></legend>
	<form action="topicAdd.php" method="post">
		<input type="hidden" name="ispost" value="1" />
		<input type="hidden" name="type" value="<?=$type?>" />
		<input type="hidden" name="id" value="<?php if($data){echo $data['id'];} ?>" />
		<table class="table table-bordered table-condensed table-hover">
			<tr>
				<td style="width:70px;font-size:14px;">许可渠道:</td>
				<td><input name="channel" value="<?php if($data){echo $data['channel'];} ?>" type="text" class="in_t" /></td>
			</tr>
			<tr>
				<td style="width:70px;font-size:14px;">屏蔽渠道:</td>
				<td><input name="channot" value="<?php if($data){echo $data['channot'];} ?>" type="text" class="in_t" /></td>
			</tr>
			<tr>
				<td style="width:70px;font-size:14px;">活动标题:</td>
				<td><input name="subject" value="<?php if($data){echo $data['subject'];} ?>" type="text" class="in_t" /></td>
			</tr>
			<tr>
				<td style="width:70px;font-size:14px;">活动内容:</td>
				<td><textarea name="content" class="in_a"><?php if($data){echo $data['content'];} ?></textarea></td>
			</tr>
			<tr>
				<td style="width:70px;font-size:14px;">活动内图:</td>
				<td>暂不支持，目前需要手动上传到CDN</td>
			</tr>
			<tr>
				<td style="width:70px;font-size:14px;">活动奖励:</td>
				<td style="width:800px;">
					<!-- 乐　币　：<input name="prizes[gold]" value="<?php if($data&&isset($data['prizes']['gold'])){echo $data['prizes']['gold'];} ?>" type="text" class="in_t2" /> 个<br> -->
					乐　豆　：<input name="prizes[coins]" value="<?php if($data&&isset($data['prizes']['coins'])){echo $data['prizes']['coins'];} ?>" type="text" class="in_t2" /> 个<br>
					乐　券　：<input name="prizes[coupon]" value="<?php if($data&&isset($data['prizes']['coupon'])){echo $data['prizes']['coupon'];} ?>" type="text" class="in_t2" /> 个<br>
					免费抽奖：<input name="prizes[lottery]" value="<?php if($data&&isset($data['prizes']['lottery'])){echo $data['prizes']['lottery'];} ?>" type="text" class="in_t2" /> 次<br>
					道　具　：<input name="prizes[propItems][2]" <?php if($data&&isset($data['prizes']['propItems']['2'])){echo "checked='checked'";} ?> id="prop2" type="checkbox" /><label for="prop2">高手套装（7天）</label>
					　　<input name="prizes[propItems][3]" <?php if($data&&isset($data['prizes']['propItems']['3'])){echo "checked='checked'";} ?> id="prop3" type="checkbox" /><label for="prop3">大师套装（30天）</label>
					　　<input name="prizes[propItems][4]" <?php if($data&&isset($data['prizes']['propItems']['4'])){echo "checked='checked'";} ?> id="prop4" type="checkbox" /><label for="prop4">富豪套装</label>
				</td>
			</tr>
			<tr>
				<td style="width:70px;font-size:14px;">上线日期:</td>
				<td>
					<input name="start_time" value="<?php if($data){echo date('Y-m-d',$data['start_time']);} ?>" class="textbox dtime in_t2" type="text" readonly onclick="WdatePicker({dateFmt:'yyyy-MM-dd'})"/>(零点) -
					<input name="end_time" value="<?php if($data){echo date('Y-m-d',$data['end_time']);} ?>" class="textbox dtime in_t2" type="text" readonly onclick="WdatePicker({dateFmt:'yyyy-MM-dd'})"/>(零点)
				</td>
			</tr>
			<tr>
				<td style="width:70px;font-size:14px;">热门日期:</td>
				<td>
					<input name="start_lobby" value="<?php if($data){echo date('Y-m-d',$data['start_lobby']);} ?>" class="textbox dtime in_t2" type="text" readonly onclick="WdatePicker({dateFmt:'yyyy-MM-dd'})"/>(零点) -
					<input name="end_lobby" value="<?php if($data){echo date('Y-m-d',$data['end_lobby']);} ?>" class="textbox dtime in_t2" type="text" readonly onclick="WdatePicker({dateFmt:'yyyy-MM-dd'})"/>(零点)
				</td>
			</tr>
			<tr>
				<td style="width:70px;font-size:14px;">热门图片:</td>
				<td>暂不支持，目前需要手动上传到CDN</td>
			</tr>
			<tr>
				<td style="width:70px;font-size:14px;">活动排序:</td>
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
