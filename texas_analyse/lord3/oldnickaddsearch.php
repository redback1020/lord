<?php
require_once '../manage/checkPriv.php';
require_once '../include/database.class.php';
$pageIndex = 10*$_REQUEST['pageIndex'];
$pageSize = $_REQUEST['pageSize'];
$nick = trim($_REQUEST['nick']);
$cool_num = intval($_REQUEST['cool_num']);

if (!$nick || !$cool_num) {
	$array['data'] = array();
	echo json_encode($array);
	exit();
}

$sql = "select cool_num from lord_game_user where nick='$nick'";
$data = $db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
$data = $data ? $data : array();
$cool_nums = array();
foreach ( $data as $k => $v )
{
	$cool_nums[]=$v['cool_num'];
}
$cool_nums[]= $cool_num;

$sql = "select u.cool_num,u.coins,a.login,a.matches,a.win,u.coupon,u.nick,a.add_time,a.last_login from lord_game_analyse a left join lord_game_user u on a.uid = u.uid where u.cool_num in (".join(',', $cool_nums).")";
//var_dump($sql);
$res = $db -> query($sql)-> fetchAll(PDO::FETCH_ASSOC);
$res = $res ? $res : array();
if ($res) {
	$res_ = $res;
	$res = array();
	foreach ( $cool_nums as $k => $v )
	{
		foreach ($res_ as $key => $value) {
			if ( $value['cool_num'] == $v ) {
				$res[] = $value;
			}
		}
	}
}

$array['data'] = $res;
echo json_encode($array);


?>