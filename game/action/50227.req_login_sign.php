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

$prizesConfig = $this->confs['login_got_config'];
$gift = $prizesConfig[intval(($usertask['login_day5_day']))];

//这个增加抽奖机会没有用任务器实现
//发奖
$prizes = array();
if ( $gift['coins'] ) $prizes['coins'] = $gift['coins'];
if ( $gift['coupon'] ) $prizes['coupon'] = $gift['coupon'];
if ( $gift['lottery'] ) $prizes['lottery'] = $gift['lottery'];
$propids = $gift['props'];
foreach ( $propids as $iid )
{
    $prizes['items'][$iid] = array('id'=>$iid,'num'=>1);
}
$res = $this->model->userPrize($ud, $prizes, $user, '每日签到');
if ( !$res ) {
    gerr("用户激活失败[$fd|$ud|$td|$sd] props=".json_encode($propids) );
    $data['errno'] = 4;
    $data['error'] = "操作失败，请稍候重试。\n您可以联系客服QQ或者拨打客服热线。";
    $res = sendToFd($fd, $cmd, $code, $data);
    goto end;
}
/* $addU['coins'] = $signCoins;
$addU['lottery'] = $signLottery;
$res = $this->model->incUserInfo($ud, $addU);
foreach ( $addU as $k => $v ) $this->model->getRecord()->money('每日签到', $k, $v, $ud, $user);
 */
$newU['checkin_undo'] = $user['checkin_undo'] = 0;
$res = setUser($ud, $newU);
$newUT['login_day5_got'] = $usertask['login_day5_got'] = 1;
$res = $this->model->setUserTask($ud, $newUT);
$user = $this->model->getUserInfo($ud);
//$this->model->redis->redis->hset("login_got_$dd",$deviceID,1);

$data['goldLevel'] = isset($usertask['gold_level']) ? $usertask['gold_level'] : 0;
$data['goldLevelNeed'] = $this->confs['gold_level1'];
$data['goldCostAll'] = isset($usertask['gold_all']) ? $usertask['gold_all'] : 0;
$data['signDay'] = isset($usertask['login_day5_day']) ? $usertask['login_day5_day'] : 1;
$data['signGot'] = isset($usertask['login_day5_got']) ? $usertask['login_day5_got'] : 1;
$data['coins'] = $user['coins'];
$data['lottery'] = $user['lottery'];
$res = sendToFd($fd, $cmd, $code, $data);

//通知用户 刷新数据
$cmd = 4; $code = 110;
$propDress = $this->model->getDbUserDress($ud);
$send = array('coins'=>$user['coins'], 'lottery'=>$user['lottery'], 'checkin_undo'=>0, 'propDress'=>$propDress);
$res = sendToFd($fd, $cmd, $code, $send);

$isDone = 1;

end:{
	if ( $isDone )
	$this->model->record->action($accode, $rd, $td, $ud, $user);
}
