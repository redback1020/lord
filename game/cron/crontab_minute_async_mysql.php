<?php

// 注意⚠： 本脚本因为运行时间，内存占用等因素，不可以放在sweety中执行，只能直接使用linux-crontab

define('TAG_NAME', 'ONLINE_TOTAL_AND_FIX');
require("/data/sweety/conf/cron.php");
$redis = getRedis();
$mysql = getMysql();
function gerr($log){
	return serr($log);
}



//各个服务器减压
$_num = 0;
while ( $_num < 50000 ) {
	while ( $sql = $redis->lpop('srv_mysql') ) {
		$res = $mysql->runSql($sql);
		if ( !$res && strpos($sql, '_game_loginout_') ) {
			$monthid = date('Ym');
			$res = $mysql->runSql("CREATE TABLE IF NOT EXISTS `lord_game_loginout_$monthid` (
				`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
				`dateid` int(8) unsigned NOT NULL DEFAULT '0' COMMENT '日期id',
				`uid` int(10) unsigned NOT NULL DEFAULT '0' COMMENT 'uid',
				`login_coins` int(10) unsigned DEFAULT '0' COMMENT '登入筹码',
				`login_gold` int(10) unsigned DEFAULT '0' COMMENT '登入金币',
				`login_time` varchar(255) DEFAULT '' COMMENT '登入时间',
				`last_action` varchar(255) DEFAULT '' COMMENT '最后操作',
				`last_time` varchar(255) DEFAULT '' COMMENT '最后操作时间',
				`logout_coins` int(10) unsigned DEFAULT '0' COMMENT '登出筹码',
				`logout_gold` int(10) unsigned DEFAULT '0' COMMENT '登出金币',
				`logout_time` varchar(255) DEFAULT '' COMMENT '登出时间',
				`online_time` int(10) unsigned DEFAULT '0' COMMENT '在线时长(秒)',
				PRIMARY KEY (`id`),
				INDEX `search` (`dateid`,`online_time`),
				INDEX `search2` (`uid`,`dateid`)
			) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='用户登入登出记录，每月都要换表名_201506' AUTO_INCREMENT=1");
			$res = $mysql->runSql($sql);
			if ( !$res ) {
				gerr("[ONLINE] srv_mysql $sql");
			}
		} elseif( !$res ) {
			gerr("[ONLINE] srv_mysql $sql");
		}
	}
	usleep(1000);
	$_num++;
}
