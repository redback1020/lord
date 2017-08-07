<?php

/**
 * PHP redis class
 */
class RD
{
	public $errno = 0;
	public $error = '';
	public $host = RD_HOST;
	public $port = RD_PORT;
	public $redis = false;
	public $iskeep = false;

	function __construct($iskeep = false, $isthrow = false)
	{
		$this->iskeep = $iskeep;
		$this->redis = new Redis();
		try {
			$this->connect(0, $isthrow);
		} catch (Exception $e) {
			serr("[REDISD][class.redis.php-" . __LINE__ . "] Redis construct failed.");
			if ($isthrow) throw new Exception("Redis construct failed.");
		}
	}

	function connect($reconnect = 1, $isthrow = false)
	{
		if ($reconnect && !$this->ping()) {
			$this->close();
		}
		for ($i = 0; $i < 2; $i++) {
			if ($this->iskeep) {
				$res = @$this->redis->pconnect($this->host, $this->port);
			} else {
				$res = @$this->redis->connect($this->host, $this->port);
			}
			if ($res) {
				return true;
			}
		}
		serr("[REDISD][class.redis.php-" . __LINE__ . "] Redis connect failed.");
		if ($isthrow) throw new Exception("Redis connect failed.");
		return false;
	}

	function workLock($key, $is_only = 0, $times = 0)
	{
		// static $times_ = 0;
		// $times_++;

		// if ( $times >= 50 || $times_ >= 50 ) {
		// 	serr("[REDISD][workLock-".__LINE__."] $key | $is_only | $times | $times_ ");
		// }
		$key = trim(strval($key));
		if (!$key) return false;
		$now = round(microtime(1) + mt_rand(0, 100) / 997, 6);
		$loop_interval = 0.2;    //尝试间隔 -秒
		$lock_keeptime = 10;    //有效锁长 -秒
		$loop_maxtimes = intval($lock_keeptime / $loop_interval);    //尝试次数上限
		$lock_expire = $now + $lock_keeptime;                        //失效期限 -秒
		$res = $this->redis->setnx($key, $lock_expire);
		//已经有锁，尚可尝试
		if ($res === false && $times < $loop_maxtimes) {
			//有锁互斥，加锁失败
			if ($is_only)
				return false;
			//针对旧锁，尝试换新
			$expire = $this->redis->getSet($key, $lock_expire);
			//旧锁已解，加锁成功
			if ($expire === false)
				return true;
			//旧锁有效，稍候再试
			if ($expire != $lock_expire && $expire > $now) {
				usleep($loop_interval * 1000000);
				$times++;
				return $this->workLock($key, $is_only, $times);
			}
			//旧锁过期，加锁成功
			if ($expire != $lock_expire && $expire < $now)
				return true;
			//极端情况，稍候再试
			usleep($loop_interval * 1000000);
			$times++;
			return $this->workLock($key, $is_only, $times);
		} //已经有锁，不可尝试
		elseif ($res === false && $times >= $loop_maxtimes) {
			serr("[REDISD][class.redis.php-" . __LINE__ . "] Redis workLock overflow.");
			//强制解锁，再次抢锁
			$this->workDone($key);
			return $this->workLock($key, $is_only, 0);
		}
		//当前无锁，加锁成功
		return true;
	}

	function workDone($key)
	{
		return $this->del($key);
	}

	function getLock($key)
	{
		$res = $this->redis->get($key);
		return is_numeric($res) && $res > 0 ? $res : false;
	}

	function setLock($key, $is_only = 0, $times = 0)
	{
		return $this->workLock($key, $is_only, $times);
	}

	function delLock($key)
	{
		return $this->del($key);
	}

	function fixLock($pattern, $fixPool)
	{
		//查找上次锁
		$old = $this->get($fixPool);//srv_locks
		$old = ($old && is_array($old)) ? $old : [];
		//查找当前锁
		$now = [];
		$now_ = $this->keys($pattern);
		$now_ = ($now_ && is_array($now_)) ? $now_ : [];
		if ($now_) {
			foreach ($now_ as $k => $v) {
				$val = $this->get($v);
				if ($val > 0) {
					$now[] = $v . '_value_' . $val;
				}
			}
		}
		if ($now) {    //有当前锁
			if ($old) {    //有上次锁，匹配上次锁和当前锁之间的重复锁
				$die = array_values(array_intersect($old, $now));
				if ($die && is_array($die)) {    //找到重复锁，删除重复锁，筛选出正常的本次锁
					foreach ($die as $keyval) {
						$key = preg_replace('/_value_.*/i', '', $keyval);
						$res = $this->del($key);
						gerr("死锁完成修复[" . $keyval . "]");
					}
					$now = array_values(array_diff($now, $die));
				}
			}
			//把正常的当前锁，重设至锁池
			$res = $this->set($fixPool, $now);
		} else {    //无当前锁
			if ($old) {    //有上次锁，清空上次锁池
				$this->del($fixPool);
			}
		}
		return true;
	}

	function keys($pattern)
	{
		$pattern = trim(strval($pattern));
		if ($pattern === '') return false;
		try {
			return $this->redis->keys($pattern);
		} catch (Exception $e) {
			try {
				$this->connect();
				return $this->redis->keys($pattern);
			} catch (Exception $ee) {
				return false;
			}
		}
	}

	function set($key, $value)
	{
		$key = trim(strval($key));
		if ($key === '') return false;
		try {
			return $this->redis->set($key, $this->_json_encode($value));
		} catch (Exception $e) {
			try {
				$this->connect();
				return $this->redis->set($key, $this->_json_encode($value));
			} catch (Exception $ee) {
				return false;
			}
		}
	}

	function setnx($key, $value)
	{
		$key = trim(strval($key));
		if ($key === '') return false;
		try {
			return $this->redis->setnx($key, $this->_json_encode($value));
		} catch (Exception $e) {
			try {
				$this->connect();
				return $this->redis->setnx($key, $this->_json_encode($value));
			} catch (Exception $ee) {
				return false;
			}
		}
	}

	function get($key)
	{
		$key = trim(strval($key));
		if ($key === '') return false;
		try {
			return $this->_json_decode($this->redis->get($key));
		} catch (Exception $e) {
			try {
				$this->connect();
				return $this->_json_decode($this->redis->get($key));
			} catch (Exception $ee) {
				return false;
			}
		}
	}

	function del($key)
	{
		$key = trim(strval($key));
		if ($key === '') return false;
		try {
			$this->redis->del($key);
		} catch (Exception $e) {
			try {
				$this->connect();
				$this->redis->del($key);
			} catch (Exception $ee) {
				return false;
			}
		}
		return true;
	}

	function hset($key, $field, $value)
	{
		$key = trim(strval($key));
		if ($key === '') return false;
		$field = trim(strval($field));
		if ($field === '') return false;
		if ($key == 'lord_table_info_0') {
			serr("ssssss hset($key, $field, $value)");
		}
		try {
			$res = $this->redis->hset($key, $field, $this->_json_encode($value));
			return $res === false ? false : true;
		} catch (Exception $e) {
			try {
				$this->connect();
				$res = $this->redis->hset($key, $field, $this->_json_encode($value));
				return $res === false ? false : true;
			} catch (Exception $ee) {
				return false;
			}
		}
	}

	function hsetnx($key, $field, $value)
	{
		$key = trim(strval($key));
		if ($key === '') return false;
		$field = trim(strval($field));
		if ($field === '') return false;
		try {
			$res = $this->redis->hsetnx($key, $field, $this->_json_encode($value));
			return $res;
		} catch (Exception $e) {
			try {
				$this->connect();
				$res = $this->redis->hsetnx($key, $field, $this->_json_encode($value));
				return $res;
			} catch (Exception $ee) {
				return false;
			}
		}
	}

	function hget($key, $field)
	{
		$key = trim(strval($key));
		if ($key === '') return false;
		$field = trim(strval($field));
		if ($field === '') return false;
		try {
			return $this->_json_decode($this->redis->hget($key, $field));
		} catch (Exception $e) {
			try {
				$this->connect();
				return $this->_json_decode($this->redis->hget($key, $field));
			} catch (Exception $ee) {
				return false;
			}
		}
	}

	function hdel($key, $field)
	{
		$key = trim(strval($key));
		if ($key === '') return false;
		$field = trim(strval($field));
		if ($field === '') return false;
		try {
			$this->redis->hdel($key, $field);
		} catch (Exception $e) {
			try {
				$this->connect();
				$this->redis->hdel($key, $field);
			} catch (Exception $ee) {
				return false;
			}
		}
		return true;
	}

	function hmset($key, $dataArray)
	{
		$key = trim(strval($key));
		if ($key === '') return false;
		$dataArray = (is_array($dataArray) && $dataArray) ? $dataArray : [];
		if (!$dataArray) return false;
		if ($key == 'lord_table_info_0') {
			serr("ssssss hmset($key, data) data=" . json_encode($dataArray));
		}
		foreach ($dataArray as $k => $v) {
			$dataArray[$k] = $this->_json_encode($v);
		}
		try {
			return $this->redis->hmset($key, $dataArray);
		} catch (Exception $e) {
			try {
				$this->connect();
				return $this->redis->hmset($key, $dataArray);
			} catch (Exception $ee) {
				return false;
			}
		}
	}

	function hmget($key, $fieldArray)
	{
		$key = trim(strval($key));
		if ($key === '') return false;
		$fieldArray = (is_array($fieldArray) && $fieldArray) ? $fieldArray : [];
		if (!$fieldArray) return false;
		try {
			$res = $this->redis->hmget($key, $fieldArray);
		} catch (Exception $e) {
			try {
				$this->connect();
				$res = $this->redis->hmget($key, $fieldArray);
			} catch (Exception $ee) {
				return false;
			}
		}
		$res = (is_array($res) && $res) ? $res : [];
		foreach ($res as $k => $v) {
			$res[$k] = $this->_json_decode($v);
		}
		return $res;
	}

	function hgetall($key)
	{
		$key = trim(strval($key));
		if ($key === '') return false;
		try {
			$res = $this->redis->hgetall($key);
		} catch (Exception $e) {
			try {
				$this->connect();
				$res = $this->redis->hgetall($key);
			} catch (Exception $ee) {
				return false;
			}
		}
		$res = (is_array($res) && $res) ? $res : [];
		foreach ($res as $k => $v) {
			$res[$k] = $this->_json_decode($v);
		}
		return $res;
	}

	function hadd($key, $field)
	{
		return $this->hincrby($key, $field, 1);
	}

	function hdda($key, $field)
	{
		return $this->hincrby($key, $field, -1);
	}

	function hincrby($key, $field, $increment = 1)
	{
		$key = trim(strval($key));
		if (!$key || !$field) {
			return false;
		}
		$increment = $increment + 0;
		try {
			$res = $this->redis->hincrby($key, $field, $increment);
		} catch (Exception $e) {
			try {
				$res = $this->connect();
				$res = $res ? $this->redis->hincrby($key, $field, $increment) : false;
			} catch (Exception $ee) {
				return false;
			}
		}
		return $res;
	}

	//重设list:key里的某个index的值
	function lset($key, $index, $data)
	{
		$key = trim(strval($key));
		if (!$key || !$data || !is_int($index) || $index < 0) {
			return false;
		}
		$data = $this->_json_encode($data);
		try {
			$res = $this->redis->lset($key, $index, $data);
		} catch (Exception $e) {
			try {
				$this->connect();
				$res = $this->redis->lset($key, $index, $data);
			} catch (Exception $ee) {
				return false;
			}
		}
		return $res;
	}

	//追加list:key新值data，返回index
	function ladd($key, $data)
	{
		$key = trim(strval($key));
		if (!$key || !$data) {
			return -1;
		}
		$data = $this->_json_encode($data);
		try {
			$res = $this->redis->rpush($key, $data);
		} catch (Exception $e) {
			try {
				$res = $this->connect();
				$res = $res ? $this->redis->rpush($key, $data) : 0;
			} catch (Exception $ee) {
				return -1;
			}
		}
		return $res - 1;
	}

	//前加list:key新值data，返回length
	function ldda($key, $data)
	{
		$key = trim(strval($key));
		if (!$key || !$data) {
			return false;
		}
		$data = $this->_json_encode($data);
		try {
			$res = $this->redis->lpush($key, $data);
		} catch (Exception $e) {
			try {
				$res = $this->connect();
				$res = $res ? $this->redis->lpush($key, $data) : 0;
			} catch (Exception $ee) {
				return false;
			}
		}
		return $res;
	}

	//获取list:key的索引为index的值
	function lget($key, $index)
	{
		$key = trim(strval($key));
		if (!$key || !is_int($index) || $index < 0) {
			return false;
		}
		try {
			return $this->_json_decode($this->redis->lget($key, $index));
		} catch (Exception $e) {
			try {
				$this->connect();
				return $this->_json_decode($this->redis->lget($key, $index));
			} catch (Exception $ee) {
				return false;
			}
		}
	}

	//获取list:key的start到end范围内的元素值，并作为一个数组返回
	function labc($key, $start, $end)
	{
		return $this->lrange($key, $start, $end);
	}

	//获取list:key的start到end范围内的元素值，并作为一个数组返回
	function lrange($key, $start, $end)
	{
		$key = trim(strval($key));
		if (!$key || !is_int($start) || !is_int($end)) {
			return false;
		}
		try {
			$res = $this->redis->lrange($key, $start, $end);
		} catch (Exception $e) {
			try {
				$this->connect();
				$res = $this->redis->lrange($key, $start, $end);
			} catch (Exception $ee) {
				return false;
			}
		}
		$res = is_array($res) ? $res : [];
		foreach ($res as $k => $v) {
			$res[$k] = $this->_json_decode($v);
		}
		return $res;
	}

	//获取list:key的start到end范围内的元素值作为一个数组返回，并在库中剪切掉其余的元素
	function lcut($key, $start, $end)
	{
		$key = trim(strval($key));
		if (!$key || !is_int($start) || !is_int($end)) {
			return false;
		}
		try {
			$res = $this->redis->listTrim($key, $start, $end);
		} catch (Exception $e) {
			try {
				$this->connect();
				$res = $this->redis->listTrim($key, $start, $end);
			} catch (Exception $ee) {
				return false;
			}
		}
		$res = is_array($res) ? $res : [];
		foreach ($res as $k => $v) {
			$res[$k] = $this->_json_decode($v);
		}
		return $res;
	}

	//获取list:key的头元素值，并在库中移除
	function ltop($key)
	{
		return $this->lpop($key);
	}

	//获取list:key的头元素值，并在库中移除
	function lpop($key, $isthrow = false)
	{
		$key = trim(strval($key));
		if (!$key) return false;
		try {
			$res = $this->redis->lpop($key);
		} catch (Exception $e) {
			try {
				$this->connect($isthrow);
				$res = $this->redis->lpop($key);
			} catch (Exception $ee) {
				if ($isthrow) throw new Exception("Redis lpop failed.");
				return false;
			}
		}
		// //测试抛出异常
		// if ( !array_rand(range(0, 10)) ) {
		// 	throw new Exception("Redis lpop failed.");
		// }
		return $this->_json_decode($res);
	}

	//获取list:key的尾元素值，并在库中移除
	function lbot($key)
	{
		return $this->rpop($key);
	}

	//获取list:key的尾元素值，并在库中移除
	function rpop($key)
	{
		$key = trim(strval($key));
		if (!$key) {
			return false;
		}
		try {
			$res = $this->redis->rpop($key);
		} catch (Exception $e) {
			try {
				$this->connect();
				$res = $this->redis->rpop($key);
			} catch (Exception $ee) {
				return false;
			}
		}
		return $this->_json_decode($res);
	}

	//移除list:key里的某些与value相同的所有值，并返回移除的元素的个数
	function ldel($key, $value)
	{
		return $this->lrem($key, $value);
	}

	//移除list:key里的某些与value相同的值,受$count的正负大小影响,并返回移除的元素的个数
	function lrem($key, $value, $count = 0)
	{
		$key = trim(strval($key));
		if (!$key) {
			return false;
		}
		try {
			$res = $this->redis->lrem($key, $value, $count);
		} catch (Exception $e) {
			try {
				$this->connect();
				$res = $this->redis->lrem($key, $value, $count);
			} catch (Exception $ee) {
				return false;
			}
		}
		return intval($res);
	}

	//获取list:key的长度
	function llen($key)
	{
		$key = trim(strval($key));
		if (!$key) {
			return false;
		}
		try {
			return $this->redis->llen($key);
		} catch (Exception $e) {
			try {
				$this->connect();
				return $this->redis->llen($key);
			} catch (Exception $ee) {
				return false;
			}
		}
	}

	//删除list:key
	function lnot($key)
	{
		return $this->del($key);
	}


	//SortedSet（有序集合）


	//添加元素到有序集合，返回成功与否
	function zset($key, $score, $member)
	{
		return $this->zadd($key, $score, $member);
	}

	function zadd($key, $score, $member)
	{
		$key = trim(strval($key));
		$score = $score + 0;
		$member = trim($member);
		if (empty($key) || empty($member)) return false;
		try {
			$res = $this->redis->zAdd($key, $score, $member);
		} catch (Exception $e) {
			try {
				$this->connect();
				$res = $this->redis->zAdd($key, $score, $member);
			} catch (Exception $ee) {
				return false;
			}
		}
		return true;
	}


	//获取有序集合的元素个数，并返回
	function zlen($key)
	{
		return $this->zsize($key);
	}

	function zcard($key)
	{
		return $this->zsize($key);
	}

	function zsize($key)
	{
		$key = trim(strval($key));
		if (empty($key)) return false;
		try {
			$res = $this->redis->zCard($key);
		} catch (Exception $e) {
			try {
				$this->connect();
				$res = $this->redis->zCard($key);
			} catch (Exception $ee) {
				return false;
			}
		}
		return $res;
	}


	//计算符合分值范围的元素个数，并返回
	function zcount($key, $min = "-inf", $max = "+inf")
	{
		$key = trim(strval($key));
		$min = $min == "-inf" ? $min : ($min + 0);
		$max = $max == "+inf" ? $max : ($max + 0);
		if (empty($key)) return false;
		try {
			$res = $this->redis->zCount($key, $min, $max);
		} catch (Exception $e) {
			try {
				$this->connect();
				$res = $this->redis->zCount($key, $min, $max);
			} catch (Exception $ee) {
				return false;
			}
		}
		return $res;
	}


	//增加有序集合某元素的值，返回处理后的值
	function zincr($key, $scoreadd, $member)
	{
		return $this->zincrby($key, $scoreadd, $member);
	}

	function zincrby($key, $scoreadd, $member)
	{
		$key = trim(strval($key));
		$scoreadd = $scoreadd + 0;
		$member = trim($member);
		if (empty($key) || empty($member)) return false;
		try {
			$res = $this->redis->zIncrBy($key, $scoreadd, $member);
		} catch (Exception $e) {
			try {
				$this->connect();
				$res = $this->redis->zIncrBy($key, $scoreadd, $member);
			} catch (Exception $ee) {
				return false;
			}
		}
		return $res;
	}


	//返回有序集合里的某个元素的当前分值
	function zscore($key, $member)
	{
		$key = trim(strval($key));
		$member = trim(strval($member));
		if (empty($key) || empty($member)) return false;
		try {
			$res = $this->redis->zScore($key, $member);
		} catch (Exception $e) {
			try {
				$this->connect();
				$res = $this->redis->zScore($key, $member);
			} catch (Exception $ee) {
				return false;
			}
		}
		return $res;
	}


	//返回有序集合里的某个元素的正序排名
	function zrank($key, $member)
	{
		$key = trim(strval($key));
		$member = trim(strval($member));
		if (empty($key) || empty($member)) return false;
		try {
			$res = $this->redis->zRank($key, $member);
		} catch (Exception $e) {
			try {
				$this->connect();
				$res = $this->redis->zRank($key, $member);
			} catch (Exception $ee) {
				return false;
			}
		}
		return $res;
	}


	//返回有序集合里的某个元素的倒序排名
	function zrevrank($key, $member)
	{
		$key = trim(strval($key));
		$member = trim(strval($member));
		if (empty($key) || empty($member)) return false;
		try {
			$res = $this->redis->zRevRank($key, $member);
		} catch (Exception $e) {
			try {
				$this->connect();
				$res = $this->redis->zRevRank($key, $member);
			} catch (Exception $ee) {
				return false;
			}
		}
		return $res;
	}


	//按规则计算两个有序集合的交集并返回新集合的元素个数
	function zinter($newkey, $keys, $com = [], $agg = "SUM")
	{
		$new = trim(strval($newkey));
		$len = count($keys);
		if (empty($new) || $len < 2) return false;
		$com = ($com && is_array($com) && count($com) == $len) ? $com : array_pad([1], $len, 1);
		foreach ($com as $k => $v) {
			$com[$k] = $v + 0;
		}
		$com = array_values($com);
		$agg = in_array($agg, ["SUM", "MIN", "MAX"]) ? $agg : "SUM";
		try {
			$res = $this->redis->zInter($new, $keys, $com, $agg);
		} catch (Exception $e) {
			try {
				$this->connect();
				$res = $this->redis->zInter($new, $keys, $com, $agg);
			} catch (Exception $ee) {
				return false;
			}
		}
		return $res;
	}


	//按规则计算两个有序集合的并集并返回新集合的元素个数
	function zunion($newkey, $keys, $com = [], $agg = "SUM")
	{
		$new = trim(strval($newkey));
		$len = count($keys);
		if (empty($new) || $len < 2) return false;
		$com = ($com && is_array($com) && count($com) == $len) ? $com : array_pad([1], $len, 1);
		foreach ($com as $k => $v) {
			$com[$k] = $v + 0;
		}
		$com = array_values($com);
		$agg = in_array($agg, ["SUM", "MIN", "MAX"]) ? $agg : "SUM";
		try {
			$res = $this->redis->zUnion($new, $keys, $com, $agg);
		} catch (Exception $e) {
			try {
				$this->connect();
				$res = $this->redis->zUnion($new, $keys, $com, $agg);
			} catch (Exception $ee) {
				return false;
			}
		}
		return $res;
	}


	//有序集合里的元素正序排列，返回指定排名下标范围内的(带有分值的)元素数组
	function zrange($key, $start = 0, $end = -1, $is_withscores = true)
	{
		$key = trim(strval($key));
		$start = intval($start);
		$end = intval($end);
		$is_ = !!($is_withscores);
		if (empty($key)) return false;
		try {
			$res = $this->redis->zRange($key, $start, $end, $is_);
		} catch (Exception $e) {
			try {
				$this->connect();
				$res = $this->redis->zRange($key, $start, $end, $is_);
			} catch (Exception $ee) {
				return false;
			}
		}
		return $res;
	}


	//有序集合里的元素倒序排列，返回指定排名下标范围内的(带有分值的)元素数组
	function zlist($key, $len = 10)
	{
		return $this->zrevrange($key, 0, $len - 1);
	}

	function zrevrange($key, $start = 0, $end = -1, $is_withscores = true)
	{
		$key = trim(strval($key));
		$start = intval($start);
		$end = intval($end);
		$is_ = !!($is_withscores);
		if (empty($key)) return false;
		try {
			$res = $this->redis->zRevRange($key, $start, $end, $is_);
		} catch (Exception $e) {
			try {
				$this->connect();
				$res = $this->redis->zRevRange($key, $start, $end, $is_);
			} catch (Exception $ee) {
				return false;
			}
		}
		return $res;
	}


	//有序集合里的元素正序排列，返回指定分值范围内的(带有分值的)元素数组
	function zrangebyscore($key, $min = "-inf", $max = "+inf", $options = [])
	{
		$key = trim(strval($key));
		$min = $min == "-inf" ? $min : ($min + 0);
		$max = $max == "+inf" ? $max : ($max + 0);
		if (empty($key)) return false;
		$opt = ['withscores' => true, 'limit' => [0, 10]];
		if ($options && is_array($options)) {
			$options['withscores'] = isset($options['withscores']) ? !!($options['withscores']) : true;
			$options['limit'] = (isset($options['limit']) && is_array($options['limit']) && count($options['limit']) == 2) ? $options['limit'] : [0, 10];
		} else {
			$options = $opt;
		}
		try {
			$res = $this->redis->zRangeByScore($key, $min, $max, $options);
		} catch (Exception $e) {
			try {
				$this->connect();
				$res = $this->redis->zRangeByScore($key, $min, $max, $options);
			} catch (Exception $ee) {
				return false;
			}
		}
		return $res;
	}


	//有序集合里的元素倒序排列，返回指定分值范围内的(带有分值的)元素数组
	function zrevrangebyscore($key, $min = "-inf", $max = "+inf", $options = [])
	{
		$key = trim(strval($key));
		$min = $min == "-inf" ? $min : ($min + 0);
		$max = $max == "+inf" ? $max : ($max + 0);
		if (empty($key)) return false;
		$opt = ['withscores' => true, 'limit' => [0, 10]];
		if ($options && is_array($options)) {
			$options['withscores'] = isset($options['withscores']) ? !!($options['withscores']) : true;
			$options['limit'] = (isset($options['limit']) && is_array($options['limit']) && count($options['limit']) == 2) ? $options['limit'] : [0, 10];
		} else {
			$options = $opt;
		}
		try {
			$res = $this->redis->zRevRangeByScore($key, $min, $max, $options);
		} catch (Exception $e) {
			try {
				$this->connect();
				$res = $this->redis->zRevRangeByScore($key, $min, $max, $options);
			} catch (Exception $ee) {
				return false;
			}
		}
		return $res;
	}


	//删除有序集合里的某个元素，并返回删除的数量[0/1]
	function zdel($key, $member)
	{
		return $this->zrem($key, $member);
	}

	function zdelete($key, $member)
	{
		return $this->zrem($key, $member);
	}

	function zrem($key, $member)
	{
		$key = trim(strval($key));
		$member = trim(strval($member));
		if (empty($key) || empty($member)) return false;
		try {
			$res = $this->redis->zRem($key, $member);
		} catch (Exception $e) {
			try {
				$this->connect();
				$res = $this->redis->zRem($key, $member);
			} catch (Exception $ee) {
				return false;
			}
		}
		return $res;
	}


	//删除有序集合里的指定排名范围段内的元素，并返回被删除的数量
	function zdelrank($key, $start, $end)
	{
		return $this->zremrangebyrank($key, $start, $end);
	}

	function zdeleterangebyrank($key, $start, $end)
	{
		return $this->zremrangebyrank($key, $start, $end);
	}

	function zremrangebyrank($key, $start, $end)
	{
		$key = trim(strval($key));
		$start = intval($start);
		$end = intval($end);
		if (empty($key)) return false;
		try {
			$res = $this->redis->zRemRangebyRank($key, $start, $end);
		} catch (Exception $e) {
			try {
				$this->connect();
				$res = $this->redis->zRemRangebyRank($key, $start, $end);
			} catch (Exception $ee) {
				return false;
			}
		}
		return $res;
	}


	//删除有序集合里的指定分值范围段内的元素，并返回被删除的数量
	function zdelscore($key, $min = "-inf", $max = "+inf")
	{
		return $this->zremrangebyscore($key, $min, $max);
	}

	function zdeleterangebyscore($key, $min = "-inf", $max = "+inf")
	{
		return $this->zremrangebyscore($key, $min, $max);
	}

	function zremrangebyscore($key, $min = "-inf", $max = "+inf")
	{
		$key = trim(strval($key));
		$min = $min == "-inf" ? $min : ($min + 0);
		$max = $max == "+inf" ? $max : ($max + 0);
		if (empty($key)) return false;
		try {
			$res = $this->redis->zRemRangebyRank($key, $start, $end);
		} catch (Exception $e) {
			try {
				$this->connect();
				$res = $this->redis->zRemRangebyRank($key, $start, $end);
			} catch (Exception $ee) {
				return false;
			}
		}
		return $res;
	}


	function info()
	{
		try {
			return $this->redis->info();
		} catch (Exception $e) {
			try {
				$this->connect();
				return $this->redis->info();
			} catch (Exception $ee) {
				return false;
			}
		}
	}

	function flushDb()
	{
		try {
			return $this->redis->flushDb();
		} catch (Exception $e) {
			try {
				$this->connect();
				return $this->redis->flushDb();
			} catch (Exception $ee) {
				return false;
			}
		}
	}

	function clean()
	{
		return $this->clean();
	}

	function ping()
	{
		try {
			$r = $this->redis->ping();
			if ($r == "+PONG")
				return true;
			else
				return false;
		} catch (Exception $e) {
			return false;
		}
	}

	function close()
	{
		try {
			$r = $this->redis->close();
			return $r;
		} catch (Exception $e) {
			return false;
		}
	}

	function _json_encode($var)
	{
		return json_encode($var);
	}

	function _json_decode($var)
	{
		try {
			return json_decode($var, 1);
		} catch (Exception $e) {
			return false;
		}
	}

	function expire($key, $time)
	{
		return $this->redis->expire($key, $time);
	}

	function incrByFloat($key, $val)
	{
		return $this->redis->incrByFloat($key,$val);
	}

}
