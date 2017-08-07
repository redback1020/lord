<?php
header("Content-type: text/html; charset=utf-8");
require_once '../manage/checkPriv.php';
require_once '../include/database.class.php';
$ut_now = time();
$type = isset($_REQUEST['type']) ? trim($_REQUEST['type']) : 'add';//add delete modify
$ispost = isset($_REQUEST['ispost']) ? intval($_REQUEST['ispost']) : 0;
$id = isset($_REQUEST['id']) ? intval($_REQUEST['id']) : 0;
if ($type=='delete') {
	$sql = "DELETE FROM `lord_game_tesksource` WHERE `id` = $id";
	$res = $pdo->getDB(1)->exec($sql);
	$errno = 0; $error = "";
	if ( !$res ) {
		$errno = 1; $error = "操作失败。";
	}
	echo json_encode(array('errno'=>$errno, 'error'=>$error));
	exit;
}
$data = array();
if ($type=='modify') {
	$sql = "SELECT * FROM `lord_game_tesksource` WHERE `id` = $id";
	$data = $db->query($sql)->fetch(PDO::FETCH_ASSOC);
	if ( !$data ) {
		$errno = 1; $error = "操作失败。";
	}
}
if ($ispost) {
	$name = isset($_REQUEST['name'])?trim($_REQUEST['name']):'';
	$accode = isset($_REQUEST['accode'])?intval($_REQUEST['accode']):0;
	$action = isset($_REQUEST['action'])?trim($_REQUEST['action']):"";
	$acttag = isset($_REQUEST['acttag'])?trim($_REQUEST['acttag']):"";
	$acname = isset($_REQUEST['acname'])?trim($_REQUEST['acname']):"";
	$execut = $db->quote(isset($_REQUEST['execut'])?trim($_REQUEST['execut']):"");
	$condit = $db->quote(isset($_REQUEST['condit'])?trim($_REQUEST['condit']):"");
	$result = $db->quote(isset($_REQUEST['result'])?trim($_REQUEST['result']):"");
	$sort = isset($_REQUEST['sort'])&&intval($_REQUEST['sort'])?intval($_REQUEST['sort']):99;
	$create_time = $update_time = $ut_now;
	$errno = 0; $error = "";
	if ($type=='add') {
		// if (!$uids) {
		// 	$errno = 1; $error = "无效用户。";
		// 	$res = json_encode(array('errno'=>$errno, 'error'=>$error));
		// 	echo $res;
		// 	exit;
		// }
		$sql = "INSERT INTO `lord_game_tesksource` ";
		$sql.= "(`name`,`accode`,`action`,`acttag`,`acname`,`execut`,`condit`,`result`,`sort`,`create_time`,`update_time`) VALUES ";
		$sql.= "('$name',$accode,'$action','$acttag','$acname',$execut,$condit,$result,$sort,$create_time,$update_time)";
		$res = $pdo->getDB(1)->exec($sql);
		$res = $id = $pdo->getDB(1)->lastInsertId();
		if ( !$res ) {
			$errno = 1; $error = "操作失败。";
		} else {

		}
	}
	if ($type=='modify') {
		$sql = "UPDATE `lord_game_tesksource` SET `name`='$name',`accode`=$accode,`action`='$action',`acttag`='$acttag',`acname`='$acname',`execut`=$execut,`condit`=$condit,`result`=$result,`sort`=$sort,`update_time`=$update_time WHERE `id`=$id";
		$res = $pdo->getDB(1)->exec($sql);
		if ( !$res ) {
			$errno = 1; $error = "操作失败。";
		} else {
		}
	}
	$res = json_encode(array('errno'=>$errno, 'error'=>$error));
	if ( !$errno ) {
		header('Location: sourceList.php');
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
legend{margin-bottom: 10px!important;}
table.table{ font-size: 12px;width: 95%!important;}
table.table th{ white-space: nowrap;line-height:30px;width: 70px;font-size: 14px;}
label{display: inline;}
input[type="radio"], input[type="checkbox"] {margin: 0;}
select{margin:0;}
.in_t{width: 350px;height: 30px!important;margin: 0!important}
.in_t2{width: 50px;height: 30px!important;margin: 0!important}
.in_a{width: 350px;height: 100px!important;margin: 0!important}
</style>
<script>
$(function(){
	//
});
</script>

<body>
<div style="position:absolute;left:0;top:0;padding:0 10px;">

<fieldset>
	<legend>动态任务源码 - <?php if($type=='add'){?>创建<?php }else{?>修改<?php }?></legend>
	<form action="sourceAdd.php" method="post">
		<input type="hidden" name="ispost" value="1" />
		<input type="hidden" name="type" value="<?=$type?>" />
		<input type="hidden" name="id" value="<?php if($data){echo $data['id'];} ?>" />
		<table class="table table-bordered table-condensed table-hover">
			<tr>
				<th>温馨提示:</th>
				<td style="color:red;white-space:nowrap;font-size:14px;" colspan="2">
					所有新的动态任务发布，或旧的动态任务编辑修改，强烈建议在测试机上测试。当前没有时间做字段校验，请务必参照提示来输入。以后后台开发人来了，他会处理的。<br/>
					基本原理及步骤：<br/>
					1、依据 ACCODE、ACTION、ACTTAG 来判断是否触发任务。没有触发时，本任务结束；触发成功时，进入 执行逻辑 阶段<br/>
					2、依据 执行逻辑 的json规则(多条)，对本任务的所需数据(可以不是必需的)进行运算，用于下一步的 达成条件 阶段的判定运算<br/>
					3、依据 达成条件 的json规则(多条)，对上一步骤产生的数据，进行是否达成的判断。没有达成时，本任务结束；达成时，进入下一步的 达成结果 阶段<br/>
					4、依据 达成结果 的json规则(多条)，处理用户完成本任务后的结果数据。
				</td>
			</tr>
			<tr>
				<th>行为名称:</th>
				<td><input name="name" value="<?php if($data){echo $data['name'];} ?>" type="text" class="in_t" /></td>
				<td class="desc">在创建/编辑活动任务时，选择使用此源码</td>
			</tr>
			<tr>
				<th>ACCODE:</th>
				<td><input name="accode" value="<?php if($data){echo $data['accode'];} ?>" type="text" class="in_t" /></td>
				<td class="desc">=$cmd*10000+$code 。 为0则支持所有协议号</td>
			</tr>
			<tr>
				<th>ACTION:</th>
				<td><input name="action" value="<?php if($data){echo $data['action'];} ?>" type="text" class="in_t" /></td>
				<td class="desc">=__FUNCTION__，执行此任务的协议或函数名。为空则支持所有协议和函数</td>
			</tr>
			<tr>
				<th>ACTTAG:</th>
				<td><input name="acttag" value="<?php if($data){echo $data['acttag'];} ?>" type="text" class="in_t" /></td>
				<td class="desc">=TAGNAME，用户行为的字符标记。不可为空</td>
			</tr>
			<tr>
				<th>行为统称:</th>
				<td><input name="acname" value="<?php if($data){echo $data['acname'];} ?>" type="text" class="in_t" /></td>
				<td class="desc">与ACTTAG对照，用于活动列表筛选。如ACTTAG为“user_login”时，只能为“用户登录”。会逐步做成下拉框</td>
			</tr>
			<tr>
				<th>执行逻辑:</th>
				<td><textarea name="execut" class="in_a"><?php if($data){echo $data['execut'];} ?></textarea></td>
				<td class="desc">
					1、{"key":"usertask.gold_all","exe":"+","par":"param"}--->参数直接运算模式，直接把par的值通过exe运算后赋值到key。<br />
					2、{"key":"usertesk.teskvalue","exe":"*p+p","par":"param"}--->模式同上。但十分适合0|1型的连续状态识别，比如识别连续输赢($param=[0|1])时。<br />
					3、{"key":"usertesk.teskvalue","exe":"keepdate","par":"param","ext":"usertesk.tesklast"}--->连续状态识别模式，当依据exe规则(见5见6)达成连续状态(见4)时，key+1<br />
					4、exe为连续数值比较时(见5),par与ext直接进行比较，来识别是否是连续状态；exe为连续时间比较时(见6)，par与ext依据时间周期类型进行间接比较。
					5、keep=:连续等于 | keep>=:连续大于等于 | keep>...
					6、keepdate:连续日期 | keepweek:连续星期 | keepmonth:连续月份 | keephour:连续小时
				</td>
			</tr>
			<tr>
				<th>达成条件:</th>
				<td><textarea name="condit" class="in_a"><?php if($data){echo $data['condit'];} ?></textarea></td>
				<td class="desc">
					1、{"key":"usertask.gold_all","leg":"ge","par":10}--->此用户本周期内总充值>=10<br />
					2、{"key":"usertesk.gold_day","leg":"&lt;","par":"usertesk.cost_day"}--->此用户今天充的少花的多
				</td>
			</tr>
			<tr>
				<th>达成结果:</th>
				<td><textarea name="result" class="in_a"><?php if($data){echo $data['result'];} ?></textarea></td>
				<td class="desc">
					{"key":"usertesk.teskdone","exe":"=","par":0}--->任务在本周期内可重复完成<br />
					{"key":"usertesk.teskpid","exe":"+","par":2}--->任务在下下个周期才可以再次参与<br />
					{"key":"usertesk.teskvalue","exe":"+p*e","par":-1,"ext":1000}--->任务在本周期内完成后的下一次的完成值在当前值的基础上降低1000点<br />
					{"key":"usertesk.tesktimes"}--->任务在本周期内完成后，任务总完成次数+1<br />
					{"key":"userinfo.times"}--->任务在本周期内完成后，userinfo的times值(如果有的话)+1，在这里不用管这个值变化导致的影响
				</td>
			</tr>
			<tr>
				<th>排序权重:</th>
				<td><input name="sort" value="<?php if($data){echo $data['sort'];} ?>" type="text" class="in_t2" /></td>
				<td class="desc">越小越靠上，默认99</td>
			</tr>
			<tr>
				<td>&nbsp;</td>
				<td class="span1" colspan="2"><input type="submit" value="提交" class="btn" />！任务源码修改后，必须对使用此源码的任务编辑更新！</td>
			</tr>
		</table>
	</form>
</fieldset>

</div>
</body>
