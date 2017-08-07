<?php

$ud = $user['uid'];
$rd = $user['roomId'];
$td = $user['tableId'];
$sd = $user['seatId'];
$channel = $user['channel'];

$isDone = 0;

// if ( $this->model->ippban('checkin', $user['ip']) ) {
// 	debug("用户签到受限 F=$fd U=$ud R=$rd T=$td IP=".$user['ip']);
// 	goto end;
// }

debug("用户执行签到 F=$fd U=$ud R=$rd T=$td");

$cmd = 5; $code = 140; //回馈签到结果
$data['errno'] = 0;
$data['error'] = "签到奖励领取成功。";

$usertask = $this->model->getUserTask($ud);
$dd = date("Ymd");
// $sql = "select `extend` from user_login where `uid`=$ud LIMIT 0,1";
// $deviceID = $this->model->mysql->getVar($sql);
// $rt = $this->model->redis->redis->hget("login_got_$dd",$deviceID);
$rt = '';
if ( $usertask['login_day5_got'] || $rt) {
	$data['errno'] = 1;
	$data['error'] = "您已经领过登录奖励了哦。";
	$res = sendToFd($fd, $cmd, $code, $data);
	goto end;
}

$signCoins = intval(($usertask['login_day5_day']+1) * 500 * ($usertask['gold_level']+1));
$signLottery = intval($usertask['login_day5_day']==5 && $usertask['gold_level']==1);
//这个增加抽奖机会没有用任务器实现
$addU['coins'] = $signCoins;
$addU['lottery'] = $signLottery;
$res = $this->model->incUserInfo($ud, $addU);
foreach ( $addU as $k => $v ) $this->model->getRecord()->money('每日签到', $k, $v, $ud, $user);
$newU['checkin_undo'] = $user['checkin_undo'] = 0;
$res = setUser($ud, $newU);
$newUT['login_day5_got'] = $usertask['login_day5_got'] = 1;
$res = $this->model->setUserTask($ud, $newUT);
$user = $this->model->getUserInfo($ud);
//$this->model->redis->redis->hset("login_got_$dd",$deviceID,1);
//$this->model->redis->redis->hincrby("login_got_conis",$dd,$signCoins);

$data['goldLevel'] = isset($usertask['gold_level']) ? $usertask['gold_level'] : 0;
$data['goldLevelNeed'] = $this->confs['gold_level1'];
$data['goldCostAll'] = isset($usertask['gold_all']) ? $usertask['gold_all'] : 0;
$data['signDay'] = isset($usertask['login_day5_day']) ? $usertask['login_day5_day'] : 1;
$data['signGot'] = isset($usertask['login_day5_got']) ? $usertask['login_day5_got'] : 1;
$data['signCoins'] = $signCoins;
$data['signLottery'] = $signLottery;
$data['coins'] = $user['coins'];
$data['lottery'] = $user['lottery'];
$res = sendToFd($fd, $cmd, $code, $data);

//通知用户 刷新数据
$cmd = 4; $code = 110;
$send = array('coins'=>$user['coins'], 'lottery'=>$user['lottery'], 'checkin_undo'=>0);
$res = sendToFd($fd, $cmd, $code, $send);

$isDone = 1;

end:{
	if ( $isDone )
	$this->model->record->action($accode, $rd, $td, $ud, $user);
}
