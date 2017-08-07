<?php

//cron head
require dirname(dirname(__DIR__))."/conf/cron.php";
// $redis = getRedis();
$mysql = getMysql();
define('CRONTAG', 'TOTOAL_USER_AND_SAVE_TABLE');

$ta = "lord_game_loginout_201601";
$tb = "lord_game_user_total";
// $sql = "CREATE TABLE IF NOT EXISTS `lord_game_user_total` (
//  `uid` int(10) NOT NULL COMMENT 'user_user.id',
//  `cool_num` int(10) NOT NULL DEFAULT '0' COMMENT '靓号game_num.num',
//  `nick` varchar(30) NOT NULL COMMENT '游戏昵称',
//  `sex` tinyint(4) NOT NULL DEFAULT '1',
//  `age` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '年龄id',
//  `coins` bigint(20) NOT NULL COMMENT '当前筹码',
//  `coupon` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '可用奖券数',
//  `lottery` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '可用抽奖数',
//  `matches` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '牌局数',
//  `login` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '登录数',
//  `win` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '赢局数',
//  `channel` varchar(50) NOT NULL COMMENT '当前渠道号',
//  `ver` varchar(7) NOT NULL COMMENT '客户端版本',
//  `ip` varchar(15) NOT NULL COMMENT '最后IP',
//  PRIMARY KEY `uid` (`uid`),
//  KEY `search` (`channel`,`uid`,`cool_num`),
//  KEY `nick` (`nick`)
// ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='游戏用户片段统计'";
// $mysql->runSql($sql);

$sql = "insert into `lord_game_user_total` SELECT DISTINCT(a.uid) as uid, b.cool_num, b.nick, b.sex, b.age, b.coins, b.coupon, b.lottery,
		c.matches, c.login, c.win, b.channel, c.version as ver, c.last_ip as ip from `lord_game_loginout_201601` a left join `lord_game_user` b
		on a.uid = b.uid left join `lord_game_analyse` c on a.uid = c.uid where a.dateid < 20160119 and a.logout_coins > 1000000 order by a.uid desc limit 20000";
$res1 = $mysql->runSql($sql);

$sql = "insert into `lord_game_user_total` SELECT DISTINCT(a.uid) as uid, b.cool_num, b.nick, b.sex, b.age, b.coins, b.coupon, b.lottery,
		c.matches, c.login, c.win, b.channel, c.version as ver, c.last_ip as ip from `lord_game_loginout_201601` a left join `lord_game_user` b
		on a.uid = b.uid left join `lord_game_analyse` c on a.uid = c.uid where a.dateid < 20160119 and a.logout_coins > 5000 and a.logout_coins < 200000 order by a.uid desc limit 20000";
$res2 = $mysql->runSql($sql);

$sql = "insert into `lord_game_user_total` SELECT DISTINCT(a.uid) as uid, b.cool_num, b.nick, b.sex, b.age, b.coins, b.coupon, b.lottery,
		c.matches, c.login, c.win, b.channel, c.version as ver, c.last_ip as ip from `lord_game_loginout_201601` a left join `lord_game_user` b
		on a.uid = b.uid left join `lord_game_analyse` c on a.uid = c.uid where a.dateid < 20160119 and a.logout_coins > 200000 and a.logout_coins < 1000000 order by a.uid desc limit 20000";
$res3 = $mysql->runSql($sql);
