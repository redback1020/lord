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
$types= array("add", "modify", "delete", "offline");
$type = isset($_REQUEST['type']) ? trim($_REQUEST['type']) : '';
if ( !in_array($type, $types) ) {
	writelog('[error] request='.json_encode($_REQUEST));
	echo json_encode(array('errno'=>11,'error'=>"type=$type faild. add/modify/delete/offline"));
	exit;
}
$id = isset($_REQUEST['id']) ? intval($_REQUEST['id']) : 0;
if ( !$id ) {
	writelog('[error] request='.json_encode($_REQUEST));
	echo json_encode(array('errno'=>12,'error'=>"id=$id faild"));
	exit;
}
//hosts
$hosts = getHosts($redis);
if ( !$hosts || !is_array($hosts) )
{
	writelog('[error] host='.json_encode($hosts));
	echo json_encode(array('errno'=>13,'error'=>"no server running, or redis connect faild"));
	exit;
}
//execute
$key_list_topic = "lord_list_topic";
switch ( $type ) {
	case 'delete':
		$redis->hdel($key_list_topic, $id);
		break;
	case 'offline':
		$redis->hdel($key_list_topic, $id);
		break;
	default:
		$sql = "SELECT * FROM `lord_game_topic` WHERE `id` = $id";
		$data = $mysql->getLine($sql);
		if ( !$data ) {
			writelog('[error] sql='.$sql.' data='.json_encode($data));
			echo json_encode(array('errno'=>14,'error'=>"id=$id no data in db"));
			exit;
		}
		$topic = array('id'=>intval($data['id']),'channel'=>$data['channel']?explode(' ',$data['channel']):array(),'channot'=>$data['channot']?explode(' ',$data['channot']):array(),'subject'=>trim($data['subject']),'content'=>trim($data['content']),'start_time'=>intval($data['start_time']),'end_time'=>intval($data['end_time']),'start_lobby'=>intval($data['start_lobby']),'end_lobby'=>intval($data['end_lobby']),'sort'=>intval($data['sort']));
		$redis->hset($key_list_topic, $id, $topic);
		break;
}
//respond
writelog('[itsok] '.json_encode($_REQUEST));
echo json_encode(array('errno'=>0,'error'=>"done"));
exit;
