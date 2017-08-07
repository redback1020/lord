<?php
header("Content-type: text/html; charset=utf-8");
require_once '../manage/checkPriv.php';
require_once '../include/database.class.php';
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
$totals['房间详情']['1000']['房间编号'] = '经典新手场';
$totals['房间详情']['1000']['牌桌个数'] = $res['room1000TableNum'];
$totals['房间详情']['1000']['在桌活跃'] = $res['room1000TableActive'];
$totals['房间详情']['1000']['在桌掉线'] = $res['room1000TableOffline'];
$totals['房间详情']['1000']['在桌假人'] = $res['room1000TableRobot'];
$totals['房间详情']['1001']['房间编号'] = '经典初级场';
$totals['房间详情']['1001']['牌桌个数'] = $res['room1001TableNum'];
$totals['房间详情']['1001']['在桌活跃'] = $res['room1001TableActive'];
$totals['房间详情']['1001']['在桌掉线'] = $res['room1001TableOffline'];
$totals['房间详情']['1001']['在桌假人'] = $res['room1001TableRobot'];
$totals['房间详情']['1002']['房间编号'] = '经典中级场';
$totals['房间详情']['1002']['牌桌个数'] = $res['room1002TableNum'];
$totals['房间详情']['1002']['在桌活跃'] = $res['room1002TableActive'];
$totals['房间详情']['1002']['在桌掉线'] = $res['room1002TableOffline'];
$totals['房间详情']['1002']['在桌假人'] = $res['room1002TableRobot'];
$totals['房间详情']['1003']['房间编号'] = '经典高级场';
$totals['房间详情']['1003']['牌桌个数'] = $res['room1003TableNum'];
$totals['房间详情']['1003']['在桌活跃'] = $res['room1003TableActive'];
$totals['房间详情']['1003']['在桌掉线'] = $res['room1003TableOffline'];
$totals['房间详情']['1003']['在桌假人'] = $res['room1003TableRobot'];
$totals['房间详情']['1007']['房间编号'] = '赖子新手场';
$totals['房间详情']['1007']['牌桌个数'] = $res['room1007TableNum'];
$totals['房间详情']['1007']['在桌活跃'] = $res['room1007TableActive'];
$totals['房间详情']['1007']['在桌掉线'] = $res['room1007TableOffline'];
$totals['房间详情']['1007']['在桌假人'] = $res['room1007TableRobot'];
$totals['房间详情']['1008']['房间编号'] = '赖子初级场';
$totals['房间详情']['1008']['牌桌个数'] = $res['room1008TableNum'];
$totals['房间详情']['1008']['在桌活跃'] = $res['room1008TableActive'];
$totals['房间详情']['1008']['在桌掉线'] = $res['room1008TableOffline'];
$totals['房间详情']['1008']['在桌假人'] = $res['room1008TableRobot'];
$totals['房间详情']['1009']['房间编号'] = '赖子中级场';
$totals['房间详情']['1009']['牌桌个数'] = $res['room1009TableNum'];
$totals['房间详情']['1009']['在桌活跃'] = $res['room1009TableActive'];
$totals['房间详情']['1009']['在桌掉线'] = $res['room1009TableOffline'];
$totals['房间详情']['1009']['在桌假人'] = $res['room1009TableRobot'];
$totals['房间详情']['1010']['房间编号'] = '赖子高级场';
$totals['房间详情']['1010']['牌桌个数'] = $res['room1010TableNum'];
$totals['房间详情']['1010']['在桌活跃'] = $res['room1010TableActive'];
$totals['房间详情']['1010']['在桌掉线'] = $res['room1010TableOffline'];
$totals['房间详情']['1010']['在桌假人'] = $res['room1010TableRobot'];
$totals['房间详情']['1004']['房间编号'] = '竞技场';
$totals['房间详情']['1004']['牌桌个数'] = $res['room1004TableNum'];
$totals['房间详情']['1004']['在桌活跃'] = $res['room1004TableActive'];
$totals['房间详情']['1004']['在桌掉线'] = $res['room1004TableOffline'];
$totals['房间详情']['1004']['在桌假人'] = $res['room1004TableRobot'];
$totals['房间详情']['3011']['房间编号'] = '热身场';
$totals['房间详情']['3011']['牌桌个数'] = $res['room3011TableNum'];
$totals['房间详情']['3011']['在桌活跃'] = $res['room3011TableActive'];
$totals['房间详情']['3011']['在桌掉线'] = $res['room3011TableOffline'];
$totals['房间详情']['3011']['在桌假人'] = $res['room3011TableRobot'];
$totals['房间详情']['3012']['房间编号'] = '大师场';
$totals['房间详情']['3012']['牌桌个数'] = $res['room3012TableNum'];
$totals['房间详情']['3012']['在桌活跃'] = $res['room3012TableActive'];
$totals['房间详情']['3012']['在桌掉线'] = $res['room3012TableOffline'];
$totals['房间详情']['3012']['在桌假人'] = $res['room3012TableRobot'];
$totals['房间详情']['3013']['房间编号'] = '总决场';
$totals['房间详情']['3013']['牌桌个数'] = $res['room3013TableNum'];
$totals['房间详情']['3013']['在桌活跃'] = $res['room3013TableActive'];
$totals['房间详情']['3013']['在桌掉线'] = $res['room3013TableOffline'];
$totals['房间详情']['3013']['在桌假人'] = $res['room3013TableRobot'];
$cool_num = isset($_REQUEST['cool_num']) ? intval($_REQUEST['cool_num']) : 0;
$uid = isset($_REQUEST['uid']) ? intval($_REQUEST['uid']) : 0;
$isAjax = isset($_REQUEST['isAjax']) ? intval($_REQUEST['isAjax']) : 0;
$user = array();
if ( $cool_num || $uid ) {
	if ( $cool_num ) {
		$sql = "SELECT `uid` FROM `lord_game_user` WHERE `cool_num` = $cool_num";
		$res = $db->query($sql)->fetch(PDO::FETCH_ASSOC);
		$uid = $res ? intval($res['uid']) : 0;
	}
	if ( !$isAjax && $uid ) {
		//security
		$api = isset($_REQUEST['api']) ? trim($_REQUEST['api']) : 'online';//
		$type = isset($_REQUEST['type']) ? trim($_REQUEST['type']) : 'getuser';//
		$res = apiGet($api, $type, array('uid'=>$uid));
		$online = $res ? $res['data'] : array();
	}
	if ( $isAjax && $uid ) {
		$ukey = isset($_REQUEST['ukey']) ? trim($_REQUEST['ukey']) : '';
		$uval = isset($_REQUEST['uval']) ? trim($_REQUEST['uval']) : '';
		$api = isset($_REQUEST['api']) ? trim($_REQUEST['api']) : 'online';//
		$type = isset($_REQUEST['type']) ? trim($_REQUEST['type']) : 'getuser';//
		$res = apiGet($api, $type, array('uid'=>$uid,'ukey'=>$ukey,'uval'=>$uval));
		if ( $res ) {
			$errno = $res['errno']; $error = $res['error'];
		} else {
			$errno = 9; $error = "接口错误。";
		}
		echo json_encode(array('errno'=>$errno,'error'=>$error));
		exit;
	}
}
?>
<script type="text/javascript" src="../js/jquery.js"></script>
<script type="text/javascript" src="../js/My97DatePicker/WdatePicker.js"></script>
<link rel="stylesheet" type="text/css" href="../bootstrap/css/bootstrap.min.css" />
<style type="text/css">
table.table{ font-size: 12px;}
table.table th{ white-space: nowrap;}
.h5{margin: 10px 0;}
.h5 h5{margin: 0;color: #c00;}
h4{ border-bottom: 1px solid #369;line-height: 30px;clear:left;}
dl{ float: left; width: 285px;margin:0;padding:0;overflow:hidden;}
dt{ float: left; width: 140px;text-align:right;margin:0;padding:0 5px 0 0;line-height:30px;overflow:hidden;}
dd{ float: left; width: 140px;white-space:nowrap;margin:0;padding:0;overflow:hidden;}
dd .span2{height:25px;width:78px!important;float:left!important;margin:0 1px 5px 0;}
</style>
<script>
function ajaxto( o, urlTo ) {
	var _a = urlTo.split('/'); var _this = $(o);
	if ( _a[0] != "http" ) { var _b = self.location.href.split('/'); delete _b[_b.length-1]; urlTo = _b.join('/') + urlTo; }
	$.getJSON(urlTo, function(data){
		if ( data ) {
			if ( data.errno == 0 ) {
				alert("操作成功。"); $('form').submit();
			} else {
				alert("操作失败["+data.errno+"]："+data.error);
			}
		}
	});
}
$(function(){
	$('.btn2').click(function(){
		var _this = $(this);
		var ukey = _this.attr('ukey');
		var _key = ukey.split('.');
		var uval = '';
		var type = '';
		if ( _key.length > 1 ) {
			type = 'modify';
			uval = _this.prev().val();
		} else {
			type = 'delete';
			uval = '';
		}
		$.getJSON('userOnline.php?isAjax=1&uid=<?=$uid?>&type='+type+'&ukey='+ukey+'&uval='+uval, function(data){
			if ( data ) {
				if ( data.errno == 0 ) {
					alert("操作成功。"); $('form').submit();
				} else {
					alert("操作失败["+data.errno+"]："+data.error);
				}
			}
		});
	});
});
</script>
<body>
<div style="position:absolute;top:0;left:0;margin-left:10px;width:98%;">
<fieldset>
	<legend>每分钟在线用户合计</legend>
</fieldset>

<?php if ( $totals ) { ?>
<table class="table table-bordered table-condensed table-hover">
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
</table>
<?php } ?>

<fieldset>
	<legend>查询在线用户数据</legend>
	<div class="row"><form method="get">
		<div class="span2">
			<label>UID：</label>
			<input class="span2" type="text" id="uid" name="uid" style="height:30px" value="<?=$uid?$uid:'';?>"/>
		</div>
		<div class="span2">
			<label>编号ID：</label>
			<input class="span2" type="text" id="cool_num" name="cool_num" style="height:30px" value="<?=$cool_num?$cool_num:'';?>"/>
		</div>
		<div span="span1" style="float:right;">
			<input type="submit" value="查&nbsp;&nbsp;询"  class="btn" />
		</div>
	</form></div>
</fieldset>

<table class="table table-bordered table-condensed table-hover">
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
	<?php if ( $online && $online["lord_user_info_$uid"] ) { $user = $online["lord_user_info_$uid"]; ?>
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
</table>

<div class="h5">
<h5>重要提示：</h5>
<h5>1、下面是游戏内存中的实时数据区块。理论上讲，这些功能只为技术开发人服务，用于数据排查、开发调试、bug分析。</h5>
<h5>2、如果需要“修改”某一项或“清理”某区块，您务必要先了解下面各个区块、各个属性的意义，防止发生严重事故。</h5>
<h5>3、运营同事，如果明确了解并发现有明显异常(如coins为负数)，需要把“查询条件”、“数据区块”、“属性数值”截图上报到产品负责人。</h5>
</div>

<?php
if ( $online && $online["lord_user_info_$uid"] ) {
	echo "<input ukey=\"lord_user_info_$uid\" type=\"button\" value=\"清理\" class=\"btn2\" style=\"float:right;clear:left;\" /><h4>用户当前基础数据 lord_user_info_$uid</h4>";
	foreach ( $online["lord_user_info_$uid"] as $k => $v ) {
		if ( is_array($v) ) continue;
		echo "<dl><dt>$k :</dt><dd><input class=\"span2\" type=\"text\" value=\"$v\" /><input ukey=\"lord_user_info_$uid.$k\" type=\"button\" value=\"修改\" class=\"btn2\" /></dd></dl>";
	}
}
if ( $online && $online["lord_user_task_$uid"] ) {
	echo "<input ukey=\"lord_user_task_$uid\" type=\"button\" value=\"清理\" class=\"btn2\" style=\"float:right;clear:left;\" /><h4>用户内置任务数据 lord_user_task_$uid</h4>";
	foreach ( $online["lord_user_task_$uid"] as $k => $v ) {
		if ( is_array($v) ) continue;
		echo "<dl><dt>$k :</dt><dd><input class=\"span2\" type=\"text\" value=\"$v\" /><input ukey=\"lord_user_task_$uid.$k\" type=\"button\" value=\"修改\" class=\"btn2\" /></dd></dl>";
	}
}
if ( $online && $online["lord_user_tesk_$uid"] ) {
	echo "<input ukey=\"lord_user_tesk_$uid\" type=\"button\" value=\"清理\" class=\"btn2\" style=\"float:right;clear:left;\" /><h4>用户动态任务数据 lord_user_tesk_$uid</h4>";
	foreach ( $online["lord_user_tesk_$uid"] as $k => $v ) {
		if ( is_array($v) ) continue;
		echo "<dl><dt>$k :</dt><dd><input class=\"span2\" type=\"text\" value=\"$v\" /><input ukey=\"lord_user_tesk_$uid.$k\" type=\"button\" value=\"修改\" class=\"btn2\" /></dd></dl>";
	}
}
?>

</div>
</body>
