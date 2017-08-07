<link rel="stylesheet" type="text/css" href="../bootstrap/css/bootstrap.min.css" />
<?php
header("Content-type: text/html; charset=utf-8");
require_once '../manage/checkPriv.php';
require_once '../include/database.class.php';
//security
$api = isset($_REQUEST['api']) ? trim($_REQUEST['api']) : 'useradd';//
$type = isset($_REQUEST['type']) ? trim($_REQUEST['type']) : 'coupon';//
if($_REQUEST['isCoolNum'] == 1){
	$sql = "select uid from lord_game_user where cool_num = '".$_REQUEST['uid']."'";
	$row = $db->query($sql)->fetch();
	$uid = intval($row['uid']);
}else{
	$uid = intval($_REQUEST['uid']);
}
$val = intval($_REQUEST['val']);
$res = apiGet($api, $type, array('uid'=>$uid, 'val'=>$val));
if ( $res ) {
	$res_ = json_decode($res, 1);
	if ( $res_ ) {
		if ( $res_['errno'] ) {
			$msg=$val.'乐券赠送失败'.$res_['errno'].': '.$res_['error'];
		} else {
			$msg=$val.'乐券赠送成功！';
		}
	} else {
		$msg=$val.'乐券赠送失败8: '.$res;
	}
} else {
	$msg=$val.'乐券赠送失败9: 请联系技术部';
}
?>
<body>
	<div style="padding:8px 10px;">
		<fieldset>
			<legend>系统提示</legend>
			<div>
				<?=$msg?> <a href="userEditCoupon.php" style="font-size:20px;">返回</a>继续操作
			</div>
		</fieldset>
	</div>
</body>
