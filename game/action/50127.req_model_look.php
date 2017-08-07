<?php

$code = 102;
$errno = 0;
$error = '操作成功。';

$is_back = 0;

//参数整理
// $md = isset($params['modelId']) ? intval($params['modelId']) : 0;//赛事id
// $rd = isset($params['roomId']) ? intval($params['roomId']) : 0;	//房间id
$md = 1;
$rd = 0;
$wd = intval(date("Ymd",time()-(date("N")-1)*86400));
$now = time();

if ( $md != 1 || ($rd && !in_array($rd,array_keys($this->rooms))) )
{
	$errno = 1;
	$error = '操作无效。';
	$this->model->sendError($fd,$cmd,$code,$errno,$error);
	gerr("竞技参数无效[$fd|?|$md|$rd|$wd]client-".__LINE__."");
	goto end;
}

$ud = $user['uid'];
$td = $user['tableId'];

//用户已在游戏，使用已在的房间及模式，提示用户是否继续
if ( $is_back && $td ) {
	$param = array(
		'modelId' => $user['modelId'],
		'roomId' => $user['roomId'],
		'isContinue' => 0,
	);
	require(ROOT."/action/50000.user_into_room.php");
	goto end;
}

//获取本周最新场次
$game = $this->model->getModelRoomWeekGameLast($md,$rd,$wd);
if ( !$game )
{
	$errno = 2;
	$error = "操作失败，请稍候重试。";
	$this->model->sendError($fd,$cmd,$code,$errno,$error);
	debug("赛事查看失败[$fd|$ud|$md|$rd|$wd]client-".__LINE__."");
	goto end;
}
$gameId = $game['gameId'];
$gamesId = $game['gamesId'];
$beJoin = $beQuit = $joinTime = $myWeekPoint = $myWeekRank = 0;

if ( $user['gameId'] )
{	//已入赛场
	if ( $gameId == $user['gameId'] )
	{	//已报本场
		//获取用户赛场参与情况
		$gameplay = $this->model->getModelGamePlay($gamesId,$ud);
		if ( !$gameplay )
		{
			$beJoin = $beQuit = 0;//
		}
		else
		{
			$beJoin = 0;
			$beQuit = intval(($now - $gameplay['joinTime']) > $game['gameCancelTime'] && ($game['gamePersonAll'] - $game['gamePerson']) > $game['gameCancelPerson']);
			$joinTime = $gameplay['joinTime'];
		}
	}
	else
	{	//已报他场
		$beJoin = $beQuit = 0;
	}
}
else
{	//没入赛场
	if ( $game['gameIsOpen'] && $game['gamePerson'] <  $game['gamePersonAll'] )
	{
		$beJoin = 1;
	}
}
//获取用户赛周参与情况
$weekPlay = $this->model->getModelWeekPlay($md,$rd,$wd,$ud);
if ( $weekPlay )
{
	$myWeekPoint = $weekPlay['weekPoint'];
	$myWeekRank = $weekPlay['weekRank'];
}

//debug("赛事浏览[$fd|$ud|$md|$rd|$wd]");

//通知用户: 赛事浏览
$data = array(
	"errno" => $errno,
	"error" => $error,
	"modelId" => $md,
	//"roomId" => $rd,
	"modelPool" => $game['weekPool'],	//本周总奖池乐豆数
	"weekId" => $wd,				//场次编号(年+周)
	"gameId" => $gameId,				//场次编号(本周第n次)
	"gameLevel" => $game['gameLevel'],	//场次等级
	"gameOpen" => $game['gameOpen'],	//开放时间
	"gameIsOpen" => $game['gameIsOpen'],//是否开放
	"gamePool" => $game['gamePool'],	//本场奖池
	"gamePersonAll" => $game['gamePersonAll'],	//所需人数
	"gamePerson" => $game['gamePerson'],		//当前人数
	"gameInCoins" => $game['gameInCoins'],		//报名费用
	"is_onsale" => isset($game['is_onsale'])?$game['is_onsale']:0,	//是否促销
	"gamePrizeCoins" => $game['gamePrizeCoins'],//奖励乐豆
	"gamePrizeCoupon" => isset($game['gamePrizeCoupon'])?$game['gamePrizeCoupon']:array( '1' => 888, '2-4' => 388, '5-9' => 188 ),//奖励奖券
	"gamePrizePoint" => $game['gamePrizePoint'],//奖励积分
	"gamePrizeProps" => $game['gamePrizeProps'],//奖励道具
	"gameRule" => $game['gameRule'],			//游戏规则描述
	"beJoin" => $beJoin,						//此用户是否可以报名
	"beQuit" => $beQuit,						//此用户是否可以取消报名
	"joinTime" => $joinTime,					//此用户在n秒之前已报名
	"myWeekPoint" => $myWeekPoint,				//此用户的本周积分
	"myWeekRank" => $myWeekRank,				//此用户的本周排名(0未进入积分排名)
	"lastWeekRank" => $game['lastWeekRank'],	//上周积分排名
	"thisWeekRank" => $game['thisWeekRank'],	//本周积分排名
);
$res = sendToFd( $fd, $cmd, $code, $data);

//检测开场
if ( !$game['gameIsOpen'] && $game['gamePlay'] )
{
	//加事务锁
	$lockId = 'CLEAR_MODEL_GAME_'.$gamesId;
	$res = setLock($lockId, 1);
	if ( !$res ) {
		goto end;
	}
	$gameplayAll = $this->model->getModelGamePlayAll($gamesId);
	if ( $gameplayAll )
	{
		foreach ( $gameplayAll as $k=>$v )
		{
			//删除参赛用户
			$res = $this->model->delModelGamePlay($game,$v);
			//通知用户: 报名人数不齐
			$ud = $v['uid'];
			$code = 104;
			$data['errno'] = 8;
			$data['error'] = "报名人数不齐，请等待下一场开放。\n报名费用已经返还到您的账户。";
			$data['coins'] = $v['coins']+$game['gameInCoins'];
			$res = $this->model->sendToUser( $ud, $cmd, $code, $data );
		}
	}
	//解事务锁
	$res = delLock($lockId);
}


end:{
	$this->model->record->action($accode, $rd, $td, $ud, $user);
}
