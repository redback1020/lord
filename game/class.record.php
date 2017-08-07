<?php
/**
* PHP 记录类，记录：货币、操作、流程 等
* 载入类->识别类型->处理逻辑->制作记录->写入数据
*
* 货币统计
* 0、货币种类：1乐币 2乐券 3乐豆 //4乐钻(代币)
* 1、货币投放：01后台添加、02充值乐币、03币豆加豆、04 SDK买豆、05券豆加豆、06新手乐豆、07每日签到、08领取救济、09免费抽奖、10微信签到、
*             11激活礼包、12参与活动、13牌局任务、14固定任务、15动态任务、16使用道具、17领取邮件、18领取俸禄、19竞技取消、20竞技场奖、
*             21竞技周奖、22赌一中奖、23赌二中奖、等等
* 2、货币回收：51后台扣除、52牌局抽水、53币豆减币、54币买道具、55券豆减券、56券买道具、57券换实物、58豆买道具、59竞技报名、60赌一投币、
*             61赌二投币、等等
* 3、输赢转移：91牌局赢豆、92牌局输豆、
* 4、货币记录：lord_record_money_20160225，记录用户的任意货币变化时的情况，只保留3个月
* 5、记录结构：dateid    hourid    typeid    moneyid    moneynum    moneynow    uid    channelid    roomId    tableId    exta    extb    extc    extd    exte    tmcr
*             日期ID    小时ID    类型ID     货币ID      货币数∓      当前数    用户ID    渠道ID      房间ID    '牌桌ID'   '预留A' '预留B' '预留C' '预留D' '预留E'  创建时间
* 6、货币分析：lord_analyse_money，小时统计，各种货币转移量=平均各席位的输赢绝对值、转移次数=牌局数、投放量、回收量、盈亏量=回收量*-1-投放量。每天零点统计昨天的，各种货币全库总量(持有)、货币区间人数合计、
* 7、分析结构：小时    dateid    hourid    moneyid    transfers    transtimes    outgoings    outgotimes    incomings    incomtimes    earnings    tmcr
*                    日期ID     小时      货币ID      转移量∓       转移次数      投放量∓       投放次数       回收量∓      回收次数      盈亏量∓    创建时间
*     	 	  每天    dateid    moneyid    holdings      hold0      hold1      hold2      hold3      hold4      hold5      hold6      hold7      hold8      hold9      hold10     tmcr
*     		         日期ID     货币ID  持有量  0豆/0券/0币人数 >500W/50W/500 >200/20/200 >100/10/100 >50/5/50 >20/2/20   >10W/1W/10  >5W/5K/5   >2W/2K/2   >1W/1K/1    >0/0/0    创建时间
* 8、牌桌记录：lord_record_table_20160225，记录每局的信息，各种牌组直接拼接为字符串
* 9、记录结构：dateid    hourid    roomId    tableId    rate    rake    coins    coupon    lords    joker    lord    winner1    winner2    uid0    channelid0    wins0    coins0    coupon0    hands0    cards0    uid1    channelid1    wins1    coins1    coupon1    hands1    cards1    uid2    channelid2    wins2    coins2    coupon2    hands2    cards2    start_time    end_time
*             日期ID     小时      房间ID   '牌桌ID'  倍率数   抽水数 乐豆流通数 乐券产出数  '地主牌' '赖子牌'  地主UID   赢家1UID   赢家2UID   0位UID   0位渠道ID   0位输赢数  0位乐豆数  0位乐券数  '0位手牌'  '0位剩牌'  1位UID   1位渠道ID   1位输赢数  1位乐豆数  1位乐券数  '1位手牌'  '1位剩牌'  2位UID   2位渠道ID   2位输赢数  2位乐豆数  2位乐券数  '2位手牌'  '2位剩牌'   开局时间     结束时间
*
* 操作统计
* 0、操作类型：系统操作函数、用户协议编号
* 1、操作记录：lord_record_action_20160225，记录用户关键操作、系统关键运算，只保留30天
* 2、记录结构：dateid    hourid    protocal    roomId    tableId    uid    channelid    coins    coupon    result    errors    exta    extb    extc    extd    exte    tmcr
*             日期ID    小时    '协议/函数'    房间ID    '牌桌ID'   用户ID    渠道ID    用户乐豆   用户乐券  '关键结果' '错误信息' '预留A' '预留B' '预留C' '预留D' '预留E'  创建时间
*
* 运营统计
* 待续
*
* 使用方法
* class obj {
*	public $record = null;
*	public $redis = null;
*	public $mysql = null;
* 	public function getRecord() {
*		if ( $this->record === null ) $this->record = record::inst($this->redis, $this->mysql);
*		return $this->record;
*	}
* }
* $obj = new obj;
* $record = $obj->getRecord();
* $type = '后台扣除'; $money = 'coupon'; $num = -2000;
* $res = $record->money($type, $money, $num, $uid, $user);
*
*/
class record
{
	private static $inst = null;
	public $errno = 0;
	public $error = '';
	public $errors = null;
	public $mysql = null;
	public $redis = null;
	public $sqls_num = 100;
	public $date_money = 0;
	public $hour_money = 0;
	public $sqls_money = array();
	public $date_table = 0;
	public $hour_table = 0;
	public $sqls_table = array();
	public $date_action = 0;
	public $hour_action = 0;
	public $sqls_action = array();
	public $uid = 0;
	public $user = null;
	public $channels = null;
	public $krd_user = KEY_USER_;
	public $kdb_user = 'lord_game_user';
	public $kdb_record_money_ = 'lord_record_money_';
	public $kdb_record_money_day = 'lord_record_money_day';
	public $kdb_record_money_hour = 'lord_record_money_hour';
	public $kdb_record_money_type = 'lord_record_money_type';
	public $kdb_record_table_ = 'lord_record_table_';
	public $kdb_record_table_day = 'lord_record_table_day';
	public $kdb_record_action_ = 'lord_record_action_';
	public $moneys = array('gold'=>1, 'coupon'=>2, 'coins'=>3, 'diamond'=>4);
	public $types = array(
		//低于50的为货币投放>=0 或=54
		'后台添加'=>1, '充值乐币'=>2, '币豆加豆'=>3, '币买道具'=>54, 'SDK买豆'=>4, '券豆加豆'=>5,
	 	'新手乐豆'=>6, '每日签到'=>7, '领取救济'=>8, '免费抽奖'=>9, '微信签到'=>10,
		'激活礼包'=>11, '参与活动'=>12, '牌局任务'=>13, '固定任务'=>14, '动态任务'=>15,
		'使用道具'=>16, '领取邮件'=>17, '领取俸禄'=>18, '竞技取消'=>19, '竞技场奖'=>20,
		'竞技周奖'=>21, '赌一中奖'=>22, '赌二中奖'=>23, '免责金牌'=>24, '幸运牌局'=>25,
		'新赛取消'=>26, '新赛发奖'=>27,
		//低于90的为货币回收<=0 且!=54
		'后台扣除'=>51, '牌局抽水'=>52, '币豆减币'=>53, '券豆减券'=>55,
		'券买道具'=>56, '券换实物'=>57, '豆买道具'=>58, '竞技报名'=>59, '赌一投币'=>60,
		'赌二投币'=>61, '新赛报名'=>62,
		//高于90的为牌局输赢转移
		'牌局赢豆'=>91, '牌局输豆'=>92,
	    //高于等于110百人牛牛
	    "牛牛下注"=>110,"牛牛得分"=>111,"牛牛抽水"=>112,"牛牛奖池"=>113,
		//如果这里加类型，需要moneyAnalyse里面加逻辑、需要kdb_record_money_type表加对应字段
	);
	public $rooms = array(
		1000=>'经典新手',1001=>'经典初级',1002=>'经典中级',1003=>'经典高级',1006=>'经典无限',
		1007=>'赖子新手',1008=>'赖子初级',1009=>'赖子中级',1010=>'赖子高级',1011=>'赖子无限',
		1004=>'竞技初级',
		3001=>'两千乐券场',3002=>'五千乐券场',3003=>'十万乐券场',
		3011=>'咪咕热身赛',3012=>'咪咕大师赛',3013=>'咪咕总决赛',
	);
    public $rooms_rate = array(
        1000=>240,1001=>600,1002=>900,1003=>1200,1006=>0,
        1007=>480,1008=>900,1009=>1200,1010=>0,1011=>0,
        1004=>0,
        3001=>0,3002=>0,3003=>0,
        3011=>0,3012=>0,3013=>0,
    );
    //私有构造
	private function __construct( $redis=null, $mysql=null )
	{
		$this->date_money = $this->date_table = $this->date_action = intval(date("Ymd"));
		$this->hour_money = $this->hour_table = $this->hour_action = intval(date("G"));
		$this->redis = $redis; if ( $this->redis === null ) $this->getRedis();
		$this->mysql = $mysql; if ( $this->mysql === null ) $this->getMysql();
		$this->errors = array(
			'funcname' => array(
				1 => '',
			),
		);
	}

	//析构
	private function __destruct()
	{
		return true;
	}

	//析构强制
	public function close()
	{
		return $this->insert();
	}

	//覆盖克隆
	private function __clone()
	{
		return serr("本类禁止克隆 class=".__CLASS__);
	}

	//获取单例
	public static function inst( $redis=null, $mysql=null )
	{
		if( !(self::$inst instanceof self) ) self::$inst = new record($redis, $mysql);
		return self::$inst;
	}

	//连接Mysql
	public function getMysql()
	{
		if ( $this->mysql === null ) $this->mysql = new DB;
		return $this->mysql;
	}

	//连接Redis
	public function getRedis()
	{
		if ( $this->redis === null ) $this->redis = new RD;
		return $this->redis;
	}

	//获取错误
	//return 		array('errno'=>1,'error'=>'错误信息','func'=>'setMine');
	public function getError()
	{
		if ( !$this->errno || !$this->error ) return array('errno'=>0, 'error'=>'没有错误。');
		return array('errno'=>$this->errno, 'error'=>$this->error);
	}

	//设置错误
	//errno 		错误编号
	//p?			附加参数
	//return 		false //可以直接使用“return $this->setError(__FUNCTION__, ?);”来结束运算并返回false，调用方通过“$class->getError()”可以获取到错误信息数组
	public function setError( $func, $errno, $p1='', $p2='', $p3='' )
	{
		if ( !$func || !$errno ) return false;
		if ( !isset($this->errors[$func][$errno]) ) serr("报错配置无效 class=".__CLASS__." func=$func errno=$errno p1=$p1 p2=$p2 p3=$p3");
		$this->errno = $errno;
		$this->error = sprintf($this->errors[$func][$errno], $func, $p1, $p2, $p3);
		return false;
	}

	//随时随地想插入就强制插入
	public function insert()
	{
		$this->moneyInsert();
		$this->tableInsert();
		$this->actionInsert();
		return true;
	}

	//获取渠道ID
	//channel 	str 	渠道英文标记
	//return 	int 	渠道数字ID, 默认0
	public function getChannelid( $channel )
	{
		if ( ! $channel ) return 0;
		if ( $this->channels === null ) {
			$list = $this->mysql->getData("SELECT * FROM `lord_game_channel`");
			if ( $list ) {
				$channels = array();
				foreach ( $list as $key => $val )
				{
					$channels[$val['channel']] = intval($val['id']);
				}
				$this->channels = $channels;
			} else {
				$sql = "CREATE TABLE IF NOT EXISTS `lord_game_channel` (
					`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
					`channel` varchar(16) NOT NULL DEFAULT '' COMMENT '渠道',
					`is_del` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '0正常1已删',
					`tmcr` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
					PRIMARY KEY (`id`),
					INDEX `s0` (`channel`)
				) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='游戏渠道表' AUTO_INCREMENT=1";
				$res = $this->mysql->runSql($sql);
				if ( $res ) {
					$this->channels = array();
				} else {
					gerr("MYSQL->runSql=$sql");
					return 0;
				}
			}
		}
		if ( isset($this->channels[$channel]) ) return $this->channels[$channel];
		$sql = "SELECT `id` FROM `lord_game_channel` WHERE `channel` = '$channel'";
		$id = $this->mysql->getVar($sql);
		if ( $id ) {
			$this->channels[$channel] = $id;
			return $id;
		}
		$lock = 'NEWCHANNEL_'.$channel;
		if ( ! $this->getRedis()->setLock($lock, 1) ) {
			$sql = "SELECT `id` FROM `lord_game_channel` WHERE `channel` = '$channel'";
			$times = 20;
			$id = 0;
			while ( $times && !($id = $this->mysql->getVar($sql)) )
			{
				usleep(200000);
				$times--;
			}
			if ( $id ) {
				$this->channels[$channel] = $id;
				return $id;
			} else {
				return 0;
			}
		}
		$sql = "INSERT INTO `lord_game_channel` (`channel`, `is_del`, `tmcr`) VALUES ('$channel', 0, ".time().")";
		$res = $this->mysql->runSql($sql);
		if ( $res ) {
			$id = $this->mysql->lastId();
			if ( $id ) {
				$this->channels[$channel] = $id;
				$this->getRedis()->delLock($lock);
				return $id;
			} else {
				gerr("MYSQL->lastId=$sql");
			}
		} else {
			gerr("MYSQL->runSql=$sql");
		}
		$this->getRedis()->delLock($lock);
		return 0;
	}

	//设置UID
	public function setUid( $ud )
	{
		$ud = intval($ud);
		if ( $ud < 1 ) return false;
		$this->uid = $ud;
		if ( $this->user && $this->user['uid'] != $ud ) $this->user = null;
		return true;
	}

	//设置USER
	public function setUser( $U )
	{
		if ( !is_array($U) || !isset($U['uid']) || count($U) < 10 ) {
			$this->uid = 0;
			$this->user = null;
			return false;
		}
		$U['source'] = isset($U['isMysql']) && $U['isMysql'] ? 'mysql' : 'redis';
		$this->uid = $U['uid'];
		$this->user = $U;
	}

	//获取UID
	public function getUid()
	{
		return $this->uid;
	}

	//获取USER
	//ud 		int 	=0获取当前user >0获取某个user
	//must		int 	是否从数据库内强制获取
	public function getUser( $ud=0, $must=0 )
	{
		if ( ! $ud ) return $this->user ? $this->user : array();
		if ( $ud == $this->uid && $this->user ) return $this->user;
		$user = $this->getRedis()->hgetall($this->krd_user.$ud);
		if ( $user && count($user) > 10 ) $user['source'] = 'redis';
		else $user = array();
		if ( ! $user && $must && ($user = $this->mysql->getLine("SELECT * FROM `".$this->kdb_user."` WHERE `uid` = $ud")) ) $user['source'] = 'mysql';
		if ( $user && $user['source'] == 'mysql' ) $user = array_merge($user, array('fd'=>'', 'roomId'=>0, 'tableId'=>''));
		if ( $user ) $this->setUser($user);
		return $user ? $user : array();
	}

	//记录货币历史
	//type 		str 	流通类型中文
	//money		str 	货币字段英文
	//moneynum 	int 	货币变更数量
	//uid 		int 	用户ID
	//user 		arr 	用户数据
	//return 	bool
	public function money( $type, $money, $moneynum, $uid, $user=array() )
	{
		$dateid = intval(date("Ymd"));
		$hourid = intval(date("G"));
		if ( $dateid != $this->date_money || $hourid != $this->hour_money ) $this->moneyInsert();
		$uid = intval($uid);
		if ( !isset($this->types[$type]) || !isset($this->moneys[$money]) || $uid < 1 ) return false;
		$typeid = $this->types[$type];
		$moneyid = $this->moneys[$money];
		$moneynum = intval($moneynum);
		if ( ! $user ) {
			$user = $this->getUser($uid, 1);
			if ( ! $user ) return gerr("货币用户错误 U=$uid");
		}
		if ( ! isset($user['roomId']) ) $user['roomId'] = 0;
		if ( ! isset($user['tableId']) ) $user['tableId'] = '';
		$moneynow = intval($user[$money]);
		$channelid = isset($user['channel']) && $user['channel'] ? $this->getChannelid($user['channel']) : 0;
		$roomId = intval($user['roomId']);
		$tableId = $user['tableId'] ? substr_replace($user['tableId'], '', 0, 5) : '';
		$tmcr = time();
		$this->sqls_money[]= "($dateid, $hourid, $typeid, $moneyid, $moneynum, $moneynow, $uid, $channelid, $roomId, '$tableId', '', '', '', '', '', $tmcr)";
		if ( count($this->sqls_money) > $this->sqls_num ) $this->moneyInsert();
		return true;
	}

	//插入货币记录
	//dateid 	int 	日期ID
	//return 	bool
	private function moneyInsert()
	{
		if ( ! $this->sqls_money || ! $this->date_money ) return false;
		$sql = "INSERT INTO `{$this->kdb_record_money_}{$this->date_money}` (`dateid`, `hourid`, `typeid`, `moneyid`, `moneynum`, `moneynow`, `uid`, `channelid`, `roomId`, `tableId`, `exta`, `extb`, `extc`, `extd`, `exte`, `tmcr`) VALUES ".join(', ', $this->sqls_money);
		$res = $this->mysql->runSql($sql);
		if ( ! $res ) {
			$res = $this->moneyCreate($this->date_money);
			if ( !$res ) return false;
			$res = $this->mysql->runSql($sql);
			if ( !$res ) return gerr("MYSQL->runSql=$sql");
		}
		$this->date_money = intval(date("Ymd"));
		$this->hour_money = intval(date("G"));
		$this->sqls_money = array();
		return true;
	}

	//建表货币记录
	//dateid 	int 	日期ID
	//return 	bool
	private function moneyCreate( $dateid )
	{
		$ddl = "CREATE TABLE IF NOT EXISTS `{$this->kdb_record_money_}{$dateid}` (
			`id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
			`dateid` int(8) unsigned NOT NULL DEFAULT '0' COMMENT '日期ID',
			`hourid` tinyint(2) unsigned NOT NULL DEFAULT '0' COMMENT '小时ID',
			`typeid` tinyint(2) unsigned NOT NULL DEFAULT '0' COMMENT '类型ID',
			`moneyid` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '货币ID',
			`moneynum` int(10) NOT NULL DEFAULT '0' COMMENT '货币数∓',
			`moneynow` int(10) NOT NULL DEFAULT '0' COMMENT '当前货币',
			`uid` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '用户ID',
			`channelid` int(4) unsigned NOT NULL DEFAULT '0' COMMENT '渠道ID',
			`roomId` int(5) unsigned NOT NULL DEFAULT '0' COMMENT '房间ID',
			`tableId` varchar(32) NULL DEFAULT '' COMMENT '牌桌ID',
			`exta` varchar(32) NULL DEFAULT '' COMMENT '预留A',
			`extb` varchar(32) NULL DEFAULT '' COMMENT '预留B',
			`extc` varchar(32) NULL DEFAULT '' COMMENT '预留C',
			`extd` varchar(32) NULL DEFAULT '' COMMENT '预留D',
			`exte` varchar(32) NULL DEFAULT '' COMMENT '预留E',
			`tmcr` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
			PRIMARY KEY (`id`),
			INDEX `s0` (`dateid`,`hourid`,`typeid`,`moneyid`,`roomId`,`channelid`,`uid`),
			INDEX `s1` (`typeid`,`moneyid`,`roomId`,`uid`),
			INDEX `s2` (`uid`,`typeid`,`moneyid`,`roomId`),
			INDEX `s3` (`channelid`,`uid`),
			INDEX `s4` (`dateid`,`moneyid`,`typeid`)
		) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='货币记录日换表名_20160225' AUTO_INCREMENT=1";
		$res = $this->mysql->runSql($ddl);
		if ( !$res ) return gerr("MYSQL->runSql=$ddl");
		return true;
	}

	//分析货币记录
	//dateid 	int 	日期ID
	//hourid 	int 	小时ID
	//return 	bool
	public function moneyAnalyse( $dateid, $hourid )
	{
		$dateid = intval($dateid);
		$hourid = intval($hourid);
		if ( $dateid < 1 || $hourid < 0 ) return false;
		$lastid = $hourid ? 0 : intval(date("Ymd", strtotime(substr_replace(substr_replace($dateid, '-', 4, 0), '-', 7, 0))-86400));
		if ( $dateid != $this->date_money || $hourid != $this->hour_money ) $this->moneyInsert();
		$conds = array(
			'gold'=>array('`gold`=0', '`gold`>=500', '`gold`>=200 AND `gold`<500', '`gold`>=100 AND `gold`<200',
				'`gold`>=50 AND `gold`<100', '`gold`>=20 AND `gold`<50', '`gold`>=10 AND `gold`<20',
				'`gold`>=5 AND `gold`<10', '`gold`>=2 AND `gold`<5', '`gold`>=1 AND `gold`<2',
				'`gold`>0 AND `gold`<1'),
			'coins'=>array('`coins`=0', '`coins`>=5000000', '`coins`>=2000000 AND `coins`<5000000', '`coins`>=1000000 AND `coins`<2000000',
				'`coins`>=500000 AND `coins`<1000000', '`coins`>=200000 AND `coins`<500000', '`coins`>=100000 AND `coins`<200000',
				'`coins`>=50000 AND `coins`<100000', '`coins`>=20000 AND `coins`<50000', '`coins`>=10000 AND `coins`<20000',
				'`coins`>0 AND `coins`<10000'),
			'coupon'=>array('`coupon`=0', '`coupon`>=50000', '`coupon`>=20000 AND `coupon`<50000', '`coupon`>=10000 AND `coupon`<20000',
				'`coupon`>=5000 AND `coupon`<10000', '`coupon`>=2000 AND `coupon`<5000', '`coupon`>=1000 AND `coupon`<2000',
				'`coupon`>=500 AND `coupon`<1000', '`coupon`>=200 AND `coupon`<500', '`coupon`>=100 AND `coupon`<200',
				'`coupon`>0 AND `coupon`<100'),
		);
		$typs = array_flip($this->types);
		foreach ( $this->moneys as $money => $moneyid )
		{
			if ( !isset($conds[$money]) ) continue;
			$sql = "SELECT sum(`moneynum`) FROM `{$this->kdb_record_money_}{$dateid}` WHERE `dateid` = $dateid AND `hourid` = $hourid AND `typeid` = 91 AND `moneyid` = $moneyid";
			$transfers  = $this->mysql->getVar($sql); if ( ! $transfers ) $transfers = 0;
			$sql = "SELECT count(`id`) FROM `{$this->kdb_record_money_}{$dateid}` WHERE `dateid` = $dateid AND `hourid` = $hourid AND `typeid`<110 AND `typeid` > 90 AND `moneyid` = $moneyid";
			$transtimes = intval($this->mysql->getVar($sql) / 3); if ( ! $transtimes ) $transtimes = 0;
			$sql = "SELECT sum(`moneynum`) FROM `{$this->kdb_record_money_}{$dateid}` WHERE `dateid` = $dateid AND `hourid` = $hourid AND (`typeid` < 50 OR `typeid` = 54) AND `moneyid` = $moneyid";
			$outgoings  = $this->mysql->getVar($sql); if ( ! $outgoings ) $outgoings = 0;
			$sql = "SELECT count(`id`) FROM `{$this->kdb_record_money_}{$dateid}` WHERE `dateid` = $dateid AND `hourid` = $hourid AND (`typeid` < 50 OR `typeid` = 54) AND `moneyid` = $moneyid";
			$outgotimes = $this->mysql->getVar($sql); if ( ! $outgotimes ) $outgotimes = 0;
			$sql = "SELECT sum(`moneynum`) FROM `{$this->kdb_record_money_}{$dateid}` WHERE `dateid` = $dateid AND `hourid` = $hourid AND (`typeid` > 50 AND `typeid` != 54) AND `typeid` < 90 AND `moneyid` = $moneyid";
			$incomings  = $this->mysql->getVar($sql); if ( ! $incomings ) $incomings = 0;
			$sql = "SELECT count(`id`) FROM `{$this->kdb_record_money_}{$dateid}` WHERE `dateid` = $dateid AND `hourid` = $hourid AND (`typeid` > 50 AND `typeid` != 54) AND `typeid` < 90 AND `moneyid` = $moneyid";
			$incomtimes = $this->mysql->getVar($sql); if ( ! $incomtimes ) $incomtimes = 0;
			$earnings = $incomings - $outgoings;
			$sql = "INSERT INTO `{$this->kdb_record_money_hour}` (`dateid`, `hourid`, `moneyid`, `transfers`, `transtimes`, `outgoings`, `outgotimes`, `incomings`, `incomtimes`, `earnings`, `tmcr`)"
				. " VALUES ($dateid, $hourid, $moneyid, $transfers, $transtimes, $outgoings, $outgotimes, $incomings, $incomtimes, $earnings, ".time().")";
			$res = $this->mysql->runSql($sql);
			if ( !$res ) {
				$ddl = "CREATE TABLE IF NOT EXISTS `{$this->kdb_record_money_hour}` (
					`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
					`dateid` int(8) unsigned NOT NULL DEFAULT '0' COMMENT '日期ID',
					`hourid` tinyint(2) unsigned NOT NULL DEFAULT '0' COMMENT '小时ID',
					`moneyid` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '货币ID',
					`transfers` bigint(20) NOT NULL DEFAULT '0' COMMENT '转移量∓',
					`transtimes` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '转移次数',
					`outgoings` bigint(20) NOT NULL DEFAULT '0' COMMENT '投放量∓',
					`outgotimes` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '投放次数',
					`incomings` bigint(20) NOT NULL DEFAULT '0' COMMENT '回收量∓',
					`incomtimes` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '回收次数',
					`earnings` bigint(20) NOT NULL DEFAULT '0' COMMENT '盈亏量∓',
					`tmcr` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
					PRIMARY KEY (`id`),
					UNIQUE KEY `s0` (`dateid`,`hourid`,`moneyid`)
				) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='每时货币纪录分析' AUTO_INCREMENT=1";
				$res = $this->mysql->runSql($ddl);
				if ( !$res ) gerr("MYSQL->runSql=$ddl");
				$res = $this->mysql->runSql($sql);
				if ( !$res ) gerr("MYSQL->runSql=$sql");
			}
			if ( !$lastid ) continue;
			//每天零点统计昨天的，全天的各种总量、各种货币全库总量(持有)、货币区间人数合计、
			// dateid    moneyid    小时统计里的各个项总计    holdings      hold0      hold1      hold2      hold3      hold4      hold5      hold6      hold7      hold8      hold9      hold10     tmcr
			// 日期ID     货币ID    小时统计里的各个项总计    持有量  0豆/0券/0币人数 >500W/5W/500 >200/2/200 >100/1/100 >50W/5K/50 >20/2/20 >10W/1K/10  >5W/500/5  >2W/200/2   >1W/100/1   >0/0/0    创建时间
			$sql = "SELECT sum(`transfers`) FROM `{$this->kdb_record_money_hour}` WHERE `dateid` = $lastid AND `moneyid` = $moneyid";
			$transfers  = $this->mysql->getVar($sql); if ( ! $transfers ) $transfers = 0;
			$sql = "SELECT sum(`transtimes`) FROM `{$this->kdb_record_money_hour}` WHERE `dateid` = $lastid AND `moneyid` = $moneyid";
			$transtimes = $this->mysql->getVar($sql); if ( ! $transtimes ) $transtimes = 0;
			$sql = "SELECT sum(`outgoings`) FROM `{$this->kdb_record_money_hour}` WHERE `dateid` = $lastid AND `moneyid` = $moneyid";
			$outgoings  = $this->mysql->getVar($sql); if ( ! $outgoings ) $outgoings = 0;
			$sql = "SELECT sum(`outgotimes`) FROM `{$this->kdb_record_money_hour}` WHERE `dateid` = $lastid AND `moneyid` = $moneyid";
			$outgotimes = $this->mysql->getVar($sql); if ( ! $outgotimes ) $outgotimes = 0;
			$sql = "SELECT sum(`incomings`) FROM `{$this->kdb_record_money_hour}` WHERE `dateid` = $lastid AND `moneyid` = $moneyid";
			$incomings  = $this->mysql->getVar($sql); if ( ! $incomings ) $incomings = 0;
			$sql = "SELECT sum(`incomtimes`) FROM `{$this->kdb_record_money_hour}` WHERE `dateid` = $lastid AND `moneyid` = $moneyid";
			$incomtimes = $this->mysql->getVar($sql); if ( ! $incomtimes ) $incomtimes = 0;
			$sql = "SELECT sum(`earnings`) FROM `{$this->kdb_record_money_hour}` WHERE `dateid` = $lastid AND `moneyid` = $moneyid";
			$earnings   = $this->mysql->getVar($sql); if ( ! $earnings ) $earnings = 0;
			$sql = "SELECT sum(`$money`) FROM `$this->kdb_user`";
			$holdings   = $this->mysql->getVar($sql);
			$hold0 = $hold1 = $hold2 = $hold3 = $hold4 = $hold5 = $hold6 = $hold7 = $hold8 = $hold9 = $hold10 = 0;
			foreach ( $conds[$money] as $i => $cond )
			{
				$sql = "SELECT count(uid) FROM `$this->kdb_user` WHERE $cond";
				${"hold{$i}"} = $this->mysql->getVar($sql);
			}
			$sql = "INSERT INTO `{$this->kdb_record_money_day}` (`dateid`, `moneyid`, `transfers`, `transtimes`, `outgoings`, `outgotimes`,"
				." `incomings`, `incomtimes`, `earnings`, `holdings`, `hold0`, `hold1`, `hold2`, `hold3`, `hold4`, `hold5`, `hold6`,"
				." `hold7`, `hold8`, `hold9`, `hold10`, `tmcr`) VALUES ($lastid, $moneyid, $transfers, $transtimes, $outgoings, $outgotimes,"
				." $incomings, $incomtimes, $earnings, $holdings, $hold0, $hold1, $hold2, $hold3, $hold4, $hold5, $hold6,"
				." $hold7, $hold8, $hold9, $hold10, ".time().")";
			$res = $this->mysql->runSql($sql);
			if ( ! $res ) {
				$ddl = "CREATE TABLE IF NOT EXISTS `{$this->kdb_record_money_day}` (
					`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
					`dateid` int(8) unsigned NOT NULL DEFAULT '0' COMMENT '日期ID',
					`moneyid` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '货币ID',
					`transfers` bigint(20) NOT NULL DEFAULT '0' COMMENT '转移量∓',
					`transtimes` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '转移次数',
					`outgoings` bigint(20) NOT NULL DEFAULT '0' COMMENT '投放量∓',
					`outgotimes` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '投放次数',
					`incomings` bigint(20) NOT NULL DEFAULT '0' COMMENT '回收量∓',
					`incomtimes` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '回收次数',
					`earnings` bigint(20) NOT NULL DEFAULT '0' COMMENT '盈亏量∓',
					`holdings` bigint(20) unsigned NOT NULL DEFAULT '0' COMMENT '持有量',
					`hold0` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '0豆/0券/0币人数',
					`hold1` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '>=500W/5W/500',
					`hold2` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '>=200W/2W/200',
					`hold3` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '>=100W/1W/100',
					`hold4` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '>=50W/5K/50',
					`hold5` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '>=20W/2K/20',
					`hold6` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '>=10W/1K/10',
					`hold7` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '>=5W/500/5',
					`hold8` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '>=2W/200/2',
					`hold9` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '>=1W/100/1',
					`hold10` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '>0/0/0',
					`tmcr` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
					PRIMARY KEY (`id`),
					UNIQUE KEY `s0` (`dateid`,`moneyid`)
				) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='每天货币纪录分析' AUTO_INCREMENT=1";
				$res = $this->mysql->runSql($ddl);
				if ( !$res ) gerr("MYSQL->runSql=$ddl");
				$res = $this->mysql->runSql($sql);
				if ( !$res ) gerr("MYSQL->runSql=$sql");
			}
		}
		if ( ! $lastid ) return true;
		$this->moneyAnalyseType($lastid);
		return true;
	}

	//分析货币记录
	//dateid 	int 	日期ID
	//hourid 	int 	小时ID
	//return 	bool
	//每天零点统计昨天的，全天的各类型的流通总量、流通次数
	// dateid    moneyid        t1s        t1c       ...       t92s        t92c       tmcr
	// 日期ID     货币ID    类型1合计    类型1次数                                    创建时间
	public function moneyAnalyseType( $dateid )
	{
		$dateid = intval($dateid);
		if ( ! $dateid ) return false;
		$conds = array( 'gold'=>array(), 'coupon'=>array(), 'coins'=>array() );
		$typs = array_flip($this->types);
		foreach ( $this->moneys as $money => $moneyid )
		{
			if ( !isset($conds[$money]) ) continue;
			$kv = array();
			foreach ( $typs as $typeid => $type )
			{
				$sql = "SELECT sum(`moneynum`) s, count(`moneynum`) c FROM `{$this->kdb_record_money_}{$dateid}` WHERE `dateid` = $dateid AND `moneyid` = $moneyid AND `typeid` = $typeid";
				$ret = $this->mysql->getLine($sql); if ( ! $ret ) $ret = array('s'=>0, 'c'=>0);
				if ( ! $ret['s'] ) $ret['s'] = 0;
				if ( ! $ret['c'] ) $ret['c'] = 0;
				$kv[$typeid] = $ret;
				$kv[$typeid]["n"] = $type;
			}
			$sql = "INSERT INTO `{$this->kdb_record_money_type}` (`dateid`, `moneyid`, ";
			foreach ( $kv as $k => $v )
			{
				$sql.= "`t{$k}s`, `t{$k}c`, ";
			}
			$sql.= "`tmcr`) VALUES ($dateid, $moneyid,";
			foreach ( $kv as $k => $v )
			{
				$sql.= $v['s'].", ".$v['c'].", ";
			}
			$sql.= time().")";
			$res = $this->mysql->runSql($sql);
			if ( ! $res ) {
				$ddl = "CREATE TABLE IF NOT EXISTS `{$this->kdb_record_money_type}` (
					`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
					`dateid` int(8) unsigned NOT NULL DEFAULT '0' COMMENT '日期ID',
					`moneyid` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '货币ID',";
				foreach ( $kv as $k => $v )
				{
					$ddl.= "t{$k}s bigint(20) unsigned NOT NULL DEFAULT '0' COMMENT '".$v['n']."合计',";
					$ddl.= "t{$k}c int(10) unsigned NOT NULL DEFAULT '0' COMMENT '".$v['n']."次数',";
				}
				$ddl.= "`tmcr` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
					PRIMARY KEY (`id`),
					UNIQUE KEY `s0` (`dateid`,`moneyid`)
				) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='每天货币类型分析' AUTO_INCREMENT=1";
				$res = $this->mysql->runSql($ddl);
				if ( !$res ) gerr("MYSQL->runSql=$ddl");
				$res = $this->mysql->runSql($sql);
				if ( !$res ) gerr("MYSQL->runSql=$sql");
			}
		}
		return true;
	}

	//记录牌局历史
	//table 	arr 	牌局数据
	//return 	bool
	public function table( $table )
	{
		$dateid = intval(date("Ymd"));
		$hourid = intval(date("G"));
		if ( $dateid != $this->date_table || $hourid != $this->hour_table ) $this->tableInsert();
		$roomId = intval($table['roomId']);
		$tableId = substr_replace($table['tableId'], '', 0, 5);
		if ( ! $roomId || ! $tableId ) return gerr("牌桌数据错误 ".json_encode($table));
		$rate = intval($table['rate']);
		$rake = array_sum($table['seat_rakes']);
		$coins = intval((abs($table['seat_total']['0']) + abs($table['seat_total']['1']) + abs($table['seat_total']['2']))/3);//后续要考虑改为按赢钱为流通数
		$coupon = intval($table['seat0ttcoupon']+$table['seat1ttcoupon']+$table['seat2ttcoupon']);
		$lords = join('', $table['lordCards']);
		$joker = $table['joker'];
		$lord = $table["seat".$table['lordSeat']."uid"];
		$winner1 = $table["seat".$table['lastCall']."uid"];
		$winner2 = 0;
		foreach ( $table['seat_winer'] as $sid => $iswin ) {
			if ( $iswin && $sid != $table['lastCall'] ) {
				$winner2 = $table["seat{$sid}uid"];
				break;
			}
		}
		$create = intval($table['create']);
		$starte = intval($table['starte']);
		$finish = intval($table['finish']);
		$uid0 = $table['seat0uid'];
		$channelid0 = $this->getChannelid($table['seat0channel']);
		$coins0 = $table['seat_total']['0'];
		$coupon0 = $table['seat0ttcoupon'];
		$hands0 = join('', $table['seat0hands']);
		$cards0 = join('', $table['seat0cards']);
		$uid1 = $table['seat1uid'];
		$channelid1 = $this->getChannelid($table['seat1channel']);
		$coins1 = $table['seat_total']['1'];
		$coupon1 = $table['seat1ttcoupon'];
		$hands1 = join('', $table['seat1hands']);
		$cards1 = join('', $table['seat1cards']);
		$uid2 = $table['seat2uid'];
		$channelid2 = $this->getChannelid($table['seat2channel']);
		$coins2 = $table['seat_total']['2'];
		$coupon2 = $table['seat2ttcoupon'];
		$hands2 = join('', $table['seat2hands']);
		$cards2 = join('', $table['seat2cards']);
		$tmcr = time();
        $this->sqls_table[]= "($dateid, $hourid, $roomId, '$tableId', $rate, $rake, $coins, $coupon, '$lords', '$joker', $lord, $winner1, $winner2, $create, $starte, $finish, $uid0, $channelid0, $coins0, $coupon0, '$hands0', '$cards0', $uid1, $channelid1, $coins1, $coupon1, '$hands1', '$cards1', $uid2, $channelid2, $coins2, $coupon2, '$hands2', '$cards2', '', '', '', '', '', $tmcr)";
		if ( count($this->sqls_table) > $this->sqls_num ) $this->tableInsert();
		return true;
	}

	//插入牌局记录
	//dateid 	int 	日期ID
	//return 	bool
	public function tableInsert()
	{
		if ( ! $this->sqls_table || ! $this->date_table ) return false;
		$sql = "INSERT INTO `{$this->kdb_record_table_}{$this->date_table}` (`dateid`, `hourid`, `roomId`, `tableId`, `rate`, `rake`, `coins`, `coupon`, `lords`, `joker`, `lord`, `winner1`, `winner2`, `create`, `starte`, `finish`, `uid0`, `channelid0`, `wcoins0`, `tcoupon0`, `hands0`, `cards0`, `uid1`, `channelid1`, `wcoins1`, `tcoupon1`, `hands1`, `cards1`, `uid2`, `channelid2`, `wcoins2`, `tcoupon2`, `hands2`, `cards2`, `exta`, `extb`, `extc`, `extd`, `exte`, `tmcr` ) VALUES ".join(', ', $this->sqls_table);
		$res = $this->mysql->runSql($sql);
		if ( ! $res ) {
			$res = $this->tableCreate($this->date_table);
			if ( !$res ) return false;
			$res = $this->mysql->runSql($sql);
			if ( !$res ) return gerr("MYSQL->runSql=$sql");
		}
		$this->date_table = intval(date("Ymd"));
		$this->hour_table = intval(date("G"));
		$this->sqls_table = array();
		return true;

	}

	//建表牌桌记录
	//dateid 	int 	日期ID
	//return 	bool
	private function tableCreate( $dateid )
	{
		$ddl = "CREATE TABLE IF NOT EXISTS `{$this->kdb_record_table_}{$dateid}` (
			`id` bigint(20) unsigned NOT NULL AUTO_INCREMENT COMMENT '牌局ID',
			`dateid` int(8) unsigned NOT NULL DEFAULT '0' COMMENT '日期ID',
			`hourid` tinyint(2) unsigned NOT NULL DEFAULT '0' COMMENT '小时ID',
			`roomId` int(5) unsigned NOT NULL DEFAULT '0' COMMENT '房间ID',
			`tableId` varchar(32) NULL DEFAULT '' COMMENT '牌桌ID',
			`rate` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '倍率数',
			`rake` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '实际抽水总数',
			`coins` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '乐豆流通数',
			`coupon` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '乐券产出总数',
			`lords` varchar(6) NULL DEFAULT '' COMMENT '地主牌',
			`joker` varchar(1) NULL DEFAULT '' COMMENT '赖子牌',
			`lord` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '地主UID',
			`winner1` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '赢家1UID',
			`winner2` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '赢家2UID',
			`create` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '建桌时间',
			`starte` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '开局时间',
			`finish` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '结束时间',
			`uid0` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '0位UID',
			`channelid0` int(4) unsigned NOT NULL DEFAULT '0' COMMENT '0位渠道ID',
			`wcoins0` int(10) NOT NULL DEFAULT '0' COMMENT '0位乐豆数∓',
			`tcoupon0` int(10) NOT NULL DEFAULT '0' COMMENT '0位乐券数∓',
			`hands0` varchar(40) NULL DEFAULT '' COMMENT '0位手牌',
			`cards0` varchar(40) NULL DEFAULT '' COMMENT '0位剩牌',
			`uid1` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '1位UID',
			`channelid1` int(4) unsigned NOT NULL DEFAULT '0' COMMENT '1位渠道ID',
			`wcoins1` int(10) NOT NULL DEFAULT '0' COMMENT '1位乐豆数∓',
			`tcoupon1` int(10) NOT NULL DEFAULT '0' COMMENT '1位乐券数∓',
			`hands1` varchar(40) NULL DEFAULT '' COMMENT '1位手牌',
			`cards1` varchar(40) NULL DEFAULT '' COMMENT '1位剩牌',
			`uid2` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '2位UID',
			`channelid2` int(4) unsigned NOT NULL DEFAULT '0' COMMENT '2位渠道ID',
			`wcoins2` int(10) NOT NULL DEFAULT '0' COMMENT '2位乐豆数∓',
			`tcoupon2` int(10) NOT NULL DEFAULT '0' COMMENT '2位乐券数∓',
			`hands2` varchar(40) NULL DEFAULT '' COMMENT '2位手牌',
			`cards2` varchar(40) NULL DEFAULT '' COMMENT '2位剩牌',
			`exta` varchar(32) NULL DEFAULT '' COMMENT '预留A',
			`extb` varchar(32) NULL DEFAULT '' COMMENT '预留B',
			`extc` varchar(32) NULL DEFAULT '' COMMENT '预留C',
			`extd` varchar(32) NULL DEFAULT '' COMMENT '预留D',
			`exte` varchar(32) NULL DEFAULT '' COMMENT '预留E',
			`tmcr` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
			PRIMARY KEY (`id`),
			INDEX `s0` (`dateid`,`hourid`,`roomId`),
			INDEX `s1` (`dateid`,`roomId`,`rate`),
			INDEX `s2` (`dateid`,`roomId`,`rake`),
			INDEX `s3` (`dateid`,`roomId`,`coins`),
			INDEX `s4` (`dateid`,`roomId`,`coupon`),
			INDEX `s5` (`channelid0`),
			INDEX `s6` (`channelid1`),
			INDEX `s7` (`channelid2`),
			INDEX `s8` (`uid0`),
			INDEX `s9` (`uid1`),
			INDEX `s10` (`uid2`),
			INDEX `s11` (`lord`,`winner1`,`winner2`)
		) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='牌局记录日换表名_20160225' AUTO_INCREMENT=1";
		$res = $this->mysql->runSql($ddl);
		if ( !$res ) return gerr("MYSQL->runSql=$ddl");
		return true;
	}

	// 每天零点统计一下牌桌
	public function tableAnalyse( $dateid )
	{
		$dateid = intval($dateid);
		if ( $dateid < 1 ) return false;
		$lastid = intval(date("Ymd", strtotime(substr_replace(substr_replace($dateid, '-', 4, 0), '-', 7, 0))-86400));
		$rooms = include("conf/rooms.php");
		foreach ( $rooms as $roomId => $config )
		{
			$sql = "SELECT COUNT(`id`) FROM `{$this->kdb_record_table_}{$lastid}` WHERE `dateid` = $lastid AND `roomId` = $roomId";
			$games = intval($this->mysql->getVar($sql));
			$sql = "SELECT SUM(`coins`) FROM `{$this->kdb_record_table_}{$lastid}` WHERE `dateid` = $lastid AND `roomId` = $roomId";
			$transfers = ceil($this->mysql->getVar($sql)*3/2);//后续要跟着record_table表的计算方式而改动
			$sql = "SELECT SUM(`rake`) FROM `{$this->kdb_record_table_}{$lastid}` WHERE `dateid` = $lastid AND `roomId` = $roomId";
			$rakes = ceil($this->mysql->getVar($sql));
			$sql = "SELECT SUM(`coupon`) FROM `{$this->kdb_record_table_}{$lastid}` WHERE `dateid` = $lastid AND `roomId` = $roomId";
			$coupons = ceil($this->mysql->getVar($sql));
			$sql = "SELECT SUM(`rate`) FROM `{$this->kdb_record_table_}{$lastid}` WHERE `dateid` = $lastid AND `roomId` = $roomId AND `rate`<={$config['rateMax']}";
			$rates = ceil($this->mysql->getVar($sql));
			$sql = "SELECT COUNT(`id`) FROM `{$this->kdb_record_table_}{$lastid}` WHERE `dateid` = $lastid AND `roomId` = $roomId AND `rate`>{$config['rateMax']}";
			$rates += intval($this->mysql->getVar($sql))*$config["rateMax"];
			$rate_avg = round($rates/$games);
			$sql = "SELECT SUM(`rate`) FROM `{$this->kdb_record_table_}{$lastid}` WHERE `dateid` = $lastid AND `roomId` = $roomId";
			$rate_avg_theory = round(intval($this->mysql->getVar($sql))/$games);
			$dau = array();
			$sql = "SELECT DISTINCT `uid0` FROM `{$this->kdb_record_table_}{$lastid}` WHERE `dateid` = $lastid AND `roomId` = $roomId";
			$ret = $this->mysql->getData($sql); if ( ! $ret ) $ret = array();
			foreach ( $ret as $k => $v ) $dau[$v['uid0']] = 1;
			$sql = "SELECT DISTINCT `uid1` FROM `{$this->kdb_record_table_}{$lastid}` WHERE `dateid` = $lastid AND `roomId` = $roomId";
			$ret = $this->mysql->getData($sql); if ( ! $ret ) $ret = array();
			foreach ( $ret as $k => $v ) $dau[$v['uid1']] = 1;
			$sql = "SELECT DISTINCT `uid2` FROM `{$this->kdb_record_table_}{$lastid}` WHERE `dateid` = $lastid AND `roomId` = $roomId";
			$ret = $this->mysql->getData($sql); if ( ! $ret ) $ret = array();
			foreach ( $ret as $k => $v ) $dau[$v['uid2']] = 1;
			$dau = count($dau);
			$dnu = array();
			$sql = "SELECT DISTINCT A.`uid0` FROM `{$this->kdb_record_table_}{$lastid}` A LEFT JOIN `lord_user_task` B ON A.uid0 = B.uid  WHERE A.`dateid` = $lastid AND A.`roomId` = $roomId AND B.`dateid` = $lastid";
			$ret = $this->mysql->getData($sql); if ( ! $ret ) $ret = array();
			foreach ( $ret as $k => $v ) $dnu[$v['uid0']] = 1;
			$sql = "SELECT DISTINCT A.`uid1` FROM `{$this->kdb_record_table_}{$lastid}` A LEFT JOIN `lord_user_task` B ON A.uid1 = B.uid  WHERE A.`dateid` = $lastid AND A.`roomId` = $roomId AND B.`dateid` = $lastid";
			$ret = $this->mysql->getData($sql); if ( ! $ret ) $ret = array();
			foreach ( $ret as $k => $v ) $dnu[$v['uid1']] = 1;
			$sql = "SELECT DISTINCT A.`uid2` FROM `{$this->kdb_record_table_}{$lastid}` A LEFT JOIN `lord_user_task` B ON A.uid2 = B.uid  WHERE A.`dateid` = $lastid AND A.`roomId` = $roomId AND B.`dateid` = $lastid";
			$ret = $this->mysql->getData($sql); if ( ! $ret ) $ret = array();
			foreach ( $ret as $k => $v ) $dnu[$v['uid2']] = 1;
			$dnu = count($dnu);
			$dou = $dau - $dnu;
			$sql = "SELECT MAX(`room{$roomId}TableActive`) PCU FROM `lord_online_detail` WHERE `dateid` = $lastid";
			$pcu = intval($this->mysql->getVar($sql));
			$sql = "SELECT AVG(`room{$roomId}TableActive`) ACU FROM `lord_online_detail` WHERE `dateid` = $lastid";
			$acu = intval($this->mysql->getVar($sql));
			$sql = "INSERT INTO `{$this->kdb_record_table_day}`";
			$sql.= " (`dateid`, `roomId`, `games`, `transfers`, `rakes`, `coupons`,`rate_avg`, `dau`, `dnu`, `dou`, `pcu`, `acu`, `tmcr`)";
			$sql.= " VALUES ($lastid, $roomId, $games, $transfers, $rakes, $coupons, $rate_avg, $dau, $dnu, $dou, $pcu, $acu, UNIX_TIMESTAMP())";
			$ret = $this->mysql->runSql($sql);
			if ( ! $ret ) {
				$ddl = "CREATE TABLE IF NOT EXISTS `{$this->kdb_record_table_day}` (
					`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
					`dateid` int(8) unsigned NOT NULL DEFAULT '0' COMMENT '日期ID',
					`roomId` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '房间ID',
					`games` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '牌局数',
					`transfers` bigint(20) unsigned NOT NULL DEFAULT '0' COMMENT '流通数',
					`rakes` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '乐豆回收数',
					`coupons` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '乐券投放数',
					`rate_avg` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '实际倍率平均数',
					`rate_avg_theory` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '理论倍率平均数',
					`dau` int(10) unsigned NOT NULL DEFAULT '0' COMMENT 'DAU',
					`dnu` int(10) unsigned NOT NULL DEFAULT '0' COMMENT 'DNU',
					`dou` int(10) unsigned NOT NULL DEFAULT '0' COMMENT 'DOU',
					`pcu` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '分钟PCU',
					`acu` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '分钟ACU',
					`tmcr` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
					PRIMARY KEY (`id`),
					UNIQUE KEY `s0` (`dateid`,`roomId`)
				) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='每天牌桌记录分析' AUTO_INCREMENT=1";
				$res = $this->mysql->runSql($ddl);
				if ( !$res ) gerr("MYSQL->runSql=$ddl");
				$res = $this->mysql->runSql($sql);
				if ( !$res ) gerr("MYSQL->runSql=$sql");
			}
		}
		return true;
	}

	//记录操作历史
	//protocal 	str 	协议/函数
	//roomId	int 	房间ID
	//tableId 	str 	牌桌ID,前缀有房间ID
	//uid 		int 	用户ID
	//user 		arr 	用户数据
	//result 	arr 	结果数据
	//errors 	arr 	错误数据
	//exta-z 	str 	扩展数据，用于存放协议/函数参数
	//return 	bool
	public function action( $protocal, $roomId=0, $tableId='', $uid=0, $user=array(), $result=array(), $errors=array(), $exta='', $extb='', $extc='', $extd='' )
	{
		$dateid = intval(date("Ymd"));
		$hourid = intval(date("G"));
		if ( $dateid != $this->date_action || $hourid != $this->hour_action ) $this->actionInsert();
		if ( !$protocal ) return false;
		$uid = intval($uid);
		if ( $uid && !$user ) $user = $this->getUser($uid);
		if ( !$roomId && isset($user['roomId']) ) $roomId = intval($user['roomId']);
		if ( !$tableId && isset($user['tableId']) ) $tableId = $user['tableId'];
		$tableId = $tableId ? substr_replace($tableId, '', 0, 5) : '';
		$channelid = isset($user['channel']) && $user['channel'] ? $this->getChannelid($user['channel']) : 0;
		$vercode = $user && $user['vercode'] ? $user['vercode'] : 0;
		$coins = $user && $user['coins'] ? $user['coins'] : 0;
		$coupon = $user && $user['coupon'] ? $user['coupon'] : 0;
		$result = $result ? addslashes(json_encode($result)) : '';
		$errors = $errors ? addslashes(json_encode($errors)) : '';
		$ip = $user && $user['ip'] ? $user['ip'] : '';
		$tmcr = time();
		$this->sqls_action[]= "($dateid, $hourid, '$protocal', $roomId, '$tableId', $uid, $channelid, $vercode, $coins, $coupon, '$result', '$errors', '$exta', '$extb', '$extc', '$extd', '$ip', $tmcr)";
		if ( count($this->sqls_action) > $this->sqls_num ) $this->actionInsert();
		return true;
	}

	//插入操作记录
	//dateid 	int 	日期ID
	//return 	bool
	private function actionInsert()
	{
		if ( ! $this->sqls_action || ! $this->date_action ) return false;
		$sql = "INSERT INTO `{$this->kdb_record_action_}{$this->date_action}` (`dateid`, `hourid`, `protocal`, `roomId`, `tableId`, `uid`, `channelid`, `vercode`, "
			."`coins`, `coupon`, `result`, `errors`, `exta`, `extb`, `extc`, `extd`, `exte`, `tmcr`) VALUES ".join(', ', $this->sqls_action);
		$res = $this->mysql->runSql($sql);
		if ( ! $res ) {
			$res = $this->actionCreate($this->date_action);
			if ( ! $res ) return false;
			$res = $this->mysql->runSql($sql);
			if ( ! $res ) return gerr("MYSQL->runSql=$sql");
		}
		$this->date_action = intval(date("Ymd"));
		$this->hour_action = intval(date("G"));
		$this->sqls_action = array();
		return true;
	}

	//建表操作记录
	//dateid 	int 	日期ID
	//return 	bool
	private function actionCreate( $dateid )
	{
		$sql = "CREATE TABLE IF NOT EXISTS `{$this->kdb_record_action_}{$dateid}` (
			`id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
			`dateid` int(8) unsigned NOT NULL DEFAULT '0' COMMENT '日期ID',
			`hourid` tinyint(2) unsigned NOT NULL DEFAULT '0' COMMENT '小时ID',
			`protocal` varchar(16) NOT NULL DEFAULT '' COMMENT '协议/函数',
			`roomId` int(5) unsigned NOT NULL DEFAULT '0' COMMENT '房间ID',
			`tableId` varchar(32) NOT NULL DEFAULT '' COMMENT '牌桌ID',
			`uid` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '用户ID',
			`channelid` int(4) unsigned NOT NULL DEFAULT '0' COMMENT '渠道ID',
			`vercode` int(6) unsigned NOT NULL DEFAULT '0' COMMENT '版本号',
			`coins` int(10) NOT NULL DEFAULT '0' COMMENT '用户乐豆',
			`coupon` int(10) NOT NULL DEFAULT '0' COMMENT '用户乐券',
			`result` varchar(255) NULL DEFAULT '' COMMENT '关键结果json或空',
			`errors` varchar(255) NULL DEFAULT '' COMMENT '错误信息json或空',
			`exta` varchar(32) NULL DEFAULT '' COMMENT '预留A',
			`extb` varchar(32) NULL DEFAULT '' COMMENT '预留B',
			`extc` varchar(32) NULL DEFAULT '' COMMENT '预留C',
			`extd` varchar(32) NULL DEFAULT '' COMMENT '预留D',
			`exte` varchar(32) NULL DEFAULT '' COMMENT '预留E',
			`tmcr` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
			PRIMARY KEY (`id`),
			INDEX `s0` (`dateid`,`hourid`,`protocal`,`roomId`,`tmcr`),
			INDEX `s1` (`channelid`,`vercode`,`uid`)
		) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='操作记录日换表名_20160225' AUTO_INCREMENT=1";
		$res = $this->mysql->runSql($sql);
		if ( !$res ) return gerr("MYSQL->runSql=$sql");
		return true;
	}

	public function actionAnalyse()
	{

	}

}
