<?php

// 创建下月的用户登入登出记录表
if ( date("d") == 28 ) {
	$monthid = intval(date("Ym"), time()+86400*4);
	$sqlCRE = "CREATE TABLE IF NOT EXISTS `lord_game_loginout_$monthid` (
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
		) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='用户登入登出记录，每月都要换表名_201506' AUTO_INCREMENT=1";
	$res = $this->mysql->runSql($sqlCRE);
	if ( !$res ) gerr("[CRONT] sql=$sqlCRE");
}

//
// goto end;

end:{}
