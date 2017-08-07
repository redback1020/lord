<?php

//用户校验
$ud = $user['uid'];
$md = $user['modelId'];
$rd = $user['roomId'];
$td = $user['tableId'];
$sd = $user['seatId'];

//参数校验
$show = isset($params['showcard']) ? intval(!!$params['showcard']) : 0;//是否明牌
if ( $md == 1 || ! isset($this->rooms[$rd]) || $td ) {
	debug("凑桌操作无效 F=$fd U=$ud R=$rd T=$td show=$show");
	goto end;
} elseif ( isset($user['gameStart']) && $user['gameStart'] > 0 ) {
	debug("凑桌操作禁止 F=$fd U=$ud R=$rd T=$td show=$show gameStart=".$user['gameStart']);
	goto end;
} elseif ( $user['coins'] < $this->rooms[$rd]['enterLimit'] ) {
	closeToFd($fd, "凑桌用户豆少 F=$fd U=$ud R=$rd T=$td show=$show coins=".$user['coins']);
	goto end;
}
elseif($this->rooms[$rd]['enterLimit_'] > 0 && $user['coins'] > $this->rooms[$rd]['enterLimit_'])
{
    $data['isSmall'] = 0;
    $data['type'] = 'into';
    $data['newRoomId'] = 0;
    $cmd = 5; $code = 1027;
    sendToFd($fd, $cmd, $code, $data);
    goto end;
}
//渠道 一天输赢上限
if($user["channel"] === "sjappcode")
{
    $score = $this->model->coinsStat($ud);
    if(abs($score) > model::COINS_LIMIT)
    {
        $cmd = 4; $code = 166;
        if($score > model::COINS_LIMIT)$send = array('msg'=>"您今天赢得够多了\r\n请明天再来进行游戏");
        else $send = array('msg'=>"您今天输得够多了\r\n请明天再来进行游戏");
        sendToFd($fd, $cmd, $code, $send);
        goto end;
    }
}

if ( $user['modelId'] == 3 && $user['gameId'] && ( $G = $this->match->getGame($user['modelId'],0,$user['gameId']) ) ) {
	$rooms = $this->match->getRooms($G['modelId']);
	if ( ! $rooms || ! isset($rooms[$G['roomId']]) ) {
		$newU['modelId'] = $newU['gameId'] = $newU['entry'] = $newU['score'] = 0;
		$user = array_merge($user, $newU); //setUser($ud, $newU); unset($newU);
	} else {
		$getStart = $this->match->getStart($G, 1);
		if ( ! $getStart || $getStart - time() < $this->confs['time_match_before_start'] ) {
			$cmd = 5; $code = 222;
			$send = array('errno'=>0,'error'=>"您已报名的比赛就快开始，\n请前往比赛场。",'modelId'=>$G['modelId'],'roomId'=>$G['roomId']);
			sendToFd($fd, $cmd, $code, $send);
			goto end;
		}
	}
}

debug("用户请求凑桌 F=$fd U=$ud R=$rd T=$td show=$show");

$newU['tableId'] = $newU['seatId'] = $user['tableId'] = $user['seatId'] = 0;
$newU['isShowcard'] = $user['isShowcard'] = $show;
$newU['gameStart'] = $user['gameStart'] = time();
setUser($ud, $newU); unset($newU);

//通知用户: 开始凑桌
$cmd = 5; $code = 1001; $send = array();
$res = $this->model->sendToUser($user, $cmd, $code, $send);

// //旧版凑桌: 返回索引 尝试开桌
// $index = $this->model->addRoomPlayer($user);
// if ( $index > 1 ) {
// 	// //事件 试组牌桌
// 	// $sceneId = 'ROOMID_'.$rd;
// 	// $act = "GAME_NEW_TABLE";
// 	// $params = array('roomId'=>$rd);
// 	// $delay = 0;
// 	// $hostId = HOSTID;
// 	// setTimer($sceneId, $act, $params, $delay, $hostId);
// 	$this->GAME_NEW_TABLE($rd);
// }
//新版凑桌：执行凑桌 尝试开桌
$uids = $this->model->addJoinTrio($user, $rd, 1);
if ( $uids ) $this->MAKE_TABLE($rd, $uids);

end:{
	$this->model->record->action($accode, $rd, $td, $ud, $user);
}
