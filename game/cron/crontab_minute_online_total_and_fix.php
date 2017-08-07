<?php

// 注意⚠： 本脚本因为运行时间，内存占用等因素，不可以放在sweety中执行，只能直接使用linux-crontab

define('TAG_NAME', 'ONLINE_TOTAL_AND_FIX');
require("/data/sweety/conf/cron.php");
$redis = getRedis();
$mysql = getMysql();
function gerr($log){
	return serr($log);
}

if ( !function_exists('closeToFd') ) {
	function closeToFd( $fdx, $desc='' )
	{
		$fd_ = explode("_", $fdx);
		if ( !$fd_ || !is_array($fd_) || count($fd_) !== 3 || !($ufd = intval($fd_[2])) ) return false;
		$desc = is_array($desc) && $desc ? json_encode($desc) : ( $desc ? $desc : '' );
		$hostId = $fd_[0]."_".$fd_[1];
		$route = array("act"=>"KICK", "fd"=>$fdx, "desc"=>$desc);
		global $redis;
		return $redis->ladd('srv_route_'.$hostId, $route);
	}
}

$time = time();
$date = date("Y-m-d H:i:00",$time);
//每天0点30分清理一下竞技场垃圾用户
$is_clean_match = (date("H:i",$time) == "00:30");
//是否是投放机器人时间(每天9点10分到24点)
$is_roboton = intval(!($time > (strtotime(date("Y-m-d 09:00:00"))+600) && $time < strtotime(date("Y-m-d 23:59:59"))));

//获取所有用户
$keys = $redis->keys(KEY_USER_.'*');
// //在线用户(含在桌机器)
// $users = array();
//在线总计(不含牌桌掉线，不含在桌机器)
$onlineNum = 0;
//只在房间
$inroomNum = 0;
//只在大厅
$inlobbyNum = 0;
//在线分布
$data = $closing = array();

$onlineHostNum= array();

//处理在线用户
foreach ( $keys as $k => $key )
{
	$user = $redis->hgetall($key);
	if ( !$user )
	{
	    $redis->del($key);
	    continue;
	}
	//用户
	if ( isset($user['fd']) && count($user) > 28 )
	{
		$fd = $user['fd'];
		$uid = $user['uid'];
		
		//在线用户
		if ( $fd )
		{
			$host = explode("_", $fd);
		    if(!isset($onlineHostNum[$host[0]]))$onlineHostNum[$host[0]] = 0;
		    $onlineHostNum[$host[0]] ++;
		    
			/***
			 *停服安全踢掉 
			 */
			/* if ( ! $user['tableId'] && ! $user['gameStart'] ) {
				$closing[]=$fd;
				continue;
			} */
			//普通场在线用户
			if ( !$user['gameId'] )
			{
				//空闲了半小时的普通场在线用户
				if (!isset($user['last_time']) || $user['last_time'] >0 && ($time - $user['last_time']) > 1800 + 120 )
				{
					//踢掉这些空闲连接，释放对应资源 //如果不踢，请注释下面的代码块
					$fdinfo = $redis->hgetall('srv_bind_'.$fd);
					//连接通道还是本人
					if ( $fdinfo && isset($fdinfo['uid']) && $fdinfo['uid'] == $uid )
					{
						//清理死用户
						if ( ($time - $user['last_time']) > 3600 )
						{
							$redis->del('srv_bind_'.$fd);
							$redis->del($key);
							gerr("[ONLINE]clean-????off-user key=$key user=".(isset($user['last_action']) && $user['last_action'] == 'LOGIN_GUEST' ? 'logining and outing' : json_encode($user)));
						}
						//向连接发起掉线
						else 
						{
							closeToFd($fd, "闲置掉线");
						}
						continue;
					}
					//连接通道已经不是本人
					else
					{
						//直接清理用户
						$redis->del($key);
						gerr("[ONLINE]clean-coderdie-user key=$key user=".json_encode($user));
						continue;
					}
				}
			}
			//竞技场在线用户
			else
			{
				//晚上?点?分，清理一下错误的竞技场在线用户
				if ( $is_clean_match && $user['modelId'] == 1 )
				{
					gerr("[ONLINE]clean-matchdie-user key=$key user=".json_encode($user));
					$redis->del($key);
					continue;
				}
			}
			//累计全部在线用户
			$onlineNum++;
			//累计只在房间的在线用户
			if ( $user['roomId'] && !$user['tableId'] ) {
				$inroomNum++;
			}
			//累计只在大厅的在线用户
			elseif ( !$user['roomId'] && !$user['tableId'] ) {
				$inlobbyNum++;
			}
		}
		//离线用户
		else
		{
			//普通场 离线用户，在闲置半小时后直接清理
			if (!isset($user['last_time']) || !$user['gameId'] && $user['last_time'] >0 && $time - $user['last_time'] > 1800 + 120 )
			{
				gerr('[ONLINE]clean-offdie-user key='.$key.' user='.json_encode($user));
				$redis->del($key);
				$tableId = $user['tableId'];
				if ($tableId) {
					$table = $redis->hgetall('lord_table_info_'.$tableId);
					if (!$table) {
						$redis->del('lord_table_info_'.$tableId);
					}
				}
				continue;
			}
		}
        // $users[]=$user;
	}
	//机器人
	elseif ( isset($user['robot']) && $user['robot'] )
	{
		// 非机器人投放时间，清理一下普通场机器人代码，此代码只适合线上使用
		if ( !$is_roboton && !(isset($user['modelId']) && $user['modelId']) ) {
			$redis->del($key);
		}
	}
	//数据残留
	else
	{	//标记一下用户残留数据，用作全面分析
		gerr('[ONLINE]clean-databad-user key='.$key .' user='.json_encode($user));
		$redis->del($key);
	}
}

//处理在线分布
$tableNames = $redis->keys('lord_table_info*');
$tableHists = $redis->keys('lord_table_hist*');
//$tableNames = $tableNames ? array_flip($tableNames) : array();
//$tableHists = $tableHists ? array_flip($tableHists) : array();
$tables = array_unique(array_merge($tableNames,$tableHists));

$data['统计时间'] = $date;
$data['房间个数'] = 0;
$data['牌桌个数'] = 0;
$data['在线总计'] = $onlineNum;
$data['只在大厅'] = $inlobbyNum;
$data['只在房间'] = $inroomNum;
$data['在桌活跃'] = 0;
$data['在桌掉线'] = 0;
$data['在桌假人'] = 0;
$data['房间详情'] = array();

foreach ($tables as $hkey) {
    $ikey = $hkey;
    if(strpos("history", $hkey))
    {
        $thk = explode('_', $hkey);
        $tik = $thk;
        $tik['2'] = 'info';
        $ikey = join('_',$tik);
        if (count($thk) != 7)
        {
            gerr("[ONLINE]clean-databad-table td=$ikey T=".json_encode($redis->hgetall($ikey)));
            $redis->del($hkey);
            $redis->del($ikey);
        }
    }
    if ( $v = $redis->hgetall($ikey) )
    {
        if ( !isset($v['roomId']) || !$v['roomId'] )
        {
            $redis->del($hkey);
            $redis->del($ikey);
            continue;
        }
        //普通场牌桌
        if ( !$v['gameId'] )
        {
            //15分钟还没打完的牌桌，可以直接清理了。
            if ( isset($v['gameStart']) && $v['gameStart'] > 0 && $v['gameStart'] < (time()-900) )
            {
                gerr("[ONLINE]clean-15mtdie-table tablekey=".$ikey ." table=".json_encode($v));
                $redis->del($hkey);
                $redis->del($ikey);
                continue;
            }
            //下面代码，因为牵涉到同一个桌子可以打很多场的原因，不到服务器故障因素的时候，不可以取消注释
            // elseif ( isset($v['create']) && $v['create'] > 0 && $v['create'] < (time()-3600) )
            // {
            // 	gerr("[ONLINE]clean-15mtdie-table tablekey=".$ikey ." table=".json_encode($v));
            // 	$redis->del($hkey);
            // 	$redis->del($ikey);
            // 	continue;
            // }
        }
        elseif ( isset($v['create']) && $v['create'] > 0 && $v['create'] < (time()-3600) )
        {
            gerr("[ONLINE]clean-15mtdie-table tablekey=".$ikey ." table=".json_encode($v));
            $redis->del($hkey);
            $redis->del($ikey);
            continue;
        }
        //竞技场牌桌
        else
        {
            //晚上?点?分清理一下错误的竞技场牌桌，或者直接清理掉15分钟还没打完的竞技牌桌
            if ( ( $is_clean_match && $v['modelId'] == 1 ) || ( isset($v['gameStart']) && $v['gameStart'] > 0 && $v['gameStart'] < (time()-1800) ) )
            {
                gerr("[ONLINE]clean-matchdie-table tablekey=".$ikey ." table=".json_encode($v));
                $redis->del($hkey);
                $redis->del($ikey);
                if ( $v['gamesId'] ) $redis->hdel('lord_model_games_1', $v['gamesId']);
                continue;
            }
        }
        $table[$ikey] = $v;
    }
    else
    {
        //直接删除残留的牌桌历史纪录
        $redis->del($hkey);
    }
}



$table = array();
foreach ($tableHists as $hkey => $value) {
	$thk = explode('_', $hkey);
	$tik = $thk;
	$tik['2'] = 'info';
	$ikey = join('_',$tik);
	if (count($thk) != 7)
	{
		gerr("[ONLINE]clean-databad-table td=$ikey T=".json_encode($redis->hgetall($ikey)));
		$redis->del($hkey);
		$redis->del($ikey);
	}
	elseif ( $v = $redis->hgetall($ikey) )
	{
		//普通场牌桌
		if ( !$v['gameId'] )
		{
			//15分钟还没打完的牌桌，可以直接清理了。
			if ( isset($v['gameStart']) && $v['gameStart'] > 0 && $v['gameStart'] < (time()-900) )
			{
				gerr("[ONLINE]clean-15mtdie-table tablekey=".$ikey ." table=".json_encode($v));
				$redis->del($hkey);
				$redis->del($ikey);
				continue;
			}
			//下面代码，因为牵涉到同一个桌子可以打很多场的原因，不到服务器故障因素的时候，不可以取消注释
			// elseif ( isset($v['create']) && $v['create'] > 0 && $v['create'] < (time()-3600) )
			// {
			// 	gerr("[ONLINE]clean-15mtdie-table tablekey=".$ikey ." table=".json_encode($v));
			// 	$redis->del($hkey);
			// 	$redis->del($ikey);
			// 	continue;
			// }
		}
		elseif ( isset($v['create']) && $v['create'] > 0 && $v['create'] < (time()-3600) )
		{
			gerr("[ONLINE]clean-15mtdie-table tablekey=".$ikey ." table=".json_encode($v));
			$redis->del($hkey);
			$redis->del($ikey);
			continue;
		}
		//竞技场牌桌
		else
		{
			//晚上?点?分清理一下错误的竞技场牌桌，或者直接清理掉15分钟还没打完的竞技牌桌
			if ( ( $is_clean_match && $v['modelId'] == 1 ) || ( isset($v['gameStart']) && $v['gameStart'] > 0 && $v['gameStart'] < (time()-1800) ) )
			{
				gerr("[ONLINE]clean-matchdie-table tablekey=".$ikey ." table=".json_encode($v));
				$redis->del($hkey);
				$redis->del($ikey);
				if ( $v['gamesId'] ) $redis->hdel('lord_model_games_1', $v['gamesId']);
				continue;
			}
		}
		$data['牌桌个数']++;
		if ( !isset($data['房间详情'][$v['roomId']]) )
		{
		    $data['房间详情'][$v['roomId']]['房间编号'] = $v['roomId'];
		    $data['房间详情'][$v['roomId']]['牌桌个数'] = 0;
		    $data['房间详情'][$v['roomId']]['在桌活跃'] = 0;
		    $data['房间详情'][$v['roomId']]['在桌掉线'] = 0;
		    $data['房间详情'][$v['roomId']]['在桌假人'] = 0;
		}
		$data['房间详情'][$v['roomId']]['牌桌个数']++;
		$j = 0;
		$jj = 0;
		for ( $i=0; $i < 3; $i++ )
		{
		    if ( $v['seat'.$i.'robot'] )
		    {
		        $data['在桌假人']++;
		        $data['房间详情'][$v['roomId']]['在桌假人']++;
		        $j++;
		    }
		    elseif ( $v['seat'.$i.'fd'] )
		    {
		        $data['在桌活跃']++;
		        $data['房间详情'][$v['roomId']]['在桌活跃']++;
		    }
		    else
		    {
		        $data['在桌掉线']++;
		        $data['房间详情'][$v['roomId']]['在桌掉线']++;
		        $j++;
		        $jj++;
		    }
		}
		if ( $j==3 && $jj==3 && !$v['modelId'] && ( ! $v['gameStart'] || $v['gameStart'] < time() - 900 ) ) {
		    gerr("[ONLINE]clean-3offline-table tablekey=".$k ." table=".json_encode($v));
		    $redis->del($k);
		    $redis->del(str_replace('info','history',$k));
		}
	}
	else
	{
		//直接删除残留的牌桌历史纪录
		$redis->del($hkey);
	}
}

foreach ( $tableNames as $k=>$v )
{
	if ( !isset($table[$k]) && ( $info = $redis->hgetall($k) ) ) $table[$k] = $info;
}
$data['统计时间'] = $date;
$data['房间个数'] = 0;
$data['牌桌个数'] = 0;
$data['在线总计'] = $onlineNum;
$data['只在大厅'] = $inlobbyNum;
$data['只在房间'] = $inroomNum;
$data['在桌活跃'] = 0;
$data['在桌掉线'] = 0;
$data['在桌假人'] = 0;
$data['房间详情'] = array();
foreach ( $table as $k=>$v )
{
	if ( !isset($v['roomId']) || !$v['roomId'] )
	{
		$redis->del($k);
		$redis->del(str_replace('info','history',$k));
		continue;
	}
	//普通场牌桌
	if ( !$v['gameId'] )
	{
		//15分钟还没打完的牌桌，可以直接清理了。
		if ( isset($v['gameStart']) && $v['gameStart'] > 0 && $v['gameStart'] < (time()-900) )
		{
			gerr("[ONLINE]clean-15mtdie-table tablekey=".$k ." table=".json_encode($v));
			$redis->del($k);
			$redis->del(str_replace('info','history',$k));
			continue;
		}
		//下面代码，因为牵涉到同一个桌子可以打很多场的原因，不到服务器故障因素的时候，不可以取消注释
		// elseif ( isset($v['create']) && $v['create'] > 0 && $v['create'] < (time()-3600) )
		// {
		// 	gerr("[ONLINE]clean-15mtdie-table tablekey=".$k ." table=".json_encode($v));
		// 	$redis->del($k);
		// 	$redis->del(str_replace('info','history',$k));
		// 	continue;
		// }
	}
	elseif ( isset($v['create']) && $v['create'] > 0 && $v['create'] < (time()-3600) )
	{
		gerr("[ONLINE]clean-15mtdie-table tablekey=".$k ." table=".json_encode($v));
		$redis->del($k);
		$redis->del(str_replace('info','history',$k));
		continue;
	}
	//竞技场牌桌
	else
	{
		//晚上?点?分清理一下错误的竞技场牌桌，或者直接清理掉15分钟还没打完的竞技牌桌
		if ( ( $is_clean_match && $v['modelId'] == 1 ) || ( isset($v['gameStart']) && $v['gameStart'] > 0 && $v['gameStart'] < (time()-1800) ) )
		{
			gerr("[ONLINE]clean-matchdie-table tablekey=".$k ." table=".json_encode($v));
			$redis->del($k);
			$redis->del(str_replace('info','history',$k));
			if ( $v['gamesId'] ) $redis->hdel('lord_model_games_1', $v['gamesId']);
			continue;
		}
	}
	$data['牌桌个数']++;
	if ( !isset($data['房间详情'][$v['roomId']]) )
	{
		$data['房间详情'][$v['roomId']]['房间编号'] = $v['roomId'];
		$data['房间详情'][$v['roomId']]['牌桌个数'] = 0;
		$data['房间详情'][$v['roomId']]['在桌活跃'] = 0;
		$data['房间详情'][$v['roomId']]['在桌掉线'] = 0;
		$data['房间详情'][$v['roomId']]['在桌假人'] = 0;
	}
	$data['房间详情'][$v['roomId']]['牌桌个数']++;
	$j = 0;
	$jj = 0;
	for ( $i=0; $i < 3; $i++ )
	{
		if ( $v['seat'.$i.'robot'] )
		{
			$data['在桌假人']++;
			$data['房间详情'][$v['roomId']]['在桌假人']++;
			$j++;
		}
		elseif ( $v['seat'.$i.'fd'] )
		{
			$data['在桌活跃']++;
			$data['房间详情'][$v['roomId']]['在桌活跃']++;
		}
		else
		{
			$data['在桌掉线']++;
			$data['房间详情'][$v['roomId']]['在桌掉线']++;
			$j++;
			$jj++;
		}
	}
	if ( $j==3 && $jj==3 && !$v['modelId'] && ( ! $v['gameStart'] || $v['gameStart'] < time() - 900 ) ) {
		gerr("[ONLINE]clean-3offline-table tablekey=".$k ." table=".json_encode($v));
		$redis->del($k);
		$redis->del(str_replace('info','history',$k));
	}
}
$data['房间个数'] = count($data['房间详情']);
$data['在线总计'] = $onlineNum = $inlobbyNum + $inroomNum + $data['在桌活跃'];
$playingNum = $data['在桌活跃'];
ksort($data['房间详情']);

// gerr('step 3');

// //先这么存放用来测试 后面如何处理 待续
// //存储在线分布，供后台处理查看
// file_put_contents(ROOT.'/log/onlineTotals.log', json_encode($data));
// $Hi = intval(date('Hi'));
// if ( $Hi > 2010 && $Hi < 2150 ) {
// 	error_log(json_encode($data),3,ROOT.'/log/onlineTotals_'.date("YmdHi").'.log');
// }
// //存储在线用户，供后台处理查看
// file_put_contents(ROOT.'/log/onlinePlayer.log', json_encode($users));unset($users);
// //存储在线牌桌，供随时数据分析
// file_put_contents(ROOT.'/log/onlineTables.log', json_encode($table));unset($table);

//存储在线总计，供后台处理报表
$sql = "INSERT INTO `lord_game_online` (`add_time`,`num`,`playing`) VALUES ('".$date."',".$onlineNum.",".$playingNum.")";
$res = $mysql->runSql(trim($sql));
if ( ! $res ) gerr("[MYSQL] $sql");

//存储在线明细，供后期处理使用
$_d = array();
$ut = strtotime($data['统计时间']);
$_d['dateid'] = $dateid = intval(date("Ymd", $ut));
$_d['dt'] = $dt = intval(date("ymdHi", $ut));
$_d['ut'] = $ut;
$_d['allRoomNum'] = $data['房间个数'];
$_d['allTableNum'] = $data['牌桌个数'];
$_d['allOnline'] = $data['在线总计'];
$_d['allInLobby'] = $data['只在大厅'];
$_d['allInRoom'] = $data['只在房间'];
$_d['allInTableActive'] = $data['在桌活跃'];
$_d['allInTableOffline']= $data['在桌掉线'];
$_d['allInTableRobot']  = $data['在桌假人'];
$roomIds = array(1000,1001,1002,1003,1006,1004,1007,1008,1009,1010,1011,3011,3012,3013);
$end = end($roomIds);
foreach ( $roomIds as $roomId )
{
	$_d["room{$roomId}TableNum"]    = isset($data['房间详情'][$roomId]['牌桌个数']) ? $data['房间详情'][$roomId]['牌桌个数'] : 0;
	$_d["room{$roomId}TableActive"] = isset($data['房间详情'][$roomId]['在桌活跃']) ? $data['房间详情'][$roomId]['在桌活跃'] : 0;
	$_d["room{$roomId}TableOffline"]= isset($data['房间详情'][$roomId]['在桌掉线']) ? $data['房间详情'][$roomId]['在桌掉线'] : 0;
	$_d["room{$roomId}TableRobot"]  = isset($data['房间详情'][$roomId]['在桌假人']) ? $data['房间详情'][$roomId]['在桌假人'] : 0;
}
$sql = "INSERT INTO `lord_online_detail` ( ";
foreach ( $_d as $k => $v ) {
	$sql.= "`$k`" . ( $k != "room{$end}TableRobot" ? ", " : " " );
}
$sql.= ") VALUES ( ";
foreach ( $_d as $k => $v ) {
	$sql.= "$v" . ( $k != "room{$end}TableRobot" ? ", " : " " );
}
$sql.= ")";
$res = $mysql->runSql($sql);
if ( ! $res ) gerr("[MYSQL] $sql");
$_d = array();
$ut = strtotime($data['统计时间']);
$_d['dateid'] = $dateid = intval(date("Ymd", $ut));
$_d['dt'] = $dt = intval(date("ymdHi", $ut));
$_d['ut'] = $ut;
$roomIds = array(3001,3002,3003);
$end = end($roomIds);
foreach ( $roomIds as $roomId )
{
    $_d["room{$roomId}TableNum"]    = isset($data['房间详情'][$roomId]['牌桌个数']) ? $data['房间详情'][$roomId]['牌桌个数'] : 0;
    $_d["room{$roomId}TableActive"] = isset($data['房间详情'][$roomId]['在桌活跃']) ? $data['房间详情'][$roomId]['在桌活跃'] : 0;
    $_d["room{$roomId}TableOffline"]= isset($data['房间详情'][$roomId]['在桌掉线']) ? $data['房间详情'][$roomId]['在桌掉线'] : 0;
    $_d["room{$roomId}TableRobot"]  = isset($data['房间详情'][$roomId]['在桌假人']) ? $data['房间详情'][$roomId]['在桌假人'] : 0;
}
$sql = "INSERT INTO `lord_online_detail` ( ";
foreach ( $_d as $k => $v ) {
    $sql.= "`$k`" . ( $k != "room{$end}TableRobot" ? ", " : " " );
}
$sql.= ") VALUES ( ";
foreach ( $_d as $k => $v ) {
    $sql.= "$v" . ( $k != "room{$end}TableRobot" ? ", " : " " );
}
$sql.= ")";
$res = $mysql->runSql($sql);
if ( ! $res ) gerr("[MYSQL] $sql");

$sql = "SELECT `roomId` FROM `lord_game_room` WHERE `roomId` < 10";
$ret = $mysql->getData($sql);
if ( ! $ret ) $ret = array();
foreach ( $ret as $k => $v )
{
	$roomIds[]= intval($v['roomId']);
}
$roomIds = array_unique($roomIds);
sort($roomIds);
$end = end($roomIds);
$sql = "INSERT INTO `lord_online_room` ( `dateid`, `dt`, `ut`, `roomId`, `tableNum`, `tableActive`, `tableOffline`, `tableRobot` ) VALUES ";
$sql_ = array();
foreach ( $roomIds as $roomId )
{
	$sql__= "($dateid, $dt, $ut, $roomId, ";
	$sql__.= (isset($data['房间详情'][$roomId]['牌桌个数']) ? $data['房间详情'][$roomId]['牌桌个数'] : 0).", ";
	$sql__.= (isset($data['房间详情'][$roomId]['在桌活跃']) ? $data['房间详情'][$roomId]['在桌活跃'] : 0).", ";
	$sql__.= (isset($data['房间详情'][$roomId]['在桌掉线']) ? $data['房间详情'][$roomId]['在桌掉线'] : 0).", ";
	$sql__.= (isset($data['房间详情'][$roomId]['在桌假人']) ? $data['房间详情'][$roomId]['在桌假人'] : 0).")";
	$sql_[] = $sql__;
}
$sql.= join(',', $sql_);
$ret = $mysql->runSql($sql);
if ( ! $ret ) gerr("[MYSQL] $sql");

if ( $closing ) {
	foreach ( $closing as $k => $fd )
	{
		closeToFd($fd, "闲置掉线");
	}
}


$onlineHostNum["time"] = time();
$onlineHostNum["logday"] = date("Y-m-d H:i", time());
$keys = array_keys($onlineHostNum);
$values = array_values($onlineHostNum);
$col = implode("`, `", $keys);
$val = implode("', '", $values);
$sql = "INSERT INTO `lord_host_online` (`$col`) VALUES ('$val');";
$ret = $mysql->runSql($sql);


//各个服务器减压
// // $_num = 0;
// // while ( $_num < 10000 )
// // {
// // 	while ( $sql = $redis->lpop('srv_mysql') )
// // 	{
// // 		$res = $mysql->runSql($sql);
// // 		if ( !$res ) gerr("[ONLINE] srv_mysql $sql");
// // 	}
// // 	usleep(5000);
// // 	$_num++;
// // }
//
// gerr('[ONLINE] done.');




// //特殊处理开始	建议保留	下面日期范围内(含)的23:58分，保存一份当天的榜单数据，并在非周末时清理lord_model_weekplay_1，达到每天结算的效果
// $dt_weekst = "2014-12-31";//(含)
// $dt_weeken = "2015-02-15";//(含)
// // $dt_weekst2 = "2015-03-02";//(含)
// // $dt_weeken2 = "2015-12-31";//(含)
// $dt_weekid = intval(date("Ymd",time()-(date("N")-1)*86400));
// if ( date("H:i",intval($time/60)*60) == "23:58" && $time > strtotime($dt_weekst) && $time <= (strtotime($dt_weeken)+86400) )
// // if ( date("H:i",intval($time/60)*60) == "23:58" && ( ( $time > strtotime($dt_weekst) && $time <= (strtotime($dt_weeken)+86400) ) || ( $time > strtotime($dt_weekst2) && $time <= (strtotime($dt_weeken2)+86400) ) ) )
// {
// 	$weekid = '1_0_'.$dt_weekid;
// 	$weeks = $redis->hgetall('lord_model_weeks_1');
// 	if ( $weeks && is_array($weeks) && isset($weeks[$weekid]['weekRank']) && is_array($weeks[$weekid]['weekRank']) )
// 	{
// 		$contents = "NO.,UID,NICK,POINT"."\n";
// 		$weekrank = $weeks[$weekid]['weekRank'];
// 		foreach ( $weekrank as $k => $v )
// 		{
// 			$contents.= $v['rank'].",".$v['uid'].",".str_replace(',', '', $v['nick']).",".$v['point']."\n";
// 		}
// 		$csvfile = __DIR__."/$date.csv";
// 		file_put_contents($csvfile, $contents);
// 			gerr('[ONLINE]存储周参赛名单a');
// 		$logfile = __DIR__."/$date.log";
// 		file_put_contents($logfile, json_encode($weeks));
// 		$data = file_get_contents($logfile);
// 		$data = json_decode($data,1);
// 		if ($data && is_array($data) && intval(date("N")) != 7 )
// 		{	// 非周末时，清理掉昨天的数据，来达到每天结算的效果
// 			gerr('[ONLINE]清理周参赛名单b');
// 			$redis->del('lord_model_weekplay_1');
// 			$week = $redis->hget('lord_model_weeks_1',$weekid);
// 			$week['weekRank'] = array();
// 			$redis->hset('lord_model_weeks_1',$weekid,$week);
// 			$mysql->runSql("DELETE FROM `lord_model_games` WHERE `weekId` = $dt_weekid");
// 		}
// 	}
// }
// //临时清理竞技场 建议保留
// $keys = $redis->keys('lord_user_model*');
// $keys = $keys ? $keys : array();
// foreach ( $keys as $k => $v )
// {
// 	$redis->del($v);
// }
// //临时清理竞技场 建议保留
// $keys = $redis->keys('lord_model_goonplay_*');
// $keys = $keys ? $keys : array();
// foreach ( $keys as $k => $v )
// {
// 	$redis->del($v);
// }
// //还原某个redis数据 建议保留
// $gam = '{"id":"4","roomsId":"1_1004","modelId":1,"roomId":0,"roomReal":"1004","baseCoins":"20","rate":"15","limitCoins":"0","rake":"0","enterLimit":"0","enterLimit_":"0","gameName":"\u521d\u7ea7\u6bd4\u8d5b\u573a","gameLevel":"1","gameScoreIn":"3000","gameScoreOut":"600","gameEndTime":"900","gameWinner":"9","gameRanknum":"30","gameBombAdd":"1","gameWaitFirst":"10","gameWaitOther":"5","gameOpen":"\u6bcf\u5929 09:00-23:30","gameOpenSetting":["2014-06-01 09:00:00|2018-06-31 23:30:00|1234567"],"gamePersonAll":"30","gameInCoins":"5000","gameCancelTime":"60","gameCancelPerson":"2","gamePrizeCoins":{"1":30000,"2-4":15000,"5-9":6000},"gamePrizePoint":{"1":5,"2-4":3,"5-9":1},"gamePrizeProps":{"1-9":{"2":"\u9ad8\u624b\u5957\u88c5\uff087\u5929\uff09"}},"gameRule":"\u6e38\u620f\u89c4\u5219\u3002","weekPeriod":"7","weekPrizeCoins":[],"weekPrizeProps":[],"create_time":"2014-10-29 12:08:30","update_time":"2014-10-29 12:08:30","weeksId":"1_0_20150518","weekId":20150518,"weekPool":9997000,"weekRank":[{"rank":1,"uid":1380382,"nick":"lh","point":31},{"rank":2,"uid":1376870,"nick":"5201314520","point":26},{"rank":3,"uid":1234507,"nick":"\u65b0\u624b1300819","point":26},{"rank":4,"uid":795856,"nick":"\u65b0\u624b76298","point":23},{"rank":5,"uid":1674116,"nick":"\u65b0\u624b1708694","point":23},{"rank":6,"uid":1071592,"nick":"\u65b0\u624b1153054","point":22},{"rank":7,"uid":1622122,"nick":"\u65b0\u624b1659339","point":22},{"rank":8,"uid":1168392,"nick":"\u65b0\u624b1241555","point":21},{"rank":9,"uid":723069,"nick":"\u98de\u9f99","point":21},{"rank":10,"uid":1155274,"nick":"\u5168\u662f\u70b8","point":20},{"rank":11,"uid":1146625,"nick":"\u65b0\u624b1221738","point":19},{"rank":12,"uid":1548222,"nick":"\u65b0\u624b1590011","point":19},{"rank":13,"uid":1707451,"nick":"\u65b0\u624b1740593","point":19},{"rank":14,"uid":1474294,"nick":"\u65b0\u624b1519295","point":19},{"rank":15,"uid":1335634,"nick":"\u65b0\u624b1391983","point":19},{"rank":16,"uid":1735650,"nick":"\u65b0\u624b1767550","point":18},{"rank":17,"uid":289105,"nick":"\u9ad8\u624b793543","point":18},{"rank":18,"uid":1455088,"nick":"\u65b0\u624b1500835","point":18},{"rank":19,"uid":1728295,"nick":"\u65b0\u624b1760585","point":18},{"rank":20,"uid":1766384,"nick":"\u667a\u6052","point":17},{"rank":21,"uid":1829564,"nick":"\u65b0\u624b1856533","point":17},{"rank":22,"uid":433175,"nick":"\u65b0\u624b294712","point":17},{"rank":23,"uid":689170,"nick":"\u65b0\u624b438677","point":16},{"rank":24,"uid":1780522,"nick":"\u5df4\u7f57","point":16},{"rank":25,"uid":756077,"nick":"\u65b0\u624b220782","point":16},{"rank":26,"uid":1464942,"nick":"\u65b0\u624b1510276","point":16},{"rank":27,"uid":990290,"nick":"xxiao","point":16},{"rank":28,"uid":1572875,"nick":"\u65b0\u624b1612998","point":16},{"rank":29,"uid":762432,"nick":"\u65b0\u624b518630","point":15},{"rank":30,"uid":350137,"nick":"\u4e8c\u5e08\u5144","point":15}],"weekStart":1431878400,"weekEnd":1432483199,"thisWeekRank":[{"rank":1,"uid":1380382,"nick":"lh","point":31},{"rank":2,"uid":1376870,"nick":"5201314520","point":26},{"rank":3,"uid":1234507,"nick":"\u65b0\u624b1300819","point":26},{"rank":4,"uid":795856,"nick":"\u65b0\u624b76298","point":23},{"rank":5,"uid":1674116,"nick":"\u65b0\u624b1708694","point":23},{"rank":6,"uid":1071592,"nick":"\u65b0\u624b1153054","point":22},{"rank":7,"uid":1622122,"nick":"\u65b0\u624b1659339","point":22},{"rank":8,"uid":1168392,"nick":"\u65b0\u624b1241555","point":21},{"rank":9,"uid":723069,"nick":"\u98de\u9f99","point":21},{"rank":10,"uid":1155274,"nick":"\u5168\u662f\u70b8","point":20},{"rank":11,"uid":1146625,"nick":"\u65b0\u624b1221738","point":19},{"rank":12,"uid":1548222,"nick":"\u65b0\u624b1590011","point":19},{"rank":13,"uid":1707451,"nick":"\u65b0\u624b1740593","point":19},{"rank":14,"uid":1474294,"nick":"\u65b0\u624b1519295","point":19},{"rank":15,"uid":1335634,"nick":"\u65b0\u624b1391983","point":19},{"rank":16,"uid":1735650,"nick":"\u65b0\u624b1767550","point":18},{"rank":17,"uid":289105,"nick":"\u9ad8\u624b793543","point":18},{"rank":18,"uid":1455088,"nick":"\u65b0\u624b1500835","point":18},{"rank":19,"uid":1728295,"nick":"\u65b0\u624b1760585","point":18},{"rank":20,"uid":1766384,"nick":"\u667a\u6052","point":17},{"rank":21,"uid":1829564,"nick":"\u65b0\u624b1856533","point":17},{"rank":22,"uid":433175,"nick":"\u65b0\u624b294712","point":17},{"rank":23,"uid":689170,"nick":"\u65b0\u624b438677","point":16},{"rank":24,"uid":1780522,"nick":"\u5df4\u7f57","point":16},{"rank":25,"uid":756077,"nick":"\u65b0\u624b220782","point":16},{"rank":26,"uid":1464942,"nick":"\u65b0\u624b1510276","point":16},{"rank":27,"uid":990290,"nick":"xxiao","point":16},{"rank":28,"uid":1572875,"nick":"\u65b0\u624b1612998","point":16},{"rank":29,"uid":762432,"nick":"\u65b0\u624b518630","point":15},{"rank":30,"uid":350137,"nick":"\u4e8c\u5e08\u5144","point":15}],"lastWeekRank":[{"rank":1,"uid":212968,"nick":"\u795e\u79d8\u4eba","point":317},{"rank":2,"uid":1464942,"nick":"\u65b0\u624b1510276","point":274},{"rank":3,"uid":1038339,"nick":"\u65b0\u624b1122811","point":197},{"rank":4,"uid":1155274,"nick":"\u5168\u662f\u70b8","point":196},{"rank":5,"uid":1454401,"nick":"\u5a03\u5a03","point":191},{"rank":6,"uid":976181,"nick":"\u65b0\u624b1067870","point":185},{"rank":7,"uid":589928,"nick":"\u65b0\u624b282806","point":176},{"rank":8,"uid":1548716,"nick":"\u65b0\u624b1590460","point":170},{"rank":9,"uid":1573056,"nick":"\u65b0\u624b1613176","point":158},{"rank":10,"uid":1234507,"nick":"\u65b0\u624b1300819","point":154},{"rank":11,"uid":367181,"nick":"\u8f93\u8bb0","point":140},{"rank":12,"uid":1574233,"nick":"\u65b0\u624b1614309","point":139},{"rank":13,"uid":339912,"nick":"\u52c7\u7237","point":136},{"rank":14,"uid":433175,"nick":"\u65b0\u624b294712","point":131},{"rank":15,"uid":810770,"nick":"\u65b0\u624b332377","point":131},{"rank":16,"uid":1318771,"nick":"\u65b0\u624b1376785","point":130},{"rank":17,"uid":886872,"nick":"\u6597\u57ae\u5730\u4e3b","point":129},{"rank":18,"uid":1634808,"nick":"\u51cc\u4e91","point":126},{"rank":19,"uid":189077,"nick":"\u79d1\u6bd4","point":121},{"rank":20,"uid":939296,"nick":"\u65b0\u624b1035550","point":116},{"rank":21,"uid":1071592,"nick":"\u65b0\u624b1153054","point":115},{"rank":22,"uid":1387682,"nick":"\u65b0\u624b1439253","point":112},{"rank":23,"uid":619425,"nick":"\u65b0\u624b2121212","point":109},{"rank":24,"uid":756077,"nick":"\u65b0\u624b220782","point":108},{"rank":25,"uid":1021077,"nick":"\u65b0\u624b137654","point":108},{"rank":26,"uid":1488828,"nick":"668","point":108},{"rank":27,"uid":1434878,"nick":"\u65b0\u624b1482251","point":108},{"rank":28,"uid":1404624,"nick":"\u65b0\u624b123789","point":104},{"rank":29,"uid":1168392,"nick":"\u65b0\u624b1241555","point":99},{"rank":30,"uid":1634483,"nick":"\u65b0\u624b1671116","point":96}],"gamesId":"1_0_20150518_140","gameId":140,"gamePool":5000,"gamePerson":1,"gamePlay":1,"gameStart":0,"gameCreate":1431947005}';
// $redis->hset('lord_model_games_1', '1_0_20150518_140', json_decode($gam,1));
//清理D服务器的所有信息
// $keys = $redis->keys('*10.10.85.225*');
// $keys = $keys ? $keys : array();
// foreach ( $keys as $k => $key )
// {
// 	$redis->del($key);
// }

// //临时代码处理竞技场数据
// if ( date('i') == '47' ) {
// 	$modelgame = $redis->hget('lord_model_games_1', '1_0_20151221_2210');
// 	if ( $modelgame['gamePersonAll'] != 9 ) {
// 		$modelgame['gamePersonAll'] = 9;
// 		$redis->hset('lord_model_games_1', '1_0_20151221_2210', $modelgame);
// 	}
// }
