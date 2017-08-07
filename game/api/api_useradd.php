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
$types= array('coins','coupon','lottery');//'gold',
$type = isset($_REQUEST['type']) ? trim($_REQUEST['type']) : '';
if ( !in_array($type, $types) ) {
	writelog('[error] request='.json_encode($_REQUEST));
	echo json_encode(array('errno'=>11,'error'=>"type=$type faild. ".json_encode($types)));
	exit;
}
$uid = isset($_REQUEST['uid']) ? intval($_REQUEST['uid']) : 0;
if ( !$uid ) {
	writelog('[error] request='.json_encode($_REQUEST));
	echo json_encode(array('errno'=>12,'error'=>"uid=$uid faild."));
	exit;
}
$val = isset($_REQUEST['val']) ? intval($_REQUEST['val']) : 0;
if ( !$val ) {
	writelog('[error] request='.json_encode($_REQUEST));
	echo json_encode(array('errno'=>12,'error'=>"val=$val faild."));
	exit;
}
$from = isset($_REQUEST['from']) ? trim($_REQUEST['from']) : '';
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
$data = array(
	"act" => "API_USERADD",
	"data" => array(
		'uid' => $uid,
		'val' => $val,
		'col' => $type,
		'from'=> $from,
	),
);
$redis->ladd($key_api_task, $data);
//respond
writelog('[itsok] '.json_encode($_REQUEST));
echo json_encode(array('errno'=>0,'error'=>"done"));
exit;
