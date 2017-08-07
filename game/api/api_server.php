<?php

//head
$redis = getRedis(); //comment it if not need
// $mysql = getMysql(); //comment it if not need
//base
$time = time();
$date = date("Y-m-d H:i:s");
$dateid = intval(date("Ymd"));
$weekid = intval(date("Ymd", time()-(date("N")-1)*86400));
//types
$types= array("reload");//
$type = isset($_REQUEST['type']) ? trim($_REQUEST['type']) : '';
if ( !in_array($type, $types) ) {
	writelog('[error] request='.json_encode($_REQUEST));
	echo json_encode(array('errno'=>11,'error'=>"type=$type faild. ".json_encode($types)));
	exit;
}
//hosts
$hosts = getHosts($redis);
if ( !$hosts || !is_array($hosts) )
{
	writelog('[error] host='.json_encode($hosts));
	echo json_encode(array('errno'=>12,'error'=>"no server running, or redis connect faild."));
	exit;
}
//params
$delay = isset($_REQUEST['delay']) ? intval($_REQUEST['delay']) : 0;
//execute
$key_api_task = "lord_api_task";
switch ( $type ) {
	case 'reload':
		$data = array(
			"act" => "API_SRVRELOAD",
			"data" => array(
				'delay' => $delay,
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

