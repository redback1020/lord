<?php

//每周日的23点50分 	统计周期赛事，并执行发奖
$now = time();
foreach ( $this->rooms as $rd=>$room )
{
	$md = $room['modelId'];
	if ( $md != 1 ) continue;
	//每天都重载一下配置
	$rooms = $this->model->getModelRooms($md,1);
	if ( !$rooms ) continue;
	$room = $rooms[array_rand($rooms)];
	$room = $rooms['1_1004'];//暂时强制为初级竞技场，增大参与量
	$rd = $room['roomId'];
	$rd = 0;//实际上为0；后续扩展时，需要严格注意这个roomId在什么情况下为0什么情况下不为0！！！！！！！！！
	$weeks = $this->model->getModelWeeks($md,$rd);
	if ( !$weeks ) continue;
	krsort($weeks);
	$week = reset($weeks);
	$weekId = $week['weekId'];
	// if ( $now < strtotime("2016-03-08") ) {
	// 	//每周发奖失败时，模拟重新发奖
	// 	$weekId = 20160229;
	// 	$gameId = 1401;
	// 	$game = $this->model->getModelGame($md, $rd, $weekId, $gameId);
	// 	$week['weeksId'] = '1_0_20160229';
	// 	$week['modelId'] = 1;
	// 	$week['roomId'] = 0;
	// 	$week['weekId'] = 20160229;
	// 	$week['weekPool'] = $game['weekPool'];
	// 	$week['weekRank'] = $game['thisWeekRank'];
	// } else {
		$game = $this->model->getModelRoomWeekGameLast($md,$rd,$weekId);
		if ( !$game ) continue;
	// }
	$gamesId = $game['gamesId'];
	//每次执行的时候，都要清理一次用户最后一场的参赛信息
	if ( !$game['gameIsOpen'] && $game['gamePlay'] )
	{
		//加事务锁	CLEAR_MODEL_GAME_.$gamesId
		$res = setLock('CLEAR_MODEL_GAME_'.$gamesId, 1);
		if ( $res )
		{
			$gamePlayAll = $this->model->getModelGamePlayAll($gamesId);
			$gamePlayAll = ( is_array($gamePlayAll) && $gamePlayAll ) ? $gamePlayAll : array();
			foreach ( $gamePlayAll as $k=>$v )
			{
				//删除参赛用户
				$res = $this->model->delModelGamePlay($game,$v);
				//通知用户: 报名人数不齐
				$uid = $v['uid'];
				$cmd = 5; $code = 104;
				$data['errno'] = 8;
				$data['error'] = "报名人数不齐，请等待下一场开放。\n报名费用已经返还到您的账户。";
				$data['coins'] = $v['coins']+$game['gameInCoins'];
				$res = $this->model->sendToUser( $uid, $cmd, $code, $data );
			}
			//解事务锁	CLEAR_MODEL_GAME_.$gamesId
			$res = delLock('CLEAR_MODEL_GAME_'.$gamesId);
		}
	}
	// if ( $now < strtotime("2016-03-08") ) {
	// 	//每周发奖失败时，模拟重新发奖
	// } else {
		if ( ($week['weekEnd'] - $now) > 900 || ($week['weekEnd'] - $now) < 0 ) continue;//还没有到周末结算
	// }
	debug("赛周结算开始[$md|$rd|$weekId]");
	$weekPlayAll = $this->model->getModelWeekPlayAll($md,$rd,$weekId);
	$weekPlayAll = $weekPlayAll ? $weekPlayAll : array();
	$weekPlay = $weekRank = array();
	foreach ( $weekPlayAll as $k=>$v )
	{
		$weekPlay[$v['uid']] = $v;
		//删除赛周用户的redis数据
		$this->model->delModelWeekPlay($md,$rd,$weekId,$v['uid']);
	}
	foreach ( $week['weekRank'] as $k=>$v )
	{
		$weekRank[$v['uid']] = $v['rank'];
	}
	//处理赛周奖励
	$prizeCoins = array();
	foreach ( $room['weekPrizeCoins'] as $k=>$v )
	{
		$r = explode('-',$k);
		$i=$r[0];
		$j=isset($r[1]) ? $r[1] : $i;
		for ( ; $i <= $j; $i++)
		{
			$prizeCoins[$i] = $v;
		}
	}
	$prizeProps = array();
	foreach ( $room['weekPrizeProps'] as $k=>$v )
	{
		$r = explode('-',$k);
		$i=$r[0];
		$j=isset($r[1]) ? $r[1] : $i;
		for ( ; $i <= $j; $i++)
		{
			$prizeProps[$i] = $v;
		}
	}
	$weekPrize = $weekPrizeCoins = $weekPrizeProps = array();
	foreach ( $weekRank as $uid=>$rank )
	{
		if ( isset($prizeCoins[$rank]) ) $weekPrizeCoins[$uid] = $weekPrize[$uid]['coins'] = $prizeCoins[$rank];
		else break;
	}
	foreach ( $weekRank as $uid=>$rank )
	{
		if ( isset($prizeProps[$rank]) ) $weekPrizeProps[$uid] = $weekPrize[$uid]['items'] = $prizeProps[$rank];
		else break;
	}
	foreach ( $weekRank as $uid=>$rank )
	{
		if ( isset($prizeCoins[$rank]) || isset($prizeProps[$rank]) ) $weekPrize[$uid]['rank'] = $rank;
		else break;
	}
	foreach ( $weekPrize as $uid=>$data )
	{
		if ( !isset($data['coins']) ) $weekPrize[$uid]['coins'] = 0;
		if ( !isset($data['items']) ) $weekPrize[$uid]['items'] = array();
	}
	//更新赛周信息
	$week['weekPrizeCoins'] = $weekPrizeCoins;
	$week['weekPrizeProps'] = $weekPrizeProps;
	$res = $this->model->setModelWeek($md,$rd,$weekId,$week);
	foreach ( $weekPrizeCoins as $uid=>$v )
	{
		$weekPlay[$uid]['weekRank'] = isset($weekRank[$uid])?$weekRank[$uid]:0;
		$weekPlay[$uid]['weekPrizeCoins'] = $v;
		$weekPlay[$uid]['update_time'] = $now;
	}
	foreach ( $weekPrizeProps as $uid=>$v )
	{
		$weekPlay[$uid]['weekRank'] = isset($weekRank[$uid])?$weekRank[$uid]:0;
		$weekPlay[$uid]['weekPrizeProps'] = $v;
		$weekPlay[$uid]['update_time'] = $now;
	}
	//入库赛周信息
	$res = $this->model->insModelWeek($md,$rd,$weekId,$week);
	//入库赛周用户
	$res = $this->model->insModelWeekPlay($md,$rd,$weekId,$weekPlay);
	debug("赛周发奖开始[$md|$rd|$weekId]");
	foreach ( $weekPrize as $uid=>$v )
	{
		$user = $this->model->getUserInfo($uid);
		$user = $user ? $user : array();
		$prize = $v; unset($prize['rank']);
		$res = $this->model->userPrize($uid, $prize, $user, '竞技周奖');
		$user = $user ? getUser($uid) : array();
		$fd = ($user && isset($user['fd'])) ? $user['fd'] : '';
		$cmd = 5; $code = 120; //赛事发奖
		$data = array(
			"errno" => 0,
			"error" => "恭喜您在第{$weekId}周竞技赛中，排第".$v['rank']."名" . ( !$v['coins'] && !$v['items'] ? "。" : ": " ) . ($v['items']?("\n奖励道具".join('、',$v['items'])):"") . ($v['coins']?("\n奖励乐豆".$v['coins']):"") ,
			"modelId" => $md,
			"weekId" => $weekId,
			"rank" => $v['rank'],	//用户本周排名
			"coins" => $v['coins'],	//用户奖励筹码
			"props" => $v['items'],	//用户奖励道具
		);
		if ( $v['rank'] == 1 ) {
			if ( !$user || !isset($user['nick']) ) {
				$user = $this->model->getUserData($uid);
				if ( !$user ) {
					$user = $this->model->getRobotData($uid);
				}
			}
			$nick = $user && isset($user['nick']) ? $user['nick'] : '乐乐';
			$res = sendHorn("恭喜·{$nick}·获得第{$weekId}周竞技赛第一名！", 1);
		}
		if ( $user && $fd )
		{	//即刻通知
			$data['coins'] = $user['coins'];//用户当前筹码
			$data['props'] = $user['propDress'];//用户当前服装
			$res = sendToFd( $fd, $cmd, $code, $data);
		}
		else
		{	//写表lord_game_usermsg
			$data['act'] = 'USER_ALERT';
			$data['cmd'] = $cmd;
			$data['code'] = $code;
			$res = $this->model->insUserMsg($uid, $data);
		}
	}
	debug("赛周发奖完毕[$md|$rd|$weekId]");
}
//
// goto end;

end:{}
