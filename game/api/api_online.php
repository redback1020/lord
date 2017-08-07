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
$types= array('getuser', 'modify', 'delete');
$type = isset($_REQUEST['type']) ? trim($_REQUEST['type']) : '';
if ( !in_array($type, $types) ) {
	writelog('[error] '.__LINE__.' request='.json_encode($_REQUEST));
	echo json_encode(array('errno'=>11,'error'=>"type=$type faild. ".json_encode($types)));
	exit;
}
$uid = isset($_REQUEST['uid']) ? intval($_REQUEST['uid']) : 0;
if ( !$uid ) {
	writelog('[error] '.__LINE__.' request='.json_encode($_REQUEST));
	echo json_encode(array('errno'=>12,'error'=>"uid=$uid faild."));
	exit;
}
$ukey = isset($_REQUEST['ukey']) ? trim($_REQUEST['ukey']) : '';
if ( ( $type == 'delete' && !$ukey ) || ( $type == 'modify' && !strpos($ukey, '.') ) ) {
	writelog('[error] '.__LINE__.' request='.json_encode($_REQUEST));
	echo json_encode(array('errno'=>12,'error'=>"type=$type ukey=$ukey faild."));
	exit;
}
$uval = isset($_REQUEST['uval']) ? $_REQUEST['uval'] : '';
if ( $type == 'modify' && !isset($_REQUEST['uval']) ) {
	writelog('[error] '.__LINE__.' request='.json_encode($_REQUEST));
	echo json_encode(array('errno'=>12,'error'=>"type=$type uval=$uval faild."));
	exit;
}
//hosts
$hosts = getHosts($redis);
if ( !$hosts || !is_array($hosts) )
{
	writelog('[error] '.__LINE__.' host='.json_encode($hosts));
	echo json_encode(array('errno'=>13,'error'=>"no server running, or redis connect faild"));
	exit;
}
//execute
switch ( $type ) {
	case 'modify':
		$k_ = explode('.', $ukey);
		if ( is_numeric($uval) ) $uval += 0;
		$res = $redis->hset($k_[0], $k_[1], $uval);
		if ( $res ) {
			$user = array();
		} else {
			writelog('[error] '.__LINE__.' request='.json_encode($_REQUEST));
			echo json_encode(array('errno'=>14,'error'=>"redis query faild."));
			exit;
		}
	break;
	case 'delete':
		$res = $redis->del($ukey);
		if ( $res ) {
			$user = array();
		} else {
			writelog('[error] '.__LINE__.' request='.json_encode($_REQUEST));
			echo json_encode(array('errno'=>14,'error'=>"redis query faild."));
			exit;
		}
	break;
	default://getuser
		$ukey = 'lord_user_info_'.$uid;
		$res = $redis->hgetall($ukey);
		$user[$ukey] = $res ? $res : array();
		$ukey = 'lord_user_task_'.$uid;
		$res = $redis->hgetall($ukey);
		$user[$ukey] = $res ? $res : array();
		$ukey = 'lord_user_tesk_'.$uid;
		$res = $redis->hgetall($ukey);
		$user[$ukey] = $res ? $res : array();
	break;
}
//respond
writelog('[itsok] '.json_encode($_REQUEST));
echo json_encode(array('errno'=>0,'error'=>"done",'data'=>$user));
exit;
