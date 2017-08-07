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
$types= array("add", "modify", "delete", "close");
$type = isset($_REQUEST['type']) ? trim($_REQUEST['type']) : '';
if ( !in_array($type, $types) ) {
	writelog('[error] request='.json_encode($_REQUEST));
	echo json_encode(array('errno'=>11,'error'=>"type=$type faild. add/modify/delete/close"));
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
$key_list_ = "lord_list_room";
switch ( $type ) {
	case 'delete':
		$redis->hdel($key_list_, $id);
		break;
	case 'close':
		$info = $redis->hget($key_list_, $id);
		if ( $info ) {
			$info['isOpen'] = 0;
		}
		$redis->hset($key_list_, $id, $info);
		break;
	default:
		$sql = "SELECT * FROM `lord_game_room` WHERE `roomId` = $id";
		$data = $mysql->getLine($sql);
		if ( !$data ) {
			writelog('[error] sql='.$sql.' data='.json_encode($data));
			echo json_encode(array('errno'=>14,'error'=>"id=$id no data in db"));
			exit;
		}
		$info = array(
			'isOpen'=>intval($data['isOpen']),
			'isMobi'=>intval($data['isMobi']),
			'verMin'=>intval($data['verMin']),
			'modelId'=>intval($data['modelId']),
			'mode'=>trim($data['mode']),
			'roomId'=>intval($data['roomId']),
			'room'=>trim($data['room']),
			'name'=>trim($data['name']),
			'showRules'=>$data['showRules'] && ($tmp = json_decode($data['showRules'],1)) ? $tmp : array(),
			'baseCoins' => intval($data['baseCoins']),
			'rate' => intval($data['rate']),
			'rateMax' => intval($data['rateMax']),
			'limitCoins' => intval($data['limitCoins']),
			'rake' => intval($data['rake']),
			'enter' => trim($data['enter']),
			'enterLimit' => intval($data['enterLimit']),
			'enterLimit_' => intval($data['enterLimit_']),
			'gameBombAdd' => intval($data['gameBombAdd']),
			'brief' => trim($data['brief']),
			'entry' => trim($data['entry']),
			'tips' => trim($data['tips']),
			'rules' => trim($data['rules']),
			'start' => intval($data['start']),
			'entryMoney' => trim($data['entryMoney']),
			'entryCost'=>intval($data['entryCost']),
			'entryTime'=>intval($data['entryTime']),
			'entryOut'=>intval($data['entryOut']),
			'entryOsec'=>intval($data['entryOsec']),
			'entryOmax'=>intval($data['entryOmax']),
			'entryMax'=>intval($data['entryMax']),
			'entryMin'=>intval($data['entryMin']),
			'entryFull'=>intval($data['entryFull']),
			'entryMore'=>intval($data['entryMore']),
			'entryLess'=>intval($data['entryLess']),
			'scoreInit'=>intval($data['scoreInit']),
			'scoreRate'=>$data['scoreRate']+0,
			'rankRule'=>intval($data['rankRule']),
			'tableRule'=>intval($data['tableRule']),
			'outRule'=>intval($data['outRule']),
			'outValue'=>trim($data['outValue']),
			'awardRule'=>$data['awardRule'] && ($tmp = json_decode($data['awardRule'],1)) ? $tmp : array(),
			'apkurl'=>$data['apkurl'],
			'isForce'=>intval($data['isForce']),
			'appid'=>intval($data['appid']),
			'ver'=>$data['ver'],
			'vercode'=>intval($data['vercode']),
			'bytes'=>$data['bytes']+0,
			'desc'=>trim($data['desc']),
			'md5'=>$data['md5'],
			'package'=>$data['package'],
			'sort'=>intval($data['sort']),
		);
		$redis->hset($key_list_, $id, $info);
		break;
}
//respond
writelog('[itsok] '.json_encode($_REQUEST));
echo json_encode(array('errno'=>0,'error'=>"done"));
exit;
