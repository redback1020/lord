<?php
/**
* PHP 可编辑的活动任务
* 后台创建[更新|上线|下线|删除] -> 数据库更新数据 -> aip.php?api=tesk... -> redis更新数据 -> redis创建各服事件 -> 各服执行任务 -> 写入本服任务文件
* 用户操作 -> include本服任务文件 -> 获取redis中的任务周期 -> 逐个执行某action/acttag下的各个任务：
* -> 校验渠道+用户+房间 -> 检查周期时间+周期号 -> 如果任务周期过期 -> 写锁 -> redis更新所有任务周期+数据库异步更新任务周期 -> 解锁 ->
* -> 执行用户数据增减 -> 识别任务达成 -> 识别是否有暴奖 -> 获取redis中的暴奖列表 -> 暴奖概率校验+暴奖次数校验 -> 暴奖数据处理 -> 返回增减数据+发奖邮件+暴奖数据
*/
class tesk
{
	public $errno = 0;
	public $error = '';
	public $mysql = null;
	public $redis = null;
	public $local = null;
	public $mysql_game_user = 'lord_game_user';//uid,nick,...
	public $mysql_game_tesk = 'lord_game_tesk';//id,name,...
	public $mysql_game_surprise = 'lord_game_surprise';//id,name,...
	public $mysql_user_teskrecord = 'lord_user_teskrecord';//id,uid,teskId,...
	public $mysql_user_tesksurprise = 'lord_user_tesksurprise';//id,uid,teskId,...
	public $mysql_user_tesk = 'lord_user_tesk';//uid,teskCode,update_time
	public $mysql_user_inbox = 'lord_user_inbox';//id,uid,subject,...
	public $key_game_tesk = 'lord_game_tesk';//teskId:[{teskId:1,...},...],...
	public $key_game_surprise = 'lord_game_surprise';//id,name,...
	public $key_user_info = KEY_USER_;//.$uid uid:111,coins:1000,...
	public $key_user_tesk = 'lord_user_tesk_';//.$uid update_time:0,tesk111:0(未完成),tesk111pid:22(周期id),tesk111val:100(本周期的值),
	public $key_tesk_period = 'lord_tesk_period';//periodTime_22:86400,periodId_22:123,periodStart_12:1234567890,periodEnd_12:1234569890,...
	public $key_surprise_rests = 'lord_surprise_rests';//times_22:1000,rests_22:88,periodTime_22:86400,periodId_22:123,periodStart_22:1234567890,periodEnd_22:1234569890,...
	public $key_user_tesksurprise = 'lord_user_tesksurprise';//id:{id:111,teskId,...}
	public $key_user_grabmail = "lord_user_grabmail";//id:{id:111,uid:0,...}
	public $key_queue_push = "lord_queue_push";
	public $key_queue_tsgrab_ = "lord_queue_tsgrab_";//争抢暴奖队列
	public $file_tesk = "/include/data_tesk_list.php";
	public $file_surprise = "/include/data_surprise_list.php";
	public $uid = 0;
	public $accode = 0;
	public $action = '';
	public $acttag = '';
	public $periods = null;
	public $teskall = null;
	public $teskpart = null;
	public $surprise = null;
	public $suchance = null;
	public $userinfo = null;
	public $usertask = null;
	public $usertesk = null;
	public $grab_time = 8;

	public function __construct( $mysql=null, $redis=null, $accode=0, $action='', $acttag='' )
	{
		$this->mysql = $mysql;
		$this->redis = $redis;
		$this->accode = $accode;
		$this->action = $action;
		$this->acttag = $acttag;
		$this->file_tesk = ROOT.$this->file_tesk;
		$this->file_surprise = ROOT.$this->file_surprise;
	}

	public function getMysql()
	{
		if ( $this->mysql === null ) {
			$this->mysql = new DB;
		}
		return $this->mysql;
	}

	public function getRedis()
	{
		if ( $this->redis === null ) {
			$this->redis = new RD;
		}
		return $this->redis;
	}

	// 获取所有活动任务列表
	// return 		mix		false | array() | array('12'=>array('teskId'=>12,...),...)
	public function getTeskAll( $isAll=0 )
	{
		$time = time();
		if ( $this->teskall===null ) {
			// 读文件
			$teskall = $this->readTeskFile();
			if ( !$teskall ) {
				// // 读redis
				// $redis = $this->getRedis();
				// $teskall = $redis->hgetall($this->key_game_tesk);
				// if ( !$teskall ) {
				// 	// 读数据库
				// 	$mysql = $this->getMysql();
				// 	$sql = "SELECT * FROM `".$this->mysql_game_tesk."` WHERE `is_online` = 1 AND `is_del` = 0";
				// 	$data = $mysql->getData($sql);
				// 	$data = $data && is_array($data) ? $data : array();
				// 	foreach ( $data as $k => $v )
				// 	{
				// 		$teskall[$v['teskId']] = $v;
				// 	}
				// 	// 写redis
				// 	$teskall = $redis->hmset($this->key_game_tesk, $teskall);
				// }
				$teskall = array();
				// 写文件
				$this->writeTeskFile($teskall);
			}
			if ( !$isAll ) {
				foreach ( $teskall as $k => $v )
				{
					if ( !$v['is_online'] || $v['is_del'] || !($time>=$v['start_time'] && ($v['end_time']?($time<=$v['end_time']):true)) ) {
						unset($teskall[$k]);
					}
				}
			}
			$this->teskall = $teskall;
		}
		return $this->teskall;
	}

	// 获取某个活动任务数据
	// return 		array 	array() | array('teskId'=>1,...)
	public function getTeskOne( $teskId=0, $isAll=1 )
	{
		$teskall = $this->getTeskAll($isAll);
		return $teskId ? (isset($teskall[$teskId]) ? $teskall[$teskId] : array()) : reset($teskall);
	}

	// 获取当前功能或当前标记下的活动任务列表
	// return 		mix		false | array() | array('12'=>array('teskId'=>12,...),...)
	public function getTeskPart()
	{
		$teskpart = $teskall = $this->getTeskAll();
		if ( $this->action || $this->acttag ) {
			foreach ( $teskpart as $k => $v )
			{
				if ( ($this->action && $v['action'] && $this->action != $v['action']) || ($this->acttag && $v['acttag'] && $this->acttag != $v['acttag']) ) {
					unset($teskpart[$k]);
				}
			}
		}
		$this->teskpart = $teskpart;
		return $teskpart;
	}

	// 创建一个活动任务，并返回所有活动任务列表
	// teskId		int 	活动任务编号
	// data			array 	活动任务数据
	// return 		mix		false | array() | array('12'=>array('teskId'=>12,...),...)
	public function create( $teskId, $data )
	{
		$teskId = intval($teskId);
		$data = $data && is_array($data) ? $data : array();
		if ( $teskId <= 0 || !$data ) return false;
		$teskall = $this->getTeskAll(1);
		$teskall[$teskId] = $data;
		$this->writeTeskFile($teskall);
		$redis = $this->getRedis();
		$redis->hdel($this->key_tesk_period, "periodTime_{$teskId}");
		$redis->hdel($this->key_tesk_period, "periodStart_{$teskId}");
		$redis->hdel($this->key_tesk_period, "periodEnd_{$teskId}");
		return $this->teskall;
	}

	// 更新一个活动任务，并返回所有活动任务列表
	// teskId		int 	活动任务编号
	// data			array 	活动任务新数据
	// return 		mix		false | array() | array('12'=>array('teskId'=>12,...),...)
	public function update( $teskId, $data )
	{
		return $this->create($teskId, $data);//
		$teskId = intval($teskId);
		$data = $data && is_array($data) ? $data : array();
		if ( $teskId <= 0 || !$data ) return false;
		$dold = $this->getTeskOne($teskId);
		if ( !$dold ) return false;
		$data = array_merge($dold, $data);
		$this->teskall[$teskId] = $data;
		$this->writeTeskFile($this->teskall);
		$redis = $this->getRedis();
		$redis->hdel($this->key_tesk_period, "periodTime_{$teskId}");
		$redis->hdel($this->key_tesk_period, "periodStart_{$teskId}");
		$redis->hdel($this->key_tesk_period, "periodEnd_{$teskId}");
		return $this->teskall;
	}

	// 删除／下线一个活动任务，并返回所有活动任务列表
	// teskId		int 	活动任务编号
	// return 		mix		false | array() | array('12'=>array('teskId'=>12,...),...)
	public function delete( $teskId )
	{
		$teskId = intval($teskId);
		if ( $teskId <= 0 ) return false;
		$dold = $this->getTeskOne($teskId);
		if ( !$dold ) return false;
		unset($this->teskall[$teskId]);
		$this->writeTeskFile($this->teskall);
		$redis = $this->getRedis();
		$redis->hdel($this->key_tesk_period, "periodTime_{$teskId}");
		$redis->hdel($this->key_tesk_period, "periodStart_{$teskId}");
		$redis->hdel($this->key_tesk_period, "periodEnd_{$teskId}");
		return $this->teskall;
	}

	// 写入到本地文件夹
	public function writeTeskFile( $data, $teskId=0 )
	{
		$teskId = intval($teskId);
		if ( !$data || !is_array($data) ) return false;
		$data_tesk_list = array();
		if ( file_exists($this->file_tesk) ) {
			include $this->file_tesk;
		}
		if ( $teskId ) {
			$data_tesk_list[$teskId] = $data;
		} else {
			$data_tesk_list = $data;
		}
		$this->teskall = $data_tesk_list;
		//new code start
		$tmp = str_replace('include/','include/_',$this->file_tesk);
		$filename = $this->file_tesk;
		$ret = file_write($tmp, '<?php $data_tesk_list = '.var_export($data_tesk_list,true).';');
		$ret = shell_exec("mv -f $tmp $filename");
		return true;
		//new code end
		return file_write($this->file_tesk, '<?php $data_tesk_list = '.var_export($data_tesk_list,true).';');
	}

	// 从本地文件夹读出
	public function readTeskFile( $teskId=0 )
	{
		$teskId = intval($teskId);
		$data_tesk_list = array();
		if ( file_exists($this->file_tesk) ) {
			include $this->file_tesk;
		}
		$this->teskall = $data_tesk_list;
		if ( $teskId ) {
			if ( isset($data_tesk_list[$teskId]) ) {
				return $data_tesk_list[$teskId];
			} else {
				return array();
			}
		} else {
			return $data_tesk_list;
		}
	}

	// 写入到本地文件夹
	public function writeSurprise( $data, $id=0 )
	{
		$id = intval($id);
		if ( !$data || !is_array($data) ) return false;
		$data_surprise_list = array();
		if ( file_exists($this->file_surprise) ) {
			include $this->file_surprise;
		}
		if ( $id ) {
			$data_surprise_list[$id] = $data;
		} else {
			$data_surprise_list = $data;
		}
		$this->surprise = $data_surprise_list;
		//new code start
		$tmp = str_replace('include/','include/_',$this->file_surprise);
		$filename = $this->file_surprise;
		$ret = file_write($tmp, '<?php $data_surprise_list = '.var_export($data_surprise_list,true).';');
		$ret = shell_exec("mv -f $tmp $filename");
		return true;
		//new code end
		return file_write($this->file_surprise, '<?php $data_surprise_list = '.var_export($data_surprise_list,true).';');
	}

	// 从本地文件夹读出
	public function readSurprise( $id=0 )
	{
		$id = intval($id);
		$data_surprise_list = array();
		if ( file_exists($this->file_surprise) ) {
			include $this->file_surprise;
		}
		$this->surprise = $data_surprise_list;
		if ( $id ) {
			if ( isset($data_surprise_list[$id]) ) {
				return $data_surprise_list[$id];
			} else {
				return array();
			}
		} else {
			return $data_surprise_list;
		}
	}

	// 新增一个任务暴奖，并返回所有任务暴奖列表
	// id			int 	编号
	// data			array 	数据
	// return 		mix		false | array() | array('12'=>array('id'=>12,...),...)
	public function createSurprise( $id, $data )
	{
		$id = intval($id);
		if ( $id <= 0 || !$data || !is_array($data) ) return false;
		$list = $this->getSurprise();
		$list[$id] = $data;
		$this->writeSurprise($list);
		return $this->surprise;
	}

	// 更新一个任务暴奖，并返回所有任务暴奖列表
	// id			int 	编号
	// data			array 	数据
	// return 		mix		false | array() | array('12'=>array('id'=>12,...),...)
	public function updateSurprise( $id, $data )
	{
		$id = intval($id);
		if ( $id <= 0 || !$data || !is_array($data) ) return false;
		$list = $this->getSurprise();
		$list[$id] = $data;
		$this->writeSurprise($list);
		return $this->surprise;
	}

	// 删除一个任务暴奖，并返回所有任务暴奖列表
	// id			int 	编号
	// return 		mix		false | array() | array('12'=>array('id'=>12,...),...)
	public function deleteSurprise( $id )
	{
		$id = intval($id);
		if ( $id <= 0 || !$data || !is_array($data) ) return false;
		$dold = $this->getSurprise($id);
		if ( !$dold ) return false;
		unset($this->surprise[$id]);
		$this->writeSurprise($this->surprise);
		return $this->surprise;
	}

	// 获取全部暴奖列表/某个暴奖数据
	// id			int 	指定编号
	// return 		arr		array('id'=>12,...) | array('12'=>array('id'=>12,...),...)
	public function getSurprise( $id=0 )
	{
		$id = intval($id);
		$surprise = $this->surprise;
		if ( $this->surprise === null ) {
			$surprise = $this->readSurprise();
			if ( !$surprise ) {
				// $redis = $this->getRedis();
				// $surprise = $redis->hgetall($this->key_game_surprise);
				// if ( !$surprise ) {
					$surprise = array();
					$mysql = $this->getMysql();
					$sql = "SELECT * FROM `".$this->mysql_game_surprise."` WHERE `chance` > 0 AND `is_del` = 0";
					$data = $mysql->getData($sql);
					if ( !$data || !is_array($data) ) $data = array();
					foreach ( $data as $k => $v )
					{
						$surprise[$v['id']] = $v;
					}
					$surprise && $redis->hmset($this->key_game_surprise, $surprise);
				// }
			}
			$this->surprise = $surprise;
		}
		if ( $id ) {
			if ( isset($surprise[$id]) ) {
				return $surprise[$id];
			} else {
				return array();
			}
		} else {
			return $surprise;
		}
	}

	// 获取惊喜总概率
	public function getSuchance()
	{
		if ( $this->suchance === null ) {
			$surprise = $this->getSurprise();
			foreach ( $surprise as $id => $v )
			{
				$this->suchance += $v['chance'];
			}
			$this->suchance += 0;
		}
		return $this->suchance;
	}

	public function setTeskPeriod( $tesk )
	{
		if ( !is_array($tesk) || !isset($tesk['id']) || !isset($tesk['periodTime']) || !isset($tesk['periodStart']) || !isset($tesk['periodEnd']) ) {
			return false;
		}
		$id = $tesk['id'];
		$time = time();
		$data = array(
			"periodTime_{$id}"  => intval($tesk['periodTime']),
			"periodId_{$id}"    => 1,
			"periodStart_{$id}" => intval($tesk['periodStart']),
			"periodEnd_{$id}"   => intval($tesk['periodEnd']),
		);
		$n = ceil(($time - $data["periodEnd_{$id}"]) / $data["periodTime_{$id}"]);
		if ( $n > 0 ) {
			$data["periodId_{$id}"]    += $n;
			$data["periodStart_{$id}"] += intval($n * $data["periodTime_{$id}"]);
			$data["periodEnd_{$id}"]   += intval($n * $data["periodTime_{$id}"]);
		}
		$redis = $this->getRedis();
		$res = $redis->hmset($this->key_tesk_period, $data);
		$this->periods[$id] = $data;
		return $data;
	}

	// 获取全部(某个)任务的当前周期数据
	// id 			int 	任务id
	// return 		arr 	array('periodTime_22'=>86400,'periodId_22'=>123,'periodStart_12'=>1234567890,'periodEnd_12'=>1234569890)
	public function getTeskPeriod( $id=0, $isredis=0 )
	{
		$periods = $this->periods;
		if ( $periods === null || $isredis ) {
			$redis = $this->getRedis();
			$periods = $redis->hgetall($this->key_tesk_period);
			if ( !$periods ) $periods = array();
			$this->periods = $periods;
		}
		if ( $id ) {
			$id = intval($id);
			foreach ( $periods as $k => $v )
			{
				$_k = explode('_', $k);
				if ( count($_k) != 2 || $_k[1] != $id ) unset($periods[$k]);
			}
		}
		return $periods;
	}

	// 重置(需要的话)并返回某个任务的最新周期数据，需要外部加锁保证，来确保原子性
	// id 			int 	任务id
	// time 		int 	重置所用的时间基点，例如当前时间
	// return 		arr 	array('periodTime_22'=>86400,'periodId_22'=>123,'periodStart_12'=>1234567890,'periodEnd_12'=>1234569890)
	private function resetTeskPeriod( $tesk, $data, $time )
	{
		$id = $tesk['id'];
		$data = $this->getTeskPeriod($id, 1);//锁内重新强制从redis获取一次，确保原子性
		if ( !$data || !isset($data["periodId_{$id}"]) ) {
			$data = $this->setTeskPeriod($tesk);
		}
		$newp = array();
		if (!isset($data["periodTime_{$id}"]) ) $newp["periodTime_{$id}"]  = $data["periodTime_{$id}"]  = intval($tesk['periodTime']);
		if (!isset($data["periodStart_{$id}"])) $newp["periodStart_{$id}"] = $data["periodStart_{$id}"] = intval($tesk['periodStart']);
		if (!isset($data["periodEnd_{$id}"])  ) $newp["periodEnd_{$id}"]   = $data["periodEnd_{$id}"]   = intval($tesk['periodEnd']);
		if ( $newp ) {
			$redis = $this->getRedis();
			$redis->hmset($this->key_tesk_period, $newp);
			foreach ( $newp as $k => $v )
			{
				$this->periods[$id][$k] = $v;
			}
		}
		$n = ceil(($time - $data["periodEnd_{$id}"] - 1) / $data["periodTime_{$id}"]);
		if ( $n > 0 ) {
			$newp = array();
			$redis = $this->getRedis();
			$newp["periodId_{$id}"]   = $data["periodId_{$id}"]   = $redis->hincrby($this->key_tesk_period, "periodId_{$id}", intval($n));
			$newp["periodStart_{$id}"]= $data["periodStart_{$id}"]= $redis->hincrby($this->key_tesk_period, "periodStart_{$id}", intval($n * $data["periodTime_{$id}"]));
			$newp["periodEnd_{$id}"]  = $data["periodEnd_{$id}"]  = $redis->hincrby($this->key_tesk_period, "periodEnd_{$id}",   intval($n * $data["periodTime_{$id}"]));
			foreach ( $newp as $k => $v )
			{
				$this->periods[$id][$k] = $v;
			}
		}
		return $data;
	}

	private function checkTeskPeriod( $tesk, $time )
	{
		$id = $tesk['id'];
		$data = $this->getTeskPeriod($id);
		if ( !$data || !isset($data["periodId_{$id}"]) ) {
			$data = $this->setTeskPeriod($tesk);
		}
		$newp = array();
		if (!isset($data["periodTime_{$id}"]) ) $newp["periodTime_{$id}"]  = $data["periodTime_{$id}"]  = intval($tesk['periodTime']);
		if (!isset($data["periodStart_{$id}"])) $newp["periodStart_{$id}"] = $data["periodStart_{$id}"] = intval($tesk['periodStart']);
		if (!isset($data["periodEnd_{$id}"])  ) $newp["periodEnd_{$id}"]   = $data["periodEnd_{$id}"]   = intval($tesk['periodEnd']);
		if ( $newp ) {
			$redis = $this->getRedis();
			$redis->hmset($this->key_tesk_period, $newp);
			foreach ( $newp as $k => $v )
			{
				$this->periods[$id][$k] = $v;
			}
		}
		$ptime = $data["periodTime_{$id}"];	//周期循环时长
		$pid = $data["periodId_{$id}"];		//本周期id
		$pstart = $data["periodStart_{$id}"];//本周期开始时间
		$pend = $data["periodEnd_{$id}"];	//本周期结束时间
		// 任务无周期时，任务周期、用户参与情况，不做任何变化
		if ( !$ptime || !$pstart || !$pend ) {
			return intval($pid);
		}
		// 任务已过期时，加锁，重置任务周期，获取最新任务周期数据
		if ( $time > $pend ) {
			$lockId = __FUNCTION__."_{$id}";
			$redis = $this->getRedis();
			$res = $redis->workLock($lockId, 1);//必须用互斥锁
			if ( !$res ) {
				return false;//加锁失败时，此任务强制失效
			}
			$data = $this->resetTeskPeriod($tesk, $data, $time);
			$ptime = $data["periodTime_{$id}"];
			$pid = $data["periodId_{$id}"];
			$pstart = $data["periodStart_{$id}"];
			$pend = $data["periodEnd_{$id}"];
			$res = $redis->workDone($lockId);
		}
		// 本期任务不在执行时间内时，不再继续
		if ( !($pstart <= $time && $time <= $pend) ) {
			return false;
		}
		return intval($pid);
	}

	// 获取某暴奖次数
	public function getSurpriseRests( $id, $surprise )
	{
		$time = time();
		$redis = $this->getRedis();
		$data = $redis->hmget($this->key_surprise_rests, array("periodTime_{$id}","periodId_{$id}","periodStart_{$id}","periodEnd_{$id}","times_{$id}","rests_{$id}"));
		if ( !is_array($data) || count($data) != 6 ) {
			$lockId = __FUNCTION__."_{$id}";
			$redis = $this->getRedis();
			$res = $redis->workLock($lockId, 1);
			if ( !$res ) {
				return 0;//撞锁失败时，此暴奖强制失效
			}
			$data["periodTime_{$id}"] = $surprise["periodTime"];
			$data["periodId_{$id}"] = 1;
			$data["periodStart_{$id}"] = $surprise["periodStart"];
			$data["periodEnd_{$id}"] = $surprise["periodEnd"];
			$data["times_{$id}"] = $surprise["times"];
			$data["rests_{$id}"] = $surprise["times"];
			$data['total_{$id}'] = 0;
			if ( $data["periodTime_{$id}"] && $data["periodStart_{$id}"] && $data["periodEnd_{$id}"] && $data["times_{$id}"] ) {
				$n = ceil(($time - $data["periodEnd_{$id}"]) / $data["periodTime_{$id}"]);
				$data["periodId_{$id}"]   += $n;
				$data["periodStart_{$id}"]+= intval($n * $data["periodTime_{$id}"]);
				$data["periodEnd_{$id}"]  += intval($n * $data["periodTime_{$id}"]);
			}
			$res = $redis->hmset($this->key_surprise_rests, $data);
			$redis->workDone($lockId);
			if ( !$res ) {
				return 0;// 写入失败时，此暴奖强制失效
			}
		}
		// 暴奖无周期时，暴奖周期不做任何变化，直接返回次数
		if ( !$data["periodTime_{$id}"] || !$data["periodStart_{$id}"] || !$data["periodEnd_{$id}"] ) {
			if ( $data["times_{$id}"] ) {
				return intval($data["rests_{$id}"]);// 有上限时，使用当前次数
			} else {
				return 1;// 无上限时，永远为1
			}
		}
		// 暴奖次数无限制时，周期设置没有意义，判定为无效数据，所以返回0
		if ( !$data["times_{$id}"] ) {
			return 0;
		}
		// 暴奖周期已过期时，加锁，重置暴奖周期，获取最新暴奖周期数据
		if ( $time > $data["periodEnd_{$id}"] ) {
			$lockId = __FUNCTION__."_{$id}";
			$redis = $this->getRedis();
			$res = $redis->workLock($lockId);
			if ( !$res ) {
				return 0;//加锁失败时，此暴奖强制失效
			}
			// 锁后重新获取一次
			$data = $redis->hmget($this->key_surprise_rests, array("periodTime_{$id}","periodId_{$id}","periodStart_{$id}","periodEnd_{$id}","times_{$id}","rests_{$id}"));
			$n = ceil(($time - $data["periodEnd_{$id}"]) / $data["periodTime_{$id}"]);
			if ( $n > 0 ) {
				$data["periodId_{$id}"]   += $n;
				$data["periodStart_{$id}"]+= intval($n * $data["periodTime_{$id}"]);
				$data["periodEnd_{$id}"]  += intval($n * $data["periodTime_{$id}"]);
				$data["times_{$id}"] = $surprise["times"];
				$data["rests_{$id}"] = $surprise["times"];
				$res = $redis->hmset($this->key_surprise_rests, $data);
				if ( !$res ) {
					$redis->workDone($lockId);
					return 0;// 写入失败时，此暴奖强制失效
				}
			}
			$redis->workDone($lockId);
		}
		// 暴奖时间未开始时，返回0
		if ( !($data["periodStart_{$id}"] <= $time && $time < $data["periodEnd_{$id}"]) ) {
			return 0;
		}
		return $data["rests_{$id}"];
	}

	// 扣除某暴奖次数1
	public function ddaSurpriseRests( $id, $surprise )
	{
		if ( $surprise['times'] ) {
			$redis = $this->getRedis();
			if ( $redis ) {
				$redis->hadd($this->key_surprise_rests, "total_{$id}");
				return $redis->hdda($this->key_surprise_rests, "rests_{$id}");
			}
			return 0;
		}
		return 1;
	}

	// 赋值计算器
	// o 			num 	原值
	// s 			str 	运算逻辑 + - * / = ...
	// p 			num 	运算参数
	// e 			num 	扩展参数
	// f 			num 	再扩参数 预留
	// is_reset 	bool 	0以累加1以赋值 方式输出结果
	// is_float 	bool 	0以整数1以小数 方式输出结果
	// is_minus 	bool 	0最小零1可为负 方式输出结果
	// return 		num 	新值
	private function compute( $o, $s='+', $p=1, $e=1, $f=1, $is_reset=0, $is_float=0, $is_minus=0 )
	{
		// $execut = array('key'=>'usertesk.teskvalue','exe'=>'keepdate','par'=>'param','ext'=>'usertesk.tesklast')
		// $execut = array('key'=>'usertesk.teskvalue','exe'=>'keep>','par'=>'param','ext'=>20)
		// 扩展参数连续模式keep???，当依据exe达成连续条件(exe为比较运算时,par与ext直接比较；exe为时间运算时，par与ext间接比较)时，key+1
		// keep=:连续等于 keep>=:连续大于等于 ... date:连续日期 week:连续星期 hour:连续小时 //month:连续月份
		if ( strpos($s, 'keep') === 0 ) {
			$s = str_replace('keep', '', $s);
			switch ( $s ) {
				case '=':
					$n = $p == $e ? ($o + 1) : 0;
					break;
				case '==':
					$n = $p == $e ? ($o + 1) : 0;
					break;
				case '!=':
					$n = $p != $e ? ($o + 1) : 0;
					break;
				case '>=':
					$n = $p >= $e ? ($o + 1) : 0;
					break;
				case '<=':
					$n = $p <= $e ? ($o + 1) : 0;
					break;
				case '>':
					$n = $p >  $e ? ($o + 1) : 0;
					break;
				case '<':
					$n = $p <  $e ? ($o + 1) : 0;
					break;
				case 'date':
					if ( !$e ) {
						$n = 1;
					} else {
						$p = strtotime(substr_replace(substr_replace($p, '-', 4, 0), '-', 7, 0));
						$e = strtotime(substr_replace(substr_replace($e, '-', 4, 0), '-', 7, 0));
						$n = ($p != $e) ? ($p == ($e + 86400 * 1) ? ($o + 1) : 1) : ($o + 0);
					}
					break;
				case 'week':
					if ( !$e ) {
						$n = 1;
					} else {
						$p = strtotime(substr_replace(substr_replace($p, '-', 4, 0), '-', 7, 0));
						$e = strtotime(substr_replace(substr_replace($e, '-', 4, 0), '-', 7, 0));
						$n = ($p != $e) ? ($p == ($e + 86400 * 7) ? ($o + 1) : 1) : ($o + 0);
					}
					break;
				case 'hour':
					if ( !$e ) {
						$n = 1;
					} else {
						$p = strtotime(substr_replace(substr_replace(substr_replace($p, '-', 4, 0), '-', 7, 0), ' ', 10, 0).':00:00');
						$e = strtotime(substr_replace(substr_replace(substr_replace($e, '-', 4, 0), '-', 7, 0), ' ', 10, 0).':00:00');
						$n = ($p != $e) ? ($p == ($e + 86400 /24) ? ($o + 1) : 1) : ($o + 0);
					}
					break;
				default://month
					$n = $o;//待续
					break;
			}
		} else {
			// $execut = array('key'=>'usertesk.teskvalue','exe'=>'+','par'=>'param','ext'=>1)
			switch ( $s )
			{
				case '=':
					return $p;
				case '-':
					$n = $o - $p + 0 ;
					break;
				case '*':
					$n = $o * $p + 0 ;
					break;
				case '/':
					$n = $p ? ($o / $p + 0) : 0 ;
					break;
				case '*p+e':
					$n = $o * $p + $e;
					break;
				case '*p+p':
					$n = $o * $p + $p;
					break;
				case '+p*e':
					$n = $o + $p * $e;
					break;
				default:// 默认为+
					$n = $o + $p + 0 ;
					break;
			}
		}
		if ( !$is_float ) $n = intval($n);
		if ( !$is_minus ) $n = max(0, $n);
		if ( !$is_reset ) $n = intval($n - $o);
		return $n;
	}

	// 条件比较器
	// l 			num 	左边参数
	// leg 			str 	比较方式 l e g < = >
	// r 			num 	右边参数
	// return 		mix 	结果true|false，无法比较时返回null
	private function compare( $l, $leg, $r )
	{
		$legs = array('l'=>1, '<'=>1, 'le'=>2, '<='=>2, 'g'=>3, '>'=>3, 'ge'=>4, '>='=>4, 'ne'=>5, '!='=>5, 'e'=>6, '='=>6, '=='=>6);
		if ( !isset($legs[$leg]) ) return null;
		switch ($legs[$leg])
		{
			case 1 : return ($l <  $r);
			case 2 : return ($l <= $r);
			case 3 : return ($l >  $r);
			case 4 : return ($l >= $r);
			case 5 : return ($l != $r);
			default: return ($l == $r);//默认为=
		}
	}

	// 用户任务信息输出
	public function getUserTeskInfo( $uid )
	{
		$redis = $this->getRedis();
		$usertesk = $redis->hgetall($this->key_user_tesk.$uid);
		$teskpart = array();
		if ( $usertesk && is_array($usertesk) && count($usertesk) < 2 ) {//目前代码适合2
			$teskpart = $usertesk;
			$usertesk = array();
		}
		if ( !$usertesk || !is_array($usertesk) ) {
			$mysql = $this->getMysql();
			$sql = "SELECT `teskCode` FROM `lord_user_tesk` WHERE `uid` = $uid";
			$usertesk = $mysql->getVar($sql);
			if ( $usertesk ) {
				$usertesk = json_decode($usertesk, 1);
				if ( $usertesk ) {
					if ( $teskpart ) $usertesk = array_merge($usertesk, $teskpart);
					$redis->hmset($this->key_user_tesk.$uid, $usertesk);
				}
			}
			if ( !$usertesk ) $usertesk = $teskpart;
		}
		return $usertesk;
	}

	// 用户任务列表输出
	public function getUserTeskAndList( $userinfo, $usertesk=array() )
	{
		if ( !$userinfo || !isset($userinfo['uid']) || !$userinfo['uid'] ) return array('usertesk'=>$usertesk,'tesklist'=>array());
		$uid = $userinfo['uid'];
		$time = time();
		$dateid = intval(date("Ymd"));
		$newutesk = array();
		if ( !$usertesk ) $usertesk = $this->getUserTeskInfo($uid);
		//每日救济数据刷新
		if ( !isset($usertesk['ttdateid']) || $usertesk['ttdateid'] != $dateid ) {
		    $newutesk['ttdateid'] = $usertesk['ttdateid'] = $dateid;
			$newutesk['tttimes'] = $usertesk['tttimes'] = 0;
			//每日奖券领取次数重置
			include(ROOT.'/include/data_tesk_id_list.php');
			if(!empty($data_tesk_id_list)){
			    foreach($data_tesk_id_list as $key=>$val){
			        $item = array();
			        if(!empty($data_tesk_id_list[$key])){
			            $task_target = $data_tesk_id_list[$key]['target'];
			            $teskid= $data_tesk_id_list[$key]['id'];
			            $newutesk["teskvalue_{$teskid}"] = $usertesk["teskvalue_{$teskid}"] = 0;//任务本周期重新赋值
			        }
			    }
			}
		}
		$list = $this->getTeskAll();
		// 基础校验
		foreach ( $list as $teskId => $tesk )
		{
			// 用户账号校验
			if ( $tesk['users'] && is_array($tesk['users']) && !in_array($uid, $tesk['users']) ) {
				unset($list[$teskId]); continue;
			}
			// 用户渠道校验
			if ( $tesk['channels'] && is_array($tesk['channels']) && !in_array($userinfo['channel'], $tesk['channels']) ) {
				unset($list[$teskId]); continue;
			}
			// 用户房间校验
			if ( $tesk['rooms'] && is_array($tesk['rooms']) && !in_array($userinfo['roomId'], $tesk['rooms']) ) {
				unset($list[$teskId]); continue;
			}
			// 用户领奖校验 处于可领奖状态的任务，不予继续
			if ( isset($usertesk["teskstate_{$teskId}"]) && $usertesk["teskstate_{$teskId}"] == 2 ) {
				continue;
			}
			// 任务周期校验 生成周期更新后的新任务数据
			if ( $tesk['periodTime'] && $tesk['periodStart'] && $tesk['periodEnd'] ) {
				$pid = $this->checkTeskPeriod($tesk, $time);
				if ( $pid === false ) {
					unset($list[$teskId]); continue;
				} else {
					$list[$teskId]['periodId'] = $tesk['periodId'] = $pid;
				}
			} else {
				$pid = $tesk['periodId'];
			}
			// 用户周期校验 并重置用户周期任务的参与情况
			if ( !isset($usertesk["teskpid_{$teskId}"]) || $pid != $usertesk["teskpid_{$teskId}"] ) {
				$newutesk["teskpid_{$teskId}"] = $usertesk["teskpid_{$teskId}"] = $pid;	//任务本周期id
				$newutesk["teskdone_{$teskId}"] = $usertesk["teskdone_{$teskId}"] = 0;	//任务本周期完成情况
				$newutesk["teskvalue_{$teskId}"] = $usertesk["teskvalue_{$teskId}"] = 0;//任务本周期重新赋值
				$newutesk["teskstate_{$teskId}"] = $usertesk["teskstate_{$teskId}"] = 0;//任务本周期重设状态
			}
		}
		// 前置校验
		foreach ( $list as $teskId => $tesk )
		{
			// 任务进度校验 处于已完成状态的任务，不校验前置
			if ( isset($usertesk["teskdone_{$teskId}"]) && $usertesk["teskdone_{$teskId}"] ) {
				continue;
			}
			// 用户领奖校验 处于已激活状态的任务，不校验前置
			if ( isset($usertesk["teskstate_{$teskId}"]) && $usertesk["teskstate_{$teskId}"] > 0 ) {
				continue;
			}
			// 用户前置校验
			if ( !isset($tesk['prev']) || !$tesk['prev'] || ($tesk['prev'] && isset($list[$tesk['prev']]) && isset($usertesk["teskdone_".$tesk['prev']]) && $usertesk["teskdone_".$tesk['prev']]) ) {
				$newutesk["teskstate_{$teskId}"] = $usertesk["teskstate_{$teskId}"] = 1;//任务本周期重设状态
			}
		}
		// 用户任务信息变更
		if ( $newutesk ) {
			$redis = $this->getRedis();
			$redis->hmset($this->key_user_tesk.$uid, $newutesk);
		}
		return array('usertesk'=>$usertesk,'tesklist'=>$list);
	}

	// 执行任务
	public function execute( $acttag, &$userinfo, &$usertesk, $param, &$table)
	{
		$time = time();
		$this->acttag = $acttag;
		$teskKeys = array('teskpid','teskvalue','teskdone','tesklast','tesktimes','teskstate');
		if ( !$acttag || !$userinfo || !isset($userinfo['uid']) || !$userinfo['uid'] ) return false;
		$uid = $userinfo['uid'];
		$fd = isset($userinfo['fd']) && $userinfo['fd'] ? $userinfo['fd'] : 0;
		$adduinfo = $addutesk = $newutesk = $teskPids = array();
		$res = $this->getUserTeskAndList($userinfo, $usertesk);
		$teskpart = $res['tesklist'];
		$usertesk = $res['usertesk'];
		$teskpart_ = $this->getTeskPart();
		foreach ( $teskpart as $teskId => $tesk )
		{
			// 任务区间校验
			if ( !isset($teskpart_[$teskId]) ) {
				unset($teskpart[$teskId]); continue;
			}
			// 前置任务校验
			if ( isset($tesk['prev']) && $tesk['prev'] && !(isset($usertesk["teskdone_".$tesk['prev']]) && $usertesk["teskdone_".$tesk['prev']]) ) {
				unset($teskpart[$teskId]); continue;
			}
			// 用户领奖校验 处于可领奖状态的任务，不予继续
			if ( isset($usertesk["teskstate_{$teskId}"]) && $usertesk["teskstate_{$teskId}"] == 2 ) {
				unset($teskpart[$teskId]); continue;
			}
			// 任务进度校验 处于已完成状态的任务，不予继续
			if ( isset($usertesk["teskdone_{$teskId}"]) && $usertesk["teskdone_{$teskId}"] > 0 ) {
				unset($teskpart[$teskId]); continue;
			}
			$teskPids[$teskId] = $tesk['periodId'];
			// 执行任务
			$executs = $tesk['execut'] && is_array($tesk['execut']) ? $tesk['execut'] : array();
			foreach ( $executs as $execut )
			{	// $executs = array(array('key'=>'usertask.gold_all','exe'=>'+','par'=>'param'),...)	//参数数值运算模式，直接把运算结果加减到$key，
				// $execut = array('key'=>'usertesk.teskvalue','exe'=>'*p+e','par'=>'param','ext'=>1)	//扩展参数运算模式，直接把运算结果加减到$key。也适合0|1型的连续模式，比如输赢($param=[0|1])时，累加$ext到连续赢的次数
				// $execut = array('key'=>'usertesk.teskvalue','exe'=>'keepdateid','par'=>'param','ext'=>'usertesk.tesklast')//扩展参数连续模式keep???，当依据exe达成连续条件(exe为比较运算时,par与ext直接比较；exe为时间运算时，par与ext间接比较)时，key+1：
				// keep=:连续等于 keep>=:连续大于等于 ... dateid:连续日期 weekid:连续星期 monthid:连续月份 hourid:连续小时
				// 运算逻辑规整
				if ( !isset($execut['exe']) ) { $execut['exe'] = '+'; }	//默认为+
				$is_reset = intval($execut['exe'] == '=');
				// 用户字段规整
				$_k = explode('.', $execut['key']);
				$v = $_k[0];
				$k = $_k[1];
				// 任务项值运算
				if ( $v=='usertesk' && in_array($k, $teskKeys) ) {
					$k.= "_{$teskId}";
				}
				if ( !isset(${$v}[$k]) ) {
					$v = 'usertesk';//默认为usertesk
					if ($is_reset) {
						$newutesk[$k] = ${$v}[$k] = '';//默认为空
					} else {
						$addutesk[$k] = ${$v}[$k] = 0;//默认归零
					}
				}
				$execut['key'] = ${$v}[$k];
				// 运算参数规整
				if ( !isset($execut['par']) ) {
					$execut['par'] = 1;//默认为1
				} elseif ( is_numeric($execut['par']) ) {
					$execut['par'] = $execut['par'] + 0;
				} elseif ( $execut['par'] === 'param' ) {
					$execut['par'] = ${$execut['par']};//使用传参
				} else {
					$_v = explode('.', $execut['par']);
					$vv = $_v[0];
					$kk = $_v[1];
					// 任务项值运算
					if ( $vv=='usertesk' && in_array($kk, $teskKeys) ) {
						$kk.= "_{$teskId}";
					}
					if ( !isset(${$vv}[$kk]) ) {
						$vv = 'usertesk';//默认为usertesk
						if ($is_reset) {
							$newutesk[$kk] = ${$vv}[$kk] = '';//默认为空
						} else {
							$addutesk[$kk] = ${$vv}[$kk] = 0;//默认归零
						}
					}
					$execut['par'] = ${$vv}[$kk];
				}
				// 扩展参数规整
				if ( !isset($execut['ext']) ) {
					$execut['ext'] = 1;//默认为1
				} elseif ( is_numeric($execut['ext']) ) {
					$execut['ext'] = $execut['ext'] + 0;
				} else {
					$_v = explode('.', $execut['ext']);
					$vvv = $_v[0];
					$kkk = $_v[1];
					// 任务项值运算
					if ( $vvv=='usertesk' && in_array($kkk, $teskKeys) ) {
						$kkk.= "_{$teskId}";
					}
					if ( $vvv=='usertesk' && $kkk=="tesklast_{$teskId}") {
						$newutesk[$kkk] = $param;//把新参数赋值给tesklast，用于下次的连续性判断
					}
					if ( !isset(${$vvv}[$kkk]) ) {
						$vvv = 'usertesk';//默认为usertesk
						if ($is_reset) {
							$newutesk[$kkk] = ${$vvv}[$kkk] = '';//默认为空
						} else {
							$addutesk[$kkk] = ${$vvv}[$kkk] = 0;//默认归零
						}
					}
					$execut['ext'] = ${$vvv}[$kkk];
				}
				// 执行运算逻辑 返回基于原数值的累加数值
				if ($is_reset) {
					$new = $this->compute($execut['key'], $execut['exe'], $execut['par']);
					$v_ = str_replace('user', '', $v);
					${'newu'.$v_}[$k] = $new;
					${$v}[$k] = $new;
				} else {
					$add = $this->compute($execut['key'], $execut['exe'], $execut['par'], $execut['ext']);//, $execut['add']);
					$v_ = str_replace('user', '', $v);
					${'addu'.$v_}[$k] = (isset(${'addu'.$v_}[$k]) ? ${'addu'.$v_}[$k] : 0) + $add;
					${$v}[$k] += $add;
				}
			}
		}
		// 验收阶段
		$surprise_teskIds = $mail = array();
		foreach ( $teskpart as $teskId => $tesk )
		{
			// 执行验收
			$is_done = 0;
			$condits = $tesk['condit'] && is_array($tesk['condit']) ? $tesk['condit'] : array();
			foreach ( $condits as $condit )
			{	// $condits = array(array('key'=>'usertask.gold_all','leg'=>'ge','par'=>10),...)
				// $condit = array('key'=>'usertesk.gold_day','leg'=>'<','par'=>'usertesk.cost_day')//这个例子意味着此用户今天充的少花的多
				// 比较逻辑规整
				if ( !isset($condit['leg']) ) { $condit['leg'] = '='; }	//默认为=
				// 用户字段规整
				$_k = explode('.', $condit['key']);
				$v = $_k[0];
				$k = $_k[1];
				// 任务项值运算
				if ( $v=='usertesk' && in_array($k, $teskKeys) ) {
					$k.= "_{$teskId}";
				}
				if ( !isset(${$v}[$k]) ) {
					$v = 'usertesk';//默认为usertesk
					${$v}[$k] = 0;//默认归零
				}
				$condit['key'] = ${$v}[$k];
				// 比较参数规整
				if ( !isset($condit['par']) ) {
					$condit['par'] = 1;//默认为1
				} elseif ( is_numeric($condit['par']) ) {
					$condit['par'] = $condit['par'] + 0;
				} elseif ( $condit['par'] === 'param' ) {
					$condit['par'] = ${$condit['par']};//使用传参
				} else {//待扩展至对于当前时间time等的判断
					$_v = explode('.', $condit['par']);
					$vv = $_v[0];
					$kk = $_v[1];
					// 任务项值运算
					if ( $vv=='usertesk' && in_array($kk, $teskKeys) ) {
						$kk.= "_{$teskId}";
					}
					if ( !isset(${$vv}[$kk]) ) {
						$vv = 'usertesk';//默认为usertesk
						$condit['par'] = ${$vv}[$kk] = 0;//默认归零
					} elseif ( $vv == 'userinfo' && $kk == 'tableId' ) {
						$condit['par'] = ${$vv}[$kk] . '_' . (isset($table['gameStart'])?$table['gameStart']:0);
					} else {
						$condit['par'] = ${$vv}[$kk];
					}
				}
				$res = $this->compare($condit['key'], $condit['leg'], $condit['par']);//有null因素
				if ( !$res ) {
					$is_done = 0;
					break;
				}
				$is_done = 1;
			}
			// 任务达成
			if ( !$is_done ) {
				continue;
			}
			// 处理达成
			$n_gold = $n_coins = $n_coupon = $n_lottery = 1;
			$results = $tesk['result'] && is_array($tesk['result']) ? $tesk['result'] : array();
			foreach ( $results as $result )
			{	// $results = array(array('key'=>'usertesk.teskdone','exe'=>'=','par'=>0),...)//这个例子意味着任务在本周期内可重复完成
				// $result = array('key'=>'usertesk.teskpid','exe'=>'+','par'=>2)//这个例子意味着任务在下下个周期才可以再次参与
				// $result = array('key'=>'usertesk.teskvalue','exe'=>'+p*e','par'=>-1,'ext'=>1000)//这个例子意味着任务在本周期内完成后的下一次的完成值在当前值的基础上降低1000点
				// $result = array('key'=>'usertesk.tesktimes')//这个例子意味着任务在本周期内完成后，任务总完成次数+1
				// $result = array('key'=>'userinfo.times')//这个例子意味着任务在本周期内完成后，userinfo的times值(如果有的话)+1，在这里不用管这个值变化导致的影响
				// 运算逻辑规整
				if ( !isset($result['exe']) ) { $result['exe'] = '+'; }	//默认为+
				$is_reset = intval($result['exe'] == '=');
				// 用户字段规整
				$_k = explode('.', $result['key']);
				$v = $_k[0];
				$k = $_k[1];
				// 系数调整运算
				if ( $v == 'prizes' ) {
					if ( in_array($k, array('n_gold', 'n_coins', 'n_coupon', 'n_lottery')) ) {
						if (!isset($result['exe'])) $result['exe'] = '+';
						if (!isset($result['par'])) $result['par'] = 1;
						if ($result['par'] === 'param') $result['par'] = ${$result['par']};//使用传参
						$$k += $this->compute($$k, $result['exe'], $result['par']);
					}
					continue;
				}
				// 任务项值运算
				if ( $v=='usertesk' && in_array($k, $teskKeys) ) {
					$k.= "_{$teskId}";
				}
				if ( !isset(${$v}[$k]) ) {
					$v = 'usertesk';//默认为usertesk
					if ($is_reset) {
						$newutesk[$k] = ${$v}[$k] = '';//默认为空
					} else {
						$addutesk[$k] = ${$v}[$k] = 0;//默认归零
					}
				}
				$result['key'] = ${$v}[$k];
				// 运算参数规整
				if ( !isset($result['par']) ) {
					$result['par'] = 1;//默认为1
				} elseif ( is_numeric($result['par']) ) {
					$result['par'] = $result['par'] + 0;
				} elseif ( $result['par'] === 'param' ) {
					$result['par'] = ${$result['par']};//使用传参
				} else {
					$_v = explode('.', $result['par']);
					$vv = $_v[0];
					$kk = $_v[1];
					// 任务项值运算
					if ( $vv=='usertesk' && in_array($kk, $teskKeys) ) {
						$kk.= "_{$teskId}";
					}
					if ( !isset(${$vv}[$kk]) ) {
						$vv = 'usertesk';//默认为usertesk
						if ($is_reset) {
							$newutesk[$kk] = ${$vv}[$kk] = '';//默认为空
						} else {
							$addutesk[$kk] = ${$vv}[$kk] = 0;//默认归零
						}
						$result['par'] = ${$vv}[$kk];
					} elseif ( $vv == 'userinfo' && $kk == 'tableId' ) {
						$result['par'] = ${$vv}[$kk] . '_' . (isset($table['gameStart'])?$table['gameStart']:0);
					} else {
						$result['par'] = ${$vv}[$kk];
					}
				}
				// 扩展参数规整
				if ( !isset($result['ext']) ) {
					$result['ext'] = 1;//默认为1
				} elseif ( is_numeric($result['ext']) ) {
					$result['ext'] = $result['ext'] + 0;
				} else {
					$_v = explode('.', $result['ext']);
					$vvv = $_v[0];
					$kkk = $_v[1];
					// 任务项值运算
					if ( $vvv=='usertesk' && in_array($kkk, $teskKeys) ) {
						$kkk.= "_{$teskId}";
					}
					if ( !isset(${$vvv}[$kkk]) ) {
						$vvv = 'usertesk';//默认为usertesk
						if ($is_reset) {
							$newutesk[$kkk] = ${$vvv}[$kkk] = '';//默认为空
						} else {
							$addutesk[$kkk] = ${$vvv}[$kkk] = 0;//默认归零
						}
						$result['ext'] = ${$vvv}[$kkk];
					} elseif ( $vvv == 'userinfo' && $kkk == 'tableId' ) {
						$result['ext'] = ${$vvv}[$kkk] . '_' . (isset($table['gameStart'])?$table['gameStart']:0);
					} else {
						$result['ext'] = ${$vvv}[$kkk];
					}
				}
				// 执行运算逻辑 返回基于原数值的累加数值
				if ($is_reset) {
					$new = $this->compute($result['key'], $result['exe'], $result['par']);
					$v_ = str_replace('user', '', $v);
					${'newu'.$v_}[$k] = $new;
					${$v}[$k] = $new;
				} else {
					$add = $this->compute($result['key'], $result['exe'], $result['par'], $result['ext']);//, $execut['add']);
					$v_ = str_replace('user', '', $v);
					${'addu'.$v_}[$k] = (isset(${'addu'.$v_}[$k]) ? ${'addu'.$v_}[$k] : 0) + $add;
					${$v}[$k] += $add;
				}
			}
			// 处理领奖方式 类外处理
            if ( isset($newutesk['teskstate_'.$teskId]) && $newutesk['teskstate_'.$teskId] == 3 ) {
				if ( $tmp = $this->getPrizes($tesk, $n_gold, $n_coins, $n_coupon, $n_lottery) ) {
					if ( $adduinfo ) {
						foreach ( $adduinfo as $ukk => $uvv )
						{
							if ( isset($tmp[$ukk]) ) {
								$adduinfo[$ukk] += $tmp[$ukk];
								unset($tmp[$ukk]);
							}
						}
						if ( $tmp ) $adduinfo = array_merge($adduinfo, $tmp);
					} else {
						$adduinfo = $tmp;
					}
					$adduinfo['teskdones'][] = $teskId;
				}
            }
			// 处理领奖方式 任务面板
			elseif ( !$tesk['mailSubject'] )
			{

				$newutesk['teskstate_'.$teskId] = $usertesk['teskstate_'.$teskId] = 2;
				// 通知 达成任务量增加
				if ( $fd ) {
					$redis = $this->getRedis();
					$user = $userinfo;
					$task_unaward_key = "task".($tesk['type']+1)."_unaward";
					$task_unaward = $redis->hincrby($this->key_user_info.$uid, $task_unaward_key, 1);
					$cmd = 4; $code = 110;
					$send = array($task_unaward_key=>$task_unaward);
					$res = sendToFd($fd, $cmd, $code, $send);
				}
			}
			// 处理领奖方式 邮箱面板
			else
			{
				$mailSubject = sprintf($tesk['mailSubject'], $tesk['name']);
				$mailContent = $tesk['mailContent'];
                $mailItems = $this->getPrizes($tesk, $n_gold, $n_coins, $n_coupon, $n_lottery);
				// 处理发送邮件
				$mail['type'] = 0;
				$mail['fromUid'] = $teskId;//10000<teskId<20000
				$mail['uid'] = $userinfo['uid'];
				$mail['subject'] = $mailSubject;
				$mail['content'] = $mailContent;
				$mail['items'] = $mailItems;
				$mail['fileid'] = intval($tesk['mailFileid']);
				$mail['sort'] = 1;
				$mail['create_time'] = $time;
				$mail['update_time'] = $time;
				$this->sendMail($mail);
				// 通知 某任务已经达成
				$done = $mailSubject."\n".$tesk['prizeName'];
				$this->sendDone($userinfo, $done);
			}
			// 识别暴奖开关
			if ( $tesk['is_surprise'] ) {
				$surprise_teskIds[] = $teskId;
			}
		}// 验收运算 达成运算 领奖方式运算 奖励邮件运算 处理完毕
		// 处理暴奖
		$surprises = array();
		if ( $surprise_teskIds && !( ( isset($userinfo['lastSurprise']) && ($time - $userinfo['lastSurprise']) < 20 ) ||
			( isset($table['lastSurprise']) && ($time - $table['lastSurprise']) < 20 ) ) ) {
			$specials = array();//专属活动暴奖
			$surprises = $this->getSurprise();
			foreach ( $surprises as $k => $v )
			{
				//没邮件、没机会 不暴奖
				if ( !$v['mailSubject'] || !$v['chance'] ) {
					unset($surprises[$k]);
					continue;
				}
				//有专属 组专属
				if ( isset($v['teskids']) && $v['teskids'] && is_array($v['teskids']) ) {
					$is_in = 0;
					foreach ( $surprise_teskIds as $teskId )
					{
						if ( in_array($teskId, $v['teskids']) ) {
							$specials[$k] = $v;
							$specials[$k]['teskids_'][] = $teskId;
							$is_in = 1;
						}
					}
					if ( !$is_in ) {
						$surprises[$k]['teskids_'] = $surprise_teskIds;
					}
				} else {
					$surprises[$k]['teskids_'] = $surprise_teskIds;
				}
			}
			//有专属用专属 / 无专属用普通
			if ( $specials ) {
				$surprises = $specials;
			}
			$allchance = 0;
			foreach ( $surprises as $k => $v )
			{
				$allchance+=$v['chance'];
			}
			$needle = mt_rand(1, 10000);//=100%
			if ( $needle > $allchance ) {
				$surprises = array();
			}
		}
		if ( $surprises ) {
			$step = 0;
			foreach ( $surprises as $k => $v )
			{
				$v['chance'] += $step;
				if ( $needle > $step && $needle <= $v['chance'] ) {
					$rests = $this->getSurpriseRests($v['id'], $v);
					if ( $rests > 0 ) {
						$this->ddaSurpriseRests($v['id'], $v);
						$is_grab = intval($v['is_grab'] && $userinfo['tableId'] && $table);//可抢+牌桌
						$teskId = $v['teskids_'][array_rand($v['teskids_'])];
						$surprise = $v;
						$surprise['dateid'] = intval(date("Ymd"));
						$surprise['teskId'] = $teskId;
						$surprise['periodId'] = $teskPids[$teskId];
						$surprise['surpriseId'] = $v['id'];
						$surprise['uid'] = $is_grab ? 0 : $userinfo['uid'];
						$surprise['teskUid'] = $userinfo['uid'];
						$surprise['rests'] = $rests;
						$surprise['tableId'] = $userinfo['tableId'];
						$surprise['create_time'] = $time;
						$surprise['update_time'] = $is_grab ? 0 : $time;
						$♨️️ = $this->tesksprise($surprise, $is_grab, $table, $userinfo);
						if ( isset($table['tableId']) && $table['tableId'] ) {
							$table['lastSurprise'] = time();
						}
					}
					break;
				}
				$step = $v['chance'];
			}
		}//暴奖处理完毕
		//预留 处理用户任务完成记录表数据
		// 处理用户任务信息，返回adduinfo
		if ( $addutesk ) {
			foreach ( $addutesk as $k => $v )
			{
				if ( !isset($newutesk[$k]) ) $newutesk[$k] = $usertesk[$k];
			}
		}
		if ( $newutesk ) {
			$redis = $this->getRedis();
			$redis->hmset($this->key_user_tesk.$uid, $newutesk);
			if ( !$fd ) {
				$usertesk = array_merge($usertesk, $newutesk);
				$sql = "REPLACE INTO `lord_user_tesk` ( `uid`, `teskCode`, `update_time` ) VALUES ( $uid, '".addslashes(json_encode($usertesk))."', ".time()." )";
				$res = bobSql($sql);
			}
		}
		return $adduinfo;
	}
    private function getPrizes( $tesk, $n_gold, $n_coins, $n_coupon, $n_lottery )
	{
        $mailItems = array('gold'=>0,'coins'=>0,'coupon'=>0,'lottery'=>0,'props'=>array(),'other'=>array());
        $prizes = $tesk['prizes'] && is_array($tesk['prizes']) ? $tesk['prizes'] : array();
        // $prizes = array('coins'=>1000,'props'=>array(array('id'=>4,'name'=>'富...','categoryId'=>1,'num'=>1)),...)
        // $prizes = array('key'=>'coins','exe'=>'+p*e','par'=>'param','ext'=>2,'other'=>array('key'=>'tel_charge','val'=>10,'name'=>'10元话费'),'props'=>array(array('id'=>4,'name'=>'富...','categoryId'=>1,'num'=>1,'ext'=>30)))//这个例子是浮动奖励+固定奖励
        // 计算浮动奖励
        // 目前暂不支持在管理后台使用浮动奖励编辑方式，而只使用固定奖励+达成运算时的参数干扰方式。待续
        if ( isset($prizes['key']) ) {
            // 用户字段规整
            if ( !in_array($prizes['key'], array('gold','coins','coupon','lottery')) ) {
                return ;
            }
            // 运算逻辑规整
            if ( !isset($prizes['exe']) ) {
                $prizes['exe'] = '+';//默认为+
            }
            // 运算参数规整
            if ( !isset($prizes['par']) ) {
                $prizes['par'] = 1;//默认为1
            } elseif ( is_numeric($prizes['par']) ) {
                $prizes['par'] = $prizes['par'] + 0;
            } elseif ( $prizes['par'] === 'param' ) {
                $prizes['par'] = ${$prizes['par']};//使用传参
            } else {
                $_v = explode('.', $prizes['par']);
                $v = $_v[0];
                $k = $_v[1];
                if ( !isset(${$v}[$k]) ) {
                    $prizes['par'] = 1;//默认为1
                } else {
                    $prizes['par'] = ${$v}[$k];
                }
            }
            // 扩展参数规整
            if ( !isset($prizes['ext']) ) {
                $prizes['ext'] = 1;//默认为1
            } elseif ( is_numeric($prizes['ext']) ) {
                $prizes['ext'] = $prizes['ext'] + 0;
            } else {
                $_v = explode('.', $prizes['ext']);
                $v = $_v[0];
                $k = $_v[1];
                if ( !isset(${$v}[$k]) ) {
                    $prizes['ext'] = 1;//默认为1
                } else {
                    $prizes['ext'] = ${$v}[$k];
                }
            }
            // 执行运算逻辑 返回基于原数值的累加数值
            $add = $this->compute($prizes['key'], $prizes['exe'], $prizes['par'], $prizes['ext']);
            $mailItems[$prizes['key']] += $add;
        }
        // 累加固定奖励
        if ( isset($prizes['gold']) ) $mailItems['gold'] += $prizes['gold'] * $n_gold;//使用result运算时的固定奖励干扰方式。待续
        if ( isset($prizes['coins']) ) $mailItems['coins'] += $prizes['coins'] * $n_coins;
        if ( isset($prizes['coupon']) ) $mailItems['coupon'] += $prizes['coupon'] * $n_coupon;
        if ( isset($prizes['lottery']) ) $mailItems['lottery'] += $prizes['lottery'] * $n_lottery;
        if ( isset($prizes['props']) && is_array($prizes['props']) && $prizes['props'] ) $mailItems['props'] = array_merge($mailItems['props'], $prizes['props']);
        if ( isset($prizes['propItems']) && is_array($prizes['propItems']) && $prizes['propItems'] ) $mailItems['props'] = array_merge($mailItems['props'], $prizes['propItems']);
        if ( isset($prizes['other']) && is_array($prizes['other']) && $prizes['other'] ) $mailItems['other'] = array_merge($mailItems['other'], $prizes['other']);
        if ( !$mailItems['gold'] ) unset($mailItems['gold']);
        if ( !$mailItems['coins'] ) unset($mailItems['coins']);
        if ( !$mailItems['coupon'] ) unset($mailItems['coupon']);
        if ( !$mailItems['lottery'] ) unset($mailItems['lottery']);
        foreach ( $mailItems['props'] as $k => $v )
        {
            if ( isset($v['ext']) && $v['ext'] > 0 && $v['cd'] == 1 ) {
                $mailItems['props'][$k]['name'] = $v['name']."(".$v['ext']."天)";
            } else {
                $mailItems['props'][$k]['name'] = $v['name'];
                $mailItems['props'][$k]['ext'] = $v['ext'];
            }
        }
        if ( !$mailItems['props'] ) unset($mailItems['props']);
        foreach ( $mailItems['other'] as $k => $v )
        {
            $mailItems['other'][$k] = $v['name'];
        }
        $mailItems['other'] = $mailItems['other'] ? join("\n", $mailItems['other']) : '';
        if ( !$mailItems['other'] ) unset($mailItems['other']);
        return $mailItems;
    }
	// 处理暴奖记录，写入数据库，如果可抢写入redis，如果不可抢且有奖品，发邮件
	public function tesksprise( $d, $is_grab, $table, $userinfo )
	{
		// 写入数据库
		$sql = "INSERT INTO `".$this->mysql_user_tesksurprise."` (`dateid`,`teskId`,`periodId`,`surpriseId`,`uid`,`teskUid`,`rests`,";
		$sql.= "`tableId`,`gold`,`coins`,`coupon`,`lottery`,`propId`,`props`,`other`,`is_grab`,`create_time`,`update_time`) VALUES (";
		$sql.= $d['dateid'].",".$d['teskId'].",".$d['periodId'].",".$d['surpriseId'].",".$d['uid'].",".$d['teskUid'].",".$d['rests'].",'".$d['tableId']."',";
		$mailSubject = $d['mailSubject'] ? sprintf($d['mailSubject'], $is_grab?'抢到':'获得') : 0;
		$mailContent = $d['mailContent'];
		$mailItems = array('gold'=>0,'coins'=>0,'coupon'=>0,'lottery'=>0,'props'=>array(),'other'=>'');
		$d_ = array_chunk($mailItems, 4, 1);
		$d_ = reset($d_);
		$d = array_merge($d, $d_);
		if ( isset($mailItems[$d['keyName']]) ) {
			$d[$d['keyName']] = $mailItems[$d['keyName']] = $d_[$d['keyName']] = $d['keyVal']+0;
			$d['propId'] = 0;
			$d['props'] = $d['other'] = '';
			$sql.= join(',',$d_).",0,'','',";
		} elseif ( $d['keyName']=='propId' ) {
			$d[$d['keyName']] = intval($d['keyVal']);
			$sql.= "0,0,0,0,".$d['keyVal'].",";
			$m_ = array('id'=>intval($d['keyVal']),'name'=>$d['name'],'categoryId'=>1,'num'=>1,'ext'=>$d['keyExt']+0);
			$d['props'] = $mailItems['props'] = array($m_);// name=富翁套装(7天)
			$d['other'] = '';
			$sql.= "'".($d['props']?addslashes(json_encode($d['props'])):'')."','',";
		} else {
			$d['propId'] = 0;
			$d['props'] = '';
			$d['other'] = $mailItems['other'] = $d['name'];// name=有乐公仔1个
			$sql.= "0,0,0,0,0,'','".$d['other']."',";
		}
		$sql.= $is_grab.",".$d['create_time'].",".$d['update_time'].")";
		$mysql = $this->getMysql();
		$mysql->runSql($sql);
		$id = $mysql->lastId();
		$redis = $this->getRedis();
		// 修改个人上次暴奖时间
		if ( $d['uid'] ) {
			$res = $redis->hset($this->key_user_info.$d['uid'], 'lastSurprise', time());
		}
		// 修改牌桌上次暴奖时间(如果有的话)
		if ( isset($table['tableId']) && $table['tableId'] ) {
			$res = $redis->hset('lord_table_info_'.$table['tableId'], 'lastSurprise', time());
		}
		// 组装邮件
		if ( !$mailItems['gold'] ) unset($mailItems['gold']);
		if ( !$mailItems['coins'] ) unset($mailItems['coins']);
		if ( !$mailItems['coupon'] ) unset($mailItems['coupon']);
		if ( !$mailItems['lottery'] ) unset($mailItems['lottery']);
		if ( !$mailItems['props'] ) unset($mailItems['props']);
		if ( !$mailItems['other'] ) unset($mailItems['other']);
		$mail['type'] = 0;
		$mail['fromUid'] = $d['teskId'];//10000<teskId<20000
		$mail['uid'] = $d['uid'];//不一定>0
		$mail['subject'] = $mailSubject;
		$mail['content'] = $mailContent;
		$mail['items'] = $mailItems;
		$mail['fileid'] = intval($d['mailFileid']);
		$mail['sort'] = 1;
		$mail['create_time'] = $d['create_time'];
		$mail['update_time'] = $d['create_time'];
		$mailId = $this->sendMail($mail);
		// 通知争抢
		if ( $is_grab ) {
			// 首入队列
			$d['mailId'] = $mailId;
			$d['tesksurpriseId'] = $id;
			$d['nicks'] = array($table['seat0uid']=>$table['seat0info']['nick'], $table['seat1uid']=>$table['seat1info']['nick'], $table['seat2uid']=>$table['seat2info']['nick']);
			$d['success'] = array(
				'errno' => 0,
				'error' => "恭喜玩家·%s·抢到礼包\n".$d['name'],
				'type' => 'grab',
				'fileids' => array($d['fileid']),
			);
			$d['failed'] = array(
				'errno' => 0,
				'error' => "很遗憾，礼包被·%s·抢走了\n还有很多机会，争取下次抢到哦",
				'type' => "failed",
			);
			$d['table'] = $table;
			$redis->ladd($this->key_queue_tsgrab_.$id, $d);
			//事件 - 争抢结果
			$sceneId = 'GRAB_GIFT_'.$table['tableId'];
			$act = 'TASK_GRAB_SURPRISE';
			$params = array('id'=>$id);
			$delay = $this->grab_time * 1000;
			setTimer($sceneId, $act, $params, $delay);
			// //事件 - 争抢结果
			// $act = 'TASK_GRAB_SURPRISE';
			// $params = array('id'=>$id);
			// $delay = $this->grab_time * 1000;
			// setEvent($act, $params, $delay);
			// 通知哄抢
			$this->sendGrab($table, $id);
		} else {
			// 通知定向
			$this->sendTome($userinfo, $d);
		}
		return $id;
	}
	// 处理邮件发送或暂存
	private function sendMail( $m )
	{
		$mysql = $this->getMysql();
		$sql = "INSERT INTO `".$this->mysql_user_inbox."` (`type`,`fromuid`,`uid`,`subject`,`content`,`items`,`fileid`,`sort`,`create_time`,`update_time`) ";
		$sql.= "VALUES (".$m['type'].",".$m['fromUid'].",".$m['uid'].",'".$m['subject']."','".$m['content']."','".($m['items']?addslashes(json_encode($m['items'])):'')."',".$m['fileid'].",".$m['sort'].",".$m['create_time'].",".$m['update_time'].")";
		$res = $mysql->runSql($sql);
		if ( $res ) {
			$id = $mysql->lastId();
			$redis = $this->getRedis();
			if ( $m['uid'] ) { // 直接通知
				$queue = array(
					'type' => 'mail',
					'uid' => intval($m['uid']),
					'id' => $id,
			 		'subject' => $m['subject'],
			 		'content' => $m['content'],
			 		'items' => intval(!!$m['items']),
			 		'fileid' => $m['fileid'],
			 		'is_read' => 0,
			 		'sort' => 1,
				);
				$redis->ladd($this->key_queue_push, $queue);
			} else { // 暂入redis，接收到争抢时使用items，并修正数据库的里的uid，然后发邮件通知，并销毁本数据
				$redis->hset($this->key_user_grabmail, $id, $m);
			}
			return $id;
		}
		return 0;
	}

	// 处理发送定向礼包
	private function sendDone( $userinfo, $done )
	{
		$fd = $userinfo['fd'];
		$cmd = 6;//使用道具协议
		$code = 102;//+mt_rand(0,1)*2;//有个任务奖励
		$data['errno'] = 0;
		$data['error'] = $done;
		$data['type'] = 'mail';
		sendToFd($fd, $cmd, $code, $data);
	}

	// 处理发送定向礼包
	private function sendTome( $userinfo, $d )
	{
		$fd = $userinfo['fd'];
		$cmd = 6;//使用道具协议
		$code = 106;//有个定向礼包
		$data['errno'] = 0;
		$data['error'] = "恭喜您获得一个额外的定向礼包\n".$d['name'];
		$data['type'] = 'mail';
		$data['fileids'] = array($d['fileid']);
		sendToFd($fd, $cmd, $code, $data);
	}

	// 处理发送哄抢礼包
	private function sendGrab( $table, $id )
	{
		$cmd = 6;//使用道具协议
		$code = 106;//有个哄抢礼包
		$data['errno'] = 0;
		$data['error'] = "这是一个额外的哄抢礼包，礼包里有惊喜\n请赶快按确定键争抢";
		$data['type'] = 'grab';
		$data['id'] = $id;
		$this->sendToTable($table, $cmd, $code, $data);
	}

	// 发送到牌桌玩家
	public function sendToTable( $table, $cmd, $code, $data )
	{
		$tableId = $table['tableId'];
		foreach ( $table['seats'] as $uid=>$seatId )
		{
			$fd = $table["seat{$seatId}fd"];
			if ( $fd ) {
				$data['log']['ud'] = $uid;
				$data['log']['td'] = $tableId;
				$res = sendToFd($fd, $cmd, $code, $data);
			}
		}
	}

}
