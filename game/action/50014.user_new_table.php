<?php

//用户校验
$ud = $user['uid'];
$md = $user['modelId'];
$rd = $user['roomId'];
$td = $user['tableId'];
$sd = $user['seatId'];

//参数校验
$show = 0;//用户换桌加入房间队列时不明牌
if ( $md == 1 || ! isset($this->rooms[$rd]) ) {// || !$td ) {
	debug("换桌操作无效 F=$fd U=$ud R=$rd T=$td show=$show");
	goto end;
} elseif ( isset($user['gameStart']) && $user['gameStart'] > 0 ) {
	debug("换桌操作禁止 F=$fd U=$ud R=$rd T=$td show=$show gameStart=".$user['gameStart']);
	goto end;
} elseif ( $user['coins'] < $this->rooms[$rd]['enterLimit'] ) {
    if($user['vercode'] >10902)
    {
        $cmd = 4; $code = 120; $send = array('type'=>1,'title'=>'乐豆不足！','msg'=>'您的乐豆太少了，快去获取一些吧。',"goto"=>1,"button"=>3);
        sendToFd($fd, $cmd, $code, $send);
    }else{
	   closeToFd($fd, "换桌用户豆少 F=$fd U=$ud R=$rd T=$td show=$show coins=".$user['coins']);
    }
    goto end;
} elseif ( $td && ($table = $this->model->getTableInfo($td)) ) {
	debug("换桌开始散桌 F=$fd U=$ud R=$rd T=$td show=$show");
	$res = $this->TABLE_BREAK($table, 1);
	if ( !$res ) {
		debug("换桌散桌失败 F=$fd U=$ud R=$rd T=$td show=$show");
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

debug("用户请求换桌 F=$fd U=$ud R=$rd T=$td show=$show");

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
