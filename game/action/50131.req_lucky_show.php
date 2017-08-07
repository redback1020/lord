<?php

$cmd = 5;
$code = 132; //回馈抽奖界面
$errno = 0;
$error = "操作成功。";

$uid = $user['uid'];
$tableId = $user['tableId'];
$seatId = $user['seatId'];

//登录跨天，重新矫正用户日期
$dateid = intval(date("Ymd"));
if ( $user['dateid'] != $dateid ) {
	$newU['dateid'] = $user['dateid'] = $dateid;
	$res = setUser($uid, $newU);
}
//获取用户任务信息，如果任务信息跨天，会重新矫正抽奖机会
$usertask = $this->model->getUserTask($uid);
$user['coupon'] = isset($user['coupon']) ? intval($user['coupon']) : 0;
$user['lottery'] = isset($user['lottery']) ? intval($user['lottery']) : 0;
$todayNormalPlay = isset($usertask['normal_day_play']) ? intval($usertask['normal_day_play']) : 0;
$todayWeekPoints = isset($usertask['match_day_point']) ? intval($usertask['match_day_point']) : 0;
$todayGold2Coins = isset($usertask['gold_day']) ? intval($usertask['gold_day']) : 0;

$data_lottery_rules = array();
include(ROOT.'/include/data_lottery_rules.php');
$luckyRules = $data_lottery_rules;

$data_lottery_prizes = array();
include(ROOT.'/include/data_lottery_prizes.php');
//用户可抽奖品范围筛选
if ($data_lottery_prizes)
{	// 抽调14个奖品，刷新奖品顺序
	$keys = array_rand($data_lottery_prizes,14);
	if (count($keys) != 14)
	{
		gerr("用户抽奖失败[$fd|$uid|$tableId|$seatId] prizes=".json_encode($data_lottery_prizes));
		$errno = 1;
		$error = "很抱歉，抽奖系统错误，请稍候重试。\n您也可以联系官方QQ群：11032773。";
		$data = array(
			'errno' => $errno,
			'error' => $error,
			'costCoins' => 2000,
		);
		$res = sendToFd($fd, $cmd, $code, $data);
		goto end;
	}
	$newU['lottery_nots'] = array();
	foreach ( $data_lottery_prizes as $k => $v )
	{
		if ( !in_array($k, $keys) ) {
			unset($data_lottery_prizes[$k]);
			$newU['lottery_nots'][]= $k;
		}
		else
		{
			$v_['id'] = $v['id'];
			$v_['name'] = $v['name'];
			$data_lottery_prizes[$k] = $v_;
		}
	}
	$res = setUser($uid,$newU);
	shuffle($data_lottery_prizes);
}
$luckyPrize = $data_lottery_prizes;

// $luckyRecord = array( //一周内的未知条数
// 	array('id'=>1003,'name'=>'伪造记录1','datetime'=>date("m-d H:i")),
// );
$luckyRecord = $this->model->getUserLottery($uid);

debug("查看抽奖界面[$fd|$uid|$tableId|$seatId]");

// 客户端测试代码开始 用户可抽奖数
// $user['lottery'] = 10;
// 客户端测试代码结束

$data = array(
	'errno' => $errno,
	'error' => $error,
	'coupon'  => $user['coupon'],		//奖券
	'lottery' => $user['lottery'],		//剩余抽奖次数
	'luckyRules' => $luckyRules,		//抽奖规则
	'luckyPrize' => $luckyPrize,		//奖品列表
	'luckyRecord'=> $luckyRecord,		//抽奖记录
	'todayNormalPlay' => $todayNormalPlay,	//今日普通场次数[>=100]
	'todayWeekPoints' => $todayWeekPoints,	//今日竞技场积分[>=20]
	'todayGold2Coins' => $todayGold2Coins,	//今日金币换乐豆[>=50&&<=100|>=100]
	'costCoins' => 2000,
);
$res = sendToFd($fd, $cmd, $code, $data);


end:{}
