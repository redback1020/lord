<?php

//用户校验
$ud = $user['uid'];
$md = $user['modelId'];
$rd = $user['roomId'];
$td = $user['tableId'];
$sd = $user['seatId'];

$gold = intval($params['gold']);
$coins = intval($gold * $this->confs['gold2coin']);
$coupon = 0;
$propId = 0;

if ( $gold <= 0 || $user['gold'] - $gold < 0 ) {
	$cmd = 5; $code = 4001; $send = array();//data['errorInfo'] = "gold error".$gold;
	sendToFd($fd, $cmd, $code, $send);
	goto end;
}
$addU['gold'] = -$gold;
$addU['coins'] = $coins;
$this->model->incUserInfo($ud, $addU);
$user['gold'] += $addU['gold'];
$user['coins'] += $addU['coins'];
$this->model->record->money('币豆减币', 'gold', $gold, $ud, $user);
$this->model->record->money('币豆加豆', 'coins', $coins, $ud, $user);

if ( $td ) {
	$table = $this->model->getTableInfo($td);
	if ( $table && isset($table["seat{$sd}coins"]) && isset($table["seat{$sd}uid"]) && $table["seat{$sd}uid"] == $ud ) {
		$addT["seat{$sd}coins"] = $coins;
		$this->model->incTableInfo($ud, $addT);
	}
}

$type = strtolower($action);
$date = date("Y-m-d H:i:s");
$dateid = date("Ymd");
$time = time();
$sql = "INSERT INTO lord_user_cost (`dateid`,`type`,`channel`,`uid`,`gold`,`coins`,`coupon`,`propId`,`ip`,`date`,`time`) VALUES ";
$sql.= "($dateid,'$type','".$user['channel']."',$ud,".$addU['gold'].",".$addU['coins'].",0,0,'','$date',$time)";
bobSql($sql);

// 开始用户统计
$userinfo = $user;
$usertask = $this->model->getUserTask($ud);
// 普通场游戏次数
$adding['gold_all'] = $gold;
$adding['gold_week'] = $gold;
$adding['gold_day'] = $gold;
// 暂不入榜
// 累加入榜 用户金币兑换榜单
// $res = $this->model->zUserGoldcost($ud, $gold);
// 更新用户统计信息
$res = $this->model->incUserTask($ud,$adding);
$usertask['gold_all'] += $gold;
$usertask['gold_week'] += $gold;
$usertask['gold_day'] += $gold;
// 结束用户统计

// 开始执行任务: 成为金牌用户、每天两次50乐豆兑换乐币
if ( $gold ) {
	$taskcmd = 0;
	$taskcode = 0;
	$taskid = array(4,5,6);
	$taskfresh = $this->is_freshtask;
	$tasker = new task($this->model, $taskid, $taskcmd, $taskcode, $taskfresh);
	$res = $tasker->run($userinfo, $usertask);
	if ( $res ) {
		$uis = $uts = array();
		foreach ( $taskid as $k => $id )
		{
			debug("任务消耗乐豆[$fd|$ud|$td|$sd] taskid=$id");
			$uis = array_merge($uis, isset($res[$id]['userinfo']) ? $res[$id]['userinfo'] : array());
			$uts = array_merge($uts, isset($res[$id]['usertask']) ? $res[$id]['usertask'] : array());
		}
		$userinfo = $uis ? array_merge($userinfo, $uis) : array();
		$usertask = $uts ? array_merge($usertask, $uts) : array();
	}
}
// 结束执行任务:

debug("用户兑换[$fd|$ud|$td|$sd]");

//通知用户: 兑换成功
$cmd = 5; $code = 4000; $send = array( 'gold' => $user['gold'], 'coins' => $user['coins'], 'gold_' => $gold, 'coins_' => $coins );
sendToFd($fd, $cmd, $code, $send);


end:{
	$this->model->record->action($accode, $rd, $td, $ud, $user);
}
