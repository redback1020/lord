<?php
class model
{
	// public $s_noob = array('yishiteng'=>5000);//特殊渠道新手乐豆数
	// public $s_trial = array('yishiteng'=>5000);//特殊渠道补助乐豆数
    /**
     * @var $mysql DB
     */
	public $mysql = null;
    /**
     * @var $redis RD
     */
	public $redis = null;
	public $shm = null;
	public $prop = null;
	public $record = null;

	//渠道 一天输赢上限
	const COINS_LIMIT = 10000000;
	
	private $key_cache = "lord_cache";	//缓存数组
	private $key_api_task = "lord_api_task";//接口任务池
	private $key_queue_push = "lord_queue_push";//任务推送队列
	private $key_queue_file_ = "lord_queue_file_";//文件操作队列
	private $key_queue_tsgrab_ = "lord_queue_tsgrab_";//争抢暴奖队列
	private $key_list_file = "lord_list_file";//素材列表
	private $key_list_tips = "lord_list_tips";//提示列表
	private $key_list_topic = "lord_list_topic";//活动列表
	private $key_list_notice = "lord_list_notice";//公告列表
	private $key_list_newmail_ = "lord_list_newmail_";//个人新邮件列表key_list_newmail_.$ud，向在线用户新增时以mailid为key增加，每次定时推送后或者拉取完整邮件列表时删除
	private $key_list_room = "lord_list_room";//扩展房间列表
	private $key_game_version = "lord_game_version";//各种版本号

	private $key_model_rooms_ = "lord_model_rooms_";//赛事配置$md: $roomsId => 房间的配置
	private $key_model_weeks_ = "lord_model_weeks_";//周赛记录$md: $mrw => 房间周赛数据，依据报名、取消、场次结束而变更
	private $key_model_games_ = "lord_model_games_";//赛场记录$md: $mrwg => 房间周赛赛场数据、赛场状态，用hostId识别执行服
	private $key_model_weekplay_ = "lord_model_weekplay_";//周赛玩家记录$md: $playersid => 房间周赛用户的参赛数据，依据参赛情况而变更
	private $key_model_gameplay_ = "lord_model_gameplay_";//赛场玩家记录$mrwg: $ud => 房间赛场用户的参赛数据，依据参赛情况而变更
	private $key_model_goonplay_ = "lord_model_goonplay_";//参赛报名数组$mrwg: list $gameplay
	private $key_modelgame_players_ = "lord_modelgame_players_";

	private $key_room_player_ = "lord_room_player_";
	private $key_room_trio_ = "lord_room_trio_";
	private $key_table_info_  = "lord_table_info_";
	private $key_table_history_ = "lord_table_history_";

	private $key_robot = "lord_robot"; //当前值班机器人
	private $key_robot_list = "lord_robot_zlist";//预留
	private $key_user_task_ = "lord_user_task_";	//用户任务信息
	private $key_user_queue_ = "lord_user_queue_";	//用户发包队列
	// private $key_user_lottery_ = "lord_user_lottery_";	//用户彩票记录
	private $key_user_grabmail = "lord_user_grabmail";	//临时暴奖争抢邮件，暂无收件人
	private $key_user_tesksurprise = "lord_user_tesksurprise";//
	
	private $ini_user = array(
		'uid'=>0,
		//...
		'fd'=>0,
		'modelId'=>0,
		'roomId'=>0,
		'tableId'=>0,
		'seatId'=>0,
	);
	private $ini_table = array(//65
		'tableId'=>0,	//标记桌号
		'hostId'=>0,	//标记HOST
		'modelId'=>0,	//标记模式
		'roomId'=>0,	//标记房间
		'state'=>0,		//桌子状态
		'rate'=>0,		//牌桌倍率
		'rateMax'=>0,	//牌桌顶倍
		'rake'=>0,		//抽水倍率
		'baseCoins'=>0,	//底注基线
		'limitCoins'=>0,//赢钱上限
		'rate_'=>0,				//临时倍率 与rate、seat0rate、seat1rate、seat2rate用于叫抢地主算法
		'firstShow'=>4,			//哪个位置先明牌
		'lordSeat'=>4,			//哪个位置是地主[0|1|2]
		'turnSeat'=>4,			//哪个位置要操作[0|1|2]最先明牌的有叫地主优先权
		'lastCall'=>4,			//哪个席位叫牌/跟牌
		'lastCards'=>array(),	//叫牌/跟牌的内容
		'lastJokto'=>array(),	//叫牌/跟牌的百搭(无花色十六进制旧版牌面)
		'lastType'=>0,			//叫牌/跟牌的牌型
		'outCards'=>array(),	//废牌(已出的)
		'noteCards'=>'S1M124A4K4Q4J4T494847464544434',	//剩牌(记牌器)
		'lordCards'=>array(),	//底牌(地主牌)
		'joker'=>'',			//赖子牌(无花色)
		'noFollow'=>0,			//记录不跟次数
		'shuffle'=>0,			//记录发牌次数
		'isRegame'=>1,			//是否再来一局
		'isNewGame'=>1,			//是否是新一局
		'isStop'=>0,			//是否已经停止继续轮次
		'move'=>0,				//轮次每移动一次＋1
		'create'=>0,			//table create microtime
		'update'=>0,			//table update microtime
		'starte'=>0,			//current game start microtime
		'finish'=>0,			//current game end microtime
		'lastSurprise'=>0,		//
		'seat_rakes'=>array('2'=>0,'1'=>0,'0'=>0),//每人每局抽水数
		'seats'=>array(),		//uid=>[0|1|2]
		'seat0info'=>array(),	//uid=>/sex=>/nick...
		'seat0fd'=>0,			//席位fd
		'seat0uid'=>0,			//席位uid
		'seat0coins'=>0,		//席位coins
		'seat0coupon'=>0,		//席位coupon
		'seat0score'=>0,		//席位score
		'seat0show'=>0,			//席位是否名牌
		'seat0state'=>0,		//席位游戏状态
		'seat0queue'=>-1,		//席位在牌桌队列的掉线重发起点
		'seat0hands'=>array(),	//席位开始手牌(含称为地主后的地主牌)
		'seat0cards'=>array(),	//席位当前有牌
		'seat0rate'=>-1,		//席位叫抢地主的倍率
		'seat0robot'=>0,		//席位是否为机器人
		'seat0trust'=>0,		//席位托管状态
		'seat0delay'=>0,		//席位超时次数
		'seat0sent'=>0,			//席位出牌次数
		'seat0giveup'=>0,		//席位竞技逃跑
		'seat0tttimes'=>0,		//席位今日牌局任务完成次数
		'seat0tteskid'=>0,		//席位在本局任务id
		'seat0task'=>array(),	//席位在本局任务数据
		'seat0tcoupon'=>0,		//席位在本局完成任务获取coupon
		'seat1info'=>array(),
		'seat1fd'=>0,
		'seat1uid'=>0,
		'seat1coins'=>0,
		'seat1coupon'=>0,
		'seat1score'=>0,
		'seat1show'=>0,
		'seat1state'=>0,
		'seat1queue'=>-1,
		'seat1hands'=>array(),
		'seat1cards'=>array(),
		'seat1rate'=>-1,
		'seat1robot'=>0,
		'seat1trust'=>0,
		'seat1delay'=>0,
		'seat1sent'=>0,
		'seat1giveup'=>0,
		'seat1tttimes'=>0,
		'seat1tteskid'=>0,
		'seat1task'=>array(),
		'seat1tcoupon'=>0,
		'seat2info'=>array(),
		'seat2fd'=>0,
		'seat2uid'=>0,
		'seat2coins'=>0,
		'seat2coupon'=>0,
		'seat2score'=>0,
		'seat2show'=>0,
		'seat2state'=>0,
		'seat2queue'=>-1,
		'seat2hands'=>array(),
		'seat2cards'=>array(),
		'seat2rate'=>-1,
		'seat2robot'=>0,
		'seat2trust'=>0,
		'seat2delay'=>0,
		'seat2sent'=>0,
		'seat2giveup'=>0,
		'seat2tttimes'=>0,
		'seat2tteskid'=>0,
		'seat2task'=>array(),
		'seat2tcoupon'=>0,
	);

	function __construct( $redis=null, $mysql=null )
	{
		$this->redis = $redis;
		$this->mysql = $mysql;
		$this->prop = $this->getProp();
		$this->record = $this->getRecord();
		$this->confs = include(ROOT.'/conf/confs.php');
		$this->rooms = include(ROOT.'/conf/rooms.php');
		$ext = $this->listGetRoom();
		foreach ( $ext as $k => $v ) {
			$this->rooms[$v['roomId']] = $v;
		}
	}

	function __destruct()
	{
		$this->record->close();
		$this->record = null;
		$this->prop->close();
		$this->prop = null;
		$this->redis = null;
		$this->mysql = null;
		return true;
	}

	// prop
	public function getProp()
	{
		if ( $this->prop === null ) $this->prop = prop::inst($this->redis, $this->mysql);
		return $this->prop;
	}

	// record
	public function getRecord()
	{
		if ( $this->record === null ) $this->record = record::inst($this->redis, $this->mysql);
		return $this->record;
	}

	// push
	public function popPUSH()
	{
		return $this->redis->lpop($this->key_queue_push);
	}

	// API
	public function popAPIS()
	{
		return $this->redis->lpop($this->key_api_task);
	}

	// file
	public function popFILE()
	{
		return $this->redis->lpop($this->key_queue_file_.HOSTID);
	}

	public function bobGRTS( $id, $ud )
	{
		return $this->redis->ladd($this->key_queue_tsgrab_.$id, $ud);
	}

	public function popGRTS( $id )
	{
		return $this->redis->lpop($this->key_queue_tsgrab_.$id);
	}
	public function delGRTS( $id )
	{
		return $this->redis->del($this->key_queue_tsgrab_.$id);
	}
	public function retUserMail( $id, $ud )
	{
		$mail = $this->redis->hget($this->key_user_grabmail, $id);
		$mail['id'] = $id;
		$mail['uid'] = $ud;
		$sql = "UPDATE lord_user_inbox SET `uid` = $ud WHERE `id` = $id";
		$res = $this->mysql->runSql($sql);
		$res = $this->redis->hdel($this->key_user_grabmail, $id);
		return $mail;
	}
	public function delUserMail( $id )
	{
		return $this->redis->hdel($this->key_user_grabmail, $id);
	}
	public function sendMail( $ud, $subject, $content, $items=array(), $isLogin=0 )
	{
		$ud = intval($ud);
		if ( $ud < 1 || !$subject || !$content || !is_array($items) ) return false;
		$time = time();
		$sql = "INSERT INTO `lord_user_inbox` (`type`,`fromuid`,`uid`,`subject`,`content`,`items`,`fileid`,`is_read`,`is_del`,`sort`,`create_time`,`update_time`) VALUES (0,0,$ud,'$subject','$content','".addslashes(json_encode($items))."',0,0,0,1,$time,$time)";
		$res = $this->mysql->runSql($sql);
		if ( !$res ) return gerr("邮件插入失败 sql=$sql");
		if ( $isLogin ) return true;
		$id = intval($this->mysql->lastId());
		if ( !$id ) return gerr("邮件插入失败 id=$id sql=$sql");
		$push = array(
			'type' => 'mail',
			'uid' => $ud,
			'id' => $id,
			'subject' => $subject,
			'content' => $content,
			'items' => intval(!!$items),
			'fileid' => 0,
			'is_read' => 0,
			'sort' => 1,
		);
		$this->redis->ladd($this->key_queue_push, $push);
		return true;
	}
	public function updSurpriseRecord( $id, $ud )
	{
		$sql = "UPDATE lord_user_tesksurprise SET `uid` = $ud WHERE `id` = $id";
		return $this->mysql->runSql($sql);
	}
	public function delSurpriseRecord( $id )
	{
		$sql = "DELETE FROM lord_user_tesksurprise WHERE `id` = $id";
		return $this->mysql->runSql($sql);
	}


// 执行发送业务

	//发送错误到fd
	function sendError( $fd, $cmd, $code, $errno, $error=null, $data=array() )
	{
		if ( !$fd || !$cmd || !$errno || !is_array($data) ){
			gerr("发送错误失败 sendError($fd,$cmd,$code,$errno,$error,".json_encode($data) );
			return false;
		}
		$data['errno'] = $errno;
		$data['error'] = $error ? $error : "操作失败。";
		return $this->sendToFd($fd, $cmd, $code, $data);
	}

	//发送到牌桌所有用户，相同数据
	function sendToTable( $table, $cmd, $code, $data, $line=0 )
	{
		if ( !isset($table['seats']) || !$table['seats'] || !is_array($table['seats']) )
		{
			gerr("牌桌数据无效 sendToTable table=".json_encode($table));
			return false;
		}
		foreach ( $table['seats'] as $uid=>$sid ) {
			$player = array('fd'=>$table["seat{$sid}fd"], 'uid'=>$uid, 'tableId'=>$table['tableId']);
			$res = $this->sendToPlayer($player, $cmd, $code, $data);
		}
		return true;
	}

	//发送到牌桌某个用户，不同(相同)数据
	//is_save	当前发送的内容: 0不存历史+直接发送/1存入历史+可能缓发/2不存历史+可能缓发
	function sendToPlayer( $player, $cmd, $code, $data, $is_save=1 )
	{
		$fd = $player['fd'];//发送给牌桌用户的时候使用牌桌上的用户fd
		$ud = $player['uid'];
		$td = $player['tableId'];
		$data['log']['ud'] = $ud;
		$data['log']['td'] = $td;
		//添加到牌桌历史
		if ( $is_save == 1 ) $this->addTableHistory($td, array('uid'=>$ud,'cmd'=>$cmd,'code'=>$code,'data'=>$data));
		//掉线用户或机器人不发
		if ( !$fd ) return false;
		//不用存入历史的，绿色通道，直接发送
		if ( !$is_save ) return $this->sendToFd($fd, $cmd, $code, $data);
		//常规消息
		$fdinfo = getBind($fd);
		//阻塞状态，增加到缓发队列	//return $index;
		if ( $fdinfo && isset($fdinfo['is_lock']) && $fdinfo['is_lock'] ) {
			return $this->addUserQueue( $ud, array('uid'=>$ud,'cmd'=>$cmd,'code'=>$code,'data'=>$data) );
		}
		//畅通状态，优先发送缓发数据 //预留 这个地方需要优化，目前无优化方案
		while ( $queue = $this->popUserQueue($ud) )
		{
			$this->sendToFd($fd, $queue['cmd'], $queue['code'], $queue['data']);
		}
		//正式发送
		return $this->sendToFd($fd, $cmd, $code, $data);
	}

	//发送给不一定在牌桌的用户
	function sendToUser( $ud, $cmd, $code, $data )
	{
		if ( is_array($ud) && isset($ud['fd']) && isset($ud['uid']) && isset($ud['tableId']) ) {
			$user = $ud;
			$ud = intval($user['uid']);
			if ( $ud < 1 ) return false;
		} elseif ( $ud > 0 ) {
			$user = $this->getUserInfo($ud);
			if ( !$user ) return false;
		} else {
			return false;
		}
		if ( !$user['fd'] ) return false;
		$fd = $user['fd'];	//常规发送使用用户信息里的fd
		$td = $user['tableId'];
		$data['log']['ud'] = $ud;
		$data['log']['td'] = $td;
		return $this->sendToFd($fd, $cmd, $code, $data);
	}

	//发送给FD
	function sendToFd( $fd, $cmd, $code, $data )
	{
		//暂不支持串型桌号、整型微信码、整型靓号
		if ( isset($data['check_code']) ) $data['check_code'] = strval($data['check_code']);
		if ( isset($data['cool_num']) ) $data['cool_num'] = strval($data['cool_num']);
		if ( isset($data['tableId']) ) $data['tableId'] = 1;
		if ( isset($data['seat0info']['tableId']) ) $data['seat0info']['tableId'] = 1;
		if ( isset($data['seat1info']['tableId']) ) $data['seat1info']['tableId'] = 1;
		if ( isset($data['seat2info']['tableId']) ) $data['seat2info']['tableId'] = 1;
		if ( isset($data['0']['tableId']) ) $data['0']['tableId'] = 1;
		if ( isset($data['1']['tableId']) ) $data['1']['tableId'] = 1;
		if ( isset($data['2']['tableId']) ) $data['2']['tableId'] = 1;
		return sendToFd($fd, $cmd, $code, $data);
	}

	//关闭连接uid
	function closeToUid( $ud, $data )
	{
		$user = $this->getUserInfo($ud);
		if ( $user && isset($user['fd']) && $user['fd'] )
		{
			$fd = $user['fd'];
			$info = getBind($fd);
			if ( $info && isset($info['uid']) && $info['uid'] == $ud ) {
				return closeToFd($fd, $data);
			}
		}
		gerr("[KILLUD][$ud] ".$data);
		$line = intval(str_replace('client-', '', $data));
		$this->desUserInfo($ud, $user, $line);
		return true;
	}


// 用户基础信息

	// 增减 原子操作
	function incUserInfo( $ud, $info )
	{
		$ud = intval($ud);
		if ( $ud < 1 || !$info || !is_array($info) ) return false;
		$sends = array('coins', 'coupon', 'lottery', 'mail_unread', 'checkin_undo', 'task1_unaward', 'task2_unaward', 'task3_unaward');
		$data = $send = array();
		foreach ( $info as $k => $v ) {
			$v = intval($v);
			if ( !$v ) continue;
			$res = incUser($ud, $k, $v);
			if ( $res === false ) continue;
			$data[$k] = $res;
			if ( in_array($k, $sends) ) $send[$k] = $res;
			if($k == 'coupon' && $v > 0 ){
 	                   incUser($ud, 'login_got_coupon', $v);
 		        }
		}
		return array('info'=>$data, 'send'=>$send);
	}
	// 入库
	function updUserInfo( $ud, $info=array(), $now=false )
	{
		$ud = intval($ud);
		if ( $ud < 1 || !is_array($info) ) return false;
		if ( !$info ) {
			$info = $this->getUserInfo($ud);
		}
		$info = is_array($info) && isset($info['sex']) && isset($info['age']) && isset($info['gold']) && isset($info['coins']) && isset($info['coupon']) && isset($info['lottery']) ? $info : array();
		$set = array();
		if (isset($info['sex'])) $set[]= " `sex`=".intval($info['sex']);
		if (isset($info['age'])) $set[]= " `age`=".intval($info['age']);
		if (isset($info['gold'])) $set[]= " `gold`=".($info['gold']>0?intval($info['gold']):0);
		if (isset($info['coins'])) $set[]= " `coins`=".intval($info['coins']);
		if (isset($info['coupon'])) $set[]= " `coupon`=".intval($info['coupon']);
		if (isset($info['lottery'])) $set[]= " `lottery`=".intval($info['lottery']);
		$set = $set ? join(',', $set) : '';
		if ( $set ) {
			$sql = "UPDATE `lord_game_user` SET $set WHERE `uid` = $ud";
			if ( $now ) {
				$res = $this->mysql->runSql($sql);
			} else {
				$res = bobSql($sql);
			}
		}
		return false;
	}
	// 获取用户昵称
	function getUserNick( $ud )
	{
		$info = $this->getUserInfo($ud);
		if ( $info && is_array($info) && isset($info['nick']) ) {
			return $info['nick'];
		} else {
			$sql = "SELECT `nick` FROM `lord_game_user` WHERE `uid` = $ud LIMIT 0,1";
			$nick = $this->mysql->getVar($sql);
		}
		if (!$nick) {
			$sql = "SELECT `nick` FROM `lord_game_robot` WHERE `uid` = $ud LIMIT 0,1";
			$nick = $this->mysql->getVar($sql);
		}
		$nick = $nick ? strval($nick) : ("新手".($ud+1234567));
		return $nick;
	}

	//销毁用户数据
	function desUserInfo( $ud, $U=array(), $line=0, $robot=0, $now=false, $fd=false )
	{
		if ( $U && isset($U['robot']) && $U['robot'] ) $robot = 1;
		if ( ! $robot )
		{
			if ( $U && count($U) > 28 ) {
				$time = time();
				$U['play'] = isset($U['play']) ? ($U['play'] - $U['Lplay']) : 0;
				$U['win'] = isset($U['win']) ? ($U['win'] - $U['Lwin']) : 0;
				if ( ! isset($U['Ltime']) ) $U['Ltime'] = $U['login_time'];
				if ( ! isset($U['Lgold']) ) $U['Lgold'] = $U['login_gold'];
				if ( ! isset($U['Lcoins']) ) $U['Lcoins'] = $U['login_coins'];
				if ( ! isset($U['Lcoupon']) ) $U['Lcoupon'] = $U['login_coupon'];
				if ( ! isset($U['Llottery']) ) $U['Llottery'] = $U['login_lottery'];
				$U['online_time'] = $time - $U['Ltime'];
				$dateid = intval(date("Ymd", $time));
				$sql = "INSERT INTO `lord_user_logout_$dateid` (";
				$sql.= "`uid`,`login_channel`,`login_vercode`,`login_ip`,`login_time`,`login_gold`,`login_coins`,`login_coupon`,`login_lottery`,";
				$sql.= "`last_action`,`last_time`,`play`,`win`,`logout_time`,`logout_gold`,`logout_coins`,`logout_coupon`,`logout_lottery`,`online_time`) VALUES (";
				$sql.= $U['uid'].",'".$U['channel']."',".$U['vercode'].",'".$U['ip']."',".$U['Ltime'].",".$U['Lgold'].",".$U['Lcoins'].",".$U['Lcoupon'].",".$U['Llottery'].",";
				$sql.= "'".$U['last_action']."',".$U['last_time'].",".$U['play'].",".$U['win'].",$time,".$U['gold'].",".$U['coins'].",".$U['coupon'].",".$U['lottery'].",".$U['online_time'].")";
				bobSql($sql);
			}
			$this->updUserInfo($ud, $U, 1);
			$this->updUserTask($ud, array(), 1);
			$this->updUserTesk($ud, 1);
		}
		if($fd == $U["fd"] || !$U["fd"])
		delUser($ud);
		$this->delUserTask($ud);
		return true;
	}

	//销毁机器人
	function desRobot( $ud, $line=0 )
	{
		$sql = "UPDATE lord_game_robot SET state = 0 WHERE uid = $ud";
		bobSql($sql);
		delUser($ud);
		return true;
	}

// 用户任务信息

	function getUserInfo( $ud )
	{
		$ud = intval($ud); if ( $ud < 1 ) return false;
		$user = getUser($ud); if ( !$user ) return false;
		if ( isset($user['fd']) && isset($user['uid']) && isset($user['coins']) && isset($user['robot']) && !(!$user['robot'] && count($user) < 29) ) return $user;
		$this->desUserInfo($ud, $user);
		debug("用户安全清理 U=$ud user=".json_encode($user));
		return false;
	}

	function getUserTesk( $ud )
	{
		$ud = intval($ud); if ( $ud < 1 ) return array();
		$tesk = new tesk($this->mysql, $this->redis);
		return $tesk->getUserTeskInfo($ud);
	}

	// 获取 默认在无redis数据时从数据库初始化数据
	function getUserTask( $ud, $isInit=1 )
	{
		$ud = intval($ud);
		if ( $ud < 1 ) return array();
		$dateid = intval(date("Ymd"));
		$wd = intval(date("Ymd",time()-(date("N")-1)*86400));
		$usertask = $this->redis->hgetall($this->key_user_task_.$ud);
		if ( $usertask && ( !isset($usertask['gold_all']) || !isset($usertask['login_day5_got']) || count($usertask) < 70 ) ) {
			$res = $this->redis->del($this->key_user_task_.$ud);
			$usertask = array();
		}
		if ( !$isInit ) return ($usertask && is_array($usertask)) ? $usertask : array();
		if ( !$usertask )
		{
			$usertask = $this->mysql->getLine("SELECT * FROM `lord_user_task` WHERE `uid` = $ud");
			if ( !$usertask ) {
				$A = $this->getUserAnalyse($ud);
				$reg_dateid = $A && isset($A['add_time']) ? intval(str_replace('-','',substr_replace($A['add_time'],'',10))) : $dateid;
				$res = $this->mysql->runSql("INSERT INTO `lord_user_task` ( `uid`, `dateid` ) VALUES ( $ud, $reg_dateid )");
				$usertask = $this->mysql->getLine("SELECT * FROM `lord_user_task` WHERE `uid` = $ud");
				if ( !$usertask ) return array();
			}
			foreach ( $usertask as $k => $v ) {
				$usertask[$k] = intval($v);
			}
			$this->setUserTask($ud, $usertask);
		}
		$is_newday = $is_neweek = 0;
		if ( $usertask['login_this_dateid'] ) {
			$last_dateid = $usertask['login_this_dateid'];
			$last_uttime = strtotime(substr_replace(substr_replace($last_dateid, '-', 4, 0), '-', 7, 0));
			$last_weekid = intval(date("Ymd", $last_uttime-(date("N", $last_uttime)-1)*86400));
			$is_newday = intval($last_dateid != $dateid);
			$is_neweek = intval($last_weekid != $wd);
		}
		if ( $is_newday )
		{
			$newut['login_day_times'] = 0;
			$newut['gold_day'] = 0;
			$newut['coupon_day'] = 0;
			$newut['coins_day'] = 0;
			$newut['normal_day_play'] = 0;
			$newut['normal_day_win'] = 0;
			$newut['normal_day_earn'] = 0;
			$newut['normal_day_maxrate'] = 0;
			$newut['normal_day_maxearn'] = 0;
			$newut['match_day_play'] = 0;
			$newut['match_day_point'] = 0;
			$newut['lottery_day_times'] = 0;
			if ( $is_neweek ) {
				$newut['gold_week'] = 0;
				$newut['coupon_week'] = 0;
				$newut['coins_week'] = 0;
				$newut['normal_week_play'] = 0;
				$newut['normal_week_win'] = 0;
				$newut['normal_week_earn'] = 0;
				$newut['normal_week_maxrate'] = 0;
				$newut['normal_week_maxearn'] = 0;
				$newut['match_week_play'] = 0;
				$newut['match_week_point'] = 0;
				$newut['lottery_week_times'] = 0;
			}
			$res = $this->setUserTask($ud, $newut);
			$usertask = array_merge($usertask, $newut);
		}
		return ($usertask && is_array($usertask)) ? $usertask : array();
	}
	// 设置
	function setUserTask( $ud, $info )
	{
		$ud = intval($ud);
		if ( $ud < 1 || !$info || !is_array($info) ) return false;
		return $this->redis->hmset($this->key_user_task_.$ud,$info);
	}
	// 加减
	function incUserTask( $ud, $info )
	{
		$ud = intval($ud);
		if ( $ud < 1 || !$info || !is_array($info) ) return false;
		foreach ( $info as $k => $v ) {
			$res = $this->redis->hincrby($this->key_user_task_.$ud, $k, intval($v));
			if ($res===false) return false;
			$info[$k] = $res;
		}
		return $info;
	}
	// 入库
	function updUserTask( $ud, $info=array(), $now=false )
	{
		$ud = intval($ud);
		if ( $ud < 1 || !is_array($info) ) return false;
		if ( !$info ) $info = $this->getUserTask($ud, 0);
		if ($info && isset($info['uid'])) unset($info['uid']);
		$info = ( is_array($info) && count($info) ) ? $info : array();
		if ( !$info ) return false;
		$sql_ = array();
		foreach ( $info as $k => $v ) {
			$sql_[]= " `$k`=".intval($v);
		}
		$sql_ = $sql_ ? join(',', $sql_) : '';
		if ($sql_) {
			$sql = "UPDATE `lord_user_task` SET $sql_ WHERE `uid` = $ud";
			if ( $now ) {
				$res = $this->mysql->runSql($sql);
			} else {
				$res = bobSql($sql);
			}
		}
		return false;
	}
	// 删除
	function delUserTask( $ud )
	{
		$ud = intval($ud);
		if ( $ud < 1 ) return false;
		return $this->redis->del($this->key_user_task_.$ud);
	}

	//第三方注册
    function reg3rd($uuid, $open_type, $open_id, $extend, $channel='', $version='', $password='', $ip='')
    {
    	//open_type登录 | open_id登录
    	$isOpentp = $isOpenid = 0;
    	if ( is_numeric($open_type) ) 
    	{
    	    $sql = "SELECT * FROM `user_login` WHERE `open_type`='$open_type'";
    	    $rows = $this->mysql->getData($sql);
    		if ( $rows ) {
    			$isOpentp = 1;
    		} else {
    		    $sql = "SELECT * FROM `user_login` WHERE `open_id`='$open_id'";
    		    $rows = $this->mysql->getData($sql);
    			$isOpenid = 1;
    		}
    	}
    	else
    	{
    	    $sql = "SELECT * FROM `user_login` WHERE `open_id`='$open_id'";
    	    $rows = $this->mysql->getData($sql);
    		$isOpenid = 1;
    	}
    	//处理登录过程
    	if ( $rows )
    	{
    		//用户重复现象，3rd中不会存在
    		$r = current($rows);
    		$uid = $r['uid'];
    		//重设用户的open_type为可能存在的SDKUID, 下次可以通过open_type登录
    		if ( $isOpenid && in_array($r['open_type'], array('', '0', 'name', 'device')) && ($sdkuid = str_replace('3rdplt_', '', $open_id)) && is_numeric($sdkuid) ) {
    		    $this->mysql->runSql('begin');
    		    $sql = "UPDATE `user_login` set  `open_type`='$sdkuid' WHERE `uid`=$uid";
    		    $this->mysql->runSql($sql);
    		    $this->mysql->runSql('commit');
    		}
    		//查找用户密码
    		$sql = "SELECT * FROM `user_user` WHERE `id`=$uid";
    		$rows = $this->mysql->getData($sql);
    		if ( $rows ) {
    			$rr = current($rows);
    			$result['code'] = 0;
    			$result['password'] = $rr['password'];
    		} else {
    			$result['code'] = 3;
    			$result['error'][] = array('result'=>0, 'table'=>'user_user', 'select'=>"id=$uid");
    		}
    		return $result;
    	}
    	
    	$result = $this->reg($uuid, $open_type, $open_id, $extend, $channel, $version, $password, $ip);
    	return $result;
    }
	
    //游客注册
    function regDev($uuid, $open_type, $open_id, $extend, $channel='', $version='', $password='', $ip='')
    {
        //open_type登录 | open_id登录
        $isOpentp = $isOpenid = 0;
        if ( is_numeric($open_type) )
        {
            $sdkuid = intval($open_type);
        }
        else
        {
            $sdkuid = substr_count($open_id, '3rdplt_') ? str_replace('3rdplt_', '', $open_id) : 0;
    		if ( is_numeric($sdkuid) ) {
    			$sdkuid = intval($sdkuid);
    		} else {
    			$sdkuid = 0;
    		}
        }
        //尝试以SDKUID直接获取用户
    	$r = array();
    	if ( $sdkuid ) 
    	{
    	    $sql = "SELECT * FROM `user_login` WHERE `open_type`='$sdkuid'";
    	    $users = $this->mysql->getData($sql);
    		if ( $users ) 
    		{
    			$r = current($users);
    		}
    	}
    	if(!$r)
    	{
    	    $sql = "SELECT * FROM `user_login` WHERE `open_id`='$open_id'";
    	    $users = $this->mysql->getData($sql);
    	    if ( !$users ) $users = array();
    	    //优先匹配扩展设备号
    	    foreach ( $users as $k => $v )
    	    {
    	        if ( $v['extend'] === $extend ) {
    	            if ( $v['open_type'] !== 'device' ) {
    	                unset($users[$k]);
    	                continue;
    	            } else {
    	                $r = $v;
    	                break;
    	            }
    	        } elseif ( $v['extend'] || $v['open_type'] !== 'device' ) {
    	            unset($users[$k]);
    	        }
    	    }
    	    //其次使用原始设备号
    	    if ( ! $r && $users) $r = reset($users);
    	}
    	if ( $r )
    	{
    	    $uid = $r['uid'];
    	    //更新独占扩展设备号
    	    if ( $extend && ! $r['extend'] ) {
    	        $sql = "UPDATE `user_login` set `extend`='$extend' WHERE `uid`=$uid AND `extend`=''";
    	        $this->mysql->runSql($sql);
    	    }
    	    //重设用户的open_type为可能存在的SDKUID, 下次可以通过open_type登录
    	    if ( $sdkuid && $r['open_type'] === 'device' ) {
    	        $sql = "UPDATE `user_login` set `extend`='$extend' WHERE `uid`=$uid";
    	        $this->mysql->runSql($sql);
    	    }
    	    //查找用户密码
    	    $sql = "SELECT * FROM `user_user` WHERE `id`=$uid";
    	    $rows = $this->mysql->getData($sql);
    	    if ( $rows ) {
    	        $rr = current($rows);
    	        $result['code'] = 0;
    	        $result['password'] = $rr['password'];
    	    } else {
    	        $result['code'] = 3;
    	        $result['error'][] = array('result'=>0, 'table'=>'user_user', 'select'=>"id=$uid");
    	    }
    	    return $result;
    	}
        
    	$result = $this->reg($uuid, $open_type, $open_id, $extend, $channel, $version, $password, $ip);
    	
        return $result;
    }

    //注册入库
    public function reg($uuid, $open_type, $open_id, $extend, $channel='', $version='', $password='', $ip='')
    {
        //处理注册过程
        $this->mysql->runSql('begin');
        $results = array();
        //插入基础表
        $sql = "INSERT INTO `user_user` (`uuid`,`password`,`channel`)VALUES('$uuid','$password','$channel')";
        $res = $this->mysql->runSql($sql);
        $uid = $this->mysql->lastId();
        $results[] = array('result'=>$res);
        //插入登录表
        $sql = "INSERT INTO `user_login` (`uid`,`open_id`,`open_type`,`extend`)VALUES($uid,'$open_id','$open_type','$extend')";
        $res = $this->mysql->runSql($sql);
        $results[] = array('result'=>$res);
        //插入分析表
        $now = time();
        $sql = "INSERT INTO `user_analyse` (`uid`,`device`,`ip`,`last_ip`,`add_time`,`last_time`)VALUES($uid,'$open_id','$ip','$ip',$now,$now)";
        $res = $this->mysql->runSql($sql);
        $results[] = array('result'=>$res);
        //处理插入结果
        $error = array();
        foreach ( $results as $k => $v )
        {
            if( !$v['result']) $error[] = $v;
        }
        if ( $error )
        {
            $this->mysql->runSql("rollback");
            $result['code'] = 3;
            $result['error'] = $error;
        }
        else
        {
            $this->mysql->runSql('commit');
            $result['code'] = 0;
            $result['password'] = $password;
        }
        
        return $result;
    }
    
	//用户 - 校验登录参数
	//d 			str 	原始设备号 device | deviceid | open_id | did | d
	//e 			str 	扩展设备号 extend | e
	//t 			str 	支付设备号 open_type | t
	//p 			str 	用户密码字串 password | p
	//return 		arr 	用户编号密码 array('id'=>1,'password'=>'6789')
	//return 		int 	错误编码 11设备为空 13设备不存在 15密码错误
	public function userCheck( $t, $d, $e, $p )
	{
		if ( ! $t || ! $d || ! $p ) {
			gerr("用户设备无效 T=$t D=$d E=$e P=$p");
			return 11;
		}
		
		if ( $t === 'device' ) {
			$sql = "SELECT * FROM `user_login` WHERE `open_id`='$d' AND `extend`='$e'";
			$rows = $this->mysql->getData($sql);
			if ( $rows ) {
				$isDevice = $isName = 0;
				foreach ( $rows as $row ) {
					if ( $row['open_type'] == "device" ) {
						$ud = $row['uid'];
						$isDevice = 1;
						break;
					}
				}
				if ( ! $ud ) {
					foreach ( $rows as $row ) {
						if ( $row['open_type'] == "name" ) {
							$ud = $row['uid'];
							$isName = 1;
							break;
						}
					}
					if ( $isName ) {
						$sql = "UPDATE `user_login` SET `open_type` = 'device' WHERE `uid` = $ud";
						$this->mysql->runSql($sql);
					}
				}
				if ( ! $ud ) {
					foreach ( $rows as $row ) {
						if ( is_numeric($row['open_type']) ) {
							$ud = $row['uid'];
							break;
						}
					}
				}
			}
		} else {
			$sql = "SELECT `uid` FROM `user_login` WHERE `open_type`='$t'";
			$ud = $this->mysql->getVar($sql);
			if ( ! $ud ) {
				$sql = "SELECT * FROM `user_login` WHERE `open_id`='$d' AND `extend`='$e'";
				$rows = $this->mysql->getData($sql);
				if ( $rows ) {
					foreach ( $rows as $row ) {
						if ( $row['open_type'] == "name" ) {
							$ud = $row['uid'];
							break;
						}
					}
					if ( ! $ud ) {
						foreach ( $rows as $row ) {
							if ( $row['open_type'] == "device" ) {
								$ud = $row['uid'];
								break;
							}
						}
					}
				}
				if ( $ud ) {
					$sql = "UPDATE `user_login` SET `open_type` = '$t' WHERE `uid` = $ud";
					$this->mysql->runSql($sql);
				}
			}
		}
		if ( !$ud ) {
			gerr("用户设备错误 T=$t D=$d E=$e P=$p Q=$sql");
			return 13;
		}
		$sql = "SELECT `id`, `password` FROM `user_user` WHERE `id` = $ud";
		$res = $this->mysql->getLine($sql);
		if ( !$res ) {
			gerr("用户编号未知 T=$t D=$d E=$e P=$p U=$ud Q=$sql");
			return 13;
		}
		if ( $res['password'] != $p ) {
			gerr("用户密码错误 T=$t D=$d E=$e P=$p U=$ud R=".json_encode($res));
			return 15;
		}
		return $res;
	}

	//用户 - 登录获取数据
	//ud 			int 	用户ID
	//d 			str 	用户设备编号 device | deviceid | open_id | did
	//e 			str 	用户扩展编号 extend
	//v 			str 	用户客端版本 version
	//c 			str 	用户登录渠道 channel
	//i 			str 	用户登录IP ip
	//robot 		str 	外置机器人标记 channel=robot 预留
	//datm 			str 	用户登录日期时间
	//wn 			str 	第三方昵称预置
	//return 		arr 	用户数据
	//return 		int 	错误编码 15密码错误
	public function userLogin( $ud, $d, $e, $v, $c, $i, $wn, $robot, $datm, $noob=0 )
	{
		$ud = intval($ud);
		if ( $ud < 1 || !$d ) {
			gerr("用户登录错误 U=$ud D=$d E=$e V=$v C=$c I=$i");
			return 15;
		}
		$dd = dateid($datm);
		$sql = "SELECT `uid`, `cool_num`, `nick`, `sex`, `age`, `word`, `gold`, `coins`, `coupon`, `lottery`, `level`, `exp`, `avatar`, `check_code`, `channel` FROM `lord_game_user` WHERE `uid` = $ud";
		$data = $this->mysql->getLine($sql);
		if ( $data ) {

			gerr("LG".json_encode($data));

			$data['nick'] = $this->user3rdnick($ud, $data['nick'], $wn);
			$data['is_noob'] = $noob;
			$A = $this->getUserAnalyse($ud);
			if ( !$A ) {
				$sql = "INSERT INTO `lord_game_analyse` (`uid`, `device`, `extend`, `ip`, `last_ip`, `add_time`, `last_time`, `version`) VALUES ($ud, '$d', '$e', '$i', '$i', '$datm', '$datm', '$v')";
				$res = $this->mysql->runSql($sql);
				$A = $this->getUserAnalyse($ud);
				if ( !$A ) return 15;
			}
			if ( dateid($A['last_login']) == $dd ) {
				$first = $A['first'] = 0;
			} else {
				$first = $A['first'] = 1;
				$A['trial_count'] = $A['trial_daily'] = 0;
			}
			// if ( $A['version'] && $A['version'] !== $v && $v === '1.6.0' ) {//{"items":{"9":{"id":9,"name":"\u8bb0\u724c\u5668(7\u5929)","cd":"2","num":1,"ext":0}}}
			// 	$this->sendMail($ud, '恭喜获得7天记牌器奖励', "恭喜您的斗地主版本升到了1.6.0，\n将获得7天记牌器。\n第一次获得记牌器的用户，需要您重新登录，\n然后在牌桌内即可使用。", array('items'=>array('9'=>array('id'=>9,'name'=>'记牌器(7天)','cd'=>2,'num'=>1,'ext'=>0))), 1);//登录时发送1
			// }
			$sqlp = ( $e && !$A['extend'] ? ", `extend` = '$e'" : "" ) . ( $first ? ", trial_count = 0, trial_daily = 0" : "" );
			$sql = "UPDATE `lord_game_analyse` SET last_login = '$datm', login = login + 1 , `last_ip` = '$i', `version` = '$v' $sqlp WHERE `uid` = $ud";
			bobSql($sql);
			//每次登录记录
			$sql = "INSERT INTO `lord_game_login` ( `uid`, `cool_num`, `gold`, `coins`, `device`, `channel`, `is_tv`, `version`, `login_ip`, `login_time` ) ";
			$sql.= "VALUES ( $ud, ".$data['cool_num'].", ".$data['gold'].", ".$data['coins'].", '$d', '$c', 1, '$v', '$i', '$datm' )";
			bobSql($sql);
			//更新用户渠道
			if ( $data['channel'] != $c ) {
				$sql = "UPDATE `lord_game_user` SET `channel` = '$c' WHERE `uid` = $ud";
				bobSql($sql);
			}
			//用户乐币矫正(兼容旧版)
			if ( $data['gold'] < 0 ) {
				$data['gold'] = 0;
				$sql = "UPDATE `lord_game_user` SET `gold` = 0 WHERE `uid` = $ud";
				bobSql($sql);
			}
			$data['analyse'] = $A;
			$data['first'] = $first;
			return $data;
		}
		//预留 尝试串号数据拷贝
		$cool = $ud + 1234567;//新的靓号生成机制
		$nick = $this->confs['user_nick_prev'].$cool;
		$coins = $this->confs['user_noob_coins'];
		$check_code = mt_rand(1000, 9999);
		$sqls = array();
		$sqls[] = "INSERT INTO `lord_game_user` (`uid`, `cool_num`, `nick`, `coins`, `check_code`, `channel`) VALUES ($ud, $cool, '$nick', $coins, '$check_code', '$c')";
		$sqls[] = "INSERT INTO `lord_game_analyse` (`uid`, `device`, `extend`, `ip`, `last_ip`, `add_time`, `last_time`, `version`) VALUES ($ud, '$d', '$e', '$i', '$i', '$datm', '$datm', '$v')";
		$flag = true;
		//使用mysql 事物
		$this->mysql->runSql('begin');
		foreach ( $sqls as $sql )
		{
			if ( !$this->mysql->runSql($sql) ) {
				gerr("用户登录错误 U=$ud D=$d E=$e V=$v C=$c I=$i Q=$sql");
				$flag = false;
				break;
			}
		}
		if ( !$flag ) {
			$this->mysql->runSql('rollback');
			return 15;
		}
		$this->mysql->runSql('commit');
		return $this->userLogin($ud, $d, $e, $v, $c, $i, $wn, $robot, $datm, 1);
	}

	function user3rdnick( $ud, $nick, $wn )
	{
		if ( ! $wn ) return $nick;
		$wnn = utf8substr(trim($wn), 0, 15);
		if ( substr_count($nick, $wnn) ) return $nick;
		$mye = mysqli_real_escape_string($this->mysql->db, $wnn);
		$i = 0;
		$sql = "SELECT * FROM `lord_game_user` WHERE `nick` = '$mye'";
		$ret = $this->mysql->getData($sql);
		if ( $ret ) {
			if ( count($ret) > 1 ) {
				foreach ( $ret as $k => $v )
				{
					if ( ! $k ) continue;
					$sql = "UPDATE `lord_game_user` SET `nick` = '{$mye}{$k}' WHERE `uid` = ".$v['uid'];
					$this->mysql->runSql($sql);
					$i = $k;
				}
				$i++;
				$sql = "UPDATE `lord_game_user` SET `nick` = '{$mye}{$i}' WHERE `uid` = $ud";
				$this->mysql->runSql($sql);
				return $wnn.$i;
			} elseif ( $ret[0]['uid'] == $ud ) {
				return $wnn;
			} else {
				$i = mt_rand(10000,99999);
				$sql = "UPDATE `lord_game_user` SET `nick` = '{$mye}{$i}' WHERE `uid` = $ud";
				$this->mysql->runSql($sql);
				return $wnn.$i;
			}
		}
		$mye = mysqli_real_escape_string($this->mysql->db, $wnn);
		$sql = "UPDATE `lord_game_user` SET `nick` = '{$mye}' WHERE `uid` = $ud";
		$this->mysql->runSql($sql);
		return $wnn;
	}

	//从数据库获取用户信息
	function getUserData( $ud )
	{
		$ud = intval($ud);
		if ( $ud < 1 ) return false;
		$sql = "SELECT `uid`, `cool_num`, `nick`, `sex`, `age`, `word`, `gold`, `coins`, `coupon`, `lottery`, `level`, `exp`, `avatar`, `check_code`, `channel` FROM `lord_game_user` WHERE `uid` = $ud";
		$info = $this->mysql->getLine($sql);
		if ( !$info ) return false;
		$A = $this->getUserAnalyse($ud);
		if ( !$A ) return false;
		$info['trial_count'] = $A['trial_count']+0;
		$info['trial_daily'] = $A['trial_daily']+0;
		$info['play'] = $A['matches']+0;
		$info['win'] = $A['win']+0;
		$info['propDress'] = array('1'=>1);
		$info['propItems'] = $info['realItems'] = array();
		$info['mail_unread'] = 0;
		$info['is_noob'] = 0;
		$info['robot'] = 0;
		$info['fd'] = 0;
		$info['dateid'] = dateid();
		$info['isShowcard'] = 0;
		$info['giveup'] = 0;
		$info['lastSurprise'] = 0;
		return $info;
	}

	//机器人 - 获取数据
	function getRobotData( $ud )
	{
		$sql = "SELECT `uid`, `cool_num`, `nick`, `sex`, `word`, `gold`, `coins`, `coupon`, `lottery`, `level`, `exp`, `avatar`, `channel` FROM `lord_game_robot` WHERE `uid` = $ud";
		$data = $this->mysql->getLine($sql);
		if ( ! is_array($data) ) $data = array();
		if ( $data ) $data['check_code'] = '';
		return $data;
	}
	function getDbUserBuff( $ud )
	{
		$ud = (int)$ud; if ( $ud < 1 ) return array();
		$items = $this->prop->getMine($ud, 2, 1);
		if ( ! is_array($items) ) $items = array();
		foreach ( $items as $miid => $item )
		{
			if ( $item['pd'] == 8 ) {
				return array('8'=>intval($item['num']));
			}
		}
		return array();
	}
	//从数据库获取用户服装信息
	function getDbUserDress( $ud )
	{
		$ud = (int)$ud; if ( $ud < 1 ) return array('1'=>1);
		return $this->prop->getMine($ud, 1);
	}
	//从数据库获取用户道具信息
	function getDbUserItems( $ud, $isNum=0 )
	{
		$items = $this->prop->getMine($ud, 2);
		if ( ! is_array($items) ) $items = array();
		if ( $isNum && ! isset($items['5']) && $this->getUserNumItem($ud, 5) ) {
			$items['5'] = 1;
		}
		return $items;
	}
	//获取用户次数道具
	function getUserNumItem( $ud, $id=5 )
	{
		return intval($this->redis->hget("lord_user_numitem_{$id}", $ud));
	}
	//添加(减少)用户次数道具
	function addUserNumItem( $U, $id=5, $add=1 )
	{
		$ud = $U['uid'];
		$num = $this->redis->hincrby("lord_user_numitem_{$id}", $ud, $add);
		if ( $num ) {
			if ( ! isset($U['propItems'][$id]) || ! $U['propItems'][$id] ) {
				$U['propItems'][$id] = 1;
				setUser($ud, array('propItems'=>$U['propItems']));
			}
		} else {
			$this->redis->hdel("lord_user_numitem_{$id}", $ud);
			$U['propItems'] = $U['realItems'] = $this->getDbUserItems($ud);
			setUser($ud, array('propItems'=>$U['propItems'], 'realItems'=>$U['realItems']));
		}
		$U['propNum'] = $num;
		return $U;
	}

	//物品 - 用户换装
	//ud 			int 	用户ID
	//id 			int 	物品ID
	//return 		arr 	穿戴状态 array('1'=>1);
	function itemDress( $ud, $id )
	{
		$ud = intval($ud);
		$id = intval($id);
		if ( $ud < 1 || $id < 1 ) return array('1'=>1);
		$lock = 'PROP_'.$ud;
		setLock($lock);
		$dress = $this->prop->dressup($ud, $id);
		delLock($lock);
		if ( $dress === false ) {
			gerr("用户换装失败 U=$ud I=$id S=1 E=".json_encode($this->prop->getError()));
			return array('1'=>1);
		}
		setUser($ud, array("propDress"=>$dress));
		return $dress;
	}

	//物品 - 切换状态
	//ud 			int 	用户ID
	//id 			int 	物品ID
	//state 		int 	物品状态 0拥有1启用
	//U 			int 	用户数据 用于识别牌桌ID
	//return 		int 	物品状态 0拥有1启用
	function itemShift( $ud, $id, $state, $U )
	{
		$ud = intval($ud);
		$id = intval($id);
		if ( $ud < 1 || $id < 1 ) return $state;
		$lock = 'PROP_'.$ud;
		setLock($lock);
		$state = $this->prop->shift($ud, $id, 1, $state);
		delLock($lock);
		if ( $state === false ) {
			$error = $this->prop->getError();
			if ( !(isset($error['errno']) && in_array($error['errno'], array(2,4,5,6,7))) )
			gerr("切换道具失败 U=$ud I=$id S=$state E=".json_encode($error));
			return $state;
		}
		$state = $state['state'];
		switch ( $id ) {
			case 5://记牌器
				$td = $U['tableId'];
				$sd = $U['seatId'];
				$table = $this->getTableInfo($td);
				if ( $table ) $this->setTableInfo($td, array("seat{$sd}ps{$id}"=>$state));
				break;
			case 1://预留
				//
				break;
			default://不处理
				//
				break;
		}
		setUser($ud, array("ps{$id}"=>$state));
		return $state;
	}
	//存储用户滞留信息
	function insUserMsg( $ud, $data )
	{
		$ud = (int)$ud;
		$data = (array)$data;
		if ( $ud < 1 || !$data ) return false;
		$sql = "INSERT INTO `lord_user_message` ( `uid`, `msg` ) VALUES ( $ud, '".addslashes(json_encode($data))."' )";
		return bobSql($sql);
	}
	//重发用户滞留消息
	function exeUserMsg( $ud )
	{
		$ud = (int)$ud;
		if ( $ud < 1 ) return false;
		$sql = "SELECT * FROM `lord_user_message` where `uid` = $ud";
		$list = $this->mysql->getData($sql);
		if ( !$list || !is_array($list) ) return true;
		foreach ( $list as $k=>$v )
		{
			$data = json_decode($v['msg'],1);//act/cmd/code/...
			if ( !$data ) continue;
			$data['uid'] = $ud;
			$sceneId = 'USR_MSG_'.$ud.'_'.$k;
			$act = $data['act']; unset($data['act']);
			$params = $data;
			$delay = ( isset($this->confs['time_'.strtolower($act)]) ? $this->confs['time_'.strtolower($act)] : 0 ) * 1000;
			setTimer($sceneId, $act, $params, $delay);
			// $act = $data['act']; unset($data['act']);
			// $params = $data;
			// $delay = $this->confs['time_'.strtolower($act)] * 1000;
			// setEvent($act, $params, $delay);
		}
		bobSql("DELETE FROM `lord_user_message` where `uid` = $ud");
		return true;
	}

	//校验用户乐豆，用户是否可以发放补助的乐豆 返回发豆相关数据
	function checkUserCoins( $ud, $user=array() )
	{
		if ( !$user ) $user = $this->getUserInfo($ud);
		if ( !$user ) return array();
		//V10000前后版本兼容
		//高于V10000版本的用户，不再用这种方式补豆
		if ( $user && $user['vercode'] > 10000 ) {//客户端没有更新之前
			$user['isSend'] = 0;
			$user['sendCoins'] = 0;
			$user['sendCoinsTimesToday'] = 0;
			$user['sendCoinsTimes'] = 0;
			return $user;
		}
		//低于V10000版本的用户，继续使用下面的补豆逻辑
		$fd = $user['fd'];
		$user_trial_count = $this->confs['user_trial_count'];
		$user_trial_daily = $this->confs['user_trial_daily'];
		if ( $user['coins'] < $this->confs['user_trial_count'] && $user['trial_daily'] < $user_trial_daily )
		{
			$newU['trial_count'] = $user['trial_count'] = $user_trial_count;
			$addU['trial_daily'] = $user_trial_count;
			$user['trial_daily'] += $user_trial_count;
			$addU['coins'] = $user_trial_count;
			$user['coins'] += $user_trial_count;
			if ( $fd ) {
				$addU && $this->incUserInfo($ud, $addU, 2);//2补豆
				$newU && setUser($ud, $newU);
			} else {
				$sql = "UPDATE `lord_game_user` SET `coins` = ".$user['coins']."  WHERE uid = $ud";
				bobSql($sql);
			}
			$this->record->money('领取救济', 'coins', $user_trial_count, $ud, $user);
			$sql = "UPDATE `lord_game_analyse` SET `trial_count` = ".$user['trial_count'].", `trial_daily` = `trial_daily` + $user_trial_count WHERE uid = $ud";
			bobSql($sql);
			$data['isSend'] = 1;
			$data['sendCoins'] = $user_trial_count;
		}
		else
		{
			$data['isSend'] = 0;
			$data['sendCoins'] = 0;
		}
		$data['sendCoinsTimesToday'] = intval($user['trial_daily']/$user_trial_count);//今日次数
		$data['sendCoinsTimes'] = intval($user_trial_daily/$user_trial_count);//总次数
		if ( $fd ) {	//在线用户
			$cmd = 5; $code = 1024;//通知用户 补豆，无论是否有豆可补
			$this->sendToFd($fd, $cmd, $code, $data);
		}
		$user = array_merge($user, $data);
		return $user;
	}

	// 获取用户抽奖记录
	function getUserLottery( $ud )
	{
		$ud = intval($ud); if ( $ud < 1 ) return false;
		$lastid = date("Ymd", time() - 8 * 86400);//一周内
		$data_lottery_prizes = array();
		include(ROOT.'/include/data_lottery_prizes.php');
		$prizes = $data_lottery_prizes;
		$data_lottery_prizes = array();
		include(ROOT.'/include/data_lottery_prizesali.php');
		$prizesali = $data_lottery_prizes;
		$sql = "SELECT * FROM `lord_record_lottery` WHERE `uid` = $ud AND `dateid` > $lastid order by id desc limit 48";
		$res = $this->mysql->getData($sql);
		$res = ( $res && is_array($res) ) ? $res : array();
		$list = array();
		foreach ( $res as $k => $v )
		{
			if ( !isset($prizes[$v['prizeid']]) && !isset($prizesali[$v['prizeid']]) ) continue;
			$name = isset($prizes[$v['prizeid']]) ? $prizes[$v['prizeid']]['name'] : $prizesali[$v['prizeid']]['name'];
			$list[$v['ut_create']] = array('id'=>intval($v['id']), 'name'=>$name, 'datetime'=>date("Y-m-d H:i:s",$v['ut_create']), 'ut_create'=>$v['ut_create']);
		}
		if ( $list ) {
			krsort($list);
			$list = array_values($list);
		}
		return $list;
	}

	// 记录用户抽奖历史
	function addUserLottery( $user, $prize )
	{
		$ud = intval($user['uid']); if ( $ud < 1 ) return false;
		$dateid = date("Ymd");
		$sql = "INSERT INTO `lord_record_lottery` "
			." ( `dateid`, `uid`, `cool_num`, `nick`, `prizeid`, `cateid`, `gold`, `coins`, `coupon`, `propid`, `ut_create` ) VALUES "
			." ( $dateid, $ud, ".$user['cool_num'].", '".mysqli_real_escape_string($this->mysql->db,$user['nick'])."', ".$prize['id'].", ".$prize['cateid'].", ".$prize['gold'].", ".$prize['coins'].", ".$prize['coupon'].", ".$prize['propid'].", ".time()." )";
		$res = $this->mysql->runSql($sql);
		return $res ? true : false;
	}

	//旧版-用户加入房间队列
	function addRoomPlayer( $user, $hd=0 )
	{
		$res = $this->redis->ladd($this->key_room_player_.($hd?$hd:HOSTID).'_'.$user['roomId'], $user);
		if ( $res !== -1 ) return $res;
		return false;
	}
	//旧版-尽可能获取房间队列的?个用户(默认3个)
	function getRoomPlayer( $rd, $num=3, $hosts=array() )
	{
		$player = array();
		if ( $hosts )
		{
			foreach ( $hosts as $k => $hd )
			{
				while ( $num && ($user = $this->redis->lpop($this->key_room_player_.$hd.'_'.$rd)) )
				{
					$oldu = $user;
					$ud = $user['uid'];
					$user = $this->getUserInfo($ud);
					if ( !$user ) continue;
					$oldu['fd'] = $user['fd'];
					$oldu['coins'] = $user['coins'];
					$oldu['tttimes'] = $user['tttimes'];
					$player[$ud] = $oldu;
					$num--;
				}
			}
			return $player;
		}
		while ( $num && ($user = $this->redis->lpop($this->key_room_player_.HOSTID.'_'.$rd)) )
		{
			$oldu = $user;
			$ud = $user['uid'];
			$user = $this->getUserInfo($ud);
			if ( !$user ) continue;
			$oldu['fd'] = $user['fd'];
			$oldu['coins'] = $user['coins'];
			$oldu['tttimes'] = $user['tttimes'];
			$player[$ud] = $oldu;
			$num--;
		}
		return $player;
	}
	//旧版-获取房间凑桌队列数
	function countRoomPlayer( $rd, $hosts=array() )
	{
		if ( !is_array($hosts) ) return false;
		if ( $hosts ) {
			$num = 0;
			foreach ( $hosts as $k => $hd )
			{
				$num += $this->redis->llen($this->key_room_player_.$hd.'_'.$rd);
			}
			return $num;
		}
		return $this->redis->llen($this->key_room_player_.HOSTID.'_'.$rd);
	}
	
	//新版-用户加入凑桌
	function addJoinTrio( $U, $rd, $isTry=0)
	{
	    $fd = $U['fd'];
	    $fdArr = explode('_', $fd);
	    $host = $fdArr[0];
		//[ug]添加队列$key 变量 之前版本 $key 没有定义
		$key = $this->key_room_trio_.'_'.$rd;
	    if ( ISLOCAL ) {
		    $this->redis->zadd($key, $U['coins'], $U['uid']);
		} else {
		    $this->redis->zadd($key, $U['coins'], $U['uid'].'_'.$U['ip'].'_'.$host);
		}
		
		if ( !$isTry ) return array();
		$hi = intval(date("Hi"));
		$rate = $hi >= 0 && $hi < 800 ? 1 : ( ($hi>=800 && $hi <=940) || ($hi>=2240 && $hi <=2359) ? 1.5 : 2 );
		$num = intval($this->rooms[$rd]['trio'] * $rate) * 3;
		if ( $this->sumJoinTrio($rd) >= $num ) {
		    return $this->getJoinTrio($rd, $num);
		}
		return array();
	}
	//新版-用户放弃凑桌
	function ddaJoinTrio( $ud, $rd )
	{
	    $lock = $key = $this->key_room_trio_.'_'.$rd;
	    if ( ! setLock($lock, 1) ) return false;
		if ( $uidip_coins = $this->redis->zrevrange($key) ) {
			foreach ( $uidip_coins as $uid_ip => $coins )
			{
				$uidip = explode('_', $uid_ip);
				if ( $uidip[0] == $ud ) {
					$this->redis->zrem($key, $uid_ip);
					break;
				} 
			}
		}
		delLock($lock);
		return true;
	}
	//新版-获取凑桌用户 0==all
	function getJoinTrio( $rd, $num=0)
	{
	    $trios = $ips = $local_players = $other_players = array();
		
		$lock = $key = $this->key_room_trio_.'_'.$rd;
		
		if ( ! setLock($lock, 1) ) return $trios;
		$uidip_coins = $this->redis->zrevrange($key);
		if ($uidip_coins)
		{
			foreach ( $uidip_coins as $uid_ip => $coins )
			{
				$uidip = explode('_', $uid_ip);
				if ( ! isset($uidip[1]) || ! in_array($uidip[1], $ips) ) {
					if ( isset($uidip[1]) )
					{
					    $ips[]= $uidip[1];
					}
					if(!isset($uidip[2]) || $uidip[2] !== HOST)$other_players[$uidip[0]]= intval($uidip[0]);
                    else $local_players[$uidip[0]]= intval($uidip[0]);
                    $this->redis->zrem($key, $uid_ip);
				}
			}
		}
		$trios = array_merge($local_players,$other_players);
		delLock($lock);
		
		return $trios;
	}
	//新版-获取凑桌数量
	function sumJoinTrio( $rd )
	{
		$size = $this->redis->zsize($this->key_room_trio_.'_'.$rd);
	    return $size;
	}

	//从数据库获取用户统计信息
	function getUserAnalyse( $ud )
	{
		$ud = intval($ud);
		if ( $ud < 1 ) return false;
		$sql = "SELECT * FROM `lord_game_analyse` WHERE `uid` = $ud";
		return $this->mysql->getLine($sql);
	}
	//追加牌桌信息队列
	function addTableHistory($td, $data)
	{
		if ( !$td || !$data || !is_array($data) ) return false;
		//$data = array('uid'=>$ud,'cmd'=>$cmd,'code'=>$code,'data'=>$data);
		$index = $this->redis->ladd($this->key_table_history_.$td, json_encode($data));
		if ( $index === -1 ) return false;
		return $index;
	}
	//获取牌桌信息队列
	function getTableHistory($td,$start,$end)
	{
		if ( !$td || !is_int($start) || !is_int($end) ) return false;
		return $this->redis->labc($this->key_table_history_.$td,$start,$end);
	}
	//删除牌桌信息队列
	function delTableHistory( $td )
	{
		if ( !$td ) return false;
		return $this->redis->del($this->key_table_history_.$td);
	}
	//初始化牌桌信息
	function iniTableInfo( $rd, $players, $md=0, $roomMock=0, $wd=0, $gd=0 )
	{
		$room = $this->rooms[$rd];
		$table = $this->ini_table;
		$td = $rd.'_'.join('_',array_keys($players));
		$res = $this->delTableHistory($td);
		if ( !$res ) return false;
		$res = delTimer($td);
		if ( !$res ) return false;
		$table['modelId'] = $md = intval($room['modelId']);
		$table['roomId'] = $rd;
		$table['roomMock'] = $roomMock;
		$table['weekId'] = $wd;
		$table['gameId'] = $gd;
		$table['gamesId'] = $mrwg = $gd ? ($md.'_'.$roomMock.'_'.$wd.'_'.$gd) : '';
		$table['tableId'] = $td;
		$table['rate'] = $room['rate'];
		$table['rateMax'] = $room['rateMax'];
		$table['rake'] = $room['rake'];
		$table['baseCoins']  = $room['baseCoins'];
		$table['limitCoins'] = $room['limitCoins'];
		$table['create'] = $table['update'] = microtime(1);
		$table['version'] = array();
		$sd = 0;
		foreach ( $players as $ud=>$user )
		{
			if ( !$table['hostId'] && $user['fd'] && ($k_ = explode('_', $user['fd'])) ) $table['hostId'] = $k_[0]."_".$k_[1];
			if ( $table['firstShow'] == 4 && $user['isShowcard'] ) $table['firstShow'] = $sd;
			$table['seats'][$ud] = $sd;
			$table["seat{$sd}info"] = $user;//逐步优化掉
			if(!$user['robot'])$table["version"][] = $user['vercode']; 
			//为了能让用户报名淘汰赛后继续普通场游戏
			// if ( $md == 1 ) $newU['modelId'] = $table["seat{$sd}info"]['modelId'] = $md;
			// $newU['modelId'] = $table["seat{$sd}info"]['modelId'] = $md;
			$newU['roomId'] = $table["seat{$sd}info"]['roomId'] = $rd;
			$newU['weekId'] = $table["seat{$sd}info"]['weekId'] = $wd;
			//为了能让用户报名淘汰赛后继续普通场游戏
			// if ( $md == 1 ) $newU['gameId'] = $table["seat{$sd}info"]['gameId'] = $gd;
			// $newU['gameId'] = $table["seat{$sd}info"]['gameId'] = $gd;
			$newU['gamesId'] = $table["seat{$sd}info"]['gamesId'] = $mrwg;
			$newU['tableId'] = $table["seat{$sd}info"]['tableId'] = $td;
			$newU['seatId'] = $table["seat{$sd}info"]['seatId'] = $sd;
			$newU['lastSurprise'] = 0;//
			$table["seat{$sd}fd"] = $user['fd'];			//连接，需要变化
			$table["seat{$sd}uid"] = $ud;					//用户，基础识别
			$table["seat{$sd}coins"] = $user['coins'];		//乐豆，经常变化
			$table["seat{$sd}coupon"] = $user['coupon'];	//乐券，经常变化
			$table["seat{$sd}score"] = $user['score'];		//赛币，需要变化
			$table["seat{$sd}nick"] = $user['nick'];		//昵称，基础识别
			$table["seat{$sd}sex"] = $user['sex'];			//性别，基础识别
			$table["seat{$sd}word"] = $user['word'];		//签名，基础识别
			$table["seat{$sd}dress"] = $user['propDress'];	//服装，需要变化
			$table["seat{$sd}items"] = $user['propItems'];	//道具，需要变化
			$table["seat{$sd}realItems"] = isset($user['realItems']) ? $user['realItems'] : $user['propItems'];	//真实道具，需要变化
			// $table["seat{$sd}acces"] = $user['propAcces'];	//配饰，需要变化
			$table["seat{$sd}buff"] = in_array($rd,array(1006,1011))?array():$user['buff'];		//增益，经常变化
			$table["seat{$sd}channel"] = $user['channel'];	//渠道，基础识别
			$table["seat{$sd}vercode"] = $user['vercode'];	//版本，基础识别
			$table["seat{$sd}robot"] = $user['robot'];		//假人，基础识别
			$table["seat{$sd}show"] = $user['isShowcard'];	//明牌，需要变化
			$table["seat{$sd}giveup"] = $user['giveup'];	//弃赛，需要变化
			$table["seat{$sd}state"] = 17;					//状态，经常变化，17:SYS开始发牌
			$table["seat{$sd}task"] = array();				//用户，本桌牌局任务数据
			$table["seat{$sd}tttimes"] = isset($user['tttimes']) ? $user['tttimes'] : 0;	//用户，今日牌局任务完成次数
			$table["seat{$sd}ttdone"] = 0;					//用户，本桌牌局任务完成情况
			$table["seat{$sd}ttcoupon"] = 0;				//用户，本桌牌局任务获得奖券
			$res = setUser($ud, $newU);
			$sd++;
		}
		if ( !$table['hostId'] ) $table['hostId'] = HOSTID;
		$table['turnSeat'] = $table['firstShow'];
		$res = $this->setTableInfo($td, $table);
		if ( !$res ) return false;
		return $table;
	}
	//设置牌桌信息
	function setTableInfo( $td, $info )
	{
		return $this->redis->hmset($this->key_table_info_.$td, $info);
	}
	//获取全部牌桌信息
	function getTableInfo( $td )
	{
		$table = $this->redis->hgetall($this->key_table_info_.$td);
		if ( !$table ) return false;
		if ( count($table) < 66 ) {
			gerr("异常牌桌清理[$td] ".json_encode($table));
			$this->delTableInfo($td);
			delTimer($td, isset($table['hostId']) ? $table['hostId'] : 0);
			$this->delTableHistory($td);
			return false;
		}
		return $table;
	}
	//获取牌桌上某席位用户在牌桌的关键数据
	function getTableUser( $table, $sd )
	{
		if ( isset($table["seat{$sd}info"]) ) return $table["seat{$sd}info"];
		$info = array();
		foreach ( $table as $k => $v )
		{
			if ( strpos($k, "seat{$sd}") === 0 ) {
				$info[str_replace("seat{$sd}", '', $k)] = $v;
			}
		}
		$info['tableId'] = $table['tableId'];
		$info['modelId'] = $table['modelId'];
		$info['roomId'] = $table['roomId'];
		$info['seatId'] = $sd;
		return $info;
	}
	// 增减 原子操作
	function incTableInfo( $td, $info )
	{
		foreach ( $info as $k => $v )
		{
			$res = $this->redis->hincrby($this->key_table_info_.$td, $k, intval($v));
			$info[$k] = $res;
		}
		return $info;
	}
	//删除全部牌桌信息
	function delTableInfo( $td )
	{
		return $this->redis->del($this->key_table_info_.$td);
	}
	//更新桌子状态
	function setTableState( $td, $state )
	{
		if ( !$td ) return false;
		return $this->redis->hset($this->key_table_info_.$td,'state', $state);
	}
	//设定牌桌地主位
	function setTableLordto( $td, $seatId )
	{
		if ( !$td || !in_array($seatId,range(0,2)) ) return false;
		return $this->redis->hset($this->key_table_info_.$td,'lordSeat',$seatId);
	}
	//更新牌桌轮权
	function setTableTurnto( $td, $seatId )
	{
		if ( !$td || !in_array($seatId,range(0,2)) ) return false;
		return $this->redis->hset($this->key_table_info_.$td,'turnSeat',$seatId);
	}
	//获取牌桌轮权
	function getTableTurnto( $td )
	{
		if ( !$td ) return false;
		return $this->redis->hget($this->key_table_info_.$td,'turnSeat');
	}
	//设置牌桌首个明牌席位
	function setTableFirstShow( $td, $seatId )
	{
		if ( !$td || !in_array($seatId,range(0,2)) ) return false;
		return $this->redis->hset($this->key_table_info_.$td,'firstShow',$seatId);
	}
	//获取牌桌首个明牌席位
	function getTableFirstShow( $td )
	{
		if ( !$td ) return false;
		return $this->redis->hget($this->key_table_info_.$td,'firstShow');
	}
	//设置牌桌倍率
	function setTableRate( $td, $rate )
	{
		if ( !$td ) return false;
		return $this->redis->hset($this->key_table_info_.$td,'rate',$rate);
	}
	//设置牌桌叫抢临时倍率
	function setTableRate_( $td, $rate )
	{
		if ( !$td ) return false;
		return $this->redis->hset($this->key_table_info_.$td,'rate_',$rate);
	}
	//更新座位明牌状态
	function setSeatShow( $td, $seatId )
	{
		if ( !$td || !in_array($seatId,range(0,2)) ) return false;
		return $this->redis->hset($this->key_table_info_.$td,'seat'.$seatId.'show',1);
	}
	//更新座位用户状态
	function setSeatState( $td, $seatId, $state )
	{
		if ( !$td || !in_array($seatId,range(0,2)) ) return false;
		return $this->redis->hset($this->key_table_info_.$td,'seat'.$seatId.'state',$state);
	}
	//更新座位用户手牌
	function setSeatCards($td,$seatId,$cards)
	{
		if ( !$td || !in_array($seatId,range(0,2)) ) return false;
		return $this->redis->hset($this->key_table_info_.$td,'seat'.$seatId.'cards',$cards);
	}
	//更新座位用户抢叫地主的倍率
	function setSeatRate($td,$seatId,$rate)
	{
		if ( !$td || !in_array($seatId,range(0,2)) ) return false;
		return $this->redis->hset($this->key_table_info_.$td,'seat'.$seatId.'rate',$rate);
	}

	//处理某人准备再来一局，是否是明牌准备
	function setSeatReady( $table, $seatId, $isShowcard )
	{
		if ( !$table || !is_array($table) || !in_array($seatId,range(0,2)) ) return false;
		$rd = $table['roomId'];
		$td = $table['tableId'];
		$newT["seat{$seatId}state"] = $table["seat{$seatId}state"] = 16;//已经准备
		if ( $isShowcard )
		{
			$newT["seat{$seatId}show"] = $table["seat{$seatId}show"] = 1;//明牌开始
			if ( $table['firstShow'] == 4 )
			{
				$newT['firstShow'] = $table['firstShow'] = $seatId;
				$newT['turnSeat']  = $table['turnSeat']  = $seatId;
				$newT['rate']      = $table['rate']      = 75;//$this->rooms[$rd]['rate'] * $this->confs['rate_showcard'];
			}
		}
		$res = $this->setTableInfo($td, $newT);
		return $table;
	}
	//叫地主，返回最新牌桌数据
	function call_lord( $table, $doLord )
	{
		if ( !$table || !is_array($table) || !in_array($doLord,array(1,2)) ) return false;
		$td = $table['tableId'];
		$seatId = $table['turnSeat'];
		$rate_ = $table['rate_'];
		$seat_rate = $rate_ * $this->confs['rate_belord'];
		$is_want = intval($doLord == 1);//1叫抢/2不要
		$seat_rate = $seat_rate * $is_want;
		$res = $this->setSeatRate($td,$seatId,$seat_rate);
		if ( !$res ) return false;
		$table["seat{$seatId}rate"] = $seat_rate;
		//临时叫抢倍率变更
		if ( $seat_rate > $rate_ )
		{
			$res = $this->setTableRate_($td,$seat_rate);
			if ( !$res ) return false;
			$table['rate_'] = $seat_rate;
		}
		//只要有人叫，马上进入抢地主阶段
		if ( $is_want )
		{
			$state = 5;//抢地主阶段
			$res = $this->setTableState($td,$state);
			if ( !$res ) return false;
			$table['state'] = $state;
		}
		return $this->check_lord($table);
	}
	//抢地主，返回最新牌桌数据
	function grab_lord( $table, $doLord )
	{
		if ( !$table || !is_array($table) || !in_array($doLord,array(1,2)) ) return false;
		$td = $table['tableId'];
		$seatId = $table['turnSeat'];
		$rate_ = $table['rate_'];//临时叫抢倍率
		$seat_rate = $rate_ * $this->confs['rate_grablord'];
		$is_want = intval($doLord == 1);//1叫抢/2不要
		$seat_rate = $seat_rate * $is_want;
		$res = $this->setSeatRate($td,$seatId,$seat_rate);
		if ( !$res ) return false;
		$table["seat{$seatId}rate"] = $seat_rate;
		//临时叫抢倍率变更
		if ( $seat_rate > $rate_ )
		{
			$res = $this->setTableRate_($td,$seat_rate);
			if ( !$res ) return false;
			$table['rate_'] = $seat_rate;
		}
		return $this->check_lord($table);
	}
	//检查地主是否确定，返回最新牌桌数据
	function check_lord( $table )
	{
		if ( !$table || !is_array($table) ) return false;
		$td = $table['tableId'];
		$rate = $table['rate'];
		$seat_rates = array(
			'seat0rate'=>$table['seat0rate'],
			'seat1rate'=>$table['seat1rate'],
			'seat2rate'=>$table['seat2rate'],
		);
		$count = array_count_values($seat_rates);
		$new = array();
		$is_lordto = false;//是否确定地主
		//还有人没发话：轮转向下一位
		if ( isset($count['-1']) )
		{
			$seat = $this->getSeatNext($table['turnSeat']);
			$new['turnSeat'] = $seat;
		}
		//三人放弃，且无人明牌：轮转向空位，还原牌桌信息，用于重新发牌
		elseif ( isset($count['0']) && $count['0'] == 3 && $table['firstShow'] == 4 )
		{
			$seat = 4;
			$new['turnSeat'] = $seat;
			$new['state'] = 3;//发牌明牌阶段
			$new['lordCards'] = array();
			$new['seat0rate'] = $new['seat1rate'] = $new['seat2rate'] = -1;
			$new['seat0cards'] = $new['seat1cards'] = $new['seat2cards'] = array();
		}
		//三人放弃，且有人明牌：轮转向明牌的座位，确定地主，改变状态，处理牌组
		elseif ( isset($count['0']) && $count['0'] == 3 && $table['firstShow'] != 4 )
		{
			$seat = $table['firstShow'];
			$new['turnSeat'] = $seat;
			$new['lordSeat'] = $seat;
			$new['state'] = 6;//出牌过牌阶段
			$new["seat{$seat}cards"] = Card::cardsSort(array_merge($table["seat{$seat}cards"], $table['lordCards']), 1);
		}
		//两人放弃，或都抢弃过：轮转最大倍率座位，确定地主，改变状态，处理牌组
		elseif ( isset($count['0']) && $count['0'] == 2  || !in_array($rate, $seat_rates))
		{
			$seat_rate = max($seat_rates);
			$seat = intval(substr(array_search($seat_rate,$seat_rates),4,1));
			$new['turnSeat'] = $seat;
			$new['lordSeat'] = $seat;
			$new['state'] = 6;//出牌过牌阶段
			$new["seat{$seat}cards"] = Card::cardsSort(array_merge($table["seat{$seat}cards"], $table['lordCards']), 1);
		}
		//其他情形：轮转向下一个未放弃的座位
		else
		{
			$seat = $this->getSeatNext($table['turnSeat']);
			if ( !$table["seat{$seat}rate"] )
			{
				$seat = $this->getSeatNext($seat);
			}
			$new['turnSeat'] = $seat;
		}
		$res = $this->setTableInfo($td, $new);
		if ( !$res ) return false;
		return array_merge($table,$new);
	}
	//获取下一个席位
	function getSeatNext( $seatId )
	{
		if ( !in_array($seatId,range(0,2)) ) return 0;
		return --$seatId == -1 ? 2 : $seatId;
	}


	//追加一条用户的缓发队列
	function addUserQueue( $ud, $send )
	{
		if ( !$ud ) return false;
		return $this->redis->ladd($this->key_user_queue_.$ud, $send);
	}
	//取出首条用户的缓发队列
	function popUserQueue($ud)
	{
		if ( !$ud ) return false;
		return $this->redis->lpop($this->key_user_queue_.$ud);
	}
	//清空当前用户的缓发队列
	function delUserQueue($ud)
	{
		if ( !$ud ) return false;
		return $this->redis->del($this->key_user_queue_.$ud);
	}

	//获取模式最新赛场
	function getModelRoomWeekGameLast( $md, $rd, $wd, $isNew=0, $gd=1 )
	{
		if ( $md != 1 || !$wd ) return false;
		$now = time();
		$day0 = strtotime(date('Y-m-d'));
		$wday = date('N');	//周n[1-7]
		$game = array();
		if ( !$isNew && ($games = $this->getModelGames($md)) ) {
			foreach ( $games as $k=>$v )
			{
				if ( $md == $v['modelId'] && ($rd ? ($rd==$v['roomId']) : true) && $wd == $v['weekId'] ) $game[$v['gameId']] = $v;
			}
			if ( $game ) {
				krsort($game);
				$game = reset($game);
				$week = $this->getModelWeek($md, $rd, $wd);
				if ( !$week ) return false;
				$game['thisWeekRank'] = $week ? $week['weekRank'] : array();
				$prev = $this->getModelWeekPrev($md, $rd, $wd);
				$game['lastWeekRank'] = $prev ? $prev['weekRank'] : array();
				$game['weekPool'] = $week['weekPool'];
			}
		}
		if ( !$game )
		{
			//预留 此处考虑加锁
			$room = $this->getModelRoom($md, $rd, 1);
			if ( !$room ) return false;
			$week = $this->getModelWeek($md, $rd, $wd, 1, $room);
			if ( !$week ) return false;
			$week['thisWeekRank'] = $week['weekRank'];
			$prev = $this->getModelWeekPrev($md, $rd, $wd);
			$week['lastWeekRank'] = $prev ? $prev['weekRank'] : array();
			$game = $this->newModelGame($room, $week, $gd);
			if ( !$game ) return false;
			$game['weekPool'] = $week['weekPool'];
		}
		//判断赛场是否开启
		$setting = $game['gameOpenSetting'];
		$setting = ($setting && is_array($setting)) ? $setting : array();
		$game['gameIsOpen'] = 0;
		foreach ( $setting as $k=>$v )
		{
			$v = explode("|", $v);
			if ( count($v) != 3 ) break;
			$start = explode(" ", $v[0]);
			$dateStart = strtotime($start[0].' 00:00:00');
			$todayStart = strtotime(date("Y-m-d ".$start[1]));
			$end = explode(" ", $v[1]);
			$dateEnd =  strtotime($end[0].' 23:59:59');
			$todayEnd = strtotime(date("Y-m-d ".$end[1]));
			$weeks = $v[2];
			if ( $day0 > $dateStart && $day0 < $dateEnd && $now > $todayStart && $now < $todayEnd && ($weeks ? (strpos($weeks,$wday) !== false) : 1) )
			{
				$game['gameIsOpen'] = 1;
				break;
			}
		}
		return $game;
	}

	//获取模式所有赛场
	function getModelGames( $md )
	{
		if ( $md != 1 ) return false;
		return $this->redis->hgetall($this->key_model_games_.$md);
	}

	//初始化一个赛场
	function newModelGame($room,$week,$gd=1)
	{
		$md = $week['modelId'];
		$mrwg = $week['modelId'].'_'.$week['roomId'].'_'.$week['weekId'].'_'.$gd;
		$game = array(
			'gamesId' => $mrwg,
			'modelId' => $week['modelId'],
			'roomId' => $week['roomId'],
			'weekId' => $week['weekId'],
			'gameId' => $gd,
			'gameLevel' => $room['gameLevel'],
			'gamePool' => 0,
			'gamePerson' => 0,
			'gamePlay' => 0,
			'gameStart' => 0,
			'gameCreate' => time(),
		);
		$game = array_merge($room, $week, $game);
		$game['thisWeekRank'] = array_slice($game['thisWeekRank'], 0, 9);
		$game['lastWeekRank'] = array();
		unset($game['weekRank']);
		$res = $this->redis->hset($this->key_model_games_.$md, $mrwg, $game);
		if ( !$res ) return false;
		return $game;
	}

	//获取赛事某个房间
	function getModelRoom( $md, $rd, $isNew=0 )
	{
		if ( $md != 1 ) return false;
		if ( $rd ) return $this->redis->hget($this->key_model_rooms_.$md, $md.'_'.$rd);
		$rooms = $this->getModelRooms($md, $isNew);
		if ( !$rooms ) return false;
		krsort($rooms);
		$i = 1;
		foreach ( $rooms as $roomsId=>$v )
		{
			if ( $i ==1 )
			{
				$rand['1'] = $roomsId;
			}
			else if ( $i == 2 )
			{
				$rand['2'] = $rand['3'] = $rand['4'] = $roomsId;
			}
			else
			{
				$rand['5'] = $rand['6'] = $rand['7'] = $rand['8'] = $roomsId;
			}
			$i++;
		}
		return $rooms['1_1004'];//固定为初级竞技场
		return $rooms[$rand[mt_rand(2,8)]];
	}

	//获取赛事所有房间
	function getModelRooms($md,$isNew=0)
	{
		if ( $md != 1 ) return false;
		$rooms = $this->redis->hgetall($this->key_model_rooms_.$md);
		if ( $rooms ) return $rooms;
		if ( !$isNew ) return false;
		//载入房间配置
		$sql = "SELECT * FROM `lord_model_rooms` where `modelId` = $md";
		$rooms = $this->mysql->getData($sql);
		if ( $rooms )
		{
			$data = array();
			foreach ( $rooms as $k=>$v )
			{
				$v['gameOpenSetting']= json_decode($v['gameOpenSetting'],1);
				$v['gamePrizeCoins'] = json_decode($v['gamePrizeCoins'],1);
				$v['gamePrizeCoupon'] = json_decode($v['gamePrizeCoupon'],1);
				$v['gamePrizePoint'] = json_decode($v['gamePrizePoint'],1);
				$v['gamePrizeProps'] = json_decode($v['gamePrizeProps'],1);
				$v['weekPrizeCoins'] = json_decode($v['weekPrizeCoins'],1);
				$v['weekPrizeProps'] = json_decode($v['weekPrizeProps'],1);
				$data[$v['roomsId']] = $v;
			}
			$rooms = $data;
			$res = $this->redis->hmset($this->key_model_rooms_.$md,$rooms);
			if ( !$res )
			{
				return false;
			}
		}
		elseif ( isset($this->rooms) && $this->rooms && is_array($this->rooms) )
		{
			$rooms_ = $this->rooms;
			$rooms = array();
			foreach ( $rooms_ as $k=>$v )
			{
				$rooms[$v['roomsId']] = $v;
			}
			//加事务锁	互斥锁
			$res = setLock(__FUNCTION__,1);
			if ( !$res )
			{
				return $rooms;//直接返回房间配置
			}
			$res = $this->redis->hmset($this->key_model_rooms_.$md,$rooms);
			if ( !$res )
			{
				//解事务锁
				$res = delLock(__FUNCTION__);
				return false;
			}
			$sql = "INSERT INTO `lord_model_rooms` ( `roomsId`, `modelId`, `roomId`, `roomReal`, `baseCoins`, `rate`, `limitCoins`, `rake`, `enterLimit`, `enterLimit_`, `gameName`, `gameLevel`, `gameScoreIn`, `gameScoreOut`, `gameEndTime`, `gameWinner`, `gameRanknum`, `gameBombAdd`, `gameWaitFirst`, `gameWaitOther`, `gameOpen`, `gameOpenSetting`, `gamePersonAll`, `gameInCoins`, `gameCancelTime`, `gameCancelPerson`, `gamePrizeCoins`, `gamePrizeCoupon`, `gamePrizePoint`, `gamePrizeProps`, `gameRule`, `weekPeriod`, `weekPrizeCoins`, `weekPrizeProps`, `create_time`, `update_time` ) VALUES";
			foreach ( $rooms as $k=>$v )
			{
				$sql.= " ( '".$v['roomsId']."', ".$v['modelId'].", ".$v['roomId'].", ".$v['roomReal'].", ".$v['baseCoins'].", ".$v['rate'].", ".$v['limitCoins'].", ".$v['rake'].", ".$v['enterLimit'].", ".$v['enterLimit_'].", '".$v['gameName']."', ".$v['gameLevel'].", ".$v['gameScoreIn'].", ".$v['gameScoreOut'].", ".$v['gameEndTime'].", ".$v['gameWinner'].",  ".$v['gameRanknum'].", ".$v['gameBombAdd'].", ".$v['gameWaitFirst'].", ".$v['gameWaitOther'].", '".$v['gameOpen']."', '".addslashes(json_encode($v['gameOpenSetting']))."', ".$v['gamePersonAll'].", ".$v['gameInCoins'].", ".$v['gameCancelTime'].", ".$v['gameCancelPerson'].", '".addslashes(json_encode($v['gamePrizeCoins']))."', '".addslashes(json_encode($v['gamePrizeCoupon']))."', '".addslashes(json_encode($v['gamePrizePoint']))."', '".addslashes(json_encode($v['gamePrizeProps']))."', '".$v['gameRule']."', ".$v['weekPeriod'].", '".addslashes(json_encode($v['weekPrizeCoins']))."', '".addslashes(json_encode($v['weekPrizeProps']))."', NOW(), NOW() ),";
			}
			$res = $this->mysql->runSql(trim($sql,','));
			if ( !$res )
			{
				echo $sql."\n";
				//解事务锁
				$res = delLock(__FUNCTION__);
				return false;
			}
			//解事务锁
			$res = delLock(__FUNCTION__);
		}
		else
		{
			return false;
		}
		return $rooms;
	}
	//获取赛事房间所有周赛数据
	function getModelWeeks( $md, $rd )
	{
		if ( $md != 1 ) return false;
		$weeks = $this->redis->hgetall($this->key_model_weeks_.$md);
		if ( !$weeks ) return array();
		foreach ( $weeks as $k=>$v )
		{
			if ( $v['roomId'] && $v['roomId'] != $rd ) unset($weeks[$k]);
		}
		return $weeks;
	}

	//设置赛事周赛数据
	function setModelWeek( $md, $rd, $wd, $week )
	{
		if ( $md != 1  || !$wd ) return false;
		$mrw = $md."_".$rd."_".$wd;
		return $this->redis->hset($this->key_model_weeks_.$md, $mrw, $week);
	}

	//获取赛事某个周赛
	function getModelWeek( $md, $rd, $wd, $isNew=0, $room=array() )
	{
		if ( $md != 1  || !$wd ) return false;
		$mrw = $md."_".$rd."_".$wd;
		$week = $this->redis->hget($this->key_model_weeks_.$md, $mrw);
		if ( $week ) return $week;
		if ( !$isNew ) return false;
		//预留 此处考虑加锁
		$week = $this->mysql->getLine("SELECT * FROM `lord_model_weeks` where `weeksId` = '$mrw'");
		if ( $week ) {
			unset($week['id']);
			$week['modelId'] = intval($week['modelId']);
			$week['roomId'] = intval($week['roomId']);
			$week['weekId'] = intval($week['weekId']);
			$week['weekRank']       = json_decode($week['weekRank'], 1);
			$week['weekPrizeCoins'] = json_decode($week['weekPrizeCoins'], 1);
			$week['weekPrizeProps'] = json_decode($week['weekPrizeProps'], 1);
			$res = $this->redis->hset($this->key_model_weeks_.$md, $mrw, $week);
			if ( !$res ) return false;
			return $week;
		}
		$date = str_split($wd,2);
		$weekStart = strtotime($date[0].$date[1].'-'.$date[2].'-'.$date[3].' 00:00:00');
		$week = array(
			'weeksId' => $mrw,
			'modelId' => $md,
			'roomId' => $rd,
			'weekId' => $wd,
			'weekPool' => 0,
			'weekRank' => array(),
			'weekPrizeCoins' => array(),
			'weekPrizeProps' => array(),
			'weekStart'=> $weekStart,
			'weekEnd'=> $weekStart+intval($room['weekPeriod']*86400)-1,
		);
		$res = $this->redis->hset($this->key_model_weeks_.$md, $mrw, $week);
		if ( !$res ) return false;
		$sql = "INSERT INTO `lord_model_weeks` ( `weeksId`, `modelId`, `roomId`, `weekId`, `weekPool`, `weekRank`, `weekPrizeCoins`, `weekPrizeProps`, `weekStart`, `weekEnd` ) VALUES ('$mrw',$md,$rd,$wd,0,'[]','[]','[]',".$week['weekStart'].",".$week['weekEnd'].")";
		$res = $this->mysql->runSql($sql);
		if ( !$res ) {
			$this->redis->hdel($this->key_model_weeks_.$md, $mrw);
			return gerr("MYSQL->runSql($sql)");
		}
		return $week;
	}
	//获取某周赛的上个周赛
	function getModelWeekPrev( $md, $rd, $wd )
	{
		if ( $md != 1 || !$wd ) return false;
		$weeks = $this->redis->hgetall($this->key_model_weeks_.$md);
		if ( !$weeks ) return false;
		$list = array();
		foreach ( $weeks as $week )
		{
			$list[$week['weekId']] = $week;
		}
		// if ( !isset($list[$wd]) ) return false;
		krsort($list);
		foreach ( $weeks as $wd_ => $week )
		{
			if ( $wd_ < $wd ) return $week;
		}
		return false;
	}
	//入库周赛信息
	function insModelWeek( $md, $rd, $wd, $week )
	{
		if ( $md != 1 || !$wd || !$week || !is_array($week) ) return false;
		$mrw = $md.'_'.$rd.'_'.$wd;
		$data = $this->mysql->getLine("SELECT * FROM `lord_model_weeks` where `weeksId` = '$mrw'");
		if ( $data ) {
			$sql = "UPDATE `lord_model_weeks` SET `weekPool` = ".$week['weekPool'].", `weekRank` = '".addslashes(json_encode($week['weekRank']))."', `weekPrizeCoins` = '".addslashes(json_encode($week['weekPrizeCoins']))."', `weekPrizeProps` = '".addslashes(json_encode($week['weekPrizeProps']))."' WHERE `id` = ".$data['id'];
			bobSql($sql);
		} else {
			$sql = "INSERT INTO `lord_game_weeks` ( `weeksId`, `modelId`, `roomId`, `weekId`, `weekPool`, `weekRank`, `weekPrizeCoins`, `weekPrizeProps` ) VALUES ( '".$mrw."', ".$md.", ".$rd.", ".$wd.", ".$week['weekPool'].", '".addslashes(json_encode($week['weekRank']))."', '".addslashes(json_encode($week['weekPrizeCoins']))."', '".addslashes(json_encode($week['weekPrizeProps']))."' )";
			bobSql($sql);
		}
		return true;
	}

	//获取某个赛事信息
	function getModelGame( $md, $rd, $wd, $gd )
	{
		if ( $md != 1 || !$wd || !$gd ) return false;
		$mrwg = $md.'_'.$rd.'_'.$wd.'_'.$gd;
		return $this->redis->hget($this->key_model_games_.$md, $mrwg);
	}
	//重设某个赛事信息
	function setModelGame($md,$rd,$wd,$gd,$game)
	{
		if ( $md != 1 || !$wd || !$gd || !$game || !is_array($game) )
		{
			return false;
		}
		$mrwg = $md.'_'.$rd.'_'.$wd.'_'.$gd;
		return $this->redis->hset($this->key_model_games_.$md,$mrwg,$game);
	}
	//删除赛场信息
	function delModelGame($md,$mrwg)
	{
		if ( $md != 1 || !$mrwg )
		{
			return false;
		}
		return $this->redis->hdel($this->key_model_games_.$md,$mrwg);
	}
	//入库赛场信息，并清空redis数据
	function insModelGame($md,$rd,$wd,$gd,$game)
	{
		if ( $md != 1 || !$wd || !$gd || !$game || !is_array($game) )
		{
			return false;
		}
		$mrwg = $game['gamesId'];
		$sql = "INSERT INTO `lord_model_games` ( `gamesId`, `modelId`, `roomId`, `weekId`, `gameId`, `gameLevel`, `gamePool`, `gamePerson`, `gamePlay`, `gameScore`, `gamePrizeCoins`, `gamePrizeCoupon`, `gamePrizePoint`, `gamePrizeProps`, `gameStart`, `gameOver` ) VALUES ( '$mrwg', $md, $rd, $wd, $gd, ".$game['gameLevel'].", ".$game['gamePool'].", ".$game['gamePerson'].",  ".$game['gamePlay'].", '".addslashes(json_encode($game['gameScore']))."', '".addslashes(json_encode($game['gamePrizeCoins']))."', '".addslashes(json_encode($game['gamePrizeCoupon']))."', '".addslashes(json_encode($game['gamePrizePoint']))."', '".addslashes(json_encode($game['gamePrizeProps']))."', ".$game['gameStart'].", ".$game['gameOver']." )";
		$res = $this->mysql->runSql($sql);
		if ( !$res )
		{
			echo $sql."\n";
			return false;
		}
		else
		{
			return $this->delModelGame($md,$mrwg);
		}
	}
	//获取赛场全部用户
	function getModelGamePlayAll($mrwg)
	{
		if ( !$mrwg )
		{
			return false;
		}
		return $this->redis->hgetall($this->key_model_gameplay_.$mrwg);
	}
	//获取赛场用户
	function getModelGamePlay($mrwg,$ud)
	{
		if ( !$mrwg || !$ud )
		{
			return false;
		}
		return $this->redis->hget($this->key_model_gameplay_.$mrwg,$ud);
	}
	//加入赛场用户
	function addModelGamePlay( $game, $user )
	{
		if ( !$game || !is_array($game) || !$user || !is_array($user) ) return false;
		$now = time();
		$ud = $user['uid'];
		$md = $game['modelId'];
		$rd = $game['roomId'];
		$roomReal = $game['roomReal'];
		$wd = $game['weekId'];
		$gd = $game['gameId'];
		$mrwg = $game['gamesId'];
		$playid = $mrwg.'_'.$ud;
		$new['gameplayId']= $user['gameplayId']= $playid;
		$new['gamesId']  = $user['gamesId']  = $mrwg;
		$new['modelId']  = $user['modelId']  = $md;
		$new['roomId']   = $user['roomId']   = $roomReal;//用户信息里面存储实际房间id
		$new['weekId']   = $user['weekId']   = $wd;
		$new['gameId']   = $user['gameId']   = $gd;
		$new['joinTime'] = $user['joinTime'] = $now;
		$new['score']    = $user['score']    = $game['gameScoreIn'];
		$user['coins'] -= $game['gameInCoins'];
		$res = setUser($ud,$new);
		$addU['coins'] = $game['gameInCoins'] * -1;
		$addU && $this->incUserInfo($ud, $addU);
		$this->record->money('竞技报名', 'coins', $game['gameInCoins'], $ud, $user);
		$gameplay = array(
			'roomId'=>$rd,//参与情况里面使用伪造roomId
			'deadTime' => 0,
			'overTime' => 0,
			'create_time' => $now,
			'update_time' => $now,
		);
		$gameplay = array_merge($user,$gameplay);
		$res = $this->redis->hset($this->key_model_gameplay_.$mrwg,$ud,$gameplay);
		$game = $this->getModelGame($md,$rd,$wd,$gd);
		$game['gamePool'] += $game['gameInCoins'] * 0.1;//>=V10500
		$game['gamePerson']++;
		$game['gamePlay']++;
		$res = $this->setModelGame($md,$rd,$wd,$gd,$game);
		$week = $this->getModelWeek($md,$rd,$wd);
		$week['weekPool'] += $game['gameInCoins'] * 0.1;//>=V10500
		$res = $this->setModelWeek($md,$rd,$wd,$week);
		return $game;
	}
	//入库赛场用户，并清空redis数据
	function insModelGamePlay($mrwg,$gamePlayAll)
	{
		if ( !$mrwg || !$gamePlayAll || !is_array($gamePlayAll) ) return false;
		$mrwg_ = explode('_', $mrwg);
		$md = $mrwg_[0];
		$rd = $mrwg_[1];
		$wd = $mrwg_[2];
		$gd = $mrwg_[3];
		$sql = "INSERT INTO `lord_model_gameplay` (`gameplayId`, `modelId`, `roomId`, `weekId`, `gameId`, `uid`, `cool_num`, `joinTime`, `deadTime`, `overTime`, `coins`, `score`, `create_time`, `update_time`) VALUES ";
		foreach ( $gamePlayAll as $k=>$v )
		{
			if ( $v['gameplayId'] && $v['uid'] ) {
				$sql.="('".$v['gameplayId']."', $md, $rd, $wd, $gd, ".$v['uid'].", ".$v['cool_num'].", ".$v['joinTime'].", ".$v['deadTime'].", ".$v['overTime'].", ".$v['coins'].", ".$v['score'].", ".$v['create_time'].", ".$v['update_time']."),";
				$res = $this->redis->hdel($this->key_model_gameplay_.$mrwg,$v['uid']);
			} else {
				gerr("[badplayer] ".json_encode($v));
			}
		}
		$res = $this->mysql->runSql(trim($sql,','));
		if ( !$res ) return false;
		return true;
	}
	//设置参赛用户
	function setModelGamePlay( $mrwg, $ud, $gameplay )
	{
		if ( !$mrwg || !$ud || !$gameplay || !is_array($gameplay) ) return false;
		return $this->redis->hset($this->key_model_gameplay_.$mrwg, $ud, $gameplay);
	}
	//更新参赛用户
	function updModelGamePlay($mrwg,$ud,$data)
	{
		if ( !$mrwg || !$ud || !$data || !is_array($data) ) return false;
		$gameplay = $this->getModelGamePlay($mrwg,$ud);
		if ( !$gameplay ) return false;
		$gameplay = array_merge($gameplay,$data);
		return $this->setModelGamePlay($mrwg,$ud,$gameplay);
	}
	//删除参赛用户
	function delModelGamePlay( $game, $user )
	{
		if ( !$game || !is_array($game) || !$user || !is_array($user) ) return false;
		$ud = $user['uid'];
		$md = $game['modelId'];
		$rd = $game['roomId'];
		$wd = $game['weekId'];
		$gd = $game['gameId'];
		$mrwg = $game['gamesId'];
		$playid = $mrwg.'_'.$ud;
		$coins = $game['gameInCoins'];
		$user = $this->getUserInfo($ud);
		if ( $user )
		{
			if ( isset($user['fd']) && isset($user['coins']) && $user['fd'] ) {
				$newU = array('gameplayId'=>'', 'gamesId'=>'', 'modelId'=>0, 'roomId'=>0, 'weekId'=>0, 'gameId'=>0, 'joinTime'=>0, 'giveup'=>0, 'score'=>0);
				$res = setUser($ud, $newU);
				$addU['coins'] = $coins;
		 		$res = $this->incUserInfo($ud, $addU);
			} elseif ( $user['robot'] ) {
				$res = $this->desRobot($ud, __LINE__);
			} else {
				$sql = "UPDATE `lord_game_user` SET `coins` = `coins` + $coins WHERE `uid` = $ud";
				bobSql($sql);
				$res = $this->desUserInfo($ud, $user, __LINE__);
			}
		}
		$this->record->money('竞技取消', 'coins', $game['gameInCoins'], $ud, $user);
		$res = $this->redis->hdel($this->key_model_gameplay_.$mrwg, $ud);
		$week = $this->getModelWeek($md,$rd,$wd);
		$week['weekPool'] -= $game['gameInCoins'] * 0.1;//>=V10500
		$res = $this->setModelWeek($md,$rd,$wd,$week);
		$game = $this->getModelGame($md,$rd,$wd,$gd);
		$game['gamePool'] -= $game['gameInCoins'] * 0.1;//>=V10500
		$game['gamePerson']--;
		$game['gamePlay']--;
		return $this->setModelGame($md,$rd,$wd,$gd,$game);
	}
	//加入赛场再来队列
	function addModelGoonPlay($mrwg,$gameplay)
	{
		if ( !$mrwg || !$gameplay || !is_array($gameplay) )
		{
			return false;
		}
		$ud = $gameplay['uid'];
		$new['gameplayId']= $gameplay['gameplayId'];
		$new['gamesId']  = $gameplay['gamesId'];
		$new['modelId']  = $gameplay['modelId'];
		$new['roomId']   = $gameplay['roomId'];
		$new['weekId']   = $gameplay['weekId'];
		$new['gameId']   = $gameplay['gameId'];
		$new['joinTime'] = $gameplay['joinTime'];
		$new['score']    = $gameplay['score'];
		$res = setUser($ud,$new);
		return $this->redis->ladd($this->key_model_goonplay_.$mrwg, $gameplay);
	}
	//获取赛场再来队列的长度
	function lenModelGoonPlay($mrwg)
	{
		if ( !$mrwg )
		{
			return false;
		}
		return $this->redis->llen($this->key_model_goonplay_.$mrwg);
	}
	//获取赛场再来队列中的n个用户，默认3个
	function getModelGoonPlay( $mrwg, $num=3 )
	{
		if ( !$mrwg || !$num || !is_int($num) || $num < 1 ) return false;
		$len = $num > 1 ? $this->lenModelGoonPlay($mrwg) : 1;
		if ( $len < $num ) return false;
		$gameplays = array();
		$i = 0;
		while ( $i < $num )
		{
			$gameplay = $this->redis->lpop($this->key_model_goonplay_.$mrwg);
			if ( !$gameplay ) break;
			$uid = $gameplay['uid'];
			$user = $this->getUserInfo($uid);
			if ( !$user ) {
				gerr("赛场用户无效 G=$mrwg F=? U=$uid");
				continue;
			}
			$fd = $user['fd'];
			if ( !isset($user['giveup']) || !$user['giveup'] ) {
				$gameplay['fd'] = $fd;
				$gameplays[] = $gameplay;
				$i++;
				continue;
			}
			$mrwg_ = explode('_', $mrwg);
			$md = $mrwg_[0];
			$rd = $mrwg_[1];
			$wd = $mrwg_[2];
			$gd = $mrwg_[3];
			if ( $fd ) {
				debug("赛场用户放弃 G=$mrwg F=$fd U=$uid");
				$newU = array('gameplayId'=>'','gamesId'=>'','modelId'=>0,'roomId'=>0,'weekId'=>0,'gameId'=>0,'joinTime'=>0,'score'=>0,'giveup'=>0);
				$res = setUser($uid, $newU); unset($newU);
				$cmd = 5; $code = 112;
				$send = array('errno'=>0, 'error'=>"您已放弃放弃竞技赛。", 'modelId'=>$md, 'gameId'=>$gd, 'score'=>0);
				$this->sendToFd($fd, $cmd, $code, $send);
			} else {
				debug("赛场弃后销毁 G=$mrwg F=$fd U=$uid");
				$this->desUserInfo($uid, $user);
			}
		}
		if ( count($gameplays) == $num ) return $gameplays;
		foreach ( $gameplays as $k=>$v )
		{
			$this->addModelGoonPlay($mrwg,$v);
		}
		return false;
	}
	//获取周赛用户全部
	function getModelWeekPlayAll( $md, $rd, $wd )
	{
		if ( $md != 1 || !$wd ) return false;
		$mrw = $md.'_'.$rd.'_'.$wd;
		$list = $this->redis->hgetall($this->key_model_weekplay_.$md);
		if ( !$list ) return false;
		$wd_ll = intval(date("Ymd", strtotime(substr_replace(substr_replace($wd, '-', 4, 0), '-', 7, 0))-86400*7*3));
		$data = array();
		foreach ( $list as $k=>$v )
		{
			if ( $v['weekId'] <= $wd_ll ) {
				$this->delModelWeekPlay($v['modelId'], $v['roomId'], $v['weekId'], $v['uid']);
				continue;
			}
			if ( $v['roomId'] == $rd && $v['weekId'] == $wd )
			{
				$data[$k]= $v;
			}
		}
		return $data ? $data : false;
	}
	//获取周赛用户
	function getModelWeekPlay( $md, $rd, $wd, $ud )
	{
		if ( $md != 1 || !$wd || !$ud ) return false;
		$weekplayId = $md.'_'.$rd.'_'.$wd.'_'.$ud;
		return $this->redis->hget($this->key_model_weekplay_.$md,$weekplayId);
	}
	//设置周赛用户
	function setModelWeekPlay($md,$rd,$wd,$ud,$weekplay)
	{
		if ( $md != 1 || !$wd || !$ud || !$weekplay || !is_array($weekplay) ) return false;
		$weekplayId = $md.'_'.$rd.'_'.$wd.'_'.$ud;
		return $this->redis->hset($this->key_model_weekplay_.$md,$weekplayId,$weekplay);
	}
	//更新周赛用户
	function updModelWeekPlay($md,$rd,$wd,$ud,$data)
	{
		if ( $md != 1 || !$wd || !$ud || !$data || !is_array($data) ) return false;
		$weekplay = $this->getModelWeekPlay($md,$rd,$wd,$ud);
		if ( !$weekplay ) return false;
		$weekplay = array_merge($weekplay,$data);
		return $this->setModelWeekPlay($md,$rd,$wd,$ud,$weekplay);
	}
	//删除周赛用户
	function delModelWeekPlay( $md, $rd, $wd, $ud )
	{
		if ( $md != 1 || !$wd || !$ud ) return false;
		$weekplayId = $md.'_'.$rd.'_'.$wd.'_'.$ud;
		return $this->redis->hdel($this->key_model_weekplay_.$md, $weekplayId);
	}
	//入库周赛用户
	function insModelWeekPlay( $md, $rd, $wd, $weekPlayAll )
	{
		if ( $md != 1 || !$wd || !$weekPlayAll || !is_array($weekPlayAll) ) return false;
		foreach ( $weekPlayAll as $k=>$v )
		{
			if ( !isset($v['uid']) || !isset($v['weekplayId']) || !isset($v['cool_num']) || !isset($v['weekPoint']) || !isset($v['weekRank']) || !isset($v['weekPrizeCoins']) || !isset($v['weekPrizeProps']) || !isset($v['create_time']) || !isset($v['update_time']) ) {
				gerr("[BADATA] insModelWeekPlay $md,$rd,$wd,".json_encode($v));
				continue;
			}
			$ud = $v['uid'];
			$sql = "INSERT INTO `lord_model_weekplay` ( `weekplayId`, `modelId`, `roomId`, `weekId`, `uid`, `cool_num`, `weekPoint`, `weekRank`, `weekPrizeExp`, `weekPrizeCoins`, `weekPrizeProps`, `create_time`, `update_time` ) VALUES ( '".$v['weekplayId']."', ".$md.", ".$rd.", ".$wd.", ".$ud.", ".$v['cool_num'].", ".$v['weekPoint'].", ".$v['weekRank'].", ".$v['weekPrizeExp'].", ".$v['weekPrizeCoins'].", '".addslashes(json_encode($v['weekPrizeProps']))."', ".$v['create_time'].", ".$v['update_time']." )";
			bobSql($sql);
		}
		return true;
	}


	//获取道具列表
	//list 			int 	0返回关联数组 1返回索引数组
	//return 		arr 	道具列表
	function getlistItem( $list=0 )
	{
		$item = $this->prop->getlistItem();
		if ( $item === false ) {
			gerr("获取道具列表 error=".json_encode($this->prop->getError()));
			return array();
		}
		if ( !$list ) $item = array_values($item);
		return $item;
	}


	//用户道具列表
	//ud 			int 	用户ID
	//list 			int 	0返回索引数组 1返回关联数组
	//return 		arr 	道具列表
	function getuserItem( $ud, $list=0 )
	{
		$item = $this->prop->getMine($ud, 0, 1);
		if ( $item === false ) {
			gerr("获取用户道具 error=".json_encode($this->prop->getError()));
			return array();
		}
		if ( !$list ) $item = array_values($item);
		return $item;
	}


	//用户获得奖励 货币 道具 实物
	//ud 			int 	用户ID
	//prize 		arr 	奖包数据
	//U 			arr 	用户信息
	//type 			int 	奖包来源
	//return 		arr 	奖包数据
	//return 		false 	操作失败
	function userPrize( $ud, $prize, $U=array(), $type='' )
	{
		if ( !$ud || !$prize || !is_array($prize) || !is_array($U) ) return false;
		$td = isset($U['tableId']) ? $U['tableId'] : '';
		$sd = isset($U['seatId']) ? $U['seatId'] : 0;
		//参数数据规整
		$prize_ = $prize;
		if ( !isset($prize['gold']) )     $prize['gold'] = 0;
		if ( !isset($prize['golds']) )    $prize['golds'] = 0;//预留 乐钻
		if ( !isset($prize['coins']) )    $prize['coins'] = 0;
		if ( !isset($prize['coupon']) )   $prize['coupon'] = 0;
		if ( !isset($prize['lottery']) )  $prize['lottery'] = 0;
		if ( !isset($prize['items']) )    $prize['items'] = array();
		if ( isset($prize['props']) )     $prize['items'] = array_merge($prize['items'],$prize['props']);
		if ( isset($prize['propItems']) ) $prize['items'] = array_merge($prize['items'],$prize['propItems']);
		if ( isset($prize['propDress']) ) $prize['items'] = array_merge($prize['items'],$prize['propDress']);
		$newU = $addU = $addT = array();
		//规整货币数值
		if ( $typen = array_search($type, $this->record->types) ) $type = $typen;
		elseif ( ! $type ) $type = '领取邮件';
		foreach ( $prize as $k => $v )
		{
			if ( ! in_array($k, array('items','props','propItems','propDress')) && $v ) {
				$addU[$k] = $v;
				$U[$k] = isset($U[$k]) ? ($U[$k] + $v) : $v;
				$this->record->money($type, $k, $v, $ud, $U);
			}
		}
		//写入用户道具
		if ( $prize['items'] )
		{
			$cd0 = $cd1 = $cd2 = 0;//扩展
			$listitem = $this->prop->getlistItem();
			foreach ( $prize['items'] as $iid => $item )//有可能为id=>name
			{
				$iid = is_array($item) && isset($item['id']) ? intval($item['id']) : intval($iid);
				$cd  = is_array($item) && isset($item['cd']) ? intval($item['cd']) : 0;
				$num = is_array($item) && isset($item['num']) ? intval($item['num']) : 1;
				$ext = is_array($item) && isset($item['ext']) ? intval($item['ext']) : 0;//预留扩展
				$pd = isset($listitem[$iid]) ? $listitem[$iid]['pd'] : 0;
				if ( !$iid || !$num ) return gerr("物品配置错误 U=$ud prize=".json_encode($prize));
				$lock = 'PROP_'.$ud;
				setLock($lock);
				$res = $this->prop->obtain($ud, 0, $iid, $num);//道具写入
				delLock($lock);
				if ( $res === false ) return gerr("物品写入失败 U=$ud prize=".json_encode($prize));
				if ( $pd == 7 ) $this->newMcard($ud, isset($listitem[$iid]) ? $listitem[$iid]['second'] : 0);
				if ( $pd == 8 ) {
					$U['buff'] = $newU['buff'] = $this->addBuff($ud, $pd, $U);
					if ( $td && ( $table = $this->getTableInfo($td) ) ) {
						$newT = array("seat{$sd}buff"=>$U['buff']);
						$this->setTableInfo($td, $newT);
					}
				}
				${"cd{$cd}"} = 1;
			}
			if ( $cd0 || $cd1 ) $newU['propDress'] = $this->prop->getMine($ud, 1);//1.服装信息
			if ( $cd0 || $cd2 ) {
				$newU['propItems'] = $this->getDbUserItems($ud, 1);//2.道具信息
				$newU['realItems'] = $this->getDbUserItems($ud);//2.真实道具信息
			}
		}
		//处理在线用户
		if ( $U && isset($U['fd']) && isset($U['coins']) && isset($U['tableId']) )
		{
			//更新牌桌乐豆
			if ( $U['tableId'] && isset($addU['coins']) )
			{
				$lock = $td = $U['tableId'];
				$sd = $U['seatId'];
				$table = $this->getTableInfo($td);
				if ( $table && isset($table["seat{$sd}coins"]) ) {
					$addT["seat{$sd}coins"] = $addU['coins'];
					$this->incTableInfo($td, $addT);
				}
			}
			//更新货币数值
			if ( $addU ) {
				$res = $this->incUserInfo($ud, $addU, $type);
				if ( $res ) {
					$U = array_merge($U, $res['info']);
					if ( $res['send'] ) sendToFd($U['fd'], 4, 110, $res['send']);
				}
			}
			//更新物品状态
			if ( $newU ) setUser($ud, $newU);
		}
		//处理下线用户
		else
		{
			//更新货币数值
			if ( $addU ) {
				$sql = "UPDATE `lord_game_user` SET ";
				$sqlp = array();
				foreach ( $addU as $k => $v )
				{
					if ( in_array($k, array('point')) ) continue;
					$sqlp[]= "`$k` = `$k` + $v";
				}
				if ( $sqlp ) {
					$sql.= join(', ', $sqlp) . " WHERE `uid` = $ud";
					bobSql($sql);
				}
			}
		}
		// //写入发奖记录
		// $sql = "INSERT INTO `lord_record_prize` (`dateid`,`type`,`uid`,`coins`,`coupon`,`lottery`,`items`,`create_time`) VALUES (";
		// $sql.= date("Ymd").",$type,$ud,".$prize['coins'].",".$prize['coupon'].",".$prize['lottery'].",'".addslashes(json_encode($prize['items']))."',".time().")";
		// bobSql($sql);
		return $prize_;
	}


	//购买商品
	//U 			arr 	用户信息
	//id 			int 	商品ID
	//free 			int 	是否免费 0花钱 1免费
	//return 		arr 	兼容旧版的货币变化结果
	//return 		int 	购买失败的各种错误码
	function buyGoods( $U, $id, $free=0 )
	{
		$id = intval($id);
		if ( !$U || !is_array($U) || $id < 2 ) return 1;//购买失败
		$ud = intval($U['uid']);
		if ( $ud < 1 ) return 1;//购买失败
		$fd = isset($U['fd']) ? $U['fd'] : '';
		$td = isset($U['tableId']) ? $U['tableId'] : 0;
		$sd = isset($U['seatId']) ? $U['seatId'] : 0;
		$lock = 'PROP_'.$ud;
		setLock($lock);
		$res = $this->prop->buyGoods($U, $id, 1, $free);//U gd num free
		delLock($lock);
		if ( $res === false ) {
			$error = $this->prop->getError();
			gerr("购买商品失败 F=$fd U=$ud errno=".$error['errno'].' error='.$error['error']);
			return $error['errno'];
		}
		$addU = $res['addU'];
		if ( isset($addU['golds']) ) {
			unset($addU['golds']);//预留 代币
		}
		$goods = $res['goods'];
		$propDress = $res['propDress'];
		$propItems = $realItems = $res['propItems'];
		if ( isset($U['mysql']) && $U['mysql'] ) {
			if ( $addU ) {
				$sqlp = array();
				foreach ( $addU as $col => $val ) {
					$sqlp[]= "`$col` = `$col` + $val";
					$U[$col]+=$val;
				}
				$sql = "UPDATE `lord_game_user` SET ".join(',', $sqlp)." WHERE `uid` = $ud";
				$res = $this->mysql->runSql($sql);
			}
		} else {
			$newU['propDress'] = $propDress;
			$newU['realItems'] = $realItems;
			$newU['propItems'] = $propItems = $this->getDbUserItems($ud, 1);
			$res = setUser($ud, $newU);
			if ( isset($addU['coins']) && $addU['coins'] && $td ) {
				$lock = $td;
				if ( setLock($lock) ) {
					if ( $table = $this->getTableInfo($td) ) {
						$addT["seat{$sd}coins"] = $addU['coins'];
						if ( isset($addU['coupon']) && $addU['coupon'] ) {
							$addT["seat{$sd}coupon"] = $addU['coupon'];
						}
						$res = $this->incTableInfo($td, $addT);
						$newT["seat{$sd}items"] = $propDress;
						$newT["seat{$sd}realItems"] = $realItems;
						$res = $this->setTableInfo($td, $newT);
					}
					delLock($lock);
				}
			}
			if ( $addU ) $res = $this->incUserInfo($ud, $addU);
			$U = $this->getUserInfo($ud);
		}
		if ( $addU ) {
			foreach ( $addU as $k => $v ) $this->record->money('币买道具', $k, $v, $ud, $U);
		}
		$items = $this->prop->getlistItem();
		//创建叠加月卡
		$conf = $this->getGoodsCtrl('baoyuelibao', isset($U['channel']) ? $U['channel'] : '');
		if ( $conf && isset($conf['ids']) && $conf['ids'] && is_array($conf['ids']) && in_array($id, $conf['ids']) ) {
			$sec = isset($items[$goods['iid']]) ? $items[$goods['iid']]['second'] : 0;
			$this->newMcard($ud, $sec);
		}
		//幸运牌局buff
		$conf = $this->getGoodsCtrl('xingyunpaiju', isset($U['channel']) ? $U['channel'] : '');
		if ( $conf && isset($conf['ids']) && $conf['ids'] && is_array($conf['ids']) && in_array($id, $conf['ids']) ) {
			$U['buff'] = $this->addBuff($ud, $items[$goods['iid']]['pd'], $U);
			if ( $td ) {
				$lock = $td;
				if ( setLock($lock) ) {
					if ( $table = $this->getTableInfo($td) ) {
						$newT = array("seat{$sd}buff"=>$U['buff']);
						$this->setTableInfo($td, $newT);
					}
					delLock($lock);
				}
			}
		}
		//向下兼容
		$result['gold'] = $U['gold'];
		$result['coins'] = $U['coins'];
		$result['coupon'] = $U['coupon'];
		$result['gold_'] = $goods['money'] == 'gold' ? $goods['price'] : 0;
		$result['coins_'] = $goods['money'] == 'coins' ? $goods['price'] : 0;
		$result['coupon_'] = $goods['money'] == 'coupon' ? $goods['price'] : 0;
		$result['name'] = $goods['name'];
		$result['propDress'] = $propDress;
		$result['realItems'] = $realItems;
		$result['propItems'] = $propItems;
		return $result;
	}

	//获取特殊标记下的特殊渠道的不同的商品ID、价格、图片呈现，用于控制客户端的商品呈现
	function getGoodsCtrl( $tag, $channel, $gd=0, $rd=0, $ext=0 )
	{
		$data_goods_control = array();
		include ROOT.'/include/data_goods_control.php';
		if ( !$tag || !isset($data_goods_control[$tag]) ) return gerr("商控配置出错 tag=$tag channel=$channel ".json_encode($data_goods_control));
		$confs = $data_goods_control[$tag];
		if ( $tag === 'datinglibao' ) {
			foreach ( $confs as $k => $conf )
			{
				if ( $channel && $conf['channot'] && is_array($conf['channot']) &&  in_array($channel, $conf['channot']) ) {
					unset($confs[$k]);
					continue;
				}
				if ( $channel && $conf['channel'] && is_array($conf['channel']) && !in_array($channel, $conf['channel']) ) {
					unset($confs[$k]);
					continue;
				}
				unset($confs[$k]['channot']); unset($confs[$k]['channel']);
			}
			$confs = $confs ? array_values($confs) : array();
			return $confs;
		}
		foreach ( $confs as $k => $conf )
		{
			if ( $channel && $conf['channot'] && is_array($conf['channot']) &&  in_array($channel, $conf['channot']) ) continue;
			if ( $channel && $conf['channel'] && is_array($conf['channel']) && !in_array($channel, $conf['channel']) ) continue;
			unset($conf['channot']); unset($conf['channel']);
			if ( $gd ) {
				if ( $conf['id'] == $gd ) {
					unset($conf['roomId']); unset($conf['ext']);
					return $conf;
				}
				continue;
			}
			if ( $rd ) {
				if ( ! $conf['roomId'] || ( $conf['roomId'] == $rd && $conf['ext'] == $ext ) ) {
					unset($conf['roomId']); unset($conf['ext']);
					return $conf;
				}
			} else {
				unset($conf['roomId']); unset($conf['ext']);
				return $conf;
			}
		}
		return false;
	}



	//榜单系列


	//榜单通用函数－查榜
	//dateid 	日id或周id 	日id:20141217	周id:20141215
	//flag 		榜单名内存标记
	//is_week 	0日榜单1周榜单2月榜单
	//$len		榜单长度
	//$ud		获取某用户的在前100名内的名次
	private function zList( $dateid, $flag, $is_week=0, $len=20, $ud=0 )
	{
		if ($dateid > 20371231 || $dateid < 20141111) return array();
		$hourid = $dateid * 100 + ( $is_week == 2 ? 0 :(intval($is_week ? date("Ymd",time()-(date("N")-1)*86400) : date("Ymd")) == $dateid ? date("H") : 24) );
		$hourcol = $flag.$hourid;
		$listkey = $flag.$dateid;
		$list = $this->redis->hget($this->key_cache, $hourcol);
		$list = ( $list && is_array($list) ) ? $list : array();
		if ($list) {
			$list_ = array_chunk($list, $len);//只取所需长度
			if ( $ud ) {
				$list_[0]['myval'] = isset($list[$ud]['val']) ? $list[$ud]['val'] : 0;
				$list_[0]['myrank'] = isset($list[$ud]['rank']) ? $list[$ud]['rank'] : 0;
			}
			return $list_[0];
		}
		//加事务锁	互斥锁
		$lock = $listkey;
		$res = setLock($lock, 1);
		if ( !$res )
		{
			return array();
		}
		$res = $this->redis->zlist($listkey, 100);//实际存储前100名
		$res = $res ? $res : array();
		$list = array();
		$i=0;
		foreach ( $res as $uid => $val )
		{
			$i++;
			$nick = $this->getUserNick($uid);
			$list[$uid]= array('id'=>$uid,'rank'=>$i, 'nick'=>$nick, 'val'=>$val);
		}
		$res = $this->redis->hset($this->key_cache, $hourcol, $list);
		$res = delLock($lock);
		if ($list) {
			$list_ = array_chunk($list, $len);//只取所需长度
			if ( $ud ) {
				$list_[0]['myval'] = isset($list[$ud]['val']) ? $list[$ud]['val'] : 0;
				$list_[0]['myrank'] = isset($list[$ud]['rank']) ? $list[$ud]['rank'] : 0;
			}
			return $list_[0];
		}
		return $list;
	}

	//榜单通用函数－增量插榜
	//dateid 	int    日id或周id 	日id:20141217	周id:20141215
	//flag 		string 榜单名内存标记
	//keyval 	array  uid=>要增加的值
	private function zAdd( $dateid, $flag, $keyval )
	{
		if ($dateid > 20371231 || $dateid < 20141111 || empty($flag) || !$keyval || !is_array($keyval)) return false;
		$key = $flag.$dateid;
		foreach ( $keyval as $member => $score )
		{
			$res = $this->redis->zincr($key, $score, $member);
			if (!$res) return false;
		}
		return true;
	}

	//榜单通用函数－替换插榜
	//dateid 	int    日id或周id 	日id:20141217	周id:20141215
	//flag 		string 榜单名内存标记
	//keyval 	array  uid=>要增加的值
	private function zSet( $dateid, $flag, $keyval )
	{
		if ($dateid > 20371231 || $dateid < 20141111 || empty($flag) || !$keyval || !is_array($keyval)) return false;
		$key = $flag.$dateid;
		foreach ( $keyval as $member => $score )
		{
			$res = $this->redis->zset($key, $score, $member);
			if (!$res) return false;
		}
		return true;
	}

	//获取榜单 普通场每日赢场排行榜
	function zListNormalDayWin( $dateid, $len=20, $ud=0 )
	{
		$flag = 'lord_list_normal_day_win_';
		return $this->zList($dateid, $flag, 0, $len, $ud);
	}

	//获取榜单 普通场每日赢钱排行榜
	function zListNormalDayEarn( $dateid, $len=20, $ud=0 )
	{
		$flag = 'lord_list_normal_day_earn_';
		return $this->zList($dateid, $flag, 0, $len, $ud);
	}

	//获取榜单 普通场每日倍率排行榜
	function zListNormalDayMaxrate( $dateid, $len=20, $ud=0 )
	{
		$flag = 'lord_list_normal_day_maxrate_';
		return $this->zList($dateid, $flag, 0, $len, $ud);
	}

	//获取榜单 普通场每周赢场排行榜
	function zListNormalWeekWin( $dateid, $len=20, $ud=0 )
	{
		$flag = 'lord_list_normal_week_win_';
		return $this->zList($dateid, $flag, 1, $len, $ud);
	}

	//获取榜单 普通场每周赢钱排行榜
	function zListNormalWeekEarn( $dateid, $len=20, $ud=0 )
	{
		$flag = 'lord_list_normal_week_earn_';
		return $this->zList($dateid, $flag, 1, $len, $ud);
	}

	//获取榜单 竞技场每周积分排行榜
	function zListMatchWeekPoint( $dateid, $len=20, $ud=0 )
	{
		$flag = 'lord_list_match_week_point_';
		return $this->zList($dateid, $flag, 1, $len, $ud);
	}

	//获取榜单 用户每日使用金币排行榜
	function zListUserDayCost( $dateid, $len=10, $ud=0 )
	{
		$flag = 'lord_list_user_day_cost_';
		return $this->zList($dateid, $flag, 0, $len, $ud);
	}

	//获取榜单 用户每日充值金币排行榜
	function zListUserDayRecharge( $dateid, $len=10, $ud=0 )
	{
		$flag = 'lord_list_user_day_recharge_';
		return $this->zList($dateid, $flag, 0, $len, $ud);
	}

	//获取榜单 用户每月充值金币排行榜
	function zListUserMonthRecharge( $dateid, $len=10, $ud=0 )
	{
		$flag = 'lord_list_user_month_recharge_';
		return $this->zList($dateid, $flag, 0, $len, $ud);
	}

	//累加入榜 普通场游戏次数榜单 周、日
	function zNormalPlay( $ud, $val=1 )
	{
		$dateid = intval(date("Ymd",time()-(date("N")-1)*86400));
		$flag = 'lord_list_normal_week_play_';
		$res = $this->zAdd($dateid, $flag, array($ud=>$val));
		if (!$res) return false;
		$dateid = intval(date("Ymd"));
		$flag = 'lord_list_normal_day_play_';
		$res = $this->zAdd($dateid, $flag, array($ud=>$val));
		if (!$res) return false;
		return true;
	}

	//累加入榜 普通场胜局次数榜单 周、日
	function zNormalWin( $ud, $val=1 )
	{
		$dateid = intval(date("Ymd",time()-(date("N")-1)*86400));
		$flag = 'lord_list_normal_week_win_';
		$res = $this->zAdd($dateid, $flag, array($ud=>$val));
		if (!$res) return false;
		$dateid = intval(date("Ymd"));
		$flag = 'lord_list_normal_day_win_';
		$res = $this->zAdd($dateid, $flag, array($ud=>$val));
		if (!$res) return false;
		return true;
	}

	//累加入榜 普通场赢钱数量榜单 周、日
	function zNormalEarn( $ud, $val )
	{
		$dateid = intval(date("Ymd",time()-(date("N")-1)*86400));
		$flag = 'lord_list_normal_week_earn_';
		$res = $this->zAdd($dateid, $flag, array($ud=>$val));
		if (!$res) return false;
		$dateid = intval(date("Ymd"));
		$flag = 'lord_list_normal_day_earn_';
		$res = $this->zAdd($dateid, $flag, array($ud=>$val));
		if (!$res) return false;
		return true;
	}

	//更新入榜 普通场最大倍率榜单 日
	function zNormalDayMaxrate( $ud, $val )
	{
		$dateid = intval(date("Ymd"));
		$flag = 'lord_list_normal_day_maxrate_';
		$res = $this->zSet($dateid, $flag, array($ud=>$val));
		if (!$res) return false;
		return true;
	}

	//累加入榜 竞技场积分数量榜单 周、日
	function zMatchPoint( $ud, $val )
	{
		$dateid = intval(date("Ymd",time()-(date("N")-1)*86400));
		$flag = 'lord_list_match_week_point_';
		$res = $this->zAdd($dateid, $flag, array($ud=>$val));
		if (!$res) return false;
		$dateid = intval(date("Ymd"));
		$flag = 'lord_list_match_day_point_';
		$res = $this->zAdd($dateid, $flag, array($ud=>$val));
		if (!$res) return false;
		return true;
	}

	//获取-各种版本号
	function getVersion( $name = "", $vercode=0 )
	{
		$versions = $this->redis->hgetall($this->key_game_version);
		if ( ! $versions ) {
			$sql = "SELECT `name`,max(`version`) vers FROM `lord_game_version` WHERE `is_done` = 1 GROUP BY `name`";
			$ver = $this->mysql->getData($sql);
			$ver = $ver ? $ver : array();
			$versions = array();
			foreach ( $ver as $k => $v ) $versions[$v['name']] = intval($v['vers']);
			$res = $versions ? $this->redis->hmset($this->key_game_version, $versions) : false;
		}
		$versions = $versions ? $versions : array();
		if ( $vercode < 10800 && isset($versions['verfile']) ) $versions['verfile'] = ISLOCAL ? 111 : 118;
		if ( $name ) {
			if ( isset($versions[$name]) ) {
				return $versions[$name];
			}
			return 0;
		}
		return $versions;
	}
	function newVersion( $name, $version=0, $time=0 )
	{
		if ( !in_array($name, array('version', 'verconf', 'verfile', 'vertips')) ) return false;
		$time = $time ? $time : time();
		$version++;
		if ( $version == $this->getVersion($name) ) {
			return $version;
		}
		$sql = "UPDATE `lord_game_version` SET `end_time` = $time, `comments`= '自动', `is_done` = 1 WHERE `name` = '$name' AND `version` = $version AND `is_done` = 0";
		$res = $this->mysql->runSql($sql);
		$sql = "INSERT INTO `lord_game_version` (`name`, `version`, `start_time`, `end_time`, `comments`, `is_done`) VALUES ('$name', ".($version+1).", ".($time+1).", 0, '', 0)";
		$res = $this->mysql->runSql($sql);
		return $version;
	}

	//列表-获取-需要更新的提示列表
	function listGetTips( $ver, $channel = '' )
	{
		$version = $this->getVersion('vertips');
		if ( $ver >= $version ) {
			return array();
		}
		$list = $this->redis->hgetall($this->key_list_tips);
		$list = $list ? $list : array();
		if ( !$list ) {
			$sql = "SELECT * FROM `lord_game_tips` ORDER BY `id` DESC";
			$list_ = $this->mysql->getData($sql);
			$list_ = $list_ ? $list_ : array();
			foreach ( $list_ as $k => $v )
			{
				$list[$v['id']] = $v;
			}
			$res = $this->redis->hmset($this->key_list_tips,$list);
		}
		$is_newversion = 0;
		foreach ( $list as $k => $v )
		{
			if ( $channel && $v['channel'] && $channel != $v['channel'] ) {	//筛选渠道
				unset($list[$k]);
			// } elseif ( $v['start_time'] && $v['start_time'] > time() ) {	//未到启用时间
			// 	unset($list[$k]);
			// } elseif ( $v['end_time'] && $v['end_time'] < time() && !$v['is_del'] ) {//刚到删除时间
			// 	// unset($list[$k]);
			// 	// $this->listDelTips($v['id']);
			// 	//加事务锁
			// 	$lock = $this->key_list_tips.'_'.$v['id'];
			// 	$res = setLock($lock, 1);
			// 	if ( $res ) {
			// 		$list[$k] = $v = $this->listDelTips($v['id'], $v, $version);
			// 		$is_newversion = 1;
			// 		$res = delLock($lock);
			// 	} else {
			// 		return array();
			// 	}
			}
		}
		$version += $is_newversion ;
		$list_ = $list;
		$list = array('vertips'=>$version,'insert'=>array(),'update'=>array(),'delete'=>array());
		foreach ( $list_ as $k => $v )
		{
			$vv = array();
			$vv['id'] = intval($v['id']);
			$vv['path'] = $v['path'];
			$vv['content'] = $v['content'];
			$vv['sort'] = intval($v['sort']);
			if ( !($v['version'] > $ver && $v['version'] <= $version) ) {}	//无新操作：跳过
			elseif ( $v['ver_del'] && $v['ver_del'] <= $ver ) {}			//有删除，无新删：跳过
			elseif ( $v['ver_del'] && $v['ver_del'] > $ver ) {				//有删除，有新删
				if ( $v['ver_ins'] > $ver ) {}								//有新增，有新删：跳过
				else { $list['delete'][] = $vv; }							//无新增，有新删：删除
			}
			elseif ( $v['ver_upd'] && $v['ver_upd'] <= $ver ) {}			//无删除，有更新，无新更：跳过
			elseif ( $v['ver_upd'] && $v['ver_upd'] > $ver ) {				//无删除，有更新，有新更
				if ( $v['ver_ins'] > $ver ) { $list['insert'][] = $vv; }	//无删除，有更新，有新更，有新增：当作新增处理
				else { $list['update'][] = $vv; }							//无删除，有更新，有新更，无新增：更新
			}
			elseif ( $v['ver_ins'] && $v['ver_ins'] <= $ver ) {}			//无删除，无更新，无新增：跳过
			elseif ( $v['ver_ins'] && $v['ver_ins'] > $ver ) {				//无删除，无更新，有新增：新增
				$list['insert'][] = $vv;
			}
			else {}															//无删除，无更新，无新增：忽略
		}
		return $list;
	}
	//列表-设置-提示
	function listSetTips( $data )
	{
		if ( !isset($data['id']) || !$data['id'] || !isset($data['channel']) || !isset($data['content']) || $data['content'] ) return false;
		return $this->redis->hset($this->key_list_tips, $data['id'], $data);
	}
	//列表-删除-提示
	// function listDelTips( $id )
	// {
	// 	return $this->redis->hdel($this->key_list_tips, $id);
	// }
	function listDelTips( $id, $data, $version )
	{
		$name = "vertips";
		$time = time();
		$version = $this->newVersion($name, $version, $time);
		$sql = "UPDATE `lord_game_tips` SET `version` = $version, `ver_del` = $version, `is_del` = 1, `update_time` = $time WHERE `id` = $id";
		$res = $this->mysql->runSql($sql);
		$new['version'] = $data['version'] = $version;
		$new['ver_del'] = $data['ver_del'] = $version;
		$new['is_del'] = $data['is_del'] = 1;
		$new['update_time'] = $data['update_time'] = $time;
		$res = $this->redis->hmset($this->key_list_tips, $id, $new);
		return $data;
	}
	//列表-获取-需要更新的素材列表
	function listGetFile( $ver, $channel='', $vercode=0 )
	{
		$version = $this->getVersion('verfile', $vercode);
		if ( $ver >= $version ) return array();
		if ( ! $ver && $vercode < 10800 ) $version = ISLOCAL ? 111 : 118;
		$list = $this->redis->hgetall($this->key_list_file);
		if ( ! $list ) {
			$list = array();
			$sql = "SELECT * FROM `lord_game_file` ORDER BY `id` DESC";
			$data = $this->mysql->getData($sql);
			if ( ! $data ) $data = array();
			foreach ( $data as $k => $v )
			{
				$list[$v['id']] = $v;
				$list[$v['id']]['channel'] = $v['channel'] ? explode(' ', $v['channel']) : array();
				$list[$v['id']]['channot'] = $v['channot'] ? explode(' ', $v['channot']) : array();
			}
			$res = $this->redis->hmset($this->key_list_file, $list);
		}
		$tmp = $list; $list = $goods = $gctrl = array();
		foreach ( $tmp as $k => $v )
		{
			if ( $v['ver_ins'] > $version || $v['ver_upd'] > $version || $v['ver_del'] > $version ) {
				unset($tmp[$k]);
			} elseif ( $v['path'] == 'mall_goods' ) {
				$goods[]= $v;
				unset($tmp[$k]);
			} elseif ( $v['path'] == 'goods_control' ) {
				$gctrl[]= $v;
				unset($tmp[$k]);
			}
		}
		$list = array_merge($goods, $gctrl, $tmp);
		$files = array('verfile'=>$version,'insert'=>array(),'update'=>array(),'delete'=>array());
		foreach ( $list as $k => $v )
		{
			if ( $channel && $v['channot'] &&   in_array($channel, $v['channot']) ) continue;
			if ( $channel && $v['channel'] && ! in_array($channel, $v['channel']) ) continue;
			$vv = array();
			$vv['id'] = intval($v['fileid']);
			$vv['path'] = $v['path'];
			if ( !($v['version'] > $ver && $v['version'] <= $version) ) {}	//无新操作：跳过
			elseif ( $v['ver_del'] && $v['ver_del'] <= $ver ) {}			//有删除，无新删：跳过
			elseif ( $v['ver_del'] && $v['ver_del'] > $ver ) {				//有删除，有新删
				if ( $v['ver_ins'] > $ver ) {}								//有新增，有新删：跳过
				else { $files['delete'][] = $vv; }							//无新增，有新删：删除
			}
			elseif ( $v['ver_upd'] && $v['ver_upd'] <= $ver ) {}			//无删除，有更新，无新更：跳过
			elseif ( $v['ver_upd'] && $v['ver_upd'] > $ver ) {				//无删除，有更新，有新更
				if ( $v['ver_ins'] > $ver ) { $files['insert'][] = $vv; }	//无删除，有更新，有新更，有新增：当作新增处理
				else { $files['update'][] = $vv; }							//无删除，有更新，有新更，无新增：更新
			}
			elseif ( $v['ver_ins'] && $v['ver_ins'] <= $ver ) {}			//无删除，无更新，无新增：跳过
			elseif ( $v['ver_ins'] && $v['ver_ins'] > $ver ) {				//无删除，无更新，有新增：新增
				$files['insert'][] = $vv;
			}
			else {}															//无删除，无更新，无新增：忽略
		}
		return $files;
	}


	//列表-获取-收件箱
	function listGetInbox( $ud )
	{
		$ud = intval($ud);
		if ( $ud < 1 ) return array();
		$sql = "SELECT * FROM `lord_user_inbox` WHERE `uid` = $ud AND `is_del` = 0 ORDER BY `id` DESC";
		$list = $this->mysql->getData($sql);
		$list = $list ? $list : array();
		$i = 0;
		$list2 = array();
		foreach ( $list as $k => $v )
		{
			if ( ( $v['type'] && $v['create_time'] < time() - $this->confs['time_clear_inbox_type1'] ) || $v['create_time'] < time() - $this->confs['time_clear_inbox_type0'] ) {
				unset($list[$k]);
				continue;
			}
			$list2[$k]['id'] = $v['id']+0;
			$list2[$k]['subject'] = $v['subject'];
			$list2[$k]['content'] = $v['content'];
			$list2[$k]['items'] = $v['items'] ? json_decode($v['items'], 1) : array();
			$list2[$k]['fileid'] = $v['fileid']+0;
			$list2[$k]['is_read'] = $v['is_read']+0;
			$list2[$k]['create_time'] = date("Y-m-d H:i:s", $v['create_time']);
			$list2[$k]['void_time'] = date("Y-m-d H:i:s", ($v['type']?7:30)*86400+$v['create_time']);
			$list2[$k]['sort'] = ++$i;
		}
		$res = $this->redis->del($this->key_list_newmail_.$ud);
		return array_values($list2);
	}
	//列表-获取-新邮件
	function listGetNewMail( $ud )
	{
		$ud = intval($ud); if ( $ud < 1 ) return array();
		$list = $this->redis->hgetall($this->key_list_newmail_.$ud);
		if ( $list ) {
			$res = $this->redis->del($this->key_list_newmail_.$ud);
			return array_values($list);
		}
		return array();
	}
	//列表-增加-新邮件
	function listSetNewMail( $data )
	{
		if ( !is_array($data) || !isset($data['id']) || !isset($data['uid'] ) ) return false;
		$id = $data['id']+0;
		$ud = $data['uid']+0;
		$mail = array();
		$mail['id'] = $id;
		$mail['subject'] = $data['subject'];
		$mail['content'] = $data['content'];
		$mail['items'] = intval($data['items'] && json_decode($data['items'],1));
		$mail['fileid'] = $data['fileid']+0;
		$mail['is_read'] = $data['is_read']+0;
		$mail['create_time'] = date("Y-m-d H:i:s", $data['create_time']);
		$mail['void_time'] = date("Y-m-d H:i:s", ($data['type']?7:30)*86400+$data['create_time']);
		$mail['sort'] = $data['sort']+0;
		$res = $this->incUserInfo($ud, array('mail_unread'=>1));
		return $this->redis->hmset($this->key_list_newmail_.$ud, array($id=>$mail));
	}
	//列表-获取-收件箱新邮件数量
	function getMailNewNum( $ud )
	{
		$ud = intval($ud); if ( $ud < 1 ) return 0;
		$num = 0;
		$list = $this->listGetInbox($ud);
		foreach ( $list as $k => $v )
		{
			if ( !$v['is_read'] ) $num++;
		}
		return $num;
	}
	//列表-设置-收件箱邮件标记已读
	function listSetInboxRead( $ud, $id )
	{
		$ud = intval($ud);
		$id = intval($id);
		if ( $ud < 1 || $id <= 0 ) return false;
		$sql = "UPDATE `lord_user_inbox` SET `is_read` = 1, `update_time` = ".time()." WHERE `id` = $id";
		$res = $this->mysql->runSql($sql);
		$addU = array('mail_unread'=>-1);
		$res = $this->incUserInfo($ud, $addU); unset($addU);
		$mail_unread = 0;
		if ( $res && $res['info']['mail_unread'] < 0 ) {
			$mail_unread = $newU['mail_unread'] = 0;
			setUser($ud, $newU);
		} elseif ( $res ) {
			$mail_unread = $res['info']['mail_unread'];
		}
		return $mail_unread;
	}
	//列表-设置-收件箱邮件领取附件
	function listSetInboxGetItems( $user, $id )
	{
		$id = intval($id);
		if ( !is_array($user) || !isset($user['uid']) || $user['uid'] < 1 || $id < 1 ) return 1;
		$sql = "SELECT * FROM `lord_user_inbox` WHERE `id` = $id AND `is_del` = 0";
		$data = $this->mysql->getLine($sql);
		if ( !$data || !isset($data['items']) || !$data['items'] ) return 1;
		$items = json_decode($data['items'], 1);
		if ( !$items || !is_array($items) ) return 1;
		$res = $this->listDelInbox($id);
		if ( !$res ) return 1;
		$res = $this->userPrize($user['uid'], $items, $user, '领取邮件');
		return $items;
	}
	//列表-设置-收件箱邮件删除
	function listDelInbox( $id )
	{
		$id = intval($id);
		if ( $id <= 0 ) return false;
		$sql = "UPDATE `lord_user_inbox` SET `is_del` = 1, `update_time` = ".time()." WHERE `id` = $id";
		$res = $this->mysql->runSql($sql);
		return true;
	}


	//列表-获取-活动
	function listGetTopic( $channel = '', $isLobby=0 )
	{
		$time = time();
		$list = array();
		$topic_black_channel = array();
		include(ROOT.'/include/topic_black_channel.php');
		if ( in_array($channel, $topic_black_channel) ) return $list;
		if ( ! ( $list_ = $this->redis->hgetall($this->key_list_topic) ) ) {
			$list_ = array();
			$sql = "SELECT * FROM `lord_game_topic` WHERE `state` = 0 ORDER BY `sort` DESC, `id` DESC";
			if ( $data = $this->mysql->getData($sql) ) {
				foreach ( $data as $k => $v )
				{
					$vv['id'] = intval($v['id']);
					$vv['channel'] = $v['channel'] ? explode(' ', $v['channel']) : array();
					$vv['channot'] = $v['channot'] ? explode(' ', $v['channot']) : array();
					$vv['subject'] = trim($v['subject']);
					$vv['content'] = trim($v['content']);
					$vv['start_time'] = intval($v['start_time']);
					$vv['end_time'] = intval($v['end_time']);
					$vv['start_lobby'] = intval($v['start_lobby']);
					$vv['end_lobby'] = intval($v['end_lobby']);
					$vv['sort'] = intval($v['sort']);
					$list_[$vv['id']] = $vv;
				}
				$res = $this->redis->hmset($this->key_list_topic, $list_);
			}
		}
		foreach ( $list_ as $k => $v )
		{
			if ( $channel && $v['channot'] &&   in_array($channel, $v['channot']) ) continue;
			if ( $channel && $v['channel'] && ! in_array($channel, $v['channel']) ) continue;
			if ( $v['start_time'] > $time ) continue;
			if ( $v['end_time'] < $time ) {
				$res = $this->listDelTopic($v['id']);
				continue;
			}
			$vv['id'] = $v['id'];
			$vv['subject'] = $v['subject'];
			$vv['content'] = $v['content'];
			if ( $isLobby ) {
				$vv['start_lobby'] = $v['start_lobby'];
				$vv['end_lobby'] = $v['end_lobby'];
			}
			$vv['sort'] = $v['sort'];
			$list[]=$vv;
		}
		return $list;
	}
	//列表-获取-活动-大厅
	function listGetTopicLobby( $channel = '' )
	{
		$time = time();
		$list = array();
		$list_ = $this->listGetTopic($channel, 1);
		foreach ( $list_ as $k => $v )
		{
			if ( $v['start_lobby'] < $time && $time < $v['end_lobby'] ) {
				$list[]= array('id'=>$v['id'],'path'=>'topic_lobby','sort'=>$v['sort']);
			}
		}
		return $list;
	}
	//列表-设置-活动
	function listSetTopic( $data )
	{
		if ( !isset($data['id']) || !$data['id'] || !isset($data['channel']) || !isset($data['subject']) || !$data['subject'] || !isset($data['content']) || $data['content'] ) return false;
		return $this->redis->hset($this->key_list_topic, $data['id'], $data);
	}
	//列表-活动-删除
	function listDelTopic( $id )
	{
		return $this->redis->hdel($this->key_list_topic, $id);
	}
	//列表-公告-获取
	function listGetNotice( $channel = '' )
	{
		$list = array();
		$list_ = $this->redis->hgetall($this->key_list_notice);
		$list_ = $list_ ? $list_ : array();
		if ( !$list_ ) {
			$sql = "SELECT * FROM `lord_game_notice` WHERE `state` = 0 ORDER BY `sort` DESC, `id` DESC";
			$data = $this->mysql->getData($sql);
			$data = $data ? $data : array();
			foreach ( $data as $k => $v )
			{
				$list_[$v['id']] = $v;
			}
			$res = $this->redis->hmset($this->key_list_notice,$list_);
		}
		foreach ( $list_ as $k => $v )
		{
			if ( $channel && $v['channel'] && $channel != $v['channel'] ) {
				unset($list_[$k]);
			} elseif ( $v['start_time'] > time() ) {
				unset($list_[$k]);
			} elseif ( $v['end_time'] < time() ) {
				unset($list_[$k]);
				$res = $this->listDelNotice($v['id']);
			} else {
				$vv = array();
				$vv['id'] = intval($v['id']);
				$vv['subject'] = $v['subject'];
				$vv['content'] = $v['content'];
				$vv['sort'] = intval($v['sort']);
				$list[]=$vv;
			}
		}
		return $list;
	}
	//列表-公告-设置
	function listSetNotice( $data )
	{
		if ( !isset($data['id']) || !$data['id'] || !isset($data['channel']) || !isset($data['subject']) || !$data['subject'] || !isset($data['content']) || $data['content'] ) return false;
		return $this->redis->hset($this->key_list_notice, $data['id'], $data);
	}
	//列表-公告-删除
	function listDelNotice( $id )
	{
		return $this->redis->hdel($this->key_list_notice, $id);
	}
	//列表-房间-获取
	function listGetRoom()
	{
		$list = $this->redis->hgetall($this->key_list_room);
		if ( $list ) return $list;
		$sql = "SELECT * FROM `lord_game_room` WHERE `is_del` = 0 ORDER BY `sort` DESC, `id`";
		$res = $this->mysql->getData($sql);
		if ( !$res ) return array();
		$list = array();
		foreach ( $res as $k => $v )
		{
			$list[$v['roomId']]=array(
				'isOpen'=>intval($v['isOpen']),
				'isMobi'=>intval($v['isMobi']),
				'verMin'=>intval($v['verMin']),
				'modelId'=>intval($v['modelId']),
				'mode'=>trim($v['mode']),
				'roomId'=>intval($v['roomId']),
				'room'=>trim($v['room']),
				'name'=>trim($v['name']),
				'showRules'=>$v['showRules'] && ($tmp = json_decode($v['showRules'],1)) ? $tmp : array(),
				'baseCoins' => intval($v['baseCoins']),
				'rate' => intval($v['rate']),
				'rateMax' => intval($v['rateMax']),
				'limitCoins' => intval($v['limitCoins']),
				'rake' => intval($v['rake']),
				'enter' => trim($v['enter']),
				'enterLimit' => intval($v['enterLimit']),
				'enterLimit_' => intval($v['enterLimit_']),
				'gameBombAdd' => intval($v['gameBombAdd']),
				'brief' => trim($v['brief']),
				'entry' => trim($v['entry']),
				'tips' => trim($v['tips']),
				'rules' => trim($v['rules']),
				'start' => intval($v['start']),
				'entryMoney' => trim($v['entryMoney']),
				'entryCost'=>intval($v['entryCost']),
				'entryTime'=>intval($v['entryTime']),
				'entryOut'=>intval($v['entryOut']),
				'entryOsec'=>intval($v['entryOsec']),
				'entryOmax'=>intval($v['entryOmax']),
				'entryMax'=>intval($v['entryMax']),
				'entryMin'=>intval($v['entryMin']),
				'entryFull'=>intval($v['entryFull']),
				'entryMore'=>intval($v['entryMore']),
				'entryLess'=>intval($v['entryLess']),
				'scoreInit'=>intval($v['scoreInit']),
				'scoreRate'=>$v['scoreRate']+0,
				'rankRule'=>intval($v['rankRule']),
				'tableRule'=>intval($v['tableRule']),
				'outRule'=>intval($v['outRule']),
				'outValue'=>trim($v['outValue']),
				'awardRule'=>$v['awardRule'] && ($tmp = json_decode($v['awardRule'],1)) ? $tmp : array(),
				'apkurl'=>$v['apkurl'],
				'isForce'=>isset($v['isForce'])?intval($v['isForce']):0,
				'appid'=>intval($v['appid']),
				'ver'=>$v['ver'],
				'vercode'=>intval($v['vercode']),
				'bytes'=>$v['bytes']+0,
				'desc'=>trim($v['desc']),
				'md5'=>$v['md5'],
				'package'=>$v['package'],
				'sort'=>intval($v['sort']),
			);
		}
		$this->redis->hmset($this->key_list_room, $list);
		return $list;
	}
	//列表-获取-消费记录
	function listGetCost( $ud )
	{
		$ud = intval($ud);
		if ( $ud < 1 ) return array();
		$sql = "SELECT `id`,`gold`,`coins`,`coupon`,`propId`,`date` FROM `lord_user_cost` WHERE `uid` = $ud AND `is_del` = 0 ORDER BY `time` DESC";
		$res = $this->mysql->getData($sql);
		$res = $res ? $res : array();
		return $res;
	}
	//数组-获取-用户救济数据
	function getdataUserTrial( $ud )
	{
		$ud = intval($ud);
		if ( $ud < 1 ) return array('trial_count'=>99, 'trial_cooldown'=>86400);
		$dd = date("Ymd");
		
// 		$sql = "select `extend` from user_login where `uid`=$ud LIMIT 0,1";
// 		$deviceID = $this->mysql->getVar($sql);
// 		$data = null;
// 		if($deviceID)$data = $this->redis->hget("lord_trial_$dd", $deviceID);
// 		if(!$data || !$deviceID)
		    $data = $this->redis->hget("lord_trial_$dd", $ud);
		if ( !$data ) {
			$count = 0;
			$cd = microtime(1);
		} else {
			$data = explode("_", $data);
			$count = intval($data[0]);
			$cd = $data[1] + 0;
		}
		$cd = $cd - microtime(1);
		$cd = $cd > 0 ? ceil($cd) : 0;
		return array('trial_count'=>$count, 'trial_cooldown'=>$cd);
	}
	//数组-设置-用户救济数据
	function setdataUserTrial( $ud, $channel, $count )
	{
		$ud = intval($ud);
		if ( $ud < 1 ) return false;
		$dd = date("Ymd");
		$cd = $this->getvalueTrialCooldown($channel, $count);
// 		$sql = "select `extend` from user_login where `uid`=$ud LIMIT 0,1";
// 		$deviceID = $this->mysql->getVar($sql);
// 		if($deviceID)$this->redis->hset("lord_trial_$dd", $deviceID, $count.'_'.($cd+microtime(1)));
// 		else $this->redis->hset("lord_trial_$dd", $ud, $count.'_'.($cd+microtime(1)));
		return $this->redis->hset("lord_trial_$dd", $ud, $count.'_'.($cd+microtime(1)));
	}
	//列表-获取-救济基数倍率
	function getlistTrialCoins( $channel, $count=0 )
	{
		$channel = trim($channel);
		$count = intval($count) > 0 ? intval($count) : 0;//预留
		$res = $this->redis->hgetall('lord_list_trialcoins');
		if ( !($res && is_array($res)) ) {
			$res = array();
			$sql = "SELECT * FROM `lord_list_trialcoins` WHERE `is_del` = 0 ORDER BY `sort`";
			$res_= $this->mysql->getData($sql);
			if ( $res_ ) {
				foreach ( $res_ as $k => $v )
				{
					$res[$v['id']] = array('id'=>intval($v['id']),'channel'=>$v['channel'], 'value'=>intval($v['value']),'multiple'=>intval($v['multiple']),'probability'=>intval($v['probability']));
				}
				$this->redis->hmset('lord_list_trialcoins', $res);
			}
		}
		$list = array();
		foreach ( $res as $k => $v )
		{
			if ( ($v['channel'] && $v['channel'] != $channel) || !$v['value'] || !$v['multiple'] ) {
				continue;
			}
			$list[]= array('value'=>$v['value'], 'multiple'=>$v['multiple'], 'probability'=>$v['probability']);
		}
		if ( !$list ) {
			$list[]= array('value'=>1000, 'multiple'=>1, 'probability'=>10000);
		}
		return $list;
	}
	//数值-获取-某救济次数的冷却时间
	function getvalueTrialCooldown( $channel, $count=0 )
	{
		$channel = trim($channel);
		$count = intval($count) > 0 ? intval($count) : 0;
		$res = $this->redis->hgetall('lord_list_trialcd');
		if ( !($res && is_array($res)) ) {
			$res = array();
			$sql = "SELECT * FROM `lord_list_trialcd` WHERE `is_del` = 0 ORDER BY `sort`";
			$res_= $this->mysql->getData($sql);
			if ( $res_ ) {
				foreach ( $res_ as $k => $v )
				{
					$res[$v['count']] = array('id'=>intval($v['id']),'channel'=>$v['channel'],'count'=>intval($v['count']),'cooldown'=>intval($v['cooldown']));
				}
				$this->redis->hmset('lord_list_trialcd', $res);
			}
		}
		if ( !$res ) return 300;
		$list = array();
		foreach ( $res as $k => $v )
		{
			if ( !$v['channel'] ) $list[$v['count']] = intval($v['cooldown']);
		}
		foreach ( $res as $k => $v )
		{
			if ( $v['channel'] == $channel ) $list[$v['count']] = intval($v['cooldown']);
		}
		if ( !$list ) return 300;
		if ( isset($list[$count]) ) return $list[$count];
		$counts = array_keys($list);
		sort($counts);
		$needle = $count;
		$step = 0;
		foreach ( $counts as $k => $v )
		{
			if ( $needle >= $step && $needle <= $v ) {
				return $list[$v];
			}
			$step = $v;
		}
		return $list[end($counts)];
	}
	//减少-物品-库存数量 返回新的数量变化：-2无变化
	function ddaItemStore( $item, $num=1 )
	{
		if ( !isset($item['store']) || $item['store'] == -1 ) return -2;
		if ( !$item['store'] ) return 0;
		$item['store']--;
		$sql = "UPDATE `lord_list_convert` SET `store` = `store` - 1 WHERE `id` = ".$item['id'];
		$res = $this->mysql->runSql($sql);
		$res = $this->redis->hset('lord_list_convert', $item['id'], $item);
		return $item['store'];
	}

	//获取-列表-充值乐币
	function getlistGold( $channel, $is_list=0 )
	{
		$channel = trim($channel);
		$res = $this->prop->getlistGoods($channel, 1);//充值乐币
		$list = array();
		foreach ( $res as $k => $v )
		{
			if ( !$v['id'] || !$v['cd'] || !$v['name'] || !($v['fileId'] || $v['is_hide']) || !$v['money'] || !$v['price'] ) continue;
			$list[$v['id']] = array('id'=>intval($v['id']), 'cd'=>intval($v['cd']), 'name'=>$v['name'], 'fileId'=>intval($v['fileId']), 'value'=>intval($v['buyto']['gold']), 'price'=>intval($v['price']), 'store'=>intval($v['store']), 'is_onsale'=>intval($v['is_onsale']), 'onsale'=>$v['onsale'], 'is_recommend'=>intval($v['is_recommend']), 'is_hide'=>intval($v['is_hide']), 'sort'=>intval($v['sort']));
		}
		if ( !$is_list ) {
			// $sorts = array();
			// foreach ( $list as $k => $v )
			// {
			// 	$sorts[$k] = $v['sort'];
			// }
			// array_multisort($list, $sorts);
			$list = array_values($list);
		}
		return $list;
	}

	//获取-列表-兑换乐豆
	function getlistRecharge( $channel, $is_list=0, $is_recommend=0 )
	{
		$channel = trim($channel);
		$res = $this->prop->getlistGoods($channel, 2);//购买乐豆
		$list = array();
		foreach ( $res as $k => $v )
		{
			if ( !$v['id'] || !$v['cd'] || !$v['name'] || !($v['fileId'] || $v['is_hide']) || !$v['money'] || !$v['price'] ) continue;
			if ( $is_recommend ) {
				$list[$v['id']] = array('id'=>intval($v['id']), 'cd'=>intval($v['cd']), 'name'=>$v['name'], 'resume'=>$v['resume'], 'fileId'=>intval($v['fileId']), 'value'=>intval($v['buyto']['coins']), 'price'=>intval($v['price']), 'taskid'=>intval($v['taskid']), 'store'=>intval($v['store']), 'is_onsale'=>intval($v['is_onsale']), 'onsale'=>$v['onsale'], 'is_recommend'=>intval($v['is_recommend']), 'is_hide'=>intval($v['is_hide']), 'sort'=>intval($v['sort']));
			} else {
				$list[$v['id']] = array('id'=>intval($v['id']), 'type'=>'gold2coins', 'title'=>$v['name'], 'fileId'=>intval($v['fileId']), 'value'=>intval($v['buyto']['coins']), 'price'=>intval($v['price']), 'taskid'=>intval($v['taskid']), 'store'=>intval($v['store']), 'is_onsale'=>intval($v['is_onsale']), 'onsale'=>$v['onsale'], 'is_hide'=>intval($v['is_hide']), 'sort'=>intval($v['sort']));
			}
		}
		if ( !$is_list ) {
			// $sorts = array();
			// foreach ( $list as $k => $v )
			// {
			// 	$sorts[$k] = $v['sort'];
			// }
			// array_multisort($list, $sorts);
			$list = array_values($list);
		}
		return $list;
	}
	//获取-列表-兑换面板
	function getlistConvert( $channel, $type="", $is_list=0, $mincost=null )
	{
		$channel = trim($channel);
		$res = $this->redis->hgetall('lord_list_convert');
		if ( !($res && is_array($res)) ) {
			$res = array();
			$sql = "SELECT * FROM `lord_list_convert` WHERE `state` = 0 ORDER BY `sort`";
			$res_= $this->mysql->getData($sql);
			if ( $res_ ) {
				foreach ( $res_ as $k => $v )
				{
					$v['channel'] = $v['channel'] ? explode(' ', $v['channel']) : array();
					$res[$v['id']] = array('id'=>intval($v['id']),'channel'=>$v['channel'], 'type'=>$v['type'],'title'=>$v['title'],'fileId'=>intval($v['fileId']),'value'=>intval($v['value']),'price'=>intval($v['price']),'recharge'=>intval($v['recharge']),'store'=>intval($v['store']),'is_recommend'=>intval($v['is_recommend']),'is_onsale'=>intval($v['is_onsale']),'onsale'=>$v['onsale'],'sort'=>intval($v['sort']));
				}
				$this->redis->hmset('lord_list_convert', $res);
			}
		}
		$list = array();
		foreach ( $res as $k => $v )
		{
			if ( ($type && $type != 'recommend' && $v['type'] != $type) || ($type && $type == 'recommend' && (!isset($v['is_recommend'])||!$v['is_recommend']))
			  || ($v['channel'] && !in_array($channel, $v['channel'])) || !$v['id'] || !$v['type'] || !$v['title'] || !$v['fileId'] || !$v['value'] || !$v['price'] ) {
				continue;
			}
			if ( isset($v['recharge']) && $mincost !== null && $mincost < $v['recharge'] ) continue;
			$list[$v['id']] = array('id'=>$v['id'], 'type'=>$v['type'], 'channel'=>$v['channel'], 'title'=>$v['title'], 'fileId'=>$v['fileId'], 'value'=>$v['value'], 'price'=>$v['price'], 'store'=>$v['store'], 'is_onsale'=>$v['is_onsale'], 'onsale'=>$v['onsale'], 'sort'=>$v['sort']);
		}
		if ( !$is_list && $list ) {
			$sorts = array();
			foreach ( $list as $k => $v )
			{
				unset($v['channel']);
				$list[$k] = $v;
				$sorts[$k] = $v['sort'];
			}
			array_multisort($list, $sorts);
			$list = array_values($list);
		}
		return $list;
	}
	//获取-列表-商品面板
	function getlistGoods( $channel, $is_list=0, $isAll=0 )
	{
		$channel = trim($channel);
		$ext = 1;//带有物品道具扩展iid/icd/ipd的数据结构，这个数据结构，将在有用户背包之后舍弃
		$res = $this->prop->getlistGoods($channel, 4, $ext);//道具商品
		$res2 = $isAll ? $this->prop->getlistGoods($channel, 3, $ext) : array();//试衣间商品
		if ( $res2 ) {
			foreach ( $res2 as $k => $v )
			{
				$res[$k] = $v;
			}
		}
		$list = array();
		foreach ( $res as $k => $v )
		{
			if ( $isAll && $v['cd'] < 3 ) continue;
			if ( !$v['id'] || !$v['cd'] || !$v['name'] || !($v['fileId'] || $v['is_hide']) || !$v['money'] || !$v['price'] ) continue;
			if ( $ext ) {
				$list[$v['id']] = array('id'=>intval($v['id']), 'cd'=>intval($v['cd']), 'iid'=>intval($v['iid']), 'icd'=>intval($v['icd']), 'ipd'=>intval($v['ipd']), 'name'=>$v['name'], 'resume'=>$v['resume'], 'fileId'=>intval($v['fileId']), 'taskid'=>intval($v['taskid']), 'money'=>$v['money'], 'price'=>intval($v['price']), 'buyto'=>$v['buyto'], 'store'=>intval($v['store']), 'is_onsale'=>intval($v['is_onsale']), 'onsale'=>$v['onsale'], 'is_recommend'=>intval($v['is_recommend']), 'is_hide'=>intval($v['is_hide']), 'sort'=>intval($v['sort']));
			} else {
				$list[$v['id']] = array('id'=>intval($v['id']), 'cd'=>intval($v['cd']), 'name'=>$v['name'], 'resume'=>$v['resume'], 'fileId'=>intval($v['fileId']), 'taskid'=>intval($v['taskid']), 'money'=>$v['money'], 'price'=>intval($v['price']), 'buyto'=>$v['buyto'], 'store'=>intval($v['store']), 'is_onsale'=>intval($v['is_onsale']), 'onsale'=>$v['onsale'], 'is_recommend'=>intval($v['is_recommend']), 'is_hide'=>intval($v['is_hide']), 'sort'=>intval($v['sort']));
			}
		}
		if ( !$is_list ) {
			// $sorts = array();
			// foreach ( $list as $k => $v )
			// {
			// 	$sorts[$k] = $v['sort'];
			// }
			// array_multisort($list, $sorts);
			$list = array_values($list);
		}
		return $list;
	}
	//记录 - 兑换 - 奖券到话费
	function recordConvert( $user, $item, $num, $other )
	{
		switch ($item['type']) {
			case 'coupon2mobifee':
				$conv_fr = 'coupon';
			break;
			default:
				$conv_fr = 'coupon';
			break;
		}
		$sql = "INSERT INTO `lord_record_convert` (`dateid`, `uid`, `cool_num`, `nick`, `channel`, ";
		$sql.= "`iid`, `type`, `title`, `num`, `cost`, `after`, `other`, ";
		$sql.= "`oid`, `state`, `comments`, `create_time`, `update_time`) ";
		$sql.= "VALUES (".date("Ymd").",".$user['uid'].",".$user['cool_num'].",'".$user['nick']."','".$user['channel']."',";
		$sql.= $item['id'].",'".$item['type']."','".$item['title']."',$num,".intval($item['price']*$num).",".$user[$conv_fr].",'$other',";
		$sql.= "0,0,'',".time().",0)";
		bobSql($sql);
		return true;
	}
	//获取-列表-道具列表
	function getlistProp()
	{
		$list = $this->prop->getlistProp();//所有道具
		return $list;
	}

	//获取 - 列表 - 全部(用户)任务
	function getlistTesk( $user=array() )
	{
		if ( $user ) {
			$tesk = new tesk($this->mysql, $this->redis);
			return $tesk->getUserTeskAndList($user);
		} else {
			$data_tesk_list = array();
			include(ROOT.'/include/data_tesk_list.php');
			return array('usertesk'=>array(), 'tesklist'=>$data_tesk_list);
		}
	}
	// 设置
	function setUserTesk( $ud, $info )
	{
		$ud = intval($ud);
		if ( $ud < 1 || !$info || !is_array($info) ) return false;
		return $this->redis->hmset("lord_user_tesk_$ud", $info);
	}
	// 增减 原子操作
	function incUserTesk( $ud, $info )
	{
		$ud = intval($ud);
		if ( $ud < 1 || !$info || !is_array($info) ) return false;
		foreach ( $info as $k => $v ) {
			$v = intval($v);
			if ( !$v ) continue;
			$res = $this->redis->hincrby("lord_user_tesk_$ud", $k, $v);
			if ( $res===false ) return false;
			$info[$k] = $res;
		}
		return $info;
	}
	// 入库
	function updUserTesk( $ud, $now=false )
	{
		$key = 'lord_user_tesk_'.$ud;
		$tesk = $this->redis->hgetall($key);
		if ( !$tesk ) return false;
		$sql = "REPLACE INTO `lord_user_tesk` ( `uid`, `teskCode`, `update_time` ) VALUES ( $ud, '".addslashes(json_encode($tesk))."', ".time()." )";
		if ( $now ) {
			$res = $this->mysql->runSql($sql);
		} else {
			$res = bobSql($sql);
		}
		return $this->redis->del($key);
	}
	// 获取未获得额外炸弹的局数
	function getMissBomb( $ud )
	{
		$dateid = intval(date("Ymd"));
		$wd = intval(date("Ymd", time()-(date("N")-1)*86400));
		$key = "lord_miss_bomb_$wd";
		$val = $this->redis->hget($key, $ud);
		return $val ? $val : 3;
	}
	// 设置未获得额外炸弹的局数
	function setMissBomb( $ud, $val )
	{
		$dateid = intval(date("Ymd"));
		$wd = intval(date("Ymd", time()-(date("N")-1)*86400));
		if ( $dateid == $wd && date("His") == '010101' ) {
			$this->redis->del("lord_miss_bomb_".date("Ymd", time() - 7 * 86400));
		}
		$key = "lord_miss_bomb_$wd";
		$res = $this->redis->hset($key, $ud, $val);
		return $res;
	}
	// 新设包月乐豆领取情况/叠加
	function newMcard( $ud, $secs=0, $sttm=0 )
	{
		$ud = intval($ud); if ( $ud < 1 ) return false;
		$secs = intval($secs); if ( $secs < 1 ) $secs = max(30,intval(date('t'))) * 86400;
		$sttm = intval($sttm); if ( $sttm < 1 ) $sttm = time();
		$card = $this->redis->hget("lord_user_mcard", $ud);
		if ( $card ) {
			$card = explode('_', $card);
			if ( intval($card[0]) < time() ) {
				$card = false;
			}
		}
		if ( $card ) {
			$card[0] += $secs;
			$card = join('_', $card);
		} else {
			$card = $sttm + $secs;
		}
		$res = $this->redis->hset("lord_user_mcard", $ud, $card);
		return $res;
	}
	// 获取包月乐豆领取情况 0无1未领2已领
	function getMcard( $ud )
	{
		$card = $this->redis->hget("lord_user_mcard", $ud);
		if ( $card ) {
			$tdid = intval(date("Ymd"));
			$card = explode('_', $card);
			$entm = intval($card[0]);
			$last = intval(isset($card[1])?end($card):0);
			if ( $entm < time() ) {
				return 0;
			} elseif ( $last === $tdid ) {
				return 2;
			} else {
				return 1;
			}
		} else {
			return 0;
		}
	}
	// 使用包月卡领取乐豆
	function useMcard( $U )
	{
		if ( !is_array($U) || !$U || !isset($U['uid']) || !isset($U['fd']) || !isset($U['tableId']) ) return false;
		$ud = intval($U['uid']); if ( $ud < 1 ) return false;
		$fd = $U['fd']; if ( !$fd ) return false;
		$td = $U['tableId'];
		$sd = $U['seatId'];
		$conf_mcard_coins = 38888;//配置
		$card = $this->redis->hget("lord_user_mcard", $ud);
		if ( !$card ) {
			$cmd = 4; $code = 226; $send = array('errno'=>1, 'error'=>"您还没有购买包月卡，请到商城购买。\n购买后，每天可领取{$conf_mcard_coins}乐豆。");
			sendToFd($fd, $cmd, $code, $send);
			return false;
		}
		$tdid = intval(date("Ymd"));
		$card = explode('_', $card);
		$entm = intval($card[0]);
		$last = intval(isset($card[1])?end($card):0);
		if ( $entm < time() ) {
			$cmd = 4; $code = 226; $send = array('errno'=>2, 'error'=>"您的月卡当前已过期，请到商城续费。\n续费后，每天可领取{$conf_mcard_coins}乐豆。");
			sendToFd($fd, $cmd, $code, $send);
			return false;
		}
		if ( $last === $tdid ) {
			$cmd = 4; $code = 226; $send = array('errno'=>3, 'error'=>"您今天已经领过月卡俸禄，请明天再来领取。");
			sendToFd($fd, $cmd, $code, $send);
			return false;
		}
		$card[] = $tdid;
		$card = join('_', $card);
		$res = $this->redis->hset("lord_user_mcard", $ud, $card);
		$addU = array('coins'=>$conf_mcard_coins);
		$res = $this->incUserInfo($ud, $addU); unset($addU);
		$U['coins'] += $conf_mcard_coins;
		$this->record->money('领取俸禄', 'coins', $conf_mcard_coins, $ud, $U);
		if ( $td ) {
			$lock = $td;
			setLock($lock);
			if ( $table = $this->getTableInfo($td) ) {
				$addT = array("seat{$sd}coins"=>$conf_mcard_coins);
				$this->incTableInfo($td, $addT); unset($addT);
			}
			delLock($lock);
		}
		if ( $res && $res['send'] ) {
			$cmd = 4; $code = 110; $send = $res['send'];
			sendToFd($fd, $cmd, $code, $send);
		}
		$cmd = 4; $code = 226; $send = array('errno'=>0, 'error'=>"包月会员您好，本次领取{$conf_mcard_coins}乐豆。");
		sendToFd($fd, $cmd, $code, $send);
		return true;
	}
	//叠加增益状态
	function addBuff( $ud, $pd, $user=array() )
	{
		if ( ! $user ) $user = $this->getUserInfo($ud);
		if ( ! $user || ! isset($user['buff']) ) $user['buff'] = array();
		if ( ! isset($user['buff'][$pd]) ) $user['buff'][$pd] = 0;
		$user['buff'][$pd]++;
		setUser($ud, array('buff'=>$user['buff']));
		return $user['buff'];
	}
	//清除增益状态
	function ddaBuff( $ud, $pd, $user=array() )
	{
		if ( ! $user ) $user = $this->getUserInfo($ud);
		if ( ! $user || ! isset($user['buff']) ) $user['buff'] = array();
		if ( ! isset($user['buff'][$pd]) ) $user['buff'][$pd] = 1;
		//监控代码，暂时勿删
		$sql = "SELECT `num` FROM `lord_user_item` WHERE `uid` = $ud AND `pd` = $pd";
		$num1 = $this->mysql->getVar($sql);
		if ( ! $num1 ) {
			gerr("幸运牌局监控 U=$ud num1=$num1 sql=$sql");
		}
		$user['buff'][$pd]--;
		if ( ! $user['buff'][$pd] ) unset($user['buff'][$pd]);
		if ( ! $user['buff'] ) $user['buff'] = array();
		setUser($ud, array('buff'=>$user['buff']));
		$lock = 'PROP_'.$ud;
		setLock($lock);
		$res = $this->prop->useuse($ud, $pd, array(), 1);
		delLock($lock);
		if ( $res === false ) {
			$error = $this->prop->getError();
			gerr("消除增益失败 U=$ud errno=".$error['errno'].' error='.$error['error']);
		}
		$sql = "SELECT `num` FROM `lord_user_item` WHERE `uid` = $ud AND `pd` = $pd";
		$num2 = $this->mysql->getVar($sql);
		if ( $num2 >= $num1 ) {
			gerr("幸运牌局监控 U=$ud num2=$num2 num1=$num1 sql=$sql");
		}
		return $user['buff'];
	}
	//获取用户摇摇乐次数
	function getLuckyShake($uid,$dateid,$roomid){
	    if ( !$uid || !$dateid || !$roomid) return false;
	    $num  = $this->redis->hget("key_lucky_shake_info_".$uid.'_'.$dateid,$roomid);
	    if(!$num){
	        //数据库中同步数据
	        $shakeinfo = $this->mysql->getLine("SELECT count(*) as num FROM `lord_lucky_shake_log` WHERE `uid` = $uid and  `dateid` = $dateid and `roomid` = $roomid");
	        if($shakeinfo){
	            $this->redis->hset("key_lucky_shake_info_".$uid.'_'.$dateid, $roomid,$shakeinfo['num']);
	            $this->redis->expire("key_lucky_shake_info_".$uid.'_'.$dateid,60*60*24);
	        }
	    }
	    return $this->redis->hget("key_lucky_shake_info_".$uid.'_'.$dateid,$roomid);
	}
	function setLuckyShake($uid,$dateid,$roomid,$num){
	    return $this->redis->hset("key_lucky_shake_info_".$uid.'_'.$dateid, $roomid,$num);
	}
	//用户进行一次摇摇乐
	function doLuckyShake($uid,$dateid,$roomid,$consume_coins,$win_coins,$type,$num){
	    if ( !$uid || !$dateid || !$roomid) return false;
	    $res =$this->setLuckyShake($uid,$dateid,$roomid,$num);
	    if ( !$res ) return false;
	    $time = time();
	    $sql = "INSERT INTO `lord_lucky_shake_log` (`dateid`,`uid`,`roomid`,`consume_coins`,`type`,`win_coins`, `time`) VALUES ($dateid,$uid,$roomid,$consume_coins,'$type',$win_coins,$time)";
	    $res = $this->mysql->runSql($sql);
	    if ( !$res ) {
	        $this->redis->hdel("key_lucky_shake_info_".$uid.'_'.$dateid, $roomid);
	        return gerr("MYSQL->runSql($sql)");
	    }
	    return $res;
	}
	//获取产品版本号
	function getPrizeVersion(){
	    $result = $this->mysql->getLine("SELECT * FROM `lord_game_prize_version` ");
	    return $result['version'];
	}
	//比较版本号
	public function checkNowVersion($version1, $version2){

	    $v1 = explode('.', $version1);
	    $v2 = explode('.', $version2);
	    if( (int)$v1[0] < (int)$v2[0] ) {
	        return false;
	    }elseif((int)$v1[0] <= (int)$v2[0] && (int)$v1[1] < (int)$v2[1] ) {
	        return false;
	    }elseif( (int)$v1[0] <= (int)$v2[0] && (int)$v1[1] <= (int)$v2[1] && (int)$v1[2] < (int)$v2[2] ) {
	        return false;
	    }

	    return true;
	}
	public function getPrizeList($version)
	{
	    $key = "lord_game_prize_version_$version";
	    $data = $this->redis->hgetall($key);
	    if(!$data){
	        $type = 0;
	        $info = array();
	        $sql = "SELECT * FROM `lord_game_prize_new` where `version` = $version order by `type`, `picture_id`";
	        $data = $this->mysql->getData($sql);
	        if(!empty($data)){
	            foreach($data as $k => $v){
	                if($v['type'] != $type){
	                    $type = $v['type'];
	                }
	                $info[$type][] = $v;
	            }
	        }
	        $this->redis->hmset($key,$info);
	        $data = $info;
	    }
	    return $data;
	}
	public function getGoods()
	{
	    $key = "lord_list_goods";
	    $info  = $this->redis->hgetall($key);
	    if(!$info){
	        $sql = "SELECT * FROM `lord_list_goods` WHERE `state` = 0 ORDER BY `cd`, `sort`";
	        $info = $this->mysql->getData($sql);
	        $this->redis->hmset($key,$info);
	    }
	    return $info;
	}
	public function getLuckyDrawStaticsResult($uid,$dateid){
	    $sql = "select COALESCE(sum(coins),0) as coins, COALESCE(sum(coupon),0) as coupon,count(*) as num from `lord_record_lottery` where `dateid` = $dateid and `uid` = $uid";
	    $result = $this->mysql->getLine($sql);
	    return $result;
	}
    public function getNRewardList($uid, $ver=0){
        $result = array();
        $testInfo = $this->getUserTesk($uid);
        include(ROOT.'/include/data_tesk_id_list.php');
        if(!empty($data_tesk_id_list)){
            foreach($data_tesk_id_list as $key=>$val){
                $item = array();
                if(!empty($data_tesk_id_list[$key])){
                    $task_target = $data_tesk_id_list[$key]['target'];
                    $teskid= $data_tesk_id_list[$key]['id'];
                    $key1 = "teskvalue_$teskid";
                    $times = isset($testInfo[$key1])?$testInfo[$key1]:0;
                    $item['task_left'] = $task_target - $times;
                    $item['target'] = $task_target;
                    $item['reward'] = $data_tesk_id_list[$key]['reward'];
                    if($ver >=$this->confs["time_auto_play_new_version"] && ($key === 1000 || $key === 1007))$item['target'] =0;
                }
                $result[$key] = $item;
            }

        }
        return $result;

    }
    //比较牌桌内所有用户的版本号，1.8.0之后增加双倍功能,$version>10800
    public function checkTableVersion( $table, $vercode )
	{
		if ( ! $table['seat0robot'] && $table['seat0vercode'] < $vercode ) return false;
		if ( ! $table['seat1robot'] && $table['seat1vercode'] < $vercode ) return false;
		if ( ! $table['seat2robot'] && $table['seat2vercode'] < $vercode ) return false;
		return true;
    }

    // 获取新版用户抽奖记录
    function getNewUserLottery( $ud )
    {
        $ud = intval($ud); if ( $ud < 1 ) return false;
        $lastid = date("Ymd", time() - 8 * 86400);//一周内
        $data_lottery_prizes_new = array();
        include(ROOT.'/include/data_lottery_prizes_new.php');
        $prizes = $data_lottery_prizes_new;
        $sql = "SELECT * FROM `lord_record_lottery` WHERE `uid` = $ud AND `dateid` > $lastid order by id desc limit 48";
        $res = $this->mysql->getData($sql);
        $res = ( $res && is_array($res) ) ? $res : array();
        $list = array();
        foreach ( $res as $k => $v )
        {
            if ( !isset($prizes[$v['prizeid']]) && !isset($prizesali[$v['prizeid']]) ) continue;
            $name = isset($prizes[$v['prizeid']]) ? $prizes[$v['prizeid']]['name'] : "";
            $list[$v['ut_create']] = array('id'=>intval($v['id']), 'name'=>$name, 'datetime'=>date("Y-m-d H:i:s",$v['ut_create']), 'ut_create'=>$v['ut_create']);
        }
        if ( $list ) {
            krsort($list);
            $list = array_values($list);
        }
        return $list;
    }

	// 用户执行某个操作前，用户IP的前三段是否已经有过2次使用
	//$tag		str 	操作标记
	//$ip 		str 	用户IP
	//return 	bool 	false没有超过|true已经满2次
	function ippban( $tag, $ip )
	{
		if ( ! $tag || ! $ip ) return gerr("IPPBAN参数无效 tag=$tag ip=$ip");
		if ( false !== strpos($tag, '_') ) return gerr("IPPBAN参数无效 tag=$tag ip=$ip");
		$key = "lord_ippban_{$tag}_".dateid();
		$ip = explode('.', $ip);
		if ( count($ip) != 4 ) return gerr("IPPBAN参数无效 tag=$tag ip=".join('.', $ip));
		array_pop($ip);
		$ip = join('_', $ip);
		$num = $this->redis->hget($key, $ip);
		if ( $num > 1 ) return true;//配置2
		$this->redis->hadd($key, $ip);
		return false;
	}

	//Tidel 每天乐豆统计
	function coinsStat($uid, $add=0)
    {
        if($uid < 100000)return ;
        $coinsStatTime = $this->redis->hget("lord_coins_stat_time", $uid);
        if(date("Ymd",$coinsStatTime) != date("Ymd",time()))
        {
            $this->redis->hset("lord_coins_stat_time", $uid, time());
            $this->redis->hset("lord_coins_stat", $uid, 0);
        }
        $coins = $this->redis->hincrby("lord_coins_stat", $uid, $add);
        return $coins;
    }
    
}
