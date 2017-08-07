<?php

//用户信息
$ud = $user['uid'];
$td = $user['tableId'];
$sd = $user['seatId'];

//参数整理
$md = isset($params['modelId']) ? intval($params['modelId']) : 0;
$rd = isset($params['roomId']) ? intval($params['roomId']) : 0;
$isBack = isset($params['isContinue']) ? intval($params['isContinue']) : 0;//是否重返牌桌
$isDone = 0;
if ( ! isset($this->rooms[$rd]) ) {
	closeToFd( $fd, "进房参数无效 params=".json_encode($params) );
	goto end;
}
$rdc = $this->rooms[$rd];
$md = $rdc['modelId'];//向后兼容

//用户牌桌数据强制矫正
$T = false;
if ( $td && ! ($T = $this->model->getTableInfo($td)) ) {
	$td = $user['tableId'] = 0;
	setUser($ud,array('tableId'=>0,'seatId'=>0));
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

//竞技赛: 已报名且未开始，不可进入其他场
if ( ! $td && $user['modelId'] == 1 && $user['gameId'] && $user['modelId'] != $md ) {
	$cmd = 5; $code = 122;
	$send = array('errno'=>0,'error'=>"您当前正在竞技场中，请稍候再试。",'modelId'=>$md,'roomId'=>$rd);
	sendToFd($fd, $cmd, $code, $send);
	goto end;
}

//淘汰赛: 已报名且未开始，可以进入其他场，但开赛前10分钟阻断
if ( ! $td && $user['modelId'] == 3 && $user['gameId'] && $user['modelId'] != $md ) {
	if ( $md == 1 ) {
		$cmd = 5; $code = 222;
		$send = array('errno'=>0,'error'=>"游戏版本错误，请升级到最新版。",'modelId'=>$md,'roomId'=>$rd);
		sendToFd($fd, $cmd, $code, $send);
		goto end;
	} elseif ( $G = $this->match->getGame($user['modelId'],0,$user['gameId']) ) {
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
}

//普通场和赖子场
if ( $md == 0 || $md == 2 )
{
	//摇摇乐 跳转房间
    include(ROOT.'/include/data_lucky_shake_conf.php');
    $data_conf = isset($data_lucky_shake_conf[$rd]) ? $data_lucky_shake_conf[$rd] : array();
    if ($data_conf ) {
		//修改 $dd 变量没有定义的bug
		// $times = $this->model->getLuckyShake($ud,$dd,$rd);
        $times = $this->model->getLuckyShake($ud,date("Ymd"),$rd);
        $last_times = $data_conf['day_times'] - $times;
        $last_times = $last_times > 0 ? $last_times : 0;
        $one_cost = $data_conf['one_cost'];
        $rules = array();
        $cmd = 4; $code = 426; $send = array('errno'=>0,'error'=>'','rules'=>$rules,'times'=>$last_times,'one_cost'=> $one_cost);
        $res = sendToFd($fd, $cmd, $code, $send);
    }
    // 校验并重设用户乐豆数量
	if ( ! ($user = $this->model->checkUserCoins($ud, $user)) ) {
		closeToFd($fd, "进房用户失效 data=".json_encode($data));
		goto end;
	}
	$data['modelId'] = $md;
	$data['isSend'] = $user['isSend']; unset($user['isSend']);
	$data['sendCoins'] = $user['sendCoins']; unset($user['sendCoins']);
	$data['sendCoinsTimesToday'] = $user['sendCoinsTimesToday']; unset($user['sendCoinsTimesToday']);
	$data['sendCoinsTimes'] = $user['sendCoinsTimes']; unset($user['sendCoinsTimes']);
	$data['modelId'] = $md;
	$data['roomId'] = $rd;
	$data['coins'] = $user['coins'];
	$data['score'] = $user['score'];
	$data['enterLimit'] = $rdc['enterLimit'];
	$data['enterLimit_'] = $rdc['enterLimit_'];
	$data['type'] = 'into';
	if ( $user['coins'] < $rdc['enterLimit'] ) {		//乐豆少了
		$data['isSmall'] = 1;echo 111;
		$data['type'] = 'into';
		$data['newRoomId'] = 0;
		$cmd = 5; $code = 1027;
		sendToFd($fd, $cmd, $code, $data);
		goto end;
	} elseif ( $rdc['enterLimit_'] > 0 && $user['coins'] > $rdc['enterLimit_']) {	//乐豆多了
		$data['isSmall'] = 0;
		$data['type'] = 'into';
		$data['newRoomId'] = 0;
		$cmd = 5; $code = 1027;
		sendToFd($fd, $cmd, $code, $data);
		goto end;
	}
	// 处理房间内购买记牌器数据
	$id = 5;
	$cost = isset($this->rooms[$rd]) ? $this->rooms[$rd]["cost{$id}"] : 1500;
	$data['buys']['propId'] = 5;				//乐豆道具id
	$data['buys']['propName'] = '记牌器(单次)';	//乐豆道具名称
	$data['buys']['propPrice'] = $cost;//$this->confs['coins_buy_5'];		//乐豆道具价格
	$data['buys']['propNum'] = $this->model->getUserNumItem($ud, 5);//乐豆道具当前剩余数量
	$goodslist = $this->model->getlistGoods($user['channel'], 1);
	$goods = isset($goodslist[$this->confs['table_buy_gd']]) ? $goodslist[$this->confs['table_buy_gd']] : array();
	$data['buys']['goodsId'] = $goods ? $goods['id'] : 0;			//乐币商品id
	$data['buys']['goodsName'] = $goods ? $goods['name'] : '已下架';//乐币商品名称
	$data['buys']['goodsPrice'] = $goods ? $goods['price'] : 0;		//乐币商品价格
	$data['buys']['goodsSec'] = 0;				//乐币商品当前剩余秒数
	$items = $this->model->getuserItem($ud, 1);
	foreach ( $items as $k => $v )
	{
		if ( $v['pd'] != 5 ) continue;
		$data['buys']['goodsSec'] = intval($v['sec'] > 0 ? $v['sec'] : max(0, $v['end'] > 0 ? ($v['end']-time()) : 0));
		if ( ! $data['buys']['goodsSec'] ) {
			if ( $user['vercode'] < 10800 ) {
				$data['buys']['goodsSec'] = 86313600;
			} else {
				$data['buys']['goodsSec'] = -1;
			}
		}
		break;
	}
}

//不在牌桌
if ( ! $td ) {
	// 用户进房成功
	debug("用户进房成功 F=$fd U=$ud R=$rd");
	setUser($ud, array('roomId'=>$rd, 'lastRoomId'=>$rd));//给用户设置房间
	$data['modelId'] = $md;
	$data['roomId'] = $rd;
	$data['isGaming'] = 0;	//不在游戏
	$data['isContinue'] = 0;//不返牌桌
	$data['baseCoins'] = $rdc['baseCoins'];
	$data['rate'] = $rdc['rate'];
	$data['rateMax'] = isset($rdc['rateMax']) ? $rdc['rateMax'] : 0;
	$data['limitCoins'] = $rdc['limitCoins'];
	$data['rake'] = $rdc['rake'];
	$data['gameBombAdd'] = $rdc['gameBombAdd'];
	$cmd = 5; $code = 1015;
	$res = sendToFd($fd, $cmd, $code, $data);
	$isDone = 1;
	goto end;
}

//已在牌桌

//依据用户牌桌，重载房间配置，用于用户返回房间
$md = $T['modelId'];
$rd = $T['roomId'];
if ( ! isset($this->rooms[$rd]) ) {
	closeToFd($fd, "进房参数无效 params=".json_encode($params));
	goto end;
}
$rdc = $this->rooms[$rd];
debug(($isBack ? "用户进房返桌" : "用户进房待返" )." F=$fd U=$ud R=$rd T=$td");

//提示返桌
$cmd = 5;
$code = 1015;
$data['modelId'] = $md;
$data['roomId'] = $rd;
$data['coins'] = $user['coins'];
$data['score'] = $user['score'];
$data['isGaming'] = intval(!$isBack);	//正在游戏
$data['isContinue'] = $isBack;			//重返牌桌
$data['baseCoins'] = $rdc['baseCoins'];
$data['rate'] = $rdc['rate'];
$data['rateMax'] = isset($rdc['rateMax']) ? $rdc['rateMax'] : 0;
$data['limitCoins'] = $rdc['limitCoins'];
$data['rake'] = $rdc['rake'];
$data['gameBombAdd'] = $rdc['gameBombAdd'];
$res = sendToFd($fd, $cmd, $code, $data);

if ( ! $isBack ) goto end;

//处理返桌
if ( ! in_array($T['state'], array(3,4,5,6)) )
{
	debug("返桌网络延迟 F=$fd U=$ud R=$rd T=$td state3456=".$T['state']);
	//竞技场确保踢出
	if ( $md == 1 || $md == 3 ) {
		closeToFd($fd, "返桌操作失效 params=".json_encode($params));
		goto end;
	}
	//进入房间 牌桌已结束
	$code = 1015;//isComplete用于客户端
	$data['modelId'] = $md;
	$data['roomId'] = $rd;
	$data['coins'] = $user['coins'];
	$data['score'] = $user['score'];
	$data['isGaming'] = intval(!$isBack);	//正在游戏
	$data['isContinue'] = $isBack;			//重返牌桌
	$data['baseCoins'] = $rdc['baseCoins'];
	$data['rate'] = $rdc['rate'];
	$data['rateMax'] = isset($rdc['rateMax']) ? $rdc['rateMax'] : 0;
	$data['limitCoins'] = $rdc['limitCoins'];
	$data['rake'] = $rdc['rake'];
	$data['gameBombAdd'] = $rdc['gameBombAdd'];
	$data['isComplete'] = 1;//牌桌已结束
	$res = sendToFd($fd, $cmd, $code, $data);
	$isDone = 1;
	goto end;
}

//更新牌桌
$newT = array();
$newT["seat{$sd}fd"] = $T["seat{$sd}fd"] = $fd;
$newT["seat{$sd}trust"] = $T["seat{$sd}trust"] = 0;
$res = $this->model->setTableInfo($td, $newT);

$code = 1001; $send = array();
$res = $this->model->sendToUser($user, $cmd, $code, $send);

//通道加锁，使牌桌消息缓发
$fdinfo = getBind($fd);
$fdinfo['is_lock'] = 1;
setBind($fd, $fdinfo);

//推送历史
$list = $this->model->getTableHistory($td, 0, -1);
if ( ! $list ) $list = array();
foreach ( $list as $k=>$v )
{
	$v = json_decode($v, 1);
	if ( isset($v['uid']) && $v['uid'] == $ud && isset($v['cmd']) && isset($v['code']) && isset($v['data']) && !in_array($v['code'],array(1025)) )
	{
		if ( $v['code'] == 1004 ) {
			$v['data']['isNewGame'] = 1;
		} elseif ( isset($v['data']['isNewGame']) ) {
			unset($v['data']['isNewGame']);//!!!这个属性一定要这么写，源于最初版本中坑爹的协议设计
		}
		$this->model->sendToPlayer( $user, $v['cmd'], $v['code'], $v['data'], 0);//不存储为历史
	}
}



//通知用户: 推送完毕
$code = 1010;
$send = array();
$this->model->sendToPlayer( $user, $cmd, $code, $send, 0 );//不存储为历史

//通道解锁，使牌桌消息正常
$fdinfo = getBind($fd);
$fdinfo['is_lock'] = 0;
setBind($fd,$fdinfo);

//主动解除托管
$res = $this->USER_DETRUST($fd, $T, $user['seatId'], 0);//不存储为历史

$isDone = 1;

end:{
	if ( $isDone )
	$this->model->getRecord()->action($accode, $rd, $td, $ud, $user);
}
