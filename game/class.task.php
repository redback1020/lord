<?php
/**
* PHP task class
*/
class task
{
	public $errno = 0;
	public $error = '';
	public $model = null;
	public $mysql = null;
	public $redis = null;
	public $local = null;
	public $mysql_table_gametask = 'lord_game_task';
	public $mysql_table_usertaskrecord = 'lord_user_taskrecord';
	public $mysql_table_usertask = 'lord_user_task';
	public $mysql_table_userinfo = 'lord_game_user';
	public $redis_key_gametask = 'lord_game_task';
	public $redis_key_usertaskrecord = 'lord_user_taskrecord';
	public $redis_key_usertask = 'lord_user_task';
	public $redis_key_userinfo = KEY_USER_;
	public $redis_key_mysql_table_pre = 'lord_mysql_';
	public $gametask = array();
	public $uid = 0;
	public $userinfo = array();
	public $usertask = array();
	public $tasklist = array();

	// 1、所有任务都是从代码产生的 "include/data_task_***.php" 或 "include/data_task_all.php"
	// 2、filecode  ->  if_noINredis  ->  if_noINmysql  ->  createTOmysql  ->  createTOredis  ->  checkdone  ->  returnTask
	//								  ->  if_beINmysql  ->  updateTOmysql  ->  createTOredis  ->  checkdone  ->  returnTask
	//					if_beINredis  ->  code.is_update==true  ->  diffTOredis  ->  is_diff==true  ->  upateTOmysql  ->  ...
	//									  code.is_update==false  ->checkdone  ->  returnTask

	public function __construct( $model, $taskids=array(), $cmd=0, $code=0, $is_check=0 )
	{
		$this->redis_key_userinfo = trim($this->redis_key_userinfo, '_');
		if ( $model ) $this->model = $model;
		if ( $model && $model->mysql ) $this->mysql = $model->mysql;
		if ( $model && $model->redis ) $this->redis = $model->redis;
		if ( !$this->mysql ) $this->mysql = new DB;
		if ( !$this->redis ) $this->redis = new RD;
		// 创建任务
		$tasklist = array();
		if ( $taskids && ( is_array($taskids) || ( is_int($taskids) && $taskids > 0 ) ) )
		{	// 依据提供的一个或多个taskid创建任务列表
			$taskids = is_int($taskids) ? array($taskids) : $taskids;
			foreach ( $taskids as $k => $taskid )
			{
				$task = "data_task_".$taskid;
				$$task = array();
				require(ROOT."/include/".$task.".php");
				$task = $$task;
				if ( !$task ) {
					gerr("任务文件无效 task-".__LINE__." file=data_task_".$taskid);
					return false;
				}
				if ( $is_check ) {
					$old = $task;
					$task = $this->check($task);
					if ( !$task ) {
						gerr("任务检测失败 task-".__LINE__." task=".json_encode($old));
						return false;
					}
				}
				$tasklist[$task['id']] = $task;
			}
		}
		else
		{	// 自动载入所有文件任务
			$task = "data_task_"."all";
			$$task = array();
			require(ROOT."/include/".$task.".php");
			$tasks = $$task;
			if ( !$tasks ) {
				gerr("任务文件无效 task-".__LINE__." file=".$task);
				return false;
			}
			foreach ( $tasks as $k => $task )
			{
				if ( $is_check ) {
					$old = $task;
					$task = $this->check($task);
					if ( !$task ) {
						gerr("任务检测失败 task-".__LINE__." task=".json_encode($old));
						return false;
					}
				}
				$tasklist[$task['id']] = $task;
			}
		}
		$actid = $cmd * 10000 + $code;
		foreach ( $tasklist as $k => $v )
		{
			if ( $actid && !in_array($actid, $v['actions']) ) {
				unset($tasklist[$k]);
				continue;
			}
		}
		$this->tasklist = $tasklist;
	}


	// 刷新任务
	private function fresh( $taskid )
	{
		$res = $this->clear($taskid);
		if ( $res ) {
			return $this->init($taskid);
		}
		return false;
	}


	// 清除任务[列表]
	private function clear( $taskid=0 )
	{
		if ( $taskid ) {
			return $this->redis->hdel($this->redis_key_gametask, $taskid);
		}
		return $this->redis->del($this->redis_key_gametask);
	}


	// 初始任务[列表]
	private function init( $taskid=0 )
	{
		$gametask = $this->gametask;
		$gametask = ( $gametask && is_array($gametask) ) ? $gametask : array();
		if ( $taskid ) {
			$task = $this->redis->hget($this->redis_key_gametask, $taskid);
			if ( $task && is_array($task) ) {
				$gametask[$taskid] = $task;
			}
			else{
				gerr("任务数据无效 task-".__LINE__." taskid=".$taskid);
				return false;
			}
		}
		else{
			$gametask = $this->redis->hgetall($this->redis_key_gametask);
		}
		if ( !$gametask )
		{
			$gametask = array();
			$sql = $taskid ? " AND `id` = $taskid" : "";
			$res = $this->mysql->getData("SELECT * FROM `".$this->mysql_table_gametask."` WHERE `is_del` = 0 and `is_lock` = 0 ".$sql);
			$res = $res ? $res : array();
			foreach ( $res as $k => $v )
			{
				$v['id'] = $id = intval($v['id']);
				$v['name'] = $name = trim($v['name']);
				$arr = json_decode($v['actions'],1);
				$v['actions'] = $actions = is_array($arr) ? $arr : array();
				$arr = json_decode($v['columns'],1);
				$v['columns'] = $columns = is_array($arr) ? $arr : array();
				$v['is_get'] = $is_get = intval($v['is_get']);
				$arr = json_decode($v['if_pre'],1);
				$v['if_pre'] = $if_pre = is_array($arr) ? $arr : array();
				$arr = json_decode($v['if_not'],1);
				$v['if_not'] = $if_not = is_array($arr) ? $arr : array();
				$arr = json_decode($v['if_nrs'],1);
				$v['if_nrs'] = $if_nrs = is_array($arr) ? $arr : array();
				$arr = json_decode($v['if_yes'],1);
				$v['if_yes'] = $if_yes = is_array($arr) ? $arr : array();
				$arr = json_decode($v['if_yrs'],1);
				$v['if_yrs'] = $if_yrs = is_array($arr) ? $arr : array();
				$v['days'] = $days = intval($v['days']);
				$v['times'] = $times = intval($v['times']);
				$arr = json_decode($v['opening'],1);
				$v['opening'] = $opening = is_array($arr) ? $arr : array();
				$v['is_lock'] = $is_lock = intval($v['is_lock']);
				$v['is_del'] = $is_del = intval($v['is_del']);
				$v['ut_create'] = $ut_create = intval($v['ut_create']);
				$v['ut_update'] = $ut_update = intval($v['ut_update']);
				$v['is_open'] = $is_open = $this->check_opening($v);
				$gametask[$id] = $v;
			}
			$res = $this->redis->hmset($this->redis_key_gametask,$gametask);
		}
		$this->gametask = $gametask;
		return $taskid ? ( $gametask ? $gametask[$taskid] : array() ) : $gametask;
	}


	// 设置用户
	public function setUser( $userinfo, $usertask=array() )
	{
		if ( !$userinfo || !is_array($userinfo) || count($userinfo) < 29 || !isset($userinfo['uid']) ) {
			return false;
		}
		$uid = $userinfo['uid'];
		$this->uid = $uid;
		$this->userinfo = $userinfo;
		if ($usertask)
		{
			$this->usertask = $usertask;
		}
		else
		{
			$usertask = $this->redis->hgetall($this->redis_key_usertask.'_'.$uid);
			if (!$usertask) {
				$usertask = $this->resetUserTask($uid);
			}
			$this->usertask = ( $usertask && is_array($usertask) ) ? $usertask : array();
		}
		return $uid;
	}


	// 重设用户
	private function resetUserTask( $uid, $taskid=0, $usertask=array() )
	{
		if (!$taskid)
		{
			$usertask = $this->mysql->getLine("SELECT * FROM `".$this->mysql_table_usertask."` WHERE `uid` = $uid");
			if (!$usertask) {
				$res = $this->mysql->runSql("INSERT INTO `".$this->mysql_table_usertask."` ( `uid`, `dateid` ) VALUES ( $uid, ".intval(date("Ymd"))." )");
				$usertask = $this->mysql->getLine("SELECT * FROM `".$this->mysql_table_usertask."` WHERE `uid` = $uid");
			}
			if ($usertask && is_array($usertask)) {
				foreach ( $usertask as $k => $v ) {
					$usertask[$k] = intval($v);
				}
				$res = $this->redis->hmset($this->redis_key_usertask.'_'.$uid, $usertask);
			}
			return ( $usertask && is_array($usertask) ) ? $usertask : array();
		}
		else
		{
			$usertask = ( $usertask && is_array($usertask) ) ? $usertask : $this->redis->hgetall($this->redis_key_usertask.'_'.$uid);
			$usertask = ( $usertask && is_array($usertask) ) ? $usertask : array();
			$usertaskmysql = $this->mysql->getLine("SELECT * FROM `".$this->mysql_table_usertask."` WHERE `uid` = $uid");
			if (!$usertaskmysql) {
				$res = $this->mysql->runSql("INSERT INTO `".$this->mysql_table_usertask."` ( `uid`, `dateid` ) VALUES ( $uid, ".intval(date("Ymd"))." )");
				$usertaskmysql = $this->mysql->getLine("SELECT * FROM `".$this->mysql_table_usertask."` WHERE `uid` = $uid");
			}
			$usertasknew = array();
			$usertaskmysql = ($usertaskmysql && is_array($usertaskmysql)) ? $usertaskmysql : array();
			foreach ( $usertaskmysql as $k => $v ) {
				if (!isset($usertask[$k])) {
					$usertasknew[$k] = intval($v);
				}
			}
			if ($usertasknew) {
				$this->redis->hmset($this->redis_key_usertask.'_'.$uid, $usertasknew);
				$usertask = array_merge($usertask,$usertasknew);
			}
			return $usertask;
		}
	}


	// 检测任务
	public function check($task)
	{
		if ( !is_array($task) || !isset($task['id']) || !isset($task['name']) || !isset($task['if_yes']) || !isset($task['if_not']) )
		{
			gerr("任务数据无效 task-".__LINE__." task=".json_encode($task));
			return false;
		}
		$is_new = $is_upd = 0;
		if ( !$this->gametask ) {
			$this->gametask = $this->init();
		}
		$gametask = $this->gametask;
		// 任务id
		$task['id'] = $id = intval($task['id']);
		if ( $id <= 0 ) {
			gerr("任务数据无效 task-".__LINE__." taskid=".$id);
			return false;
		}
		$taskold = isset($gametask[$id]) ? $gametask[$id] : array();
		// 任务名称
		$task['name'] = $name = trim($task['name']);
		if (!$name) {
			gerr("任务数据无效 task-".__LINE__." taskid=".$id." name=".$name);
			return false;
		}
		// 任务绑定的协议ID
		$actions = ( isset($task['actions']) && is_array($task['actions']) ) ? $task['actions'] : array();
		if ($actions)
		{	// 协议校验
			global $REQ;
			$all_actions = array();
			foreach ( $REQ as $k => $v )
			{
				if ( !$v || !is_array($v) ) {
					continue;
				}
				foreach ( $v as $kk => $vv )
				{
					$all_actions[] = strval($k * 10000 + $kk);
				}
			}
			$action_diff = array_diff($actions, array_intersect($actions, $all_actions));
			if ($action_diff) {
				gerr("任务数据无效 task-".__LINE__." taskid=".$id." action_diff=".json_encode($action_diff));
				return false;
			}
		}
		$task['actions'] = $actions;
		// 任务相关的字段名
		$columns = ( isset($task['columns']) && is_array($task['columns']) ) ? $task['columns'] : array();
		$table_columns = array();
		if ($columns)
		{	// 字段校验 $k=表名.字段名 $v=字段特征(int等等。目前版本仅为备注)
			foreach ( $columns as $k => $v )
			{
				$tcs = explode('.', $k);
				if ( count($tcs) != 2 )
				{
					gerr("任务数据无效 task-".__LINE__." taskid=".$id." columns=".json_encode($columns));
					return false;
				}
				$tab = $tcs[0];
				$col = $tcs[1];
				$table_columns[$tab][]= $col;
			}
			foreach ( $table_columns as $k => $v )
			{
				if (in_array($k, array('userinfo',$this->redis_key_userinfo,$this->mysql_table_userinfo)))
				{
					// // redis专属字段校验  此版本执行check时，还么有用户
					// foreach ( $v as $kk => $vv )
					// {
					// 	if (!isset($userinfo[$vv])) {
					// 		gerr("任务数据无效 task-".__LINE__." columns=".json_encode($columns));
					// 		return false;
					// 	}
					// }
				}
				elseif (in_array($k, array('usertask',$this->redis_key_usertask,$this->mysql_table_usertask)))
				{
					// // redis字段校验  此版本执行check时，还么有用户
					// $redis_news = array();
					// foreach ( $v as $kk => $vv )
					// {
					// 	if (!isset($usertask[$vv])) {
					// 		$redis_news[]=$vv;
					// 	}
					// }
					// if (!$redis_news) {
					// 	continue;
					// }
					// 用户任务数据表字段校验
					$tablename = $this->mysql_table_usertask;
					$data = $this->mysql->getData("DESC `$tablename`");
					$cols = array();
					foreach ( $data as $kk => $vv )
					{
						$cols[] = $vv['Field'];
					}
					// 任务周期(天)	0一次性的任务/N以N天为一周期 //1每天日常任务/7每周任务(周一开始)/30每月任务(月一开始月末28293031结束)/其他数字，比如5:每5天为一周期(以参与任务的当天开始)
					$days  = intval( isset($task['days'])  && $task['days'] > 0 );
					// 每天次数(次)	0没有次数限制/N每天可执行N次 //days=1 times=2 : 每天两次的日常任务 //days=0 times=1 : 1次性任务 //days=5 times=1 : 5天为一周期，每天一次 //days=0 times=0 : 无效任务
					$times = intval( isset($task['times']) && $task['times'] > 0 );
					// `task11dateid`, 本任务的本次执行日期
					// `task11`, 1天1次的任务(默认)，或者1次性任务, 或者无效任务？
					// `task11`, `task11days`, N天1次的任务(N!=1)
					// `task11`, `task11times`,  1天N次的任务(N!=1)
					// `task11`, `task11days`, `task11times`, N天N次的任务(N!=1)
					$base = array('task'.$id,'task'.$id.'dateid');
					if ( $days > 1 ) { $base[]= 'task'.$id.'days'; }
					if ( $times> 1 ) { $base[]= 'task'.$id.'times'; }
					$v = array_unique(array_merge($base,$v));
					$news = array_diff($v, $cols);
					if ($news)
					{	// 任务字段表自动扩建字段，默认int，当前版本仅支持int，设置任务时要注意条件配置，足够了。
						foreach ( $news as $kk => $vv )
						{
							$sql = "ALTER TABLE `$k` ADD `$vv` INT(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT ''";
							$res = $this->mysql->runSql($sql);
							if ( !$res ) {
								gerr("任务扩字失败 task-".__LINE__." taskid=".$id." table_columns=".json_encode($table_columns));
								return false;
							}
						}
					}
				}
				else
				{	// 非任务字段表，终止任务创建，并报错
					gerr("任务字段无效 task-".__LINE__." taskid=".$id." table_columns=".json_encode($table_columns));
					return false;
				}
			}
		}
		$task['columns'] = $columns;
		// 0不需领取即可依据条件开始/1需要达到领取条件后才可领取
		$task['is_get'] = $is_get = intval( isset($task['is_get']) && $task['is_get'] );
		// 前置(领取)条件(且)
		$column = 'if_pre';
		$task[$column] = $$column = $this->check_columns( $task, $table_columns, $column, __LINE__ );
		if ($$column === false) { return false; }
		// 成功条件(且)
		$column = 'if_yes';
		$task[$column] = $$column = $this->check_columns( $task, $table_columns, $column, __LINE__ );
		if ($$column === false) { return false; }
		// 成功结果(且)
		$column = 'if_yrs';
		$task[$column] = $$column = $this->check_columns( $task, $table_columns, $column, __LINE__ );
		if ($$column === false) { return false; }
		// 失败条件(或)(失败优先 但目前的斗地主任务都不会失败)
		$column = 'if_not';
		$task[$column] = $$column = $this->check_columns( $task, $table_columns, $column, __LINE__ );
		if ($$column === false) { return false; }
		// 失败结果(且)
		$column = 'if_nrs';
		$task[$column] = $$column = $this->check_columns( $task, $table_columns, $column, __LINE__ );
		if ($$column === false) { return false; }
		// 结果回馈 预留
		// $task['respond'] = array();//array('cmd'=>5,'code'=>1022,'params'=>array(array('key'=>'user.coins','val'=>45678)))
		// 任务周期(天)	0没有周期的N次性任务/1日常任务/7每周任务(周一开始)/30每月任务(月一开始月末28293031结束)/其他数字，比如5:每5天为一周期(以参与任务的当天开始)
		$task['days'] = $days = intval( isset($task['days']) && $task['days'] > 0 );
		// 任务次数(期)	0不限次数 //days=1 times=2 : 每天两次的日常任务 //days=0 times=1 : 1次性任务 //days=5 times=1 : 5天为一周期，每天一次 //days=0 times=0 : 无效任务
		$task['times'] = $times = intval( isset($task['times']) && $task['times'] > 0 );
		// 开放设置(或)	''永久开放/array("2014-06-01 09:00:00|2018-06-31 23:30:00|1234567",...)
		$task['opening'] = $opening = ( isset($task['opening']) && is_array($task['opening']) ) ? $task['opening'] : array();
		$task['is_lock'] = $task['is_del'] = $is_del = $is_lock = 0;
		$task['ut_create'] = $task['ut_update'] = $ut_now = time();
		$task['is_open'] = $this->check_opening($task);

		if ( $taskold )
		{	// 更新mysql
			$res = $this->mysql->runSql("UPDATE `".$this->mysql_table_gametask."` SET `name`='".$name."', `actions`='".json_encode($actions)."', `columns`='".$this->mysql->db->real_escape_string(json_encode($columns))."', `is_get`=$is_get, `if_pre`='".json_encode($if_pre)."', `if_yes`='".json_encode($if_yes)."', `if_yrs`='".json_encode($if_yrs)."', `if_not`='".json_encode($if_not)."', `if_nrs`='".json_encode($if_nrs)."', `days`=$days, `times`=$times, `opening`='".json_encode($opening)."', `is_lock`=$is_lock, `is_del`=$is_del, `ut_update`=$ut_now  WHERE `id`=$id");
		}
		else
		{	// 写入mysql
			$res = $this->mysql->runSql("INSERT INTO `".$this->mysql_table_gametask."` ( `id`, `name`, `actions`, `columns`, `is_get`, `if_pre`, `if_yes`, `if_yrs`, `if_not`, `if_nrs`, `days`, `times`, `opening`, `is_lock`, `is_del`, `ut_create`, `ut_update` ) VALUES ".
			"( $id, '$name', '".json_encode($actions)."', '".$this->mysql->db->real_escape_string(json_encode($columns))."', $is_get, '".json_encode($if_pre)."', '".json_encode($if_yes)."', '".json_encode($if_yrs)."', '".json_encode($if_not)."', '".json_encode($if_nrs)."', $days, $times, '".json_encode($opening)."', $is_lock, $is_del, $ut_now, $ut_now )");
		}
		// 写入redis
		$res = $this->redis->hset($this->redis_key_gametask, $task['id'], $task);
		return $task;
	}


	// 更新系统任务
	private function update($taskid)
	{
		// 预留
	}


	// 移除系统任务
	public function remove($task)
	{
		// 预留
	}


	/*	mysql-table_usertaskrecord
		id				int			编号
		redis_key_usertaskrecord
		uid				int			用户id
		taskid			int			任务id
		dateid			int			任务周期开始日期	如20141111
		days			int			任务周期内第几天	如1
		times			int			任务周期内第几次	如1
		coins			int			获得乐豆
		coupon			int			获得奖券
		gold			int			获得金币
		exp				int			获得经验
		lottery			int			获得抽奖机会
		propid			int			获得主要道具id
		props			jsontext	获得道具ids
		is_done			tinyint		是否结束
		ut_create		int			创建时间
		ut_update		int			更新时间
	 */


	// 检查当前任务
	private function isdone( $usertask, $task )
	{
		$uid = $usertask['uid'];
		$taskid = $task['id'];
		$dateid = intval(date("Ymd"));
		if ( !$usertask['task'.$taskid] ) {
			return false;
		}
		if ( $task['days'] > 1 )
		{	// 有连续多天周期循环
			$is_done_days = !!( ($dateid - $usertask['task'.$taskid.'dateid']) == ($usertask['task'.$taskid.'days'] - 1) && $usertask['task'.$taskid.'days'] == $task['days'] );
		}
		elseif ( $task['days'] == 1 )
		{	// 每日任务
			$is_done_days = !!( $usertask['task'.$taskid.'dateid'] == $dateid );
		}
		else
		{	// 一次性任务
			return true;
		}
		if ( $task['times'] > 1 )
		{	// 每周期多次
			$is_done_times = !!( $usertask['task'.$taskid.'times'] == $task['times'] );
		}
		elseif ( $task['times'] == 1 )
		{	// 每周期一次
			$is_done_times = !!( $usertask['task'.$taskid.'dateid'] == $dateid );
		}
		else
		{	// 每周期不限次数
			$is_done_times = false;
		}
		return $is_done_days && $is_done_times;
	}


	// 执行完成任务
	private function finish($usertask,$task,$result)
	{
		// 处理道具 预留 目前在client实现了。

		// 处理结束 处理记录
		$uid = $usertask['uid'];
		$taskid = $task['id'];
		$dateid = intval(date("Ymd"));
		$record['uid'] = $uid;
		$record['taskid'] = $taskid;
		$is_done_days = $is_done_times = 0;
		if (!isset($usertask['task'.$taskid.'days']))
		{	// 任务周期(天)	0一次性的任务/N以N天为一周期
			$record['dateid'] = $usertask['task'.$taskid.'dateid'] = $dateid;
			$record['days'] = $task['days'];//0/1
			$is_done_days = 1;
		}
		else
		{	// 有周期循环  // 固定的周月周期，与不固定的其他数字周期 的计算方法 // 预留
			// 目前使用非固定周期计算天数 如持续5天为一周期
			if (!$usertask['task'.$taskid.'days'])
			{	// 没有任务 初始化周期开始日期
				$record['dateid'] = $usertask['task'.$taskid.'dateid'] = $dateid;
				$record['days'] = $usertask['task'.$taskid.'days'] = 1;
			}
			else
			{
				if ( ($dateid - $usertask['task'.$taskid.'dateid']) == ($usertask['task'.$taskid.'days']-1) )
				{	// 上次日期连续 周期不变，天数增加
					$record['dateid'] = $usertask['task'.$taskid.'dateid'];
					$record['days'] = $usertask['task'.$taskid.'days']++;
					// 识别周期结束
					$is_done_days = intval($record['days'] >= $task['days']);
				}
				else
				{	// 任务重置 初始化周期开始日期
					$record['dateid'] = $usertask['task'.$taskid.'dateid'] = $dateid;
					$record['days'] = $usertask['task'.$taskid.'days'] = 1;
				}
			}
		}
		if (!isset($usertask['task'.$taskid.'times']))
		{	// 每天次数(次)	0没有次数限制/N每天可执行N次
			$record['times'] = $task['times'];//0/1
			$is_done_times = $task['times'];//0用不结束
		}
		else
		{	// 有次数限制
			if (!$usertask['task'.$taskid.'times'] || $usertask['task'.$taskid.'times'] >= $task['times'])
			{	// 没有任务 初始化次数
				$record['times'] = $usertask['task'.$taskid.'times'] = 1;
			}
			else
			{	// 次数增加
				$record['times'] = $usertask['task'.$taskid.'times']++;
				// 识别次数结束
				$is_done_times = intval($record['times'] >= $task['times']);
			}
		}
		$record = array_merge($record,$result['record']);
		$record['is_done'] = $is_done = intval($is_done_days && $is_done_times);
		$record['ut_create'] = $record['ut_update'] = time();
		if ($is_done)
		{	// 处理任务结束
			$newut['task'.$taskid] = $usertask['task'.$taskid] = 1;
		}
		$newut['task'.$taskid.'dateid'] = $usertask['task'.$taskid.'dateid'];
		if (isset($usertask['task'.$taskid.'days'])) {
			$newut['task'.$taskid.'days'] = $usertask['task'.$taskid.'days'];
		}
		if (isset($usertask['task'.$taskid.'times'])) {
			$newut['task'.$taskid.'times'] = $usertask['task'.$taskid.'times'];
		}
		// 更改用户任务
		$key = $this->redis_key_usertask.'_'.$uid;
		$newut = array_merge($result['usertask'],$newut);
		$res = $this->redis->hmset($key,$newut);

		// 更改用户信息 确认在这里合适？ 待续 待最终确认 待优化重整
		$res = setUser($uid, $result['userinfo']);

		// 写入处理记录 进入redis 后续要入mysql 标记 cron脚步开发 待续
		$key = $this->redis_key_usertaskrecord;
		$res = $this->redis->ladd($key,$record);
		return $newut;
	}


	// 字段检测 任务条件/任务结果
	private function check_columns($task, $table_columns, $column, $line)
	{
		$cons = ( isset($task[$column]) && is_array($task[$column]) ) ? $task[$column] : array();
		if ($cons)
		{	// 字段校验
			foreach ( $cons as $k => $v )
			{
				$vkey = $v['key'];
				$vkey = explode('.', $vkey);
				if ( count($vkey) != 2 || !isset($table_columns[$vkey[0]]) || !in_array($vkey[1], $table_columns[$vkey[0]]) )
				{
					gerr("任务条件无效 task-$line taskid=".$task['id']." task.$column=".json_encode($cons));
					return false;
				}
			}
		}
		return $cons;
	}


	// 开放检测
	private function check_opening($task)
	{
		// 目前所有任务都默认为is_open=1
		return 1;
		$is_open = 0;
		if ( !isset($task['opening']) || !isset($task['is_del']) || !isset($task['is_lock']) || $task['is_del'] || $task['is_lock'] ) {
			return $is_open;
		}
		if ( !$task['opening'] || !is_array($task['opening']) ) {
			return $is_open = 1;
		}
		$wk_day = date("N");//周n[1-7]
		$mt_now = microtime(1);
		$ut_day = strtotime(date('Y-m-d'));
		$setting = $task['opening'];
		foreach ( $setting as $k=>$v )
		{
			$v = explode("|",$v);
			if ( count($v) != 3 ) {
				continue;
			}
			$start = explode(" ", $v[0]);
			$dateStart = strtotime($start[0].' 00:00:00');
			$todayStart = strtotime(date("Y-m-d ".$start[1]));
			$end = explode(" ", $v[1]);
			$dateEnd =  strtotime($end[0].' 23:59:59');
			$todayEnd = strtotime(date("Y-m-d ".$end[1]));
			$weeks = $v[2];
			if ( $ut_day > $dateStart && $ut_day < $dateEnd && $mt_now > $todayStart && $mt_now < $todayEnd && ($weeks ? (strpos($weeks,$wk_day) !== false) : 1) )
			{
				$is_open = 1;
				break;
			}
		}
		return $is_open;
	}


	// 赋值计算器
	private function compute($valOld,$valStr)
	{
		$c = array('+','-','*','/','=');
		$o = $valOld;
		$s = strval($valStr);
		$p = $s[0];
		$f = str_replace( $c, '', $s )+0;
		switch ( $p )
		{
			case '+':
				$n = max( 0, ( $o + $f ) ) + 0 ;
				break;
			case '-':
				$n = max( 0, ( $o - $f ) ) + 0 ;
				break;
			case '*':
				$n = max( 0, ( $o * $f ) ) + 0 ;
				break;
			case '/':
				$n = max( 0, ( $o / $f ) ) + 0 ;
				break;
			default:
				$n = max( 0, $f ) + 0 ;
				break;
		}
		return $n;
	}


	// 条件比较器
	private function compare($valLeft,$leg,$valRight)
	{
		switch ($leg)
		{
			case 'l':
				$cond = intval($valLeft < $valRight);
			break;
			case 'le':
				$cond = intval($valLeft <= $valRight);
			break;
			case 'g':
				$cond = intval($valLeft > $valRight);
			break;
			case 'ge':
				$cond = intval($valLeft >= $valRight);
			break;
			case 'ne':
				$cond = intval($valLeft != $valRight);
			break;
			default:
				$cond = intval($valLeft == $valRight);
			break;
		}
		return !!$cond;
	}


	// 条件执行期
	private function run_condit( $key, $task, $userinfo, $usertask, $is_and=true )
	{
		$id = $task['id'];
		$if_ = isset($task[$key]) ? $task[$key] : array();
		$if_ = ( $if_ && is_array($if_) ) ? $if_ : array();
		// 无条件时，默认通过
		if ( !$if_ ) { return true; }
		// 有条件时，遍历条件
		foreach ( $if_ as $k => $v )
		{
			$key = explode('.', $v['key']);
			$leg = $v['leg'];
			$val = $v['val'];
			$tnam = $key[0];
			$tcol = $key[1];
			$is_true = false;
			if ( in_array($tnam, array('userinfo',$this->redis_key_userinfo,$this->mysql_table_userinfo)) )
			{
				if (!isset($userinfo[$tcol])) {
					gerr("任务条件失败 task-".__LINE__." taskid=".$id." ".__FUNCTION__." !isset=userinfo.".$tcol);
					continue;
				}
				$is_true = $this->compare($userinfo[$tcol],$leg,$val);
			}
			elseif ( in_array($tnam, array('usertask',$this->redis_key_usertask,$this->mysql_table_usertask)) )
			{
				if (!isset($usertask[$tcol])) {
					gerr("任务条件失败 task-".__LINE__." taskid=".$id." ".__FUNCTION__." !isset=usertask.".$tcol);
					continue;
				}
				$is_true = $this->compare($usertask[$tcol],$leg,$val);
			}
			else
			{
				// 预留 暂不支持其他表的数据。
				// 使用数据缓存或大数据方案之后，可以考虑继续开发扩展
				// 或者更改任务设置，在user表或用户任务表中增加对应字段
				gerr("任务执行失败 task-".__LINE__." taskid=".$id." ".__FUNCTION__.".key=".$v['key']);
				continue;
			}
			if ( $is_and )
			{
				if ( !$is_true ) {
					return false;
				}
			}
			else
			{
				if ( $is_true ) {
					return true;
				}
			}
		}
		return $is_and;
	}


	// 结果执行器
	private function run_result( $key, $task, $userinfo, $usertask )
	{
		$rs_arr = array('userinfo'=>array(),'usertask'=>array(),'record'=>array('coins'=>0,'coupon'=>0,'gold'=>0,'exp'=>0,'lottery'=>0,'propid'=>0,'props'=>array()));
		$id = $task['id'];
		$rs_ = isset($task[$key]) ? $task[$key] : array();
		$rs_ = ( $rs_ && is_array($rs_) ) ? $rs_ : array();
		// 无结果时，直接返回
		if ( !$rs_ ) { return $rs_arr; }
		// 有结果时，遍历条件
		foreach ( $rs_ as $k => $v )
		{
			$key = explode('.', $v['key']);
			$val = $v['val'];
			$tnam = $key[0];
			$tcol = $key[1];
			if ( in_array($tnam, array('userinfo',$this->redis_key_userinfo,$this->mysql_table_userinfo)) )
			{
				if (!isset($userinfo[$tcol])) {
					gerr("任务结果失败 task-".__LINE__." taskid=".$id." ".__FUNCTION__." !isset=userinfo.".$tcol);
					continue;
				}
				$old = $userinfo[$tcol];
				$rs_arr['userinfo'][$tcol] = $userinfo[$tcol] = $this->compute($old,$val);
				if (in_array($tcol, array('lottery','coins','coupon','gold','exp'))) {
					$rs_arr['record'][$tcol] = max(intval($userinfo[$tcol]-$old),0);
				}
				elseif ($tcol == 'propid') {
					$rs_arr['record']['propid'] = $val;
					$rs_arr['record']['props'][]= $val;
				}
				// 道具的 发放 问题。。。暂时预留后面
			}
			elseif ( in_array($tnam, array('usertask',$this->redis_key_usertask,$this->mysql_table_usertask)) )
			{
				if (!isset($usertask[$tcol])) {
					gerr("任务结果失败 task-".__LINE__." taskid=".$id." ".__FUNCTION__." !isset=usertask.".$tcol);
					continue;
				}
				$old = $usertask[$tcol];
				$rs_arr['usertask'][$tcol] = $usertask[$tcol] = $this->compute($old,$val);
			}
			else
			{
				// 预留 暂时不支持其他表的数据。 使用数据缓存或大数据方案之后，可以考虑继续开发扩展
				// 或者更改任务设置，在user表或redis-userinfo或用户任务表中增加对应字段
				gerr("任务结果失败 task-".__LINE__." taskid=".$id." ".__FUNCTION__." table.column=".$v['key']);
				continue;
			}
		}
		return $rs_arr;
	}


	/*
	 * return 执行相关情况
	 */
	// 自动执行任务
	public function run( $userinfo=array(), $usertask=array() )
	{
		if ($userinfo) {
			$res = $this->setUser($userinfo, $usertask);
			if (!$res) {
				gerr("任务设置失败 task-".__LINE__." userinfo=".json_encode($userinfo));
				return false;
			}
		}
		else {
			gerr("任务用户无效 task-".__LINE__." userinfo=".json_encode($userinfo));
			return false;
			//系统级任务？ 预留
		}
		$tasklist = $this->tasklist;
		$uid = $this->uid;
		$userinfo = $this->userinfo;
		$usertask = $this->usertask;
		$rs_arr = array();
		foreach ( $tasklist as $k => $task )
		{
			$id = $task['id'];
			// 全部开放 暂时不需检查
			// if ( !$this->check_opening($task) ) {
			// 	unset($tasks[$id]);
			// 	continue;
			// }
			if ( !isset($usertask['task'.$id]) ) {
				$usertask = $this->usertask = $this->resetUserTask($uid, $id, $usertask);
			}
			if ( !$this->isdone($usertask, $task) )
			{
				//以and语法校验if_pre的条件 默认无前置条件
				$res = $task['if_pre'] ? $this->run_condit('if_pre',$task,$userinfo,$usertask,true) : true;
				if ( !$res ) {
					continue;
				}
				// if ($task['is_get']) {
				// 	// 预留 执行领取操作 待续
				// 	DEBUG && debug("任务执行领取 task-".__LINE__." taskid=".$id." need_get=1");
				// }
				//以or语法校验if_not的条件 失败优先 默认无失败
				$res = $task['if_not'] ? $this->run_condit('if_not',$task,$userinfo,$usertask,false) : false;
				if ($res) {
					// 预留 执行失败结果
					// $res = $this->run_result('if_nrs',$task,$userinfo,$usertask);
					continue;
				}
				//以and语法校验if_yes的条件 成功其后 默认成功
				$res = $task['if_yes'] ? $this->run_condit('if_yes',$task,$userinfo,$usertask,true) : true;
				if ($res) {
					$res = $this->run_result('if_yrs',$task,$userinfo,$usertask);
					$rs_arr[$id]['task'] = $task;
					$rs_arr[$id]['record'] = $res['record'];
					$rs_arr[$id]['userinfo'] = $res['userinfo'];
					$userinfo = array_merge($userinfo,$res['userinfo']);
					foreach ( $res['record'] as $kk => $vv )
					{
						if ( ! $vv ) continue;
						$this->model->getRecord()->money('固定任务', $kk, $vv, $uid, $userinfo);
					}
					$rs_arr[$id]['usertask'] = $res['usertask'];
					$usertask = array_merge($usertask,$res['usertask']);
					$newut = $this->finish($usertask,$task,$rs_arr[$id]);
					$rs_arr[$id]['usertask'] = array_merge($rs_arr[$id]['usertask'],$newut);
					$usertask = array_merge($usertask,$newut);
					continue;
				}
			}
		}
		$this->userinfo = $userinfo;
		$this->usertask = $usertask;
		return $rs_arr;
	}

}


// date_default_timezone_set('PRC');
// error_reporting(E_ALL);		//E_ERROR | E_WARNING | E_PARSE
// define("ROOT", dirname(__DIR__));
// define("DEBUG", 1);			//0关闭调试1开启调试
// define("IS_TEST", 1);		//0常规逻辑1压测逻辑
// define("IS_ROBOT", 1);		//0无机器人1有机器人
// define("IS_LOCAL_LOG", 1);	//0远程日志1本地日志
// define("IS_CLIENT_BEAT", 0);//0服端心跳1客端心跳
// define("ROE_ACTION", 0.4);	//协议执行超过N秒时，记录ERROR
// define("ROE_TASK", 0.4);	//任务执行超过N秒时，记录ERROR
// define("ROE_TIMER", 1.5);	//时钟执行超过自身周期的N倍时间时，记录ERROR
// define("LOGID", "GAME-DDZ");//远程日志中的游戏标识 区别于其他游戏
// //host
// define("HOST", "112.124.4.59");	//ALI DZPK 内网
// define("PORT", 9000);			//德州测试机专用
// define("HOSTID", HOST."_".PORT);//HOST标识
// //mysql
// define("MYSQL_HOST", "127.0.0.1");//本机mysql
// define("MYSQL_PORT", 3306);
// define("MYSQL_USERNAME", "dbx5415j5nf05kqn");
// define("MYSQL_PASSWORD", "TYxYpysG8fR8PQdp");
// define("MYSQL_DBNAME", "dbx5415j5nf05kqn");
// define("MYSQL_CHARSET", "UTF8");
// //redis
// define("REDIS_HOST", "127.0.0.1");//本机redis
// define("REDIS_PORT", 6379);
// //swoole
// define("SWOOLE_MAX_CONNECT",10240);	//最大连接数10240
// define("SWOOLE_MAX_BACKLOG",128);	//最大排队数128
// define("SWOOLE_DISPATCH_MOD",2);	//FD分配模式2
// define("SWOOLE_WORKER_NUM",16);		//工作进程数16/32
// define("SWOOLE_TASKER_NUM",24);		//任务进程数24/48
// define("SWOOLE_WORKER_REQ",10000);	//进程回收数10000
// define("SWOOLE_BEAT_INTERVAL",10);	//心跳频率秒10
// define("SWOOLE_BEAT_IDLETIME",300);	//心跳寿命秒300
// define("SWOOLE_ERROR_LOG", ROOT."/log/sweety.log");//PHP-ERROR/SWOOLE-ERROR/ECHO/DUMP/../本地日志

// //local_log
// define("LOG_LEVEL", 75);//5-CRITICAL//10-ERROR//25-WARNING//50-NOTICE//75-INFO//100-DEBUG
// if ( IS_TEST ) {
// define("DEBUG_LOG", ROOT."/log/game.log");
// define("GAME_LOG", ROOT."/log/game.log");
// define("GAME_ERR", ROOT."/log/game.log");
// define("SERVER_LOG", ROOT."/log/game.log");
// define("SERVER_ERR", ROOT."/log/game.log");
// }
// else{
// define("DEBUG_LOG", ROOT."/log/game.log");
// define("GAME_LOG", ROOT."/log/game.log");
// define("GAME_ERR", ROOT."/log/error.log");
// define("SERVER_LOG", ROOT."/log/game.log");
// define("SERVER_ERR", ROOT."/log/error.log");
// }

// //run
// require_once(ROOT."/config.php");
// require_once(ROOT."/lib/util.php");
// if ( IS_LOCAL_LOG ) {
// 	require_once(ROOT."/lib/class.log.php");
// }
// require_once(ROOT."/lib/class.mysql.php");
// require_once(ROOT."/lib/class.redis.php");
// require_once(ROOT."/game/card.php");
// require_once(ROOT."/game/client.php");
// require_once(ROOT."/game/queue.php");
// require_once(ROOT."/game/model.php");


// $cmd = 5;
// $code = 0;
// $model = new model();
// $userdata = $model->db->getLine('select * from lord_game_user where gold = 0 order by uid limit 1');
// $tasker = new task($model,$userdata);
// $task = array(
// 	'name' => '登录任务',
// 	'actions' => array(50000,50001),
// 	'columns' => array('lord_game_user.gold'=>'','lord_game_user.coins'=>'','lord_user_task.aaa'=>''),
// 	'if_get' => 0,
// 	'if_pre' => array(),
// 	'if_yes' => array(array('key'=>'lord_game_user.gold','leg'=>'e','val'=>0)),
// 	'if_yrs' => array(array('key'=>'lord_game_user.coins','val'=>'+1'),array('key'=>'lord_user_task.aaa','val'=>'=1231')),
// 	'if_not' => array(),
// 	'if_nrs' => array(),
// 	'days' => 1,
// 	'times' => 0,
// 	'opening' => array(),
// );
// $task = $tasker->create($task);
// DEBUG && debug("任务检查代码 task=".json_encode($task));

// $res = $tasker->run($cmd,$code,$userdata);

// DEBUG && debug("任务检查代码 task=".json_encode($res));
