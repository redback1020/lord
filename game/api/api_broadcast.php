<?php

//head
$redis = getRedis(); //comment it if not need
// $mysql = getMysql(); //comment it if not need
//base
$time = time();
$date = date("Y-m-d H:i:s");
$dateid = intval(date("Ymd"));
$weekid = intval(date("Ymd", time()-(date("N")-1)*86400));
//params
$types= array("add");
$type = isset($_REQUEST['type']) ? trim($_REQUEST['type']) : '';
if ( !in_array($type, $types) ) {
	writelog('[error] request='.json_encode($_REQUEST));
	echo json_encode(array('errno'=>11,'error'=>"type=$type faild. add"));
	exit;
}
$msg = isset($_REQUEST['msg']) ? trim($_REQUEST['msg']) : '';
if ( !$msg ) {
	writelog('[error] request='.json_encode($_REQUEST));
	echo json_encode(array('errno'=>12,'error'=>"msg=$msg faild"));
	exit;
}
$level = isset($_REQUEST['level']) ? intval($_REQUEST['level']) : 1;
//hosts
$hosts = getHosts($redis);
if ( !$hosts || !is_array($hosts) )
{
	writelog('[error] host='.json_encode($hosts));
	echo json_encode(array('errno'=>13,'error'=>"no server running, or redis connect faild"));
	exit;
}
//execute
$key_api_task = "lord_api_task";
switch ( $type ) {
	case 'add':
		$data = array(
			"act" => "API_BROADCAST",
			"data" => array(
				'msg' => $msg,
				'level' => $level,
				//
			),
		);
		$redis->ladd($key_api_task, $data);
		break;
	default:
		break;
}
//respond
writelog('[itsok] '.json_encode($_REQUEST));
echo json_encode(array('errno'=>0,'error'=>"done"));
exit;

