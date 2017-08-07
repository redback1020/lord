<?php

// 注意⚠： 本脚本因为运行时间，内存占用等因素，不可以放在sweety中执行，只能直接使用linux-crontab
// 注意⚠： 55 * * * * /usr/local/php/bin/php /data/sweety/game/cron/crontab_hour_total_record.php

define('TAG_NAME', 'HOUR_TOTAL_RECORD_AND_CLEAN_MYSQL_REDIS');
require("/data/sweety/conf/cron.php");
$redis = getRedis();
$mysql = getMysql();
function gerr($log){
	return serr($log);
}

//每小时统计一次各种记录

require("/data/sweety/game/class.record.php");
$record = record::inst($redis, $mysql);

// $channels = array();
// $sql = "select `id`, `channel` from `lord_game_channel` where `is_del` = 0";
// $row = $mysql->getData($sql);
// if ( ! $row ) { $row = array(); }
// foreach ( $row as $val ) {
// 	$channels[$val['id']] = $val['channel'];
// }

// 旧的数据写入
// $day0s = strtotime("2016-05-30");
// $day0e = strtotime(date("Y-m-d", time()-86400));
// for ( $i = $day0s; $i <= $day0e; $i+=86400 ) {
// 	$dd = intval(date("Ymd", $i));
// 	for ($hd=0; $hd < 24; $hd++) {
// 		$record->moneyAnalyse($dd, $hd);
// 	}
// }
// $time = strtotime(date("Y-m-d H:i:s", time()-3600));
// $dd = intval(date("Ymd", $time));
// $hd = intval(date("H", $time));
// for ($hd_=0; $hd_ <= $hd; $hd_++) {
// 	$record->moneyAnalyse($dd, $hd_);
// }

// 定时数据写入


//每小时统计上一时的货币
$time = strtotime(date("Y-m-d H:i:s", time()-3600));
$monthid = intval(date("Ym", $time));
$dd = intval(date("Ymd", $time));
$hd = intval(date("H", $time));
$record->moneyAnalyse($dd, $hd);


//每一点统计上一天的牌桌
if ( ! $hd ) {
	$record->tableAnalyse($dd);
}

//每两点统计上一天的用户
if ( $hd === 1 )
{
	$time = strtotime(date("Y-m-d H:i:s", time()-86400));
	$monthid = intval(date("Ym", $time));
	$dd = intval(date("Ymd", $time));
	//lord_user_logout_min
	//lord_user_logout_max
	//lord_user_logout_tmp
	//lord_user_logout_$dd
	//lord_record_hotuser
	//lord_record_logout_$monthid
	//1.建表 临时ID表 首次登出表 末次登出表
	$sql = "CREATE TABLE IF NOT EXISTS `lord_record_hotuser` (
		`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
		`dd` int(8) unsigned NOT NULL DEFAULT '0' COMMENT '日期ID',
		`uid` int(10) unsigned NOT NULL DEFAULT '0' COMMENT 'UID',
		`reg` int(8) unsigned NOT NULL DEFAULT '0' COMMENT '注册日期ID',
		`channel` varchar(32) NOT NULL DEFAULT '' COMMENT '当日渠道',
		`vercode` int(5) unsigned NOT NULL DEFAULT '0' COMMENT '当日版本',
		`ip` varchar(15) NOT NULL DEFAULT '' COMMENT '当日IP',
		`coins` int(11) NOT NULL DEFAULT '0' COMMENT '当前乐豆',
		`coupon` int(11) NOT NULL DEFAULT '0' COMMENT '当前乐券',
		`play` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '当前局数',
		`win` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '当前胜局',
		`ddcoins` int(11) NOT NULL DEFAULT '0' COMMENT '当日赚豆',
		`ddcoupon` int(11) NOT NULL DEFAULT '0' COMMENT '当日赚券',
		`ddplay` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '当日局数',
		`ddwin` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '当日胜局',
		`ddlogin` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '当日登陆次数',
		`ddseconds` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '当日在线时长',
		`tmcr` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '当前时间',
		PRIMARY KEY (`id`),
		KEY `uid` (`uid`),
		KEY `regdateid` (`reg`),
		KEY `coins` (`dd`,`coins`),
		KEY `coupon` (`dd`,`coupon`),
		KEY `play` (`dd`,`play`),
		KEY `win` (`dd`,`win`),
		KEY `ddcoins` (`dd`,`ddcoins`),
		KEY `ddcoupon` (`dd`,`ddcoupon`),
		KEY `ddplay` (`dd`,`ddplay`),
		KEY `ddwin` (`dd`,`ddwin`),
		KEY `ddlogin` (`dd`,`ddlogin`),
		KEY `ddseconds` (`dd`,`ddseconds`),
		KEY `channel` (`channel`),
		KEY `vercode` (`vercode`)
	) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='DAU乐豆或乐券排前1000的用户关键记录' AUTO_INCREMENT=1 ;";
	$ret = $mysql->runSql($sql);
	if ( ! $ret ) {
		gerr("runSql $sql");
		exit;
	}
	$sql = "CREATE TABLE IF NOT EXISTS `lord_record_logout_$monthid` (
		 `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
		 `dd` int(8) unsigned NOT NULL DEFAULT '0' COMMENT '登出日期ID',
		 `uid` int(10) unsigned NOT NULL DEFAULT '0' COMMENT 'UID',
		 `reg` int(8) unsigned NOT NULL DEFAULT '0' COMMENT '注册日期ID',
		 `channel` varchar(32) NOT NULL DEFAULT '' COMMENT '登出渠道',
		 `vercode` int(5) unsigned NOT NULL DEFAULT '0' COMMENT '登出版本',
		 `ip` varchar(15) NOT NULL DEFAULT '' COMMENT '登出IP',
		 `coins` int(11) NOT NULL DEFAULT '0' COMMENT '登出乐豆',
		 `coupon` int(11) NOT NULL DEFAULT '0' COMMENT '登出乐券',
		 `play` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '登出局数',
		 `win` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '登出胜局',
		 `ddcoins` int(11) NOT NULL DEFAULT '0' COMMENT '当日赚豆',
		 `ddcoupon` int(11) NOT NULL DEFAULT '0' COMMENT '当日赚券',
		 `ddplay` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '当日局数',
		 `ddwin` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '当日胜局',
		 `ddlogin` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '当日登陆次数',
		 `ddseconds` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '当日在线时长',
		 `tmcr` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '当前时间',
		 PRIMARY KEY (`id`),
		 KEY `uid` (`uid`),
		 KEY `reg` (`reg`,`uid`),
		 KEY `coins` (`dd`,`coins`),
		 KEY `coupon` (`dd`,`coupon`),
		 KEY `play` (`dd`,`play`),
		 KEY `win` (`dd`,`win`),
		 KEY `ddcoins` (`dd`,`ddcoins`),
		 KEY `ddcoupon` (`dd`,`ddcoupon`),
		 KEY `ddplay` (`dd`,`ddplay`),
		 KEY `ddwin` (`dd`,`ddwin`),
		 KEY `ddlogin` (`dd`,`ddlogin`),
		 KEY `ddseconds` (`dd`,`ddseconds`),
		 KEY `channel` (`dd`,`channel`),
		 KEY `vercode` (`dd`,`vercode`)
	) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='用户登出记录'";
	$ret = $mysql->runSql($sql);
	if ( ! $ret ) {
		gerr("runSql $sql");
		exit;
	}
	$sql = "DROP TABLE IF EXISTS `lord_user_logout_tmp`";
	$mysql->runSql($sql);
	$sql = "CREATE TABLE `lord_user_logout_tmp` (
		`id` int(10) unsigned NOT NULL,
		PRIMARY KEY (`id`)
	)  ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT=''";
	$ret = $mysql->runSql($sql);
	if ( ! $ret ) {
		gerr("runSql $sql");
		exit;
	}
	$sql = "DROP TABLE IF EXISTS `lord_user_logout_min`";
	$mysql->runSql($sql);
	$sql = "CREATE TABLE `lord_user_logout_min` (
		`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
		`uid` int(10) unsigned NOT NULL DEFAULT '0' COMMENT 'uid',
		`login_channel` varchar(32) DEFAULT '' COMMENT '登入渠道',
		`login_vercode` int(5) unsigned DEFAULT '0' COMMENT '登入版本号',
		`login_ip` varchar(15) DEFAULT '' COMMENT '登入IP',
		`login_time` int(10) unsigned DEFAULT '0' COMMENT '登入时间',
		`login_gold` int(10) unsigned DEFAULT '0' COMMENT '登入金币',
		`login_coins` int(10) unsigned DEFAULT '0' COMMENT '登入筹码',
		`login_coupon` int(10) unsigned DEFAULT '0' COMMENT '登入乐券',
		`login_lottery` int(10) unsigned DEFAULT '0' COMMENT '登入抽奖数',
		`last_action` varchar(32) DEFAULT '' COMMENT '最后操作',
		`last_time` int(10) unsigned DEFAULT '0' COMMENT '最后操作时间',
		`play` int(10) unsigned DEFAULT '0' COMMENT '牌局数',
		`win` int(10) unsigned DEFAULT '0' COMMENT '胜局数',
		`logout_time` int(10) unsigned DEFAULT '0' COMMENT '登出时间',
		`logout_gold` int(10) unsigned DEFAULT '0' COMMENT '登出金币',
		`logout_coins` int(10) unsigned DEFAULT '0' COMMENT '登出筹码',
		`logout_coupon` int(10) unsigned DEFAULT '0' COMMENT '登出乐券',
		`logout_lottery` int(10) unsigned DEFAULT '0' COMMENT '登出抽奖数',
		`online_time` int(10) unsigned DEFAULT '0' COMMENT '在线时长(秒)',
		PRIMARY KEY (`id`),
		KEY `s0` (`uid`),
		KEY `s1` (`login_channel`,`login_vercode`),
		KEY `s2` (`login_vercode`,`login_channel`),
		KEY `s3` (`online_time`),
		KEY `s4` (`logout_coins`),
		KEY `s5` (`logout_coupon`),
		KEY `s6` (`play`),
		KEY `s7` (`win`)
	) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COMMENT='用户登出记录_20160722'";
	$ret = $mysql->runSql($sql);
	if ( ! $ret ) {
		gerr("runSql $sql");
		exit;
	}
	$sql = "DROP TABLE IF EXISTS `lord_user_logout_max`";
	$mysql->runSql($sql);
	$sql = "CREATE TABLE `lord_user_logout_max` (
		`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
		`uid` int(10) unsigned NOT NULL DEFAULT '0' COMMENT 'uid',
		`login_channel` varchar(32) DEFAULT '' COMMENT '登入渠道',
		`login_vercode` int(5) unsigned DEFAULT '0' COMMENT '登入版本号',
		`login_ip` varchar(15) DEFAULT '' COMMENT '登入IP',
		`login_time` int(10) unsigned DEFAULT '0' COMMENT '登入时间',
		`login_gold` int(10) unsigned DEFAULT '0' COMMENT '登入金币',
		`login_coins` int(10) unsigned DEFAULT '0' COMMENT '登入筹码',
		`login_coupon` int(10) unsigned DEFAULT '0' COMMENT '登入乐券',
		`login_lottery` int(10) unsigned DEFAULT '0' COMMENT '登入抽奖数',
		`last_action` varchar(32) DEFAULT '' COMMENT '最后操作',
		`last_time` int(10) unsigned DEFAULT '0' COMMENT '最后操作时间',
		`play` int(10) unsigned DEFAULT '0' COMMENT '牌局数',
		`win` int(10) unsigned DEFAULT '0' COMMENT '胜局数',
		`logout_time` int(10) unsigned DEFAULT '0' COMMENT '登出时间',
		`logout_gold` int(10) unsigned DEFAULT '0' COMMENT '登出金币',
		`logout_coins` int(10) unsigned DEFAULT '0' COMMENT '登出筹码',
		`logout_coupon` int(10) unsigned DEFAULT '0' COMMENT '登出乐券',
		`logout_lottery` int(10) unsigned DEFAULT '0' COMMENT '登出抽奖数',
		`online_time` int(10) unsigned DEFAULT '0' COMMENT '在线时长(秒)',
		PRIMARY KEY (`id`),
		KEY `s0` (`uid`),
		KEY `s1` (`login_channel`,`login_vercode`),
		KEY `s2` (`login_vercode`,`login_channel`),
		KEY `s3` (`online_time`),
		KEY `s4` (`logout_coins`),
		KEY `s5` (`logout_coupon`),
		KEY `s6` (`play`),
		KEY `s7` (`win`)
	) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COMMENT='用户登出记录_20160722'";
	$ret = $mysql->runSql($sql);
	if ( ! $ret ) {
		gerr("runSql $sql");
		exit;
	}
	//2.使用临时ID表，写入首次登出记录、末次登出记录
	// $sql = "INSERT INTO `lord_user_logout_tmp` SELECT min(`id`) AS id FROM `lord_user_logout_$dd` GROUP BY `uid`";
	// $mysql->runSql($sql);
	// $sql = "INSERT INTO `lord_user_logout_min` SELECT b.* FROM `lord_user_logout_tmp` a LEFT JOIN `lord_user_logout_$dd` ON a.`id` = b.`id`";
	// $mysql->runSql($sql);
	// $sql = "TRUNCATE TABLE `lord_user_logout_tmp`";
	// $mysql->runSql($sql);
	$sql = "INSERT INTO `lord_user_logout_min` SELECT * FROM `lord_user_logout_$dd` GROUP BY `uid`";
	$mysql->runSql($sql);
	sleep(3);
	$sql = "INSERT INTO `lord_user_logout_tmp` SELECT max(`id`) AS id FROM `lord_user_logout_$dd` GROUP BY `uid`";
	$mysql->runSql($sql);
	$sql = "INSERT INTO `lord_user_logout_max` SELECT b.* FROM `lord_user_logout_tmp` a LEFT JOIN `lord_user_logout_$dd` b ON a.`id` = b.`id`";
	$mysql->runSql($sql);
	sleep(3);
	$sql = "SELECT COUNT(`id`) FROM `lord_user_logout_min`";
	$minnum = $mysql->getVar($sql);
	$sql = "SELECT COUNT(`id`) FROM `lord_user_logout_max`";
	$maxnum = $mysql->getVar($sql);
	if ( $minnum !== $maxnum ) {
		gerr("临时表严重错误 minnum=$minnum maxnum=$maxnum");
		exit;
	}
	//3.所有用户逐个统计
	$id = 0;
	while ( true )
	{
		$uids = $mins = $regs = array();
		$sql = "SELECT * FROM `lord_user_logout_min` WHERE `id` > $id LIMIT 100";
		$min100 = $mysql->getData($sql);
		if ( ! $min100 ) {
			gerr("没有更多的数据 id=$id");
			break;
		}
		foreach ( $min100 as $k => $v )
		{
			$id = max($id, $v['id']);
			$uids[]= $v['uid'];
			$mins[$v['uid']] = $v;
		}
		$sql = "SELECT * FROM `lord_user_logout_max` WHERE `uid` IN (".join(',', $uids).")";
		$max100 = $mysql->getData($sql);
		if ( ! $max100 || count($max100) != count($uids) ) {
			gerr("临时表严重错误 id=$id countuid=".count($uids)." countmax=".($max100?count($max100):0));
			break;
		}
		$sql = "SELECT `uid`,`add_time` FROM `lord_game_analyse` WHERE `uid` IN (".join(',', $uids).")";
		$reg100 = $mysql->getData($sql);
		if ( ! $reg100 ) {// || count($reg100) != count($uids) ) {
			gerr("用户表严重错误 id=$id countuid=".count($uids)." countreg=".($reg100?count($reg100):0));
			break;
		}
		foreach ( $reg100 as $k => $v )
		{
			$regs[$v['uid']] = intval(str_replace('-','',substr($v['add_time'],0,10)));
		}
		$sqls = array();
		foreach ( $max100 as $k => $v )
		{
			$uid = $v['uid'];
			$reg = isset($regs[$uid]) ? $regs[$uid] : 0;//$reg = $regs[$uid];
			$channel = $v['login_channel'];
			$vercode = $v['login_vercode'];
			$ip = $v['login_ip'];
			$coins = $v['logout_coins'];
			$coupon = $v['logout_coupon'];
			$play = $win = 0;//包括后面的ddplay/ddwin后续依据数据来源的逻辑变动而调整
			$ddcoins = $coins - $mins[$uid]['login_coins'];
			$ddcoupon = $coupon - $mins[$uid]['login_coupon'];
			$ddplay = intval($mysql->getVar("SELECT SUM(`play`) FROM `lord_user_logout_$dd` WHERE `uid` = $uid"));
			$ddwin = intval($mysql->getVar("SELECT SUM(`win`) FROM `lord_user_logout_$dd` WHERE `uid` = $uid"));
			$ddlogin = intval($mysql->getVar("SELECT COUNT(`id`) FROM `lord_user_logout_$dd` WHERE `uid` = $uid"));
			$ddseconds = intval($mysql->getVar("SELECT SUM(`online_time`) FROM `lord_user_logout_$dd` WHERE `uid` = $uid"));
			$sqls[]="($dd,$uid,$reg,'$channel',$vercode,'$ip',$coins,$coupon,$play,$win,$ddcoins,$ddcoupon,$ddplay,$ddwin,$ddlogin,$ddseconds,".time().")";
		}
		$sql = "INSERT INTO `lord_record_logout_$monthid` (";
		$sql.= "`dd`,`uid`,`reg`,`channel`,`vercode`,`ip`,`coins`,`coupon`,`play`,`win`,`ddcoins`,`ddcoupon`,`ddplay`,`ddwin`,`ddlogin`,`ddseconds`,`tmcr`";
		$sql.= ") VALUES ".join(',',$sqls);
		$mysql->runSql($sql);
	}
	//4.提取各种前1000名，入新表，用于后台查询。
	// $ranks = array('coins','coupon','play','win','ddcoins','ddcoupon','ddplay','ddwin','ddlogin','ddseconds');
	$ranks = array('coins','coupon','ddcoins','ddcoupon','ddplay','ddwin','ddlogin','ddseconds','login_coins','login_coupon');
	foreach ( $ranks as $col )
	{
		$sql = "INSERT INTO `lord_record_hotuser` (`dd`,`uid`,`reg`,`channel`,`vercode`,`ip`,`login_coins`,`login_coupon`,`coins`,`coupon`,`play`,`win`,`ddcoins`,`ddcoupon`,`ddplay`,`ddwin`,`ddlogin`,`ddseconds`,`tmcr`) ";
		$sql.= "SELECT `dd`,`uid`,`reg`,`channel`,`vercode`,`ip`,`coins`- `ddcoins` as `login_coins`,`coupon`-`ddcoupon` as `login_coupon`,`coins`,`coupon`,`play`,`win`,`ddcoins`,`ddcoupon`,`ddplay`,`ddwin`,`ddlogin`,`ddseconds`,`tmcr` ";
		$sql.= "FROM `lord_record_logout_$monthid` WHERE `dd` = $dd ORDER BY `$col` DESC LIMIT 1000";
		$mysql->runSql($sql);
	}
	$sql = "UPDATE `lord_record_hotuser` INNER JOIN `lord_game_user` ON `lord_record_hotuser`.uid=`lord_game_user`.uid SET `lord_record_hotuser`.cool_num=`lord_game_user`.cool_num,`lord_record_hotuser`.nick=`lord_game_user`.nick where `lord_record_hotuser`.dd=$dd";        
    $mysql->runSql($sql);
    $sql = "UPDATE `lord_record_hotuser` INNER JOIN `lord_user_task` ON `lord_record_hotuser`.uid=`lord_user_task`.uid SET `lord_record_hotuser`.charge_num=`lord_user_task`.gold_all where `lord_record_hotuser`.dd=$dd";
    $mysql->runSql($sql);
    $dm = date("Ym");
    $sql = "update  `lord_record_money_day`,(select sum(coins)-sum(ddcoins) as total_coins, sum(coupon)-sum(ddcoupon) as total_coupon from lord_record_logout_$dm  where `dd` =$dd) lord_record_logout_$dd set lord_record_money_day.`day_coins`=lord_record_logout_$dd.total_coins,lord_record_money_day.`day_coupon`=lord_record_logout_$dd.total_coupon   where lord_record_money_day.`dateid` =$dd;";
    $mysql->runSql($sql);
}

//每五点清理上一天的数据
if ( $hd != 4 ) exit;

// MYSQL维护
$date90 = intval(date("Ymd",time()-86400*91));
$date15 = intval(date("Ymd",time()-86400*16));
$date30 = intval(date("Ymd",time()-86400*32));
$month2 = intval(date("Ym",time()-86400*61));
$mysql->runSql("DROP TABLE `lord_record_action_{$date90}`");
$mysql->runSql("DROP TABLE `lord_record_table_{$date90}`");
$mysql->runSql("DROP TABLE `lord_record_money_{$date90}`");
$mysql->runSql("DROP TABLE `lord_game_loginout_{$month2}`");

// REDIS维护
$weekid = intval(date("Ymd",time()-(date("N")-1)*86400));
$lastweekid = intval(date("Ymd",time()-(date("N")-1)*86400-7*86400));
$dateid = intval(date("Ymd"));
$lastdateid = intval(date("Ymd",time()-86400));
$keys = $redis->redis->keys('lord_stwin_20*');
foreach ( $keys as $k => $key )
{
	$vv = explode('_', $key);
	if ( count($vv) != 3 ) continue;
	if ( $vv[2] != $dateid ) {
		// var_dump($vv);
		sleep(2);
		$redis->del($key);
	}
}
$keys = $redis->redis->keys('lord_stlos_20*');
foreach ( $keys as $k => $key )
{
	$vv = explode('_', $key);
	if ( count($vv) != 3 ) continue;
	if ( $vv[2] != $dateid ) {
		// var_dump($vv);
		sleep(2);
		$redis->del($key);
	}
}
$keys = $redis->redis->keys('lord_gold_day_220*');
foreach ( $keys as $k => $key )
{
	$vv = explode('_', $key);
	if ( count($vv) != 4 ) continue;
	if ( $vv[3] != ('2'.$dateid) ) {
		// var_dump($vv);
		$redis->del($key);
	}
}
$keys = $redis->redis->keys('lord_trial_20*');
foreach ( $keys as $k => $key )
{
	$vv = explode('_', $key);
	if ( count($vv) != 3 ) continue;
	if ( $vv[2] != $dateid ) {
		// var_dump($vv);
		sleep(2);
		$redis->del($key);
	}
}
$keys = $redis->redis->keys('lord_list_*');
foreach ( $keys as $k => $key )
{
	$vv = explode('_', $key);
	if ( count($vv) != 6 || ! in_array($vv[2], array('normal','match')) || ! in_array($vv[3], array('day','week')) ) continue;
	if ( $vv[3] == 'day' && $vv[5] != $dateid ) {
		// var_dump($vv);
		sleep(2);
		$redis->del($key);
	}
	if ( $vv[3] == 'week' && !($vv[5] == $weekid || $vv[5] == $lastweekid) ) {
		// var_dump($vv);
		sleep(2);
		$redis->del($key);
	}
}
$keys = $redis->redis->hkeys('lord_model_games_1');
foreach ( $keys as $k => $v )
{
	$vv = explode('_', $v);
	if ( $vv[2] != $weekid ) {
		// var_dump($vv);
		$redis->hdel('lord_model_games_1', $v);
	}
}
$keys = $redis->redis->keys("lord_model_gameplay_*");
foreach ( $keys as $k => $key )
{
	$vv = explode('_', $key);
	if ( $vv[5] != $weekid ) {
		// var_dump($vv);
		$redis->del($key);
	}
}
$keys = $redis->redis->keys("lord_model_goonplay_*");
foreach ( $keys as $k => $key )
{
	$vv = explode('_', $key);
	if ( $vv[5] != $weekid ) {
		// var_dump($vv);
		$redis->del($key);
	}
}
$keys = $redis->redis->keys("lord_libao_2016*");
foreach ( $keys as $k => $key )
{
	$vv = explode('_', $key);
	if ( $vv[2] != $dateid ) {
		// var_dump($vv);
		sleep(2);
		$redis->del($key);
	}
}
$keys = $redis->redis->keys("lord_libao_xingyun*");
foreach ( $keys as $k => $key )
{
	$vv = explode('_', $key);
	if ( $vv[4] != $dateid ) {
		// var_dump($key);
		sleep(2);
		$redis->del($key);
	}
}
$keys = $redis->redis->keys("lord_libao_mianze*");
foreach ( $keys as $k => $key )
{
	$vv = explode('_', $key);
	if ( $vv[4] != $dateid ) {
		// var_dump($key);
		sleep(2);
		$redis->del($key);
	}
}
$keys = $redis->redis->keys("lord_libao_dznm_times_*");
foreach ( $keys as $k => $key )
{
	$vv = explode('_', $key);
	if ( $vv[4] != $dateid ) {
		// var_dump($key);
		sleep(2);
		$redis->del($key);
	}
}
$keys = $redis->redis->keys("lord_libao_qrcode1_times_*");
foreach ( $keys as $k => $key )
{
	$vv = explode('_', $key);
	if ( $vv[4] != $dateid ) {
		// var_dump($key);
		sleep(2);
		$redis->del($key);
	}
}
// $keys = $redis->redis->keys("lord_libao_qrcode2_times_*");
// foreach ( $keys as $k => $key )
// {
// 	$vv = explode('_', $key);
// 	if ( $vv[4] != $dateid ) {
// 		// var_dump($key);
// 		sleep(2);
// 		$redis->del($key);
// 	}
// }
$keys = $redis->redis->keys("lord_ippban_*");
foreach ( $keys as $k => $key )
{
	$vv = explode('_', $key);
	if ( $vv[3] != $dateid ) {
		// var_dump($key);
		sleep(2);
		$redis->del($key);
	}
}

$keys = $redis->redis->keys("login_got_20*");
foreach ( $keys as $k => $key )
{
    $vv = explode('_', $key);
    if ( $vv[3] != $dateid ) {
        // var_dump($key);
        sleep(2);
        $redis->del($key);
    }
}


exit;
