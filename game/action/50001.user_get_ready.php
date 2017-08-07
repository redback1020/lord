<?php

//参数校验
$show = isset($params['showcard']) ? intval(!!$params['showcard']) : 0;//是否明牌
//用户校验
$ud = $user['uid'];
$md = $user['modelId'];
$rd = $user['roomId'];
$td = $user['tableId'];
$sd = $user['seatId'];

if ( $md == 1 || ! isset($this->rooms[$rd]) || !$td ) {
	debug("再局牌桌失效 F=$fd U=$ud R=$rd T=$td show=$show");
	goto end;
} elseif ( $user['coins'] < $this->rooms[$rd]['enterLimit'] ) {
    if($user['vercode'] >10902)
    {
        $cmd = 4; $code = 120; $send = array('type'=>1,'title'=>'乐豆不足！','msg'=>'您的乐豆太少了，快去获取一些吧。',"goto"=>1,"button"=>3);
        sendToFd($fd, $cmd, $code, $send);
    }else{
	   closeToFd($fd, "再局用户豆少 F=$fd U=$ud R=$rd T=$td show=$show coins=".$user['coins']);
    }
    goto end;
}elseif($this->rooms[$rd]['enterLimit_'] > 0 && $user['coins'] > $this->rooms[$rd]['enterLimit_'])
{
    $data['isSmall'] = 0;
    $data['type'] = 'into';
    $data['newRoomId'] = 0;
    $cmd = 5; $code = 1027;
    sendToFd($fd, $cmd, $code, $data);
    goto end;
}
//牌桌校验
$table = $this->model->getTableInfo($td);
if ( !$table ) {
	debug("再局牌桌失效 F=$fd U=$ud R=$rd T=$td show=$show");
	goto end;
} elseif ( $table['state'] != 2 ) {
	debug("再局网络延迟 F=$fd U=$ud R=$rd T=$td show=$show state2=".$table['state']);
	goto end;
}

if ( $user['modelId'] == 3 && $user['gameId'] && ( $G = $this->match->getGame($user['modelId'],0,$user['gameId']) ) ) {
	$rooms = $this->match->getRooms($G['modelId']);
	if ( ! $rooms || ! isset($rooms[$G['roomId']]) ) {
		$newU['modelId'] = $newU['gameId'] = $newU['entry'] = $newU['score'] = 0;
		$user = array_merge($user, $newU); setUser($ud, $newU); unset($newU);
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

debug("用户请求再局 F=$fd U=$ud R=$rd T=$td show=$show");

$table = $this->model->setSeatReady($table, $sd, $show);

//通知牌桌: 我已准备
$cmd = 5; $code = 1003; $send = array('readyId'=>$sd);
$res = $this->model->sendToTable($table, $cmd, $code, $send, __LINE__);
$newT["seat{$sd}double"] = 0;
$res = $this->model->setTableInfo($td, $newT);

//检测开局
if ( $table['seat0state'] == 16 && $table['seat1state'] == 16 && $table['seat2state'] == 16 )
{
	// //事件 牌桌开始
	// $sceneId = $td;
	// $act = "GAME_ALL_READY";
	// $params = array('tableId'=>$td);
	// $delay = 0;
	// $hostId = $table['hostId'];
	// setTimer($sceneId, $act, $params, $delay, $hostId);
	$res = $this->GAME_ALL_READY($table, 0);
}


end:{
	// $this->model->getRecord()->action($accode, $rd, $td, $ud, $user);
}
