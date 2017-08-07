<?php

//head
$redis = getRedis(); //comment it if not need
$mysql = getMysql(); //comment it if not need
//base
$time = time();
$date = date("Y-m-d H:i:s");
$dateid = intval(date("Ymd"));
$weekid = intval(date("Ymd", time()-(date("N")-1)*86400));
//params
$types= array("add", "modify", "delete");
$type = isset($_REQUEST['type']) ? trim($_REQUEST['type']) : '';
if ( !in_array($type, $types) ) {
	writelog('[error] request='.json_encode($_REQUEST));
	echo json_encode(array('errno'=>2,'error'=>"type=$type faild. add/modify/delete"));
	exit;
}
$id = isset($_REQUEST['id']) ? intval($_REQUEST['id']) : 0;
if ( !$id ) {
	writelog('[error] request='.json_encode($_REQUEST));
	echo json_encode(array('errno'=>1,'error'=>"id=$id faild"));
	exit;
}
//hosts
$hosts = getHosts($redis);
if ( !$hosts || !is_array($hosts) )
{
	writelog('[error] host='.json_encode($hosts));
	echo json_encode(array('errno'=>3,'error'=>"no server running, or redis connect faild"));
	exit;
}
//execute
$key_list_file = "lord_list_file";
switch ( $type ) {
	case 'delete':
		$redis->hdel($lord_list_file, $id);
		break;
	default:
		$sql = "SELECT * FROM `lord_game_file` WHERE `id` = $id";
		$data = $mysql->getLine($sql);
		if ( ! $data ) {
			writelog('[error] sql='.$sql.' data='.json_encode($data));
			echo json_encode(array('errno'=>4,'error'=>"id=$id no data in db"));
			exit;
		}
		$data['channel'] = $data['channel'] ? explode(' ', $data['channel']) : array();
		$data['channot'] = $data['channot'] ? explode(' ', $data['channot']) : array();
		$redis->hset($key_list_file, $id, $data);
		break;
}
//respond
writelog('[itsok] '.json_encode($_REQUEST));
echo json_encode(array('errno'=>0,'error'=>"done"));
exit;
