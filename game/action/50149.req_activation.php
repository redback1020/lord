<?php

$cmd = 5;
$code = 150;
$data['errno'] = 0;
$data['error'] = "操作成功";

// 校验参数
$giftcode = isset( $params['giftCode']) ? trim($params['giftCode']) : '';
if ( ! $giftcode ) {
	$res = closeToFd($fd, "激活参数无效 params=".json_encode($params));
	goto end;
}

$ud = $user['uid'];
$rd = $user['roomId'];
$td = $user['tableId'];
$sd = $user['seatId'];

debug("用户激活礼包 F=$fd U=$ud T=$td C=$giftcode");

$data_activation_gift_list = array();
include(ROOT.'/include/data_activation_gift_list.php');

//通用激活码 配置
$spcodes = array(
	// 22446688 => array(//通用码
	// 	'channel' => array('ali'),
	// 	'datest' => 20160527,//开始日含
	// 	'datend' => 20160630,//结束日含
	// 	'maxnum' => 500000,//礼包数量
	// 	'cateid' => 6,//使用奖励配置，来自于../include/data_activation_gift_list.php
	// 	'name' => "阿里5月礼包",//名称，用于礼包领完之后的用户呈现
	// ),
	44668800 => array(
		'channel' => array(),
		'datest' => 20160326,
		'datend' => 20161231,
		'maxnum' => 2000000,
		'cateid' => 3,
		'name' => "暑期礼包",
	),
);

$dateid = intval(date('Ymd'));
if ( isset($spcodes[$giftcode]) && ( !( $spcodes[$giftcode]['channel'] && is_array($spcodes[$giftcode]['channel'])) || in_array($user['channel'], $spcodes[$giftcode]['channel']) ) && $spcodes[$giftcode]['datest'] <= $dateid && $dateid <= $spcodes[$giftcode]['datend'] ) {
	$special = $giftcode;
	$maxnum = $spcodes[$giftcode]['maxnum'];
	$cateid = $spcodes[$giftcode]['cateid'];
	$name = $spcodes[$giftcode]['name'];
	$num = $this->model->redis->hadd("lord_special_activation_".$user['channel'], $special);	//加一次
	if ( $num > $maxnum ) {
		$this->model->redis->hdda("lord_special_activation_".$user['channel'], $special);	//扣一次
		$data['errno'] = 2;
		$data['error'] = $name."已经被领的一个不留。\n您老人家来的可有点儿晚了哦。。。";
		$res = $this->sendToFd($fd, $cmd, $code, $data);
		goto end;
	}
}
else {
	$special = 0;
	require_once(ROOT.'/class.activation.php');
	$activation = new activation($this->model->mysql);
	$cateid = $activation->check($giftcode);
}

if ( $cateid < 0 ) {
	$data['errno'] = 2;
	$data['error'] = "激活码错误或者已经过期，\n您可以联系客服QQ或者拨打客服热线。";
	$res = sendToFd($fd, $cmd, $code, $data);
	goto end;
}
elseif ( ! $cateid || ! isset($data_activation_gift_list[$cateid]) ) {
	gerr("用户激活失败[$fd|$ud|$td|$sd] cateid=$cateid");
	$data['errno'] = 1;
	$data['error'] = "操作失败，请稍候重试，\n您可以联系客服QQ或者拨打客服热线。";
	$res = sendToFd($fd, $cmd, $code, $data);
	goto end;
}
$gift = $data_activation_gift_list[$cateid];

//常规激活码流程
if ( in_array($cateid, array(1,2,3,4,5,6,7)) )
{
	// 任务
	$userinfo = $user;
	$usertask = $this->model->getUserTask($ud);
	//特殊礼包激活码矫正 旧礼包分类更新重用导致的矫正
	$newUT = array();
	$notModify = 1;
	if ( $usertask['login_last_dateid'] < 20160530 && $usertask['task12'] ) $notModify = $usertask['task12'] = $newUT['task12'] = $usertask['task12dateid'] = $newUT['task12dateid'] = 0;//分类2 20160530阿里5月礼包
	if ( $usertask['login_last_dateid'] < 20160704 && $usertask['task13'] ) $notModify = $usertask['task13'] = $newUT['task13'] = $usertask['task13dateid'] = $newUT['task13dateid'] = 0;//分类3 20160704暑期礼包
	if ( $usertask['login_last_dateid'] < 20160831 && $usertask['task14'] ) $notModify = $usertask['task14'] = $newUT['task14'] = $usertask['task14dateid'] = $newUT['task14dateid'] = 0;//分类4 20160408海信白金礼包（价值100元）
	if ( $usertask['login_last_dateid'] < 20160401 && $usertask['task15'] ) $notModify = $usertask['task15'] = $newUT['task15'] = $usertask['task15dateid'] = $newUT['task15dateid'] = 0;//分类5 20140401水晶礼包乐豆2万记牌器3天大师套装7天
	if ( $usertask['login_last_dateid'] < 20160829 && $usertask['task16'] ) $notModify = $usertask['task16'] = $newUT['task16'] = $usertask['task16dateid'] = $newUT['task16dateid'] = 0;//分类6 20160829阿里礼包
	if ( $usertask['login_last_dateid'] < 20160408 && $usertask['task17'] ) $notModify = $usertask['task17'] = $newUT['task17'] = $usertask['task17dateid'] = $newUT['task17dateid'] = 0;//分类7 20160408海信钻石礼包（价值300元）
	if ( ! $notModify ) { $newUT['login_last_dateid'] = $usertask['login_last_dateid'] = $usertask['login_this_dateid']; }
	if ( $newUT ) { $this->model->setUserTask($ud, $newUT); unset($newUT); }
	$taskcmd = 5;
	$taskcode = 149;
	$taskid = 10+$cateid;
	$taskfresh = $this->is_freshtask;
	$tasker = new task($this->model, $taskid, $taskcmd, $taskcode, $taskfresh);
	$res = $tasker->run($userinfo, $usertask);
	if ( $res ) {
		debug("任务激活礼包[$fd|$ud] taskid=$taskid");
		$userinfo = array_merge($userinfo, isset($res[$taskid]['userinfo']) ? $res[$taskid]['userinfo'] : array());
		$usertask = array_merge($usertask, isset($res[$taskid]['usertask']) ? $res[$taskid]['usertask'] : array());
	} else {
		if ( $special ) {
			$this->model->redis->hdda("lord_special_activation", $special);	//扣一次
		} else {
			$res = $activation->reset(1);//重置giftcode到已发状态
		}
		$data['errno'] = 3;
		$data['error'] = "您已经领过“".$gift['name']."”礼包，只能领一次哦。";
		$res = sendToFd($fd, $cmd, $code, $data);
		goto end;
	}
	$user = $userinfo;

	//发奖
	$prizes = array();
	if ( $gift['coins'] ) $prizes['coins'] = $gift['coins'];
	if ( $gift['coupon'] ) $prizes['coupon'] = $gift['coupon'];
	if ( $gift['lottery'] ) $prizes['lottery'] = $gift['lottery'];
	$propids = $gift['propid'] ? array_unique(array_merge($gift['props'],array($gift['propid']))) : array();
	foreach ( $propids as $iid )
	{
		$prizes['items'][$iid] = array('id'=>$iid,'num'=>1);
	}
	$res = $this->model->userPrize($ud, $prizes, $user, '激活礼包');
	if ( !$res ) {
		gerr("用户激活失败[$fd|$ud|$td|$sd] props=".json_encode($props) );
		$data['errno'] = 4;
		$data['error'] = "操作失败，请稍候重试。\n您可以联系客服QQ或者拨打客服热线。";
		$res = sendToFd($fd, $cmd, $code, $data);
		goto end;
	}
	$user = $this->model->getUserInfo($ud);

	$data['coins'] = $user['coins'];
	$data['giftCoins'] = $gift['coins'];
	$data['propDress'] = $user['propDress'];
	$data['error'] = $gift['info'];
	sendToFd($fd, $cmd, $code, $data);

	$cmd = 4; $code = 110; $send = array('coins'=>$user['coins'],'coupon'=>$user['coupon'],'lottery'=>$user['lottery'],'propDress'=>$user['propDress'],'propItems'=>$user['propItems']);
	sendToFd($fd, $cmd, $code, $send);
}
//商品激活码流程
elseif ( in_array($cateid, array(51,52,53,54,55)) )
{
	$ip = $user['ip'];
	$uid = $ud;
	$channel = $user['channel'];
	$propId = $gift['goodsid'];
	$gold = $gift['gold'];
	$coins = $gift['coins'];
	//以下19行代码来自于/home/wwwroot/ddz.protocal.51864.com/landlord/public/chargeBySDK.php 这些代码必须与原文件保证一致
	if ( in_array($propId, array(5, 6, 7, 8, 24, 25, 26, 28, 31, 32)) ) {	// 这些 propId 购买乐币商品获得乐豆
		$propId = 0;//归零
		$gold = intval($gold);//不变
		$coins = intval($gold * 10000);//重设?
	} elseif ( $propId ) {							// 购买道具
		$propId = intval($propId);//不变
		$gold = intval($gold);//不变
		$coins = 0;//归零?
	} elseif ( $coins ) {							// 直冲乐豆
		$propId = 0;//归零
		$gold = intval($coins/10000);//还原
		$coins = intval($coins);//不变
	} else {										// 旧版充值
		$propId = 0;//归零
		$gold = intval($gold);//不变
		$coins = intval($gold*10000);//重设?
	}
	$task['data'] = array( 'ip'=>$ip, 'uid'=>$uid, 'gold'=>$gold, 'coins'=>$coins, 'coupon'=>0, 'propId'=>$propId, 'channel'=>$channel );
	$task['act']  = $propId ? 'API_GOLD2PROP' : 'API_GOLD2COINS';//加道具/加乐豆
	//以上19行代码来自于/home/wwwroot/ddz.protocal.51864.com/landlord/public/chargeBySDK.php 这些代码必须与原文件保证一致
	$res = $this->model->redis->ladd("lord_api_task", $task);
	//
	$data['errno'] = 5;
	$data['error'] = $gift['info'];
	sendToFd($fd, $cmd, $code, $data);
} else {
	gerr("激活逻辑错误 cateid=$cateid");
}


end:{
	$this->model->record->action($accode, $rd, $td, $ud, $user);
}
