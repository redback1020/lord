<link rel="stylesheet" type="text/css" href="../bootstrap/css/bootstrap.min.css" />
<?php
header("Content-type: text/html; charset=utf-8");
require_once '../manage/checkPriv.php';
require_once '../include/database.class.php';
$types = array('coins'=>'乐豆', 'coupon'=>'乐券', 'lottery'=>'抽奖数');
//security
$api = isset($_REQUEST['api']) ? trim($_REQUEST['api']) : 'useradd';//
$type = isset($_REQUEST['type']) ? trim($_REQUEST['type']) : '';//
if($_REQUEST['isCoolNum'] == 1){
	$sql = "select uid from lord_game_user where cool_num = '".$_REQUEST['uid']."'";
	$row = $db->query($sql)->fetch();
	$uid = intval($row['uid']);
}else{
	$uid = intval($_REQUEST['uid']);
}
$val = intval($_REQUEST['val']);
$res = apiGet($api, $type, array('uid'=>$uid, 'val'=>$val));
if ( $res['errno'] ) {
	$msg=$val.$types[$type].'赠扣失败'.$res['errno'].': '.$res['error'];
} else {
	$msg=$val.$types[$type].'赠扣成功！';
}
?>
<body>
	<div style="padding:8px 10px;">
		<fieldset>
			<legend>系统提示</legend>
			<div>
				<?=$msg?> <a href="userDataAdd.php" style="font-size:20px;">返回继续操作</a>
			</div>
		</fieldset>
	</div>
</body>
