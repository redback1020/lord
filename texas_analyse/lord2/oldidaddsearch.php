<?php
require_once '../manage/checkPriv.php';
require_once '../include/database.class.php';
$pageIndex = 10*$_REQUEST['pageIndex'];
$pageSize = $_REQUEST['pageSize'];
$cool_num1 = intval($_REQUEST['cool_num1']);
$cool_num2 = intval($_REQUEST['cool_num2']);

if (!$cool_num1 || !$cool_num2) {
	$array['data'] = array();
	echo json_encode($array);
	exit();
}

$cool_nums = array($cool_num2, $cool_num1);

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
