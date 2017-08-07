<?php

//报名参赛

/**
 * @var $this gamer
 */

$ud = $user['uid'];
$md = $user['modelId'];
$rd = isset($params['roomId']) ? intval($params['roomId']) : 0;
$td = $user['tableId'];
$sd = $user['seatId'];

//渠道 一天输赢上限
if($user["channel"] === "sjappcode")
{
    $score = $this->model->coinsStat($ud);
    if(abs($score) > model::COINS_LIMIT)
    {
        if($score > model::COINS_LIMIT)$send = array('msg'=>"您今天赢得够多了\r\n请明天再来进行游戏");
        else $send = array('msg'=>"您今天输得够多了\r\n请明天再来进行游戏");
        $cmd = 5; $code = 208;
        sendToFd($fd, $cmd, $code, $send);
        goto end;
    }
}

$R = $G = array();
$ret = $this->match->entry($rd, $user);
$errno = $this->match->errno;
$error = $this->match->error;
$errors = $this->match->getError();
if ( $errno == 99 ) gerr("[$accode] F=$fd U=$ud R=$rd T=$td ".json_encode($errors));
if ( ! $ret ) {
	$send = array('errno'=>$errno, 'error'=>$error, 'coins'=>$user['coins'], 'coupon'=>$user['coupon']);
} else {
	$user = array_merge($user, $ret['newU']); // $room = $ret['room'];
	$send = array('errno'=>$errno, 'error'=>'', 'money'=>$ret['room']['entryMoney'], 'cost'=>$ret['room']['entryCost'], 'coins'=>$user['coins'], 'coupon'=>$user['coupon']);
	$this->record->money('新赛报名', $ret['room']['entryMoney'], $ret['room']['entryCost'], $ud, $user);
	$R = $ret['room'];
	$G = $ret['game'];
}
debug("用户报名参赛 F=$fd U=$ud R=$rd T=$td");
$cmd = 5; $code = 208;
sendToFd($fd, $cmd, $code, $send);


//报名操作后 主动刷新房间
$room = $this->match->showRoom($rd, $user);
$errno = $this->match->errno;
$errors = $this->match->getError();
if ( $errno == 99 ) gerr("[$accode] F=$fd U=$ud R=$rd T=$td ".json_encode($errors));
if ( ! $room ) $room = array('brief'=>'', 'state'=>0);
$view = array('brief'=>$room['brief'], 'state'=>$room['state']);
$cmd = 5; $code = 206; $send = array('errno'=>$errno,'error'=>''); $send = array_merge($send, $view);
sendToFd($fd, $cmd, $code, $send);

//if ( $R && $G ) $this->match->check($R, $G);

end:{
	// $rd = $user['lastRoomId'];
	// $this->model->record->action($accode, $rd, $td, $ud, $user);
}
