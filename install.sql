CREATE TABLE lord_game_fruit (
  uid             INT(11)  NOT NULL DEFAULT 0,
  first_day       DATETIME NOT NULL DEFAULT '1970-01-01 00:00:00',
  credit          INT(11)           DEFAULT 0,
  round           INT(11)           DEFAULT 0,
  intervene_round INT(11)           DEFAULT 0,
  curr_round_win  INT(11)           DEFAULT 0,
  total_win       INT(11)           DEFAULT 0,
  persist_round   INT(11)           DEFAULT 0,
  total_bet       INT(11)           DEFAULT 0,
  PRIMARY KEY (uid)
)
  ENGINE = MYISAM;

-- ----------------------------
--  Table structure for `lord_user_cost`
-- ----------------------------
DROP TABLE IF EXISTS `lord_user_cost_month_card`;
CREATE TABLE `lord_user_cost_month_card` (
  `id`          INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `dateid`      INT(8) UNSIGNED  NOT NULL DEFAULT '0'
  COMMENT '日期id',
  `type`        VARCHAR(16)               DEFAULT ''
  COMMENT '类型',
  `channel`     VARCHAR(32)               DEFAULT ''
  COMMENT '渠道',
  `uid`         INT(11)                   DEFAULT '0'
  COMMENT 'UID',
  `gold`        INT(11)                   DEFAULT '0'
  COMMENT '乐币变化',
  `coins`       INT(11)                   DEFAULT '0'
  COMMENT '乐豆变化',
  `coupon`      INT(11)                   DEFAULT '0'
  COMMENT '乐券变化',
  `propId`      INT(11)                   DEFAULT '0'
  COMMENT '道具变化',
  `ip`          VARCHAR(15)               DEFAULT ''
  COMMENT 'IP',
  `date`        DATETIME                  DEFAULT NULL
  COMMENT '日期',
  `time`        INT(11)                   DEFAULT '0'
  COMMENT '时间',
  `is_del`      TINYINT(1) UNSIGNED       DEFAULT '0'
  COMMENT '是否已删',
  `update_time` INT(10) UNSIGNED          DEFAULT '0'
  COMMENT '更新时间',
  PRIMARY KEY (`id`),
  KEY `search` (`dateid`, `type`, `channel`, `uid`, `propId`)
)
  ENGINE = InnoDB
  AUTO_INCREMENT = 0
  DEFAULT CHARSET = utf8
  ROW_FORMAT = COMPACT
  COMMENT = '斗地主月卡用户消费记录表';


CREATE TABLE `lord_user_month_card` (
  `id` INT(10) UNSIGNED NOT NULL,
  PRIMARY KEY (`id`)
)
  ENGINE = InnoDB
  DEFAULT CHARSET = utf8
  ROW_FORMAT = COMPACT
  COMMENT = '月卡用户ID';

TRUNCATE lord_user_month_card;
INSERT INTO lord_user_month_card (`id`) (SELECT uid
                                         FROM lord_user_item
                                         WHERE pd = 7
                                         GROUP BY uid);
TRUNCATE lord_user_cost_month_card;
INSERT INTO lord_user_cost_month_card (SELECT *
                                       FROM lord_user_cost
                                       WHERE uid IN (SELECT id
                                                     FROM lord_user_month_card));

SELECT count(*) AS num
FROM log_fruit_enter
WHERE log_fruit_bet.logdate >= '2016-09-01' AND log_fruit_bet.logdate <= '2016-09-02'
LIMIT 1;

DROP TABLE IF EXISTS `lord_stat_fruit_online`;
CREATE TABLE `lord_stat_fruit_online` (
  `time`    INT(11) DEFAULT '0'
  COMMENT '时间',
  `logday`  DATE    DEFAULT '1970-01-01'
  COMMENT '日期',
  `logtime` TIME    DEFAULT '00:00'
  COMMENT '时间',
  `num`     INT(11) DEFAULT '0'
  COMMENT '数量',
  PRIMARY KEY (`time`)
)
  ENGINE = InnoDB
  AUTO_INCREMENT = 0
  DEFAULT CHARSET = utf8
  COMMENT = '水果机在线';

DROP TABLE IF EXISTS `lord_game_user_extra`;
CREATE TABLE `lord_game_user_extra` (
  `uid`            INT(11)     NOT NULL,

  `entry_expenses` VARCHAR(30) NOT NULL  DEFAULT '' COMMENT '报名比赛场的真实花费',

  `bk_int1`        INT(11)     NOT NULL  DEFAULT '0' COMMENT '备用字段INT1',
  `bk_int2`        INT(11)     NOT NULL  DEFAULT '0' COMMENT '备用字段INT2',
  `bk_int3`        INT(11)     NOT NULL  DEFAULT '0' COMMENT '备用字段INT3',
  `bk_int4`        INT(11)     NOT NULL  DEFAULT '0' COMMENT '备用字段INT4',
  `bk_int5`        INT(11)     NOT NULL  DEFAULT '0' COMMENT '备用字段INT5',
  `bk_int6`        INT(11)     NOT NULL  DEFAULT '0' COMMENT '备用字段INT6',
  `bk_int7`        INT(11)     NOT NULL  DEFAULT '0' COMMENT '备用字段INT7',
  `bk_int8`        INT(11)     NOT NULL  DEFAULT '0' COMMENT '备用字段INT8',
  `bk_int9`        INT(11)     NOT NULL  DEFAULT '0' COMMENT '备用字段INT9',
  `bk_int10`       INT(11)     NOT NULL  DEFAULT '0' COMMENT '备用字段INT10',

  `bk_vchar1`      VARCHAR(30) NOT NULL  DEFAULT '' COMMENT '备用字段VARCHAR1',
  `bk_vchar2`      VARCHAR(30) NOT NULL  DEFAULT '' COMMENT '备用字段VARCHAR2',
  `bk_vchar3`      VARCHAR(30) NOT NULL  DEFAULT '' COMMENT '备用字段VARCHAR3',
  `bk_vchar4`      VARCHAR(30) NOT NULL  DEFAULT '' COMMENT '备用字段VARCHAR4',
  `bk_vchar5`      VARCHAR(30) NOT NULL  DEFAULT '' COMMENT '备用字段VARCHAR5',
  `bk_vchar6`      VARCHAR(30) NOT NULL  DEFAULT '' COMMENT '备用字段VARCHAR6',
  `bk_vchar7`      VARCHAR(30) NOT NULL  DEFAULT '' COMMENT '备用字段VARCHAR7',
  `bk_vchar8`      VARCHAR(30) NOT NULL  DEFAULT '' COMMENT '备用字段VARCHAR8',
  `bk_vchar9`      VARCHAR(30) NOT NULL  DEFAULT '' COMMENT '备用字段VARCHAR9',
  `bk_vchar10`     VARCHAR(30) NOT NULL  DEFAULT '' COMMENT '备用字段VARCHAR10',
  PRIMARY KEY (`uid`)
) ENGINE = InnoDB AUTO_INCREMENT = 0 DEFAULT CHARSET = utf8 ROW_FORMAT = COMPACT COMMENT = '游戏用户额外表';

##region 百人牛牛

-- ------------------------------------------------------
-- 百人牛牛进场日志
-- ------------------------------------------------------
DROP TABLE IF EXISTS log_cow_enter;
CREATE TABLE `log_cow_enter` (
  `id`     INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `uid`    INT(10)          NOT NULL DEFAULT 0 COMMENT '玩家UID',
  `gold`   INT(15)          NOT NULL DEFAULT 0 COMMENT '进场时带入的货币金额',
  `bet`    INT(15)          NOT NULL DEFAULT 0 COMMENT '兑换后的筹码',
  logday   DATE             NOT NULL DEFAULT '1970-01-01' COMMENT '日志记录日期',
  firstday DATE             NOT NULL DEFAULT '1970-01-01' COMMENT '首次玩百人牛牛的日期',
  logdate  DATETIME         NOT NULL DEFAULT '1970-01-01 00:00:00' COMMENT '日志记录的时间',
  PRIMARY KEY (`id`)
)
  ENGINE = MYISAM
  DEFAULT CHARSET = utf8
  COMMENT = '百人牛牛进场日志';

-- ------------------------------------------------------
-- 百人牛牛离场日志
-- ------------------------------------------------------
DROP TABLE IF EXISTS log_cow_exit;
CREATE TABLE `log_cow_exit` (
  `id`     INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `uid`    INT(10)          NOT NULL DEFAULT 0 COMMENT '玩家UID',
  `gold`   INT(15)          NOT NULL DEFAULT 0 COMMENT '离场时带出的货币金额',
  `bet`    INT(15)          NOT NULL DEFAULT 0 COMMENT '兑换前的筹码',
  logday   DATE             NOT NULL DEFAULT '1970-01-01' COMMENT '日志记录日期',
  firstday DATE             NOT NULL DEFAULT '1970-01-01' COMMENT '首次玩百人牛牛的日期',
  logdate  DATETIME         NOT NULL DEFAULT '1970-01-01 00:00:00' COMMENT '日志记录的时间',
  PRIMARY KEY (`id`)
)
  ENGINE = MYISAM
  DEFAULT CHARSET = utf8
  COMMENT = '百人牛牛进场日志';

-- ------------------------------------------------------
-- 百人牛牛结算日志
-- ------------------------------------------------------
DROP TABLE IF EXISTS log_cow_settlement;
CREATE TABLE `log_cow_settlement` (
  `id`         INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `uid`        INT(10)          NOT NULL DEFAULT 0 COMMENT '玩家UID',
  `per_profit` TINYINT(1)       NOT NULL DEFAULT 0 COMMENT '抽成前的结算值',
  `system_cut` INT(15)          NOT NULL DEFAULT 0 COMMENT '系统抽成',
  `pot_cut`    INT(15)          NOT NULL DEFAULT 0 COMMENT '奖池抽成',
  `pot_gift`   INT(15)          NOT NULL DEFAULT 0 COMMENT '奖池奖励',
  `profit`     INT(15)          NOT NULL DEFAULT 0 COMMENT '真实的结算值',
  `investment` VARCHAR(32)      NOT NULL DEFAULT '' COMMENT '在位置上的总共的下注数',
  logday       DATE             NOT NULL DEFAULT '1970-01-01' COMMENT '日志记录日期',
  firstday     DATE             NOT NULL DEFAULT '1970-01-01' COMMENT '首次玩百人牛牛的日期',
  logdate      DATETIME         NOT NULL DEFAULT '1970-01-01 00:00:00' COMMENT '日志记录的时间',
  PRIMARY KEY (`id`)
)
  ENGINE = MYISAM
  DEFAULT CHARSET = utf8
  COMMENT = '百人牛牛下注日志';

-- ------------------------------------------------------
-- 百人牛牛下注日志
-- ------------------------------------------------------
DROP TABLE IF EXISTS log_cow_wager;
CREATE TABLE `log_cow_wager` (
  `id`         INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `uid`        INT(10)          NOT NULL DEFAULT 0 COMMENT '玩家UID',
  `position`   TINYINT(1)       NOT NULL DEFAULT 0 COMMENT '位置 1=天 2=地 3=玄 4=黄',
  `num`        INT(15)          NOT NULL DEFAULT 0 COMMENT '下注金额',
  `bet`        INT(15)          NOT NULL DEFAULT 0 COMMENT '下注后手中的筹码',
  `investment` INT(15)          NOT NULL DEFAULT 0 COMMENT '在位置上的总共的下注数',
  `round`      INT(16)          NOT NULL DEFAULT 0 COMMENT '回合编号',
  logday       DATE             NOT NULL DEFAULT '1970-01-01' COMMENT '日志记录日期',
  firstday     DATE             NOT NULL DEFAULT '1970-01-01' COMMENT '首次玩百人牛牛的日期',
  logdate      DATETIME         NOT NULL DEFAULT '1970-01-01 00:00:00' COMMENT '日志记录的时间',
  PRIMARY KEY (`id`)
)
  ENGINE = MYISAM
  DEFAULT CHARSET = utf8
  COMMENT = '百人牛牛下注日志';

-- ------------------------------------------------------
-- 百人牛牛发牌日志
-- ------------------------------------------------------
DROP TABLE IF EXISTS log_cow_send_card;
CREATE TABLE `log_cow_send_card` (
  `id`              INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `round`           INT(16)          NOT NULL DEFAULT 0 COMMENT '回合编号',
  `position0_cards` VARCHAR(255)     NOT NULL DEFAULT '' COMMENT '庄家的牌',
  `position1_cards` VARCHAR(255)     NOT NULL DEFAULT '' COMMENT '天位置的牌',
  `position2_cards` VARCHAR(255)     NOT NULL DEFAULT '' COMMENT '地位置的牌',
  `position3_cards` VARCHAR(255)     NOT NULL DEFAULT '' COMMENT '玄位置的牌',
  `position4_cards` VARCHAR(255)     NOT NULL DEFAULT '' COMMENT '黄位置的牌',
  `position0_type`  TINYINT(2)       NOT NULL DEFAULT 0 COMMENT '庄家牛几?',
  `position1_type`  TINYINT(2)       NOT NULL DEFAULT 0 COMMENT '天位置牛几?',
  `position2_type`  TINYINT(2)       NOT NULL DEFAULT 0 COMMENT '地位置牛几?',
  `position3_type`  TINYINT(2)       NOT NULL DEFAULT 0 COMMENT '玄位置牛几?',
  `position4_type`  TINYINT(2)       NOT NULL DEFAULT 0 COMMENT '黄位置牛几?',
  logday            DATE             NOT NULL DEFAULT '1970-01-01' COMMENT '日志记录日期',
  firstday          DATE             NOT NULL DEFAULT '1970-01-01' COMMENT '首次玩百人牛牛的日期',
  logdate           DATETIME         NOT NULL DEFAULT '1970-01-01 00:00:00' COMMENT '日志记录的时间',
  PRIMARY KEY (`id`)
)
  ENGINE = MYISAM
  DEFAULT CHARSET = utf8
  COMMENT = '百人牛牛发牌日志';

-- ------------------------------------------------------
-- 百人牛牛注变化日志
-- ------------------------------------------------------

-- ------------------------------------------------------
-- 百人牛牛在线日志
-- ------------------------------------------------------
DROP TABLE IF EXISTS `log_cow_online`;
CREATE TABLE `log_cow_online` (
  `time`    INT(11) DEFAULT '0' COMMENT '时间',
  `logday`  DATE    DEFAULT '1970-01-01' COMMENT '日期',
  `logtime` TIME    DEFAULT '00:00' COMMENT '时间',
  `num`     INT(11) DEFAULT '0' COMMENT '数量',
  PRIMARY KEY (`time`)
)
  ENGINE = InnoDB
  AUTO_INCREMENT = 0
  DEFAULT CHARSET = utf8
  COMMENT = '百人牛牛在线日志';
##endregion
