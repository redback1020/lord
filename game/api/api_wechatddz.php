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
$types= array('bind', 'unbind', 'userInfo', 'updateUserName', 'updateUserPassword', 'getReward', 'userDevice', 'userCheck', 'userData', 'userModifyPassword', 'userModifyUsername', 'userAddMoney');
$type = isset($_REQUEST['type']) ? trim($_REQUEST['type']) : '';
if ( !in_array($type, $types) ) {
	writelog('[error] '.__LINE__.' errno=11 request='.json_encode($_REQUEST));
	echo json_encode(array('errno'=>11,'error'=>"无效操作。"));
	exit;
}
//微信ID 不再需要
$open_id = isset($_REQUEST['openId']) ? trim($_REQUEST['openId']) : '';
// if ( !$open_id ) {
// 	writelog('[error] '.__LINE__.' errno=12 request='.json_encode($_REQUEST));
// 	echo json_encode(array('errno'=>12,'error'=>"无效操作。"));
// 	exit;
// }
//hosts
$hosts = getHosts($redis);
if ( !$hosts || !is_array($hosts) )
{
	writelog('[error] '.__LINE__.' errno=13 request='.json_encode($_REQUEST));
	echo json_encode(array('errno'=>13,'error'=>"操作失败，请稍候重试。"));
	exit;
}
if ( !function_exists('utf8substr') ) {
function utf8substr($str, $start, $len)
{
	$res = "";
	$strlen = $start + $len;
	for ($i = 0; $i < $strlen; $i++) {
		if ( ord(substr($str, $i, 1)) > 127 ) {
			$res.=substr($str, $i, 3);
			$i+=2;
		}
		else {
			$res.= substr($str, $i, 1);
		}
	}
	return $res;
}
}
//execute
switch ( $type ) {
	case 'bind':
		$sql = "SELECT * FROM `lord_wechat_binding_log` WHERE `open_id` = '$open_id'";
		$bindlog = $mysql->getLine($sql);
		if ( $bindlog ) {
			writelog('[error] '.__LINE__.' errno=14 request='.json_encode($_REQUEST));
			echo json_encode(array('errno'=>14,'error'=>"操作失败，您已绑定过此微信。\n如果您没有进行过此操作，请联系官方客服。"));
			exit;
		}
		$cool_num = isset($_REQUEST['gameId']) ? intval($_REQUEST['gameId']) : 0;
		if ( !$cool_num ) {
			writelog('[error] '.__LINE__.' errno=15 request='.json_encode($_REQUEST));
			echo json_encode(array('errno'=>15,'error'=>"操作失败，错误的游戏ID。"));
			exit;
		}
		$check_code = isset($_REQUEST['wechatCode']) ? intval($_REQUEST['wechatCode']) : 0;
		if ( !$check_code ) {
			writelog('[error] '.__LINE__.' errno=16 request='.json_encode($_REQUEST));
			echo json_encode(array('errno'=>16,'error'=>"操作失败，错误的微信校验码。"));
			exit;
		}
		$sql = "SELECT `uid`, `nick`, `cool_num`, `check_code`, `wechat` FROM `lord_game_user` WHERE `cool_num` = $cool_num";
		$u = $mysql->getLine($sql);
		if ( !$u ) {
			writelog('[error] '.__LINE__.' errno=17 request='.json_encode($_REQUEST));
			echo json_encode(array('errno'=>17,'error'=>"操作失败，请稍候重试。"));
			exit;
		}
		if ( $u['check_code'] != $check_code ) {
			writelog('[error] '.__LINE__.' errno=18 request='.json_encode($_REQUEST));
			echo json_encode(array('errno'=>18,'error'=>"操作失败，您的游戏ID与微信校验码不匹配。"));
			exit;
		}
		$uid = intval($u['uid']);
		$nick = $u['nick'];
		$coins = mt_rand(3,3) * 1000;//绑定赠送3-3000奖励
		$userinfo = $redis->hgetall('lord_user_info_'.$uid);
		if ( $userinfo && isset($userinfo['coins']) ) {
			$redis->hincrby('lord_user_info_'.$uid, 'coins', $coins);
		}
		$sql = "UPDATE `lord_game_user` SET `wechat` = '$open_id', `coins` = `coins` + $coins WHERE `uid` = $uid";
		$res = $mysql->runSql($sql);
		if ( !$res ) {
			writelog('[error] '.__LINE__.' errno=19 request='.json_encode($_REQUEST));
			echo json_encode(array('errno'=>19,'error'=>"操作失败，请稍候重试。"));
			exit;
		}
		$sql = "INSERT INTO `lord_wechat_binding_log` (`dateid`,`open_id`,`poker_id`,`binding_time`) VALUES ($dateid,'$open_id',$uid,'$date')";
		$res = $mysql->runSql($sql);
		$res = array('errno'=>0, 'error'=>"绑定成功。获得{$coins}乐豆", 'username'=>$nick, 'amount'=>$coins);
	break;
	case 'unbind':
		$sql = "SELECT * FROM `lord_wechat_binding_log` WHERE `open_id` = '$open_id'";
		$bindlog = $mysql->getLine($sql);
		if ( !$bindlog ) {
			writelog('[error] '.__LINE__.' errno=14 request='.json_encode($_REQUEST));
			echo json_encode(array('errno'=>14,'error'=>"操作失败，您还没有绑定过此微信。\n如果您确实已经绑定，请联系官方客服。"));
			exit;
		}
		$id = intval($bindlog['id']);
		$uid = intval($bindlog['poker_id']);
		$sql = "UPDATE `lord_game_user` SET `wechat` = '' WHERE `uid` = $uid";
		$res = $mysql->runSql($sql);
		if ( !$res ) {
			writelog('[error] '.__LINE__.' errno=15 request='.json_encode($_REQUEST));
			echo json_encode(array('errno'=>15,'error'=>"操作失败，请稍候重试。"));
			exit;
		}
		$sql = "DELETE FROM `lord_wechat_binding_log` WHERE `id` = $id";
		$res = $mysql->runSql($sql);
		if ( !$res ) {
			writelog('[error] '.__LINE__.' errno=16 request='.json_encode($_REQUEST));
			echo json_encode(array('errno'=>16,'error'=>"操作失败，请稍候重试。"));
			exit;
		}
		$res = array('errno'=>0, 'error'=>"解除绑定成功");
	break;
	case 'userInfo':
		$sql = "SELECT * FROM `lord_wechat_binding_log` WHERE `open_id` = '$open_id'";
		$bindlog = $mysql->getLine($sql);
		if ( !$bindlog ) {
			// $sql = "SELECT `uid` FROM `lord_game_user` WHERE `wechat` = '$open_id'";
			// $uid = intval($mysql->getVar($sql));
			$uid = 0;
		} else {
			$uid = intval($bindlog['poker_id']);
		}
		if ( !$uid ) {
			writelog('[error] '.__LINE__.' errno=14 request='.json_encode($_REQUEST));
			echo json_encode(array('errno'=>14,'error'=>"查看失败，用户未绑定。"));
			exit;
		}
		$sql = "SELECT a.`uid`, a.`cool_num`, a.`nick`, a.`vip_lv`, a.`coins`, b.`open_id`, b.`extend` FROM `lord_game_user` a LEFT JOIN `user_login` b ON a.`uid` = b.`uid` WHERE a.`uid` = $uid";
		$u = $mysql->getLine($sql);
		if ( !$u ) {
			writelog('[error] '.__LINE__.' errno=15 request='.json_encode($_REQUEST));
			echo json_encode(array('errno'=>15,'error'=>"操作失败，请稍候重试。"));
			exit;
		}
		$u['device'] = $u['extend'] ? $u['extend'] : $u['open_id'];
		$res = array('errno'=>0, 'error'=>"查看成功。", 'uid'=>$u['uid'], 'gameId'=>$u['cool_num'], 'username'=>$u['nick'], 'device'=>$u['device'], 'amount'=>$u['coins']);
	break;
	case 'userDevice':
		$cool_num = isset($_REQUEST['gameId']) ? trim($_REQUEST['gameId']) : '';
		$sql = "SELECT * FROM `lord_game_user` WHERE `cool_num` = $cool_num";
		$user = $mysql->getLine($sql);
		if ( !$user ) {
			$uid = 0;
		} else {
			$uid = intval($user['uid']);
		}
		if ( !$uid ) {
			writelog('[error] '.__LINE__.' errno=14 request='.json_encode($_REQUEST));
			echo json_encode(array('errno'=>14,'error'=>"查找失败，此用户编号ID不存在。"));
			exit;
		}
		$sql = "SELECT a.`uid`, a.`cool_num`, a.`nick`, a.`vip_lv`, a.`coins`, b.`open_type`, b.`open_id`, b.`extend` FROM `lord_game_user` a LEFT JOIN `user_login` b ON a.`uid` = b.`uid` WHERE a.`uid` = $uid";
		$u = $mysql->getLine($sql);
		if ( !$u ) {
			writelog('[error] '.__LINE__.' errno=14 request='.json_encode($_REQUEST));
			echo json_encode(array('errno'=>14,'error'=>"查找失败，此用户编号ID不存在。"));
			exit;
		}
		$u['device'] = $u['extend'] ? $u['extend'] : $u['open_id'];
		$res = array('errno'=>0, 'error'=>"查找成功。", 'device'=>$u['device'], 'uid'=>$u['uid'], 'open_type'=>$u['open_type'], 'open_id'=>$u['open_id'], 'extend'=>$u['extend']);
	break;
	case 'updateUserName':
		$nick = isset($_REQUEST['username']) ? trim($_REQUEST['username']) : '';
		if ( !$nick ) {
			writelog('[error] '.__LINE__.' errno=15 request='.json_encode($_REQUEST));
			echo json_encode(array('errno'=>15,'error'=>"您的昵称无效，请重新输入。"));
			exit;
		}
		$nick = mysqli_real_escape_string($mysql->db, utf8substr($nick, 0, 21));//7个中文21个英文
		$sql = "SELECT * FROM `lord_wechat_binding_log` WHERE `open_id` = '$open_id'";
		$bindlog = $mysql->getLine($sql);
		if ( !$bindlog ) {
			// $sql = "SELECT `uid` FROM `lord_game_user` WHERE `wechat` = '$open_id'";
			// $uid = intval($mysql->getVar($sql));
			$uid = 0;
		} else {
			$uid = intval($bindlog['poker_id']);
		}
		if ( !$uid ) {
			writelog('[error] '.__LINE__.' errno=14 request='.json_encode($_REQUEST));
			echo json_encode(array('errno'=>14,'error'=>"修改昵称失败，用户未绑定。"));
			exit;
		}
		$sql = "UPDATE `lord_game_user` SET `nick` = '$nick' WHERE `uid` = $uid";
		$res = $mysql->runSql($sql);
		if ( !$res ) {
			writelog('[error] '.__LINE__.' errno=15 request='.json_encode($_REQUEST));
			echo json_encode(array('errno'=>15,'error'=>"修改昵称失败，请稍候重试。"));
			exit;
		}
		$res = array('errno'=>0, 'error'=>"昵称修改成功，重新登录游戏才能生效。");
	break;
	case 'updateUserPassword':
		$password = isset($_REQUEST['password']) ? mysqli_real_escape_string($mysql->db, trim($_REQUEST['password'])) : '';
		if ( !$password ) {
			writelog('[error] '.__LINE__.' errno=15 request='.json_encode($_REQUEST));
			echo json_encode(array('errno'=>15,'error'=>"您的密码无效，请重新输入。"));
			exit;
		}
		$sql = "SELECT * FROM `lord_wechat_binding_log` WHERE `open_id` = '$open_id'";
		$bindlog = $mysql->getLine($sql);
		if ( !$bindlog ) {
			// $sql = "SELECT `uid` FROM `lord_game_user` WHERE `wechat` = '$open_id'";
			// $uid = intval($mysql->getVar($sql));
			$uid = 0;
		} else {
			$uid = intval($bindlog['poker_id']);
		}
		if ( !$uid ) {
			writelog('[error] '.__LINE__.' errno=14 request='.json_encode($_REQUEST));
			echo json_encode(array('errno'=>14,'error'=>"修改密码失败，用户未绑定。"));
			exit;
		}
		$sql = "UPDATE `user_user` SET `password` = '$password' WHERE `id` = $uid";
		$res = $mysql->runSql($sql);
		if ( !$res ) {
			writelog('[error] '.__LINE__.' errno=16 request='.json_encode($_REQUEST));
			echo json_encode(array('errno'=>16,'error'=>"修改密码失败，请稍候重试。"));
			exit;
		}
		$res = array('errno'=>0, 'error'=>"密码修改成功。");
	break;
	case 'getReward':
		$sql = "SELECT * FROM `lord_wechat_binding_log` WHERE `open_id` = '$open_id'";
		$bindlog = $mysql->getLine($sql);
		if ( !$bindlog ) {
			// $sql = "SELECT `uid` FROM `lord_game_user` WHERE `wechat` = '$open_id'";
			// $uid = intval($mysql->getVar($sql));
			$uid = 0;
		} else {
			$uid = intval($bindlog['poker_id']);
		}
		if ( !$uid ) {
			writelog('[error] '.__LINE__.' errno=14 request='.json_encode($_REQUEST));
			echo json_encode(array('errno'=>14,'error'=>"签到失败，用户未绑定。"));
			exit;
		}
		if ( $dateid == intval($bindlog['checkin']) ) {
			writelog('[error] '.__LINE__.' errno=15 request='.json_encode($_REQUEST));
			echo json_encode(array('errno'=>15,'error'=>"签到失败，不能重复签到。"));
			exit;
		}
		$sql = "UPDATE `lord_wechat_binding_log` SET `checkin` = $dateid WHERE `open_id` = '$open_id'";
		$res = $mysql->runSql($sql);
		if ( !$res ) {
			writelog('[error] '.__LINE__.' errno=16 request='.json_encode($_REQUEST));
			echo json_encode(array('errno'=>16,'error'=>"签到失败，请稍候重试。"));
			exit;
		}
		$coins = mt_rand(1,3) * 1000;//随机赠送1-3000奖励
		$userinfo = $redis->hgetall('lord_user_info_'.$uid);
		if ( $userinfo && isset($userinfo['coins']) ) {
			$redis->hincrby('lord_user_info_'.$uid, 'coins', $coins);
		}
		$sql = "UPDATE `lord_game_user` SET `coins` = `coins` + $coins WHERE `uid` = $uid";
		$res = $mysql->runSql($sql);
		$sql = "INSERT INTO `lord_wechat_checkin_log` (`dateid`,`uid`,`coins`,`coupon`,`lottery`,`create_time`) VALUES ($dateid,$uid,$coins,0,0,$time)";
		$res = $mysql->runSql($sql);
		$res = array('errno'=>0, 'error'=>"签到成功，获得{$coins}乐豆", 'amount'=>$coins);
		break;
	case 'userCheck':
		$cool_num = isset($_REQUEST['gameId']) ? intval($_REQUEST['gameId']) : 0;
		$check_code = isset($_REQUEST['wechatCode']) ? intval($_REQUEST['wechatCode']) : 0;
		if ( $cool_num < 1 || $check_code < 1000 ) {
			writelog('[error] '.__LINE__.' errno=14 request='.json_encode($_REQUEST));
			echo json_encode(array('errno'=>14,'error'=>"操作失败。您的输入有误。"));
			exit;
		}
		$sql = "SELECT * FROM `lord_game_user` WHERE `cool_num` = $cool_num";
		$u = $mysql->getLine($sql);
		if ( !$u || !isset($u['check_code']) || $u['check_code'] != $check_code ) {
			writelog('[error] '.__LINE__.' errno=14 request='.json_encode($_REQUEST));
			echo json_encode(array('errno'=>14,'error'=>"操作失败。您的输入有误。"));
			exit;
		}
		$uid = $u['uid'];
		$sql = "SELECT `open_id`, `extend` FROM `user_login` WHERE `uid` = $uid";
		$a = $mysql->getLine($sql);
		if ( !$a ) {
			writelog('[error] '.__LINE__.' errno=14 request='.json_encode($_REQUEST));
			echo json_encode(array('errno'=>14,'error'=>"操作失败。请稍后重试。"));
			exit;
		}
		$u['device'] = $a['extend'] ? $a['extend'] : $a['open_id'];
		$res = array('errno'=>0, 'error'=>"操作成功。", 'uid'=>$u['uid'], 'gameId'=>$u['cool_num'], 'username'=>$u['nick'], 'device'=>$u['device'], 'coins'=>$u['coins'], 'coupon'=>$u['coupon'], 'lottery'=>$u['lottery']);
		break;
	case 'userData':
		$uid = isset($_REQUEST['uid']) ? intval($_REQUEST['uid']) : 0;
		if ( $uid < 1 ) {
			writelog('[error] '.__LINE__.' errno=15 request='.json_encode($_REQUEST));
			echo json_encode(array('errno'=>15,'error'=>"操作失败。参数有误。"));
			exit;
		}
		$sql = "SELECT * FROM `lord_game_user` WHERE `uid` = $uid";
		$u = $mysql->getLine($sql);
		if ( !$u ) {
			writelog('[error] '.__LINE__.' errno=16 request='.json_encode($_REQUEST));
			echo json_encode(array('errno'=>16,'error'=>"操作失败。请稍后重试。"));
			exit;
		}
		$uid = $u['uid'];
		$sql = "SELECT `open_id`, `extend` FROM `user_login` WHERE `uid` = $uid";
		$a = $mysql->getLine($sql);
		if ( !$a ) {
			writelog('[error] '.__LINE__.' errno=17 request='.json_encode($_REQUEST));
			echo json_encode(array('errno'=>17,'error'=>"操作失败。请稍后重试。"));
			exit;
		}
		$u['device'] = $a['extend'] ? $a['extend'] : $a['open_id'];
		$res = array('errno'=>0, 'error'=>"操作成功。", 'uid'=>$u['uid'], 'gameId'=>$u['cool_num'], 'username'=>$u['nick'], 'device'=>$u['device'], 'coins'=>$u['coins'], 'coupon'=>$u['coupon'], 'lottery'=>$u['lottery']);
		break;
	case 'userModifyPassword':
		$uid = isset($_REQUEST['uid']) ? intval($_REQUEST['uid']) : 0;
		$password = isset($_REQUEST['password']) ? mysqli_real_escape_string($mysql->db, trim($_REQUEST['password'])) : '';
		if ( !$uid || !$password ) {
			writelog('[error] '.__LINE__.' errno=14 request='.json_encode($_REQUEST));
			echo json_encode(array('errno'=>14,'error'=>"操作失败。您的输入有误。"));
			exit;
		}
		$sql = "UPDATE `user_user` SET `password` = '$password' WHERE `id` = $uid";
		$res = $mysql->runSql($sql);
		if ( !$res ) {
			writelog('[error] '.__LINE__.' errno=14 request='.json_encode($_REQUEST));
			echo json_encode(array('errno'=>14,'error'=>"操作失败。请稍后重试。"));
			exit;
		}
		$res = array('errno'=>0, 'error'=>"操作成功。");
		break;
	case 'userModifyUsername':
		$uid = isset($_REQUEST['uid']) ? intval($_REQUEST['uid']) : 0;
		$nick = $username = isset($_REQUEST['username']) ? trim($_REQUEST['username']) : '';
		if ( !$uid || !$nick ) {
			writelog('[error] '.__LINE__.' errno=14 request='.json_encode($_REQUEST));
			echo json_encode(array('errno'=>14,'error'=>"操作失败。您的输入有误。"));
			exit;
		}
		$nick = mysqli_real_escape_string($mysql->db, utf8substr($nick, 0, 21));//7个中文21个英文
		$sql = "SELECT * FROM `lord_game_user` WHERE `nick` = '$nick'";
		$u = $mysql->getLine($sql);
		if ( $u && $u['uid'] != $uid ) {
			$username = utf8substr($username,0,15).mt_rand(100000,999999);
			writelog('[error] '.__LINE__.' errno=14 request='.json_encode($_REQUEST));
			echo json_encode(array('errno'=>14,'error'=>"操作失败。昵称已被使用。\n推荐使用 $username",'recommend'=>$username));
			exit;
		}
		$sql = "UPDATE `lord_game_user` SET `nick` = '$nick' WHERE `uid` = $uid";
		$res = $mysql->runSql($sql);
		if ( !$res ) {
			writelog('[error] '.__LINE__.' errno=14 request='.json_encode($_REQUEST));
			echo json_encode(array('errno'=>14,'error'=>"操作失败。请稍后重试。"));
			exit;
		}
		$res = array('errno'=>0, 'error'=>"操作成功。请重新登录游戏。");
		break;
	case 'userAddMoney':
		$uid = isset($_REQUEST['uid']) ? intval($_REQUEST['uid']) : 0;
		$money['coins'] = isset($_REQUEST['coins']) ? intval($_REQUEST['coins']) : 0;
		$money['coupon'] = isset($_REQUEST['coupon']) ? intval($_REQUEST['coupon']) : 0;
		$money['lottery'] = isset($_REQUEST['lottery']) ? intval($_REQUEST['lottery']) : 0;
		if ( !$uid || !($money['coins'] || $money['coupon'] || $money['lottery']) ) {
			writelog('[error] '.__LINE__.' errno=14 request='.json_encode($_REQUEST));
			echo json_encode(array('errno'=>15,'error'=>"操作失败。参数有误。"));
			exit;
		}
		$hosts = getHosts($redis);
		if ( !$hosts || !is_array($hosts) )
		{
			writelog('[error] '.__LINE__.' errno=15 host='.json_encode($hosts));
			echo json_encode(array('errno'=>16,'error'=>"操作失败。请稍后重试。"));
			exit;
		}
		$key_api_task = "lord_api_task";
		foreach ( $money as $col => $val ) {
			if ( !$val ) continue;
			$task = array( "act" => "API_USERADD", "data" => array( 'uid' => $uid, 'col' => $col, 'val' => $val, 'from'=>'wechat' ) );
			$redis->ladd($key_api_task, $task);
		}
		$res = array('errno'=>0, 'error'=>"操作成功。请稍候在游戏中查看。");
		break;
	default://
		// $ukey = 'lord_user_info_'.$uid;
		// $res = $redis->hgetall($ukey);
		// $user[$ukey] = $res ? $res : array();
		// $ukey = 'lord_user_task_'.$uid;
		// $res = $redis->hgetall($ukey);
		// $user[$ukey] = $res ? $res : array();
		// $ukey = 'lord_user_tesk_'.$uid;
		// $res = $redis->hgetall($ukey);
		// $user[$ukey] = $res ? $res : array();
	break;
}

// 重复绑定，可多次获得乐豆。（是个bug)，应改成绑定多次，也只给一次。 解绑后不扣除乐币，但下次绑定不在进行赠送乐币
// 		当绑定成功时errno 为 14   		  提示语：您已经绑定了账号。
// 		当解绑，再次绑定成功 error为 114  提示语：恭喜您绑定成功，由于您是重复绑定，因此乐豆奖励只发放一次。
// 查看账户：
// 		返回信息更改 将isVIP 改为 rechange
// 签到奖励：
// 		当进行解绑后，再次绑定可以再次签到
//http://yjsdk.51864.com/apis/pull_balance.php?did=ksfklsf7897&token=token(ksfklsf7897)
//return json_encode(array('code'=>0,'msg'=>'',data=>array('balance'=>10)));
function token( $arr ) {
	$arr = is_array($arr) ? $arr : array();
	$arr[] = 'Rzz75~86@we_!^^@q';
	return md5(join($arr));
}

//respond
writelog('[itsok] '.json_encode($_REQUEST));
echo json_encode($res);
exit;
