<?php

$code = 104;
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
	gerr("报名参数无效[$fd|?|$md|$rd|$wd]");
	goto end;
}

$ud = $user['uid'];
$td = $user['tableId'];

//赛事冲突
// if ( $td || ($user['modelId'] && $user['modelId'] != $md) )
if ( $td ) {
	$res =$this->ACT_MODEL_UNIQUE( $fd, $ud, $md, $rd, $user['modelId'] );
	goto end;
}

//获取本周最新场次
$game = $this->model->getModelRoomWeekGameLast($md,$rd,$wd);
if ( !$game )
{
	$errno = 2;
	$error = "报名失败，请稍候重试。";
	$this->model->sendError($fd,$cmd,$code,$errno,$error);
	debug("报名赛查失败[$fd|$ud|$md|$rd|$wd]");
	goto end;
}
$gameId = $game['gameId'];
$gamesId = $game['gamesId'];

//加事务锁	ENROLL_CANCEL_$gamesId
$lockId = 'ENROLL_CANCEL_'.$gamesId;
$res = setLock($lockId);
if ( !$res ) {
	goto end;
}

//!!!重新获取本场次信息
$gameIsOpen = $game['gameIsOpen'];
$game = $this->model->getModelGame($md,$rd,$wd,$gameId);
if ( !$game )
{
	$errno = 2;
	$error = "报名失败，请稍候重试。";
	$this->model->sendError($fd,$cmd,$code,$errno,$error);
	debug("报名赛查失败[$fd|$ud|$md|$rd|$wd]");
	//解事务锁
	$res = delLock($lockId);
	goto end;
}

// 下面的日期范围(含)之间，报名费用降低至1000乐币
if ( time() > strtotime("2015-03-23") && time() <= (strtotime("2015-04-19")+86400) ) $game['gameInCoins'] = 1000;

$game['gameIsOpen'] = $gameIsOpen;
//等待开放
if ( !$game['gameIsOpen'] )
{
	$errno = 3;
	$error = "竞技场已更新成比赛场，请升级最新版本体验精彩赛事。";
	$this->model->sendError($fd,$cmd,$code,$errno,$error);
	debug("报名赛场未开[$fd|$ud|$gamesId]");
	//解事务锁
	$res = delLock($lockId);
	goto end;
}
//人数满员
if ( $game['gamePerson'] >= $game['gamePersonAll'] )
{
	$errno = 4;
	$error = "报名人数满员，请稍候下一场。";
	$this->model->sendError($fd,$cmd,$code,$errno,$error);
	debug("报名赛场满员[$fd|$ud|$gamesId]");
	//解事务锁
	$res = delLock($lockId);
	goto end;
}
//乐豆不够
if ( $user['coins'] < $game['gameInCoins'] )
{
	$errno = 5;
	$error = "您的乐豆不够报名当前场次。";
	$this->model->sendError($fd,$cmd,$code,$errno,$error);
	debug("报名用户钱少[$fd|$ud|$gamesId]");
	//解事务锁
	$res = delLock($lockId);
	goto end;
}
//重复报名
if ( $this->model->getModelGamePlay($gamesId,$ud) )
{
	$errno = 6;
	$error = "不能重复报名。";
	$this->model->sendError($fd,$cmd,$code,$errno,$error);
	debug("报名用户重复[$fd|$ud|$gamesId]");
	//解事务锁
	$res = delLock($lockId);
	goto end;
}

//加入报名
$user['modelId'] = 1;
$gameNew = $this->model->addModelGamePlay($game,$user);

if ( !$gameNew )
{
	$errno = 7;
	$error = "操作失败，请稍候重试。";
	$this->model->sendError($fd,$cmd,$code,$errno,$error);
	debug("报名执行失败[$fd|$ud|$gamesId]");
	//解事务锁
	$res = delLock($lockId);
	goto end;
}

debug("赛场报名成功[$fd|$ud|$gamesId]");

//通知用户: 赛事报名
$data = array(
	"errno" => 0,
	"error" => "报名成功",
	"modelId" => $md,
	//"roomId" => $rd,
	"weekId" => $wd,			//场次编号(年+周)
	"gameId" => $gameId,			//场次编号(本周第n次)
	"gameInCoins" => $game['gameInCoins'],	//报名费coins
	"coins" => $user['coins']-$game['gameInCoins'],//处理完毕后的coins
);
$res = sendToFd( $fd, $cmd, $code, $data);

//检查报名是否已满
if ( $gameNew['gamePerson'] >= $gameNew['gamePersonAll'] )
{
	$this->ACT_MODEL_CHECK($gameNew);
}

//解事务锁
$res = delLock($lockId);


end:{
	$this->model->record->action($accode, $rd, $td, $ud, $user);
}
