<?php

//出牌

//参数整理
if ( ! isset($params['sendCards']) || ! $params['sendCards'] ) {
	closeToFd($fd, "出牌参数无效 params=".json_encode($params));
	goto end;
}
//用户信息
$ud = $user['uid'];
$rd = $user['roomId'];
$td = $user['tableId'];
$sd = $user['seatId'];
//校验房间
if ( ! isset($this->rooms[$rd]) || ! $td ) {
	debug("出牌用户失效 F=$fd U=$ud R=$rd T=$td roomId=$rd");
	goto end;
}
//获取牌桌
$table = $this->model->getTableInfo($td);
if ( !$table ) {
	debug("出牌牌桌失效 F=$fd U=$ud R=$rd T=$td params=".json_encode($params));
	goto end;
}
//校验牌桌状态、席位轮流、用户托管
elseif ( $table['state'] != 6 || $table['turnSeat'] != $sd || $table['seat'.$sd.'trust'] ) {
	debug("出牌网络延迟 F=$fd U=$ud R=$rd T=$td state6=".$table['state']." turn".$table['turnSeat']."=".$sd." trust=".$table['seat'.$sd.'trust']);
	goto end;
}
//校验手牌
elseif ( ! $table["seat0cards"] || ! $table["seat1cards"] || ! $table["seat2cards"] ) {
	gerr("出牌手牌无效 F=$fd U=$ud R=$rd T=$td table=".json_encode($table));
	goto end;
}
$md = $table['modelId'];//0经典场|1竞技场|2赖子场|3比赛场
$rd = $table['roomId'];//重置
if ( $md == 2 && $table['joker'] && isset($params['jokto']) && $params['jokto'] ) {
	$jokto = array_values($params['jokto']);
	$joker = $table['joker'];
} else {
	$jokto = array();
	$joker = '';
}
//牌组解析
$cardsRet = Card::cardsParse(array_values($params['sendCards']), $jokto, $joker);
$cardsType = intval($cardsRet['t']);//牌型编号
$cardsLen = $cardsRet['l'];//牌组长度
$cardsValue = $cardsRet['v'];//牌组取值
$cardsPlay = $cardsRet['plays'];//扮演牌组
$cardsReal = $cardsRet['reals'];//实际牌组
$jokto = $cardsRet['jokto'];//赖子角色
//牌型检测
if ( $cardsType < 1 ) {
	$cmd = 5; $code = 1020; $send = array();
	$this->model->sendToUser($user, $cmd, $code, $send);
	goto end;
}
//牌库检测
if ( array_diff($cardsReal, $table["seat{$sd}cards"]) ) {
	$cmd = 5; $code = 1020; $send = array();
	$this->model->sendToUser($user, $cmd, $code, $send);
	goto end;
}
//大小检测	有前牌、非自己、且牌小
if ( $table['lastCards'] && $table['lastCall'] != $sd && Card::cardsCompare(Card::cardsDec(Card::cardsToNew($table['lastCards'])), Card::cardsDec(Card::cardsToNew($cardsPlay)), $table['lastJokto'], $jokto) != 2 ) {
	$cmd = 5; $code = 1013; $send = array();
	$this->model->sendToUser($user, $cmd, $code, $send);
	goto end;
}
//记牌器
if ( $md == 0 || $md == 2 ) {
	$_outs = Card::cardsToNew($cardsReal);
	$noteC = str_split($table['noteCards']);
	$cVF = array('f'=>'S','e'=>'M','d'=>'2','c'=>'A','b'=>'K','a'=>'Q','9'=>'J','8'=>'T','7'=>'9','6'=>'8','5'=>'7','4'=>'6','3'=>'5','2'=>'4','1'=>'3');
	$cFI = array_values($cVF);
	foreach ( $_outs as $k => $v ) { $noteC[array_search($cVF[$v[1]], $cFI) * 2 + 1]-=1; }
	$noteCards = $newT['noteCards'] = join($noteC);
} else {
	$noteCards = '';
}
debug("用户选择出牌 F=$fd U=$ud R=$rd T=$td notes=$noteCards cards=".join($cardsReal));
//通知牌桌: 某人出牌
$cmd = 5; $code = 1017; $send = array('callId'=>$sd, 'sendCards'=>$cardsReal, 'cardType'=>$cardsType, 'noteCards'=>$noteCards);
if ( $md == 2 ) $send['jokto'] = $jokto;
$this->model->sendToTable($table, $cmd, $code, $send);

//牌局任务 检查
if ( $md != 1 )
{
	// $coupon_rate = ( (ISTESTS && strtotime('2016-04-25') < time()) || (! ISTESTS && strtotime('2016-04-28') < time() && time() < strtotime('2016-05-05') ) ) ? 2 : 1;
	$coupon_rate = 1;
	foreach ( $table['seats'] as $uid => $sid )
	{
		if ( $ud != $uid ) continue;
		$task = $table["seat{$sid}task"];
		if ( ! $task || (isset($task['is_done']) && $task['is_done']) ) continue;
		$task = Card::checkTaskDone($cardsPlay, $task);
		if ( ! $task['is_new'] ) continue;
		$newT["seat{$sid}task"] = $table["seat{$sid}task"] = $task;
		$task['coupon'] = intval($task['coupon'] * $coupon_rate);
		if ( isset($task['is_done']) && $task['is_done'] ) {
			$newT["seat{$sid}tttimes"] = $table["seat{$sid}tttimes"] = $table["seat{$sid}tttimes"] + 1;
			$newT["seat{$sid}ttdone"] = $table["seat{$sid}ttdone"] = 1;
			$newT["seat{$sid}task"] = $table["seat{$sid}task"] = array();
			$player = array('fd'=>$table["seat{$sid}fd"], 'uid'=>$uid, 'tableId'=>$td);
			$cmd = 5; $code = 1022; $send = array('table_task'=>$task['name'], 'coupon'=>$task['coupon'], 'is_done'=>1);
			$res = $this->model->sendToPlayer($player, $cmd, $code, $send);
			$res = $this->model->incUserTask($uid, array('coupon_all'=>$task['coupon']));
			$res = $this->model->incUserTesk($uid, array('tttimes'=>1));
			$res = $this->model->incUserInfo($uid, array('coupon'=>$task['coupon'], 'tttimes'=>1));
			$cmd = 4; $code = 110; $send = $res['send'];
			$this->model->sendToPlayer($player, $cmd, $code, $send);
			$newT["seat{$sid}coupon"] = $table["seat{$sid}coupon"] = $table["seat{$sid}coupon"] + $task['coupon'];
			$newT["seat{$sid}ttcoupon"] = $table["seat{$sid}ttcoupon"] = $task['coupon'];
			$user = $this->model->getTableUser($table, $sid);
			$this->model->getRecord()->money('牌局任务', 'coupon', $task['coupon'], $uid, $user);
		}
	}
}
//牌局任务 完毕

//更新牌型倍率
$rate = isset($this->confs['rate_cardstype'.$cardsType]) ? $this->confs['rate_cardstype'.$cardsType] : 1;
if ( $rate > 1 ) $newT['rate'] = $table['rate'] = $this->TABLE_NEW_RATE($table, $sd, $rate);
//增加牌桌废牌
$newT['outCards'] = $table['outCards'] = is_array($table['outCards']) ? array_merge($table['outCards'], $cardsReal) : $cardsReal;
//减少用户手牌
$newT["seat{$sd}cards"] = $table["seat{$sd}cards"] = array_values(array_diff($table["seat{$sd}cards"], $cardsReal));
//累加出牌次数
$newT["seat{$sd}sent"] = ++$table["seat{$sd}sent"];
//轮转下家席位
$newT['turnSeat'] = $table['turnSeat'] = $this->model->getSeatNext($sd);
//当前叫牌席位
$newT['lastCall'] = $table['lastCall'] = $sd;
//当前叫牌内容
$newT['lastCards'] = $table['lastCards'] = $cardsPlay;
//当前叫牌扮演
$newT['lastJokto'] = $table['lastJokto'] = $jokto;
//当前叫牌牌型
$newT['lastType'] = $table['lastType'] = $cardsType;
//重设不跟次数
$newT['noFollow'] = $table['noFollow'] = 0;
//更新牌桌信息
$res = $this->model->setTableInfo($td, $newT);

//新版任务: 打出特殊牌型
if ( $md != 1 )
{
	$_num = in_array($cardsType, array('87','88','89','99')) ? '88' : (in_array($cardsType, array('8','9','10')) ? '8910' : ($cardsType=='7' ? ('7'.count($cardsReal)) : ''));
	if ( ! $_num && $cardsType == 1 ) {
		$_send = $cardsReal[0];
		$_num = in_array($_send, array('12','22','32','42')) ? '1_2' : '';
	}
	if ( $_num ) {
		$tesk = new tesk($this->mysql, $this->redis, $accode, $action);
		$utesk = array();
		$param = 1;
		if ( $addU = $tesk->execute('user_pct_'.$_num, $user, $utesk, $param, $table) ) {
			foreach ( $addU as $k => $v ) $this->model->getRecord()->money('动态任务', $k, $v, $ud, $user);
			if ( ($res = $this->model->incUserInfo($ud, $addU)) && $res['send'] ) sendToFd($fd, 4, 110, $res['send']);
		}
	}
}

//检测手牌出完 执行GAME_OVER，并return
if ( ! count($table["seat{$sd}cards"]) ) {
		if ( $md == 3 ) $res = $this->MATCH_GAME_OVER($table);
	elseif ( $md == 1 ) $res = $this->MODEL_GAME_OVER($table);
	else $res = $this->TABLE_GAME_OVER($table);
	goto end;
}

//轮到下家打牌
$res = $this->TURN_PLAY_CARD($table);


end:{
	// $this->model->record->action($accode, $rd, $td, $ud, $user);
}
