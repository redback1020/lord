<?php
header("Content-type: text/html; charset=utf-8");
require_once '../manage/checkPriv.php';
require_once '../include/database.class.php';
$channels_durty = array("51vappFT","alitech","alizhibo","android","aostv","appstore","appvtion","banana","beimi","bianfeng","bitgames","boyakeno","cik","CMCC","dou","drpengvoice","duole","duwei-iqiyi","fanmi","gangfeng","gao","hangkeweiye","haoshi","henangd","hifly","huawei","huaweiconsumer","icntv","IMT","infoTM","iptvhebei","jingling","jinnuowei","jinruixian","jinya1","jinyatai","jiuzhou","kuaiyou","laimeng","landiankechuang","leyou","lianyi","nibiru","pengrunsen","qiwangldkc","qiwangyfcz","qpod","qvod","realplay","robotplugin","ruixiangtongxin","runhe","shitouer","the5","threelegsfrog","thtflingyue","tshifi","ujob","uprui","UTskd","vsoontech","wanhuatong","wanmei","wanweitron","whatchannel","wobo","xiaomi","xinhancommon","xinhantena","xinhanvsoontech","xinhanyixinte","xunlei","xunma","yangcong","youjoytest","zuoqi");
$itemtypes = array('coupon2mobifee'=>'乐券兑话费');
$ut_now = time();
$type = isset($_REQUEST['type']) ? trim($_REQUEST['type']) : 'add';//add modify delete online offline
$ispost = isset($_REQUEST['ispost']) ? intval($_REQUEST['ispost']) : 0;
$id = isset($_REQUEST['id']) ? intval($_REQUEST['id']) : 0;
if ($type=='delete') {
	$sql = "UPDATE `lord_list_convert` SET `state` = 2 WHERE `id` = $id";
	$res = $pdo->getDB(1)->exec($sql);
	$errno = 0; $error = "";
	if ( $res ) {
		$api = 'convert';//
		$type = 'delete';//
		$res = apiGet($api, $type, array('id'=>$id,'data'=>'[]'));
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
if ($type=='online') {
	$sql = "UPDATE `lord_list_convert` SET `state` = 0 WHERE `id` = $id";
	$res = $pdo->getDB(1)->exec($sql);
	$errno = 0; $error = "";
	if ( $res ) {
		//security
		$api = 'convert';//
		$type = 'online';//
		$res = apiGet($api, $type, array('id'=>$id,'data'=>'[]'));
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
if ($type=='offline') {
	$sql = "UPDATE `lord_list_convert` SET `state` = 1 WHERE `id` = $id";
	$res = $pdo->getDB(1)->exec($sql);
	$errno = 0; $error = "";
	if ( $res ) {
		//security
		$api = 'convert';//
		$type = 'offline';//
		$res = apiGet($api, $type, array('id'=>$id,'data'=>'[]'));
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
	$sql = "SELECT * FROM `lord_list_convert` WHERE `id` = $id";
	$data = $db->query($sql)->fetch(PDO::FETCH_ASSOC);
	if ( !$data ) {
		echo "出错了。";
		exit;
	}
}
if ($ispost) {
	$itemtype = isset($_REQUEST['itemtype'])?trim($_REQUEST['itemtype']):'coupon2mobifee';
	$channel = isset($_REQUEST['channel'])?trim($_REQUEST['channel']):'all'; $channel = $channel!='all'?$channel:'';
	$title = isset($_REQUEST['title'])?trim($_REQUEST['title']):'';
	$fileId = isset($_REQUEST['fileId'])?intval($_REQUEST['fileId']):0;
	$value = isset($_REQUEST['value'])?intval($_REQUEST['value']):0;
	$price = isset($_REQUEST['price'])?intval($_REQUEST['price']):0;
	$store = isset($_REQUEST['store'])?intval($_REQUEST['store']):-1;
	$is_onsale = isset($_REQUEST['is_onsale'])?intval($_REQUEST['is_onsale']):0;
	$onsale = isset($_REQUEST['onsale'])?trim($_REQUEST['onsale']):'';
	$sort = isset($_REQUEST['sort'])&&intval($_REQUEST['sort'])?intval($_REQUEST['sort']):99;
	$create_time = $update_time = $ut_now;
	if ($type=='add') {
		$sql = "INSERT INTO `lord_list_convert` (`type`,`channel`,`title`,`fileId`,`value`,`price`,`store`,`is_onsale`,`onsale`,`sort`,`create_time`,`update_time`)
		VALUES ('$itemtype','$channel','$title',$fileId,$value,$price,$store,$is_onsale,'$onsale',$sort,$create_time,$update_time)";
		$res = $pdo->getDB(1)->exec($sql);
		$res = $id = $pdo->getDB(1)->lastInsertId();
	}
	if ($type=='modify') {
		$sql = "UPDATE `lord_list_convert` SET `type`='$itemtype',`channel`='$channel',`title`='$title',`fileId`=$fileId,`value`=$value,`price`=$price,`store`=$store,`is_onsale`=$is_onsale,`onsale`='$onsale',`sort`=$sort,`update_time`=$update_time WHERE `id`=$id";
		$res = $pdo->getDB(1)->exec($sql);
	}
	$errno = 0; $error = "";
	if ( $res ) {
		//security
		$api = 'convert';//
		$type = $type;//
		$res = apiGet($api, $type, array('id'=>$id,'data'=>'[]'));
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
		header('Location: convertList.php');
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
	<legend>兑换项 - <?php if($type=='add'){?>创建<?php }else{?>编辑<?php }?></legend>
	<form action="convertAdd.php" method="post">
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
				<td style="width:70px;font-size:14px;">类型:</td>
				<td>
					<select class="span2" name="itemtype">
						<?php
						foreach ($itemtypes as $k => $v) {
							$selected = $data&&$data['type']==$k?" selected='selected'":"";
							echo '<option value="'.$k.'"'.$selected.'>'.$v.'</option>';
						}
						?>
					</select>
				</td>
			</tr>
			<tr>
				<td style="width:70px;font-size:14px;">名称:</td>
				<td><input name="title" value="<?php if($data){echo $data['title'];} ?>" type="text" class="in_t" /></td>
			</tr>
			<tr>
				<td style="width:70px;font-size:14px;">兑换值:</td>
				<td><input name="value" value="<?php if($data){echo $data['value'];} ?>" type="text" class="in_t" /></td>
			</tr>
			<tr>
				<td style="width:70px;font-size:14px;">价格值:</td>
				<td><input name="price" value="<?php if($data){echo $data['price'];} ?>" type="text" class="in_t" /></td>
			</tr>
			<tr>
				<td style="width:70px;font-size:14px;">库存数:</td>
				<td><input name="store" value="<?php if($data){echo $data['store'];} ?>" type="text" class="in_t" /></td>
			</tr>
			<tr>
				<td style="width:70px;font-size:14px;">图片编号:</td>
				<td><input name="fileId" value="<?php if($data){echo $data['fileId'];} ?>" type="text" class="in_t" /></td>
			</tr>
			<tr>
				<td style="width:70px;font-size:14px;">促销状态:</td>
				<td>
					<select class="span2" name="is_onsale">
						<option value="0"<?php if($data&&$data['is_onsale']==0){echo " selected='selected'";} ?>>无</option>
						<option value="1"<?php if($data&&$data['is_onsale']==1){echo " selected='selected'";} ?>>正在促销</option>
					</select>
				</td>
			</tr>
			<tr>
				<td style="width:70px;font-size:14px;">促销文字:</td>
				<td><input name="onsale" value="<?php if($data){echo $data['onsale'];} ?>" type="text" class="in_t" /> 非促销时，可以不填</td>
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
