<?php

// 设置异步query时无法插入时的尝试建表
// InnoDB 且必须为 IF NOT EXISTS
// 待扩展至依据表名、错误码来处理
$dateid = date("Ymd");
$monthid = date("Ym");
return $create_new_table = array(
	"CREATE TABLE IF NOT EXISTS `lord_user_logout_$dateid` (
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
	) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COMMENT='用户登出记录_20160722'"
);
