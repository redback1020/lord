<?php

$code = 106;
$errno = 0;
$error = '操作成功。';

//参数整理
// $md = isset($params['modelId']) ? intval($params['modelId']) : 0;//赛事id
// $rd = isset($params['roomId']) ? intval($params['roomId']) : 0;	//房间id
$md = 1;
$rd = 0;
$wd = intval(date("Ymd",time()-(date("N")-1)*86400));
$now = time();

if ( $md != 1 || ($rd && !isset($this->rooms[$rd])) ) {
	$errno = 1;
	$error = '操作无效。';
	$this->model->sendError($fd,$cmd,$code,$errno,$error);
	gerr("消报参数无效[$fd|?|$md|$rd]");
	goto end;
}

$ud = $user['uid'];
$td = $user['tableId'];

//赛事冲突
if ( $td || ($user['modelId'] && $user['modelId'] != $md) )
{
	$res =$this->ACT_MODEL_UNIQUE( $fd, $ud, $md, $rd, $user['modelId'] );
}

//获取本周最新场次
$game = $this->model->getModelRoomWeekGameLast($md,$rd,$wd);
if ( !$game )
{
	$errno = 2;
	$error = "取消报名失败，请稍候重试。";
	$this->model->sendError($fd,$cmd,$code,$errno,$error);
	debug("消报赛查失败[$fd|$ud|$md|$rd|$wd]client-".__LINE__."");
	goto end;
}
$gameId = $game['gameId'];
$gamesId = $game['gamesId'];

//加事务锁
$lockId = 'ENROLL_CANCEL_'.$gamesId;
$res = setLock($lockId);
if ( !$res )
{
	goto end;
}

//!!!重新获取本场次信息
$gameIsOpen = $game['gameIsOpen'];
$game = $this->model->getModelGame($md,$rd,$wd,$gameId);
if ( !$game )
{
	$errno = 2;
	$error = "取消报名失败，请稍候重试。";
	$this->model->sendError($fd,$cmd,$code,$errno,$error);
	debug("消报赛查失败[$fd|$ud|$md|$rd|$wd]client-".__LINE__."");
	//解事务锁
	$res = delLock($lockId);
	goto end;
}

// 下面的日期范围(含)之间，报名费用降低至1000乐币
if ( time() > strtotime("2015-03-23") && time() <= (strtotime("2015-04-19")+86400) ) $game['gameInCoins'] = 1000;

$game['gameIsOpen'] = $gameIsOpen;
$gameplay = $this->model->getModelGamePlay($gamesId,$ud);
if ( !$gameplay || !(isset($gameplay['gamesId']) && $gameplay['gamesId']) )
{
	$errno = 3;
	$error = "您还没有报名，不能取消。\n如果确已报名，请稍等比赛开始。";
	$this->model->sendError($fd,$cmd,$code,$errno,$error);
	debug("消报用户未报[$fd|$ud|$gamesId]client-".__LINE__."");
	//解事务锁
	$res = delLock($lockId);
	goto end;
}
if ( ($now-$gameplay['joinTime']) < $game['gameCancelTime'] )
{
	$errno = 4;
	$error = "请稍等一会儿，再取消。";
	$this->model->sendError($fd,$cmd,$code,$errno,$error);
	debug("消报用户刚报[$fd|$ud|$gamesId]client-".__LINE__."");
	//解事务锁
	$res = delLock($lockId);
	goto end;
}
if ( $game['gameStart'] > 0 || $game['gamePerson'] >= $game['gamePersonAll'] || $game['gamePerson'] >= ($game['gamePersonAll']-$game['gameCancelPerson']) )
{
	$errno = 5;
	$error = "游戏马上开始，请稍候。";
	$this->model->sendError($fd,$cmd,$code,$errno,$error);
	debug("消报赛场即开[$fd|$ud|$gamesId]client-".__LINE__."");
	//解事务锁
	$res = delLock($lockId);
	goto end;
}

//取消报名
$gameNew = $this->model->delModelGamePlay($game,$user);

//解事务锁
$res = delLock($lockId);

if ( !$gameNew )
{
	$errno = 6;
	$error = "取消报名失败，请稍候重试。";
	$this->model->sendError($fd,$cmd,$code,$errno,$error);
	debug("消报执行失败[$fd|$ud|$gamesId]client-".__LINE__."");
	goto end;
}

debug("赛场消报成功[$fd|$ud|$gamesId]client-".__LINE__."");

//通知用户: 赛事报消
$data = array(
	"errno" => $errno,
	"error" => $error,
	"modelId" => $md,
	//"roomId" => $rd,
	"weekId" => $wd,			//场次编号(年+周)
	"gameId" => $gameId,			//场次编号(本周第n次)
	"gameInCoins" => $game['gameInCoins'],	//报名费coins
	"coins" => $user['coins']+$game['gameInCoins'],//处理完毕后的coins
);
$res = sendToFd( $fd, $cmd, $code, $data);


end:{
	$this->model->record->action($accode, $rd, $td, $ud, $user);
}
