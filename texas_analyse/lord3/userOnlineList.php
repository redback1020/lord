<?php
header("Content-type: text/html; charset=utf-8");
require_once '../manage/checkPriv.php';
require_once '../include/database.class.php';
//post|get to url 
function urlReq($url, $is_post = false, $data = null, $agent = 0, $cookie = null, $timeout = 3)
{
	if ($agent && is_int($agent)) {
		$user_agent = ini_get('user_agent');
		ini_set('user_agent', 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; SV1; .NET CLR 2.0.50727;)');
	}
	elseif ($agent && is_array($agent)) {
		$user_agent = ini_get('user_agent');
		ini_set('user_agent', $agent[array_rand($agent)]);
	}
	elseif (is_string($agent)) {
		$user_agent = ini_get('user_agent');
		ini_set('user_agent', $agent);
	}
	else {
		$user_agent = false;
	}
	$context['http']['method'] = ($is_post && is_array($data)) ? 'POST' : 'GET';
	$context['http']['header'] = ($is_post && is_array($data)) ? "Content-Type: application/x-www-form-urlencoded; charset=utf-8" : "Content-Type: text/html; charset=utf-8";
	$context['http']['timeout'] = $timeout;
	if ( $context['http']['method'] == 'POST' )
	{
		if ( $cookie && is_string($cookie) )
		{
			$context['http']['header'] .= PHP_EOL.$cookie;
		}
		if ( strpos($url, 'https://') === 0 && isset($data['https_user']) && isset($data['https_password']) )
		{
			$context['http']['header'] .= PHP_EOL."Authorization: Basic ".base64_encode($data['https_user'].":".$data['https_password']);
			unset($data['https_user']);
			unset($data['https_password']);
		}
		$context['http']['content'] = http_build_query($data, '', '&');
	}
	$res = file_get_contents($url, false, stream_context_create($context));
	$user_agent !== false && ini_set('user_agent', $user_agent);
	return $res;
}
$sql = "SELECT * FROM `lord_online_detail` ORDER BY `id` DESC LIMIT 1";
$res = $db->query($sql)->fetch(PDO::FETCH_ASSOC);
$totals['在线总计'] = $res['allOnline'];
$totals['只在房间'] = $res['allInRoom'];
$totals['只在大厅'] = $res['allInLobby'];
$totals['统计时间'] = date("Y-m-d H:i:s", $res['ut']);
$totals['房间个数'] = $res['allRoomNum'];
$totals['牌桌个数'] = $res['allTableNum'];
$totals['在桌活跃'] = $res['allInTableActive'];
$totals['在桌掉线'] = $res['allInTableOffline'];
$totals['在桌假人'] = $res['allInTableRobot'];
$totals['房间详情']['1000']['房间编号'] = '新手场';
$totals['房间详情']['1000']['牌桌个数'] = $res['room0TableNum'];
$totals['房间详情']['1000']['在桌活跃'] = $res['room0TableActive'];
$totals['房间详情']['1000']['在桌掉线'] = $res['room0TableOffline'];
$totals['房间详情']['1000']['在桌假人'] = $res['room0TableRobot'];
$totals['房间详情']['1001']['房间编号'] = '初级场';
$totals['房间详情']['1001']['牌桌个数'] = $res['room1TableNum'];
$totals['房间详情']['1001']['在桌活跃'] = $res['room1TableActive'];
$totals['房间详情']['1001']['在桌掉线'] = $res['room1TableOffline'];
$totals['房间详情']['1001']['在桌假人'] = $res['room1TableRobot'];
$totals['房间详情']['1002']['房间编号'] = '中级场';
$totals['房间详情']['1002']['牌桌个数'] = $res['room2TableNum'];
$totals['房间详情']['1002']['在桌活跃'] = $res['room2TableActive'];
$totals['房间详情']['1002']['在桌掉线'] = $res['room2TableOffline'];
$totals['房间详情']['1002']['在桌假人'] = $res['room2TableRobot'];
$totals['房间详情']['1003']['房间编号'] = '高级场';
$totals['房间详情']['1003']['牌桌个数'] = $res['room3TableNum'];
$totals['房间详情']['1003']['在桌活跃'] = $res['room3TableActive'];
$totals['房间详情']['1003']['在桌掉线'] = $res['room3TableOffline'];
$totals['房间详情']['1003']['在桌假人'] = $res['room3TableRobot'];
$totals['房间详情']['1004']['房间编号'] = '竞技场';
$totals['房间详情']['1004']['牌桌个数'] = $res['room4TableNum'];
$totals['房间详情']['1004']['在桌活跃'] = $res['room4TableActive'];
$totals['房间详情']['1004']['在桌掉线'] = $res['room4TableOffline'];
$totals['房间详情']['1004']['在桌假人'] = $res['room4TableRobot'];
$cool_num = isset($_REQUEST['cool_num']) ? intval($_REQUEST['cool_num']) : 0;
$user = array();
if ( $cool_num ) {
	$uid = 0;
	$sql = "SELECT `uid` FROM `lord_game_user` WHERE `cool_num` = $cool_num";
	$res = $db->query($sql)->fetch();
	if ( $res ) $uid = intval($res['uid']);
	if ( $uid ) {
		//security
		$api = isset($_REQUEST['api']) ? trim($_REQUEST['api']) : 'online';//
		$type = isset($_REQUEST['type']) ? trim($_REQUEST['type']) : 'user';//
		$sign = "Hfhdkgfgu4y98rh98h89h9";
		$time = time();
		$mac = md5($api."&".$type."&&".$sign."&&&".$time);
		$baseurl = API_BASE."{$api}&type={$type}&sign={$sign}&mac={$mac}&time={$time}";
		//params
		$baseurl.= "&uid=$uid";
		//execute
		$res = urlReq($baseurl);
		//respond
		$user = $res ? json_decode($res, 1) : array();
		$user = $user ? $user : array();
	}
}
?>
<link rel="stylesheet" type="text/css" href="../bootstrap/css/bootstrap.min.css" />
<body>
	<div style="padding:8px 10px;">
	<form method="get">
		<fieldset>
		<legend>在线用户查询</legend>
		<div class="row">
			<div class="span2">
				<label>靓号/编号ID：</label>
				<input class="span2" type="text" id="cool_num" name="cool_num" style="height:30px" value="<?=$cool_num?$cool_num:'';?>"/>
			</div>
			<div span="span1" style="float:right;">
				<input type="submit" value="查&nbsp;&nbsp;询"  class="btn" />
			</div>
		</div>
		</fieldset>
	</form>

	<?php if ( $totals ) { ?>
	<div><table class="table table-bordered table-condensed table-hover" style="font-size:12px;">
		<tr class="info" style="font-size:14px;">
			<td>在线总计: <?php echo $totals['在线总计']; ?></td>
			<td>只在房间: <?php echo $totals['只在房间']; ?></td>
			<td>只在大厅: <?php echo $totals['只在大厅']; ?></td>
			<td colspan="2">统计时间: <?php echo $totals['统计时间']; ?></td>
		</tr>
		<tr class="info" style="font-size:14px;">
			<td>房间个数: <?php echo $totals['房间个数']; ?></td>
			<td>牌桌个数: <?php echo $totals['牌桌个数']; ?></td>
			<td>在桌活跃: <?php echo $totals['在桌活跃']; ?></td>
			<td>在桌掉线: <?php echo $totals['在桌掉线']; ?></td>
			<td>在桌假人: <?php echo $totals['在桌假人']; ?></td>
		</tr>
		<?php foreach ( $totals['房间详情'] as $k => $v ) { ?>
		<tr>
			<td>房间编号: <?php echo $v['房间编号']; ?></td>
			<td>牌桌个数: <?php echo $v['牌桌个数']; ?></td>
			<td>在桌活跃: <?php echo $v['在桌活跃']; ?></td>
			<td>在桌掉线: <?php echo $v['在桌掉线']; ?></td>
			<td>在桌假人: <?php echo $v['在桌假人']; ?></td>
		</tr>
		<?php } ?>
	</table></div>
	<?php } ?>

	<table class="table table-bordered table-condensed table-hover" style="font-size:12px;">
		<tbody id="inRoomUser">
			<tr class="info">
				<td width="10%"><strong>UID</strong></td>
				<td width="10%"><strong>编号ID</strong></td>
				<td width="10%"><strong>性别</strong></td> 
				<td width="10%"><strong>昵称</strong></td>
				<td width="10%"><strong>签名</strong></td>
				<td width="10%"><strong>房间</strong></td>
				<td width="10%"><strong>牌桌</strong></td>
				<td width="10%"><strong>筹码</strong></td>  
				<td width="10%"><strong>奖券</strong></td>
				<td width="10%"><strong>抽奖数</strong></td>
			</tr>
			<?php if ( $user ) { ?>
			<tr class="table-body">
				<td><a href="userInfo.php?uid=<?=$user['uid']?>"><?=$user['uid']?></a></td>
				<td><?=$user['cool_num']?></td>
				<td><?=$user['sex']==1?"男":"女";?></td>
				<td><?=$user['nick']?></td>
				<td><?=$user['word']?></td>
				<td><?=$user['roomId']?>&nbsp;</td>
				<td><?=$user['tableId']?>&nbsp;</td>
				<td><?=$user['coins']?></td>
				<td><?=$user['coupon']?></td>
				<td><?=$user['lottery']?></td>
			</tr>
			<?php } ?>
		</tbody>
	</table>
</div>
</body>
