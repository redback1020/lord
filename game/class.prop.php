<?php

// 实物 	ID原子性、字段原子性、名字可编辑、图片可配置file_path:award
// 	苹果6S 		iphone6s 		1
// 	100元话费 	mobifee_100 	2
// 货币 	ID原子性、字段原子性、系统性、前后端一致性、图片唯一性file_path:money
// 	人民币 		cny		1
// 	乐币 		gold	2
// 	代币 		golds	3
// 	乐豆 		coins	4
// 	乐券 		coupon	5
// 	抽奖数 		lottery	6
// 道具	ID原子性、系统性、前后端一致性、分类功效性、图片唯一性file_path:prop
// 		id 		ID,
//		cd 		分类ID,
//		cate 	分类名称,
//		name 	名称,
//		resume 	简述,
//		fileId 	图片(0无图),
//		sex 	性别(0通用|1男|2女),
//		showin 	物品显示(0都显示|1不在背包||),
// 		overlay	叠加方式(0不可叠加|1叠加数量|2叠加时效|3叠加持久),等同于损耗类型
//		mutex 	使用时同类互斥(0不会|1互斥),
// 		useby 	使用方式(0拥有即用|1缺失使用|2手动使用),
//		usedo 	使用损耗(0不会损耗|1降低数量|2降低时效|3降低持久),
// 		useup 	使用完毕(0销毁|1不处理|2状态2|3状态3|4状态4||),
// 	服装类 	高手套装 大师套装 富翁套装
// 	功能类 	记牌器
// 	数据类 	经验卡 乐豆卡
// 	效果类 	经验加成卡
// 物品 	ID原子性、继承并覆盖道具信息、属性个性化、图片配置性file_path:item
// 		id 		ID,
//		cd 		分类ID,
//		pd 		道具ID,
//		name 	名称,
//		resume 	简述,
//		fileId 	图片(0无图),
//		number 	叠加数量上限(0不限),
//		second 	物品时效数值(0不限),
//		points 	物品持久数值(0不限),
//		present	数量可否赠送(0不可|1可以),
//		pause 	时效可否暂停(0不可|1可以),
//		repair 	持久可否修复(0不可|1可以),
//		useas 	使用用途(0无|1改变状态|2增加乐币|3增加代币|4增加乐豆|5增加乐券|6增加抽奖数|8增加物品ID|9增加实物ID),
//		useto 	使用效值(0),
// 	用户物品 ID原子性、继承自物品信息、属性个性化、
//		id 		ID,
//		dateid 	日期ID,
//		uid 	用户ID,
//		fruid 	来源用户ID,
//		cd 		分类ID,
//		pd 		道具ID,
//		itemId 	物品ID,
//		name 	名称,
//		num 	剩余数量,
//		start 	开始时间,
//		end 	截止时间,
//		sec 	剩余秒数,
//		poi 	剩余持久,
//		state 	状态(0拥有|1在用|2用完|3坏掉|4待毁|5？),
// 	服装类 	高手套装(7天) 高手套装(1月)
// 	功能类 	经验卡 初级托管机器人 高级托管机器人
// 	数据类 	经验卡(1万) 经验卡(10万)
// 	效果类 	经验翻倍卡 经验四倍卡
// 	碎片类 	可合成某个物品
// 礼包 	基于物品和货币、无消耗、获得(物品|货币|实物)、图片可无、图片可配置file_path:gift
// 	固定类	有固定呈现、可创建(打包)、可解包到(到道具|货币)并识别是否自动使用、可系统赠送、可用户转增
// 		阿里礼包 阿里大礼包
// 	动态类	无固定呈现、直接配置为数据包、成为邮件、直接使用为(道具|货币)、图片可无、
// 		任务奖励礼包 活动奖励配置
// 商品 	基于物品和货币、消耗(人民币|货币)、获得(物品|货币|实物)、图片可配置file_path:goods
// 	充值类 	人民币-乐币 人民币-乐豆 人民币-？？服装 人民币-记牌器
// 	兑换类 	乐币-乐豆 乐币-？？服装 乐券-话费

class prop
{
	private $obj = null;
	private static $inst = null;
	public $mysql = null;
	public $redis = null;
	public $shm = null;
	public $errors = null;
	public $errno = 0;
	public $error = "";
	public $func = null;
	public $ud = 0;
	public $mines = array();
	public $award = null;
	public $money = null;
	public $prop = null;
	public $item = null;
	public $gift = null;
	public $goods = null;

	//私有构造
	private function __construct( $redis=null, $mysql=null )
	{
		$this->redis = $redis;
		$this->mysql = $mysql;
		if ( $this->redis === null ) $this->redis = $this->getRedis();
		if ( $this->mysql === null ) $this->mysql = $this->getMysql();
		$this->errors = array(
			'getAward' => array(
				'1' => "F=%s 实物奖励不存在，ID=%d",
			),
			'getGoods' => array(
				'1' => "F=%s 商品数据不存在，ID=%d, CHANNEL=%s",
			),
			'getlist' => array(
				'1' => "F=%s 错误描述？",
				'2' => "F=%s 错误描述？",
			),
			'getItem' => array(
				'1' => "F=%s 物品数据不存在，ID=%d",
			),
			'buyGoods' => array(
				'1' => "F=%s 操作无效。",
				'2' => "F=%s 商品不存在，ID=%d CHANNEL=%s",
				'3' => "F=%s 商品不可购买，ID=%d",
				'4' => "F=%s 不可重复购买，ID=%d",
				'5' => "F=%s 余额不足，ID=%d",
				'6' => "F=%s 已经下架，ID=%d",
				'7' => "F=%s 已经无货，ID=%d",
			),
			'setMine' => array(
				'1' => "F=%s 插入数据错误Q=%s",
				'2' => "F=%s 获取新ID=%s 错误%s",
				'3' => "F=%s 查询数据错误Q=%s",
			),
			'obtain' => array(
				'1' => "F=%s 物品ID=%s 配置或读取错误",
			),
			'create' => array(
				'1' => "F=%s 物品ID=%s 不存在。",
				'2' => "F=%s 用户的物品ID=%s 不存在",
				'3' => "F=%s 物品ID=%s 配置或读取错误",
			),
			'useuse' => array(
				'1' => "F=%s 没有找到用户的物品数据。",
				'2' => "F=%s 用户的物品ID=%s 不存在",
				'3' => "F=%s 物品ID=%s 配置或读取错误",
				'4' => "F=%s 性别不对不可使用。",
				'5' => "F=%s 数量已用完。",
				'6' => "F=%s 有效期已过。",
				'7' => "F=%s 持久已用完。",
			),
			'shift' => array(
				'1' => "F=%s 没有找到用户的物品数据。",
				'2' => "F=%s 用户的物品ID=%s 不存在isPd=%d。",
				'3' => "F=%s 物品ID=%s 配置或读取错误isPd=%d。",
				'4' => "F=%s 性别不对不可使用。",
				'5' => "F=%s 数量已用完。",
				'6' => "F=%s 有效期已过。",
				'7' => "F=%s 持久已用完。",
			),
			'???' => array(
				'1' => "F=%s 错误描述？",
				'2' => "F=%s 错误描述？",
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
		//
	}

	//覆盖
	private function __clone()
	{
		return serr("CLASS:".__CLASS__."禁止克隆");
	}

	//获取单例
	public static function inst( $redis=null, $mysql=null )
	{
		if( !(self::$inst instanceof self) ) self::$inst = new prop($redis, $mysql);
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

	//获取错误信息
	//return 		array('errno'=>1,'error'=>'错误信息','func'=>'setMine');
	public function getError()
	{
		if ( !$this->errno || !$this->error ) return array('errno'=>0, 'error'=>'没有错误。');
		return array('errno'=>$this->errno, 'error'=>$this->error);
	}

	//设置错误编号
	//errno 		错误编号
	//param?		附加参数
	//return 		false //可以直接使用“return $this->setError(__FUNCTION__, ?);”来结束运算并返回false，调用方通过“$prop->getError()”可以获取到错误信息数组
	public function setError( $func, $errno, $param1='', $param2='', $param3='' )
	{
		if ( !$func || !$errno ) return false;
		if ( !isset($this->errors[$func][$errno]) ) serr("报错配置无效 class=prop func=$func errno=$errno param1=$param1 param2=$param2 param3=$param3");
		$this->errno = $errno;
		$this->error = sprintf($this->errors[$func][$errno], $func, $param1, $param2, $param3);
		return false;
	}


	//获取实物列表
	//return 		list 	实物列表 array(id=>array(id=>1,name=>'sf',...),...)
	public function getlistAward( $channel='' )
	{
		if ( $this->award !== null ) {
			$award = $this->award;
		} else {
			$award = $this->refreshAward();
		}
		if ( $channel ) {
			foreach ( $award as $id => $v )
			{
				if ( $v['channel'] && !in_array($channel, $v['channel']) ) unset($award[$id]);
			}
		}
		return $award;
	}


	//刷新实物列表
	//return 		list 	实物列表 array(id=>array(id=>1,name=>'sf',...),...)
	public function refreshAward()
	{
		//临时代码 不可实际使用
		$this->award = $award = array(
			1 => array('id'=>1, 'key'=>'iphone6s', 'name'=>'iPhone 6s', 'value'=>0, 'fileId'=>1),
			2 => array('id'=>2, 'key'=>'mobifee', 'name'=>'10元手机话费', 'value'=>0, 'fileId'=>2),
			3 => array('id'=>3, 'key'=>'mobifee', 'name'=>'30元手机话费', 'value'=>0, 'fileId'=>3),
			4 => array('id'=>4, 'key'=>'mobifee', 'name'=>'50元手机话费', 'value'=>0, 'fileId'=>4),
			5 => array('id'=>5, 'key'=>'mobifee', 'name'=>'100元手机话费', 'value'=>0, 'fileId'=>5),
		);
		return $award;
		//
		$award = array();
		$sql = "SELECT * FROM `lord_list_award` WHERE `is_del` = 0";
		$res = $this->mysql->getData($sql);
		if ( !$res ) $res = array();
		foreach ( $res as $k => $v )
		{
			if ( $v['channel'] ) $v['channel'] = explode(" ", $v['channel']);
			$award[$v['id']] = $v;
		}
		$this->award = $award;
		return $award;
	}


	//获取某个实物
	//return 		arr 	实物信息 array(id=>1,name=>'sf',...)
	public function getAward( $id )
	{
		$award = $this->getlistAward();
		if ( !isset($award[$id]) ) return $this->setError(__FUNCTION__, 1, $id);
		return $award[$id];
	}


	//获取货币列表
	//return 		list 	货币列表 array(id=>array(id=>1,key=>'sf',...),...)
	public function getlistMoney()
	{
		if ( $this->money !== null ) {
			$money = $this->money;
		} else {
			$money = $this->money = array(
				// 1 => array('id'=>1, 'key'=>'gold', 'name'=>'乐币'),
				// 2 => array('id'=>2, 'key'=>'golds', 'name'=>'乐钻'),
				3 => array('id'=>3, 'key'=>'coins',   'name'=>'乐豆'),
				4 => array('id'=>4, 'key'=>'coupon',  'name'=>'乐券'),
				5 => array('id'=>5, 'key'=>'lottery', 'name'=>'抽奖数'),
			);
		}
		return $money;
	}


	//获取道具列表
	//return 		list 	道具列表 array(id=>array(id=>1,name=>'sf',...),...)
	public function getlistProp( $cd=0 )
	{
		if ( $this->prop !== null ) {
			$prop = $this->prop;
		} else {
			$prop = $this->refreshProp();
		}
		if ( $prop === false ) return false;
		if ( $cd ) {
			foreach ( $prop as $k => $v )
			{
				if ( $cd != $v['cd'] ) unset($prop[$k]);
			}
		}
		return $prop;
	}


	//刷新道具列表
	//return 		list 	道具列表 array(id=>array(id=>1,name=>'sf',...),...)
	public function refreshProp()
	{
		//id					int			道具ID (=pd)
		//cd 					int			分类ID (=categoryId)
		//cate				str			分类名称 (=category)
		//name				str			道具名称
		//resume			str			道具简介 目前无用
		//fileId			int			道具图片 目前无用
		//sex					int			性别区分 0通用 1男 2女
		//showin			int			显示区分 0不限 1只在背包
		//overlay			int			物品叠加 0不可叠加 1叠加数量 2叠加时效 3叠加持久
		//mutex				int			互斥效果 0不会互斥 1同类互斥
		//useby				int			使用方式 0拥有即用 1缺失即用 2手动使用
		//usedo				int			使用操作 0不做处理 1降低数量 2降低时效 3降低持久
		//useup				int			使用完毕 0立即销毁 1不做处理 >1状态改为
		//create_time	int			创建时间
		//udpate_time	int			修改时间
		$prop = array();
		$sql = "SELECT * FROM `lord_list_prop` ORDER BY `cd`,`id`";
		$res = $this->mysql->getData($sql);
		if ( !$res ) $res = array();
		foreach ( $res as $k => $v )
		{
			$prop[$v['id']] = $v;
		}
		$this->prop = $prop;
		return $prop;
	}


	//获取物品列表
	//return 		list 	物品列表 array(id=>array(id=>1,name=>'sf',...),...)
	public function getlistItem( $cd=0, $pd=0, $info=0 )
	{
		if ( $this->item !== null ) {
			$item = $this->item;
		} else {
			$item = $this->refreshItem();
		}
		if ( $item === false ) return false;
		if ( $info ) {
			$prop = $this->getlistProp($cd);
			if ( $prop === false ) return false;
		}
		if ( $cd || $pd || $info ) {
			foreach ( $item as $id => $v )
			{
				if ( $cd && $cd != $v['cd'] ) unset($item[$id]);
				elseif ( $pd && $pd != $v['pd'] ) unset($item[$id]);
				elseif ( $info && isset($prop[$v['pd']]) ) $item[$id] = array_merge($prop[$v['pd']], $v);
				elseif ( $info ) unset($item[$id]);
			}
		}
		return $item;
	}


	//刷新物品列表
	//return 		list 	物品列表 array(id=>array(id=>1,name=>'sf',...),...)
	public function refreshItem()
	{
		$sql = "SELECT * FROM `lord_list_item` WHERE `state` < 2 ORDER BY `sort`";
		$res = $this->mysql->getData($sql);
		if ( ! $res ) return array();
		foreach ( $res as $k => $v )
		{
			$item[$v['id']] = $v;
		}
		$this->item = $item;
		return $item;
	}


	//获取某个物品
	//id 				int 	物品ID
	//return 		arr 	物品数据 array(id=>1,name=>'sdf',...)含prop数据
	public function getItem( $id )
	{
		$items = $this->getlistItem(0, 0, 1);
		if ( $items === false ) return false;
		if ( !isset( $items[$id]) ) return $this->setError(__FUNCTION__, 1, $id);
		return $items[$id];
	}


	//获取礼包列表
	//channel 		str 	渠道限制，为空则不限制
	//type 			str 	类别限制，为空则不限制
	//return 		list 	礼包列表 array(id=>array(id=>1,name=>'sf',...),...)
	public function getlistGift( $channel='', $type='' )
	{
		if ( $this->gift !== null ) {
			$gift = $this->gift;
		} else {
			$gift = $this->refreshGift();
		}
		if ( $channel || $type ) {
			foreach ( $gift as $k => $v )
			{
				if ( $channel && $v['channel'] && !in_array($channel, $v['channel']) ) unset($gift[$k]);
				elseif ( $type && $type != $v['type'] ) unset($gift[$k]);
			}
		}
		return $gift;
	}


	//刷新礼包列表
	//return 		list 	礼包列表 array(id=>array(id=>1,name=>'sf',...),...)
	public function refreshGift()
	{
		$gift = array();
		$sql = "SELECT * FROM `lord_list_gift` WHERE `state` < 2 ORDER BY `type`, `sort`";
		$res = $this->mysql->getData($sql);
		if ( !$res ) $res = array();
		foreach ( $res as $k => $v )
		{
			if ( $v['channel'] ) $v['channel'] = explode(" ", $v['channel']);
			$gift[$v['id']] = $v;
		}
		$this->gift = $gift;
		return $gift;
	}


	//获取商品列表
	//channel		str		渠道限制，为空则不限制
	//cd				str		类别限制，为空则不限制
	//info			int		是否附加物品道具信息 0否 1是
	//return 		list	商品列表 array(id=>array(id=>1,name=>'sf',...),...)
	public function getlistGoods( $channel='', $cd=0, $info=0 )
	{
		if ( $this->goods !== null ) {
			$goods = $this->goods;
		} else {
			$goods = $this->refreshGoods();
			if ( $goods === false ) return false;
		}
		if ( $info ) {
			$items = $this->getlistItem(0, 0, 1);
			if ( $items === false ) return false;
		}
		$stores = $this->redis->hgetall('lord_goods_store');
		if ( $channel || $cd ) {
			foreach ( $goods as $k => $v )
			{
				if ( $channel && $v['channot'] && in_array($channel, $v['channot']) ) {
					unset($goods[$k]); continue;
				} elseif ( $channel && $v['channel'] && !in_array($channel, $v['channel']) ) {
					unset($goods[$k]); continue;
				} elseif ( $cd && $cd != $v['cd'] ) {
					unset($goods[$k]); continue;
				} elseif ( isset($stores[$v['id']]) ) {
					$goods[$k]['store'] = $v['store'] = $stores[$v['id']];
				}
				if ( !$info ) continue;
				$iid = isset($goods[$k]['iid']) && $goods[$k]['iid'] ? $goods[$k]['iid'] : 0;
				$goods[$k]['iid'] = $goods[$k]['icd'] = $goods[$k]['ipd'] = 0;
				if ( !$v['buyto'] ) continue;
				foreach ( $v['buyto'] as $kk => $vv )
				{
					if ( $kk != 'items' ) continue;
					foreach ( $vv as $kkk => $vvv ) {
						if ( isset($items[$vvv['id']]) ) {
							$goods[$k]['iid'] = $goods[$k]['buyto']['items'][$kkk]['iid'] = $items[$vvv['id']]['id'];
							$goods[$k]['icd'] = $goods[$k]['buyto']['items'][$kkk]['icd'] = $items[$vvv['id']]['cd'];
							$goods[$k]['ipd'] = $goods[$k]['buyto']['items'][$kkk]['ipd'] = $items[$vvv['id']]['pd'];
						}
					}
					if ( count($goods[$k]['buyto']['items']) > 1 ) {//超过一个物品的混合商品不做物品标记关联
						$goods[$k]['iid'] = $goods[$k]['icd'] = $goods[$k]['ipd'] = 0;
					}
				}
				if ( $iid ) {//扩展字段: iid 商品的首选物品，即使混合商品也可以有首选物品
					$goods[$k]['iid'] = $items[$iid]['id'];
					$goods[$k]['icd'] = $items[$iid]['cd'];
					$goods[$k]['ipd'] = $items[$iid]['pd'];
				}
			}
		}
		return $goods;
	}


	//刷新商品列表
	//return 		list 	商品列表 array(id=>array(id=>1,name=>'sf',...),...)
	public function refreshGoods()
	{
		$goods = array();
		$sql = "SELECT * FROM `lord_list_goods` WHERE `state` = 0 ORDER BY `cd`, `sort`";
		$res = $this->mysql->getData($sql);
		if ( !$res ) $res = array();
		$stores = array();
		foreach ( $res as $k => $v )
		{
			if ( $v['channel'] ) $v['channel'] = explode(" ", $v['channel']);
			if ( $v['channot'] ) $v['channot'] = explode(" ", $v['channot']);
			if ( $v['buyto'] ) $v['buyto'] = json_decode($v['buyto'], 1);
			$goods[$v['id']] = $v;
			$stores[$v['id']] = $v['store'];
		}
		if ( $stores ) $this->redis->hmset('lord_goods_store', $stores);
		$this->goods = $goods;
		return $goods;
	}


	//获取某个商品
	//gd 			int 	商品ID
	//channel 		str 	专属渠道
	//return 		arr 	商品信息 array(id=>1,name=>'sf',...)
	public function getGoods( $gd, $channel='', $info=0 )
	{
		$goods = $this->getlistGoods($channel, 0, $info);//是否包含iid icd ipd
		if ( !isset($goods[$gd]) ) return $this->setError(__FUNCTION__, 1, $gd, $channel);
		return $goods[$gd];
	}


	//更新商品库存
	//stores 		arr 	商品库存增减 array(gd=>add,...)
	//return 		bool 	true|false
	public function storeup( $stores )
	{
		if ( !$stores ) $stores = array();
		foreach ( $stores as $gd => $add )
		{
			$this->redis->hincrby('lord_goods_store', $gd, $add);
			$sql = "UPDATE `lord_list_goods` SET `store` = `store` + $add WHERE `id` = $gd";
			bobSql($sql);
		}
		return true;
	}


	//创建用户物品数据
	//ud 			int 	用户ID
	//modify 		list 	用户物品的新增或修改数据。为空时则拉取当前的用户物品列表
	//return 		list 	用户物品列表 array('2'=>array('pd'=>2,'name',...),...)//警告：不附加物品信息、不附加道具信息
	public function setMine( $ud, $modify=array() )
	{
		if ( false && $ud == $this->ud && $this->time + 3600 > time() ) {//预留 因进程的用户信息无法绑定因素 暂不考虑使用此种策略，待扩展
			$mines = $this->mine;
		} else {
			//预留 从redis读取
			$time = time();
			$mines = array();
			$sql = "SELECT * FROM `lord_user_item` WHERE `uid` = $ud";
			$res = $this->mysql->getData($sql);
			if ( !$res ) $res = array();
			$dels = $delMcard = array();
			foreach ( $res as $k => $v )
			{
				if ( $v['state'] > 1 || $v['num'] < 1 || ($v['end'] && $v['end'] < $time) ) {
					$dels[]= $v['id'];//if ( $v['useup'] == 0 )
					if ( $v['pd'] == 7 ) {
						$delMcard[] = $v['uid'];
					}
					//预留 按useup的规则处理
				} else {
					$mines[$v['id']] = $v;
				}
			}
			$dels && bobSql("DELETE FROM `lord_user_item` WHERE `id` IN (".join(",", $dels).")");
			if ( $delMcard ) {
				$delMcard = array_unique($delMcard);
				$msg = addslashes(json_encode(array('act'=>'USER_MESSAGE', 'cmd'=>4, 'code'=>228, 'isPush'=>1, 'errno'=>1, 'error'=>"亲，您的包月礼包已过期。\n是否续费，继续享用礼包特权！")));
				$sqlp = array();
				foreach ( $delMcard as $k => $uid )
				{
					$sqlp[]= "( $uid, '$msg' )";
				}
				$sql = "INSERT INTO `lord_user_message` ( `uid`, `msg` ) VALUES ".join(', ', $sqlp);
				$this->mysql->runSql($sql);
			}
			$mines['1'] = array('id'=>1, 'dateid'=>1, 'uid'=>$ud, 'fruid'=>0, 'cd'=>1, 'pd'=>1, 'itemId'=>1, 'name'=>'基础套装', 'num'=>1, 'start'=>1, 'end'=>0, 'sec'=>0, 'poi'=>1, 'state'=>0, 'create_time'=>1, 'update_time'=>1);
			$this->ud = $ud;
			$this->time = $time;
			$this->mine = $mines;
		}
		if ( !$modify ) return $mines;
		foreach ( $modify as $k => $v )
		{
			if ( $k == 1 ) continue;
			if ( ! $v ) {
				$sql = "DELETE FROM `lord_user_item` WHERE `id` = $k";
				$this->mysql->runSql($sql);
				unset($this->mine[$k]);
				unset($mines[$k]);
				continue;
			}
			if ( isset($mines[$k]) ) {
				if ( (isset($v['state']) && $v['state'] > 1) || (isset($v['num']) && $v['num'] < 1) || (isset($v['end']) && $v['end'] && $v['end'] < $time) ) {
					$sql = "DELETE FROM `lord_user_item` WHERE `id` = $k";
					$this->mysql->runSql($sql);
					unset($this->mine[$k]);
					unset($mines[$k]);
				} else {
					$sql = "UPDATE `lord_user_item` SET ";
					$sqlp = array();
					foreach ( $v as $kk => $vv )
					{
						$sqlp[]= "`$kk` = $vv";
					}
					$sql.= join(', ', $sqlp)." WHERE `id` = $k";
					// bobSql($sql);//预留 待扩展
					$this->mysql->runSql($sql);
					$this->mine[$k] = $mines[$k] = array_merge($mines[$k], $v);
				}
			} else {
				// $this->mine[$k] = $mines[$k] = $v;
				// continue;//预留 目前无效 临时伪造ID，不进行数据库查询，减少数据库压力
				if ( isset($v['id']) ) unset($v['id']);
				if ( !$v ) continue;
				$keys = array_keys($v);
				foreach ( $keys as $kk => $vv )
				{
					$keys[$kk] = "`$vv`";
				}
				$vals = array_values($v);
				foreach ( $vals as $kk => $vv )
				{
					$vals[$kk] = is_numeric($vv) ? $vv : ("'".$vv."'");
				}
				$sql = "INSERT INTO `lord_user_item` ( ".join(', ', $keys)." ) VALUES ( ".join(', ', $vals)." )";
				$res = $this->mysql->runSql($sql);
				if ( !$res ) return $this->setError(__FUNCTION__, 1, $sql);
				$id = $this->mysql->lastId();
				if ( !$id ) return $this->setError(__FUNCTION__, 2, $sql);
				$sql = "SELECT * FROM `lord_user_item` WHERE `id` = $id";
				$v = $this->mysql->getline($sql);
				if ( !$v ) return $this->setError(__FUNCTION__, 3, $sql);
				$this->mine[$id] = $mines[$id] = $v;
			}
		}
		return $mines;
	}


	//获取用户物品列表
	//ud 			int 	用户ID
	//cd 			int 	分类ID 0默认不限分类
	//info 			int 	明细类型 0用户道具及状态 1用户物品数据列表 2用户物品数据列表+附加道具原生数据
	//return 		list 	用户物品列表 array(md=>array(cd=>1,pd=>1,id=>1,name=>'',...),...)
	//return 		arr 	用户道具状态 array(id=>1,...)
	//return 		arr 	array()
	//return 		bool 	false操作失败
	public function getMine( $ud, $cd=0, $info=0 )
	{
		$mines = $this->setMine($ud);
		if ( $mines === false ) return false;
		if ( $cd < 2 || $info == 2 ) {
			$item = $this->getlistItem($cd, 0, 1);
			// if ( $cd < 2 ) $mines['1'] = $item['1'];//md=>item
		}
		$data = array();
		foreach ( $mines as $md => $v )
		{
			if ( $cd && $cd != $v['cd'] ) continue;
			if ( $info == 2 ) $data[$md] = array_merge($item[$v['itemId']], $v);//md=>data+info
			elseif ( !$info ) $data[$v['pd']] = intval(isset($data[$v['pd']]) ? max($data[$v['pd']], $v['state']) :$v['state']);//pd=>state
			else $data[$md] = $v;//mid=>data
		}
		if ( $cd == 1 && $info == 0 && !in_array(1, $data) ) $data['1'] = 1;
		return $data;
	}


	//获取用户服装状态
	public function getUserDress( $ud )
	{
		$mines = $this->getMine($ud, 1);
		if ( $mines === false ) return array();
		return $mines;
	}


	//获取用户物品状态 (服装之外的)
	public function getUserItems( $ud )
	{
		$mines = $this->getMine($ud, 0, 1);
		if ( $mines === false ) return array();
		$items = array();
		foreach ( $mines as $k => $v )
		{
			if ( $v['cd'] != 1 ) $items[$v['pd']] = intval($v['state']);
		}
		return $items;
	}



	//检查可否购买
	//U 			arr 	用户信息
	//gd 			int 	商品ID
	//num 			int 	购买数量
	//free 			int 	是否免费 0否1是
	//return 		arr 	array(addU=>array(),addI=>array())
	//return 		bool 	false操作失败
	public function buyGoods( $U, $gd, $num=1, $free=0 )
	{
		$gd = intval($gd);
		$num = intval($num);
		if ( !$U || $gd < 2 || !$num ) {
			gerr("购买操作无效 ".json_encode(func_get_args()));//no optional args
			return $this->setError(__FUNCTION__, 1);
		}
		$ud = intval($U['uid']);
		$goods = $this->getGoods($gd, $U['channel'], 1);
		if ( $goods === false ) {
			return $this->setError(__FUNCTION__, 2, $gd, $U['channel']);
		}
		$money = $goods['money'];
		$buyto = $goods['buyto'];
		$addU = $addI = $addS = array();
		//检测不可叠加(购买)
		$mines = $this->getMine($ud, 0, 2);
		if ( $mines === false ) return false;
		$isvalid = $invalid = 0;//有效属性//无效属性
		foreach ( $buyto as $k => $v )
		{
			//货币型
			if ( $k != 'items' ) {
				if ( !$v ) {
					unset($buyto[$k]);
					continue;
				} else {
					$isvalid++;
					$addU[$k] = intval($v);
				}
				continue;
			}
			//物品型
			foreach ( $v as $kk => $vv )
			{
				$isvalid++;
				$isbreak = 0;
				foreach ( $mines as $md => $mtem )
				{
					if ( $mtem['pd'] != $vv['ipd'] ) continue;
					if ( $mtem['state'] < 2 && !$mtem['overlay'] ) {
						$invalid++;
						$isbreak = 1;
						break;
					}
				}
				if ( !$isbreak ) {
					$addI[$vv['id']] = $vv;
				}
			}
		}
		if ( $isvalid == $invalid ) return $this->setError(__FUNCTION__, 4, $gd);
		//检测收费制约
		if ( !$free ) {
			//检测不可购买
			if ( !$goods['price'] ) return $this->setError(__FUNCTION__, 3, $gd);
			if ( $money != 'gold' ) $addU[$money] = $goods['price'] * -1;
			//检测金额不足
			if ( !isset($U[$money]) || $U[$money] < $goods['price'] ) return $this->setError(__FUNCTION__, 5, $gd);
			//检测已经下架
			if ( $goods['state'] > 0 ) return $this->setError(__FUNCTION__, 6, $gd);
			//检测已经无货
			if ( !$goods['store'] ) return $this->setError(__FUNCTION__, 7, $gd);
		}
		foreach ( $addI as $id => $item )
		{
			$res = $this->obtain($ud, 0, $id, $item['num']);
			if ( $res === false ) return false;
			if ( $res['addMoney'] ) {
				foreach ( $res['addMoney'] as $kk => $vv )
				{
					$addU[$kk] = $vv + (isset($addU[$kk]) ? $addU[$kk] : 0);
				}
			}
		}
		if ( $goods['store'] > 0 ) $addS[$gd] = $num * -1;
		if ( $addS ) $res = $this->storeup($addS);
		return array('addU'=>$addU, 'goods'=>$goods, 'propDress'=>$this->getUserDress($ud), 'propItems'=>$this->getUserItems($ud));
	}

	//得到某个物品
	//ud 			int 	用户ID
	//fruid 		int		来源用户ID
	//id 			int 	物品ID
	//num 			int 	物品数量 默认1
	//return 		arr 	组合信息 array(isNew=>0,this=>array(id=>10,...),newp=>array(num=>1,...),addMoney=>array(coupon=>10,...),newItem=>array(id=>10,...),newAward=>array(id=>10,...))
	//return 		bool 	false
	//return 		false操作失败 || isNew是否全新物品 | this用户物品数据 | newp物品属性变化 | addMoney货币属性增减 | newItem另一个新物品 | newAward另一个新实物
	public function obtain( $ud, $fruid, $id, $num=1 )
	{
		$mines = $this->getMine($ud, 0, 2);
		if ( $mines === false ) return false;
		$items = $this->getlistItem(0, 0, 1);
		if ( $items === false ) return false;
		if ( !isset($items[$id]) ) return $this->setError(__FUNCTION__, 1, $id);
		$mtem = array();
		$count = 0;
		foreach ( $mines as $md => $v )
		{
			if ( $v['itemId'] == $id || ( $v['overlay'] && $v['pd'] == $items[$id]['pd'] ) ) {
				$mtem = $v;
				$count++;
			}
		}
		if ( $count > 1 ) return $this->setError(__FUNCTION__, 1, $id);
		$newp = $addMoney = $newItem = $newAward = array();
		//处理创建/叠加
		$isNew = 0;
		if ( !$mtem ) {
			$mtem = $this->create($ud, $fruid, $id, 1);
			if ( !$mtem ) return false;
			$isNew = 1;
			$num -= 1;
		}
		if($num){
			$res = $this->overlay($ud, $fruid, $id, $mtem, $num);
			if ( !$res ) return false;
			$mtem = $res['this'];
			$newp = $res['newp'];
		}
		$md = intval($mtem['id']);
		$cd = intval($mtem['cd']);
		$pd = intval($mtem['pd']);
		$id = intval($mtem['itemId']);
		switch ( $mtem['useby'] ) {//使用途径
			case 0://拥有即用
				$res = $this->useuse($ud, $id, $mtem);
				if ( $res === false ) return false;
				$mtem = array_merge($mtem, $res['this']);
				$newp = array_merge($newp, $res['newp']);
				$addMoney = $res['addMoney'];
				$newItem = $res['newItem'];
				$newAward = $res['newAward'];
				break;
			case 1://缺失使用
				$isHas = 0;
				foreach ( $mines as $k => $v )
				{
					if ( $k > 1 && $v['cd'] == $cd && $v['pd'] == $pd ) $isHas = 1;
				}
				if ( $isHas ) break;
				$res = $this->useuse($ud, $id, $mtem);
				if ( $res === false ) return false;
				$mtem = array_merge($mtem, $res['this']);
				$newp = array_merge($newp, $res['newp']);
				$addMoney = $res['addMoney'];
				$newItem = $res['newItem'];
				$newAward = $res['newAward'];
				break;
			default://2手动使用
				break;
		}
		if ( $isNew ) $newp = array();
		$modify = array();
		if ( $newp ) $modify[$md] = $newp;
		if ( $newItem ) $modify[$newItem['id']] = $newItem;
		$mines2 = $this->setMine($ud, $modify);
		if ( $mines2 === false ) return false;
		return array('isNew'=>$isNew, 'this'=>$mtem, 'newp'=>$newp, 'addMoney'=>$addMoney, 'newItem'=>$newItem, 'newAward'=>$newAward);
	}


	//创建用户物品
	//ud 			int 	用户ID
	//fruid 		int 	来源用户ID
	//id 			int 	物品ID
	//num 			int 	物品数量 默认1
	//return 		arr 	用户物品信息＋附加 array(id=>123,dateid=>20151123,...)
	//return 		bool 	false操作失败
	public function create( $ud, $fruid, $id, $num=1 )
	{
		$mines = $this->getMine($ud, 0, 2);
		if ( $mines === false ) return false;
		$items = $this->getlistItem(0, 0, 1);
		if ( $items === false ) return false;
		if ( !isset($items[$id]) ) return $this->setError(__FUNCTION__, 1, $id);
		$item = $items[$id];
		$cd = intval($item['cd']);
		$pd = intval($item['pd']);
		$id = intval($item['id']);
		$md = max(array_keys($mines)) + 10;//预留 目前无效 临时伪造ID，不进行数据库查询，减少数据库压力
		$mtem['id'] = $md;
		$mtem['dateid'] = intval(date("Ymd"));
		$mtem['uid'] = intval($ud);
		$mtem['fruid'] = intval($fruid);
		$mtem['cd'] = $cd;
		$mtem['pd'] = $pd;
		$mtem['itemId'] = $id;
		$mtem['name'] = $item['name'];
		$mtem['num'] = $num > 0 ? intval($num) : 1;
		$mtem['start'] = 0;
		$mtem['end'] = 0;
		$mtem['sec'] = intval($item['second']);
		$mtem['poi'] = intval($item['points']);
		$mtem['state'] = 0;
		$mtem['create_time'] = $mtem['update_time'] = time();
		$mines2 = $this->setMine($ud, array($md=>$mtem));
		if ( $mines2 === false ) return false;
		$mines3 = $this->getMine($ud, 0, 2);
		if ( $mines3 === false ) return false;
		$md = array_diff(array_keys($mines3), array_keys($mines));
		$md = end($md);
		if ( !isset($mines3[$md]) ) return false;
		return $mines3[$md];
	}


	//叠加某个物品
	//ud 			int 	用户ID
	//fruid 		int 	来源用户ID
	//id 			int 	物品ID
	//mtem 			arr 	用户物品信息＋附加
	//num 			int 	叠加物品基数
	//return 		arr 	组合信息 array(done=>1,newp=>array(num=>11,...),this=>array(id=>123,dateid=>20151123,...))
	public function overlay( $ud, $fruid, $id, $mtem, $num )
	{
		$item = $this->getItem($id);
		if ( $item === false ) return false;
		$cd = intval($mtem['cd']);
		$pd = intval($mtem['pd']);
		$id = intval($mtem['itemId']);
		$md = intval($mtem['id']);
		$newp = array();
		switch ( $mtem['overlay'] ) {
			case 1://叠加数量
				$newp['num'] = $mtem['num'] = $mtem['num'] + $num;//
				if ( $mtem['number'] && $mtem['number'] < $mtem['num'] ) {
					$newp['num'] = $mtem['num'] = $mtem['number'];
					//预留 自动分包
				}
				break;
			case 2://叠加时效
				if ( !$item['second'] ) {
					$newp['sec'] = $newp['end'] = $mtem['sec'] = $mtem['end'] = 0;
					$newp['start'] = $mtem['start'] = time();
				} elseif ( !$mtem['end'] && !$mtem['sec'] ) {
					$newp['sec'] = $newp['end'] = $mtem['sec'] = $mtem['end'] = 0;
				} elseif ( $mtem['end'] ) {
					$newp['end'] = $mtem['end'] = $mtem['end'] + $num * $item['second'];//
				} elseif ( $mtem['sec'] ) {
					$newp['sec'] = $mtem['sec'] = $mtem['sec'] + $num * $item['second'];//
				} else {
					$newp['sec'] = $mtem['sec'] = $mtem['sec'] + $num * $item['second'] + $mtem['end'] - $mtem['start'];//
					$newp['start'] = $newp['end'] = $mtem['start'] = $mtem['end'] = 0;
				}
				break;
			case 3://叠加持久
				$newp['poi'] = $mtem['poi'] = $mtem['poi'] + $num * $item['points'];//
				break;
			default://0不可叠加
				break;
		}
		$done = intval(!!$newp);
		if ( $done ) $newp['update_time'] = $mtem['update_time'] = time();
		return array('done'=>$done, 'this'=>$mtem, 'newp'=>$newp);
	}


	//使用某个物品
	//ud 			int 	用户ID
	//id 			int 	道具ID
	//mtem 			arr		用户的此道具数据，如果为空，则需依据ID查询数据
	//sex 			int 	//预留 性别判定
	//return 		arr 	使用结果 array(this=>array(id=>10,...),newp=>array(num=>1,...),addMoney=>array(coupon=>10,...),newItem=>array(id=>10,...),newAward=>array(id=>10,...))//this用户物品数据 | newp物品属性变化 | addMoney货币属性增减 | newItem另一个新物品 | newAward另一个新实物
	//return 		bool 	false操作失败
	function useuse( $ud, $id, $mtem=array(), $isPd=0, $sex=0 )
	{
		if ( !$mtem || !is_array($mtem) ) {
			$mines = $this->getMine($ud, 0, 2);
			if ( $mines === false ) return false;
			if ( !$mines ) return $this->setError(__FUNCTION__, 1);
			$mtem = array();
			$num = 0;
			foreach ( $mines as $md => $v )
			{
				if ( ($isPd && $v['pd'] == $id) || (!$isPd && $v['itemId'] == $id) ) {
					$mtem = $v;
					$num++;
				}
			}
			if ( !$num ) return $this->setError(__FUNCTION__, 2, $id);
			if ( $num > 1 ) return $this->setError(__FUNCTION__, 3, $id);
		}
		$cd = intval($mtem['cd']);
		$pd = intval($mtem['pd']);
		$id = intval($mtem['itemId']);
		$md = intval($mtem['id']);
		if ( $mtem['sex'] && $sex && $sex != $mtem['sex'] ) return $this->setError(__FUNCTION__, 4);
		$time = time();
		$newp = $addMoney = $newItem = $newAward = array();
		$newp['update_time'] = $mtem['update_time'] = $time;
		$isUseup = 0;
		switch ( $mtem['usedo'] ) {//使用操作
			case 1://降低数量
				if ( $mtem['num'] < 1 ) return $this->setError(__FUNCTION__, 5);
				$newp['num'] = $mtem['num'] = $mtem['num'] - 1;
				if ( !$mtem['num'] ) $isUseup = 1;
				break;
			case 2://降低时效
				if ( ($mtem['sec'] || $mtem['end']) && $mtem['sec'] < 1 && $mtem['end'] < 1 ) return $this->setError(__FUNCTION__, 6);
				if ( $mtem['sec'] ) {
					$newp['start'] = $mtem['start'] = $time;
					$newp['end']   = $mtem['end']   = $time + $mtem['sec'];
					$newp['sec']   = $mtem['sec']   = 0;
				// } else {//这两行保留，有后续扩展
				// 	$newp['start'] = $mtem['start'] = $time;
				}
				break;
			case 3://降低持久
				if ( $mtem['poi'] < 1 ) return $this->setError(__FUNCTION__, 7);
				$newp['poi'] = $mtem['poi'] = $mtem['poi'] - 1;
				if ( !$mtem['poi'] ) $isUseup = 1;
				break;
			default://0不做处理
				//nothing to do
				break;
		}
		switch ( $mtem['useas'] ) {//使用用途
			case 1://改变状态
				$res = $this->shift($ud, $id, 0, $mtem['useto'], $mtem);
				if ( !$res ) return false;
				$mtem = $res['this'];
				$newp = array_merge($newp, $res['newp']);
				break;
			case 2://增加乐币
				$addMoney['gold'] = $mtem['useto'];
				break;
			case 3://增加代币
				$addMoney['golds'] = $mtem['useto'];
				break;
			case 4://增加乐豆
				$addMoney['coins'] = $mtem['useto'];
				break;
			case 5://增加乐券
				$addMoney['coupon'] = $mtem['useto'];
				break;
			case 6://增加抽奖数
				$addMoney['lottery'] = $mtem['useto'];
				break;
			case 8://获得物品
				if ( $id != $mtem['useto'] ) {
					$res = $this->obtain($ud, 0, $mtem['useto']);
					if ( !$res ) return false;
					$newItem = $res['newItem'];
				}
				break;
			case 9://获得实物
				$newAward = $this->getAward($mtem['useto']);
				if ( !$newAward ) return false;
				break;
			default://0不做处理
				//nothing to do
				break;
		}
		if ( $isUseup ) {
			switch ( $mtem['useup'] ) {//使用完毕
				case 0://销毁物品
					$sql = "DELETE FROM `lord_user_item` WHERE `id` = $md";
					$this->mysql->runSql($sql);
					$mtem = $newp = array();
					break;
				case 1://不做处理
					//nothing to do
					break;
				default://>=2状态变更
					$newp['state'] = $mtem['state'] = $mtem['useup'];
					break;
			}
		}
		$modify[$md] = $mtem ? $newp : $mtem;
		$mines = $this->setMine($ud, $modify);
		if ( $mines === false ) return false;
		return array('this'=>$mtem, 'newp'=>$newp, 'addMoney'=>$addMoney, 'newItem'=>$newItem, 'newAward'=>$newAward);
	}

	//穿着某件套装
	//ud 			int 	用户ID
	//pd 			int 	道具ID
	//return 		arr 	服装状态 array('1'=>1,'2'=>0,...)
	//return 		bol 	false操作失败
	public function dressup( $ud, $pd )
	{
		$cd = 1;
		$dress = $this->getMine($ud, $cd);
		if ( $dress === false) return false;
		if ( count($dress) == 1 ) return $dress;
		$res = $this->shift($ud, $pd, 1, 1);
		if ( $res === false ) return false;
		$dress = $this->getMine($ud, $cd);
		return $dress;
	}

	//切换道具状态
	//ud 			int 	用户ID
	//id 			int 	物品ID
	//isPd 			int 	id是否为pd 0否1是
	//state 		int 	目标状态 false自动0拥有1在用
	//mtem 			arr 	切换前的用户物品，如果有效则忽略$pd和$isPd参数
	//return 		arr 	切换结果 array('state'=>1,'this'=>array(),'newp'=>array(),'modify'=>array(...))//state切换后的状态 | this物品新的数据 | newp物品属性变化 | modify牵连数据变更
	//return 		bol 	false操作失败
	public function shift( $ud, $id, $isPd=0, $state=false, $mtem=array() )
	{
		$mines = $this->getMine($ud, 0, 2);
		if ( $mines === false ) return false;
		if ( !$mines ) return $this->setError(__FUNCTION__, 1);
		if ( !$mtem ) {
			$num = 0;
			foreach ( $mines as $md => $v )
			{
				if ( ($isPd && $v['pd'] == $id) || (!$isPd && $v['itemId'] == $id) ) {
					$mtem = $v;
					$num++;
				}
			}
			if ( !$num ) return $this->setError(__FUNCTION__, 2, $id, $isPd);
			if ( $num > 1 ) return $this->setError(__FUNCTION__, 3, $id, $isPd);
		}
		$cd = intval($mtem['cd']);
		$pd = intval($mtem['pd']);
		$id = intval($mtem['itemId']);
		$md = intval($mtem['id']);
		if ( $mtem['sex'] && $sex != $mtem['sex'] ) return $this->setError(__FUNCTION__, 4);
		$state = $state === false ? intval(!$mtem['state']) : intval(!!$state);
		$time = time();
		$newp = $modify = array();
		if ( $state ) {
			//同类互斥状态下的同类启用状态切换
			if ( $mtem['mutex'] ) {
				foreach ( $mines as $k => $v )
				{
					if ( $v['cd'] != $cd || $k == $md ) continue;
					if ( $v['state'] == $state ) {
						if ( $v['end'] || $v['sec'] ) {
							$modify[$k]['end'] = 0;
							$modify[$k]['sec'] = $v['sec'] + $v['end'] - $time;
						}
						if ( $v['pause'] ) {
							$modify[$k]['start'] = 0;
						}
						$modify[$k]['state'] = 0;
						$modify[$k]['update_time'] = $time;
					}
				}
			}
			if ( $mtem['end'] || $mtem['sec'] ) {
				$modify[$md]['end']   = $newp['end']   = $mtem['end']   = ($mtem['end'] ? ($mtem['end']-$mtem['start']) : 0) + $mtem['sec'] + $time;
				$modify[$md]['start'] = $newp['start'] = $mtem['start'] = $time;
				$modify[$md]['sec']   = $newp['sec']   = $mtem['sec']   = 0;
			}
		} else {
			if ( $mtem['pause'] ) {
				$modify[$md]['sec']   = $newp['sec']   = $mtem['sec']   = ($mtem['end'] ? ($mtem['end']-$mtem['start']) : 0) + $mtem['sec'];
				$modify[$md]['start'] = $newp['start'] = $mtem['start'] = 0;
				$modify[$md]['end']   = $newp['end']   = $mtem['end']   = 0;
			}
		}
		$modify[$md]['state']       = $newp['state']       = $mtem['state']       = $state;
		$modify[$md]['update_time'] = $newp['update_time'] = $mtem['update_time'] = $time;
		$mines = $this->setMine($ud, $modify);
		if ( $mines === false ) return false;
		return array('state'=>$state, 'this'=>$mtem, 'newp'=>$newp, 'modify'=>$modify);
	}

	//转赠某个道具
	//pd 		道具ID
	//ud 		赠送方用户ID
	//ud2 		接受方用户ID
	//U 		赠送方用户信息
	//U2 		接受方用户信息
	//return 预留
	public function present( $pd, $ud, $ud2, $U=array(), $U2=array() )
	{
		//
	}

	//销毁某个道具
	//pd 		道具ID
	//ud 		用户ID
	//U 		用户信息
	//return 预留
	public function destroy( $pd, $ud, $U=array() )
	{
		//
	}








//keep this comment
}//End Class prop
