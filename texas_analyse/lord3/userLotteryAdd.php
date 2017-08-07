<?php

require_once '../manage/checkPriv.php';
require_once '../include/database.class.php';

$uid = intval($_REQUEST['uid']);
if ( !$uid ) {
	echo json_encode(array('result'=>'有多个重复编号ID，或者没有用户UID'));
	exit;
}

$sql = "update lord_game_user set lottery = lottery + 1 where uid = $uid";
$res = $pdo->getDB(1)->query($sql);
if ( !$res ) {
	echo json_encode(array('result'=>'执行失败，请稍后重试，或者找管理员帮助1'));
	exit;
}
$sql = "select lottery from lord_game_user where uid = $uid";
$res = $db->query($sql)->fetch();
if ( !$res ) {
	echo json_encode(array('result'=>'执行失败，请稍后重试，或者找管理员帮助2'));
	exit;
}
echo json_encode(array('result'=>'执行成功，用户当前抽奖数为'.$res['lottery']));
exit;
