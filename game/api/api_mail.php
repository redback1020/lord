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
$types= array("add");//
$type = isset($_REQUEST['type']) ? trim($_REQUEST['type']) : '';
if ( !in_array($type, $types) ) {
	writelog('[error] request='.json_encode($_REQUEST));
	echo json_encode(array('errno'=>11,'error'=>"type=$type faild. add"));
	exit;
}
$data = isset($_REQUEST['data']) ? json_decode($_REQUEST['data'],1) : array();
if ( !$data ) {
	writelog('[error] request='.json_encode($_REQUEST));
	echo json_encode(array('errno'=>12,'error'=>"data=".json_encode($data)." faild"));
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
$key_queue_push = "lord_queue_push";
switch ( $type ) {
	case 'add':
		foreach ( $data as $k => $v )
		{
			$queue = array(
				'type' => 'mail',
				'uid' => intval($v['uid']),
				'id' => intval($v['id']),
		 		'subject' => $v['subject'],
		 		'content' => $v['content'],
		 		'items' => intval($v['items']),
		 		'fileid' => intval($v['fileid']),
		 		'is_read' => intval($v['is_read']),
		 		'sort' => intval($v['sort']),
			);
			$redis->ladd($key_queue_push, $queue);
		}
		break;
	default:
		break;
}
//respond
writelog('[itsok] '.json_encode($_REQUEST));
echo json_encode(array('errno'=>0,'error'=>"done"));
exit;

