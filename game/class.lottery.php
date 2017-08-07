<?php
/**
* PHP lottery class
*/
class lottery
{
	public $errno = 0;
	public $error = '';
	private $ndIns = 0;	//是否[01]需要投币
	private $isIns = 0;	//是否[01]已经投币
	private $types = array('coins','coupon','gold');	//投币币种
	private $cheat = array();	//作弊调整
	private $pool = array();	//奖池状况
	private $prizes = array();	//奖品状况
	private $prize = array();	//最近中奖结果
	private $history = array();	//抽中历史纪录(10条不含空奖)

	/**
	* $cheat 	作弊调整
	*		prizeId => array(	// 奖品id =>
	*			'isAbs' => 0,		// 是否无视概率绝对中奖，0否1是。
	*			'chance' => "=10",	// 中奖机会调整["=10"|"+10"|"-10"|"*10"|"/10"]
	*			'gold' => 0			// 中奖结果项调整
	*			...
	*		),
	*		...
	* $pool 	奖池状况
	*		coins		乐豆盈余
	*		coinsIn		投入乐豆
	*		coinsOut	放出乐豆
	*		coupon		奖券盈余
	*		couponIn	投入奖券
	*		couponOut	放出奖券
	*		gold		金币盈余
	*		goldIn		投入金币
	*		goldOut		放出金币
	*		propOut 	放出道具（特指游戏道具）
	*		playTimes	抽奖次数
	*		prizeTotal	抽中总次数
	*		prizesOut	各奖品抽中次数
	*			prizeId => prizeOutNum, 	// 奖品id => 此奖品抽出次数
	*			...
	* $prizes 	奖品配置表
	*		prizeId		奖品id
	*		name		奖品名称
	*		chance		中奖机会
	*		coins		奖励乐豆
	*		coupon		奖励奖券
	*		gold		奖励金币
	*		propId		奖励道具（特指游戏道具）
	*		filter		导致中奖机会变更(等)的奖池状况条件过滤器。顺序遍历逐个覆盖，即以当前条件的中奖机会(等)覆盖默认(上次已重设的)中奖机会(等)。举例如下：
	*			0 => array( 'key' => 'goldIn', 'leg' => 'l', 'val' => 1000000, 'chance' => "=111" ) 当前奖池的投入金币<=1000000时当前奖品的中奖机会为111
	*			1 => array( 'key' => 'goldIn', 'leg' => 'e', 'val' => 1000000, 'chance' => "+111" ) 当前奖池的投入金币==1000000时当前奖品的中奖机会加111
	*			2 => array( 'key' => 'goldIn', 'leg' => 'g', 'val' => 1000000, 'chance' => "*111" ) 当前奖池的投入金币>=1000000时当前奖品的中奖机会乘111
	*			3 => array( 'key' => 'prizeOut', 'leg' => 'g', 'val' => 100, 'chance' => "=0" ) 当前奖池内本奖品被抽出次数大于等于100时当前奖品的中奖机会为0
	*			4 => array( 'key' => 'date', 'leg' => 'e', 'val' => "2014-06-01 09:00:00|2018-06-31 23:30:00|2", 'chance' => 0 ) 在14年6月1日到18年6月1日之间的周二的上午9点到晚上11点半之前时，当前奖品的中奖机会为0
	*			...
	**/
	function __construct($cheat=array(),$pool=array(),$prizes=array())
	{
		$pool['coins'] = ( is_array($pool) && isset($pool['coins']) ) ? intval($pool['coins']) : 0;
		$pool['coinsIn'] = ( is_array($pool) && isset($pool['coinsIn']) ) ? intval($pool['coinsIn']) : 0;
		$pool['coinsOut'] = ( is_array($pool) && isset($pool['coinsOut']) ) ? intval($pool['coinsOut']) : 0;
		$pool['coupon'] = ( is_array($pool) && isset($pool['coupon']) ) ? intval($pool['coupon']) : 0;
		$pool['couponIn'] = ( is_array($pool) && isset($pool['couponIn']) ) ? intval($pool['couponIn']) : 0;
		$pool['couponOut'] = ( is_array($pool) && isset($pool['couponOut']) ) ? intval($pool['couponOut']) : 0;
		$pool['gold'] = ( is_array($pool) && isset($pool['gold']) ) ? intval($pool['gold']) : 0;
		$pool['goldIn'] = ( is_array($pool) && isset($pool['goldIn']) ) ? intval($pool['goldIn']) : 0;
		$pool['goldOut'] = ( is_array($pool) && isset($pool['goldOut']) ) ? intval($pool['goldOut']) : 0;
		$pool['propOut'] = ( is_array($pool) && isset($pool['propOut']) ) ? intval($pool['propOut']) : 0;
		$pool['playTimes'] = ( is_array($pool) && isset($pool['playTimes']) ) ? intval($pool['playTimes']) : 0;
		$pool['prizeTotal'] = ( is_array($pool) && isset($pool['prizeTotal']) ) ? intval($pool['prizeTotal']) : 0;
		$pool['prizesOut'] = ( is_array($pool) && isset($pool['prizesOut']) && is_array($pool['prizesOut']) ) ? $pool['prizesOut'] : array();
		$this->pool = $pool;
		$prizes = ( $prizes && is_array($prizes) ) ? $prizes : array(	// chance float 100=100%
			'0' => array( 'id'=>0, 'name'=>'什么都没中', 'chance'=>0, 'coins'=>0, 'coupon'=>0, 'gold'=>0, 'lottery'=>0, 'propId'=>0, 'filter'=>array() ),
			'1' => array( 'id'=>1, 'name'=>'3000乐豆', 'chance'=>100, 'coins'=>3000, 'coupon'=>0, 'gold'=>0, 'lottery'=>0, 'propId'=>0, 'filter'=>array() ),
			'2' => array( 'id'=>2, 'name'=>'5000乐豆', 'chance'=>50, 'coins'=>5000, 'coupon'=>0, 'gold'=>0, 'lottery'=>0, 'propId'=>0, 'filter'=>array() ),
			'3' => array( 'id'=>3, 'name'=>'10000乐豆', 'chance'=>30, 'coins'=>10000, 'coupon'=>0, 'gold'=>0, 'lottery'=>0, 'propId'=>0, 'filter'=>array() ),
			'4' => array( 'id'=>4, 'name'=>'20000乐豆', 'chance'=>10, 'coins'=>20000, 'coupon'=>0, 'gold'=>0, 'lottery'=>0, 'propId'=>0, 'filter'=>array() ),
			'5' => array( 'id'=>5, 'name'=>'50000乐豆', 'chance'=>1, 'coins'=>50000, 'coupon'=>0, 'gold'=>0, 'lottery'=>0, 'propId'=>0, 'filter'=>array() ),
			'6' => array( 'id'=>6, 'name'=>'10元充值卡', 'chance'=>0.1, 'coins'=>0, 'coupon'=>0, 'gold'=>0, 'lottery'=>0, 'propId'=>0, 'filter'=>array() ),
			'7' => array( 'id'=>7, 'name'=>'20元充值卡', 'chance'=>0.05, 'coins'=>0, 'coupon'=>0, 'gold'=>0, 'lottery'=>0, 'propId'=>0, 'filter'=>array() ),
			'8' => array( 'id'=>8, 'name'=>'30元充值卡', 'chance'=>0.01, 'coins'=>0, 'coupon'=>0, 'gold'=>0, 'lottery'=>0, 'propId'=>0, 'filter'=>array() ),
			'9' => array( 'id'=>9, 'name'=>'50元充值卡', 'chance'=>0.001, 'coins'=>0, 'coupon'=>0, 'gold'=>0, 'lottery'=>0, 'propId'=>0, 'filter'=>array() ),
			'10'=> array( 'id'=>10,'name'=>'100元充值卡', 'chance'=>0, 'coins'=>0, 'coupon'=>0, 'gold'=>0, 'lottery'=>0, 'propId'=>0, 'filter'=>array() ),
			'11'=> array( 'id'=>11,'name'=>'飞利浦音响', 'chance'=>0, 'coins'=>0, 'coupon'=>0, 'gold'=>0, 'lottery'=>0, 'propId'=>0, 'filter'=>array() ),
			'12'=> array( 'id'=>12,'name'=>'拍立得相机', 'chance'=>0, 'coins'=>0, 'coupon'=>0, 'gold'=>0, 'lottery'=>0, 'propId'=>0, 'filter'=>array() ),
			'13'=> array( 'id'=>13,'name'=>'Iphone 6', 'chance'=>0, 'coins'=>0, 'coupon'=>0, 'gold'=>0, 'lottery'=>0, 'propId'=>0, 'filter'=>array() ),
		);
		$this->prizes = $prizes;
		$cheat = ( $cheat && is_array($cheat) ) ? $cheat : array();
		foreach ( $cheat as $prizeId => $prizeSet )
		{
			if ( !isset($prizes[$prizeId]) ) {
				unset($cheat[$prizeId]);
				continue;
			}
			elseif ( is_array($prizeSet) ) {
				$cheat[$prizeId]['chance'] = isset($prizeSet['chance']) ? $prizeSet['chance'] : '=0';
				$cheat[$prizeId]['isAbs'] = isset($prizeSet['isAbs']) ? $prizeSet['isAbs'] : 0;
			}
			else{
				$cheat[$prizeId] = array();
				$cheat[$prizeId]['chance'] = strval($prizeSet);
				$cheat[$prizeId]['isAbs'] = 0;
			}
		}
		$this->setting = $cheat;
		$this->filter();
	}

	public function getError()
	{
		return $this->errno ? array('errno'=>$this->errno,'error'=>$this->error) : false;
	}

	public function getPool()
	{
		return $this->pool;
	}

	public function getPrize()
	{
		return $this->prize;
	}

	public function getPrizes()
	{
		return $this->prizes;
	}

	public function getHistory()
	{
		return $this->history;
	}

	public function insert($num, $type="coins")
	{
		if ( !$this->ndIns ) {
			$this->errno = 2;
			$this->errno = "do not need insert coins.";
			return false;
		}
		if ( !in_array($type, $this->types) ) {
			$this->errno = 3;
			$this->errno = "error conins type.";
			return false;
		}
		$num = intval($num);
		$num = $num > 0 ? $num : 0;
		if ( !$num ) {
			$this->errno = 4;
			$this->errno = "error conins num.";
			return false;
		}
		$this->pool[$type] += $num;
		$this->pool[$type.'In'] += $num;
		$this->isIns = 1;
		return true;
	}

	private function setPrize($prize)
	{
		$this->pool['playTimes']++;
		$this->prize = $prize;
		if ( !$prize['id'] ) {
			return true;
		}
		$this->pool['prizeTotal']++;
		$coins = intval($prize['coins']);
		if ( $coins > 0 ) {
			$this->pool['coins'] -= $coins;
			$this->pool['coinsOut'] += $coins;
		}
		$coupon = intval($prize['coupon']);
		if ( $coupon > 0 ) {
			$this->pool['coupon'] -= $coupon;
			$this->pool['couponOut'] += $coupon;
		}
		$gold = intval($prize['gold']);
		if ( $gold > 0 ) {
			$this->pool['gold'] -= $gold;
			$this->pool['goldOut'] += $gold;
		}
		if ( $prize['propId'] > 0 ) {
			$this->pool['propOut']++;
		}
		if ( isset($this->pool['prizesOut'][$prize['id']]) ) {
			$this->pool['prizesOut'][$prize['id']]++;
		}
		else {
			$this->pool['prizesOut'][$prize['id']]=1;
		}
		$history = $this->history;
		$history[]= $prize;
		if ( count($history) == 11 )
		{
			array_shift($history);
		}
		$this->history = $history;
		return true;
	}

	public function filter()
	{
		$cheat = $this->setting;
		$pool = $this->pool;
		$prizes = $this->prizes;
		$now = microtime(1);
		$day0 = strtotime(date('Y-m-d'));
		$wday = date("N");	//周n[1-7]
		foreach ( $prizes as $k => $v )
		{
			//基础过滤
			if ( !isset($v['id']) || !isset($v['name']) || !isset($v['chance']) )
			{
				unset($prizes[$k]);
				continue;
			}
			$prizes[$k]['id'] = $prizeId = abs($v['id']+0);
			$prizes[$k]['name'] = $name = trim($v['name']);
			$prizes[$k]['chance'] = $chance = abs($v['chance']+0);
			$prizes[$k]['gold'] = $gold = isset($v['gold']) ? intval($v['gold']) : 0;
			$prizes[$k]['coins'] = $coins = isset($v['coins']) ? intval($v['coins']) : 0;
			$prizes[$k]['coupon'] = $coupon = isset($v['coupon']) ? intval($v['coupon']) : 0;
			$prizes[$k]['propId'] = $propId = isset($v['propId']) ? intval($v['propId']) : 0;
			$prizes[$k]['filter'] = $filters = (isset($v['filter']) && is_array($v['filter'])) ? $v['filter'] : array();
			$prizes[$k]['isAbs'] = 0;
			//作弊调整——强制中奖
			if ( isset($cheat[$prizeId]['isAbs']) && $cheat[$prizeId]['isAbs'] )
			{
				$prizes[$k] = array_merge($prizes[$k], $cheat[$prizeId]);
				break;
			}
			//全局调整
			foreach ( $filters as $kk => $filter )
			{
				if ( !isset($filter['key']) || !isset($filter['leg']) || !in_array($filter['leg'], array('l','e','g')) || !isset($filter['val']) || !isset($filter['chance']) )
				{
					continue;
				}
				if ( $filter['key'] == 'date' )
				{	// 日期过滤
					$val_ = explode("|",$filter['val']);
					if ( count($val_) != 3 )
					{
						continue;
					}
					$start = explode(" ", $val_[0]);
					$dateStart = strtotime($start[0].' 00:00:00');
					$todayStart = strtotime(date("Y-m-d ".$start[1]));
					$end = explode(" ", $val_[1]);
					$dateEnd =  strtotime($end[0].' 23:59:59');
					$todayEnd = strtotime(date("Y-m-d ".$end[1]));
					$weeks = $val_[2];
					if ( $day0 > $dateStart && $day0 < $dateEnd && $now > $todayStart && $now < $todayEnd && ($weeks ? (strpos($weeks,$wday) !== false) : 1) )
					{
						$prizes[$k]['chance'] = $chance = $this->compute($chance,$filter['chance']);
						if ( !$chance )
						{
							break;
						}
						unset($filter['key']);
						unset($filter['leg']);
						unset($filter['val']);
						unset($filter['chance']);
						$prizes[$k] = array_merge($prizes[$k],$filter);
					}
				}
				elseif ( $filter['key'] == 'prizeOut' )
				{	// 限数过滤
					$prizeOut = isset($pool['prizesOut'][$prizeId]) ? $pool['prizesOut'][$prizeId] : 0;
					$filterVal = $filter['val'];
					switch ($filter['leg'])
					{
						case 'l':
							$cond = intval($prizeOut <= $filterVal);
						break;
						case 'g':
							$cond = intval($prizeOut >= $filterVal);
						break;
						default:
							$cond = intval($prizeOut == $filterVal);
						break;
					}
					if ( $cond )
					{
						$prizes[$k]['chance'] = $chance = $this->compute($chance,$filter['chance']);
						if ( !$chance )
						{
							break;
						}
						unset($filter['key']);
						unset($filter['leg']);
						unset($filter['val']);
						unset($filter['chance']);
						$prizes[$k] = array_merge($prizes[$k],$filter);
					}
				}
				elseif ( isset($pool[$filter['key']]) )
				{
					$poolVal = $pool[$filter['key']];
					$filterVal = $filter['val'];
					switch ($filter['leg'])
					{
						case 'l':
							$cond = intval($poolVal <= $filterVal);
						break;
						case 'g':
							$cond = intval($poolVal >= $filterVal);
						break;
						default:
							$cond = intval($poolVal == $filterVal);
						break;
					}
					if ( $cond )
					{
						$prizes[$k]['chance'] = $chance = $this->compute($chance,$filter['chance']);
						if ( !$chance )
						{
							break;
						}
						unset($filter['key']);
						unset($filter['leg']);
						unset($filter['val']);
						unset($filter['chance']);
						$prizes[$k] = array_merge($prizes[$k],$filter);
					}
				}
				else
				{
					continue;
				}
			}//End foreach $filters
			//作弊调整——机会变化
			if ( isset($cheat[$prizeId]) )
			{
				$prizes[$k] = array_merge($prizes[$k], $cheat[$prizeId]);
				$prizes[$k]['chance'] = $chance = $this->compute($chance,$cheat[$prizeId]['chance']);
			}
			if ( $chance < 0.0001 ) {
				unset($prizes[$k]);
				continue;
			}
		}
		$this->prizes = $prizes;
	}

	// val计算器
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

	public function run()
	{
		if ( $this->ndIns && !$this->isIns ) {
			$this->errno = 1;
			$this->error = "please insert coins.";
			return false;
		}
		$prizes = $this->prizes;
		$chanceMin = 0;
		$prizeIndex = $prizeChance = array();
		foreach ( $prizes as $k => $v )
		{
			if ( $v['isAbs'] ) {
				$this->setPrize($v);
				return true;
			}
			if ( $v['chance'] > 0 ) {
				$prizeIndex[$v['id']] = $v;
				$prizeChance[$v['id']] = $v['chance'];
				$chanceMin = $chanceMin ? min($chanceMin,$v['chance']) : $v['chance'];
			}
		}
		if ( intval($chanceMin) != $chanceMin )
		{	//float
			$chance = intval(str_replace('.','',strval($chanceMin)));
			$power = $chance / $chanceMin;
			foreach ( $prizeChance as $k => $v )
			{
				$prizeChance[$k] = $v * $power;
			}
		}
		if ( !$prizeChance ) {
			$this->errno = 5;
			$this->error = "bad prizes list.";
			return false;
		}
		$chanceTotal = array_sum($prizeChance);
		// var_dump("最大数：".$chanceTotal);
		$needle = mt_rand(1,$chanceTotal);
		// var_dump("随机数：".$needle);
		$step = 0;
		foreach ( $prizeChance as $k => $v )
		{
			$v += $step;
			// var_dump("区间：".$step." -> ".$v);
			if ( $needle > $step && $needle <= $v ) {
				$this->setPrize($prizeIndex[$k]);
				return true;
			}
			$step = $v;
		}
		arsort($prizeChance);
		$this->setPrize($prizeIndex[reset(array_flip($prizeChance))]);
		return true;
	}
}





// // html test

// ini_set("display_errors","On");
// error_reporting(E_ALL);		//E_ERROR | E_WARNING | E_PARSE

// echo '<!doctype html>
// <html lang="en">
//  <head>
//   <meta charset="utf-8">
//   <title>各服务器在线</title>
//   </head>
//  <body>
// ';
// echo "<pre>";

// $luck = array(
// 	'1' => "您本次【手气不好】，获得安慰奖 ",
// 	'2' => "您本次【手气一般】，获得鼓励奖 ",
// 	'3' => "您本次【手气尚可】，获得大奖 ",
// 	'4' => "您本次【手气很棒】，获得更大奖 ",
// 	'5' => "您本次【人品发力】，获得超大奖 ",
// 	'6' => "您本次【人品爆发】，获得现金奖 ",
// 	'7' => "您本次【人品爆棚】，获得现金奖 ",
// 	'8' => "您本次【祖上积德】，获得现金大奖 ",
// 	'9' => "您本次【祖上冒烟】，获得现金巨奖 ",
// 	'10' => "您冲了【不少钱】，获得现金回馈奖 ",
// 	'11' => "您冲了【很多钱】，获得实物回馈奖 ",
// 	'12' => "您肯定【作弊了】，获得作弊奖 ",
// 	'13' => "您绝对是【开发人】，获得最牛奖 ",
// );
// function getLevel($lottery,$prizeId=1){
// 	$prize = $lottery->run();
// 	$prize = $lottery->getPrize();
// 	if ( $prize && $prize['id'] == $prizeId ) {
// 		return $prize;
// 	}
// 	return false;
// }



// echo "<hr><h3>一、基础测试</h3>";
// $lottery = new lottery();
// $prize = $lottery->run();
// $prize = $lottery->getPrize();
// echo "<h3>".$luck[$prize['id']].$prize['name']."</h3>";
// echo "<hr>";
// for ( $prizeId=1; $prizeId<5 ; $prizeId++ ) {
// 	$i=0;
// 	while (++$i) {
// 		$prize = getLevel($lottery,$prizeId);
// 		if ( $prize ) {
// 			echo "<h3>为了获得《".$prize['id']." - ".$prize['name']."》，抽了【".$i."】次奖。</h3>";
// 			break;
// 		}
// 	}
// }



// echo "<hr><h3>二、作弊调整——强制中奖</h3>";
// $cheat = array(
// 	'9' => array( 'isAbs' => 1 ),
// );
// $lottery = new lottery($cheat);
// $prize = $lottery->run();
// $prize = $lottery->getPrize();
// echo "<h3>".$luck[$prize['id']].$prize['name']."</h3>";



// echo "<hr><h3>三、作弊调整——干扰概率</h3>";
// $cheat = array(
// 	'9' => 100,
// 	'8' => array( 'chance' => 100 ),
// );
// $lottery = new lottery($cheat);
// $prize = $lottery->run();
// $prize = $lottery->getPrize();
// echo "<h3>".$luck[$prize['id']].$prize['name']."</h3>";



// echo "<hr><h3>四、全局调整——受奖池因素干扰概率</h3>";
// $cheat = array();
// $pool = array();
// $prizes = array(
// 	'0' => array( 'id'=>0, 'name'=>'什么都没中', 'chance'=>0, 'coins'=>0, 'coupon'=>0, 'gold'=>0, 'lottery'=>0, 'propId'=>0, 'filter'=>array() ),
// 	'1' => array( 'id'=>1, 'name'=>'3000乐豆', 'chance'=>100, 'coins'=>3000, 'coupon'=>0, 'gold'=>0, 'lottery'=>0, 'propId'=>0, 'filter'=>array() ),
// 	'2' => array( 'id'=>2, 'name'=>'5000乐豆', 'chance'=>50, 'coins'=>5000, 'coupon'=>0, 'gold'=>0, 'lottery'=>0, 'propId'=>0, 'filter'=>array() ),
// 	'3' => array( 'id'=>3, 'name'=>'10000乐豆', 'chance'=>30, 'coins'=>10000, 'coupon'=>0, 'gold'=>0, 'lottery'=>0, 'propId'=>0, 'filter'=>array() ),
// 	'4' => array( 'id'=>4, 'name'=>'20000乐豆', 'chance'=>10, 'coins'=>20000, 'coupon'=>0, 'gold'=>0, 'lottery'=>0, 'propId'=>0, 'filter'=>array() ),
// 	'5' => array( 'id'=>5, 'name'=>'50000乐豆', 'chance'=>1, 'coins'=>50000, 'coupon'=>0, 'gold'=>0, 'lottery'=>0, 'propId'=>0, 'filter'=>array() ),
// 	'6' => array( 'id'=>6, 'name'=>'10元充值卡', 'chance'=>0.1, 'coins'=>0, 'coupon'=>0, 'gold'=>0, 'lottery'=>0, 'propId'=>0, 'filter'=>array() ),
// 	'7' => array( 'id'=>7, 'name'=>'20元充值卡', 'chance'=>0.05, 'coins'=>0, 'coupon'=>0, 'gold'=>0, 'lottery'=>0, 'propId'=>0, 'filter'=>array() ),
// 	'8' => array( 'id'=>8, 'name'=>'30元充值卡', 'chance'=>0.01, 'coins'=>0, 'coupon'=>0, 'gold'=>0, 'lottery'=>0, 'propId'=>0, 'filter'=>array() ),
// 	'9' => array( 'id'=>9, 'name'=>'50元充值卡', 'chance'=>0.001, 'coins'=>0, 'coupon'=>0, 'gold'=>0, 'lottery'=>0, 'propId'=>0, 'filter'=>array() ),
// 	'10'=> array( 'id'=>10,'name'=>'100元充值卡', 'chance'=>0, 'coins'=>0, 'coupon'=>0, 'gold'=>0, 'lottery'=>0, 'propId'=>0, 'filter'=>array() ),
// 	'11'=> array( 'id'=>11,'name'=>'飞利浦音响', 'chance'=>0, 'coins'=>0, 'coupon'=>0, 'gold'=>0, 'lottery'=>0, 'propId'=>0, 'filter'=>array() ),
// 	'12'=> array( 'id'=>12,'name'=>'拍立得相机', 'chance'=>0, 'coins'=>0, 'coupon'=>0, 'gold'=>0, 'lottery'=>0, 'propId'=>0, 'filter'=>array() ),
// 	'13'=> array( 'id'=>13,'name'=>'Iphone 6', 'chance'=>0, 'coins'=>0, 'coupon'=>0, 'gold'=>0, 'lottery'=>0, 'propId'=>0, 'filter'=>array(
// 		array( 'key'=>'playTimes', 'leg'=>'g', 'val'=>5, 'chance'=>'=1000' ),//超过5次抽奖后，概率加到很大，几乎必中
// 		array( 'key'=>'prizeOut', 'leg'=>'g', 'val'=>1, 'chance'=>'=0' ),//中过一次后，不会再中
// 	) ),
// );
// $i=0;
// while ( $i <= 20) {
// 	$i++;
// 	$lottery = new lottery($cheat,$pool,$prizes);
// 	$prize = $lottery->run();
// 	$prize = $lottery->filter();
// 	$prize = $lottery->getPrize();
// 	$pool = $lottery->getPool();
// 	echo "<p>第{$i}次。".$luck[$prize['id']].$prize['name']."</p>";
// }
