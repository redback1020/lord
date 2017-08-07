<?php

$cost_coins = $this->confs['lottery_cost_coins'];//抽奖消费乐豆数
$cost_lottery = $this->confs['lottery_cost_lottery'];//抽奖使用抽奖数
$cmd = 5; $code = 134; //回馈抽奖结果

$ud = $user['uid'];
$md = $user['modelId'];
$rd = $user['roomId'];
$td = $user['tableId'];
$sd = $user['seatId'];

//基础校验
$user['lottery'] = isset($user['lottery']) ? intval($user['lottery']) : 0;
if ( !$user['lottery'] && $user['coins'] < $cost_coins ) {
	$send = array(
		'errno' => 1,
		'error' => "您的免费抽奖次数为0，且乐豆不够一次抽奖。\n您可以前往商城购买乐豆或游戏赢取乐豆。",
		'lottery' => $user['lottery'],	//剩余次数
		'coins' => $user['coins'],	//剩余乐豆
		'costCoins' => 2000,
	);
	$res = sendToFd($fd, $cmd, $code, $send);
	goto end;
}
if ( $user['lottery'] ) {
	$cost_coins = 0;
} else {
	$cost_lottery = 0;
	$Utask = $this->model->getUserTask($ud);
	$maxtimes = $Utask['gold_level'] ? 200 : 100;
	$user_lottery = $this->model->redis->hget('lord_user_lottery', $ud);
	$dateid = date("Ymd");
	if ( ! $user_lottery ) $user_lottery = $dateid.'_0';
	$user_lottery = explode('_', $user_lottery);
	if ( $user_lottery[0] != $dateid ) {
		$user_lottery[0] = $dateid;
		$user_lottery[1] = 0;
	}
	if ( $user_lottery[1] >= $maxtimes ) {
		$send = array(
			'errno' => 4,
			'error' => "您的乐豆抽奖次数已经达到每日上限，\n".($Utask['gold_level'] ? "先玩玩游戏，明天再来吧。" : "成为金牌用户，可以增加更多次数。"),
			'lottery' => $user['lottery'],	//剩余次数
			'coins' => $user['coins'],	//剩余乐豆
			'costCoins' => 2000,
		);
		$res = sendToFd($fd, $cmd, $code, $send);
		goto end;
	}
	elseif ( $td && $md != 1 && ($table = $this->model->getTableInfo($td)) && $table['state'] != 2 ) {
		debug("用户在桌抽奖[$fd|$ud|$td|$sd]");
	    $send = array('lottery'=>$user['lottery'], 'errno'=>4, 'error'=>"您需要先结束当前牌局，才能进行抽奖。");
	    sendToFd($fd, $cmd, $code, $send);
	    goto end;
	}
	else {
		$user_lottery[1]++;
		$this->model->redis->hset('lord_user_lottery', $ud, join('_', $user_lottery));
	}
}
//奖品范围
$data_lottery_prizes = array();
include(ROOT.'/include/data_lottery_prizes.php');
$prizes = $data_lottery_prizes;
$lottery_nots = isset($user['lottery_nots']) ? $user['lottery_nots'] : array();
foreach ( $prizes as $k => $v ) {
	if (in_array($k, $lottery_nots)) {
		unset($prizes[$k]);
	}
}
//作弊因素
$cheat = array();
// $cheat = $this->model->getUserLotteryCheat($ud);
//奖池因素
$pool = array();
// $pool = $this->model->getLotteryPool();
//执行抽奖
require_once(ROOT.'/class.lottery.php');
$lottery = new lottery($cheat, $pool, $prizes);
$res = $lottery->run();
if ( !$res ) {
	gerr("用户抽奖失败[$fd|$ud|$td|$sd] runLottery ");
	$data = array(
		'errno' => 2,
		'error' => "很抱歉，抽奖系统错误，请稍候重试。\n您也可以联系官方QQ群：11032773。",
		'lottery' => $user['lottery'],
		'coins' => $user['coins'],
	);
	$res = sendToFd($fd, $cmd, $code, $data);
	goto end;
}
// $pool  = $lottery->getPool();
// $res = $this->model->setLotteryPool();
$prize = $lottery->getPrize();
//处理结果
if ( $cost_coins ) {
	$res = $this->model->incUserInfo($ud, array('coins'=>$cost_coins*-1));
	$user['coins'] -= $cost_coins;
	$this->model->getRecord()->money('赌一投币', 'coins', $cost_coins, $ud, $user);
} else {
	$res = $this->model->incUserInfo($ud, array('lottery'=>$cost_lottery*-1));
	$user['lottery'] -= $cost_lottery;
}
//处理发奖
$gift = array();
if ( $prize['coins'] ) $gift['coins'] = $prize['coins'];
if ( $prize['coupon'] ) $gift['coupon'] = $prize['coupon'];
if ( $prize['lottery'] ) $gift['lottery'] = $prize['lottery'];
$propids = $prize['propid'] ? array($prize['propid']) : array();
foreach ( $propids as $iid )
{
	$gift['items'][$iid] = array('id'=>$iid,'num'=>1);
}
$res = $this->model->userPrize($ud, $gift, $user, $cost_coins ? '赌一中奖' : '免费抽奖');
if ( !$res ) {
	gerr("用户抽奖失败[$fd|$ud|$td|$sd] model->userPrize prize=".json_encode($gift));
	$send = array(
		'errno' => 3,
		'error' => "很抱歉，抽奖系统错误，请稍候重试。\n您也可以联系官方QQ群：11032773。",
		'lottery' => $user['lottery'],
		'coins' => $user['coins'],
		'costCoins' => 2000,
	);
	$res = sendToFd($fd, $cmd, $code, $send);
	goto end;
}
$user = $this->model->getUserInfo($ud);
//抽奖记录
$res = $this->model->addUserLottery($user, $prize);

// if ( $prize['propid'] ) {
// 	$res = $this->model->buyGoods($user, $prize['propid'], 1);//强制用购买方式获取此道具
// 	if ( !is_array($res) ) {
// 		gerr("用户抽奖失败[$fd|$ud|$td|$sd] buyGoods prize=".json_encode($prize));
// 		$send = array(
// 			'errno' => 3,
// 			'error' => "很抱歉，抽奖系统错误，请稍候重试。\n您也可以联系官方QQ群：11032773。",
// 			'lottery' => $user['lottery'],
// 			'coins' => $user['coins'],
//			'costCoins' => 2000,
// 		);
// 		$res = sendToFd($fd, $cmd, $code, $send);
// 		goto end;
// 	}
// }
// $user = $this->model->getUserInfo($ud);
// $addU = array('coins'=>0, 'coupon'=>0, 'lottery'=>$cost_lottery*-1);
// if ( $prize['coins'] ) {
// 	$addU['coins'] += $prize['coins'];
// 	$user['coins'] += $addU['coins'];
// }
// if ( $prize['coupon'] ) {
// 	$addU['coupon'] += $prize['coupon'];
// 	$user['coupon'] += $addU['coupon'];
// }
// if ( $prize['lottery'] ) {
// 	$addU['lottery'] += $prize['lottery'];
// 	$user['lottery'] += $addU['lottery'];
// }
// $res = $this->model->incUserInfo($ud, $addU);

//回馈结果
debug("用户抽奖成功[$fd|$ud|$td|$sd]");
$data = array(
	'errno' => 0,
	'error' => "操作成功。",
	'prizeId' => $prize['id'],
	'prizeInfo' => $prize['info'],
	'prizeCoins' => $prize['coins'],
	'prizeCoupon' => $prize['coupon'],
	// 'prizeLottery' => $prize['lottery'],
	'prizePropid' => $prize['propid'],
	'coins' => $user['coins'],
	'coupon' => $user['coupon'],
	'lottery' => $user['lottery'],
	'propDress' => $user['propDress'],
	'propItems' => $user['propItems'],
	'costCoins' => 2000,
);
$res = sendToFd($fd, $cmd, $code, $data);
//刷新数据
$cmd = 4; $code = 110; $send = array('coins'=>$user['coins'], 'coupon'=>$user['coupon'], 'lottery'=>$user['lottery'], 'propDress'=>$user['propDress'], 'propItems'=>$user['propItems'], 'costCoins' => 2000);
$res = sendToFd($fd, $cmd, $code, $send);
//发送广播
if ( in_array($prize['id'], array(1007, 1004)) ) {
	sendHorn("恭喜·".$user['nick']."·在幸运抽奖中人品大爆发，获得".$prize['name']."。", 1);
	sendHorn("恭喜·".$user['nick']."·在幸运抽奖中人品大爆发，获得".$prize['name']."。", 1);
} elseif ( $prize['cateid'] == 5 ) {
	sendHorn("恭喜·".$user['nick']."·在幸运抽奖中人品大爆发，获得".$prize['name']."，请速与官方客服联系。", 1);
	sendHorn("恭喜·".$user['nick']."·在幸运抽奖中人品大爆发，获得".$prize['name']."，请速与官方客服联系。", 1);
}


end:{
	$this->model->record->action($accode, $rd, $td, $ud, $user);
}
