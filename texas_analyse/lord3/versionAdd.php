<?php
header("Content-type: text/html; charset=utf-8");
// ini_set("display_errors","On");error_reporting(E_ALL);//E_ERROR | E_WARNING | E_PARSE
require_once '../manage/checkPriv.php';
require_once '../include/database.class.php';
$ut_now = time();
$type = isset($_REQUEST['type']) ? trim($_REQUEST['type']) : 'version';//version verfile vertips verconf
switch ($type) {
	case 'verfile':
		$table = "lord_game_file";
		break;
	case 'vertips':
		$table = "lord_game_tips";
		break;
	case 'verconf':
		$table = "lord_game_conf";
		break;
	default://version
		$table = "lord_game_sion";
		break;
}
//取待发布的版本号
$sql = "SELECT max(`version`) as version FROM `lord_game_version` WHERE `name` = '{$type}' AND `is_done` = 0 LIMIT 1";
$res = $db->query($sql)->fetch();
$version = $res ? $res['version'] : 1;
$errno = 0; $error = "";
// $db->setAttribute(PDO::ATTR_AUTOCOMMIT, 0);//关闭自动
// try(
// 	$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);//开启异常处理
// 	$db->beginTransaction();//开启事务
	//设置版本发布状态
	$sql = "UPDATE `lord_game_version` SET `end_time` = $ut_now, `comments`= '发布', `is_done` = 1 WHERE `name` = '{$type}' AND `version` = $version AND `is_done` = 0";
	$res1 = $pdo->getDB(1)->exec($sql);
	// if(!$res) {throw new Exception("设置版本为发布状态时失败.");}
	//新增待发布的版本号
	$sql = "INSERT INTO `lord_game_version` (`name`, `version`, `start_time`, `end_time`, `comments`, `is_done`) VALUES ('{$type}', ".($version+1).", ".($ut_now+1).", 0, '', 0)";
	$res2 = $pdo->getDB(1)->exec($sql);
	// if(!$res) {throw new Exception("新增待发布的版本号时失败.");}
	//刚发布的版本下的数据更新一下
	$sql = "UPDATE `$table` SET `update_time` = $ut_now WHERE `name` = '{$type}' AND `version` = $version";
	$res3 = $pdo->getDB(1)->exec($sql);
	// if(!$res) {throw new Exception("刚发布的版本下的数据更新一下时失败.");}
// 	$db->commit();
// ) catch (Exception $e) {
// 	$errno = 1; $error = $e->getMessage();
// 	$db->rollback();
// }
// $db->setAttribute(PDO::ATTR_AUTOCOMMIT, 1);//开启自动
$sql = "SELECT max(`version`) as version FROM `lord_game_version` WHERE `name` = '{$type}' AND `is_done` = 0 LIMIT 1";
$res = $db->query($sql)->fetch();
$version = $res ? intval($res['version']) : 1;
if ( !$res1 || !$res2 ) {
	echo json_encode(array('errno'=>1, 'error'=>"操作失败[$res1][$res2][$res3]。", 'maxversion'=>$version));
	exit;
}
$apiname = 'version';//
$apitype = $type;//
$res = apiGet($apiname, $apitype, array('version'=>$version));
echo json_encode($res);
exit;
