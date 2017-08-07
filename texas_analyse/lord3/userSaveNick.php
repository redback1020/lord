<link rel="stylesheet" type="text/css" href="../bootstrap/css/bootstrap.min.css" />

<?php

require_once '../include/database.class.php';

$type = trim($_POST['type']);
$val = str_replace(array("$","*"),"",trim($_POST['val']));
$time = time();
if($_POST['isCoolNum'] == 1){
	$sql = "select uid from lord_game_user where cool_num = '".$_POST['uid']."'";
	$row = $db -> query($sql) -> fetch();
	$uid = intval($row['uid']);
}else{
	$uid = intval($_POST['uid']);
}

$sql ="UPDATE `lord_game_user` SET `nick` = '".$val."' WHERE `uid` =".$uid;
$rs = $pdo->getDB(1) -> exec($sql);
if($rs==1){
	$msg='修改成功！';
}
else{
	$msg='修改昵称失败，或者重复修改。';
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
