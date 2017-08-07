/*
 Navicat Premium Data Transfer

 Source Server         : 斗地主test
 Source Server Type    : MySQL
 Source Server Version : 50537
 Source Host           : 127.0.0.1
 Source Database       : dbx5415j5nf05kqn

 Target Server Type    : MySQL
 Target Server Version : 50537
 File Encoding         : utf-8

 Date: 09/06/2016 15:57:45 PM
*/

SET NAMES utf8;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
--  Table structure for `adm_user`
-- ----------------------------
DROP TABLE IF EXISTS `adm_user`;
CREATE TABLE `adm_user` (
  `id`          INT(10)      NOT NULL AUTO_INCREMENT,
  `admin_name`  CHAR(32)     NOT NULL,
  `admin_pwd`   CHAR(32)     NOT NULL,
  `access_priv` VARCHAR(200) NOT NULL,
  `data_priv`   VARCHAR(200) NOT NULL,
  `time`        VARCHAR(50)  NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE = InnoDB AUTO_INCREMENT = 85 DEFAULT CHARSET = utf8 ROW_FORMAT = COMPACT;

-- ----------------------------
--  Table structure for `ali_qrcodes`
-- ----------------------------
DROP TABLE IF EXISTS `ali_qrcodes`;
CREATE TABLE `ali_qrcodes` (
  `id`           INT(11)      NOT NULL AUTO_INCREMENT,
  `out_trade_no` VARCHAR(64)  NOT NULL,
  `qrcode`       VARCHAR(255) NOT NULL,
  `qrcodeimg`    VARCHAR(255) NOT NULL,
  `uid`          VARCHAR(64)  NOT NULL,
  `product`      VARCHAR(16)  NOT NULL,
  `channel`      VARCHAR(64)  NOT NULL,
  `created`      DATETIME     NOT NULL,
  PRIMARY KEY (`id`),
  KEY `out_trade_no` (`out_trade_no`),
  KEY `qrcode` (`qrcode`)
) ENGINE = InnoDB DEFAULT CHARSET = utf8;

-- ----------------------------
--  Table structure for `log_fruit_bet`
-- ----------------------------
DROP TABLE IF EXISTS `log_fruit_bet`;
CREATE TABLE `log_fruit_bet` (
  `id`          INT(11)     NOT NULL AUTO_INCREMENT,
  `logday`      DATE        NOT NULL DEFAULT '1970-01-01',
  `firstday`    DATE        NOT NULL DEFAULT '1970-01-01',
  `logdate`     DATETIME    NOT NULL DEFAULT '1970-01-01 00:00:00',
  `time`        INT(11)     NOT NULL DEFAULT '0',
  `playerId`    INT(11)     NOT NULL DEFAULT '0',
  `playerName`  VARCHAR(32) NOT NULL DEFAULT '',
  `bets`        VARCHAR(64) NOT NULL DEFAULT '',
  `cost`        INT(11)     NOT NULL DEFAULT '0',
  `old`         INT(11)     NOT NULL DEFAULT '0',
  `round`       INT(11)     NOT NULL DEFAULT '0',
  `ip`          VARCHAR(32) NOT NULL DEFAULT '0.0.0.0',
  `persisRound` INT(11)     NOT NULL DEFAULT '0',
  `profit`      INT(11)     NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `log_fruit_bet_player_id_index` (`playerId`)
) ENGINE = MyISAM AUTO_INCREMENT = 3264 DEFAULT CHARSET = utf8;

-- ----------------------------
--  Table structure for `log_fruit_enter`
-- ----------------------------
DROP TABLE IF EXISTS `log_fruit_enter`;
CREATE TABLE `log_fruit_enter` (
  `id`         INT(11)     NOT NULL AUTO_INCREMENT,
  `logday`     DATE        NOT NULL DEFAULT '1970-01-01',
  `firstday`   DATE        NOT NULL DEFAULT '1970-01-01',
  `logdate`    DATETIME    NOT NULL DEFAULT '1970-01-01 00:00:00',
  `time`       INT(11)     NOT NULL DEFAULT '0',
  `playerId`   INT(11)     NOT NULL DEFAULT '0',
  `playerName` VARCHAR(32) NOT NULL DEFAULT '',
  `gold`       INT(11)     NOT NULL DEFAULT '0',
  `bet`        INT(11)     NOT NULL DEFAULT '0',
  `ip`         VARCHAR(32) NOT NULL DEFAULT '0.0.0.0',
  PRIMARY KEY (`id`),
  KEY `log_fruit_enter_player_id_index` (`playerId`)
) ENGINE = MyISAM AUTO_INCREMENT = 675 DEFAULT CHARSET = utf8;

-- ----------------------------
--  Table structure for `log_fruit_exit`
-- ----------------------------
DROP TABLE IF EXISTS `log_fruit_exit`;
CREATE TABLE `log_fruit_exit` (
  `id`         INT(11)     NOT NULL AUTO_INCREMENT,
  `logday`     DATE        NOT NULL DEFAULT '1970-01-01',
  `logdate`    DATETIME    NOT NULL DEFAULT '1970-01-01 00:00:00',
  `firstday`   DATE        NOT NULL DEFAULT '1970-01-01',
  `time`       INT(11)     NOT NULL DEFAULT '0',
  `playerId`   INT(11)     NOT NULL DEFAULT '0',
  `playerName` VARCHAR(32) NOT NULL DEFAULT '',
  `gold`       INT(11)     NOT NULL DEFAULT '0',
  `bet`        INT(11)     NOT NULL DEFAULT '0',
  `ip`         VARCHAR(32) NOT NULL DEFAULT '0.0.0.0',
  PRIMARY KEY (`id`),
  KEY `log_fruit_exit_player_id_index` (`playerId`)
) ENGINE = MyISAM AUTO_INCREMENT = 1754 DEFAULT CHARSET = utf8;

-- ----------------------------
--  Table structure for `log_fruit_win`
-- ----------------------------
DROP TABLE IF EXISTS `log_fruit_win`;
CREATE TABLE `log_fruit_win` (
  `id`         INT(11)     NOT NULL AUTO_INCREMENT,
  `logday`     DATE        NOT NULL DEFAULT '1970-01-01',
  `firstday`   DATE        NOT NULL DEFAULT '1970-01-01',
  `logdate`    DATETIME    NOT NULL DEFAULT '1970-01-01 00:00:00',
  `time`       INT(11)     NOT NULL DEFAULT '0',
  `playerId`   INT(11)     NOT NULL DEFAULT '0',
  `playerName` VARCHAR(32) NOT NULL DEFAULT '',
  `name`       VARCHAR(32) NOT NULL DEFAULT '',
  `stopId`     INT(11)     NOT NULL DEFAULT '0',
  `stopName`   VARCHAR(32) NOT NULL DEFAULT '',
  `cells`      VARCHAR(32) NOT NULL DEFAULT '',
  `coin`       INT(11)     NOT NULL DEFAULT '0',
  `round`      INT(11)     NOT NULL DEFAULT '0',
  `key`        INT(11)     NOT NULL DEFAULT '0',
  `old`        INT(11)     NOT NULL DEFAULT '0',
  `ip`         VARCHAR(32) NOT NULL DEFAULT '0.0.0.0',
  PRIMARY KEY (`id`),
  KEY `log_fruit_win_player_id_index` (`playerId`)
) ENGINE = MyISAM AUTO_INCREMENT = 3264 DEFAULT CHARSET = utf8;

-- ----------------------------
--  Table structure for `lord_alipay`
-- ----------------------------
DROP TABLE IF EXISTS `lord_alipay`;
CREATE TABLE `lord_alipay` (
  `id`           INT(11)                                                                                     NOT NULL AUTO_INCREMENT,
  `uid`          INT(11)                                                                                     NOT NULL COMMENT '用户游戏ID',
  `ptype`        VARCHAR(3)                                                                                  NOT NULL COMMENT '商品代码',
  `out_trade_no` VARCHAR(30)                                                                                 NOT NULL COMMENT '商户方（即我方）订单号。格式中包含以下信息：产品类型(2位)，时间201403131859，mt_rand',
  `trade_no`     VARCHAR(64)                                                                                 NOT NULL COMMENT '支付宝交易号',
  `trade_status` ENUM ('WAIT_BUYER_PAY', 'TRADE_CLOSED', 'TRADE_SUCCESS', 'TRADE_PENDING', 'TRADE_FINISHED') NOT NULL COMMENT '交易状态（详查支付宝即时到账接口“1 2.5 交易状态”',
  `buyer_email`  VARCHAR(100)                                                                                NOT NULL COMMENT '买家支付宝账号，可以是Email或手机号码',
  `total_fee`    FLOAT                                                                                       NOT NULL COMMENT '该笔订单的总金额。根据该值，判断给用户充多少虚拟货币',
  `shipped`      ENUM ('YES', 'NO')                                                                          NOT NULL DEFAULT 'NO' COMMENT '游戏虚拟物品是否已发',
  `shipped_time` DATETIME                                                                                    NOT NULL COMMENT '游戏虚拟物品发货时间',
  `channel`      VARCHAR(30)                                                                                 NOT NULL COMMENT '用户来源渠道，比如：小米，乐视',
  PRIMARY KEY (`id`),
  UNIQUE KEY `out_trade_no` (`out_trade_no`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 211 DEFAULT CHARSET = utf8 ROW_FORMAT = COMPACT COMMENT = '斗地主支付宝扫码支付表';

-- ----------------------------
--  Table structure for `lord_game_activation`
-- ----------------------------
DROP TABLE IF EXISTS `lord_game_activation`;
CREATE TABLE `lord_game_activation` (
  `id`        INT(10) UNSIGNED NOT NULL COMMENT '码号，可转码值',
  `code`      VARCHAR(16)      NOT NULL COMMENT '码值，可转码号',
  `cateid`    TINYINT(3) UNSIGNED DEFAULT '1' COMMENT '分类',
  `status`    TINYINT(3) UNSIGNED DEFAULT '0' COMMENT '状态0未发1已发2已用',
  `ut_create` INT(10) UNSIGNED    DEFAULT '0' COMMENT '创建时间',
  `ut_update` INT(10) UNSIGNED    DEFAULT '0' COMMENT '更新时间',
  PRIMARY KEY (`id`),
  UNIQUE KEY `code` (`code`)
) ENGINE = InnoDB DEFAULT CHARSET = utf8 COMMENT = '激活码';

-- ----------------------------
--  Table structure for `lord_game_analyse`
-- ----------------------------
DROP TABLE IF EXISTS `lord_game_analyse`;
CREATE TABLE `lord_game_analyse` (
  `id`           INT(11)     NOT NULL AUTO_INCREMENT,
  `uid`          INT(11)     NOT NULL COMMENT 'game_user.uid',
  `device`       VARCHAR(50) NOT NULL COMMENT '游戏注册设备号',
  `extend`       VARCHAR(64) NOT NULL DEFAULT '' COMMENT '扩展设备号，用作串号校验',
  `change_nick`  INT(11)     NOT NULL DEFAULT '0' COMMENT '改名次数',
  `trial`        INT(11)     NOT NULL DEFAULT '1' COMMENT '是否体验',
  `trial_count`  INT(11)     NOT NULL COMMENT '领取筹码数',
  `trial_daily`  INT(11)     NOT NULL COMMENT '当日领取筹码数',
  `check_count`  INT(11)     NOT NULL COMMENT '签到计数',
  `is_check`     INT(11)     NOT NULL COMMENT '当日签到状态',
  `tutorial`     INT(11)     NOT NULL DEFAULT '0' COMMENT '阅读新手引导次数',
  `max_coins`    BIGINT(20)  NOT NULL DEFAULT '0' COMMENT '最大筹码',
  `max_gold`     BIGINT(20)  NOT NULL DEFAULT '0' COMMENT '最大金币',
  `matches`      INT(11)     NOT NULL COMMENT '参加比赛次数',
  `charge_money` DOUBLE      NOT NULL COMMENT '充值额',
  `gold_charge`  BIGINT(20)  NOT NULL COMMENT '充值金币',
  `coins_charge` BIGINT(20)  NOT NULL COMMENT '充值筹码',
  `gold_cost`    BIGINT(20)  NOT NULL DEFAULT '0' COMMENT '消耗金币',
  `gold_got`     BIGINT(20)  NOT NULL DEFAULT '0' COMMENT '获得金币',
  `coins_cost`   BIGINT(20)  NOT NULL DEFAULT '0' COMMENT '消耗筹码',
  `coins_got`    BIGINT(20)  NOT NULL DEFAULT '0' COMMENT '获得筹码',
  `luck_coins`   BIGINT(20)  NOT NULL DEFAULT '0' COMMENT '水果机获得筹码',
  `card_normal`  INT(11)     NOT NULL DEFAULT '0',
  `ip`           VARCHAR(30) NOT NULL,
  `version`      VARCHAR(255)         DEFAULT NULL COMMENT '最新版本号',
  `login`        INT(11)     NOT NULL DEFAULT '0' COMMENT '登录次数',
  `play`         INT(11)     NOT NULL DEFAULT '0' COMMENT '游戏次数',
  `erase`        INT(11)     NOT NULL DEFAULT '0' COMMENT '道具减免次数',
  `win`          INT(11)     NOT NULL DEFAULT '0' COMMENT '赢牌次数',
  `max_win`      INT(11)     NOT NULL DEFAULT '0' COMMENT '最大赢得筹码',
  `grown`        INT(11)     NOT NULL DEFAULT '0' COMMENT '新秀场次',
  `daily`        INT(11)     NOT NULL DEFAULT '0' COMMENT '日常登录',
  `last_ip`      VARCHAR(30) NOT NULL COMMENT '上次登录IP',
  `grown_time`   DATETIME    NOT NULL COMMENT '获得新秀时间',
  `add_time`     DATETIME    NOT NULL COMMENT '加入时间',
  `last_time`    DATETIME    NOT NULL COMMENT '上次登录',
  `last_login`   DATETIME    NOT NULL COMMENT '上次日常登录',
  `last_match`   DATETIME    NOT NULL COMMENT '上次参与比赛时间',
  PRIMARY KEY (`id`),
  KEY `uid` (`uid`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 397 DEFAULT CHARSET = utf8 ROW_FORMAT = COMPACT COMMENT = '游戏用户统计信息';

-- ----------------------------
--  Table structure for `lord_game_channel`
-- ----------------------------
DROP TABLE IF EXISTS `lord_game_channel`;
CREATE TABLE `lord_game_channel` (
  `id`      INT(10) UNSIGNED    NOT NULL AUTO_INCREMENT,
  `channel` VARCHAR(16)         NOT NULL DEFAULT '' COMMENT '渠道',
  `is_del`  TINYINT(1) UNSIGNED NOT NULL DEFAULT '0' COMMENT '0正常1已删',
  `tmcr`    INT(10) UNSIGNED    NOT NULL DEFAULT '0' COMMENT '创建时间',
  PRIMARY KEY (`id`),
  KEY `s0` (`channel`)
) ENGINE = InnoDB AUTO_INCREMENT = 25 DEFAULT CHARSET = utf8 COMMENT = '游戏渠道表';

-- ----------------------------
--  Table structure for `lord_game_charge`
-- ----------------------------
DROP TABLE IF EXISTS `lord_game_charge`;
CREATE TABLE `lord_game_charge` (
  `id`      INT(11)     NOT NULL AUTO_INCREMENT COMMENT '序列号',
  `uid`     INT(11)     NOT NULL COMMENT 'UID',
  `gold`    INT(11)     NOT NULL COMMENT '充值乐币数',
  `time`    DATETIME    NOT NULL COMMENT '充值时间',
  `before`  INT(11)     NOT NULL COMMENT '充值前乐币数',
  `after`   INT(11)     NOT NULL COMMENT '充值后乐币数',
  `ip`      VARCHAR(20) NOT NULL COMMENT 'IP',
  `channel` VARCHAR(60) NOT NULL COMMENT '渠道',
  `from`    VARCHAR(40) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `uid` (`uid`),
  KEY `channel` (`channel`)
) ENGINE = InnoDB AUTO_INCREMENT = 3236 DEFAULT CHARSET = utf8 COMMENT = '乐币充值记录表';

-- ----------------------------
--  Table structure for `lord_game_charge_sys`
-- ----------------------------
DROP TABLE IF EXISTS `lord_game_charge_sys`;
CREATE TABLE `lord_game_charge_sys` (
  `id`      INT(11)     NOT NULL AUTO_INCREMENT COMMENT '序列号',
  `uid`     INT(11)     NOT NULL COMMENT 'UID',
  `gold`    INT(11)              DEFAULT '0' COMMENT '乐币变化',
  `coins`   INT(11)     NOT NULL,
  `time`    DATETIME    NOT NULL COMMENT '充值时间',
  `ip`      VARCHAR(20) NOT NULL COMMENT 'IP',
  `channel` VARCHAR(60) NOT NULL COMMENT '渠道',
  `from`    TINYINT(4)  NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `channel` (`channel`) USING BTREE,
  KEY `uid` (`uid`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 740 DEFAULT CHARSET = utf8 ROW_FORMAT = COMPACT COMMENT = '乐币充值记录表';

-- ----------------------------
--  Table structure for `lord_game_file`
-- ----------------------------
DROP TABLE IF EXISTS `lord_game_file`;
CREATE TABLE `lord_game_file` (
  `id`          INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `channel`     VARCHAR(255)              DEFAULT '' COMMENT '许可渠道',
  `channot`     VARCHAR(255)              DEFAULT '' COMMENT '屏蔽渠道',
  `path`        VARCHAR(32)               DEFAULT '' COMMENT '路径',
  `fileid`      INT(10) UNSIGNED          DEFAULT '0' COMMENT '图片id',
  `version`     INT(10) UNSIGNED          DEFAULT '0' COMMENT '最后操作所处版本号',
  `ver_ins`     INT(10) UNSIGNED          DEFAULT '0' COMMENT '创建时的版本号',
  `ver_upd`     INT(10) UNSIGNED          DEFAULT '0' COMMENT '更新时的版本号',
  `ver_del`     INT(10) UNSIGNED          DEFAULT '0' COMMENT '删除时的版本号',
  `sort`        TINYINT(3) UNSIGNED       DEFAULT '99' COMMENT '排序',
  `is_del`      TINYINT(3) UNSIGNED       DEFAULT '0' COMMENT '是否删除，删除后不可再更新',
  `create_time` INT(10) UNSIGNED          DEFAULT '0' COMMENT '创建时间',
  `update_time` INT(10) UNSIGNED          DEFAULT '0' COMMENT '修改时间',
  PRIMARY KEY (`id`),
  KEY `search` (`path`, `fileid`, `version`)
) ENGINE = InnoDB AUTO_INCREMENT = 416 DEFAULT CHARSET = utf8 COMMENT = '斗地主文件素材版本控制表';

-- ----------------------------
--  Table structure for `lord_game_fruit`
-- ----------------------------
DROP TABLE IF EXISTS `lord_game_fruit`;
CREATE TABLE `lord_game_fruit` (
  `uid`             INT(11)  NOT NULL DEFAULT '0',
  `first_day`       DATETIME NOT NULL DEFAULT '1970-01-01 00:00:00',
  `credit`          INT(11)           DEFAULT '0',
  `round`           INT(11)           DEFAULT '0',
  `intervene_round` INT(11)           DEFAULT '0',
  `curr_round_win`  INT(11)           DEFAULT '0',
  `total_win`       INT(11)           DEFAULT '0',
  `persist_round`   INT(11)           DEFAULT '0',
  `total_bet`       INT(11)           DEFAULT '0',
  PRIMARY KEY (`uid`)
) ENGINE = MyISAM DEFAULT CHARSET = utf8;

-- ----------------------------
--  Table structure for `lord_game_login`
-- ----------------------------
DROP TABLE IF EXISTS `lord_game_login`;
CREATE TABLE `lord_game_login` (
  `id`         BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `uid`        BIGINT(20) UNSIGNED NOT NULL COMMENT '用户id',
  `cool_num`   INT(10) UNSIGNED    NOT NULL COMMENT '用户靓号',
  `gold`       INT(11)             NOT NULL COMMENT '乐币',
  `coins`      BIGINT(21)          NOT NULL COMMENT '筹码',
  `device`     VARCHAR(64)         NOT NULL COMMENT '设备号open_id',
  `channel`    VARCHAR(32)         NOT NULL COMMENT '渠道号',
  `is_tv`      TINYINT(3) UNSIGNED NOT NULL COMMENT '是否电视渠道',
  `version`    VARCHAR(255)                 DEFAULT NULL COMMENT '登录版本号',
  `login_ip`   VARCHAR(15)         NOT NULL COMMENT '登录ip',
  `login_time` DATETIME            NOT NULL COMMENT '登录时间',
  PRIMARY KEY (`id`),
  KEY `uid` (`uid`, `login_time`),
  KEY `login_time` (`login_time`)
) ENGINE = InnoDB AUTO_INCREMENT = 14801 DEFAULT CHARSET = utf8 COMMENT = '斗地主用户登录日志';

-- ----------------------------
--  Table structure for `lord_game_loginout_0`
-- ----------------------------
DROP TABLE IF EXISTS `lord_game_loginout_0`;
CREATE TABLE `lord_game_loginout_0` (
  `id`           INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `dateid`       INT(8) UNSIGNED  NOT NULL DEFAULT '0' COMMENT '日期id',
  `uid`          INT(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'uid',
  `login_coins`  INT(10) UNSIGNED          DEFAULT '0' COMMENT '登入筹码',
  `login_gold`   INT(10) UNSIGNED          DEFAULT '0' COMMENT '登入金币',
  `login_time`   VARCHAR(255)              DEFAULT '' COMMENT '登入时间',
  `last_action`  VARCHAR(255)              DEFAULT '' COMMENT '最后操作',
  `last_time`    VARCHAR(255)              DEFAULT '' COMMENT '最后操作时间',
  `logout_coins` INT(10) UNSIGNED          DEFAULT '0' COMMENT '登出筹码',
  `logout_gold`  INT(10) UNSIGNED          DEFAULT '0' COMMENT '登出金币',
  `logout_time`  VARCHAR(255)              DEFAULT '' COMMENT '登出时间',
  `online_time`  INT(10) UNSIGNED          DEFAULT '0' COMMENT '在线时长(秒)',
  PRIMARY KEY (`id`),
  KEY `search` (`dateid`, `online_time`),
  KEY `search2` (`uid`, `dateid`)
) ENGINE = InnoDB DEFAULT CHARSET = utf8 COMMENT = '用户登入登出记录，每月都要换表名_201506';

-- ----------------------------
--  Table structure for `lord_game_loginout_201607`
-- ----------------------------
DROP TABLE IF EXISTS `lord_game_loginout_201607`;
CREATE TABLE `lord_game_loginout_201607` (
  `id`           INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `dateid`       INT(8) UNSIGNED  NOT NULL DEFAULT '0' COMMENT '日期id',
  `uid`          INT(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'uid',
  `login_coins`  INT(10) UNSIGNED          DEFAULT '0' COMMENT '登入筹码',
  `login_gold`   INT(10) UNSIGNED          DEFAULT '0' COMMENT '登入金币',
  `login_time`   VARCHAR(255)              DEFAULT '' COMMENT '登入时间',
  `last_action`  VARCHAR(255)              DEFAULT '' COMMENT '最后操作',
  `last_time`    VARCHAR(255)              DEFAULT '' COMMENT '最后操作时间',
  `logout_coins` INT(10) UNSIGNED          DEFAULT '0' COMMENT '登出筹码',
  `logout_gold`  INT(10) UNSIGNED          DEFAULT '0' COMMENT '登出金币',
  `logout_time`  VARCHAR(255)              DEFAULT '' COMMENT '登出时间',
  `online_time`  INT(10) UNSIGNED          DEFAULT '0' COMMENT '在线时长(秒)',
  PRIMARY KEY (`id`),
  KEY `search` (`dateid`, `online_time`),
  KEY `search2` (`uid`, `dateid`)
) ENGINE = InnoDB AUTO_INCREMENT = 1200 DEFAULT CHARSET = utf8 COMMENT = '用户登入登出记录，每月都要换表名_201506';

-- ----------------------------
--  Table structure for `lord_game_loginout_201608`
-- ----------------------------
DROP TABLE IF EXISTS `lord_game_loginout_201608`;
CREATE TABLE `lord_game_loginout_201608` (
  `id`           INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `dateid`       INT(8) UNSIGNED  NOT NULL DEFAULT '0' COMMENT '日期id',
  `uid`          INT(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'uid',
  `login_coins`  INT(10) UNSIGNED          DEFAULT '0' COMMENT '登入筹码',
  `login_gold`   INT(10) UNSIGNED          DEFAULT '0' COMMENT '登入金币',
  `login_time`   VARCHAR(255)              DEFAULT '' COMMENT '登入时间',
  `last_action`  VARCHAR(255)              DEFAULT '' COMMENT '最后操作',
  `last_time`    VARCHAR(255)              DEFAULT '' COMMENT '最后操作时间',
  `logout_coins` INT(10) UNSIGNED          DEFAULT '0' COMMENT '登出筹码',
  `logout_gold`  INT(10) UNSIGNED          DEFAULT '0' COMMENT '登出金币',
  `logout_time`  VARCHAR(255)              DEFAULT '' COMMENT '登出时间',
  `online_time`  INT(10) UNSIGNED          DEFAULT '0' COMMENT '在线时长(秒)',
  PRIMARY KEY (`id`),
  KEY `search` (`dateid`, `online_time`),
  KEY `search2` (`uid`, `dateid`)
) ENGINE = InnoDB AUTO_INCREMENT = 27 DEFAULT CHARSET = utf8 COMMENT = '用户登入登出记录，每月都要换表名_201506';

-- ----------------------------
--  Table structure for `lord_game_notice`
-- ----------------------------
DROP TABLE IF EXISTS `lord_game_notice`;
CREATE TABLE `lord_game_notice` (
  `id`          INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `channel`     VARCHAR(32)               DEFAULT '' COMMENT '专属渠道',
  `subject`     VARCHAR(64)               DEFAULT '' COMMENT '标题',
  `content`     VARCHAR(255)              DEFAULT '' COMMENT '内容：文字[img]文字',
  `state`       TINYINT(3) UNSIGNED       DEFAULT '0' COMMENT '状态：0正常(待上/过期)1下架2删除',
  `start_time`  INT(10) UNSIGNED          DEFAULT '0' COMMENT '开始时间',
  `end_time`    INT(10) UNSIGNED          DEFAULT '0' COMMENT '结束时间',
  `sort`        TINYINT(3) UNSIGNED       DEFAULT '99' COMMENT '排序',
  `create_time` INT(10) UNSIGNED          DEFAULT '0' COMMENT '创建时间',
  `update_time` INT(10) UNSIGNED          DEFAULT '0' COMMENT '更新时间',
  PRIMARY KEY (`id`)
) ENGINE = InnoDB AUTO_INCREMENT = 9 DEFAULT CHARSET = utf8 COMMENT = '斗地主通知列表';

-- ----------------------------
--  Table structure for `lord_game_online`
-- ----------------------------
DROP TABLE IF EXISTS `lord_game_online`;
CREATE TABLE `lord_game_online` (
  `id`       INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `add_time` DATETIME         NOT NULL COMMENT '统计时间',
  `num`      INT(10) UNSIGNED          DEFAULT '0' COMMENT '在线人数',
  `playing`  INT(10) UNSIGNED          DEFAULT '0' COMMENT '在桌人数',
  PRIMARY KEY (`id`)
) ENGINE = InnoDB AUTO_INCREMENT = 368323 DEFAULT CHARSET = utf8;

-- ----------------------------
--  Table structure for `lord_game_prize`
-- ----------------------------
DROP TABLE IF EXISTS `lord_game_prize`;
CREATE TABLE `lord_game_prize` (
  `id`        INT(10) UNSIGNED     NOT NULL DEFAULT '0' COMMENT '编号',
  `cateid`    INT(10) UNSIGNED     NOT NULL DEFAULT '0' COMMENT '分类id	0未分1筹码2奖券3金币4道具5实物6礼包',
  `name`      VARCHAR(64)          NOT NULL DEFAULT '' COMMENT '名称',
  `icon`      VARCHAR(64)          NOT NULL DEFAULT '' COMMENT '图标(编号)',
  `info`      VARCHAR(255)         NOT NULL DEFAULT '' COMMENT '中奖弹框文字，为空时默认展示为：恭喜您抽中{$name}！请再接再厉。',
  `chance`    FLOAT(8, 4) UNSIGNED NOT NULL DEFAULT '0.0000' COMMENT '中奖机会',
  `gold`      INT(10) UNSIGNED     NOT NULL DEFAULT '0' COMMENT '中奖金币',
  `coupon`    INT(10) UNSIGNED     NOT NULL DEFAULT '0' COMMENT '中奖奖券',
  `coins`     INT(10) UNSIGNED     NOT NULL DEFAULT '0' COMMENT '中奖筹码',
  `propid`    INT(10) UNSIGNED     NOT NULL DEFAULT '0' COMMENT '中奖道具编号',
  `filter`    TEXT                 NOT NULL COMMENT 'jsontext扩展过滤器',
  `is_lock`   TINYINT(3) UNSIGNED  NOT NULL DEFAULT '0' COMMENT '锁定状态 0未锁定/>0不同的锁定状态',
  `is_del`    TINYINT(3) UNSIGNED  NOT NULL DEFAULT '0' COMMENT '删除状态 0未删除/>0不同的删除状态',
  `ut_create` INT(10) UNSIGNED     NOT NULL DEFAULT '0' COMMENT '创建时间',
  `ut_update` INT(10) UNSIGNED     NOT NULL DEFAULT '0' COMMENT '更新时间',
  PRIMARY KEY (`id`)
) ENGINE = InnoDB DEFAULT CHARSET = utf8;

-- ----------------------------
--  Table structure for `lord_game_prize_new`
-- ----------------------------
DROP TABLE IF EXISTS `lord_game_prize_new`;
CREATE TABLE `lord_game_prize_new` (
  `id`         INT(10) UNSIGNED    NOT NULL AUTO_INCREMENT COMMENT '编号',
  `version`    INT(10)                      DEFAULT NULL,
  `type`       INT(10)             NOT NULL DEFAULT '0' COMMENT '类型 1000,3000,5000',
  `cateid`     INT(10) UNSIGNED    NOT NULL DEFAULT '0' COMMENT '分类id 0未分1筹码2奖券3金币4道具5实物6礼包',
  `name`       VARCHAR(64)         NOT NULL DEFAULT '' COMMENT '名称',
  `icon`       VARCHAR(64)         NOT NULL DEFAULT '' COMMENT '图标(编号)',
  `info`       VARCHAR(255)        NOT NULL DEFAULT '' COMMENT '中奖弹框文字，为空时默认展示为：恭喜您抽中{$name}！请再接再厉。',
  `chance`     INT(10)                      DEFAULT NULL,
  `gold`       INT(10) UNSIGNED    NOT NULL DEFAULT '0' COMMENT '中奖金币',
  `coupon`     INT(10) UNSIGNED    NOT NULL DEFAULT '0' COMMENT '中奖奖券',
  `coins`      INT(10) UNSIGNED    NOT NULL DEFAULT '0' COMMENT '中奖筹码',
  `propid`     INT(10) UNSIGNED    NOT NULL DEFAULT '0' COMMENT '中奖道具编号',
  `filter`     TEXT                NOT NULL COMMENT 'jsontext扩展过滤器',
  `is_lock`    TINYINT(3) UNSIGNED NOT NULL DEFAULT '0' COMMENT '锁定状态 0未锁定/>0不同的锁定状态',
  `is_del`     TINYINT(3) UNSIGNED NOT NULL DEFAULT '0' COMMENT '删除状态 0未删除/>0不同的删除状态',
  `ut_create`  INT(10) UNSIGNED    NOT NULL DEFAULT '0' COMMENT '创建时间',
  `ut_update`  INT(10) UNSIGNED    NOT NULL DEFAULT '0' COMMENT '更新时间',
  `picture_id` INT(10)                      DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `version` (`version`)
) ENGINE = InnoDB AUTO_INCREMENT = 43 DEFAULT CHARSET = utf8;

-- ----------------------------
--  Table structure for `lord_game_prize_version`
-- ----------------------------
DROP TABLE IF EXISTS `lord_game_prize_version`;
CREATE TABLE `lord_game_prize_version` (
  `version`   INT(10)  NOT NULL DEFAULT '0',
  `ut_update` DATETIME NOT NULL COMMENT '更新时间'
) ENGINE = InnoDB DEFAULT CHARSET = utf8;

-- ----------------------------
--  Table structure for `lord_game_robot`
-- ----------------------------
DROP TABLE IF EXISTS `lord_game_robot`;
CREATE TABLE `lord_game_robot` (
  `id`       INT(11)          NOT NULL AUTO_INCREMENT,
  `uid`      INT(11)          NOT NULL COMMENT 'user_user.id',
  `cool_num` INT(11)          NOT NULL DEFAULT '0' COMMENT '靓号game_num.num',
  `nick`     VARCHAR(30)      NOT NULL COMMENT '游戏昵称',
  `sex`      TINYINT(4)       NOT NULL DEFAULT '1',
  `word`     VARCHAR(90)      NOT NULL,
  `gold`     INT(11)          NOT NULL COMMENT '当前金币',
  `coins`    BIGINT(20)       NOT NULL COMMENT '当前筹码',
  `coupon`   INT(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '奖券',
  `lottery`  INT(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '抽奖数',
  `level`    INT(11)          NOT NULL COMMENT '等级',
  `exp`      INT(11)          NOT NULL COMMENT '本级经验值',
  `avatar`   MEDIUMINT(9)     NOT NULL DEFAULT '0' COMMENT '头像',
  `point`    INT(11)          NOT NULL COMMENT '待定',
  `channel`  VARCHAR(50)      NOT NULL COMMENT '当前渠道号',
  `ver`      VARCHAR(30)      NOT NULL COMMENT '客户端版本',
  `is_tv`    SMALLINT(6)      NOT NULL DEFAULT '1' COMMENT '是否电视环境',
  `state`    MEDIUMINT(9)     NOT NULL COMMENT '状态',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uid` (`uid`) USING BTREE,
  KEY `coins` (`coins`) USING BTREE,
  KEY `cool_num` (`cool_num`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 65885 DEFAULT CHARSET = utf8 ROW_FORMAT = COMPACT COMMENT = '游戏机器人';

-- ----------------------------
--  Table structure for `lord_game_room`
-- ----------------------------
DROP TABLE IF EXISTS `lord_game_room`;
CREATE TABLE `lord_game_room` (
  `id`          INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `isOpen`      TINYINT(1) UNSIGNED       DEFAULT '0' COMMENT '全局开关 是否显示本场次',
  `isMobi`      TINYINT(1) UNSIGNED       DEFAULT '0' COMMENT '手机开关 是否在所有sj开头的手机渠道显示本场次',
  `verMin`      INT(5) UNSIGNED           DEFAULT '0' COMMENT '版本开关 超过此客户端版本号的用户才显示本场次',
  `modelId`     INT(10) UNSIGNED          DEFAULT '0' COMMENT '模式0普通1竞技2赖子3比赛91广告',
  `mode`        VARCHAR(32)               DEFAULT '' COMMENT '赛制名称 modelId的冗余字段，同modelId必须相同',
  `roomId`      INT(10) UNSIGNED          DEFAULT '0' COMMENT '房间编号',
  `room`        VARCHAR(32)               DEFAULT '' COMMENT '房间名称 同name',
  `name`        VARCHAR(32)               DEFAULT '' COMMENT '房间名称，同room',
  `showRules`   TEXT COMMENT '显隐规则',
  `baseCoins`   INT(10) UNSIGNED          DEFAULT '0' COMMENT '底分',
  `rate`        INT(10) UNSIGNED          DEFAULT '15' COMMENT '初始倍率',
  `rateMax`     INT(10) UNSIGNED          DEFAULT '2100000000' COMMENT '上限倍率',
  `limitCoins`  INT(10) UNSIGNED          DEFAULT '2100000000' COMMENT '赢分上限',
  `rake`        INT(10) UNSIGNED          DEFAULT '0' COMMENT '抽水',
  `enter`       VARCHAR(64)               DEFAULT '' COMMENT '入场限制文字',
  `enterLimit`  INT(10) UNSIGNED          DEFAULT '0' COMMENT '入场下限',
  `enterLimit_` INT(10) UNSIGNED          DEFAULT '2100000000' COMMENT '入场上限',
  `gameBombAdd` TINYINT(2) UNSIGNED       DEFAULT '0' COMMENT '炸弹基数',
  `brief`       VARCHAR(32)               DEFAULT '' COMMENT '开场简介 不可过长',
  `entry`       VARCHAR(32)               DEFAULT '' COMMENT '报名简介 不可过长',
  `tips`        VARCHAR(64)               DEFAULT '' COMMENT '本场提示 不可过长',
  `rules`       TEXT COMMENT '本场规则 可换行',
  `start`       INT(10) UNSIGNED          DEFAULT '0' COMMENT '开赛时间 n=0达到下限即开 0<n<=86400每天定时开赛 86400*1<n<=86400*8每周定时开赛 86400*8<n<=86400*39每月定时开赛 86400*39<n日期时间开赛',
  `entryMoney`  VARCHAR(32)               DEFAULT '' COMMENT '报名货币 默认可选coins/coupon，如果为特殊积分等数值设定，则进入额外逻辑',
  `entryCost`   INT(10) UNSIGNED          DEFAULT '0' COMMENT '报名额度 0为免费报名',
  `entryTime`   INT(10) UNSIGNED          DEFAULT '0' COMMENT '报名时间 n=0默认随时报名 n>0开赛时间之前的n秒之内才可以报名',
  `entryOut`    TINYINT(1) UNSIGNED       DEFAULT '0' COMMENT '取消报名 0不可取消 1返还货币 2不返货币',
  `entryOsec`   INT(10) UNSIGNED          DEFAULT '0' COMMENT '报名之后多少秒内不可取消报名',
  `entryOmax`   INT(10) UNSIGNED          DEFAULT '0' COMMENT '达到N人数后不可取消报名',
  `entryMax`    INT(10) UNSIGNED          DEFAULT '0' COMMENT '报名上限',
  `entryMin`    INT(10) UNSIGNED          DEFAULT '0' COMMENT '报名下限',
  `entryFull`   TINYINT(1) UNSIGNED       DEFAULT '0' COMMENT '报名人数达到上限后 0直接开赛且新开一场 1直接开赛且不开新场 2不可开赛且不可报名',
  `entryMore`   TINYINT(1) UNSIGNED       DEFAULT '0' COMMENT '时间已到,报名人数达到下限但有3余时 0填补机器人到最小整桌 1踢掉多余用户(暂不支持)',
  `entryLess`   TINYINT(1) UNSIGNED       DEFAULT '0' COMMENT '时间已到,报名人数不够下限 0填补机器人到下限 1填补机器人到最小整桌(暂不支持) 2遣散所有用户(暂不支持)',
  `scoreInit`   INT(10) UNSIGNED          DEFAULT '0' COMMENT '开局初始积分',
  `scoreRate`   FLOAT UNSIGNED            DEFAULT '1' COMMENT '再局积分缩水',
  `rankRule`    TINYINT(1) UNSIGNED       DEFAULT '0' COMMENT '排名规则 0积分高低报名先后 1不做排名积分他用(暂不支持)',
  `tableRule`   TINYINT(1) UNSIGNED       DEFAULT '0' COMMENT '组桌规则	0排名凑桌 1随机凑桌  没有产生排名或排名规则为1时强制使用1随机凑桌',
  `outRule`     TINYINT(1) UNSIGNED       DEFAULT '0' COMMENT '淘汰规则 0排名递进淘汰 1积分递进淘汰(暂不支持) 2积分即时淘汰(暂不支持)',
  `outValue`    VARCHAR(255)              DEFAULT '' COMMENT '淘汰数值 依据淘汰规则，可能为逗号分隔，可能为某个数字',
  `awardRule`   TEXT COMMENT '奖励规则json',
  `apkurl`      VARCHAR(255)              DEFAULT '' COMMENT '广告场apk网址',
  `isForce`     TINYINT(1) UNSIGNED       DEFAULT '0' COMMENT 'apkurl是否强制下载更新',
  `appid`       INT(10) UNSIGNED          DEFAULT '0' COMMENT '广告场apkid',
  `ver`         VARCHAR(10)               DEFAULT '' COMMENT '广告场apk版本号',
  `vercode`     INT(10) UNSIGNED          DEFAULT '0' COMMENT '广告场apk数字版本号',
  `bytes`       BIGINT(20) UNSIGNED       DEFAULT '0' COMMENT '广告场apk字节数',
  `desc`        VARCHAR(255)              DEFAULT '' COMMENT '广告场apk描述',
  `md5`         VARCHAR(32)               DEFAULT '' COMMENT '广告场apk校验',
  `package`     VARCHAR(255)              DEFAULT '' COMMENT '广告场apk包类名',
  `is_del`      TINYINT(1) UNSIGNED       DEFAULT '0' COMMENT '是否已删',
  `sort`        INT(10) UNSIGNED          DEFAULT '1099' COMMENT '排序',
  `update_time` INT(10) UNSIGNED          DEFAULT '0',
  `create_time` INT(10) UNSIGNED          DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `roomId` (`roomId`)
) ENGINE = InnoDB AUTO_INCREMENT = 11 DEFAULT CHARSET = utf8 COMMENT = '斗地主房间配置表';

-- ----------------------------
--  Table structure for `lord_game_surprise`
-- ----------------------------
DROP TABLE IF EXISTS `lord_game_surprise`;
CREATE TABLE `lord_game_surprise` (
  `id`          INT(10) UNSIGNED    NOT NULL AUTO_INCREMENT COMMENT '编号',
  `teskids`     VARCHAR(255)                 DEFAULT '' COMMENT '专属活动ids，json',
  `name`        VARCHAR(64)         NOT NULL DEFAULT '' COMMENT '爆出物名称',
  `keyName`     VARCHAR(32)         NOT NULL DEFAULT '' COMMENT '字段名',
  `keyVal`      INT(10) UNSIGNED    NOT NULL DEFAULT '0' COMMENT '字段主要值',
  `keyExt`      INT(10) UNSIGNED             DEFAULT '0' COMMENT '字段扩展值',
  `fileid`      INT(10) UNSIGNED    NOT NULL DEFAULT '0' COMMENT '文件id',
  `periodName`  VARCHAR(32)         NOT NULL DEFAULT '' COMMENT '周期名称',
  `periodTime`  INT(10) UNSIGNED    NOT NULL DEFAULT '0' COMMENT '周期循环秒',
  `periodId`    INT(10) UNSIGNED    NOT NULL DEFAULT '0' COMMENT '本周期id',
  `periodStart` INT(10) UNSIGNED    NOT NULL DEFAULT '0' COMMENT '本周期开始时间',
  `periodEnd`   INT(10) UNSIGNED    NOT NULL DEFAULT '0' COMMENT '本周期结束时间',
  `times`       INT(10) UNSIGNED    NOT NULL DEFAULT '0' COMMENT '每周期爆出次数上限',
  `rests`       INT(10) UNSIGNED    NOT NULL DEFAULT '0' COMMENT '本周期内剩余次数',
  `chance`      INT(10) UNSIGNED             DEFAULT '0' COMMENT '爆出概率%*10000',
  `mailSubject` VARCHAR(64)         NOT NULL DEFAULT '' COMMENT '邮件标题 [抢到|获得]',
  `mailContent` VARCHAR(255)        NOT NULL DEFAULT '' COMMENT '邮件内容',
  `mailFileid`  INT(10) UNSIGNED    NOT NULL DEFAULT '0' COMMENT '邮件图片',
  `is_grab`     TINYINT(3) UNSIGNED          DEFAULT '0' COMMENT '0不可抢1可抢',
  `is_del`      TINYINT(3) UNSIGNED          DEFAULT '0' COMMENT '0正常1删除',
  `sort`        TINYINT(3) UNSIGNED NOT NULL DEFAULT '99' COMMENT '排序',
  `create_time` INT(10) UNSIGNED             DEFAULT '0' COMMENT '创建时间',
  `update_time` INT(10) UNSIGNED             DEFAULT '0' COMMENT '更新时间',
  PRIMARY KEY (`id`)
) ENGINE = InnoDB AUTO_INCREMENT = 25 DEFAULT CHARSET = utf8;

-- ----------------------------
--  Table structure for `lord_game_task`
-- ----------------------------
DROP TABLE IF EXISTS `lord_game_task`;
CREATE TABLE `lord_game_task` (
  `id`        INT(10) UNSIGNED    NOT NULL DEFAULT '0' COMMENT '编号',
  `name`      VARCHAR(64)         NOT NULL DEFAULT '' COMMENT '名称',
  `actions`   TEXT                NOT NULL COMMENT '绑定协议ids	jsontext:array(050001,50106…)',
  `columns`   TEXT                NOT NULL COMMENT '相关字段	jsontext:array(user.uid,gold_level…)',
  `is_get`    TINYINT(1) UNSIGNED NOT NULL DEFAULT '0' COMMENT '是否需要领取	0不需领取自动执行1需要领取',
  `if_pre`    TEXT                NOT NULL COMMENT '前置/领取条件(且) jsontext:array(array(key,leg,val))',
  `if_not`    TEXT                NOT NULL COMMENT '失败条件(或)失败优先 jsontext:array(array(key,leg,val))',
  `if_nrs`    TEXT                NOT NULL COMMENT '失败执行(且) jsontext:array(array(key,val))',
  `if_yes`    TEXT                NOT NULL COMMENT '成功条件(且) jsontext:array(array(key,leg,val))',
  `if_yrs`    TEXT                NOT NULL COMMENT '成功执行(且) jsontext:array(array(key,val))',
  `days`      TINYINT(3) UNSIGNED NOT NULL DEFAULT '0' COMMENT '任务周期(天) 0一次性的任务/N以N天为一周期 /1每天日常任务/7每周任务/30每月任务/其他任意周期任务',
  `times`     TINYINT(3) UNSIGNED NOT NULL DEFAULT '0' COMMENT '每天次数(次) 0没有次数限制/N每天可执行N次。 days=1 times=2，意味着每天一轮每天两次',
  `opening`   TEXT                NOT NULL COMMENT '开放设置/空字符永久开放/array("2014-06-01 09:00:00|2018-06-31 23:30:00|1234567",...)',
  `is_lock`   TINYINT(3) UNSIGNED NOT NULL DEFAULT '0' COMMENT '锁定状态 0未锁定/>0不同的锁定状态',
  `is_del`    TINYINT(3) UNSIGNED NOT NULL DEFAULT '0' COMMENT '删除状态 0未删除/>0不同的删除状态',
  `ut_create` INT(10) UNSIGNED    NOT NULL DEFAULT '0' COMMENT '创建时间',
  `ut_update` INT(10) UNSIGNED    NOT NULL DEFAULT '0' COMMENT '更新时间',
  PRIMARY KEY (`id`)
) ENGINE = InnoDB DEFAULT CHARSET = utf8;

-- ----------------------------
--  Table structure for `lord_game_tesk`
-- ----------------------------
DROP TABLE IF EXISTS `lord_game_tesk`;
CREATE TABLE `lord_game_tesk` (
  `id`          INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `type`        TINYINT(3) UNSIGNED       DEFAULT '0' COMMENT '类型 0每日任务1成长任务2活动任务',
  `name`        VARCHAR(64)      NOT NULL DEFAULT '' COMMENT '任务名',
  `prev`        INT(10) UNSIGNED          DEFAULT '0' COMMENT '前置任务id',
  `goto`        TINYINT(3) UNSIGNED       DEFAULT '0' COMMENT '任务引导 0无引导1去普通场2去竞技场3去充值中心',
  `rooms`       VARCHAR(255)     NOT NULL DEFAULT '' COMMENT '房间号,英文空格分割',
  `channels`    VARCHAR(255)     NOT NULL DEFAULT '' COMMENT '渠道号,英文空格分割',
  `users`       VARCHAR(255)     NOT NULL DEFAULT '' COMMENT '用户UID,英文空格分割',
  `start_time`  INT(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '开始时间',
  `end_time`    INT(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '结束时间',
  `periodName`  VARCHAR(64)      NOT NULL DEFAULT '' COMMENT '周期名',
  `periodTime`  INT(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '周期循环秒',
  `periodId`    INT(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '本周期id',
  `periodStart` INT(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '本周期开始时间',
  `periodEnd`   INT(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '本周期结束时间',
  `sourceId`    INT(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '源码id',
  `accode`      INT(6) UNSIGNED  NOT NULL DEFAULT '0' COMMENT '协议号',
  `action`      VARCHAR(16)      NOT NULL DEFAULT '' COMMENT '协议名/函数名',
  `acttag`      VARCHAR(16)      NOT NULL DEFAULT '' COMMENT '任务号',
  `execut`      TEXT             NOT NULL COMMENT '执行逻辑json',
  `condit`      TEXT             NOT NULL COMMENT '达成条件json',
  `result`      TEXT             NOT NULL COMMENT '达成结果json',
  `target`      INT(10) UNSIGNED          DEFAULT '1' COMMENT '任务目标数值',
  `prizeName`   VARCHAR(255)     NOT NULL DEFAULT '' COMMENT '奖品统称，用于顶栏下拉提示。',
  `prizes`      TEXT             NOT NULL COMMENT '达成奖励json',
  `mailSubject` VARCHAR(64)      NOT NULL DEFAULT '' COMMENT '达成邮件标题',
  `mailContent` VARCHAR(255)     NOT NULL DEFAULT '' COMMENT '达成邮件内容',
  `mailFileid`  INT(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '邮件用图',
  `is_surprise` TINYINT(1) UNSIGNED       DEFAULT '0' COMMENT '是否有惊喜',
  `is_online`   TINYINT(1) UNSIGNED       DEFAULT '0' COMMENT '0下线1上线',
  `is_del`      TINYINT(1) UNSIGNED       DEFAULT '0' COMMENT '0正常1删除',
  `sort`        TINYINT(3) UNSIGNED       DEFAULT '99' COMMENT '排序',
  `create_time` INT(10) UNSIGNED          DEFAULT '0',
  `update_time` INT(10) UNSIGNED          DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE = InnoDB AUTO_INCREMENT = 10269 DEFAULT CHARSET = utf8 COMMENT = '斗地主活动任务表';

-- ----------------------------
--  Table structure for `lord_game_tesksource`
-- ----------------------------
DROP TABLE IF EXISTS `lord_game_tesksource`;
CREATE TABLE `lord_game_tesksource` (
  `id`          INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name`        VARCHAR(64)      NOT NULL DEFAULT '' COMMENT '任务源码名',
  `accode`      INT(6) UNSIGNED  NOT NULL DEFAULT '0' COMMENT '协议号',
  `action`      VARCHAR(16)      NOT NULL DEFAULT '' COMMENT '协议名/函数名',
  `acttag`      VARCHAR(16)      NOT NULL DEFAULT '' COMMENT '任务号',
  `acname`      VARCHAR(16)      NOT NULL DEFAULT '' COMMENT '行为统称',
  `execut`      TEXT             NOT NULL COMMENT '执行逻辑json',
  `condit`      TEXT             NOT NULL COMMENT '达成条件json',
  `result`      TEXT             NOT NULL COMMENT '达成结果json',
  `is_del`      TINYINT(1) UNSIGNED       DEFAULT '0' COMMENT '0正常1删除',
  `sort`        TINYINT(3) UNSIGNED       DEFAULT '99' COMMENT '排序',
  `create_time` INT(10) UNSIGNED          DEFAULT '0',
  `update_time` INT(10) UNSIGNED          DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE = InnoDB AUTO_INCREMENT = 235 DEFAULT CHARSET = utf8 COMMENT = '斗地主活动任务表';

-- ----------------------------
--  Table structure for `lord_game_tips`
-- ----------------------------
DROP TABLE IF EXISTS `lord_game_tips`;
CREATE TABLE `lord_game_tips` (
  `id`          INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `channel`     VARCHAR(32)               DEFAULT '' COMMENT '渠道',
  `path`        VARCHAR(32)               DEFAULT 'global' COMMENT '路径',
  `content`     VARCHAR(128)              DEFAULT '测试' COMMENT '提示',
  `version`     INT(10) UNSIGNED          DEFAULT '0' COMMENT '最后操作所处版本',
  `ver_ins`     INT(10) UNSIGNED          DEFAULT '0' COMMENT '新建时所处版本',
  `ver_upd`     INT(10) UNSIGNED          DEFAULT '0' COMMENT '更新时所处版本',
  `ver_del`     INT(10) UNSIGNED          DEFAULT '0' COMMENT '删除时所处版本',
  `sort`        TINYINT(3) UNSIGNED       DEFAULT '99' COMMENT '排序',
  `is_del`      TINYINT(3) UNSIGNED       DEFAULT '0' COMMENT '是否删除，已删除的不可再更新',
  `create_time` INT(10) UNSIGNED          DEFAULT '0' COMMENT '创建时间',
  `update_time` INT(10) UNSIGNED          DEFAULT '0' COMMENT '更新时间',
  PRIMARY KEY (`id`)
) ENGINE = InnoDB AUTO_INCREMENT = 17 DEFAULT CHARSET = utf8 COMMENT = '斗地主游戏左下角提示';

-- ----------------------------
--  Table structure for `lord_game_topic`
-- ----------------------------
DROP TABLE IF EXISTS `lord_game_topic`;
CREATE TABLE `lord_game_topic` (
  `id`          INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `channel`     VARCHAR(255)              DEFAULT '' COMMENT '渠道白名单 空格隔开',
  `channot`     VARCHAR(255)              DEFAULT '' COMMENT '渠道黑名 单空格隔开',
  `subject`     VARCHAR(64)               DEFAULT '' COMMENT '标题',
  `content`     VARCHAR(255)              DEFAULT '' COMMENT '内容：文字[img]文字',
  `start_time`  INT(10) UNSIGNED          DEFAULT '0' COMMENT '开始时间',
  `end_time`    INT(10) UNSIGNED          DEFAULT '0' COMMENT '结束时间',
  `start_lobby` INT(10) UNSIGNED          DEFAULT '0' COMMENT '成为大厅热门活动的开始时间',
  `end_lobby`   INT(10) UNSIGNED          DEFAULT '0' COMMENT '成为大厅热门活动的结束时间',
  `prizes`      VARCHAR(255)              DEFAULT '' COMMENT '奖项json:{"coins":1000...}',
  `sort`        TINYINT(3) UNSIGNED       DEFAULT '99' COMMENT '排序',
  `state`       TINYINT(3) UNSIGNED       DEFAULT '0' COMMENT '状态：0正常(待上/过期)1下架2删除',
  `create_time` INT(10) UNSIGNED          DEFAULT '0' COMMENT '创建时间',
  `update_time` INT(10) UNSIGNED          DEFAULT '0' COMMENT '更新时间',
  PRIMARY KEY (`id`)
) ENGINE = InnoDB AUTO_INCREMENT = 79 DEFAULT CHARSET = utf8 COMMENT = '斗地主活动列表';

-- ----------------------------
--  Table structure for `lord_game_user`
-- ----------------------------
DROP TABLE IF EXISTS `lord_game_user`;
CREATE TABLE `lord_game_user` (
  `id`                   INT(11)      NOT NULL AUTO_INCREMENT,
  `uid`                  INT(11)      NOT NULL COMMENT 'user_user.id',
  `cool_num`             INT(11)      NOT NULL DEFAULT '0' COMMENT '靓号game_num.num',
  `nick`                 VARCHAR(30)  NOT NULL COMMENT '游戏昵称',
  `sex`                  TINYINT(1) UNSIGNED   DEFAULT '0' COMMENT '性别:0未知1男2女',
  `age`                  TINYINT(1) UNSIGNED   DEFAULT '0' COMMENT '年龄:未知|1-12|13-18|19-26|27-36|37-50|50-60|61-120',
  `word`                 VARCHAR(80)  NOT NULL,
  `gold`                 INT(11)      NOT NULL COMMENT '当前金币',
  `coins`                BIGINT(20)   NOT NULL COMMENT '当前筹码',
  `coupon`               INT(10) UNSIGNED      DEFAULT '0' COMMENT '可用奖券数',
  `lottery`              INT(10) UNSIGNED      DEFAULT '0' COMMENT '可用抽奖数',
  `trial_coins`          BIGINT(20)   NOT NULL COMMENT '体验筹码',
  `trial_count`          INT(11)      NOT NULL COMMENT '体验计数',
  `match_coins`          BIGINT(20)   NOT NULL COMMENT '比赛筹码',
  `match_count`          INT(11)      NOT NULL COMMENT '比赛计数',
  `offline_gold`         BIGINT(20)   NOT NULL DEFAULT '0' COMMENT '离线累积金币',
  `offline_coins`        BIGINT(20)   NOT NULL DEFAULT '0' COMMENT '离线累积筹码',
  `offline_charge_gold`  BIGINT(20)   NOT NULL DEFAULT '0' COMMENT '离线累积充值金币',
  `offline_charge_coins` BIGINT(20)   NOT NULL DEFAULT '0' COMMENT '离线累积充值筹码',
  `lock_coins`           BIGINT(20)   NOT NULL DEFAULT '0' COMMENT '锁定筹码(用于异常恢复)',
  `vip_exp`              DATETIME              DEFAULT NULL COMMENT 'vip期限',
  `vip_lv`               INT(11)      NOT NULL COMMENT 'vip等级',
  `level`                INT(11)      NOT NULL COMMENT '等级',
  `exp`                  INT(11)      NOT NULL COMMENT '本级经验值',
  `avatar`               MEDIUMINT(9) NOT NULL DEFAULT '0' COMMENT '头像',
  `check_code`           VARCHAR(10)  NOT NULL COMMENT '微信校验码',
  `wechat`               VARCHAR(50)  NOT NULL COMMENT '绑定微信',
  `channel`              VARCHAR(50)  NOT NULL COMMENT '当前渠道号',
  `ver`                  VARCHAR(30)  NOT NULL COMMENT '客户端版本',
  `is_tv`                SMALLINT(6)  NOT NULL DEFAULT '1' COMMENT '是否电视环境',
  `state`                MEDIUMINT(9) NOT NULL COMMENT '状态',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uid` (`uid`) USING BTREE,
  KEY `cool_num` (`cool_num`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 343452 DEFAULT CHARSET = utf8 ROW_FORMAT = COMPACT COMMENT = '游戏用户';

-- ----------------------------
--  Table structure for `lord_game_version`
-- ----------------------------
DROP TABLE IF EXISTS `lord_game_version`;
CREATE TABLE `lord_game_version` (
  `id`         INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name`       VARCHAR(32)               DEFAULT '' COMMENT '名称: version服端|verconf配置|verfile素材|vertips提示',
  `version`    INT(10) UNSIGNED          DEFAULT '0' COMMENT '版本号',
  `start_time` INT(10) UNSIGNED          DEFAULT '0' COMMENT '开始时间:上个版本的结束时间+1',
  `end_time`   INT(10) UNSIGNED          DEFAULT '0' COMMENT '结束时间:发布时间',
  `comments`   VARCHAR(128)              DEFAULT '' COMMENT '备注，发布时填写',
  `is_done`    TINYINT(3) UNSIGNED       DEFAULT '0' COMMENT '是否发布，发布后自动创建version+1的同名新版本',
  PRIMARY KEY (`id`)
) ENGINE = InnoDB AUTO_INCREMENT = 165 DEFAULT CHARSET = utf8 COMMENT = '斗地主服务器端版本控制表';

-- ----------------------------
--  Table structure for `lord_list_convert`
-- ----------------------------
DROP TABLE IF EXISTS `lord_list_convert`;
CREATE TABLE `lord_list_convert` (
  `id`           INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `type`         VARCHAR(32)      NOT NULL DEFAULT '' COMMENT '类型',
  `channel`      VARCHAR(255)     NOT NULL DEFAULT '' COMMENT '渠道',
  `title`        VARCHAR(32)      NOT NULL DEFAULT '' COMMENT '名称',
  `fileId`       INT(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '图片id',
  `value`        INT(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '价值',
  `price`        INT(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '价格',
  `recharge`     INT(6) UNSIGNED           DEFAULT '0' COMMENT '用户消费达到N以上才显示',
  `store`        INT(10)                   DEFAULT '-1' COMMENT '库存 -1无上限>=0真实库存',
  `is_onsale`    TINYINT(3) UNSIGNED       DEFAULT '0' COMMENT '促销状态 0正常1促销',
  `onsale`       VARCHAR(32)               DEFAULT '' COMMENT '促销文字',
  `is_recommend` TINYINT(1) UNSIGNED       DEFAULT '0' COMMENT '是否推荐',
  `state`        TINYINT(3) UNSIGNED       DEFAULT '1' COMMENT '状态 0正常1下架2删除',
  `sort`         TINYINT(3) UNSIGNED       DEFAULT '99' COMMENT '排序',
  `create_time`  INT(10) UNSIGNED          DEFAULT '0' COMMENT '创建时间',
  `update_time`  INT(10) UNSIGNED          DEFAULT '0' COMMENT '更新时间',
  PRIMARY KEY (`id`),
  KEY `s0` (`type`),
  KEY `is_recommend` (`is_recommend`)
) ENGINE = InnoDB AUTO_INCREMENT = 43 DEFAULT CHARSET = utf8;

-- ----------------------------
--  Table structure for `lord_list_goods`
-- ----------------------------
DROP TABLE IF EXISTS `lord_list_goods`;
CREATE TABLE `lord_list_goods` (
  `id`           INT(10) UNSIGNED    NOT NULL AUTO_INCREMENT,
  `cd`           TINYINT(3) UNSIGNED NOT NULL DEFAULT '0' COMMENT '分类',
  `channel`      VARCHAR(255)        NOT NULL DEFAULT '' COMMENT '渠道白名单',
  `channot`      VARCHAR(255)                 DEFAULT '' COMMENT '渠道黑名单',
  `name`         VARCHAR(32)         NOT NULL DEFAULT '' COMMENT '名称',
  `resume`       VARCHAR(255)                 DEFAULT '' COMMENT '简介',
  `fileId`       INT(10) UNSIGNED    NOT NULL DEFAULT '0' COMMENT '图片id',
  `taskid`       INT(10) UNSIGNED             DEFAULT '0' COMMENT '关联任务ID 0无关联 >0完成任务后不再显示',
  `money`        VARCHAR(16)                  DEFAULT '' COMMENT '货币',
  `price`        INT(10) UNSIGNED    NOT NULL DEFAULT '0' COMMENT '价格',
  `buyto`        VARCHAR(255)                 DEFAULT '' COMMENT '商品盒',
  `iid`          INT(10) UNSIGNED             DEFAULT '0' COMMENT '首选关联物品id',
  `store`        INT(10)                      DEFAULT '-1' COMMENT '库存 -1无上限>=0真实库存',
  `is_onsale`    TINYINT(3) UNSIGNED          DEFAULT '0' COMMENT '促销状态 0正常1促销',
  `onsale`       VARCHAR(32)                  DEFAULT '' COMMENT '促销文字',
  `is_recommend` TINYINT(1) UNSIGNED          DEFAULT '0' COMMENT '是否推荐:0否1是',
  `state`        TINYINT(3) UNSIGNED          DEFAULT '1' COMMENT '状态 0正常1下架2删除',
  `sort`         TINYINT(3) UNSIGNED          DEFAULT '99' COMMENT '排序',
  `is_hide`      TINYINT(3) UNSIGNED          DEFAULT '0' COMMENT '是否隐藏',
  `create_time`  INT(10) UNSIGNED             DEFAULT '0' COMMENT '创建时间',
  `update_time`  INT(10) UNSIGNED             DEFAULT '0' COMMENT '更新时间',
  PRIMARY KEY (`id`)
) ENGINE = InnoDB AUTO_INCREMENT = 79 DEFAULT CHARSET = utf8;

-- ----------------------------
--  Table structure for `lord_list_item`
-- ----------------------------
DROP TABLE IF EXISTS `lord_list_item`;
CREATE TABLE `lord_list_item` (
  `id`          INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `cd`          INT(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '分类ID',
  `pd`          INT(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '道具ID',
  `name`        VARCHAR(64)      NOT NULL DEFAULT '' COMMENT '道具名称',
  `resume`      VARCHAR(255)              DEFAULT '' COMMENT '简述',
  `fileId`      INT(10) UNSIGNED          DEFAULT '0' COMMENT '图片ID',
  `number`      TINYINT(3) UNSIGNED       DEFAULT '0' COMMENT '叠加数量 0不限',
  `second`      INT(10) UNSIGNED          DEFAULT '0' COMMENT '可用时效 0不限',
  `points`      INT(10) UNSIGNED          DEFAULT '0' COMMENT '可用持久 0无损',
  `present`     TINYINT(1) UNSIGNED       DEFAULT '0' COMMENT '可否赠送(0不可|1可以)',
  `pause`       TINYINT(1) UNSIGNED       DEFAULT '0' COMMENT '可否暂停 0不可1可以',
  `repair`      TINYINT(1) UNSIGNED       DEFAULT '0' COMMENT '可否修复 0不可1可以',
  `useas`       TINYINT(1) UNSIGNED       DEFAULT '0' COMMENT '使用用途(0无|1改变状态|2增加乐币|3增加代币|4增加乐豆|5增加乐券|6增加抽奖数|8增加物品ID|9增加实物ID)',
  `useto`       INT(10) UNSIGNED          DEFAULT '1' COMMENT '使用效值 依据useas设置',
  `state`       TINYINT(1) UNSIGNED       DEFAULT '0' COMMENT '状态: 0正常1下架2删除',
  `sort`        TINYINT(3) UNSIGNED       DEFAULT '99' COMMENT '排序',
  `create_time` INT(10) UNSIGNED          DEFAULT '0' COMMENT '创建时间',
  `update_time` INT(10) UNSIGNED          DEFAULT '0' COMMENT '更新时间',
  PRIMARY KEY (`id`),
  KEY `search` (`cd`)
) ENGINE = InnoDB AUTO_INCREMENT = 22 DEFAULT CHARSET = utf8 COMMENT = '道具表';

-- ----------------------------
--  Table structure for `lord_list_prop`
-- ----------------------------
DROP TABLE IF EXISTS `lord_list_prop`;
CREATE TABLE `lord_list_prop` (
  `id`          INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `cd`          INT(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '分类ID',
  `cate`        VARCHAR(32)               DEFAULT '' COMMENT '分类名',
  `name`        VARCHAR(64)      NOT NULL DEFAULT '' COMMENT '物品名称',
  `resume`      VARCHAR(255)              DEFAULT '' COMMENT '简述',
  `fileId`      INT(10) UNSIGNED          DEFAULT '0' COMMENT '图片ID',
  `sex`         TINYINT(1) UNSIGNED       DEFAULT '0' COMMENT '性别(0通用|1男|2女)',
  `showin`      TINYINT(1) UNSIGNED       DEFAULT '0' COMMENT '物品显示(0都显示|1不在背包||)',
  `overlay`     TINYINT(1) UNSIGNED       DEFAULT '0' COMMENT '物品叠加(0不可叠加|1叠加数量|2叠加时效|3叠加持久)',
  `mutex`       TINYINT(1) UNSIGNED       DEFAULT '0' COMMENT '使用时同类互斥(0不会|1互斥)',
  `useby`       TINYINT(1) UNSIGNED       DEFAULT '0' COMMENT '使用方式(0拥有即用|1缺失使用|2手动使用)',
  `usedo`       TINYINT(1) UNSIGNED       DEFAULT '0' COMMENT '使用操作(0无|1降低数量|2降低时效|3降低持久)',
  `useup`       TINYINT(1) UNSIGNED       DEFAULT '0' COMMENT '使用完毕(0销毁|1不处理|2状态2|3状态3|4状态4||)',
  `create_time` INT(10) UNSIGNED          DEFAULT '0' COMMENT '创建时间',
  `update_time` INT(10) UNSIGNED          DEFAULT '0' COMMENT '更新时间',
  PRIMARY KEY (`id`),
  KEY `search` (`cd`)
) ENGINE = InnoDB AUTO_INCREMENT = 10 DEFAULT CHARSET = utf8 COMMENT = '游戏内置道具表';

-- ----------------------------
--  Table structure for `lord_list_recharge`
-- ----------------------------
DROP TABLE IF EXISTS `lord_list_recharge`;
CREATE TABLE `lord_list_recharge` (
  `id`          INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `type`        VARCHAR(32)      NOT NULL DEFAULT '' COMMENT '类型',
  `channel`     VARCHAR(32)      NOT NULL DEFAULT '' COMMENT '渠道',
  `title`       VARCHAR(32)      NOT NULL DEFAULT '' COMMENT '名称',
  `fileId`      INT(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '图片id',
  `value`       INT(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '价值',
  `price`       INT(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '价格',
  `store`       INT(10)                   DEFAULT '-1' COMMENT '库存 -1无上限>=0真实库存',
  `is_onsale`   TINYINT(3) UNSIGNED       DEFAULT '0' COMMENT '促销状态 0正常1促销',
  `onsale`      VARCHAR(32)               DEFAULT '' COMMENT '促销文字',
  `state`       TINYINT(3) UNSIGNED       DEFAULT '1' COMMENT '状态 0正常1下架2删除',
  `sort`        TINYINT(3) UNSIGNED       DEFAULT '99' COMMENT '排序',
  `create_time` INT(10) UNSIGNED          DEFAULT '0' COMMENT '创建时间',
  `update_time` INT(10) UNSIGNED          DEFAULT '0' COMMENT '更新时间',
  PRIMARY KEY (`id`)
) ENGINE = InnoDB AUTO_INCREMENT = 5 DEFAULT CHARSET = utf8;

-- ----------------------------
--  Table structure for `lord_list_trialcd`
-- ----------------------------
DROP TABLE IF EXISTS `lord_list_trialcd`;
CREATE TABLE `lord_list_trialcd` (
  `id`          INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `channel`     VARCHAR(32)      NOT NULL DEFAULT '' COMMENT '专属渠道',
  `count`       INT(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '救济乐豆次数',
  `cooldown`    INT(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '冷却秒数',
  `is_del`      TINYINT(3) UNSIGNED       DEFAULT '0' COMMENT '是否已删除',
  `sort`        TINYINT(3) UNSIGNED       DEFAULT '99' COMMENT '排序',
  `create_time` INT(10) UNSIGNED          DEFAULT '0' COMMENT '创建时间',
  `update_time` INT(10) UNSIGNED          DEFAULT '0' COMMENT '更新时间',
  PRIMARY KEY (`id`)
) ENGINE = InnoDB AUTO_INCREMENT = 25 DEFAULT CHARSET = utf8 COMMENT = '斗地主救济冷却配置表';

-- ----------------------------
--  Table structure for `lord_list_trialcoins`
-- ----------------------------
DROP TABLE IF EXISTS `lord_list_trialcoins`;
CREATE TABLE `lord_list_trialcoins` (
  `id`          INT(10) UNSIGNED    NOT NULL AUTO_INCREMENT,
  `channel`     VARCHAR(32)         NOT NULL DEFAULT '' COMMENT '专属渠道',
  `value`       INT(10) UNSIGNED    NOT NULL DEFAULT '0' COMMENT '救济乐豆数量',
  `multiple`    TINYINT(3) UNSIGNED NOT NULL DEFAULT '0' COMMENT '救济乐豆倍率',
  `probability` INT(10) UNSIGNED    NOT NULL DEFAULT '0' COMMENT '权重概率',
  `is_del`      TINYINT(3) UNSIGNED          DEFAULT '0' COMMENT '是否已删除',
  `sort`        TINYINT(3) UNSIGNED          DEFAULT '99' COMMENT '排序',
  `create_time` INT(10) UNSIGNED             DEFAULT '0' COMMENT '创建时间',
  `update_time` INT(10) UNSIGNED             DEFAULT '0' COMMENT '更新时间',
  PRIMARY KEY (`id`)
) ENGINE = InnoDB AUTO_INCREMENT = 13 DEFAULT CHARSET = utf8 COMMENT = '斗地主救济乐豆配置表';

-- ----------------------------
--  Table structure for `lord_list_ttesk`
-- ----------------------------
DROP TABLE IF EXISTS `lord_list_ttesk`;
CREATE TABLE `lord_list_ttesk` (
  `id`          INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `channels`    VARCHAR(255)              DEFAULT '' COMMENT '专属渠道',
  `rooms`       VARCHAR(255)              DEFAULT '' COMMENT '专属房间',
  `users`       VARCHAR(255)              DEFAULT '' COMMENT '专属用户',
  `typeid`      TINYINT(1) UNSIGNED       DEFAULT '0' COMMENT '匹配类型0匹配1误导',
  `conds`       VARCHAR(32)      NOT NULL DEFAULT '' COMMENT '达成条件',
  `prob`        INT(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '触发概率',
  `coins`       INT(10) UNSIGNED          DEFAULT '0' COMMENT '奖励筹码',
  `coupon`      INT(10) UNSIGNED          DEFAULT '0' COMMENT '奖励奖券',
  `sort`        TINYINT(3) UNSIGNED       DEFAULT '99' COMMENT '排序',
  `create_time` INT(10) UNSIGNED          DEFAULT '0' COMMENT '创建时间',
  `update_time` INT(10) UNSIGNED          DEFAULT '0' COMMENT '更新时间',
  PRIMARY KEY (`id`)
) ENGINE = InnoDB AUTO_INCREMENT = 5031 DEFAULT CHARSET = utf8 COMMENT = '斗地主牌局任务表';

-- ----------------------------
--  Table structure for `lord_list_tteskrate`
-- ----------------------------
DROP TABLE IF EXISTS `lord_list_tteskrate`;
CREATE TABLE `lord_list_tteskrate` (
  `id`          INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `times`       INT(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '每日完成次数',
  `prob`        INT(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '可完成的权重值',
  `miss`        INT(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '无法完成权重值',
  `sort`        TINYINT(3) UNSIGNED       DEFAULT '99' COMMENT '排序',
  `create_time` INT(10) UNSIGNED          DEFAULT '0' COMMENT '创建时间',
  `update_time` INT(10) UNSIGNED          DEFAULT '0' COMMENT '更新时间',
  PRIMARY KEY (`id`)
) ENGINE = InnoDB AUTO_INCREMENT = 34 DEFAULT CHARSET = utf8 COMMENT = '牌局任务可行性控制表';

-- ----------------------------
--  Table structure for `lord_list_ttesksource`
-- ----------------------------
DROP TABLE IF EXISTS `lord_list_ttesksource`;
CREATE TABLE `lord_list_ttesksource` (
  `id`          INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `typeid`      INT(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '类型id',
  `type`        VARCHAR(32)      NOT NULL DEFAULT '' COMMENT '类型',
  `name`        VARCHAR(64)      NOT NULL DEFAULT '' COMMENT '任务名称',
  `sort`        TINYINT(3) UNSIGNED       DEFAULT '0' COMMENT '排序',
  `create_time` INT(10) UNSIGNED          DEFAULT '0' COMMENT '创建时间',
  `update_time` INT(10) UNSIGNED          DEFAULT '0' COMMENT '更新时间',
  PRIMARY KEY (`id`),
  KEY `typeid` (`typeid`)
) ENGINE = InnoDB AUTO_INCREMENT = 32 DEFAULT CHARSET = utf8 COMMENT = '斗地主牌局任务源码表';

-- ----------------------------
--  Table structure for `lord_lucky_shake_log`
-- ----------------------------
DROP TABLE IF EXISTS `lord_lucky_shake_log`;
CREATE TABLE `lord_lucky_shake_log` (
  `id`            BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `dateid`        INT(8) UNSIGNED     NOT NULL DEFAULT '0' COMMENT '日期id',
  `uid`           INT(10) UNSIGNED    NOT NULL DEFAULT '0' COMMENT 'UID',
  `roomid`        TINYINT(4) UNSIGNED NOT NULL DEFAULT '0' COMMENT '房间类型',
  `consume_coins` INT(10)             NOT NULL DEFAULT '0' COMMENT '消耗乐豆',
  `type`          TINYINT(4) UNSIGNED NOT NULL DEFAULT '0' COMMENT '获奖类型',
  `win_coins`     INT(10)             NOT NULL DEFAULT '0' COMMENT '获得乐豆',
  `date`          DATETIME            NOT NULL COMMENT '日期',
  `time`          INT(10) UNSIGNED    NOT NULL DEFAULT '0' COMMENT '时间',
  PRIMARY KEY (`id`),
  KEY `search` (`dateid`, `roomid`, `uid`),
  KEY `search2` (`dateid`, `uid`, `roomid`)
) ENGINE = InnoDB AUTO_INCREMENT = 4861 DEFAULT CHARSET = utf8;

-- ----------------------------
--  Table structure for `lord_match_games`
-- ----------------------------
DROP TABLE IF EXISTS `lord_match_games`;
CREATE TABLE `lord_match_games` (
  `id`        INT(10) UNSIGNED    NOT NULL AUTO_INCREMENT,
  `modelId`   TINYINT(2) UNSIGNED NOT NULL DEFAULT '0' COMMENT '赛制ID',
  `roomId`    INT(10) UNSIGNED    NOT NULL DEFAULT '0' COMMENT '房间ID',
  `player`    TEXT                NOT NULL COMMENT '玩家json',
  `outer`     TEXT                NOT NULL COMMENT '淘汰json',
  `robot`     TEXT                NOT NULL COMMENT '假人json',
  `rank`      TEXT                NOT NULL COMMENT '排名json',
  `award`     TEXT                NOT NULL COMMENT '奖励json',
  `entryNum`  INT(10) UNSIGNED    NOT NULL DEFAULT '0' COMMENT '报名用户数',
  `entryRob`  INT(10) UNSIGNED    NOT NULL DEFAULT '0' COMMENT '填补假人数',
  `entryPool` INT(10) UNSIGNED    NOT NULL DEFAULT '0' COMMENT '报名币池',
  `round`     INT(10) UNSIGNED    NOT NULL DEFAULT '0' COMMENT '最后回合',
  `starte`    INT(10) UNSIGNED    NOT NULL DEFAULT '0' COMMENT '开赛时间',
  `finish`    INT(10) UNSIGNED    NOT NULL DEFAULT '0' COMMENT '结束时间',
  `create`    INT(10) UNSIGNED    NOT NULL DEFAULT '0' COMMENT '创建时间',
  `update`    INT(10) UNSIGNED    NOT NULL DEFAULT '0' COMMENT '更新时间',
  PRIMARY KEY (`id`),
  KEY `modelId` (`modelId`, `roomId`)
) ENGINE = InnoDB AUTO_INCREMENT = 171 DEFAULT CHARSET = utf8 COMMENT = '赛制3场次表';

-- ----------------------------
--  Table structure for `lord_model_gameplay`
-- ----------------------------
DROP TABLE IF EXISTS `lord_model_gameplay`;
CREATE TABLE `lord_model_gameplay` (
  `id`          INT(10) UNSIGNED    NOT NULL AUTO_INCREMENT,
  `gameplayId`  VARCHAR(255)        NOT NULL DEFAULT '' COMMENT 'modelId_roomId_weekId_gameId_uid',
  `modelId`     TINYINT(3) UNSIGNED NOT NULL DEFAULT '0' COMMENT '非普通模式id',
  `roomId`      INT(10) UNSIGNED    NOT NULL DEFAULT '0' COMMENT '房间id',
  `weekId`      INT(10) UNSIGNED    NOT NULL DEFAULT '0' COMMENT '周id(年周201422)',
  `gameId`      INT(10) UNSIGNED    NOT NULL DEFAULT '0' COMMENT '场次id',
  `uid`         INT(10) UNSIGNED    NOT NULL DEFAULT '0' COMMENT 'user_user.id',
  `cool_num`    INT(10) UNSIGNED             DEFAULT '0' COMMENT '靓号',
  `joinTime`    INT(10) UNSIGNED             DEFAULT '0' COMMENT '加入时间',
  `deadTime`    INT(10) UNSIGNED             DEFAULT '0' COMMENT '淘汰时间',
  `overTime`    INT(10) UNSIGNED             DEFAULT '0' COMMENT '结束时间',
  `coins`       INT(10) UNSIGNED             DEFAULT '0' COMMENT '参赛后，结算前的筹码',
  `score`       INT(10) UNSIGNED             DEFAULT '0' COMMENT '当前分数',
  `create_time` INT(10) UNSIGNED             DEFAULT '0',
  `update_time` INT(10) UNSIGNED             DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `b` (`gameplayId`),
  KEY `a` (`weekId`, `gameId`),
  KEY `uid` (`uid`)
) ENGINE = InnoDB AUTO_INCREMENT = 373829 DEFAULT CHARSET = utf8 COMMENT = '场赛玩家记录表';

-- ----------------------------
--  Table structure for `lord_model_games`
-- ----------------------------
DROP TABLE IF EXISTS `lord_model_games`;
CREATE TABLE `lord_model_games` (
  `id`              INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `gamesId`         VARCHAR(255)     NOT NULL DEFAULT '' COMMENT 'modelId_roomId_weekId_gameId',
  `modelId`         INT(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '非普通模式id',
  `roomId`          INT(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '房间id',
  `weekId`          INT(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '周id(年周201422)',
  `gameId`          INT(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '场次id',
  `gameLevel`       TINYINT(1) UNSIGNED       DEFAULT '0' COMMENT '本场等级',
  `gamePool`        INT(10) UNSIGNED          DEFAULT '0' COMMENT '本场奖池',
  `gamePerson`      INT(10) UNSIGNED          DEFAULT '0' COMMENT '共有n人报名',
  `gamePlay`        INT(10) UNSIGNED          DEFAULT '0' COMMENT '剩余n人存活',
  `gameScore`       TEXT COMMENT '本场得分:json{uid:score,...}',
  `gamePrizeCoins`  TEXT COMMENT '本场奖励乐豆:json{uid:coins,...}',
  `gamePrizeCoupon` VARCHAR(255)              DEFAULT '[]' COMMENT '奖励奖券',
  `gamePrizePoint`  TEXT COMMENT '本场奖励积分:json{uid:point,...}',
  `gamePrizeProps`  TEXT COMMENT '本场奖励道具:json{uid:propsId,...}',
  `gameStart`       INT(10) UNSIGNED          DEFAULT '0' COMMENT '本场开始时间',
  `gameOver`        INT(10) UNSIGNED          DEFAULT '0' COMMENT '本场结束时间',
  PRIMARY KEY (`id`),
  KEY `a` (`weekId`, `gameId`)
) ENGINE = InnoDB AUTO_INCREMENT = 12461 DEFAULT CHARSET = utf8 COMMENT = '场赛记录表';

-- ----------------------------
--  Table structure for `lord_model_rooms`
-- ----------------------------
DROP TABLE IF EXISTS `lord_model_rooms`;
CREATE TABLE `lord_model_rooms` (
  `id`               INT(10) UNSIGNED    NOT NULL AUTO_INCREMENT,
  `roomsId`          VARCHAR(255)        NOT NULL DEFAULT '' COMMENT 'modelId_roomId_weekId',
  `modelId`          TINYINT(3) UNSIGNED NOT NULL DEFAULT '0' COMMENT '非普通模式id',
  `roomId`           INT(10) UNSIGNED    NOT NULL DEFAULT '0' COMMENT '房间id',
  `roomReal`         INT(10) UNSIGNED    NOT NULL DEFAULT '0' COMMENT '房间真实id',
  `baseCoins`        INT(10) UNSIGNED    NOT NULL DEFAULT '0' COMMENT '底注',
  `rate`             INT(10) UNSIGNED    NOT NULL DEFAULT '0' COMMENT '底倍',
  `limitCoins`       INT(10) UNSIGNED    NOT NULL DEFAULT '0' COMMENT '限高',
  `rake`             INT(10) UNSIGNED    NOT NULL DEFAULT '0' COMMENT '抽水',
  `enterLimit`       INT(10) UNSIGNED    NOT NULL DEFAULT '0' COMMENT '入场下限',
  `enterLimit_`      INT(10) UNSIGNED    NOT NULL DEFAULT '0' COMMENT '入场上限',
  `gameName`         VARCHAR(255)                 DEFAULT '' COMMENT '场赛名称',
  `gameLevel`        TINYINT(3) UNSIGNED          DEFAULT '1' COMMENT '场级',
  `gameScoreIn`      INT(10) UNSIGNED             DEFAULT '0' COMMENT '初始n分给用户',
  `gameScoreOut`     INT(10) UNSIGNED             DEFAULT '0' COMMENT '低于n分被淘汰',
  `gameEndTime`      INT(10) UNSIGNED             DEFAULT '0' COMMENT '本场最多n秒结束',
  `gameWinner`       INT(10) UNSIGNED             DEFAULT '0' COMMENT '本场最多n人胜出',
  `gameRanknum`      INT(10) UNSIGNED             DEFAULT '30' COMMENT '周赛榜单排名人数',
  `gameBombAdd`      INT(10) UNSIGNED             DEFAULT '0' COMMENT '本场最少n个炸弹',
  `gameWaitFirst`    INT(10) UNSIGNED             DEFAULT '0' COMMENT '本场第一局等待n秒后自动开始',
  `gameWaitOther`    INT(10) UNSIGNED             DEFAULT '0' COMMENT '本场其他局等待n秒后自动开始',
  `gameOpen`         VARCHAR(255)                 DEFAULT '' COMMENT '开放时限的文字呈现',
  `gameOpenSetting`  VARCHAR(255)                 DEFAULT '' COMMENT '开放时限判断json:["开始日期时间|结束日期时间|周n"]',
  `gamePersonAll`    INT(10) UNSIGNED             DEFAULT '0' COMMENT '凑够人数后才开始',
  `gameInCoins`      INT(10) UNSIGNED             DEFAULT '0' COMMENT '报名费',
  `gameCancelTime`   INT(10) UNSIGNED             DEFAULT '0' COMMENT '报名后晚于n秒时间时才可取消',
  `gameCancelPerson` INT(10) UNSIGNED             DEFAULT '0' COMMENT '报名后低于n个空位时不可取消',
  `gamePrizeCoins`   VARCHAR(255)                 DEFAULT '' COMMENT '奖励筹码json:{"名次范围":数值,...}',
  `gamePrizePoint`   VARCHAR(255)                 DEFAULT '' COMMENT '奖励积分json:{"名次范围":数值,...}',
  `gamePrizeProps`   VARCHAR(255)                 DEFAULT '' COMMENT '奖励道具json:{"名次范围":{"道具id":"道具名",...},...}',
  `gameRule`         TEXT COMMENT '游戏规则文本',
  `weekPeriod`       TINYINT(3) UNSIGNED          DEFAULT '0' COMMENT '赛制周期天数',
  `weekPrizeCoins`   VARCHAR(255)                 DEFAULT '' COMMENT '周奖筹码json:{"名次":数值,...}',
  `weekPrizeProps`   VARCHAR(255)                 DEFAULT '' COMMENT '周奖筹码json:{"名次范围":{"道具id":"道具名",...},...}',
  `create_time`      DATETIME                     DEFAULT NULL,
  `update_time`      DATETIME                     DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `b` (`roomsId`)
) ENGINE = InnoDB DEFAULT CHARSET = utf8 COMMENT = '赛事房间配置表';

-- ----------------------------
--  Table structure for `lord_model_weekplay`
-- ----------------------------
DROP TABLE IF EXISTS `lord_model_weekplay`;
CREATE TABLE `lord_model_weekplay` (
  `id`             INT(10) UNSIGNED    NOT NULL AUTO_INCREMENT,
  `weekplayId`     VARCHAR(255)        NOT NULL DEFAULT '' COMMENT 'modelId_roomId_weekId_uid',
  `modelId`        TINYINT(3) UNSIGNED NOT NULL DEFAULT '0' COMMENT '非普通模式id',
  `roomId`         INT(10) UNSIGNED    NOT NULL DEFAULT '0' COMMENT '房间id',
  `weekId`         INT(10) UNSIGNED    NOT NULL DEFAULT '0' COMMENT '周id(年周201422)',
  `uid`            INT(10) UNSIGNED    NOT NULL DEFAULT '0' COMMENT 'user_user.id',
  `cool_num`       INT(10) UNSIGNED    NOT NULL DEFAULT '0' COMMENT 'game_num.num',
  `weekPoint`      INT(10) UNSIGNED             DEFAULT '0' COMMENT '本周积分',
  `weekRank`       TINYINT(3) UNSIGNED          DEFAULT '0' COMMENT '本周排名',
  `weekPrizeExp`   INT(10) UNSIGNED             DEFAULT '0' COMMENT '奖励经验',
  `weekPrizeCoins` INT(10) UNSIGNED             DEFAULT '0' COMMENT '奖励筹码',
  `weekPrizeProps` VARCHAR(255)                 DEFAULT '' COMMENT '奖励道具json:{"id":"name"}',
  `create_time`    INT(10) UNSIGNED             DEFAULT '0',
  `update_time`    INT(10) UNSIGNED             DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `b` (`weekplayId`)
) ENGINE = InnoDB AUTO_INCREMENT = 85174 DEFAULT CHARSET = utf8 COMMENT = '周赛玩家记录表';

-- ----------------------------
--  Table structure for `lord_model_weeks`
-- ----------------------------
DROP TABLE IF EXISTS `lord_model_weeks`;
CREATE TABLE `lord_model_weeks` (
  `id`             INT(10) UNSIGNED    NOT NULL AUTO_INCREMENT,
  `weeksId`        VARCHAR(255)        NOT NULL DEFAULT '' COMMENT 'modelId_roomId_weekId',
  `modelId`        TINYINT(3) UNSIGNED NOT NULL DEFAULT '0' COMMENT '非普通模式id',
  `roomId`         INT(10) UNSIGNED    NOT NULL DEFAULT '0' COMMENT '房间id',
  `weekId`         INT(10) UNSIGNED    NOT NULL DEFAULT '0' COMMENT '周期id(20140801)',
  `weekPool`       BIGINT(20) UNSIGNED          DEFAULT '0' COMMENT '周期奖池',
  `weekRank`       TEXT COMMENT '周期积分排名json:[{rank:1,uid:1,nick:"",point:222},...]',
  `weekPrizeCoins` TEXT COMMENT '周期奖励乐豆:json{uid:coins,...}',
  `weekPrizeProps` TEXT COMMENT '周期奖励道具:json{uid:propsId,...}',
  `weekStart`      INT(10) UNSIGNED             DEFAULT '0' COMMENT '周期开始time()',
  `weekEnd`        INT(10) UNSIGNED             DEFAULT '0' COMMENT '周期结束time()',
  PRIMARY KEY (`id`),
  UNIQUE KEY `b` (`weeksId`)
) ENGINE = InnoDB AUTO_INCREMENT = 54 DEFAULT CHARSET = utf8 COMMENT = '周赛记录表';

-- ----------------------------
--  Table structure for `lord_online`
-- ----------------------------
DROP TABLE IF EXISTS `lord_online`;
CREATE TABLE `lord_online` (
  `id`       INT(10)  NOT NULL AUTO_INCREMENT,
  `add_time` DATETIME NOT NULL,
  `num`      INT(11)  NOT NULL,
  `playing`  INT(10) UNSIGNED  DEFAULT '0' COMMENT '在桌人数',
  PRIMARY KEY (`id`)
) ENGINE = InnoDB AUTO_INCREMENT = 127338 DEFAULT CHARSET = utf8;

-- ----------------------------
--  Table structure for `lord_online_detail`
-- ----------------------------
DROP TABLE IF EXISTS `lord_online_detail`;
CREATE TABLE `lord_online_detail` (
  `id`                   INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `dateid`               INT(8) UNSIGNED  NOT NULL DEFAULT '0' COMMENT '日期id',
  `dt`                   INT(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '统计时间日期:年月日时分1502091359',
  `ut`                   INT(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '统计时间公秒:unixtime',
  `allRoomNum`           INT(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '房间个数总数',
  `allTableNum`          INT(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '牌桌个数总数',
  `allOnline`            INT(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '在线人数总数',
  `allInLobby`           INT(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '只在大厅总数',
  `allInRoom`            INT(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '只在房间总数',
  `allInTableActive`     INT(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '在桌活跃总数',
  `allInTableOffline`    INT(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '在桌掉线总数',
  `allInTableRobot`      INT(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '在桌假人总数',
  `room1000TableNum`     INT(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '新手场牌桌个数',
  `room1000TableActive`  INT(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '新手场在桌活跃',
  `room1000TableOffline` INT(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '新手场在桌掉线',
  `room1000TableRobot`   INT(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '新手场在桌假人',
  `room1001TableNum`     INT(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '初级场牌桌个数',
  `room1001TableActive`  INT(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '初级场在桌活跃',
  `room1001TableOffline` INT(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '初级场在桌掉线',
  `room1001TableRobot`   INT(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '初级场在桌假人',
  `room1002TableNum`     INT(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '中级场牌桌个数',
  `room1002TableActive`  INT(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '中级场在桌活跃',
  `room1002TableOffline` INT(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '中级场在桌掉线',
  `room1002TableRobot`   INT(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '中级场在桌假人',
  `room1003TableNum`     INT(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '高级场牌桌个数',
  `room1003TableActive`  INT(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '高级场在桌活跃',
  `room1003TableOffline` INT(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '高级场在桌掉线',
  `room1003TableRobot`   INT(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '高级场在桌假人',
  `room1004TableNum`     INT(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '竞技场牌桌个数',
  `room1004TableActive`  INT(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '竞技场在桌活跃',
  `room1004TableOffline` INT(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '竞技场在桌掉线',
  `room1004TableRobot`   INT(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '竞技场在桌假人',
  `room1006TableNum`     INT(10) UNSIGNED          DEFAULT '0' COMMENT '进阶场牌桌数',
  `room1006TableActive`  INT(10) UNSIGNED          DEFAULT '0' COMMENT '进阶场活跃数',
  `room1006TableOffline` INT(10) UNSIGNED          DEFAULT '0' COMMENT '进阶场掉线数',
  `room1006TableRobot`   INT(10) UNSIGNED          DEFAULT '0' COMMENT '进阶场机器人',
  `room1007TableNum`     INT(10) UNSIGNED          DEFAULT '0',
  `room1007TableActive`  INT(10) UNSIGNED          DEFAULT '0',
  `room1007TableOffline` INT(10) UNSIGNED          DEFAULT '0',
  `room1007TableRobot`   INT(10) UNSIGNED          DEFAULT '0',
  `room1008TableNum`     INT(10) UNSIGNED          DEFAULT '0',
  `room1008TableActive`  INT(10) UNSIGNED          DEFAULT '0',
  `room1008TableOffline` INT(10) UNSIGNED          DEFAULT '0',
  `room1008TableRobot`   INT(10) UNSIGNED          DEFAULT '0',
  `room1009TableNum`     INT(10) UNSIGNED          DEFAULT '0',
  `room1009TableActive`  INT(10) UNSIGNED          DEFAULT '0',
  `room1009TableOffline` INT(10) UNSIGNED          DEFAULT '0',
  `room1009TableRobot`   INT(10) UNSIGNED          DEFAULT '0',
  `room1010TableNum`     INT(10) UNSIGNED          DEFAULT '0',
  `room1010TableActive`  INT(10) UNSIGNED          DEFAULT '0',
  `room1010TableOffline` INT(10) UNSIGNED          DEFAULT '0',
  `room1010TableRobot`   INT(10) UNSIGNED          DEFAULT '0',
  `room1011TableNum`     INT(10) UNSIGNED          DEFAULT '0',
  `room1011TableActive`  INT(10) UNSIGNED          DEFAULT '0',
  `room1011TableOffline` INT(10) UNSIGNED          DEFAULT '0',
  `room1011TableRobot`   INT(10) UNSIGNED          DEFAULT '0',
  `room3011TableNum`     INT(10) UNSIGNED          DEFAULT '0',
  `room3011TableActive`  INT(10) UNSIGNED          DEFAULT '0',
  `room3011TableOffline` INT(10) UNSIGNED          DEFAULT '0',
  `room3011TableRobot`   INT(10) UNSIGNED          DEFAULT '0',
  `room3012TableNum`     INT(10) UNSIGNED          DEFAULT '0',
  `room3012TableActive`  INT(10) UNSIGNED          DEFAULT '0',
  `room3012TableOffline` INT(10) UNSIGNED          DEFAULT '0',
  `room3012TableRobot`   INT(10) UNSIGNED          DEFAULT '0',
  `room3013TableNum`     INT(10) UNSIGNED          DEFAULT '0',
  `room3013TableActive`  INT(10) UNSIGNED          DEFAULT '0',
  `room3013TableOffline` INT(10) UNSIGNED          DEFAULT '0',
  `room3013TableRobot`   INT(10) UNSIGNED          DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `search` (`dateid`)
) ENGINE = InnoDB AUTO_INCREMENT = 79477 DEFAULT CHARSET = utf8 COMMENT = '斗地主在线状况明细表';

-- ----------------------------
--  Table structure for `lord_online_room`
-- ----------------------------
DROP TABLE IF EXISTS `lord_online_room`;
CREATE TABLE `lord_online_room` (
  `id`           INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `dateid`       INT(8) UNSIGNED  NOT NULL DEFAULT '0' COMMENT '日期id:年月日20160811',
  `dt`           INT(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '分钟:年月日时分1608110959',
  `ut`           INT(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '公秒:unixtime',
  `roomId`       INT(6) UNSIGNED  NOT NULL DEFAULT '0' COMMENT '房间ID',
  `tableNum`     INT(6) UNSIGNED  NOT NULL DEFAULT '0' COMMENT '牌桌个数',
  `tableActive`  INT(6) UNSIGNED  NOT NULL DEFAULT '0' COMMENT '在桌活跃',
  `tableOffline` INT(6) UNSIGNED  NOT NULL DEFAULT '0' COMMENT '在桌掉线',
  `tableRobot`   INT(6) UNSIGNED  NOT NULL DEFAULT '0' COMMENT '在桌假人',
  PRIMARY KEY (`id`),
  KEY `s0` (`dateid`, `roomId`, `dt`),
  KEY `s1` (`dateid`, `dt`, `roomId`),
  KEY `s2` (`roomId`),
  KEY `s3` (`dt`)
) ENGINE = InnoDB DEFAULT CHARSET = utf8 COMMENT = '斗地主在线状况房间表';

-- ----------------------------
--  Table structure for `lord_record_action_20160707`
-- ----------------------------
DROP TABLE IF EXISTS `lord_record_action_20160707`;
CREATE TABLE `lord_record_action_20160707` (
  `id`        BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `dateid`    INT(8) UNSIGNED     NOT NULL DEFAULT '0' COMMENT '日期ID',
  `hourid`    TINYINT(2) UNSIGNED NOT NULL DEFAULT '0' COMMENT '小时ID',
  `protocal`  VARCHAR(16)         NOT NULL DEFAULT '' COMMENT '协议/函数',
  `roomId`    INT(5) UNSIGNED     NOT NULL DEFAULT '0' COMMENT '房间ID',
  `tableId`   VARCHAR(32)         NOT NULL DEFAULT '' COMMENT '牌桌ID',
  `uid`       INT(10) UNSIGNED    NOT NULL DEFAULT '0' COMMENT '用户ID',
  `channelid` INT(4) UNSIGNED     NOT NULL DEFAULT '0' COMMENT '渠道ID',
  `vercode`   INT(6) UNSIGNED     NOT NULL DEFAULT '0' COMMENT '版本号',
  `coins`     INT(10)             NOT NULL DEFAULT '0' COMMENT '用户乐豆',
  `coupon`    INT(10)             NOT NULL DEFAULT '0' COMMENT '用户乐券',
  `result`    VARCHAR(255)                 DEFAULT '' COMMENT '关键结果json或空',
  `errors`    VARCHAR(255)                 DEFAULT '' COMMENT '错误信息json或空',
  `exta`      VARCHAR(32)                  DEFAULT '' COMMENT '预留A',
  `extb`      VARCHAR(32)                  DEFAULT '' COMMENT '预留B',
  `extc`      VARCHAR(32)                  DEFAULT '' COMMENT '预留C',
  `extd`      VARCHAR(32)                  DEFAULT '' COMMENT '预留D',
  `exte`      VARCHAR(32)                  DEFAULT '' COMMENT '预留E',
  `tmcr`      INT(10) UNSIGNED    NOT NULL DEFAULT '0' COMMENT '创建时间',
  PRIMARY KEY (`id`),
  KEY `s0` (`dateid`, `hourid`, `protocal`, `roomId`, `tmcr`),
  KEY `s1` (`channelid`, `vercode`, `uid`)
) ENGINE = InnoDB AUTO_INCREMENT = 636 DEFAULT CHARSET = utf8 COMMENT = '操作记录日换表名_20160225';

-- ----------------------------
--  Table structure for `lord_record_convert`
-- ----------------------------
DROP TABLE IF EXISTS `lord_record_convert`;
CREATE TABLE `lord_record_convert` (
  `id`          INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `dateid`      INT(8) UNSIGNED  NOT NULL DEFAULT '0' COMMENT '日期id',
  `uid`         INT(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'UID',
  `cool_num`    INT(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '靓号',
  `nick`        VARCHAR(32)      NOT NULL DEFAULT '' COMMENT '昵称',
  `channel`     VARCHAR(32)      NOT NULL DEFAULT '' COMMENT '渠道',
  `iid`         INT(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '兑换项id',
  `type`        VARCHAR(32)      NOT NULL DEFAULT '' COMMENT '兑换类型',
  `title`       VARCHAR(32)      NOT NULL DEFAULT '' COMMENT '兑换物品',
  `num`         INT(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '兑换数量',
  `cost`        INT(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '用户耗费货币',
  `after`       INT(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '用户兑后货币',
  `other`       VARCHAR(255)              DEFAULT '' COMMENT '用户兑换备注，兑换话费时，即为手机号码',
  `oid`         VARCHAR(32)               DEFAULT '' COMMENT '操作员帐号',
  `state`       TINYINT(3) UNSIGNED       DEFAULT '0' COMMENT '操作状态 0未处理1已发货',
  `comments`    VARCHAR(255)              DEFAULT '' COMMENT '操作备注',
  `create_time` INT(10) UNSIGNED          DEFAULT '0' COMMENT '创建时间',
  `update_time` INT(10) UNSIGNED          DEFAULT '0' COMMENT '更新时间',
  PRIMARY KEY (`id`),
  KEY `dateid` (`dateid`, `channel`, `type`, `iid`, `uid`, `cool_num`)
) ENGINE = InnoDB AUTO_INCREMENT = 15 DEFAULT CHARSET = utf8 COMMENT = '用户兑换记录及发货管理表';

-- ----------------------------
--  Table structure for `lord_record_hotuser`
-- ----------------------------
DROP TABLE IF EXISTS `lord_record_hotuser`;
CREATE TABLE `lord_record_hotuser` (
  `id`        INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `dd`        INT(8) UNSIGNED  NOT NULL DEFAULT '0' COMMENT '日期ID',
  `uid`       INT(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'UID',
  `reg`       INT(8) UNSIGNED  NOT NULL DEFAULT '0' COMMENT '注册日期ID',
  `channel`   VARCHAR(32)      NOT NULL DEFAULT '' COMMENT '当日渠道',
  `vercode`   INT(5) UNSIGNED  NOT NULL DEFAULT '0' COMMENT '当日版本',
  `ip`        VARCHAR(15)      NOT NULL DEFAULT '' COMMENT '当日IP',
  `coins`     INT(11)          NOT NULL DEFAULT '0' COMMENT '当前乐豆',
  `coupon`    INT(11)          NOT NULL DEFAULT '0' COMMENT '当前乐券',
  `play`      INT(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '当前局数',
  `win`       INT(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '当前胜局',
  `ddcoins`   INT(11)          NOT NULL DEFAULT '0' COMMENT '当日赚豆',
  `ddcoupon`  INT(11)          NOT NULL DEFAULT '0' COMMENT '当日赚券',
  `ddplay`    INT(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '当日局数',
  `ddwin`     INT(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '当日胜局',
  `ddlogin`   INT(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '当日登陆次数',
  `ddseconds` INT(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '当日在线时长',
  `tmcr`      INT(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '当前时间',
  PRIMARY KEY (`id`),
  KEY `uid` (`uid`),
  KEY `regdateid` (`reg`),
  KEY `coins` (`dd`, `coins`),
  KEY `coupon` (`dd`, `coupon`),
  KEY `play` (`dd`, `play`),
  KEY `win` (`dd`, `win`),
  KEY `ddcoins` (`dd`, `ddcoins`),
  KEY `ddcoupon` (`dd`, `ddcoupon`),
  KEY `ddplay` (`dd`, `ddplay`),
  KEY `ddwin` (`dd`, `ddwin`),
  KEY `ddlogin` (`dd`, `ddlogin`),
  KEY `ddseconds` (`dd`, `ddseconds`),
  KEY `channel` (`channel`),
  KEY `vercode` (`vercode`)
) ENGINE = InnoDB DEFAULT CHARSET = utf8 COMMENT = 'DAU乐豆或乐券排前1000的用户关键记录';

-- ----------------------------
--  Table structure for `lord_record_logout_201608`
-- ----------------------------
DROP TABLE IF EXISTS `lord_record_logout_201608`;
CREATE TABLE `lord_record_logout_201608` (
  `id`        INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `dd`        INT(8) UNSIGNED  NOT NULL DEFAULT '0' COMMENT '登出日期ID',
  `uid`       INT(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'UID',
  `reg`       INT(8) UNSIGNED  NOT NULL DEFAULT '0' COMMENT '注册日期ID',
  `channel`   VARCHAR(32)      NOT NULL DEFAULT '' COMMENT '登出渠道',
  `vercode`   INT(5) UNSIGNED  NOT NULL DEFAULT '0' COMMENT '登出版本',
  `ip`        VARCHAR(15)      NOT NULL DEFAULT '' COMMENT '登出IP',
  `coins`     INT(11)          NOT NULL DEFAULT '0' COMMENT '登出乐豆',
  `coupon`    INT(11)          NOT NULL DEFAULT '0' COMMENT '登出乐券',
  `play`      INT(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '登出局数',
  `win`       INT(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '登出胜局',
  `ddcoins`   INT(11)          NOT NULL DEFAULT '0' COMMENT '当日赚豆',
  `ddcoupon`  INT(11)          NOT NULL DEFAULT '0' COMMENT '当日赚券',
  `ddplay`    INT(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '当日局数',
  `ddwin`     INT(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '当日胜局',
  `ddlogin`   INT(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '当日登陆次数',
  `ddseconds` INT(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '当日在线时长',
  `tmcr`      INT(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '当前时间',
  PRIMARY KEY (`id`),
  KEY `uid` (`uid`),
  KEY `reg` (`reg`, `uid`),
  KEY `coins` (`dd`, `coins`),
  KEY `coupon` (`dd`, `coupon`),
  KEY `play` (`dd`, `play`),
  KEY `win` (`dd`, `win`),
  KEY `ddcoins` (`dd`, `ddcoins`),
  KEY `ddcoupon` (`dd`, `ddcoupon`),
  KEY `ddplay` (`dd`, `ddplay`),
  KEY `ddwin` (`dd`, `ddwin`),
  KEY `ddlogin` (`dd`, `ddlogin`),
  KEY `ddseconds` (`dd`, `ddseconds`),
  KEY `channel` (`dd`, `channel`),
  KEY `vercode` (`dd`, `vercode`)
) ENGINE = InnoDB AUTO_INCREMENT = 173973 DEFAULT CHARSET = utf8 COMMENT = '用户登出记录';

-- ----------------------------
--  Table structure for `lord_record_lottery`
-- ----------------------------
DROP TABLE IF EXISTS `lord_record_lottery`;
CREATE TABLE `lord_record_lottery` (
  `id`        INT(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '编号',
  `dateid`    INT(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '用户抽奖日期 如20141128',
  `uid`       INT(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '用户id',
  `cool_num`  INT(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '用户靓号',
  `nick`      VARCHAR(64)      NOT NULL DEFAULT '' COMMENT '用户昵称',
  `prizeid`   INT(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '奖品id 0为未中奖',
  `cateid`    INT(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '奖品分类',
  `gold`      INT(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '中奖金币',
  `coupon`    INT(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '中奖奖券',
  `coins`     INT(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '中奖筹码',
  `propid`    INT(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '中奖道具编号',
  `ut_create` INT(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '创建时间',
  PRIMARY KEY (`id`),
  KEY `uid` (`uid`),
  KEY `dateid` (`dateid`)
) ENGINE = InnoDB AUTO_INCREMENT = 3393 DEFAULT CHARSET = utf8;

-- ----------------------------
--  Table structure for `lord_record_money_20160701`
-- ----------------------------
DROP TABLE IF EXISTS `lord_record_money_20160701`;
CREATE TABLE `lord_record_money_20160701` (
  `id`        BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `dateid`    INT(8) UNSIGNED     NOT NULL DEFAULT '0' COMMENT '日期ID',
  `hourid`    TINYINT(2) UNSIGNED NOT NULL DEFAULT '0' COMMENT '小时ID',
  `typeid`    TINYINT(2) UNSIGNED NOT NULL DEFAULT '0' COMMENT '类型ID',
  `moneyid`   TINYINT(1) UNSIGNED NOT NULL DEFAULT '0' COMMENT '货币ID',
  `moneynum`  INT(10)             NOT NULL DEFAULT '0' COMMENT '货币数∓',
  `moneynow`  INT(10)             NOT NULL DEFAULT '0' COMMENT '当前货币',
  `uid`       INT(10) UNSIGNED    NOT NULL DEFAULT '0' COMMENT '用户ID',
  `channelid` INT(4) UNSIGNED     NOT NULL DEFAULT '0' COMMENT '渠道ID',
  `roomId`    INT(5) UNSIGNED     NOT NULL DEFAULT '0' COMMENT '房间ID',
  `tableId`   VARCHAR(32)                  DEFAULT '' COMMENT '牌桌ID',
  `exta`      VARCHAR(32)                  DEFAULT '' COMMENT '预留A',
  `extb`      VARCHAR(32)                  DEFAULT '' COMMENT '预留B',
  `extc`      VARCHAR(32)                  DEFAULT '' COMMENT '预留C',
  `extd`      VARCHAR(32)                  DEFAULT '' COMMENT '预留D',
  `exte`      VARCHAR(32)                  DEFAULT '' COMMENT '预留E',
  `tmcr`      INT(10) UNSIGNED    NOT NULL DEFAULT '0' COMMENT '创建时间',
  PRIMARY KEY (`id`),
  KEY `s0` (`dateid`, `hourid`, `typeid`, `moneyid`, `roomId`),
  KEY `s1` (`channelid`, `uid`),
  KEY `s2` (`dateid`, `moneyid`, `typeid`)
) ENGINE = InnoDB AUTO_INCREMENT = 83 DEFAULT CHARSET = utf8 COMMENT = '货币记录日换表名_20160225';

-- ----------------------------
--  Table structure for `lord_record_money_day`
-- ----------------------------
DROP TABLE IF EXISTS `lord_record_money_day`;
CREATE TABLE `lord_record_money_day` (
  `id`         INT(10) UNSIGNED    NOT NULL AUTO_INCREMENT,
  `dateid`     INT(8) UNSIGNED     NOT NULL DEFAULT '0' COMMENT '日期ID',
  `moneyid`    TINYINT(1) UNSIGNED NOT NULL DEFAULT '0' COMMENT '货币ID',
  `transfers`  BIGINT(20)          NOT NULL DEFAULT '0' COMMENT '转移量∓',
  `transtimes` INT(10) UNSIGNED    NOT NULL DEFAULT '0' COMMENT '转移次数',
  `outgoings`  BIGINT(20)          NOT NULL DEFAULT '0' COMMENT '投放量∓',
  `outgotimes` INT(10) UNSIGNED    NOT NULL DEFAULT '0' COMMENT '投放次数',
  `incomings`  BIGINT(20)          NOT NULL DEFAULT '0' COMMENT '回收量∓',
  `incomtimes` INT(10) UNSIGNED    NOT NULL DEFAULT '0' COMMENT '回收次数',
  `earnings`   BIGINT(20)          NOT NULL DEFAULT '0' COMMENT '盈亏量∓',
  `holdings`   BIGINT(20) UNSIGNED NOT NULL DEFAULT '0' COMMENT '持有量',
  `hold0`      INT(10) UNSIGNED    NOT NULL DEFAULT '0' COMMENT '0豆/0券/0币人数',
  `hold1`      INT(10) UNSIGNED    NOT NULL DEFAULT '0' COMMENT '>=500W/5W/500',
  `hold2`      INT(10) UNSIGNED    NOT NULL DEFAULT '0' COMMENT '>=200W/2W/200',
  `hold3`      INT(10) UNSIGNED    NOT NULL DEFAULT '0' COMMENT '>=100W/1W/100',
  `hold4`      INT(10) UNSIGNED    NOT NULL DEFAULT '0' COMMENT '>=50W/5K/50',
  `hold5`      INT(10) UNSIGNED    NOT NULL DEFAULT '0' COMMENT '>=20W/2K/20',
  `hold6`      INT(10) UNSIGNED    NOT NULL DEFAULT '0' COMMENT '>=10W/1K/10',
  `hold7`      INT(10) UNSIGNED    NOT NULL DEFAULT '0' COMMENT '>=5W/500/5',
  `hold8`      INT(10) UNSIGNED    NOT NULL DEFAULT '0' COMMENT '>=2W/200/2',
  `hold9`      INT(10) UNSIGNED    NOT NULL DEFAULT '0' COMMENT '>=1W/100/1',
  `hold10`     INT(10) UNSIGNED    NOT NULL DEFAULT '0' COMMENT '>0/0/0',
  `tmcr`       INT(10) UNSIGNED    NOT NULL DEFAULT '0' COMMENT '创建时间',
  PRIMARY KEY (`id`),
  UNIQUE KEY `s0` (`dateid`, `moneyid`)
) ENGINE = InnoDB AUTO_INCREMENT = 46 DEFAULT CHARSET = utf8 COMMENT = '每天货币纪录分析';

-- ----------------------------
--  Table structure for `lord_record_money_hour`
-- ----------------------------
DROP TABLE IF EXISTS `lord_record_money_hour`;
CREATE TABLE `lord_record_money_hour` (
  `id`         INT(10) UNSIGNED    NOT NULL AUTO_INCREMENT,
  `dateid`     INT(8) UNSIGNED     NOT NULL DEFAULT '0' COMMENT '日期ID',
  `hourid`     TINYINT(2) UNSIGNED NOT NULL DEFAULT '0' COMMENT '小时ID',
  `moneyid`    TINYINT(1) UNSIGNED NOT NULL DEFAULT '0' COMMENT '货币ID',
  `transfers`  BIGINT(20)          NOT NULL DEFAULT '0' COMMENT '转移量∓',
  `transtimes` INT(10) UNSIGNED    NOT NULL DEFAULT '0' COMMENT '转移次数',
  `outgoings`  BIGINT(20)          NOT NULL DEFAULT '0' COMMENT '投放量∓',
  `outgotimes` INT(10) UNSIGNED    NOT NULL DEFAULT '0' COMMENT '投放次数',
  `incomings`  BIGINT(20)          NOT NULL DEFAULT '0' COMMENT '回收量∓',
  `incomtimes` INT(10) UNSIGNED    NOT NULL DEFAULT '0' COMMENT '回收次数',
  `earnings`   BIGINT(20)          NOT NULL DEFAULT '0' COMMENT '盈亏量∓',
  `tmcr`       INT(10) UNSIGNED    NOT NULL DEFAULT '0' COMMENT '创建时间',
  PRIMARY KEY (`id`),
  UNIQUE KEY `s0` (`dateid`, `hourid`, `moneyid`)
) ENGINE = InnoDB AUTO_INCREMENT = 1057 DEFAULT CHARSET = utf8 COMMENT = '每时货币纪录分析';

-- ----------------------------
--  Table structure for `lord_record_money_type`
-- ----------------------------
DROP TABLE IF EXISTS `lord_record_money_type`;
CREATE TABLE `lord_record_money_type` (
  `id`      INT(10) UNSIGNED    NOT NULL AUTO_INCREMENT,
  `dateid`  INT(8) UNSIGNED     NOT NULL DEFAULT '0' COMMENT '日期ID',
  `moneyid` TINYINT(1) UNSIGNED NOT NULL DEFAULT '0' COMMENT '货币ID',
  `t1s`     INT(10) UNSIGNED    NOT NULL DEFAULT '0' COMMENT '后台添加合计',
  `t1c`     INT(10) UNSIGNED    NOT NULL DEFAULT '0' COMMENT '后台添加次数',
  `t2s`     INT(10) UNSIGNED    NOT NULL DEFAULT '0' COMMENT '充值乐币合计',
  `t2c`     INT(10) UNSIGNED    NOT NULL DEFAULT '0' COMMENT '充值乐币次数',
  `t3s`     INT(10) UNSIGNED    NOT NULL DEFAULT '0' COMMENT '币豆加豆合计',
  `t3c`     INT(10) UNSIGNED    NOT NULL DEFAULT '0' COMMENT '币豆加豆次数',
  `t4s`     INT(10) UNSIGNED    NOT NULL DEFAULT '0' COMMENT 'SDK买豆合计',
  `t4c`     INT(10) UNSIGNED    NOT NULL DEFAULT '0' COMMENT 'SDK买豆次数',
  `t5s`     INT(10) UNSIGNED    NOT NULL DEFAULT '0' COMMENT '券豆加豆合计',
  `t5c`     INT(10) UNSIGNED    NOT NULL DEFAULT '0' COMMENT '券豆加豆次数',
  `t6s`     INT(10) UNSIGNED    NOT NULL DEFAULT '0' COMMENT '新手乐豆合计',
  `t6c`     INT(10) UNSIGNED    NOT NULL DEFAULT '0' COMMENT '新手乐豆次数',
  `t7s`     INT(10) UNSIGNED    NOT NULL DEFAULT '0' COMMENT '每日签到合计',
  `t7c`     INT(10) UNSIGNED    NOT NULL DEFAULT '0' COMMENT '每日签到次数',
  `t8s`     INT(10) UNSIGNED    NOT NULL DEFAULT '0' COMMENT '领取救济合计',
  `t8c`     INT(10) UNSIGNED    NOT NULL DEFAULT '0' COMMENT '领取救济次数',
  `t9s`     INT(10) UNSIGNED    NOT NULL DEFAULT '0' COMMENT '免费抽奖合计',
  `t9c`     INT(10) UNSIGNED    NOT NULL DEFAULT '0' COMMENT '免费抽奖次数',
  `t10s`    INT(10) UNSIGNED    NOT NULL DEFAULT '0' COMMENT '微信签到合计',
  `t10c`    INT(10) UNSIGNED    NOT NULL DEFAULT '0' COMMENT '微信签到次数',
  `t11s`    INT(10) UNSIGNED    NOT NULL DEFAULT '0' COMMENT '激活礼包合计',
  `t11c`    INT(10) UNSIGNED    NOT NULL DEFAULT '0' COMMENT '激活礼包次数',
  `t12s`    INT(10) UNSIGNED    NOT NULL DEFAULT '0' COMMENT '参与活动合计',
  `t12c`    INT(10) UNSIGNED    NOT NULL DEFAULT '0' COMMENT '参与活动次数',
  `t13s`    INT(10) UNSIGNED    NOT NULL DEFAULT '0' COMMENT '牌局任务合计',
  `t13c`    INT(10) UNSIGNED    NOT NULL DEFAULT '0' COMMENT '牌局任务次数',
  `t14s`    INT(10) UNSIGNED    NOT NULL DEFAULT '0' COMMENT '固定任务合计',
  `t14c`    INT(10) UNSIGNED    NOT NULL DEFAULT '0' COMMENT '固定任务次数',
  `t15s`    INT(10) UNSIGNED    NOT NULL DEFAULT '0' COMMENT '动态任务合计',
  `t15c`    INT(10) UNSIGNED    NOT NULL DEFAULT '0' COMMENT '动态任务次数',
  `t16s`    INT(10) UNSIGNED    NOT NULL DEFAULT '0' COMMENT '使用道具合计',
  `t16c`    INT(10) UNSIGNED    NOT NULL DEFAULT '0' COMMENT '使用道具次数',
  `t17s`    INT(10) UNSIGNED    NOT NULL DEFAULT '0' COMMENT '领取邮件合计',
  `t17c`    INT(10) UNSIGNED    NOT NULL DEFAULT '0' COMMENT '领取邮件次数',
  `t18s`    INT(10) UNSIGNED    NOT NULL DEFAULT '0' COMMENT '领取俸禄合计',
  `t18c`    INT(10) UNSIGNED    NOT NULL DEFAULT '0' COMMENT '领取俸禄次数',
  `t19s`    INT(10) UNSIGNED    NOT NULL DEFAULT '0' COMMENT '竞技取消合计',
  `t19c`    INT(10) UNSIGNED    NOT NULL DEFAULT '0' COMMENT '竞技取消次数',
  `t20s`    INT(10) UNSIGNED    NOT NULL DEFAULT '0' COMMENT '竞技场奖合计',
  `t20c`    INT(10) UNSIGNED    NOT NULL DEFAULT '0' COMMENT '竞技场奖次数',
  `t21s`    INT(10) UNSIGNED    NOT NULL DEFAULT '0' COMMENT '竞技周奖合计',
  `t21c`    INT(10) UNSIGNED    NOT NULL DEFAULT '0' COMMENT '竞技周奖次数',
  `t22s`    INT(10) UNSIGNED    NOT NULL DEFAULT '0' COMMENT '赌一中奖合计',
  `t22c`    INT(10) UNSIGNED    NOT NULL DEFAULT '0' COMMENT '赌一中奖次数',
  `t23s`    INT(10) UNSIGNED    NOT NULL DEFAULT '0' COMMENT '赌二中奖合计',
  `t23c`    INT(10) UNSIGNED    NOT NULL DEFAULT '0' COMMENT '赌二中奖次数',
  `t24s`    INT(10) UNSIGNED    NOT NULL DEFAULT '0' COMMENT '免责金牌合计',
  `t24c`    INT(10) UNSIGNED    NOT NULL DEFAULT '0' COMMENT '免责金牌次数',
  `t25s`    INT(10) UNSIGNED    NOT NULL DEFAULT '0' COMMENT '幸运牌局合计',
  `t25c`    INT(10) UNSIGNED    NOT NULL DEFAULT '0' COMMENT '幸运牌局次数',
  `t51s`    INT(10) UNSIGNED    NOT NULL DEFAULT '0' COMMENT '后台扣除合计',
  `t51c`    INT(10) UNSIGNED    NOT NULL DEFAULT '0' COMMENT '后台扣除次数',
  `t52s`    INT(10) UNSIGNED    NOT NULL DEFAULT '0' COMMENT '牌局抽水合计',
  `t52c`    INT(10) UNSIGNED    NOT NULL DEFAULT '0' COMMENT '牌局抽水次数',
  `t53s`    INT(10) UNSIGNED    NOT NULL DEFAULT '0' COMMENT '币豆减币合计',
  `t53c`    INT(10) UNSIGNED    NOT NULL DEFAULT '0' COMMENT '币豆减币次数',
  `t54s`    INT(10) UNSIGNED    NOT NULL DEFAULT '0' COMMENT '币买道具合计',
  `t54c`    INT(10) UNSIGNED    NOT NULL DEFAULT '0' COMMENT '币买道具次数',
  `t55s`    INT(10) UNSIGNED    NOT NULL DEFAULT '0' COMMENT '券豆减券合计',
  `t55c`    INT(10) UNSIGNED    NOT NULL DEFAULT '0' COMMENT '券豆减券次数',
  `t56s`    INT(10) UNSIGNED    NOT NULL DEFAULT '0' COMMENT '券买道具合计',
  `t56c`    INT(10) UNSIGNED    NOT NULL DEFAULT '0' COMMENT '券买道具次数',
  `t57s`    INT(10) UNSIGNED    NOT NULL DEFAULT '0' COMMENT '券换实物合计',
  `t57c`    INT(10) UNSIGNED    NOT NULL DEFAULT '0' COMMENT '券换实物次数',
  `t58s`    INT(10) UNSIGNED    NOT NULL DEFAULT '0' COMMENT '豆买道具合计',
  `t58c`    INT(10) UNSIGNED    NOT NULL DEFAULT '0' COMMENT '豆买道具次数',
  `t59s`    INT(10) UNSIGNED    NOT NULL DEFAULT '0' COMMENT '竞技报名合计',
  `t59c`    INT(10) UNSIGNED    NOT NULL DEFAULT '0' COMMENT '竞技报名次数',
  `t60s`    INT(10) UNSIGNED    NOT NULL DEFAULT '0' COMMENT '赌一投币合计',
  `t60c`    INT(10) UNSIGNED    NOT NULL DEFAULT '0' COMMENT '赌一投币次数',
  `t61s`    INT(10) UNSIGNED    NOT NULL DEFAULT '0' COMMENT '赌二投币合计',
  `t61c`    INT(10) UNSIGNED    NOT NULL DEFAULT '0' COMMENT '赌二投币次数',
  `t91s`    INT(10) UNSIGNED    NOT NULL DEFAULT '0' COMMENT '牌局赢豆合计',
  `t91c`    INT(10) UNSIGNED    NOT NULL DEFAULT '0' COMMENT '牌局赢豆次数',
  `t92s`    INT(10) UNSIGNED    NOT NULL DEFAULT '0' COMMENT '牌局输豆合计',
  `t92c`    INT(10) UNSIGNED    NOT NULL DEFAULT '0' COMMENT '牌局输豆次数',
  `tmcr`    INT(10) UNSIGNED    NOT NULL DEFAULT '0' COMMENT '创建时间',
  PRIMARY KEY (`id`),
  UNIQUE KEY `s0` (`dateid`, `moneyid`)
) ENGINE = InnoDB AUTO_INCREMENT = 52 DEFAULT CHARSET = utf8 COMMENT = '每天货币类型分析';

-- ----------------------------
--  Table structure for `lord_record_prize`
-- ----------------------------
DROP TABLE IF EXISTS `lord_record_prize`;
CREATE TABLE `lord_record_prize` (
  `id`          INT(10) UNSIGNED    NOT NULL AUTO_INCREMENT,
  `dateid`      INT(8) UNSIGNED     NOT NULL DEFAULT '0' COMMENT '日期id',
  `type`        TINYINT(3) UNSIGNED NOT NULL DEFAULT '0' COMMENT '发奖类型，同coinsRecord表',
  `uid`         INT(10) UNSIGNED    NOT NULL DEFAULT '0' COMMENT 'UID',
  `coins`       INT(10) UNSIGNED             DEFAULT '0' COMMENT '奖乐豆',
  `coupon`      INT(10) UNSIGNED             DEFAULT '0' COMMENT '奖乐券',
  `lottery`     INT(10) UNSIGNED             DEFAULT '0' COMMENT '奖抽奖',
  `items`       VARCHAR(255)                 DEFAULT '' COMMENT '奖励物品json',
  `create_time` INT(10) UNSIGNED             DEFAULT '0' COMMENT '创建时间',
  PRIMARY KEY (`id`)
) ENGINE = InnoDB AUTO_INCREMENT = 280 DEFAULT CHARSET = utf8 COMMENT = '用户发奖记录';

-- ----------------------------
--  Table structure for `lord_record_table_20160701`
-- ----------------------------
DROP TABLE IF EXISTS `lord_record_table_20160701`;
CREATE TABLE `lord_record_table_20160701` (
  `id`         BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '牌局ID',
  `dateid`     INT(8) UNSIGNED     NOT NULL DEFAULT '0' COMMENT '日期ID',
  `hourid`     TINYINT(2) UNSIGNED NOT NULL DEFAULT '0' COMMENT '小时ID',
  `roomId`     INT(5) UNSIGNED     NOT NULL DEFAULT '0' COMMENT '房间ID',
  `tableId`    VARCHAR(32)                  DEFAULT '' COMMENT '牌桌ID',
  `rate`       INT(10) UNSIGNED    NOT NULL DEFAULT '0' COMMENT '倍率数',
  `rake`       INT(10) UNSIGNED    NOT NULL DEFAULT '0' COMMENT '实际抽水总数',
  `coins`      INT(10) UNSIGNED    NOT NULL DEFAULT '0' COMMENT '乐豆流通数',
  `coupon`     INT(10) UNSIGNED    NOT NULL DEFAULT '0' COMMENT '乐券产出总数',
  `lords`      VARCHAR(6)                   DEFAULT '' COMMENT '地主牌',
  `joker`      VARCHAR(1)                   DEFAULT '' COMMENT '赖子牌',
  `lord`       INT(10) UNSIGNED    NOT NULL DEFAULT '0' COMMENT '地主UID',
  `winner1`    INT(10) UNSIGNED    NOT NULL DEFAULT '0' COMMENT '赢家1UID',
  `winner2`    INT(10) UNSIGNED    NOT NULL DEFAULT '0' COMMENT '赢家2UID',
  `create`     INT(10) UNSIGNED    NOT NULL DEFAULT '0' COMMENT '建桌时间',
  `starte`     INT(10) UNSIGNED    NOT NULL DEFAULT '0' COMMENT '开局时间',
  `finish`     INT(10) UNSIGNED    NOT NULL DEFAULT '0' COMMENT '结束时间',
  `uid0`       INT(10) UNSIGNED    NOT NULL DEFAULT '0' COMMENT '0位UID',
  `channelid0` INT(4) UNSIGNED     NOT NULL DEFAULT '0' COMMENT '0位渠道ID',
  `wcoins0`    INT(10)             NOT NULL DEFAULT '0' COMMENT '0位乐豆数∓',
  `tcoupon0`   INT(10)             NOT NULL DEFAULT '0' COMMENT '0位乐券数∓',
  `hands0`     VARCHAR(40)                  DEFAULT '' COMMENT '0位手牌',
  `cards0`     VARCHAR(40)                  DEFAULT '' COMMENT '0位剩牌',
  `uid1`       INT(10) UNSIGNED    NOT NULL DEFAULT '0' COMMENT '1位UID',
  `channelid1` INT(4) UNSIGNED     NOT NULL DEFAULT '0' COMMENT '1位渠道ID',
  `wcoins1`    INT(10)             NOT NULL DEFAULT '0' COMMENT '1位乐豆数∓',
  `tcoupon1`   INT(10)             NOT NULL DEFAULT '0' COMMENT '1位乐券数∓',
  `hands1`     VARCHAR(40)                  DEFAULT '' COMMENT '1位手牌',
  `cards1`     VARCHAR(40)                  DEFAULT '' COMMENT '1位剩牌',
  `uid2`       INT(10) UNSIGNED    NOT NULL DEFAULT '0' COMMENT '2位UID',
  `channelid2` INT(4) UNSIGNED     NOT NULL DEFAULT '0' COMMENT '2位渠道ID',
  `wcoins2`    INT(10)             NOT NULL DEFAULT '0' COMMENT '2位乐豆数∓',
  `tcoupon2`   INT(10)             NOT NULL DEFAULT '0' COMMENT '2位乐券数∓',
  `hands2`     VARCHAR(40)                  DEFAULT '' COMMENT '2位手牌',
  `cards2`     VARCHAR(40)                  DEFAULT '' COMMENT '2位剩牌',
  `exta`       VARCHAR(32)                  DEFAULT '' COMMENT '预留A',
  `extb`       VARCHAR(32)                  DEFAULT '' COMMENT '预留B',
  `extc`       VARCHAR(32)                  DEFAULT '' COMMENT '预留C',
  `extd`       VARCHAR(32)                  DEFAULT '' COMMENT '预留D',
  `exte`       VARCHAR(32)                  DEFAULT '' COMMENT '预留E',
  `tmcr`       INT(10) UNSIGNED    NOT NULL DEFAULT '0' COMMENT '创建时间',
  PRIMARY KEY (`id`),
  KEY `s0` (`dateid`, `hourid`, `roomId`),
  KEY `s1` (`dateid`, `roomId`, `rate`),
  KEY `s2` (`dateid`, `roomId`, `rake`),
  KEY `s3` (`dateid`, `roomId`, `coins`),
  KEY `s4` (`dateid`, `roomId`, `coupon`),
  KEY `s5` (`channelid0`),
  KEY `s6` (`channelid1`),
  KEY `s7` (`channelid2`),
  KEY `s8` (`uid0`),
  KEY `s9` (`uid1`),
  KEY `s10` (`uid2`),
  KEY `s11` (`lord`, `winner1`, `winner2`)
) ENGINE = InnoDB AUTO_INCREMENT = 3 DEFAULT CHARSET = utf8 COMMENT = '牌局记录日换表名_20160225';

-- ----------------------------
--  Table structure for `lord_record_table_day`
-- ----------------------------
DROP TABLE IF EXISTS `lord_record_table_day`;
CREATE TABLE `lord_record_table_day` (
  `id`        INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `dateid`    INT(8) UNSIGNED  NOT NULL DEFAULT '0' COMMENT '日期ID',
  `roomId`    INT(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '房间ID',
  `games`     INT(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '牌局数',
  `transfers` INT(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '流通数',
  `rakes`     INT(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '乐豆回收数',
  `coupons`   INT(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '乐券投放数',
  `dau`       INT(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'DAU',
  `dnu`       INT(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'DNU',
  `dou`       INT(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'DOU',
  `pcu`       INT(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '分钟PCU',
  `acu`       INT(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '分钟ACU',
  `tmcr`      INT(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '创建时间',
  PRIMARY KEY (`id`),
  UNIQUE KEY `s0` (`dateid`, `roomId`)
) ENGINE = InnoDB AUTO_INCREMENT = 253 DEFAULT CHARSET = utf8 COMMENT = '每天牌桌记录分析';

-- ----------------------------
--  Table structure for `lord_stat_fruit_online`
-- ----------------------------
DROP TABLE IF EXISTS `lord_stat_fruit_online`;
CREATE TABLE `lord_stat_fruit_online` (
  `time`    INT(11) NOT NULL DEFAULT '0' COMMENT '时间',
  `logday`  DATE             DEFAULT '1970-01-01' COMMENT '日期',
  `logtime` TIME             DEFAULT '00:00:00' COMMENT '时间',
  `num`     INT(11)          DEFAULT '0' COMMENT '数量',
  PRIMARY KEY (`time`)
) ENGINE = InnoDB DEFAULT CHARSET = utf8 COMMENT = '水果机在线';

-- ----------------------------
--  Table structure for `lord_thirdpartpay`
-- ----------------------------
DROP TABLE IF EXISTS `lord_thirdpartpay`;
CREATE TABLE `lord_thirdpartpay` (
  `id`           INT(10) UNSIGNED                                                                      NOT NULL AUTO_INCREMENT,
  `cp_trade_no`  VARCHAR(100)                                                                          NOT NULL COMMENT '开发商交易订单号',
  `trade_status` ENUM ('TRADE_CREATE', 'TRADE_SUCC', 'TRADE_CANCEL', 'TRADE_FAIL', 'TRADE_FAIL_FINAL') NOT NULL,
  `uid`          INT(11)                                                                               NOT NULL,
  `channel`      VARCHAR(30)                                                                           NOT NULL COMMENT 'apk渠道，如：xiaomi, letv',
  `create_time`  DATETIME                                                                              NOT NULL COMMENT '订单生成的时间',
  `change_time`  DATETIME                                                                              NOT NULL COMMENT '订单修改的时间',
  `ptype`        VARCHAR(3)                                                                            NOT NULL COMMENT '商品代码',
  `total_fee`    FLOAT                                                                                 NOT NULL COMMENT '订单价格',
  PRIMARY KEY (`id`),
  UNIQUE KEY `cp_trade_no` (`cp_trade_no`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1977 DEFAULT CHARSET = utf8 ROW_FORMAT = COMPACT COMMENT = '斗地主的第三方支付表';

-- ----------------------------
--  Table structure for `lord_top_list`
-- ----------------------------
DROP TABLE IF EXISTS `lord_top_list`;
CREATE TABLE `lord_top_list` (
  `id`     INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `dateid` INT(8) UNSIGNED  NOT NULL DEFAULT '0' COMMENT '日期id',
  `model`  VARCHAR(16)      NOT NULL DEFAULT '""' COMMENT '模式(普通/竞技1)',
  `period` VARCHAR(16)      NOT NULL DEFAULT '""' COMMENT '时段(月/周/日/总)',
  `name`   VARCHAR(16)      NOT NULL DEFAULT '""' COMMENT '榜单名称',
  `list`   TEXT             NOT NULL COMMENT '榜单数据',
  PRIMARY KEY (`id`),
  KEY `dateid` (`dateid`, `model`, `period`, `name`)
) ENGINE = InnoDB AUTO_INCREMENT = 1942 DEFAULT CHARSET = utf8 COMMENT = '斗地主榜单记录表';

-- ----------------------------
--  Table structure for `lord_total_channel`
-- ----------------------------
DROP TABLE IF EXISTS `lord_total_channel`;
CREATE TABLE `lord_total_channel` (
  `id`      INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `is_tv`   INT(10) UNSIGNED NOT NULL DEFAULT '1' COMMENT '是否电视',
  `channel` VARCHAR(111)     NOT NULL DEFAULT '' COMMENT '渠道',
  `dateid`  INT(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '日期id 20150211',
  `DNU`     INT(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '当日注册用户',
  `DR1`     INT(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '次日留存用户',
  `DR2`     INT(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '三日留存',
  `DR6`     INT(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '七日留存',
  `DAU`     INT(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '当日活跃用户',
  `DTU`     INT(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '当日转化用户',
  `DNPU`    INT(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '当日注册付费用户数',
  `DNPR`    FLOAT UNSIGNED   NOT NULL DEFAULT '0' COMMENT '当日注册付费转化率',
  `DPU`     INT(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '当日全部付费用户',
  `DPA`     INT(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '当日付费总额',
  `ARPPU`   FLOAT UNSIGNED   NOT NULL DEFAULT '0' COMMENT '平均每付费用户收入',
  PRIMARY KEY (`id`),
  KEY `dateid` (`dateid`),
  KEY `channel` (`channel`),
  KEY `is_tv` (`is_tv`)
) ENGINE = InnoDB AUTO_INCREMENT = 67189 DEFAULT CHARSET = utf8 COMMENT = '统计表: 渠道运营状况';

-- ----------------------------
--  Table structure for `lord_user`
-- ----------------------------
DROP TABLE IF EXISTS `lord_user`;
CREATE TABLE `lord_user` (
  `uid`         INT(10) UNSIGNED    NOT NULL AUTO_INCREMENT COMMENT 'uid',
  `cool_num`    INT(10) UNSIGNED             DEFAULT '0' COMMENT '靓号',
  `nick`        VARCHAR(32)                  DEFAULT '' COMMENT '昵称',
  `gold`        INT(10) UNSIGNED             DEFAULT '0' COMMENT '金币',
  `coins`       INT(20) UNSIGNED             DEFAULT '0' COMMENT '筹码',
  `coupon`      INT(10) UNSIGNED             DEFAULT '0' COMMENT '奖券',
  `lottery`     INT(10) UNSIGNED             DEFAULT '0' COMMENT '抽奖',
  `trial_coins` BIGINT(20) UNSIGNED          DEFAULT '0' COMMENT '体验筹码',
  `trial_daily` INT(10) UNSIGNED             DEFAULT '0' COMMENT '当日已救济筹码数',
  `sex`         TINYINT(1) UNSIGNED          DEFAULT '0' COMMENT '性别',
  `age`         TINYINT(1) UNSIGNED NOT NULL DEFAULT '0' COMMENT '年龄',
  `word`        VARCHAR(128)                 DEFAULT '' COMMENT '签名',
  `avatar`      INT(10) UNSIGNED             DEFAULT '0' COMMENT '头像',
  `exp`         INT(10) UNSIGNED             DEFAULT '0' COMMENT '经验',
  `level`       INT(10) UNSIGNED             DEFAULT '0' COMMENT '等级',
  `mobile`      VARCHAR(16)         NOT NULL DEFAULT '' COMMENT '手机',
  `wechat`      VARCHAR(64)                  DEFAULT '' COMMENT '微信号',
  `check_code`  INT(4) UNSIGNED              DEFAULT '0' COMMENT '第三方校验码',
  `channel`     VARCHAR(64)                  DEFAULT '' COMMENT '注册渠道',
  `version`     VARCHAR(8)                   DEFAULT '' COMMENT '注册版本',
  `ip`          VARCHAR(15)                  DEFAULT '0.0.0.0' COMMENT '注册ip',
  `add_time`    INT(10) UNSIGNED             DEFAULT '0' COMMENT '注册时间',
  `add_dateid`  INT(8) UNSIGNED              DEFAULT '0' COMMENT '注册日期id',
  `last_ip`     VARCHAR(15)                  DEFAULT '0.0.0.0' COMMENT '上次登录ip',
  `last_login`  INT(10) UNSIGNED             DEFAULT '0' COMMENT '上次登录时间',
  `last_dateid` INT(8) UNSIGNED              DEFAULT '0' COMMENT '上次登录日期id',
  `uuid`        VARCHAR(64)                  DEFAULT '' COMMENT '账号/原始设备号',
  `utype`       VARCHAR(16)                  DEFAULT '' COMMENT '注册类型',
  `device`      VARCHAR(64)                  DEFAULT '' COMMENT '旧版设备号',
  `extend`      VARCHAR(64)                  DEFAULT '' COMMENT '扩展设备号',
  `username`    VARCHAR(64)                  DEFAULT '' COMMENT '账号/加密设备号',
  `password`    VARCHAR(64)                  DEFAULT '' COMMENT '密码',
  `is_tv`       TINYINT(1) UNSIGNED          DEFAULT '1' COMMENT '0手机1电视2广电盒子',
  `state`       TINYINT(1) UNSIGNED          DEFAULT '0' COMMENT '用户异常状态',
  PRIMARY KEY (`uid`),
  KEY `account` (`username`, `password`),
  KEY `version` (`version`, `channel`),
  KEY `device` (`device`, `extend`, `utype`),
  KEY `channel` (`channel`, `is_tv`),
  KEY `cool` (`cool_num`, `check_code`),
  KEY `regist` (`add_dateid`, `channel`),
  KEY `mobile` (`mobile`)
) ENGINE = InnoDB DEFAULT CHARSET = utf8 COMMENT = '斗地主用户表';

-- ----------------------------
--  Table structure for `lord_user_award`
-- ----------------------------
DROP TABLE IF EXISTS `lord_user_award`;
CREATE TABLE `lord_user_award` (
  `id`      INT(10) UNSIGNED    NOT NULL AUTO_INCREMENT,
  `modelId` TINYINT(2) UNSIGNED NOT NULL DEFAULT '0' COMMENT '赛制ID',
  `roomId`  INT(10) UNSIGNED    NOT NULL DEFAULT '0' COMMENT '房间ID',
  `room`    VARCHAR(255)        NOT NULL DEFAULT '' COMMENT '房间名',
  `gameId`  INT(10) UNSIGNED    NOT NULL DEFAULT '0' COMMENT '场次ID',
  `awardid` INT(10) UNSIGNED    NOT NULL DEFAULT '0' COMMENT '奖品ID',
  `award`   VARCHAR(255)        NOT NULL DEFAULT '' COMMENT '奖品名',
  `fileId`  INT(10) UNSIGNED    NOT NULL DEFAULT '0' COMMENT '奖品图',
  `uid`     INT(10) UNSIGNED    NOT NULL DEFAULT '0' COMMENT '用户UID',
  `mobi`    BIGINT(20) UNSIGNED NOT NULL DEFAULT '0' COMMENT '手机号',
  `addr`    VARCHAR(255)        NOT NULL DEFAULT '' COMMENT '发货地址',
  `state`   TINYINT(1) UNSIGNED NOT NULL DEFAULT '0' COMMENT '状态0未处理1已填号2已沟通3已发货',
  `create`  INT(10) UNSIGNED    NOT NULL DEFAULT '0' COMMENT '创建时间',
  `update`  INT(10) UNSIGNED    NOT NULL DEFAULT '0' COMMENT '更新时间',
  PRIMARY KEY (`id`),
  KEY `sb` (`roomId`, `gameId`),
  KEY `sa` (`uid`, `state`, `roomId`, `gameId`)
) ENGINE = InnoDB AUTO_INCREMENT = 1553 DEFAULT CHARSET = utf8;

-- ----------------------------
--  Table structure for `lord_user_cost`
-- ----------------------------
DROP TABLE IF EXISTS `lord_user_cost`;
CREATE TABLE `lord_user_cost` (
  `id`          INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `dateid`      INT(8) UNSIGNED  NOT NULL DEFAULT '0' COMMENT '日期id',
  `type`        VARCHAR(16)               DEFAULT '' COMMENT '类型',
  `channel`     VARCHAR(32)               DEFAULT '' COMMENT '渠道',
  `uid`         INT(11)                   DEFAULT '0' COMMENT 'UID',
  `gold`        INT(11)                   DEFAULT '0' COMMENT '乐币变化',
  `coins`       INT(11)                   DEFAULT '0' COMMENT '乐豆变化',
  `coupon`      INT(11)                   DEFAULT '0' COMMENT '乐券变化',
  `propId`      INT(11)                   DEFAULT '0' COMMENT '道具变化',
  `ip`          VARCHAR(15)               DEFAULT '' COMMENT 'IP',
  `date`        DATETIME                  DEFAULT NULL COMMENT '日期',
  `time`        INT(11)                   DEFAULT '0' COMMENT '时间',
  `is_del`      TINYINT(1) UNSIGNED       DEFAULT '0' COMMENT '是否已删',
  `update_time` INT(10) UNSIGNED          DEFAULT '0' COMMENT '更新时间',
  PRIMARY KEY (`id`),
  KEY `search` (`dateid`, `type`, `channel`, `uid`, `propId`)
) ENGINE = InnoDB AUTO_INCREMENT = 739 DEFAULT CHARSET = utf8 ROW_FORMAT = COMPACT COMMENT = '斗地主消费记录表';

-- ----------------------------
--  Table structure for `lord_user_cost_month_card`
-- ----------------------------
DROP TABLE IF EXISTS `lord_user_cost_month_card`;
CREATE TABLE `lord_user_cost_month_card` (
  `id`          INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `dateid`      INT(8) UNSIGNED  NOT NULL DEFAULT '0' COMMENT '日期id',
  `type`        VARCHAR(16)               DEFAULT '' COMMENT '类型',
  `channel`     VARCHAR(32)               DEFAULT '' COMMENT '渠道',
  `uid`         INT(11)                   DEFAULT '0' COMMENT 'UID',
  `gold`        INT(11)                   DEFAULT '0' COMMENT '乐币变化',
  `coins`       INT(11)                   DEFAULT '0' COMMENT '乐豆变化',
  `coupon`      INT(11)                   DEFAULT '0' COMMENT '乐券变化',
  `propId`      INT(11)                   DEFAULT '0' COMMENT '道具变化',
  `ip`          VARCHAR(15)               DEFAULT '' COMMENT 'IP',
  `date`        DATETIME                  DEFAULT NULL COMMENT '日期',
  `time`        INT(11)                   DEFAULT '0' COMMENT '时间',
  `is_del`      TINYINT(1) UNSIGNED       DEFAULT '0' COMMENT '是否已删',
  `update_time` INT(10) UNSIGNED          DEFAULT '0' COMMENT '更新时间',
  PRIMARY KEY (`id`),
  KEY `search` (`dateid`, `type`, `channel`, `uid`, `propId`)
) ENGINE = InnoDB DEFAULT CHARSET = utf8 ROW_FORMAT = COMPACT COMMENT = '斗地主月卡用户消费记录表';

-- ----------------------------
--  Table structure for `lord_user_inbox`
-- ----------------------------
DROP TABLE IF EXISTS `lord_user_inbox`;
CREATE TABLE `lord_user_inbox` (
  `id`          BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `type`        TINYINT(3) UNSIGNED          DEFAULT '0' COMMENT '0常规30天清理，1群发7天清理',
  `fromuid`     INT(10) UNSIGNED             DEFAULT '0' COMMENT '来源:0系统,1-10000活动,10001-20000任务,>50000用户uid',
  `uid`         INT(10) UNSIGNED             DEFAULT '0' COMMENT 'uid为0时，是默认已读不可删除的全局邮件',
  `subject`     VARCHAR(64)                  DEFAULT '' COMMENT '标题',
  `content`     VARCHAR(255)                 DEFAULT '' COMMENT '内容',
  `items`       VARCHAR(255)                 DEFAULT '' COMMENT '内含物品json',
  `fileid`      INT(10) UNSIGNED    NOT NULL DEFAULT '0' COMMENT '用图id',
  `is_read`     TINYINT(3) UNSIGNED          DEFAULT '0' COMMENT '是否已读',
  `is_del`      TINYINT(3) UNSIGNED          DEFAULT '0' COMMENT '是否已领取或已删除',
  `sort`        TINYINT(3) UNSIGNED          DEFAULT '99' COMMENT '排序',
  `create_time` INT(10) UNSIGNED             DEFAULT '0' COMMENT '创建时间',
  `update_time` INT(10) UNSIGNED             DEFAULT '0' COMMENT '更新时间',
  PRIMARY KEY (`id`),
  KEY `search` (`uid`, `type`, `fromuid`, `is_del`)
) ENGINE = InnoDB AUTO_INCREMENT = 125 DEFAULT CHARSET = utf8 COMMENT = '斗地主用户收件箱';

-- ----------------------------
--  Table structure for `lord_user_item`
-- ----------------------------
DROP TABLE IF EXISTS `lord_user_item`;
CREATE TABLE `lord_user_item` (
  `id`          INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `dateid`      INT(8) UNSIGNED           DEFAULT '0' COMMENT '日期ID',
  `uid`         INT(10) UNSIGNED          DEFAULT '0' COMMENT '用户id',
  `fruid`       INT(10) UNSIGNED          DEFAULT '0' COMMENT '来源uid',
  `cd`          INT(10) UNSIGNED          DEFAULT '0' COMMENT '分类ID',
  `pd`          INT(10) UNSIGNED          DEFAULT '0' COMMENT '道具ID',
  `itemId`      INT(10) UNSIGNED          DEFAULT '0' COMMENT '物品ID',
  `name`        VARCHAR(64)               DEFAULT '' COMMENT '物品名称',
  `num`         INT(11)                   DEFAULT '0' COMMENT '剩余数量',
  `start`       INT(10) UNSIGNED          DEFAULT '0' COMMENT '开始时间',
  `end`         INT(10) UNSIGNED          DEFAULT '0' COMMENT '截至时间',
  `sec`         INT(11)                   DEFAULT '0' COMMENT '剩余秒数',
  `poi`         INT(11)                   DEFAULT '0' COMMENT '剩余持久',
  `state`       TINYINT(1) UNSIGNED       DEFAULT '0' COMMENT '状态 0拥有1启用2用完3坏掉4待毁5？',
  `create_time` INT(10) UNSIGNED          DEFAULT '0' COMMENT '创建时间',
  `update_time` INT(10) UNSIGNED          DEFAULT '0' COMMENT '更新时间',
  PRIMARY KEY (`id`),
  KEY `b` (`end`),
  KEY `c` (`itemId`, `cd`),
  KEY `d` (`cd`, `itemId`),
  KEY `dateid` (`dateid`),
  KEY `a` (`uid`, `cd`, `state`),
  KEY `e` (`uid`, `itemId`)
) ENGINE = InnoDB AUTO_INCREMENT = 254 DEFAULT CHARSET = utf8 COMMENT = '用户道具表';

-- ----------------------------
--  Table structure for `lord_user_logout_20160801`
-- ----------------------------
DROP TABLE IF EXISTS `lord_user_logout_20160801`;
CREATE TABLE `lord_user_logout_20160801` (
  `id`             INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `uid`            INT(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'uid',
  `login_channel`  VARCHAR(32)               DEFAULT '' COMMENT '登入渠道',
  `login_vercode`  INT(5) UNSIGNED           DEFAULT '0' COMMENT '登入版本号',
  `login_ip`       VARCHAR(15)               DEFAULT '' COMMENT '登入IP',
  `login_time`     INT(10) UNSIGNED          DEFAULT '0' COMMENT '登入时间',
  `login_gold`     INT(10) UNSIGNED          DEFAULT '0' COMMENT '登入金币',
  `login_coins`    INT(10) UNSIGNED          DEFAULT '0' COMMENT '登入筹码',
  `login_coupon`   INT(10) UNSIGNED          DEFAULT '0' COMMENT '登入乐券',
  `login_lottery`  INT(10) UNSIGNED          DEFAULT '0' COMMENT '登入抽奖数',
  `last_action`    VARCHAR(32)               DEFAULT '' COMMENT '最后操作',
  `last_time`      INT(10) UNSIGNED          DEFAULT '0' COMMENT '最后操作时间',
  `play`           INT(10) UNSIGNED          DEFAULT '0' COMMENT '牌局数',
  `win`            INT(10) UNSIGNED          DEFAULT '0' COMMENT '胜局数',
  `logout_time`    INT(10) UNSIGNED          DEFAULT '0' COMMENT '登出时间',
  `logout_gold`    INT(10) UNSIGNED          DEFAULT '0' COMMENT '登出金币',
  `logout_coins`   INT(10) UNSIGNED          DEFAULT '0' COMMENT '登出筹码',
  `logout_coupon`  INT(10) UNSIGNED          DEFAULT '0' COMMENT '登出乐券',
  `logout_lottery` INT(10) UNSIGNED          DEFAULT '0' COMMENT '登出抽奖数',
  `online_time`    INT(10) UNSIGNED          DEFAULT '0' COMMENT '在线时长(秒)',
  PRIMARY KEY (`id`),
  KEY `s0` (`uid`),
  KEY `s1` (`login_channel`, `login_vercode`),
  KEY `s2` (`login_vercode`, `login_channel`),
  KEY `s3` (`online_time`),
  KEY `s4` (`logout_coins`),
  KEY `s5` (`logout_coupon`),
  KEY `s6` (`play`),
  KEY `s7` (`win`)
) ENGINE = InnoDB AUTO_INCREMENT = 333033 DEFAULT CHARSET = utf8 COMMENT = '用户登出记录_20160722';

-- ----------------------------
--  Table structure for `lord_user_logout_max`
-- ----------------------------
DROP TABLE IF EXISTS `lord_user_logout_max`;
CREATE TABLE `lord_user_logout_max` (
  `id`             INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `uid`            INT(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'uid',
  `login_channel`  VARCHAR(32)               DEFAULT '' COMMENT '登入渠道',
  `login_vercode`  INT(5) UNSIGNED           DEFAULT '0' COMMENT '登入版本号',
  `login_ip`       VARCHAR(15)               DEFAULT '' COMMENT '登入IP',
  `login_time`     INT(10) UNSIGNED          DEFAULT '0' COMMENT '登入时间',
  `login_gold`     INT(10) UNSIGNED          DEFAULT '0' COMMENT '登入金币',
  `login_coins`    INT(10) UNSIGNED          DEFAULT '0' COMMENT '登入筹码',
  `login_coupon`   INT(10) UNSIGNED          DEFAULT '0' COMMENT '登入乐券',
  `login_lottery`  INT(10) UNSIGNED          DEFAULT '0' COMMENT '登入抽奖数',
  `last_action`    VARCHAR(32)               DEFAULT '' COMMENT '最后操作',
  `last_time`      INT(10) UNSIGNED          DEFAULT '0' COMMENT '最后操作时间',
  `play`           INT(10) UNSIGNED          DEFAULT '0' COMMENT '牌局数',
  `win`            INT(10) UNSIGNED          DEFAULT '0' COMMENT '胜局数',
  `logout_time`    INT(10) UNSIGNED          DEFAULT '0' COMMENT '登出时间',
  `logout_gold`    INT(10) UNSIGNED          DEFAULT '0' COMMENT '登出金币',
  `logout_coins`   INT(10) UNSIGNED          DEFAULT '0' COMMENT '登出筹码',
  `logout_coupon`  INT(10) UNSIGNED          DEFAULT '0' COMMENT '登出乐券',
  `logout_lottery` INT(10) UNSIGNED          DEFAULT '0' COMMENT '登出抽奖数',
  `online_time`    INT(10) UNSIGNED          DEFAULT '0' COMMENT '在线时长(秒)',
  PRIMARY KEY (`id`),
  KEY `s0` (`uid`),
  KEY `s1` (`login_channel`, `login_vercode`),
  KEY `s2` (`login_vercode`, `login_channel`),
  KEY `s3` (`online_time`),
  KEY `s4` (`logout_coins`),
  KEY `s5` (`logout_coupon`),
  KEY `s6` (`play`),
  KEY `s7` (`win`)
) ENGINE = InnoDB AUTO_INCREMENT = 333033 DEFAULT CHARSET = utf8 COMMENT = '用户登出记录_20160722';

-- ----------------------------
--  Table structure for `lord_user_logout_min`
-- ----------------------------
DROP TABLE IF EXISTS `lord_user_logout_min`;
CREATE TABLE `lord_user_logout_min` (
  `id`             INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `uid`            INT(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'uid',
  `login_channel`  VARCHAR(32)               DEFAULT '' COMMENT '登入渠道',
  `login_vercode`  INT(5) UNSIGNED           DEFAULT '0' COMMENT '登入版本号',
  `login_ip`       VARCHAR(15)               DEFAULT '' COMMENT '登入IP',
  `login_time`     INT(10) UNSIGNED          DEFAULT '0' COMMENT '登入时间',
  `login_gold`     INT(10) UNSIGNED          DEFAULT '0' COMMENT '登入金币',
  `login_coins`    INT(10) UNSIGNED          DEFAULT '0' COMMENT '登入筹码',
  `login_coupon`   INT(10) UNSIGNED          DEFAULT '0' COMMENT '登入乐券',
  `login_lottery`  INT(10) UNSIGNED          DEFAULT '0' COMMENT '登入抽奖数',
  `last_action`    VARCHAR(32)               DEFAULT '' COMMENT '最后操作',
  `last_time`      INT(10) UNSIGNED          DEFAULT '0' COMMENT '最后操作时间',
  `play`           INT(10) UNSIGNED          DEFAULT '0' COMMENT '牌局数',
  `win`            INT(10) UNSIGNED          DEFAULT '0' COMMENT '胜局数',
  `logout_time`    INT(10) UNSIGNED          DEFAULT '0' COMMENT '登出时间',
  `logout_gold`    INT(10) UNSIGNED          DEFAULT '0' COMMENT '登出金币',
  `logout_coins`   INT(10) UNSIGNED          DEFAULT '0' COMMENT '登出筹码',
  `logout_coupon`  INT(10) UNSIGNED          DEFAULT '0' COMMENT '登出乐券',
  `logout_lottery` INT(10) UNSIGNED          DEFAULT '0' COMMENT '登出抽奖数',
  `online_time`    INT(10) UNSIGNED          DEFAULT '0' COMMENT '在线时长(秒)',
  PRIMARY KEY (`id`),
  KEY `s0` (`uid`),
  KEY `s1` (`login_channel`, `login_vercode`),
  KEY `s2` (`login_vercode`, `login_channel`),
  KEY `s3` (`online_time`),
  KEY `s4` (`logout_coins`),
  KEY `s5` (`logout_coupon`),
  KEY `s6` (`play`),
  KEY `s7` (`win`)
) ENGINE = InnoDB AUTO_INCREMENT = 333031 DEFAULT CHARSET = utf8 COMMENT = '用户登出记录_20160722';

-- ----------------------------
--  Table structure for `lord_user_logout_tmp`
-- ----------------------------
DROP TABLE IF EXISTS `lord_user_logout_tmp`;
CREATE TABLE `lord_user_logout_tmp` (
  `id` INT(10) UNSIGNED NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE = InnoDB DEFAULT CHARSET = utf8;

-- ----------------------------
--  Table structure for `lord_user_message`
-- ----------------------------
DROP TABLE IF EXISTS `lord_user_message`;
CREATE TABLE `lord_user_message` (
  `id`  BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `uid` INT(10) UNSIGNED    NOT NULL DEFAULT '0',
  `msg` TEXT                NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE = InnoDB AUTO_INCREMENT = 1120 DEFAULT CHARSET = utf8;

-- ----------------------------
--  Table structure for `lord_user_month_card`
-- ----------------------------
DROP TABLE IF EXISTS `lord_user_month_card`;
CREATE TABLE `lord_user_month_card` (
  `id` INT(10) UNSIGNED NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE = InnoDB DEFAULT CHARSET = utf8 ROW_FORMAT = COMPACT COMMENT = '月卡用户ID';

-- ----------------------------
--  Table structure for `lord_user_task`
-- ----------------------------
DROP TABLE IF EXISTS `lord_user_task`;
CREATE TABLE `lord_user_task` (
  `uid`                 INT(10) UNSIGNED    NOT NULL DEFAULT '0' COMMENT 'uid',
  `dateid`              INT(10) UNSIGNED    NOT NULL DEFAULT '0' COMMENT '签到日期',
  `login_all_times`     INT(10) UNSIGNED    NOT NULL DEFAULT '0' COMMENT '登录总计次数',
  `login_day_times`     INT(10) UNSIGNED    NOT NULL DEFAULT '0' COMMENT '登录今日次数',
  `login_last_dateid`   INT(8) UNSIGNED     NOT NULL DEFAULT '0' COMMENT '上次登录日期id',
  `login_this_dateid`   INT(8) UNSIGNED     NOT NULL DEFAULT '0' COMMENT '本次登录日期id',
  `login_day5_day`      TINYINT(1) UNSIGNED NOT NULL DEFAULT '0' COMMENT '5日连续登录的第N天',
  `login_day5_got`      TINYINT(1) UNSIGNED NOT NULL DEFAULT '0' COMMENT '5日连续登录奖励是否已领取',
  `first_day2`          TINYINT(1) UNSIGNED NOT NULL DEFAULT '0' COMMENT '新用户是否次日登录',
  `first_day3`          TINYINT(1) UNSIGNED NOT NULL DEFAULT '0' COMMENT '新用户是否三日登录',
  `first_day7`          TINYINT(1) UNSIGNED NOT NULL DEFAULT '0' COMMENT '新用户是否七日登录',
  `gold_level`          TINYINT(1) UNSIGNED NOT NULL DEFAULT '0' COMMENT '1充值金牌用户',
  `gold_all`            INT(10) UNSIGNED    NOT NULL DEFAULT '0' COMMENT '累计使用金币数',
  `gold_week`           INT(10) UNSIGNED    NOT NULL DEFAULT '0' COMMENT '本周使用金币数',
  `gold_day`            INT(10) UNSIGNED    NOT NULL DEFAULT '0' COMMENT '今日使用金币数',
  `coupon_all`          INT(10) UNSIGNED    NOT NULL DEFAULT '0' COMMENT '累计获得奖券数',
  `coupon_week`         INT(10) UNSIGNED    NOT NULL DEFAULT '0' COMMENT '本周获得奖券数',
  `coupon_day`          INT(10) UNSIGNED    NOT NULL DEFAULT '0' COMMENT '今日获得奖券数',
  `coins_all`           INT(10) UNSIGNED    NOT NULL DEFAULT '0' COMMENT '累计获得筹码数',
  `coins_week`          INT(10) UNSIGNED    NOT NULL DEFAULT '0' COMMENT '本周获得筹码数',
  `coins_day`           INT(10) UNSIGNED    NOT NULL DEFAULT '0' COMMENT '今日获得筹码数',
  `normal_all_play`     INT(10) UNSIGNED    NOT NULL DEFAULT '0' COMMENT '普通场总计牌局次数',
  `normal_all_win`      INT(10) UNSIGNED    NOT NULL DEFAULT '0' COMMENT '普通场总计胜局次数',
  `normal_all_earn`     INT(10) UNSIGNED    NOT NULL DEFAULT '0' COMMENT '普通场总计赢取钱数',
  `normal_all_maxrate`  INT(10) UNSIGNED    NOT NULL DEFAULT '0' COMMENT '普通场总计最大倍率',
  `normal_all_maxearn`  INT(10) UNSIGNED    NOT NULL DEFAULT '0' COMMENT '普通场总计最大赢取',
  `normal_week_play`    INT(10) UNSIGNED    NOT NULL DEFAULT '0' COMMENT '普通场本周牌局次数',
  `normal_week_win`     INT(10) UNSIGNED    NOT NULL DEFAULT '0' COMMENT '普通场本周胜局次数',
  `normal_week_earn`    INT(10) UNSIGNED    NOT NULL DEFAULT '0' COMMENT '普通场本周赢取钱数',
  `normal_week_maxrate` INT(10) UNSIGNED    NOT NULL DEFAULT '0' COMMENT '普通场本周最大倍率',
  `normal_week_maxearn` INT(10) UNSIGNED    NOT NULL DEFAULT '0' COMMENT '普通场本周最大赢取',
  `normal_day_play`     INT(10) UNSIGNED    NOT NULL DEFAULT '0' COMMENT '普通场今天牌局次数',
  `normal_day_win`      INT(10) UNSIGNED    NOT NULL DEFAULT '0' COMMENT '普通场今天胜局次数',
  `normal_day_earn`     INT(10) UNSIGNED    NOT NULL DEFAULT '0' COMMENT '普通场今天赢取次数',
  `normal_day_maxrate`  INT(10) UNSIGNED    NOT NULL DEFAULT '0' COMMENT '普通场今天最大倍率',
  `normal_day_maxearn`  INT(10) UNSIGNED    NOT NULL DEFAULT '0' COMMENT '普通场今天最大赢取',
  `match_all_play`      INT(10) UNSIGNED    NOT NULL DEFAULT '0' COMMENT '竞技场总计参加次数',
  `match_all_point`     INT(10) UNSIGNED    NOT NULL DEFAULT '0' COMMENT '竞技场总计赢取积分',
  `match_week_play`     INT(10) UNSIGNED    NOT NULL DEFAULT '0' COMMENT '竞技场本周参加次数',
  `match_week_point`    INT(10) UNSIGNED    NOT NULL DEFAULT '0' COMMENT '竞技场本周赢取积分',
  `match_day_play`      INT(10) UNSIGNED    NOT NULL DEFAULT '0' COMMENT '竞技场今天参加次数',
  `match_day_point`     INT(10) UNSIGNED    NOT NULL DEFAULT '0' COMMENT '竞技场今天赢取积分',
  `lottery_all_times`   INT(10) UNSIGNED    NOT NULL DEFAULT '0' COMMENT '抽奖总计次数',
  `lottery_week_times`  INT(10) UNSIGNED    NOT NULL DEFAULT '0' COMMENT '抽奖本周次数',
  `lottery_day_times`   INT(10) UNSIGNED    NOT NULL DEFAULT '0' COMMENT '抽奖今天次数',
  `task1`               INT(10) UNSIGNED    NOT NULL DEFAULT '0',
  `task1dateid`         INT(10) UNSIGNED    NOT NULL DEFAULT '0',
  `task4`               INT(10) UNSIGNED    NOT NULL DEFAULT '0',
  `task4dateid`         INT(10) UNSIGNED    NOT NULL DEFAULT '0',
  `task5`               INT(10) UNSIGNED    NOT NULL DEFAULT '0',
  `task5dateid`         INT(10) UNSIGNED    NOT NULL DEFAULT '0',
  `task6`               INT(10) UNSIGNED    NOT NULL DEFAULT '0',
  `task6dateid`         INT(10) UNSIGNED    NOT NULL DEFAULT '0',
  `task3`               INT(10) UNSIGNED    NOT NULL DEFAULT '0',
  `task3dateid`         INT(10) UNSIGNED    NOT NULL DEFAULT '0',
  `task2`               INT(10) UNSIGNED    NOT NULL DEFAULT '0',
  `task2dateid`         INT(10) UNSIGNED    NOT NULL DEFAULT '0',
  `task11`              INT(10) UNSIGNED    NOT NULL DEFAULT '0',
  `task11dateid`        INT(10) UNSIGNED    NOT NULL DEFAULT '0',
  `task12`              INT(10) UNSIGNED    NOT NULL DEFAULT '0',
  `task12dateid`        INT(10) UNSIGNED    NOT NULL DEFAULT '0',
  `task13`              INT(10) UNSIGNED             DEFAULT '0',
  `task13dateid`        INT(10) UNSIGNED             DEFAULT '0',
  `task14`              INT(10) UNSIGNED             DEFAULT '0',
  `task14dateid`        INT(10) UNSIGNED             DEFAULT '0',
  `task15`              INT(10) UNSIGNED             DEFAULT '0',
  `task15dateid`        INT(10) UNSIGNED             DEFAULT '0',
  `task16`              INT(10) UNSIGNED    NOT NULL DEFAULT '0',
  `task16dateid`        INT(10) UNSIGNED    NOT NULL DEFAULT '0',
  `task17`              INT(10) UNSIGNED    NOT NULL DEFAULT '0',
  `task17dateid`        INT(10) UNSIGNED    NOT NULL DEFAULT '0',
  PRIMARY KEY (`uid`),
  KEY `dateid` (`dateid`)
) ENGINE = InnoDB DEFAULT CHARSET = utf8;

-- ----------------------------
--  Table structure for `lord_user_taskrecord`
-- ----------------------------
DROP TABLE IF EXISTS `lord_user_taskrecord`;
CREATE TABLE `lord_user_taskrecord` (
  `id`        INT(10) UNSIGNED    NOT NULL AUTO_INCREMENT COMMENT '编号',
  `uid`       INT(10) UNSIGNED    NOT NULL DEFAULT '0' COMMENT '用户id',
  `taskid`    INT(10) UNSIGNED    NOT NULL DEFAULT '0' COMMENT '任务id',
  `dateid`    INT(10) UNSIGNED    NOT NULL DEFAULT '0' COMMENT '任务周期开始日期	如20141111',
  `days`      TINYINT(3) UNSIGNED NOT NULL DEFAULT '0' COMMENT '任务周期内第几天 如1',
  `times`     TINYINT(3) UNSIGNED NOT NULL DEFAULT '0' COMMENT '任务周天内第几次 如1',
  `gold`      INT(10) UNSIGNED    NOT NULL DEFAULT '0' COMMENT '获得金币',
  `coupon`    INT(10) UNSIGNED    NOT NULL DEFAULT '0' COMMENT '获得奖券',
  `coins`     INT(10) UNSIGNED    NOT NULL DEFAULT '0' COMMENT '获得筹码',
  `exp`       INT(10) UNSIGNED    NOT NULL DEFAULT '0' COMMENT '获得经验',
  `lottery`   INT(10) UNSIGNED    NOT NULL DEFAULT '0' COMMENT '获得抽奖机会',
  `propid`    INT(10) UNSIGNED    NOT NULL DEFAULT '0' COMMENT '获得主要道具id',
  `props`     VARCHAR(255)        NOT NULL DEFAULT '[]' COMMENT '获得道具ids',
  `ut_create` INT(10) UNSIGNED    NOT NULL DEFAULT '0' COMMENT '创建时间',
  `ut_update` INT(10) UNSIGNED    NOT NULL DEFAULT '0' COMMENT '更新时间',
  PRIMARY KEY (`id`),
  KEY `taskid` (`taskid`),
  KEY `uid` (`uid`)
) ENGINE = InnoDB AUTO_INCREMENT = 574 DEFAULT CHARSET = utf8;

-- ----------------------------
--  Table structure for `lord_user_tesk`
-- ----------------------------
DROP TABLE IF EXISTS `lord_user_tesk`;
CREATE TABLE `lord_user_tesk` (
  `uid`         INT(10) UNSIGNED NOT NULL COMMENT 'UID',
  `teskCode`    TEXT COMMENT '活动任务情况json',
  `update_time` INT(10) UNSIGNED DEFAULT '0' COMMENT '更新时间',
  PRIMARY KEY (`uid`)
) ENGINE = InnoDB DEFAULT CHARSET = utf8 COMMENT = '斗地主用户下线时的活动任务完成情况';

-- ----------------------------
--  Table structure for `lord_user_teskrecord`
-- ----------------------------
DROP TABLE IF EXISTS `lord_user_teskrecord`;
CREATE TABLE `lord_user_teskrecord` (
  `id`          INT(10) UNSIGNED    NOT NULL AUTO_INCREMENT,
  `uid`         INT(10) UNSIGNED    NOT NULL DEFAULT '0' COMMENT 'UID',
  `teskId`      INT(10) UNSIGNED    NOT NULL DEFAULT '0' COMMENT '活动任务id',
  `periodId`    INT(10) UNSIGNED    NOT NULL DEFAULT '0' COMMENT '活动任务周期id',
  `dateid`      INT(8) UNSIGNED     NOT NULL DEFAULT '0' COMMENT '日期id',
  `keyVal`      VARCHAR(255)        NOT NULL DEFAULT '' COMMENT '达成任务时的字段及值',
  `gold`        INT(10) UNSIGNED             DEFAULT '0' COMMENT '任务奖励：金币',
  `coins`       INT(10) UNSIGNED             DEFAULT '0' COMMENT '任务奖励：筹码',
  `coupon`      INT(10) UNSIGNED             DEFAULT '0' COMMENT '任务奖励：奖券',
  `lottery`     INT(10) UNSIGNED             DEFAULT '0' COMMENT '任务奖励：抽奖',
  `propId`      INT(10) UNSIGNED             DEFAULT '0' COMMENT '任务奖励：主道具id',
  `props`       VARCHAR(255)                 DEFAULT '' COMMENT '任务奖励：道具json',
  `other`       VARCHAR(255)        NOT NULL DEFAULT '' COMMENT '任务奖励：其他json。如：10元话费',
  `chance`      INT(10) UNSIGNED             DEFAULT '0' COMMENT '惊喜概率/10000',
  `random`      INT(10) UNSIGNED             DEFAULT '0' COMMENT '随机点数<10000',
  `is_surprise` TINYINT(1) UNSIGNED          DEFAULT '0' COMMENT '是否触发了惊喜',
  `is_grab`     TINYINT(1) UNSIGNED NOT NULL DEFAULT '0' COMMENT '惊喜是否可抢夺',
  `create_time` INT(10) UNSIGNED             DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `search-uid` (`uid`),
  KEY `search-all` (`teskId`, `periodId`, `dateid`, `uid`, `is_surprise`, `is_grab`)
) ENGINE = InnoDB DEFAULT CHARSET = utf8 COMMENT = '斗地主用户活动任务完成记录表';

-- ----------------------------
--  Table structure for `lord_user_tesksurprise`
-- ----------------------------
DROP TABLE IF EXISTS `lord_user_tesksurprise`;
CREATE TABLE `lord_user_tesksurprise` (
  `id`          INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `dateid`      INT(8) UNSIGNED  NOT NULL DEFAULT '0' COMMENT '日期id',
  `teskId`      INT(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '活动任务id',
  `periodId`    INT(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '活动任务周期id',
  `surpriseId`  INT(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '暴奖id',
  `uid`         INT(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '惊喜获得者UID',
  `teskUid`     INT(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '触发惊喜者UID',
  `rests`       INT(10) UNSIGNED          DEFAULT '0' COMMENT '本日剩余次数',
  `tableId`     VARCHAR(32)      NOT NULL DEFAULT '' COMMENT '牌桌id',
  `gold`        INT(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '惊喜奖励：金币',
  `coins`       INT(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '惊喜奖励：筹码',
  `coupon`      INT(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '惊喜奖励：奖券',
  `lottery`     INT(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '惊喜奖励：抽奖数',
  `propId`      INT(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '惊喜奖励：主道具id',
  `props`       VARCHAR(255)     NOT NULL DEFAULT '' COMMENT '惊喜奖励：道具json',
  `other`       VARCHAR(255)     NOT NULL DEFAULT '' COMMENT '惊喜奖励：其他json。如：10元话费',
  `is_grab`     TINYINT(1) UNSIGNED       DEFAULT '0' COMMENT '0不抢1可抢',
  `create_time` INT(10) UNSIGNED NOT NULL DEFAULT '0',
  `update_time` INT(10) UNSIGNED          DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `search-uid` (`uid`, `dateid`),
  KEY `search-all` (`dateid`, `teskId`, `periodId`, `surpriseId`, `uid`)
) ENGINE = InnoDB DEFAULT CHARSET = utf8 COMMENT = '斗地主活动任务惊喜记录表';

-- ----------------------------
--  Table structure for `lord_user_unbox`
-- ----------------------------
DROP TABLE IF EXISTS `lord_user_unbox`;
CREATE TABLE `lord_user_unbox` (
  `id`          BIGINT(20) UNSIGNED NOT NULL DEFAULT '0',
  `type`        TINYINT(3) UNSIGNED          DEFAULT '0' COMMENT '0常规30天清理，1群发7天清理',
  `fromuid`     INT(10) UNSIGNED             DEFAULT '0' COMMENT '来源:0系统,>0用户uid',
  `uid`         INT(10) UNSIGNED             DEFAULT '0' COMMENT 'uid为0时，是默认已读不可删除的全局邮件',
  `subject`     VARCHAR(64)                  DEFAULT '' COMMENT '标题',
  `content`     VARCHAR(255)                 DEFAULT '' COMMENT '内容',
  `items`       VARCHAR(255)                 DEFAULT '' COMMENT '内含物品json',
  `fileid`      INT(10) UNSIGNED    NOT NULL DEFAULT '0' COMMENT '用图id',
  `is_read`     TINYINT(3) UNSIGNED          DEFAULT '0' COMMENT '是否已读',
  `is_del`      TINYINT(3) UNSIGNED          DEFAULT '0' COMMENT '是否已领取或已删除',
  `sort`        TINYINT(3) UNSIGNED          DEFAULT '99',
  `create_time` INT(10) UNSIGNED             DEFAULT '0' COMMENT '创建时间',
  `update_time` INT(10) UNSIGNED             DEFAULT '0' COMMENT '更新时间',
  PRIMARY KEY (`id`),
  KEY `search` (`uid`, `type`, `fromuid`, `is_del`)
) ENGINE = InnoDB DEFAULT CHARSET = utf8 COMMENT = '斗地主用户废件箱';

-- ----------------------------
--  Table structure for `user_analyse`
-- ----------------------------
DROP TABLE IF EXISTS `user_analyse`;
CREATE TABLE `user_analyse` (
  `id`        INT(11)     NOT NULL AUTO_INCREMENT,
  `uid`       INT(11)     NOT NULL,
  `device`    VARCHAR(50) NOT NULL COMMENT '注册设备号',
  `ip`        VARCHAR(30) NOT NULL COMMENT '注册ip',
  `last_ip`   VARCHAR(30) NOT NULL COMMENT '上次登录ip',
  `add_time`  DATETIME    NOT NULL COMMENT '加入时间',
  `last_time` DATETIME    NOT NULL COMMENT '最后登录',
  PRIMARY KEY (`id`),
  KEY `uid` (`uid`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 342038 DEFAULT CHARSET = utf8 ROW_FORMAT = COMPACT COMMENT = '用户统计信息';

-- ----------------------------
--  Table structure for `user_login`
-- ----------------------------
DROP TABLE IF EXISTS `user_login`;
CREATE TABLE `user_login` (
  `id`        INT(11)     NOT NULL AUTO_INCREMENT,
  `uid`       INT(11)     NOT NULL COMMENT 'user_user.id',
  `open_type` VARCHAR(30) NOT NULL COMMENT '登录类型',
  `open_id`   VARCHAR(50) NOT NULL COMMENT '登录名(加密)',
  `extend`    VARCHAR(64) NOT NULL DEFAULT '' COMMENT '扩展设备号，用做串号校验',
  PRIMARY KEY (`id`),
  KEY `uid` (`uid`) USING BTREE,
  KEY `login` (`open_id`, `extend`),
  KEY `open_id` (`open_id`),
  KEY `extend` (`extend`),
  KEY `sdkuid` (`open_type`)
) ENGINE = InnoDB AUTO_INCREMENT = 342038 DEFAULT CHARSET = utf8 ROW_FORMAT = COMPACT COMMENT = '登录';

-- ----------------------------
--  Table structure for `user_user`
-- ----------------------------
DROP TABLE IF EXISTS `user_user`;
CREATE TABLE `user_user` (
  `id`       INT(11)     NOT NULL AUTO_INCREMENT,
  `account`  VARCHAR(30)          DEFAULT NULL COMMENT '登录账号',
  `password` VARCHAR(50) NOT NULL COMMENT '密码',
  `uuid`     VARCHAR(50) NOT NULL COMMENT '原始id',
  `channel`  VARCHAR(50) NOT NULL COMMENT '渠道号',
  `state`    SMALLINT(6) NOT NULL DEFAULT '0' COMMENT '用户状态',
  PRIMARY KEY (`id`)
) ENGINE = InnoDB AUTO_INCREMENT = 342211 DEFAULT CHARSET = utf8 ROW_FORMAT = COMPACT COMMENT = '用户';

SET FOREIGN_KEY_CHECKS = 1;
