<?php

//script head
define('CRONTAG', 'RECORD_HOT_USER');
require("/data/sweety/conf/cron.php");
// $redis = getRedis();
$mysql = getMysql();
function gerr($log){
	return serr($log);
}

$date = '2016-07-01';
$datetime = strtotime($date);
$today = date("Y-m-d");
$todaytime = strtotime($today);
for ( $i=$datetime; $i<$todaytime; $i+=86400 )
{
	$monthid =  intval(date("Ym",$i));
	$dateid = intval(date("Ymd",$i));
	$sql = "DROP TABLE `tmp_loginoutid`";
	$mysql->runSql($sql);
	$sql = "CREATE TABLE `tmp_loginoutid` (`id` int(10) unsigned NOT NULL,PRIMARY KEY (`id`))  ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT=''";
	$mysql->runSql($sql);
	$sql = "INSERT INTO `tmp_loginoutid` SELECT max(`id`) AS id FROM `lord_game_loginout_$monthid` WHERE `dateid` = $dateid GROUP BY `uid`";
	$mysql->runSql($sql);
	$sql = "DROP TABLE `tmp_loginout`";
	$mysql->runSql($sql);
	$sql = "CREATE TABLE `tmp_loginout` (
		`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
		`dateid` int(8) unsigned NOT NULL DEFAULT '0' COMMENT '日期id',
		`uid` int(10) unsigned NOT NULL DEFAULT '0' COMMENT 'uid',
		`login_gold` int(10) unsigned DEFAULT '0' COMMENT '登入金币',
		`login_coins` int(10) unsigned DEFAULT '0' COMMENT '登入筹码',
		`login_coupon` int(10) unsigned DEFAULT '0' COMMENT '登入乐券',
		`login_lottery` int(10) unsigned DEFAULT '0' COMMENT '登入抽奖数',
		`login_time` varchar(20) DEFAULT '' COMMENT '登入时间',
		`login_ip` varchar(15) DEFAULT '' COMMENT '登入IP',
		`last_action` varchar(32) DEFAULT '' COMMENT '最后操作',
		`last_time` varchar(20) DEFAULT '' COMMENT '最后操作时间',
		`logout_gold` int(10) unsigned DEFAULT '0' COMMENT '登出金币',
		`logout_coins` int(10) unsigned DEFAULT '0' COMMENT '登出筹码',
		`logout_coupon` int(10) unsigned DEFAULT '0' COMMENT '登出乐券',
		`logout_lottery` int(10) unsigned DEFAULT '0' COMMENT '登出抽奖数',
		`logout_time` varchar(20) DEFAULT '' COMMENT '登出时间',
		`online_time` int(10) unsigned DEFAULT '0' COMMENT '在线时长(秒)',
		PRIMARY KEY (`id`),
		KEY `s0` (`uid`),
		KEY `s1` (`online_time`,`uid`),
		KEY `s2` (`logout_coins`,`uid`),
		KEY `s3` (`logout_coupon`,`uid`)
		) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COMMENT=''";
	$mysql->runSql($sql);
	$sql = "INSERT INTO `tmp_loginout` ";
	$sql.= " (`id`,`dateid`,`uid`,`login_gold`,`login_coins`,`login_time`,`last_action`,`last_time`,`logout_gold`,`logout_coins`,`logout_time`,`online_time`) SELECT ";
	$sql.= "b.`id`,b.`dateid`,b.`uid`,b.`login_gold`,b.`login_coins`,b.`login_time`,b.`last_action`,b.`last_time`,b.`logout_gold`,b.`logout_coins`,b.`logout_time`,b.`online_time` ";
	// 需要先在lord_game_loginout_$monthid表中增加字段 login_coupon login_lottery login_ip, logout_coupon logout_lottery
	// $sql.= " (`id`,`dateid`,`uid`,`login_gold`,`login_coins`,`login_coupon`,`login_lottery`,`login_time`,`login_ip`,`last_action`,`last_time`,`logout_gold`,`logout_coins`,`logout_coupon`,`logout_lottery`,`logout_time`,`online_time`) SELECT ";
	// $sql.= "b.`id`,b.`dateid`,b.`uid`,b.`login_gold`,b.`login_coins`,b.`login_coupon`,b.`login_lottery`,b.`login_time`,b.`login_ip`,b.`last_action`,b.`last_time`,b.`logout_gold`,b.`logout_coins`,b.`logout_coupon`,b.`logout_lottery`,b.`logout_time`,b.`online_time`";
	$sql.= " FROM `tmp_loginoutid` a LEFT JOIN `lord_game_loginout_$monthid` b ON a.`id` = b.`id`";
	$mysql->runSql($sql);
	$sql = "CREATE TABLE IF NOT EXISTS `lord_record_hotuser` (
		`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
		`dateid` int(8) unsigned NOT NULL DEFAULT '0' COMMENT '日期ID',
		`rankcoins` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '乐豆排名',
		`rankcoupon` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '乐券排名',
		`uid` int(10) unsigned NOT NULL DEFAULT '0' COMMENT 'UID',
		`cool_num` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '编号ID',
		`nick` varchar(32) NOT NULL DEFAULT '' COMMENT '昵称',
		`gold` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '乐币',
		`coins` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '乐豆',
		`coupon` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '乐券',
		`lottery` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '抽奖数',
		`matches` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '局数',
		`win` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '胜局',
		`channel` varchar(32) NOT NULL DEFAULT '' COMMENT '渠道',
		`version` varchar(5) NOT NULL DEFAULT '' COMMENT '版本',
		`regdateid` int(8) unsigned NOT NULL DEFAULT '0' COMMENT '注册日期ID',
		`add_time` varchar(20) NOT NULL DEFAULT '' COMMENT '注册时间',
		`logdateid` int(8) unsigned NOT NULL DEFAULT '0' COMMENT '登录日期ID',
		`last_login` varchar(20) NOT NULL DEFAULT '' COMMENT '登录日期',
		`last_ip` varchar(15) NOT NULL DEFAULT '' COMMENT '登录IP',
		`tmcr` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
		PRIMARY KEY (`id`),
		KEY `dateid` (`dateid`,`rankcoins`,`coins`),
		KEY `dateid2` (`dateid`,`rankcoupon`,`coupon`),
		KEY `uid` (`uid`),
		KEY `cool_num` (`cool_num`),
		KEY `nick` (`nick`),
		KEY `channel` (`channel`,`dateid`),
		KEY `version` (`version`,`dateid`),
		KEY `regdateid` (`regdateid`)
		) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='DAU乐豆或乐券排前1000的用户关键记录'";
	$mysql->runSql($sql);
	$sql = "SELECT * FROM `tmp_loginout` ORDER BY `logout_coins` DESC LIMIT 1000";
	$data = $mysql->getData($sql); if ( ! $data ) $data = array();
	$rankcoins = 0;
	foreach ( $data as $k => $v )
	{
		$rankcoins++;
		$uid = $v['uid'];
		$sql = "INSERT INTO `lord_record_hotuser` (";
		$sql.= "`dateid`,`rankcoins`,`rankcoupon`,`uid`,`cool_num`,`nick`,`gold`,`coins`,";
		$sql.= "`coupon`,`lottery`,`matches`,`win`,`channel`,`version`,";
		$sql.= "`regdateid`,`add_time`,`logdateid`,`last_login`,`last_ip`,`tmcr`";
		$sql.= ") SELECT ";
		$sql.= "$dateid,$rankcoins,0,u.uid,u.cool_num,u.nick,".$v['logout_gold'].",".$v['logout_coins'].",";
		$sql.= $v['logout_coupon'].",".$v['logout_lottery'].",a.matches,a.win,u.channel,a.version,";
		$sql.= "REPLACE(SUBSTRING(a.`add_time`,1,10),'-',''),a.add_time,REPLACE(SUBSTRING(a.`last_login`,1,10),'-',''),a.last_ip FROM lord_game_user u LEFT JOIN lord_game_analyse a ON a.uid = u.uid";
	}

}


exit;
