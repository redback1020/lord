<?php

$cmd = 5; $code = 160; //回馈抽奖结果
$errno = 0; $error = "操作成功。";

$dateid = dateid();
$lottery = 0; //今日剩余次数

$ud = $user['uid'];
$md = $user['modelId'];
$rd = $user['roomId'];
$td = $user['tableId'];
$sd = $user['seatId'];

$Utask = $this->model->getUserTask($ud);
$maxtimes = $Utask['gold_level'] ? 200 : 100;
$user_draw_times = $this->model->redis->hget('lord_user_lottery', $ud);
if ( ! $user_draw_times ) $user_draw_times = $dateid.'_0';
$user_draw_times = explode('_', $user_draw_times);
if ( $user_draw_times[0] != $dateid ) {
	$user_draw_times[0] = $dateid;
	$user_draw_times[1] = 0;
}
$left_times = $maxtimes - $user_draw_times[1];
$left_times = $left_times > 0 ? $left_times : 0;

$version = isset($params['version']) ? $params['version'] : 0;//版本号
$type = isset($params['type']) ? $params['type'] : 0;//抽奖类型，1000，3000，5000
$free = isset($params['free']) ? $params['free'] : 0;//是否免费
$user['lottery'] = isset($user['lottery']) ? intval($user['lottery']) : 0;

$prizes_all =  $this->model->getPrizeList($version);

//用户可抽奖品 及范围筛选
$prizes = $prizes_all[$type];
$lottery_nots = isset($user['lottery_nots']) ? $user['lottery_nots'] : array();
foreach ( $prizes as $k => $v ) {
	if (in_array($k, $lottery_nots)) {
		unset($prizes[$k]);
	}
}

require_once(ROOT.'/class.lottery.php');
$cheat = array();	//无作弊因素
// if ( $user['channel'] == 'ali' && time() > strtotime('2015-05-18 00:10:00') ) {
// 	include(ROOT.'/include/data_lottery_cheat.php');
// }
// $cheat = $this->model->getUserLotteryCheat($ud);
$pool = array();	//无奖池因素
// $pool = $this->model->getLotteryPool();
$lottery = new lottery($cheat,$pool,$prizes);
$newu['coins'] = $user['coins'];
if($free){
    $newu['lottery'] = --$user['lottery'];
    $res = setUser($ud, $newu);
}
elseif( $user['coins'] < $type ) {
	debug("用户抽奖没钱[$fd|$ud|$td|$sd] runLottery");
    $errno = 4;
    $error = "很抱歉，抽奖金额不足";
    $data = array(
        'errno' => $errno,
        'error' => $error,
        'lottery' => $user['lottery'],	//剩余抽奖次数
    );
    $res = sendToFd($fd, $cmd, $code, $data);
    goto end;
}
elseif ( $left_times <= 0 ) {
    debug("用户抽奖已满[$fd|$ud|$td|$sd] runLottery");
    $errno = 5;
    $error = "今日抽奖次数已满，明天再来吧";
    $data = array(
        'errno' => $errno,
        'error' => $error,
        'lottery' => $user['lottery'],	//剩余抽奖次数
    );
    $res = sendToFd($fd, $cmd, $code, $data);
    goto end;
}
elseif ( $td && $md != 1 && ($table = $this->model->getTableInfo($td)) && $table['state'] != 2 ) {
	debug("用户在桌抽奖[$fd|$ud|$td|$sd]");
    $send = array('lottery'=>$user['lottery'], 'errno'=>4, 'error'=>"您需要先结束当前牌局，才能进行抽奖。");
    sendToFd($fd, $cmd, $code, $send);
    goto end;
}
else{
	$this->model->record->money('赌一投币', 'coins', $type, $ud, $user);
    $newu['coins'] = $user['coins'] - $type;
    $res = setUser($ud, $newu);
	$user_draw_times[1]++;
	$this->model->redis->hset('lord_user_lottery', $ud, join('_', $user_draw_times));
}
$res = $lottery->run();
if ( !$res )
{
	gerr("用户抽奖失败[$fd|$ud|$td|$sd] runLottery ");
	$errno = 2;
	$error = "很抱歉，抽奖系统错误，请稍候重试。\n您也可以联系官方QQ群：11032773。";
	$data = array(
		'errno' => $errno,
		'error' => $error,
		'lottery' => $user['lottery'],	//剩余抽奖次数
	);
	$res = sendToFd($fd, $cmd, $code, $data);
	goto end;
}
$left_times = $maxtimes - $user_draw_times[1] ;
// $pool  = $lottery->getPool();
// $res = $this->model->setLotteryPool();
$prize = $lottery->getPrize();
$prize['id'] = $prize['picture_id'];
//处理发奖
$gift = array();
if ( $prize['coins'] ) $gift['coins'] = $prize['coins'];
if ( $prize['coupon'] ) $gift['coupon'] = $prize['coupon'];
// if ( $prize['lottery'] ) $gift['lottery'] = $prize['lottery'];
$propids = $prize['propid'] ? array($prize['propid']) : array();
foreach ( $propids as $iid )
{
	$gift['items'][$iid] = array('id'=>$iid,'num'=>1);
}
$res = $this->model->userPrize($ud, $gift, $user, $free ? '免费抽奖' : '赌一中奖');
if ( !$res ) {
	gerr("用户抽奖失败[$fd|$ud|$td|$sd] model->userPrize prize=".json_encode($gift));
	$send = array(
		'errno' => 3,
		'error' => "很抱歉，抽奖系统错误，请稍候重试。\n您也可以联系官方QQ群：11032773。",
		'lottery' => $user['lottery'],
		'coins' => $user['coins'],
		'left_lottery' => $left_times,//剩余抽奖次数
	);
	$res = sendToFd($fd, $cmd, $code, $send);
	goto end;
}
$user = $this->model->getUserInfo($ud);
// 加入抽奖记录
$res = $this->model->addUserLottery($user, $prize);
$dateid = intval(date("Ymd"));
$result = $this->model->getLuckyDrawStaticsResult($ud,$dateid);
debug("用户抽奖成功[$fd|$ud|$td|$sd]");

$data = array(
	'errno' => $errno,
	'error' => $error,
	'prizeId' => $prize['picture_id'],
	'prizeInfo' => $prize['info'],
	// 'prizeGold' => $prize['gold'],
	'prizeCoins' => $prize['coins'],
	'prizeCoupon' => $prize['coupon'],
	'prizePropid' => $prize['propid'],
	// 'prizeLottery' => $prize['lottery'],
	// 'gold' => $user['gold'],
	'coins' => $user['coins'],
	'coupon' => $user['coupon'],
	'lottery' => $user['lottery'] ,	//免费抽奖次数
    'got_coins'  => $result['coins'],        //今日获得乐豆数
    'got_coupon'  => $result['coupon'],        //今日获得乐卷数
	'propDress' => $user['propDress'],
    'left_lottery' => $left_times,//剩余抽奖次数
);
$res = sendToFd($fd, $cmd, $code, $data);
//刷新数据
$cmd = 4; $code = 110;	$send = array('coins'=>$user['coins'], 'coupon'=>$user['coupon'], 'lottery'=>$user['lottery']);
$res = sendToFd($fd, $cmd, $code, $send);


end:{
	$this->model->record->action($accode, $rd, $td, $ud, $user);
}
