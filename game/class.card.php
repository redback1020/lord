<?php

//新牌库类
class Card
{
	//获取AI技能
	static function getAiSkill( $level=1 )
	{
		$AI = array(//[机器|托管] AI的等级技能配置
			'0'=>array(
				'is_peekunder'=>0,//不会偷看底牌
				'is_peekother'=>0,//不会偷看对手
				'is_getrole'=>0,	//不会识别对手，对上下家角色使用不同的出牌逻辑
				'is_remember'=>0,	//不会识别已打牌面
				'is_remainder'=>0,//不会关注对方手牌数量
				'is_unpack'=>0,	//不会被迫拆牌来跟对手牌或抬同伴牌
				'is_betout'=>0,	//[手牌]只剩下两手时，直接先出[最大牌]以博取牌权,并不再计算其他的牌型组合
				'is_bigout'=>0,	//[预选牌]中只有一手不是[回手牌]时，先出最小的[回手牌]
				'is_giveup'=>0,	//下家为农民同伴时，如果知道下家必赢，以下家需要牌型放水。
				'is_unsame'=>0,	//下家为对手且手牌不多时，尽可能不放同牌型的。
			),
			'1'=>array(
				'is_peekunder'=>1,
				'is_peekother'=>1,
				'is_getrole'=>1,
				'is_remember'=>1,
				'is_remainder'=>1,
				'is_unpack'=>1,
				'is_betout'=>1,
				'is_bigout'=>1,
				'is_giveup'=>1,
				'is_unsame'=>1,
			),
		);
		return isset($AI[$level]) ? $AI[$level] : $AI[0];
	}

	//初始化总牌池
	//isOld 	输出旧版牌值 预留
	static function initCardsPool( $isOld=0 )
	{
		for( $i = 1; $i < 5; $i++ )	//方梅红黑
		{
			for( $j = 1; $j <= 13; $j++ )	//3 4 5 ... T J Q K A 2
			{
				$pool[] = dechex($i) . dechex($j);
			}
		}
		$pool[] = dechex(5) . dechex(14);//小王
		$pool[] = dechex(5) . dechex(15);//大王
		if ( $isOld ) $pool = self::cardsToOld($pool);
		return $pool;
	}

	//从原牌数值转移到新牌数值，多牌
	static function cardsToNew( $cards )
	{
		if ( ! $cards || ! is_array($cards) ) $cards = array();
		foreach ( $cards as $k => $v )
		{
			$cards[$k] = self::cardToNew($v);

		}
		return $cards;
	}

	//从原牌数值转移到新牌数值，单张牌
	static function cardToNew( $card )
	{
		// 旧牌: 比如 45代表方块5 2d代表红桃K 01代表大王
		// / / / / / / / / / / / / / / / / / / / / / / / //
		//  w  W  A  2  3  4  5  6  7  8  9  T  J  Q  K  //牌面字
		//  0  1  1  2  3  4  5  6  7  8  9 10 11 12 13  //低位十进制
		//  0  1                                         //0 王色
		//        1  2  3  4  5  6  7  8  9  a  b  c  d  //1 方块
		//        1  2  3  4  5  6  7  8  9  a  b  c  d  //2 梅花
		//        1  2  3  4  5  6  7  8  9  a  b  c  d  //3 红桃
		//        1  2  3  4  5  6  7  8  9  a  b  c  d  //4 黑桃
		// / / / / / / / / / / / / / / / / / / / / / / / //
		// 新牌: 比如 13代表方块5 3b代表红桃K 5f代表大王
		// / / / / / / / / / / / / / / / / / / / / / / / //
		//  3  4  5  6  7  8  9  T  J  Q  K  A  2  w  W  //牌面字
		//  1  2  3  4  5  6  7  8  9 10 11 12 13 14 15  //低位十进制
		//  1  2  3  4  5  6  7  8  9  a  b  c  d        //1 方块
		//  1  2  3  4  5  6  7  8  9  a  b  c  d        //2 梅花
		//  1  2  3  4  5  6  7  8  9  a  b  c  d        //3 红桃
		//  1  2  3  4  5  6  7  8  9  a  b  c  d        //4 黑桃
		//                                         e  f  //5 王色
		// / / / / / / / / / / / / / / / / / / / / / / / //
		// 赖子代牌: 在目标代牌花色上＋5 赖子不可替代王牌
		$color = hexdec($card[0]);
		if ( $color > 4 ) {
			$jcadd = 5;
			$color-= 5;
		} else {
			$jcadd = 0;
		}
		$color = $color == 0 ? 5 : $color;
		$point = hexdec($card[1]);
		if ( $color == 5 ) {
			$point+= 14;
		} else {
			$point-= 2;
			$point = ($point < 1) ? ($point + 13) : $point;
		}
		$color+= $jcadd;
		return dechex($color).dechex($point);
	}

	//从新牌数值转移到原牌数值，多牌
	static function cardsToOld( $cards )
	{
		$cards = ($cards && is_array($cards)) ? $cards : array();
		foreach ( $cards as $k => $v )
		{
			$cards[$k] = self::cardToOld($v);
		}
		return $cards;
	}

	//从新牌数值转移到原牌数值，单张牌
	static function cardToOld( $card )
	{
		$color = hexdec($card[0]);
		if ( $color > 5 ) {
			$jcadd = 5;
			$color-= 5;
		} else {
			$jcadd = 0;
		}
		$color = $color == 5 ? 0 : $color;
		$point = hexdec($card[1]);
		if ( $color == 0 ) {
			$point-= 14;
		} else {
			$point+= 2;
			$point = ($point > 13) ? ($point - 13) : $point;
		}
		$color+= $jcadd;
		return dechex($color).dechex($point);
	}

	//初始化牌池并发牌
	static function newCardPool( $isOld=0, $addBomb=0, $rules=array() )
	{
		//洗牌分牌 array(0=>array(...),1=>array(...),2=>array(...),lord=>array(...),miss=>array(0=>1,1=>2,2=>0))
		$cards = self::cardsDeal(self::cardsShuffle(self::initCardsPool()), $addBomb, $rules);

		//新增手牌分
		$cards['score'] = [];

		//席位牌组
		foreach( $cards as $k => $v )
		{
			if ( $k === 'miss' || $k === 'lord' || $k === 'score') continue;
			$cards[$k] = self::cardsSort($isOld ? self::cardsToOld($v) : $v, $isOld);
			$cards['score'][$k] = self::cardScore( $v, 0 );
		}
		return $cards;
	}

	//计算牌的手排分
	//计算手牌分统一按照新牌值算
	static function cardScore( $card, $isOld ){
		//牌型	双王	炸弹	大王	小王	单2	单A
		//分值	9	    8	     6	     3	     2	1
		$score = 0;

		//旧牌值转成新牌值
		if( $isOld ){
			$card =  Card::cardsToNew($card ) ;
		}

		//去掉牌字面
		$card_only_values = [];
		for( $i=0; $i<=16; $i++ ){
			array_push($card_only_values,$card[$i][1]);
		}

		sort($card_only_values);

		//计算相同牌值出现次数
		$card_count = array_count_values($card_only_values);

		while( $card_count ){
			//双王
			if(isset($card_count['e']) && isset($card_count['f'])){
				$score += 9;
				unset($card_count['e'],$card_count['f']);
				continue;
			}
			//炸弹
			elseif( ( $k = array_search(4,$card_count ) ) !== false ){
				$score += 8;
				unset($card_count[$k]);
				continue;
			}
			//大王
			elseif( isset($card_count['e']) ){
				$score += 6;
				unset($card_count['e']);
				continue;
			}
			//小王
			elseif( isset($card_count['f']) ){
				$score += 3;
				unset($card_count['f']);
				continue;
			}
			//单2
			elseif( isset($card_count['d']) &&  $card_count['d'] == 1){
				$score += 2;
				unset($card_count['d']);
				continue;
			}
			//单A
			elseif( isset($card_count['c']) &&  $card_count['c'] == 1){
				$score += 1;
				unset($card_count['c']);
				continue;
			}else{
				break;
			}
		}

		return $score;
	}

	//洗牌
	static function cardsShuffle( $cards )
	{
		shuffle($cards);
		return $cards;
	}

	//分牌 [0][1][2][lord]
	static function cardsDeal( $pool, $addBomb=0, $rules=array() )
	{
		if ( ! is_array($pool) || count($pool) != 54 ) return array();
		$seatCards = $seatBomb = array(array(), array(), array());
		$addBomb = ( 0 < $addBomb && $addBomb < 13 ) ? intval($addBomb) : 0;
		$rules = is_array($rules) ? $rules : array();
		// 干扰加炸模式
		if ( $rules && $addBomb )
		{
			$miss = isset($rules['miss']) ? $rules['miss'] : array();
			// debug("加炸用户权重 bomb=$addBomb B".json_encode($miss));//调试用
			for ( $i = 0; $i < 3; $i++ )
			{
				if ( !isset($miss[$i]) ) $miss[$i] = 3;
				if ( $miss[$i] < 0 ) $miss[$i] = 0;
			}
			$mbak = $miss;
			// debug("加炸用户权重 bomb=$addBomb C".json_encode($miss));//调试用
			$bombPoints = array_merge(range(1, 9), array('a', 'c'));//3~A
			shuffle($bombPoints);
			$bombPoints = array_slice($bombPoints, 0, $addBomb);
			for ( $i = 0; $i < $addBomb; $i++ ) {
				$sd = $step = 0;
				$needle = mt_rand(1, array_sum($miss) + 3);
				foreach ( $miss as $sid => $prob )
				{
					$prob += 1;
					if ( $needle > $step && $needle <= $prob + $step ) {
						$sd = $sid;
						break;
					}
					$step += $prob;
				}
				$seatBomb[$sd][]=strval($bombPoints[$i]);
				$miss[$sd] = $miss[$sd] > 0 ? $miss[$sd]-- : 0;
				$mbak[$sd] = 0;
			}
			foreach ( $mbak as $sid => $val ) {
				if ( $val ) $mbak[$sid]++;
			}
			// debug("加炸用户权重 bomb=$addBomb D".json_encode($mbak));//调试用
			foreach ( $pool as $k => $v )
			{
				$point = strval($v[1]);
				foreach ( $seatBomb as $sid => $vv )
				{
					if ( in_array($point, $vv) ) {
						$seatCards[strval($sid)][] = $v;
						unset($pool[$k]);
						break;
					}
				}
			}
			$pool = array_values($pool);
			$seatCards['lord'] = array_splice($pool, 0, 3);
			$seatCards['0'] = array_merge($seatCards['0'], array_splice($pool, 0, 17 - count($seatCards['0'])));
			$seatCards['1'] = array_merge($seatCards['1'], array_splice($pool, 0, 17 - count($seatCards['1'])));
			$seatCards['2'] = array_merge($seatCards['2'], $pool);
			$seatCards['miss'] = $mbak;
			// debug("加炸用户权重 bomb=$addBomb E".json_encode($seatCards));//调试用
		}
		// 随机加炸模式
		elseif ( $addBomb )
		{
			$bombPoints = array_merge(range(1, 9), range('a', 'd'));//3~2
			shuffle($bombPoints);
			$bombPoints = array_slice($bombPoints, 0, $addBomb);
			foreach ( $bombPoints as $k => $v )
			{
				$i = mt_rand(0, 2);
				if ( count($seatBomb[$i]) > 3 ) {
					$i++; $i %= 3;
					if ( count($seatBomb[$i]) > 3 ) {
						$i++; $i %= 3;
					}
				}
				$seatBomb[$i][]=strval($v);
			}
			foreach ( $pool as $k => $v )
			{
				$point = strval($v[1]);
				foreach ( $seatBomb as $kk => $vv )
				{
					if ( !in_array($point, $vv) ) continue;
					$seatCards[strval($kk)][] = $v;
					unset($pool[$k]);
					break;
				}
			}
			$pool = array_values($pool);
			$seatCards['lord'] = array_splice($pool, 0, 3);
			$seatCards['0'] = array_merge($seatCards['0'], array_splice($pool, 0, 17-count($seatCards['0'])));
			$seatCards['1'] = array_merge($seatCards['1'], array_splice($pool, 0, 17-count($seatCards['1'])));
			$seatCards['2'] = array_merge($seatCards['2'], $pool);
			$seatCards['miss'] = false;
		}
		// 默认普通模式
		else
		{
			$seatCards['lord'] = array_splice($pool, 0, 3);
			$seatCards['0'] = array_splice($pool, 0, 17);
			$seatCards['1'] = array_splice($pool, 0, 17);
			$seatCards['2'] = $pool;
			$seatCards['miss'] = false;
		}
		return $seatCards;
	}

	//牌组依据同点大小依次倒序
	//$cards 	目标牌组
	//isOld 	目标牌组为旧版牌面牌值
	static function cardsSort( $cards, $isOld=0 )
	{
		if ( $isOld ) {
			foreach( $cards as $k => $v )
			{
				$type = hexdec($v[0]);
				$point = hexdec($v[1]);
				if( $type == 0 && $point == 0 ) $point = 20;
				if( $type == 0 && $point == 1 ) $point = 21;
				if( $point < 3 )  $point += 13;
				$sorts[$point*100+$type] = $v;
			}
		} else {
			foreach( $cards as $k => $v )
			{
				$sorts[hexdec($v[1])*100+hexdec($v[0])] = $v;
			}
		}
		krsort($sorts);
		return array_values($sorts);
	}

	//牌组低位解析为十进制数组，并默认倒序
	static function cardsDec( $cards, $sort=1 )
	{
		if ( ! $cards || ! is_array($cards) ) return array();
		foreach( $cards as $card )
		{
			$hexs[] = self::cardDec($card);
		}
		if ( $sort ) rsort($hexs);
		return $hexs;
	}

	//单张牌低位解析为十进制
	static function cardDec( $card )
	{
		return hexdec(isset($card[1])?$card[1]:$card[0]);
	}

	//设置赖子牌
	//cards		array 	赖子牌的旧版十六进制牌面选取空间
	//return 	string 	赖子牌的无花色旧版十六进制牌值
	static function newJoker( $cards=array() )
	{
		// $jokall = array('2','3','4','5','6','7','8','9','a','b','c','d');//客户端赖子为A有bug
		$jokall = array('1','2','3','4','5','6','7','8','9','a','b','c','d');
		$jokers = array();
		if ( $cards && is_array($cards) ) {
			foreach ( $cards as $card )
			{
				$jokers[]=$card[1];
			}
		}
		if ( ! $jokers || array_diff($jokers, $jokall) ) $jokers = $jokall;
		return strval($jokers[array_rand($jokers)]);
	}

	//前置赖子牌
	//cards 	array 	用户手牌 旧版十六进制牌组
	//joker		string 	赖子牌值 旧版十六进制无花色牌值
	static function preJoker( $cards, $joker )
	{
		$prevs = array();
		foreach ( $cards as $k => $card )
		{
			if ( $card[0] === '0' || $card[1] !== $joker ) continue;
			$prevs[] = $card;
			unset($cards[$k]);
		}
		return $prevs ? array_merge($prevs, array_values($cards)) : $cards;
	}

	//检测赖子牌
	//cards 	array 	用户手牌 旧版十六进制牌组
	//joker		string 	赖子牌值 旧版十六进制无花色牌值
	static function hasJoker( $cards, $joker )
	{
		foreach ( $cards as $k => $card )
		{
			if ( $card[0] > 0 && $card[1] === $joker ) return true;
		}
		return false;
	}

	//底牌翻倍
	//cards 	array 	三张地主牌 旧版十六进制牌面
	static function cardsRate( $cards )
	{
		if ( ! is_array($cards) || count($cards) != 3 ) return 1;
		$cards = array_values($cards);
		//检测双王/单王 3倍/2倍
		if ( $arr = array_intersect(array('00','01'), $cards) ) return count($arr) == 2 ? 3 : 2;
		//检测同花 3倍
		if ( $cards[0][0] ==  $cards[1][0] && $cards[1][0] == $cards[2][0] ) return 3;
		//原牌翻译、获取低位十进制、倒序
		$cards = self::cardsDec(self::cardsToNew($cards));
		//检测三条 3倍
		if ( $cards[0] ==  $cards[1] && $cards[1] == $cards[2] ) return 3;
		//检测顺子 3倍
		if ( $cards[0] ==  $cards[1] + 1 && $cards[1] == $cards[2] + 1 ) return 3;
		//检测对牌 2倍
		if ( $cards[0] == $cards[1] || $cards[1] == $cards[2] ) return 2;
		return 1;
	}

	//获取可行任务 旧版牌库
	// hcards 	array(),手牌 必有牌
	// bcards 	array(),底牌 地主牌
	// tlist 	array(),牌局任务列表
	// tconf 	array(),牌局任务配置列表
	// return 	组合后的任务数据
	static function getProbTask( $hcards, $bcards, $tlist, $tconf, $debug=0 )
	{
		if ( ! $hcards || ! is_array($hcards) || ! $bcards || ! is_array($bcards) || ! $tlist || ! is_array($tlist) || ! $tconf || ! is_array($tconf) ) return false;
		//配置牌值牌面 	//服端无花色十进制字串牌值与牌面字串对照
		$raCvals = array('01'=>'3','02'=>'4','03'=>'5','04'=>'6','05'=>'7','06'=>'8','07'=>'9','08'=>'10','09'=>'J','10'=>'Q','11'=>'K','12'=>'A','13'=>'2','14'=>'小王','15'=>'大王');
		//配置无王牌值 	//没有大小王的服端无花色十进制整型牌值
		$vals = array_diff(array_keys($raCvals), array("14", "15"));foreach ( $vals as $k => $v ) { $vals[$k] = strval($v); }
		//配置牌型匹配 	//任务条件ID与牌型对照
		$confidCtype = array('2'=>'01','3'=>'02','4'=>'01','5'=>'99','6'=>'01','7'=>'01','8'=>'01','9'=>'02','10'=>'02','11'=>'02','12'=>'03_04_05','13'=>'03_04_05','14'=>'03_04_05','15'=>'06','16'=>'06','17'=>'06','18'=>'07','19'=>'07','20'=>'07','21'=>'08_09_10','22'=>'08_09_10','23'=>'08_09_10','24'=>'88_99','25'=>'88','26'=>'88_99','27'=>'99');
		//配置运算逻辑 	//任务条件ID与运算对照
		$confidCcomp = array('2'=>'v=','3'=>'v=','4'=>'v>','5'=>'v>','6'=>'v=','7'=>'v>','8'=>'v>','9'=>'','10'=>'v=','11'=>'v>','12'=>'','13'=>'v=','14'=>'v>','15'=>'','16'=>'l=','17'=>'l>','18'=>'','19'=>'g=','20'=>'g>','21'=>'','22'=>'g=','23'=>'g>','24'=>'','25'=>'v=','26'=>'v>','27'=>'v>');
		//必有牌 客端带花色十六进制字串牌值转换到服端无花色十进制整型牌值 暂不使用
		// $hcards = self::cardsDec(self::cardsToNew(array_values($hcards)));
		//可能牌 客端带花色十六进制字串牌值转换到服端无花色十进制整型牌值
		$pcards = self::cardsDec(self::cardsToNew(array_values(array_merge($hcards, $bcards))));
		//地主牌 客端带花色十六进制字串牌值转换到服端无花色十进制整型牌值
		$bcards = self::cardsDec(self::cardsToNew(array_values($bcards)));
		//地主牌 没有大小王的服端无花色十进制字串牌值，用于抓底任务运算
		$bcards_ = array_diff($bcards, array(14, 15)); foreach ( $bcards_ as $k => $v ) { $bcards_[$k] = ($v>9?'':'0').$v; }
		//可行模式 使用全部牌型
		$types = self::cardsFitTypes($pcards, 2); //不使用必有牌($hcards)，而使用可能牌: 允许没有抓到底牌时的任务无法完成现象
		if ( $types ) { krsort($types); if ( count($types) > 5 ) { $types = array_slice($types, 0, 5, 1); }; $keys = array_keys($types); shuffle($keys); $random = array(); foreach ($keys as $key) { $random[$key] = $types[$key]; }; $types = $random; }
		$debug && debug("ERR牌局可行任务 生成阶段 全部牌型 ".json_encode($types));
		//整理牌局任务列表
		foreach ( $tlist as $id => $task )
		{
			$conds = array();
			foreach ( $task['conds'] as $tconfid )
			{
				if ( $tconfid > 1 && $tconfid < 28 && !in_array($tconfid, array(4, 5, 8, 27)) ) {//牌值任务: 值为初始化0 有牌型配置 有运算配置
					$conds[] = array('id'=>$tconfid, 'name'=>$tconf[$tconfid]['name'], 'value'=>0, 'ctype'=>$confidCtype[$tconfid], 'ccomp'=>$confidCcomp[$tconfid]);
				} elseif( in_array($tconfid, array(4, 5, 8, 27)) ) { //有王任务: 值为小王牌值(便于进行大小单王或王炸时的值运算) 有牌型配置 有运算配置
					$conds[] = array('id'=>$tconfid, 'name'=>$tconf[$tconfid]['name'], 'value'=>14, 'ctype'=>$confidCtype[$tconfid], 'ccomp'=>$confidCcomp[$tconfid]);
				} elseif( $tconfid == 28 ) {	//倍率任务: 值为随机倍率 无牌型配置 无运算配置
					$conds[] = array('id'=>$tconfid, 'name'=>$tconf[$tconfid]['name'], 'value'=>mt_rand(4,12)*100);
				} else {//其它任务: 值为初始化0 无牌型配置 无运算配置
					$conds[] = array('id'=>$tconfid, 'name'=>$tconf[$tconfid]['name'], 'value'=>0);
				}
			}
			$tlist[$id]['conds'] = $conds;
		}
		$debug && debug("ERR牌局可行任务 生成阶段 条件规整 ".json_encode($tlist));
		//找出可完成的任务 此任务列表已进行过渠道房间用户筛选
		$plist = array();
		foreach ( $types as $t => $cards )
		{
			$tlv = str_split($t, 2);
			$ctyp = $tlv[0];//牌型 00无效01单张牌02对牌03三条04三带单05三带对06顺子07连对08飞机09飞机单10飞机对11四带单12四带对88//硬炸弹99王炸
			$clen = intval($tlv[1]);  //牌数 实际总长度数字
			$cval = intval($tlv[2]);  //牌值 1 2 3 4 5 6 7 8 9 10 11 12 13 14 15
			$cfac = $raCvals[$tlv[2]];//牌面 3 4 5 6 7 8 9 10 J Q K A 2 小王 大王
			if ( in_array($ctyp, array('00','11','12')) ) { continue; }//无效牌/四带牌型不做为牌局任务
			foreach ( $tlist as $id => $task )
			{
				$ccount = 0;
				foreach ( $task['conds'] as $k => $cond )
				{
					//非牌型任务 可能完成
					if ( !isset($cond['ctype']) ) {
						$task['name'][] = sprintf($cond['name'], $cond['value']);
						$ccount++;
						continue;
					}
					//底牌抓到某单张牌任务
					if ( $cond['id'] == 2 ) {
						$cs = array_intersect($vals, $bcards_);
						if ( ! is_array($cs) ) {
							echo date("Y-m-d H:i:s").json_encode($vals)."\n";
							echo date("Y-m-d H:i:s").json_encode($bcards_)."\n";
							echo date("Y-m-d H:i:s").json_encode($hcards)."\n";
							echo date("Y-m-d H:i:s").json_encode($bcards)."\n";
						}
						$cval_ = $cs[array_rand($cs)];
						$cond['value'] = intval($cval_);
						$task['name'][] = sprintf($cond['name'], $raCvals[$cval_]);
						$task['conds'][$k] = $cond;
						$ccount++;
						continue;
					}
					//底牌抓到某对牌任务
					elseif ( $cond['id'] == 3 ) {
						if ( isset($bcards_[1]) && $bcards_[0] == $bcards_[1] ) {
							$cs = array($bcards_[0]);
						} elseif ( isset($bcards_[1]) && isset($bcards_[2]) && $bcards_[1] == $bcards_[2]) {
							$cs = array($bcards_[1]);
						} else {
							continue;
						}
						$cval_ = $cs[array_rand($cs)];
						$cond['value'] = intval($cval_);
						$task['name'][] = sprintf($cond['name'], $raCvals[$cval_]);
						$task['conds'][$k] = $cond;
						$ccount++;
						continue;
					}
					//底牌抓到某王任务
					elseif ( $cond['id'] == 4 ) {
						if ( count($bcards_) != 3 ) {
							$task['name'][] = $cond['name'];
							$ccount++;
						}
						continue;
					}
					//底牌抓到王炸任务
					elseif ( $cond['id'] == 5 ) {
						if ( count($bcards_) == 1 ) {
							$task['name'][] = $cond['name'];
							$ccount++;
						}
						continue;
					}
					//牌型不匹配 因为有重复运算因素 所以无法识别 故不进行比较运算
					if ( strpos($cond['ctype'], $ctyp) === false ) {
						continue;
					}
					//牌型匹配时 进行比较运算
					switch ($cond['ccomp'])
					{
					case 'v=':
						if ( ! $cond['value'] || $cval >= $cond['value'] ) {
							$cond['value'] = $cval;
							$name = $cfac;
						} else {
							$ccount--;
						}
						break;
					case 'v>':
						if ( ! $cond['value'] || $cval >= $cond['value'] ) {
							$cond['value'] = $cval;
							$name = $cfac;
						} else {
							$ccount--;
						}
						break;
					case 'l=':
						$cond['value'] = $clen;
						$name = $clen;
						break;
					case 'l>':
						$cond['value'] = $clen;
						$name = $clen;
						break;
					case 'g=':
						switch ($ctyp)
						{
							case '07': $clen = intval($clen/2); break;//双顺子
							case '08': $clen = intval($clen/3); break;//飞机
							case '09': $clen = intval($clen/4); break;//飞机单
							default:   $clen = intval($clen/5); break;//飞机对
						}
						$cond['value'] = $clen;
						$name = $clen;
						break;
					case 'g>':
						switch ($ctyp)
						{
							case '07': $clen = intval($clen/2); break;//双顺子
							case '08': $clen = intval($clen/3); break;//飞机
							case '09': $clen = intval($clen/4); break;//飞机单
							default:   $clen = intval($clen/5); break;//飞机对
						}
						$cond['value'] = $clen;
						$name = $clen;
						break;
					default://''
						break;
					}
					$task['name'][] = sprintf($cond['name'], isset($name) ? $name : $cond['value']);
					$task['conds'][$k] = $cond;
					unset($name);
					$ccount++;
				}
				if ( $ccount == count($task['conds']) ) {
					if ( $ccount == 2 && $task['name'][1] == '赢得胜利' ) { $task['name'][1] = '胜利'; }
					$task['name'] = join('并', $task['name']);
					$task['is_new'] = $task['is_done'] = 0;
					$plist[$id] = $task;
					unset($tlist[$id]);
					$debug && debug("ERR牌局可行任务 生成阶段 满足条件 ".$task['name']." ".json_encode($task));
				} else {
					$debug && debug("ERR牌局可行任务 生成阶段 不够条件 ".$task['conds'][0]['name'].(isset($task['conds'][1]['name'])?('并'.$task['conds'][1]['name']):'')." ".json_encode($task));
				}
			}
			if ( count($plist) > 3 ) break;//直接pass
		}
		//随机出某个任务
		$total = 0;
		foreach ( $plist as $id => $task )
		{
			$total+=$task['prob'];
		}
		$needle = mt_rand(0, $total);
		$step = 0;
		foreach ( $plist as $id => $task )
		{
			if ( $needle > $step && $needle <= $task['prob'] + $step ) return $task;
			$step += $task['prob'];
		}
		return array();
	}

	//获取误导任务
	// hcards 	array(),手牌
	// bcards 	array(),底牌
	// tlist 	array(),牌局任务列表
	// tconf 	array(),牌局任务配置列表
	// return 	组合后的任务数据
	static function getMissTask( $hcards, $bcards, $tlist, $tconf, $debug=0 )
	{
		if ( ! $hcards || ! is_array($hcards) || ! $bcards || ! is_array($bcards) || ! $tlist || ! is_array($tlist) || ! $tconf || ! is_array($tconf) ) return false;
		//配置牌值牌面 	//服端无花色十进制字串牌值与牌面字串对照
		$raCvals = array('01'=>'3','02'=>'4','03'=>'5','04'=>'6','05'=>'7','06'=>'8','07'=>'9','08'=>'10','09'=>'J','10'=>'Q','11'=>'K','12'=>'A','13'=>'2','14'=>'小王','15'=>'大王');
		//配置无王牌值 	//没有大小王的服端无花色十进制整型牌值
		$vals = array_diff(array_keys($raCvals), array("14", "15"));foreach ( $vals as $k => $v ) { $vals[$k] = strval($v); }
		//配置牌型匹配 	//任务条件ID与牌型对照
		$confidCtype = array('2'=>'01','3'=>'02','4'=>'01','5'=>'99','6'=>'01','7'=>'01','8'=>'01','9'=>'02','10'=>'02','11'=>'02','12'=>'03_04_05','13'=>'03_04_05','14'=>'03_04_05','15'=>'06','16'=>'06','17'=>'06','18'=>'07','19'=>'07','20'=>'07','21'=>'08_09_10','22'=>'08_09_10','23'=>'08_09_10','24'=>'88_99','25'=>'88','26'=>'88_99','27'=>'99');
		//配置运算逻辑 	//任务条件ID与运算对照
		$confidCcomp = array('2'=>'v=','3'=>'v=','4'=>'v>','5'=>'v>','6'=>'v=','7'=>'v>','8'=>'v>','9'=>'','10'=>'v=','11'=>'v>','12'=>'','13'=>'v=','14'=>'v>','15'=>'','16'=>'l=','17'=>'l>','18'=>'','19'=>'g=','20'=>'g>','21'=>'','22'=>'g=','23'=>'g>','24'=>'','25'=>'v=','26'=>'v>','27'=>'v>');
		//必有牌 客端带花色十六进制字串牌值转换到服端无花色十进制整型牌值 暂不使用
		// $hcards = self::cardsDec(self::cardsToNew(array_values($hcards)));
		//可能牌 客端带花色十六进制字串牌值转换到服端无花色十进制整型牌值
		$pcards = self::cardsDec(self::cardsToNew(array_values(array_merge($hcards, $bcards))));
		//地主牌 客端带花色十六进制字串牌值转换到服端无花色十进制整型牌值
		$bcards = self::cardsDec(self::cardsToNew(array_values($bcards)));
		//地主牌 没有大小王的服端无花色十进制字串牌值，用于抓底任务运算
		$bcards_ = array_diff($bcards, array(14, 15)); foreach ( $bcards_ as $k => $v ) { $bcards_[$k] = ($v>9?'':'0').$v; }
		//误导模式 使用最佳牌型
		$types = self::cardsFitTypes($pcards, 2); //不使用必有牌($hcards)，而使用可能牌
		//找出误导牌型
		krsort($types);
		$types_ = array();
		foreach ( $types as $t => $cards )
		{
			$tlv = str_split($t, 2);
			if ( isset($types_[$tlv[0]]) ) {
				unset($types[$t]);//只要最高牌值的同一牌型
			} else {
				$types_[$tlv[0]] = 1;
			}
		}
		// if ( $types ) { $keys = array_keys($types); shuffle($keys); $random = array(); foreach ($keys as $key) { $random[$key] = $types[$key]; }; $types = $random; }
		$debug && debug("ERR牌局误导任务 生成阶段 全部牌型 ".json_encode($types));
		//整理牌局任务列表
		$tlistbak = $tlist;
		foreach ( $tlist as $id => $task )
		{
			$conds = array();
			foreach ( $task['conds'] as $tconfid )
			{
				if ( $tconfid > 1 && $tconfid < 28 && !in_array($tconfid, array(4, 5, 8, 27)) ) {//牌值任务: 值为初始化0 有牌型配置 有运算配置
					$conds[] = array('id'=>$tconfid, 'name'=>$tconf[$tconfid]['name'], 'value'=>0, 'ctype'=>$confidCtype[$tconfid], 'ccomp'=>$confidCcomp[$tconfid]);
				} elseif( in_array($tconfid, array(4, 5, 8, 27)) ) { //有王任务: 值为小王牌值(便于进行大小单王或王炸时的值运算) 有牌型配置 有运算配置
					$conds[] = array('id'=>$tconfid, 'name'=>$tconf[$tconfid]['name'], 'value'=>14, 'ctype'=>$confidCtype[$tconfid], 'ccomp'=>$confidCcomp[$tconfid]);
				} elseif( $tconfid == 28 ) {	//倍率任务: 值为随机倍率 无牌型配置 无运算配置
					$conds[] = array('id'=>$tconfid, 'name'=>$tconf[$tconfid]['name'], 'value'=>mt_rand(4,12)*100);
				} else {//其它任务: 值为初始化0 无牌型配置 无运算配置
					$conds[] = array('id'=>$tconfid, 'name'=>$tconf[$tconfid]['name'], 'value'=>0);
				}
			}
			$tlist[$id]['conds'] = $conds;
		}
		$debug && debug("ERR牌局误导任务 生成阶段 条件规整 ".json_encode($tlist));
		//找出可完成的任务 此任务列表已进行过渠道房间用户筛选
		$plist = array();
		foreach ( $types as $t => $cards )
		{
			$isB = 0;
			$tlv = str_split($t, 2);
			$ctyp = $tlv[0];//牌型 00无效01单张牌02对牌03三条04三带单05三带对06顺子07连对08飞机09飞机单10飞机对11四带单12四带对88//硬炸弹99王炸
			if ( $ctyp == '01' ) continue;//单张牌不做运算，只适用于cardsFitTypes
			$clen = intval($tlv[1]);  //牌数 实际总长度数字
			$cval = intval($tlv[2]);  //牌值 1 2 3 4 5 6 7 8 9 10 11 12 13 14 15
			$cfac = $raCvals[$tlv[2]];//牌面 3 4 5 6 7 8 9 10 J Q K A 2 小王 大王
			if ( in_array($ctyp, array('00','11','12')) ) { //无效牌/四带单/四带对不做为牌局任务
				$isB = 1;continue;//直接pass
			} else {
				//误导模式牌数牌值故意抬升
				switch ( $ctyp )
				{
					case '01': $cval++;	break;	//单张牌 	牌值加1
					case '02': $cval++; break;	//对子牌 	牌值加1
					case '03': $cval++; break;	//三条 	牌值加1
					case '04': $cval++; break;	//三带单 	牌值加1
					case '05': $cval++; break;	//三带对 	牌值加1
					case '06': $clen++; break;	//顺子 	牌数加1
					case '07': $clen+=2;break;	//双顺子 	牌数加2
					case '08': $clen+=3;break;	//飞机头 	牌数加3
					case '09': $clen+=4;break;	//飞机单 	牌数加4
					case '10': $clen+=5;break;	//飞机对 	牌数加5
					case '88': $cval++; break;	//软硬赖炸弹 	牌值加1
					case '99': $cval++; break;	//王炸 	牌值加1
					default: break;		//其它 	牌数不变	牌值不变
				}
				if ( $cval > 12 || $clen > 16 ) {
					$isB = 1;continue;//直接pass
				} else {
					$tlv[2] = ($cval>9?'':'0').$cval;
					$cfac= $raCvals[$tlv[2]];
				}
			}
			foreach ( $tlist as $id => $task )
			{
				$ccount = 0;
				foreach ( $task['conds'] as $k => $cond )
				{
					//非牌型任务 可能完成
					if ( !isset($cond['ctype']) ) {
						$task['name'][] = sprintf($cond['name'], $cond['value']);
						$ccount++;
						continue;
					}
					//底牌抓到某单张牌任务
					if ( $cond['id'] == 2 ) {
						$cs = array_diff($vals, $bcards_);
						$cval_ = $cs[array_rand($cs)];
						$cond['value'] = intval($cval_);
						$task['name'][] = sprintf($cond['name'], $raCvals[$cval_]);
						$task['conds'][$k] = $cond;
						$ccount++;
						continue;
					}
					//底牌抓到某对牌任务
					elseif ( $cond['id'] == 3 ) {
						$cs = array_diff($vals, $bcards_);
						$cval_ = $cs[array_rand($cs)];
						$cond['value'] = intval($cval_);
						$task['name'][] = sprintf($cond['name'], $raCvals[$cval_]);
						$task['conds'][$k] = $cond;
						$ccount++;
						continue;
					}
					//底牌抓到某王任务
					elseif ( $cond['id'] == 4 ) {
						if ( count($bcards_) == 3 ) {
							$task['name'][] = $cond['name'];
							$ccount++;
						}
						continue;
					}
					//底牌抓到王炸任务
					elseif ( $cond['id'] == 5 ) {
						if ( count($bcards_) != 1 ) {
							$task['name'][] = $cond['name'];
							$ccount++;
						}
						continue;
					}
					if ( $isB ) {
						continue;
					}
					//牌型不匹配 因为有重复运算因素 所以无法识别 故不进行比较运算
					if ( strpos($cond['ctype'], $ctyp) === false ) {
						continue;
					}
					//牌型匹配时 进行比较运算
					switch ($cond['ccomp'])
					{
					case 'v=':
						if ( $cval >= $cond['value'] ) {
							$cond['value'] = $cval;
							$name = $cfac;
						} else {
							$ccount--;
						}
						break;
					case 'v>':
						if ( $cond['ctype'] == '88_99' ) {
							$ccount--;
						} elseif ( $cval >= $cond['value'] ) {
							$cond['value'] = $cval;
							$name = $cfac;
						} else {
							$ccount--;
						}
						break;
					case 'l=':
						$cond['value'] = $clen;
						$name = $clen;
						break;
					case 'l>':
						$cond['value'] = $clen;
						$name = $clen;
						break;
					case 'g=':
						switch ($ctyp) {
							case '07': $clen = intval($clen/2); break;//双顺子
							case '08': $clen = intval($clen/3); break;//飞机
							case '09': $clen = intval($clen/4); break;//飞机单
							default:   $clen = intval($clen/5); break;//飞机对
						}
						$cond['value'] = $clen;
						$name = $clen;
						break;
					case 'g>':
						switch ($ctyp) {
							case '07': $clen = intval($clen/2); break;//双顺子
							case '08': $clen = intval($clen/3); break;//飞机
							case '09': $clen = intval($clen/4); break;//飞机单
							default:   $clen = intval($clen/5); break;//飞机对
						}
						$cond['value'] = $clen;
						$name = $clen;
						break;
					default://''
						if ( isset($types_[$ctyp]) ) {
							$ccount--;
						}
						break;
					}
					$task['name'][] = sprintf($cond['name'], isset($name) ? $name : $cond['value']);
					$task['conds'][$k] = $cond;
					unset($name);
					$ccount++;
				}
				if ( $ccount == count($task['conds']) ) {
					if ( $ccount == 2 && $task['name'][1] == '赢得胜利' ) $task['name'][1] = '胜利';
					$task['name'] = join((ISLOCAL || ISTESTS) ? '且' : '并', $task['name']);
					$task['is_new'] = $task['is_done'] = 0;
					$plist[$id] = $task;
					unset($tlist[$id]);
					$debug && debug("ERR牌局误导任务 生成阶段 满足条件 ".$task['name']." ".json_encode($task));
				} else {
					$debug && debug("ERR牌局误导任务 生成阶段 不够条件 ".$task['conds'][0]['name'].(isset($task['conds'][1]['name'])?(((ISLOCAL || ISTESTS) ? '且' : '并').$task['conds'][1]['name']):'')." ".json_encode($task));
				}
			}
			if ( count($plist) > 3 ) break;//直接pass
		}
		//随机出某个任务
		$total = 0;
		foreach ( $plist as $id => $task )
		{
			$total+=$task['prob'];
		}
		$needle = mt_rand(0, $total);
		$step = 0;
		foreach ( $plist as $id => $task )
		{
			if ( $needle > $step && $needle <= $task['prob'] + $step ) {
				return $task;
			}
			$step += $task['prob'];
		}
		return array();
		$raCvals = array_keys($raCvals);
		foreach ( $raCvals as $k => $v ) {
			$raCvals[$k] = intval($v);
		}
		$cards = array_diff($raCvals, $pcards);
		if ( $cards ) {
			foreach ( $cards as $k => $card ) {
				$cards[$k] = '0'.dechex($card);
			}
			$cardsold = $cards;
			$cards = self::cardsToOld($cards);
			if (!isset($cards[0])) {
				echo date("Y-m-d H:i:s").json_encode($cardsold)." ".json_encode($cards)."  \n";
			}
			$bcards = array($cards[0]); unset($cards[0]);
			if ( $cards ) {
				$cards = array_values($cards);
				return self::getProbTask($cards, $bcards, $tlistbak, $tconf, $debug);
			}
		}
		return array();
	}

	//检测牌局任务完成情况 底牌
	// return 组合后的任务数据
	static function checkTaskBcards( $cards, $task, $debug=0 )
	{
		if ( ! $cards || ! is_array($cards) || ! is_array($task) || count($cards) != 3 ) return false;
		//原牌翻译、获取低位十进制、倒序
		$cards = self::cardsDec(array_values(self::cardsToNew($cards)));
		$ccount = $is_new = 0;
		$debug && debug("ERR牌局任务 抓底检测 catds=".json_encode($cards)." task=".json_encode($task));
		foreach ( $task['conds'] as $k => $cond )
		{
			if ( isset($cond['is_done']) && $cond['is_done'] ) {
				$ccount++;
				continue;
			}
			if ( $cond['id'] == 1 ) {
				$task['conds'][$k]['is_done'] = $is_new = 1;
				$ccount++;
				continue;
			}
			if ( !isset($cond['ctype']) ) continue;
			if ( !($cond['id'] > 1 && $cond['id'] < 6) || !in_array($cond['ctype'], array('01','02','99')) ) continue;
			switch ( $cond['ctype'] )
			{
			case '01':
				switch ( $cond['ccomp'] )
				{
				case 'v>':
					foreach ( $cards as $card )
					{
						if ( intval($cond['value']) > $card ) continue;
						$task['conds'][$k]['is_done'] = $is_new = 1;
						$ccount++;
						break;
					}
					break;
				default://v=
					if ( in_array(intval($cond['value']), $cards) ) {
						$task['conds'][$k]['is_done'] = $is_new = 1;
						$ccount++;
					}
					break;
				}
				break;
			case '02':
				switch ( $cond['ccomp'] )
				{
				default://v=
					if ( in_array(intval($cond['value']), $cards) && ($cards[0] == $cards[1] || $cards[1] == $cards[2]) ) {
						$task['conds'][$k]['is_done'] = $is_new = 1;
						$ccount++;
					}
					break;
				}
				break;
			case '99':
				switch ( $cond['ccomp'] )
				{
				default://v=
					if ( count(array_intersect(array(14, 15), $cards)) == 2 ) {
						$task['conds'][$k]['is_done'] = $is_new = 1;
						$ccount++;
					}
					break;
				}
				break;
			default:
				break;
			}
		}
		if ( $ccount == count($task['conds']) ) $task['is_done'] = $is_new = 1;
		$task['is_new'] = $is_new;
		$debug && debug("ERR牌局任务 抓底结果 catds=".json_encode($cards)." task=".json_encode($task));
		return $task;
	}

	//检测牌局任务完成情况 打牌
	// return 组合后的任务数据
	static function checkTaskDone( $cards, $task, $debug=0 )
	{
		if ( ! $cards || ! is_array($cards) || ! $task || ! is_array($task) ) return false;
		if ( isset($task['is_done']) && $task['is_done'] ) return $task;
		$cardc = self::cardsCheck(self::cardsDec(array_values(self::cardsToNew($cards))));
		$cardc['l'] = intval($cardc['l']);
		$cardc['v'] = intval($cardc['v']);
		$ccount = $is_new = 0;
		$debug && debug("ERR牌局任务 打牌检测 catds=".json_encode($cards)." task=".json_encode($task)." cardc=".json_encode($cardc));
		foreach ( $task['conds'] as $k => $cond )
		{
			if ( isset($cond['is_done']) && $cond['is_done'] ) {
				$ccount++;
				continue;
			}
			if ( !isset($cond['ctype']) ) continue;
			if ( strpos($cond['ctype'], $cardc['t']) === false ) continue;
			if ( !($cond['id'] > 5 && $cond['id'] < 28) ) continue;
			switch ($cond['ccomp'])
			{
			case 'v=':
				if ( $cond['value'] == $cardc['v'] ) {
					$task['conds'][$k]['is_done'] = $is_new = 1;
					$ccount++;
				}
				break;
			case 'v>':
				if ( $cond['value'] <= $cardc['v'] ) {
					$task['conds'][$k]['is_done'] = $is_new = 1;
					$ccount++;
				}
				break;
			case 'l=':
				if ( $cond['value'] == $cardc['l'] ) {
					$task['conds'][$k]['is_done'] = $is_new = 1;
					$ccount++;
				}
				break;
			case 'l>':
				if ( $cond['value'] <= $cardc['l'] ) {
					$task['conds'][$k]['is_done'] = $is_new = 1;
					$ccount++;
				}
				break;
			case 'g=':
				$num = array_intersect(explode('_', $cond['ctype']), array('08','09','10')) ? 3 : 2;
				if ( $cond['value'] == intval($cardc['l'] / $num) ) {
					$task['conds'][$k]['is_done'] = $is_new = 1;
					$ccount++;
				}
				break;
			case 'g>':
				$num = array_intersect(explode('_', $cond['ctype']), array('08','09','10')) ? 3 : 2;
				if ( $cond['value'] <= intval($cardc['l'] / $num) ) {
					$task['conds'][$k]['is_done'] = $is_new = 1;
					$ccount++;
				}
				break;
			default://''
				$task['conds'][$k]['is_done'] = $is_new = 1;
				$ccount++;
				break;
			}
		}
		if ( $ccount == count($task['conds']) ) $task['is_done'] = $is_new = 1;
		$task['is_new'] = $is_new;
		$debug && debug("ERR牌局任务 打牌结果 catds=".json_encode($cards)." task=".json_encode($task)." cardc=".json_encode($cardc));
		return $task;
	}


	//解析牌组  返回牌型标识、牌型长度、最大牌值、扮演牌组、实际牌组、赖子角色
	//$cards 	array 	旧版 带花色十六进制牌组
	//$jokto 	array 	旧版 牌组内的赖子牌按顺序扮演的牌值
	//$joker 	string 	旧版 赖子牌值
	//return 	array 	array('t'=>'01', 'l'=>'01', 'v'=>'09', 'plays'=>('13','23','33','14','24','34','16','15'), 'reals'=>(...), 'jokto'=>array('2', '1')
	//			牌型标识	牌型长度	最大牌值	扮演牌组（带花色十六进制旧牌）		实际牌组	赖子角色
	static function cardsParse( $plays, $jokto=array(), $joker='' )
	{
		$void = array('t'=>'00','l'=>'00','v'=>'00','plays'=>array(),'reals'=>array(),'jokto'=>array());
		if ( ! $plays || ! is_array($plays) ) return $void;
		$isJoke = intval($jokto && is_array($jokto) && $joker);
		if ( $isJoke ) {
			$jokerRels = array();
			foreach ( $plays as $k => $v )
			{
				if ( !$jokto || $v[1] !== $joker ) continue;
				$new = ($v[0]+5).array_shift($jokto);	//目标牌面增设赖子花色
				$jokerRels[$new] = $v;					//目标牌面关联赖子牌面
				$plays[$k] = $new;						//替换后的打出目标牌组
			}
		}
		$cardsold = self::cardsSort($plays, 1);//原牌重新倒序
		$cardsnew = self::cardsToNew($cardsold);//旧牌转义新牌
		$newold = array_combine($cardsnew, $cardsold);//关联新牌旧牌
		$res = self::cardsCheck(self::cardsDec($cardsnew));
		if ( ! $res['t'] ) return $void;
		$plays = array();
		foreach ( $res['cs'] as $k => $v )
		{
			foreach ( $newold as $kk => $vv )
			{
				$val = hexdec($kk) % 16;
				if ( $val != $v ) continue;
				$plays[]=$vv;
				unset($newold[$kk]);
				break;
			}
		}
		if ( count($newold) ) return $void;
		$reals = $plays;
		$jokto = array();
		if ( $isJoke ) {
			foreach ( $plays as $k => $v )
			{
				if ( $v[0] < 6 ) continue;
				$old = $jokerRels[$v];
				$jokto[] = $v[1];				//替代牌值目标(有可能会是自身)
				$plays[$k] = ($v[0]-5).$v[1];	//打出目标牌组(已抹去赖子花色)
				$reals[$k] = $old;				//所用实际牌组(使用赖子原牌面)
			}
		}
		if ( $res['t'] == '88' ) {
			$joktonum = count($jokto);
			if ( $joktonum == 4 ) {
				$res['t'] = '89';
			} elseif ( $joktonum ) {
				$res['t'] = '87';
			}
		}
		return array('t'=>$res['t'],'l'=>$res['l'],'v'=>$res['v'],'plays'=>$plays,'reals'=>$reals,'jokto'=>$jokto);
	}

	//检查牌型
	//$cards 	array 	新版无花色十进制牌值
	//return 	array 	array('t'=>'01', 'l'=>'01', 'v'=>'09', 'array'=>(3,3,3,4,4,4,6,5)
	//						牌型标识		牌型长度		最大牌值		牌面序列
	//00 无效牌 01 单张牌 02 对子牌 03 三不带 04 三带单 05 三带对 06 单顺子 07 双顺子
	//08 飞机头 09 飞机单 10 飞机对 11 四带单 12 四带对 87 硬炸弹 88 硬炸弹 89 赖子炸 99 双王炸
	static function cardsCheck( $cards )
	{
		if ( !$cards || ! is_array($cards) || count($cards) > 20 ) return array('t'=>'00','l'=>'00','v'=>'00','cs'=>$cards);
		rsort($cards);
		$len = count($cards);
		$value = $cards[0];
		$array = $cards;
		switch ( $len )
		{
			case 1 : {//单张牌
				$type = '01';		//单张牌
				break;
			}
			case 2 : {//对子牌//双王炸//无效牌
				if ( $cards[0] == $cards[1] ) {
					$type = '02';	//对子牌
				} elseif ( $cards[0] == 15 && $cards[1] == 14 ) {
					$type = '99';	//双王炸
				} else {
					$type = '00';	//无效牌
				}
				break;
			}
			case 3 : {//三不带//无效牌
				if ( $cards[0] == $cards[1] && $cards[1] == $cards[2] ) {
					$type = '03';	//三不带
				} else {
					$type = '00';	//无效牌
				}
				break;
			}
			case 4 : {//硬炸弹/三带单//无效牌
				if ( $cards[0] == $cards[1] && $cards[1] == $cards[2] && $cards[2] == $cards[3] ) {
					$type = '88';	//硬炸弹
				} elseif ( $cards[0] == $cards[1] && $cards[1] == $cards[2] ) {
					$type = '04';	//三带单，单张牌较小
				} elseif ( $cards[1] == $cards[2] && $cards[2] == $cards[3] ) {
					$type = '04';	//三带单，单张牌较大
					$value = $cards[1];
					$array = array($cards[1],$cards[2],$cards[3],$cards[0]);
				} else {
					$type = '00';	//无效牌
				}
				break;
			}
			case 5 : {//三带对//单顺子//无效牌
				if ( $cards[0] == $cards[1] && $cards[1] == $cards[2] && $cards[3] == $cards[4] ) {
					$type = '05';	//三带对，单张牌较小
				} elseif ( $cards[0] == $cards[1] &&  $cards[2] == $cards[3] && $cards[3] == $cards[4] ) {
					$type = '05';	//三带对，单张牌较大
					$value = $cards[2];
					$array = array($cards[2],$cards[3],$cards[4],$cards[0],$cards[1]);
				} elseif ( $val_ = self::cardsCheckDanShun($cards) ) {
					$type = '06';	//单顺子
					$value = $val_['v'];
					$array = $val_['cs'];
				} else {
					$type = '00';	//无效牌
				}
				break;
			}
			case 6 : {//单顺子//双顺子//飞机头/四带单//无效牌
				if ( $val_ = self::cardsCheckDanShun($cards) ) {
					$type = '06';	//单顺子
					$value = $val_['v'];
					$array = $val_['cs'];
				} elseif ( $val_ = self::cardsCheckLianDui($cards) ) {
					$type = '07';	//双顺子
					$value = $val_['v'];
					$array = $val_['cs'];
				} elseif ( $val_ = self::cardsCheckSanShun($cards) ) {
					$type = '08';	//飞机头
					$value = $val_['v'];
					$array = $val_['cs'];
				} elseif ( $val_ = self::cardsCheckSiDaiDan($cards) ) {
					$type = '11';	//四带单
					$value = $val_['v'];
					$array = $val_['cs'];
				} else {
					$type = '00';	//无效牌
				}
				break;
			}
			case 7 : {//单顺子//无效牌
				if ( $val_ = self::cardsCheckDanShun($cards) ) {
					$type = '06';	//单顺子
					$value = $val_['v'];
					$array = $val_['cs'];
				} else {
					$type = '00';	//无效牌
				}
				break;
			}
			case 8 : {//单顺子//双顺子/飞机单/四带对//无效牌
				if ( $val_ = self::cardsCheckDanShun($cards) ) {
					$type = '06';	//单顺子
					$value = $val_['v'];
					$array = $val_['cs'];
				} elseif ( $val_ = self::cardsCheckLianDui($cards) ) {
					$type = '07';	//双顺子
					$value = $val_['v'];
					$array = $val_['cs'];
				} elseif ( $val_ = self::cardsCheckSiDaiDui($cards) ) {//44443333
					$type = '12';	//四带对
					$value = $val_['v'];
					$array = $val_['cs'];
				} elseif ( $val_ = self::cardsCheckFeiJiDan($cards) ) {
					$type = '09';	//飞机单
					$value = $val_['v'];
					$array = $val_['cs'];
				} else {
					$type = '00';	//无效牌
				}
				break;
			}
			default: {//超过8张	//单顺子//双顺子//飞机头/飞机单/飞机对//无效牌
				if ( $val_ = self::cardsCheckDanShun($cards) ) {
					$type = '06';	//单顺子
					$value = $val_['v'];
					$array = $val_['cs'];
				} elseif ( $val_ = self::cardsCheckLianDui($cards) ) {
					$type = '07';	//双顺子
					$value = $val_['v'];
					$array = $val_['cs'];
				} elseif ( $val_ = self::cardsCheckSanShun($cards) ) {//666555444333
					$type = '08';	//飞机头
					$value = $val_['v'];
					$array = $val_['cs'];
				} elseif ( $val_ = self::cardsCheckFeiJiDan($cards) ) {
					$type = '09';	//飞机单
					$value = $val_['v'];
					$array = $val_['cs'];
				} elseif ( $val_ = self::cardsCheckFeiJidui($cards) ) {
					$type = '10';	//飞机对
					$value = $val_['v'];
					$array = $val_['cs'];
				} else {
					$type = '00';	//无效牌
				}
				break;
			}
		}
		$len = $len > 9 ? strval($len) : ('0'.$len);
		$value = $value > 9 ? strval($value) : ('0'.$value);
		return array('t'=>$type,'l'=>$len,'v'=>$value,'cs'=>$array);
	}

	//检查牌型: 单顺
	static function cardsCheckDanShun( $cards )
	{
		if ( ! $cards || ! is_array($cards) || array_intersect(array(13,14,15), $cards) ) return false;
		$num = count($cards);
		if ( $num < 5 || $num > 12 ) return false;
		foreach ( $cards as $k => $v )
		{
			if ( isset($cards[$k+1]) && $v-$cards[$k+1] != 1 ) return false;
		}
		return array('v'=>$cards[0], 'cs'=>$cards);
	}

	//检查牌型: 连对
	static function cardsCheckLianDui( $cards )
	{
		if ( ! $cards || ! is_array($cards) || array_intersect(array(13,14,15), $cards) ) return false;
		$num = count($cards);
		if ( $num < 6 || $num % 2 ) return false;
		rsort($cards);
		foreach ( $cards as $k => $v )
		{
			if ( $k%2 == 1 ) {
				if ( isset($cards[$k+1]) && $cards[$k+1] + 1 != $v ) return false;
			} else {
				if ( isset($cards[$k+1]) && $cards[$k+1] != $v ) return false;
			}
		}
		return array('v'=>$cards[0], 'cs'=>$cards);
	}

	//检查牌型: 三顺=飞机头
	static function cardsCheckSanShun( $cards )
	{
		if ( ! $cards || ! is_array($cards) || array_intersect(array(13,14,15), $cards) ) return false;
		$num = count($cards);
		if ( $num < 6 || $num%3 ) return false;
		rsort($cards);
		foreach ( $cards as $k => $v )
		{
			if ( $k%3 == 2 ) {
				if ( isset($cards[$k+1]) && $cards[$k+1] + 1 != $v ) return false;
			} else {
				if ( isset($cards[$k+1]) && $cards[$k+1] != $v ) return false;
			}
		}
		return array('v'=>$cards[0], 'cs'=>$cards);
	}

	//检查牌型: 飞机单
	static function cardsCheckFeiJiDan( $cards )
	{
		if ( ! $cards || ! is_array($cards) ) return false;
		$num = count($cards);
		if ( $num < 8 || $num % 4 ) return false;
		$sanshun = $danpai = array();
		$acv = array_count_values($cards);
		krsort($acv);
		foreach ( $acv as $k => $v )
		{
			if ( $v == 4 ) {
				$sanshun[] = $k; $sanshun[] = $k; $sanshun[] = $k;
				$danpai[] = $k;
			} elseif ( $v == 3 ) {
				$sanshun[] = $k; $sanshun[] = $k; $sanshun[] = $k;
			} elseif ( $v == 2 ) {
				$danpai[] = $k; $danpai[] = $k;
			} elseif ( $v == 1 ) {
				$danpai[] = $k;
			} else {
				return false;
			}
		}
		if ( count($sanshun)/3 != $num/4 || count($danpai) != $num/4 ) return false;
		$res = self::cardsCheckSanShun($sanshun);
		if ( ! $res ) return false;
		return array('v'=>$res['v'], 'cs'=>array_merge($sanshun, $danpai));
	}

	//检查牌型: 飞机对
	static function cardsCheckFeiJidui( $cards )
	{
		if ( ! $cards || ! is_array($cards) ) return false;
		$num = count($cards);
		if ( $num < 10 || $num % 5 ) return false;
		$duipai = $sanshun = array();
		$acv = array_count_values($cards);
		krsort($acv);
		foreach ( $acv as $k => $v )
		{
			if ( $v == 4 ) {
				$duipai[]=$k; $duipai[]=$k; $duipai[]=$k; $duipai[]=$k;
			} elseif ( $v == 3 ) {
				$sanshun[]=$k; $sanshun[]=$k; $sanshun[]=$k;
			} elseif ( $v == 2 ) {
				$duipai[]=$k; $duipai[]=$k;
			} else {
				return false;
			}
		}
		if ( count($sanshun)/3 != $num/5 || count($duipai)/2 != $num/5 ) return false;
		$res = self::cardsCheckSanShun($sanshun);
		if ( ! $res ) return false;
		return array('v'=>$res['v'], 'cs'=>array_merge($sanshun, $duipai));
	}

	//检查牌型: 四带单
	static function cardsCheckSiDaiDan( $cards )
	{
		if ( ! is_array($cards) || count($cards) != 6 ) return false;
		$sitiao = $danpai = array();
		$acv = array_count_values($cards);
		foreach ( $acv as $k => $v )
		{
			if ( $v == 4 ) {
				$sitiao[]= $k; $sitiao[]= $k; $sitiao[]= $k; $sitiao[]= $k;
			} elseif ( $v == 2 ) {
				$danpai[]= $k; $danpai[]= $k;
			} elseif ( $v == 1 ) {
				$danpai[]= $k;
			} else {
				return false;
			}
		}
		if ( count($sitiao) != 4 || count($danpai) != 2 ) return false;
		return array('v'=>$sitiao[0], 'cs'=>array_merge($sitiao, $danpai));
	}

	//检查牌型: 四带对
	static function cardsCheckSiDaiDui( $cards )
	{
		if ( ! is_array($cards) || count($cards) != 8 ) return false;
		$sitiao = $duipai = array();
		$acv = array_count_values($cards);
		krsort($acv);
		foreach ( $acv as $k => $v )
		{
			if ( $v == 4 && ! $sitiao ) {
				$sitiao[]= $k; $sitiao[]= $k; $sitiao[]= $k; $sitiao[]= $k;
			} elseif ( $v == 4 ) {
				$duipai[]= $k; $duipai[]= $k; $duipai[]= $k; $duipai[]= $k;
			} elseif ( $v == 2 ) {
				$duipai[]= $k; $duipai[]= $k;
			} else {
				return false;
			}
		}
		if ( count($sitiao) != 4 || count($duipai) != 4 ) return false;
		return array('v'=>$sitiao[0], 'cs'=>array_merge($sitiao, $duipai));
	}

	//检查牌型: 四条 //硬炸弹
	static function cardsCheckSiTiao( $cards )
	{
		if ( ! is_array($cards) || count($cards) != 4 || !($cards[0] == $cards[1] && $cards[1] == $cards[2] && $cards[2] == $cards[3]) ) return false;
		return array('v'=>$cards[0], 'cs'=>$cards);
	}

	//检查牌型: 双王炸
	static function cardsCheckHuojian( $cards )
	{
		if ( ! is_array($cards) || count($cards) != 2 || !($cards[0] == 15 && $cards[1] == 14) ) return false;
		return array('v'=>$cards[0], 'cs'=>$cards);
	}

	//计算权重
	static function cardsGetLevel( $type, $len, $value )
	{
		$level = 1;
		switch ( $type )
		{
		case 99:
			$level = 5;
			break;
		case 89:
			$level = 5;
			break;
		case 88:
			$level = 5;
			break;
		case 87:
			$level = 5;
			break;
		case 12:
			$level = $value > 8 ? 5 : 4;
			break;
		case 11:
			$level = $value > 9 ? 5 : 4;
			break;
		case 10:
			$level = $len > 10 ? 5 : ( $value > 7 ? 5 : ( $value > 4 ? 4 : 3 ) );
			break;
		case  9:
			$level = $len > 8 ? 5 : ( $value > 8 ? 5 : ( $value > 5 ? 4 : 3 ) );
			break;
		case  8:
			$level = $len > 6 ? 5 : ( $value > 5 ? 5 : ( $value > 5 ? 4 : 3 ) );
			break;
		case  7:
			$level = $len > 8 ? 5 : ( $len > 6 ? 4 : ( $value > 9 ? 5 : ( $value > 6 ? 4 : ( $value > 3 ? 3 : ( $value > 0 ? 2 : 1 ) ) ) ) );
			break;
		case  6:
			$level = ( $len > 8 || $value == 11 ) ? 5 : ( $len > 7 || $value == 10 ? 4 : ( $len > 5 || $value > 7 ? 3 : ( $value > 5 ? 2 : 1 ) ) );
			break;
		case  5:
			$level = $value > 10 ? 5 : ( $value > 8 ? 4 : ( $value > 5 ? 3 : ( $value > 2 ? 2 : 1 ) ) );
			break;
		case  4:
			$level = $value > 10 ? 5 : ( $value > 8 ? 4 : ( $value > 5 ? 3 : ( $value > 2 ? 2 : 1 ) ) );
			break;
		case  3:
			$level = $value > 10 ? 5 : ( $value > 8 ? 4 : ( $value > 5 ? 3 : ( $value > 2 ? 2 : 1 ) ) );
			break;
		case  2:
			$level = $value > 11 ? 5 : ( $value > 9 ? 4 : ( $value > 6 ? 3 : ( $value > 3 ? 2 : 1 ) ) );
			break;
		case  1:
			$level = $value > 12 ? 5 : ( $value > 10 ? 4 : ( $value > 7 ? 3 : ( $value > 4 ? 2 : 1 ) ) );
			break;
		default:
			break;
		}
		return $level;
	}

	//组装所有牌型 可重复用牌 各种主牌型 含四带牌型 留最小带牌(留最佳带牌)
	static function cardsAllTypes( $cards )
	{
		if ( ! $cards ) return array();
		$types = array();
		//01 单张牌
		$danpai_r = $cards;
		sort($danpai_r);						//1 单张牌[有重复][正排序]
		$counts = array_count_values($cards);	//1 单张牌[在下标][倒排序]
		$danpai = array_keys($counts);			//1 单张牌[不重复][倒排序]
		foreach ( $danpai as $k => $v )
		{
			$val = $v>9?$v:('0'.$v);
			$types['0101'.$val] = array($v);					//01 单张牌牌型
		}
		//99 双王炸
		if ( in_array(15, $danpai) && in_array(14, $danpai) )
		{
			$val = '14';
			$types['9902'.$val] = array(15, 14);				//99 双王炸牌型
			$danpai = array_diff($danpai, array(15, 14));
		}
		$duipai = $duipai_r = $satiao = $sitiao = array();
		foreach ( $counts as $k => $v )
		{
			if 		 ( $v == 4 ) {
				$sitiao[$k] = 1;			//4 四条[在下标][倒排序]
				$satiao[$k] = 1;			//3 三条[在下标][倒排序]
				$duipai[$k] = 2;			//2 对牌[在下标][倒排序]
			} elseif ( $v == 3 ) {
				$satiao[$k] = 1;			//3 三条[在下标][倒排序]
				$duipai[$k] = 1;			//2 对牌[在下标][倒排序]
			} elseif ( $v == 2 ) {
				$duipai[$k] = 1;			//2 对牌[在下标][倒排序]
			} elseif ( $v == 1 ) {
				//
			} else 				 {
				return array();
			}
		}
		//02 对牌
		foreach ( $duipai as $k => $v )			//2 对牌[在下标][倒排序]
		{
			$val = $k>9?$k:('0'.$k);
			$types['0202'.$val] = array($k,$k);					//02 对牌牌型
			$duipai_r[] = $k;
			if ( $v == 2 ) $duipai_r[] = $k;	//2 对牌[有重复][倒排序]
		}
		$duipai = array_keys($duipai);			//2 对牌[不重复][倒排序]
		if ( $duipai_r ) sort($duipai_r);		//2 对牌[有重复][正排序]
		//03 三不带 //04 三带单 //05 三带对
		foreach ( $satiao as $k => $v )			//3 三条[在下标][倒排序]
		{
			$val = $k>9?$k:('0'.$k);
			$types['0303'.$val] = array($k,$k,$k);				//03 三不带牌型
			foreach ( $danpai_r as $vv )
			{
				if ( isset($types['0404'.$val]) ) break;//最小带
				if ( $k == $vv ) continue;//重复
				$types['0404'.$val] = array($k,$k,$k,$vv);		//04 三带单张牌型
			}
			foreach ( $duipai_r as $vv )
			{
				if ( isset($types['0505'.$val]) ) break;//最小带
				if ( $k == $vv ) continue;//重复
				$types['0505'.$val] = array($k,$k,$k,$vv,$vv);	//05 三带对牌型
			}
		}
		$satiao = array_keys($satiao);			//3 三条[不重复][倒排序]
		//06 单顺
		$types06 = self::cardsCutShunzi($danpai, 5);//array('1006'=>array(10,9,8,7,6,5),'1005'=>array(10,9,8,7,6),...)
		foreach ( $types06 as $k => $v )
		{
			$len = count($v);
			$len = $len > 9 ? $len :('0'.$len);
			$val = floor($k/100);
			$val = $val > 9 ? $val :('0'.$val);
			$types['06'.$len.$val] = $v;						//06 单顺牌型
		}
		//07 连对
		$types07 = self::cardsCutShunzi($duipai, 3);//array('1004'=>array(10,9,8,7),'1003'=>array(10,9,8),...)
		foreach ( $types07 as $k => $v )
		{
			$v = array_merge($v,$v);
			rsort($v);
			$len = count($v);
			$len = $len > 9 ? $len :('0'.$len);
			$val = floor($k/100);
			$val = $val > 9 ? $val :('0'.$val);
			$types['07'.$len.$val] = $v;						//07 双顺牌型
		}
		//08 飞机头 //09 飞机单 //10 飞机对
		$types08 = self::cardsCutShunzi($satiao, 2);//array('1003'=>array(10,9,8),'1002'=>array(10,9),...)
		foreach ( $types08 as $k => $v )
		{
			$len = count($v);//2
			$v = array_merge($v,$v,$v);//10,9,10,9,10,9
			rsort($v);//10,10,10,9,9,9
			$val = floor($k/100);//10
			$val = $val > 9 ? $val :('0'.$val);//10				//08 飞机头牌型
			$types['08'.($len > 3 ? $len*3 :('0'.$len*3)).$val] = $v;//'080610'=>array(10,10,10,9,9,9)
			$types09 = self::cardsCombine($v, $danpai_r, $len);//array('10101099943'=>array(10,10,10,9,9,9,4,3),...)
			foreach ( $types09 as $kk => $vv )
			{
				$len_ = count($vv);
				$lenval = ($len_>9?$len_:('0'.$len_)).$val;
				if ( isset($types['09'.$lenval]) ) break;//最小带
				$count = array_count_values($vv);
				rsort($count);
				//排除张数过多: 	444333-33(N)
				if ( reset($count) > 4 ) continue;
				//排除双王炸双王: 	444333-1413(N)
				if ( in_array(14, $vv) && in_array(13, $vv) ) continue;
				//排除其他牌型: 	444333-43这个牌型归属为四带对牌型
				if ( $len_ == 8 && !array_diff($count, array(4)) ) continue;
				//排除其他牌型: 	666555444-333这个牌型归属为飞机头牌型
				if ( $len_ == 12 && !array_diff($count, array(3)) ) continue;
				$types['09'.$lenval] = $vv;						//09 飞机单张牌型
			}
			$types10 = self::cardsCombine($v, $duipai_r, $len, 2);
			foreach ( $types10 as $kk => $vv )
			{
				$len_ = count($vv);
				$lenval = ($len_>9?$len_:('0'.$len_)).$val;
				if ( isset($types['10'.$lenval]) ) break;//最小带
				$count = array_count_values($vv);
				rsort($count);
				//排除张数过多: 	555444-3333(Y) 555444-4433(N)
				if ( reset($count) > 4 ) continue;
				//排除双王炸双王: 	444333-151455(N)
				if ( in_array(15, $vv) && in_array(14, $vv) ) continue;
				$types['10'.$lenval] = $vv;						//10 飞机对牌型
			}
		}
		//11 四带单 //12 四带对 //88 四不带(//硬炸弹)
		foreach ( $sitiao as $k => $v )
		{
			$val = $k>9?$k:('0'.$k);
			$v = array($k,$k,$k,$k);
			$types['8804'.$val] = $v;							//88 //硬炸弹牌型
			$types11 = self::cardsCombine($v, $danpai_r, 2);
			foreach ( $types11 as $kk => $vv )
			{
				$len_ = count($vv);
				$lenval = ($len_>9?$len_:('0'.$len_)).$val;
				if ( isset($types['11'.$lenval]) ) break;//最小带
				$count = array_count_values($vv);
				rsort($count);
				//排除张数过多: 	3333-32(N)
				if ( reset($count) > 4 ) continue;
				//排除双王炸双王: 	3333-1514(N)
				if ( in_array(15, $vv) && in_array(14, $vv) ) continue;
				$types['11'.$lenval] = $vv;						//11 四带单张牌型
			}
			$types12 = self::cardsCombine($v, $duipai_r, 2, 2);
			foreach ( $types12 as $kk => $vv )
			{
				$len_ = count($vv);
				$lenval = ($len_>9?$len_:('0'.$len_)).$val;
				if ( isset($types['12'.$lenval]) ) break;//最小带
				$count = array_count_values($vv);
				rsort($count);
				//排除张数过多: 	4444-3333(Y) 3333-3333(N)
				if ( reset($count) > 4 ) continue;
				//排除双王炸双王: 	3333-1514(N)
				if ( in_array(15, $vv) && in_array(14, $vv) ) continue;
				//排除大炸在后: 	4444-3333(Y) 3333-4444(N)
				if ( count($count) == 2 && $count[0] == 4 && $vv[0] < $vv[4] ) continue;
				$types['12'.$lenval] = $vv;						//12 四带对牌型
			}
		}
		if ( $types ) krsort($types);
		return $types;
	}

	//组装叫牌牌型 所有牌型可能(含四带二)，各个主牌之间可重复用牌，如果能带，则尽量只带最小副牌(单张牌/对牌)
	static function cardsBidTypes( $cards )
	{
		if ( ! $cards ) return array();
		$danpai = $duipai = $satiao = $sitiao = $danpai_r = $duipai_r = $satiao_r = $sitiao_u = $satiao_u = $duipai_u = $danpai_u = $sitiao_b3 = $sitiao_b2 = $sitiao_b1 = $satiao_b2 = $satiao_b1 = $duipai_b1 = array();
		$counts = array_count_values($cards);
		foreach ( $counts as $k => $v )
		{
			if 		 ( $v == 4 ) {
				$sitiao[] = $k;	//88四条
				$satiao_r[$k] = $v;	//3 三条
				$duipai_r[$k] = $v;	//2 对牌
				$danpai_r[$k] = $v;	//1 单张牌
			} elseif ( $v == 3 ) {
				$satiao[] = $k;	//3 三条
				$duipai_r[$k] = $v;	//2 对牌
				$danpai_r[$k] = $v;	//1 单张牌
			} elseif ( $v == 2 ) {
				$duipai[] = $k;	//2 对牌
				$danpai_r[$k] = $v;	//1 单张牌
			} elseif ( $v == 1 ) {
				$danpai[] = $k;	//1 单张牌
			} else 				 {
				return array();
			}
		}
		if ( $sitiao ) rsort($sitiao);//倒序
		if ( $satiao ) rsort($satiao);//倒序
		if ( $duipai ) sort($duipai);//正序
		if ( $danpai ) sort($danpai);//正序
		if ( $satiao_r ) krsort($satiao_r);
		if ( $duipai_r ) ksort($duipai_r);
		if ( $danpai_r ) ksort($danpai_r);
		$types = array();
		//99 双王炸
		if ( in_array(15,$danpai) && in_array(15,$danpai) )
		{
			$danpai = array_diff($danpai, array(15, 14));
			$types['990214'] = array(15, 14);
		}
		//88 //硬炸弹
		foreach ( $sitiao as $k => $v )
		{
			$sitiao_u[] = $v;
			$val = $v>9?$v:('0'.$v);
			$types['8804'.$val] = array($v,$v,$v,$v);
		}
		//08 三顺[不拆//硬炸弹，用三条]
		$types08 = self::cardsCutShunzi($satiao, 2);
		foreach ( $types08 as $k => $v )
		{
			$u_satiao = array_intersect($v, $satiao);
			if ( $u_satiao ) {
				$satiao_u = array_merge($u_satiao,$satiao_u);
				$satiao = array_diff($satiao,$u_satiao);
			}
			$len = count($v);
			$v = array_merge($v,$v,$v);
			rsort($v);
			$val = floor($k/100);
			$val = $val > 9 ? $val :('0'.$val);
			$types['08'.($len > 3 ? $len*3 :('0'.$len*3)).$val] = $v;
		}
		//07 连对[不拆//硬炸弹，不拆三顺，拆三条，用对牌]
		$types07 = $shuangshun = array();
		if ( $tmp = array_merge($satiao, $duipai) ) {
			$tmp = array_unique($tmp);
			rsort($tmp);
			$types07 = self::cardsCutShunzi($tmp,3);
		}
		foreach ( $types07 as $k => $v )
		{
			$u_duipai = array_intersect($v,$duipai);
			if ( $u_duipai )
			{
				$duipai_u = array_merge($u_duipai,$duipai_u);
				$duipai = array_diff($duipai,$u_duipai);
			}
			$u_satiao = array_intersect($v,$satiao);
			if ( $u_satiao )
			{
				$satiao_u = array_merge($u_satiao,$satiao_u);
				$satiao = array_diff($satiao,$u_satiao);
				//拆开的三条剩余归单张牌
				foreach ( $u_satiao as $kk => $vv )
				{
					$danpai[] = $vv;
				}
			}
			$v = array_merge($v,$v);
			rsort($v);
			$len = count($v);
			$len = $len > 9 ? $len :('0'.$len);
			$val = floor($k/100);
			$val = $val > 9 ? $val :('0'.$val);
			$types['07'.$len.$val] = $v;
			$shuangshun[] = $v;
		}
		//06 单顺[不拆//硬炸弹，不拆三顺，不拆连对，拆三条，拆对牌，用单张牌]
		$types06 = $types_ = $danshun = array();
		if ( $tmp = array_merge($satiao, $duipai, $danpai) ) {
			$tmp = array_unique($tmp);
			rsort($tmp);
			$types06 = self::cardsCutShunzi($tmp,5);
		}
		foreach ( $types06 as $k => $v )
		{
			if ( count(array_intersect($types_, $v)) > 2 ) {
				continue;//只取一个长顺即可，3即为连对了
			}
			$types_ = array_merge($types_,$v);
			$u_danpai = array_intersect($v,$danpai);
			$u_duipai = array_intersect($v,$duipai);
			$u_satiao = array_intersect($v,$satiao);
			if ( count($u_satiao) >= (count($v)/3+0) || count($u_duipai) > (count($v)/2+0) ) {
				continue;//牌艰不拆。不能拆太多三条/对牌
			}
			if ( $u_danpai )
			{
				$danpai_u = array_merge($u_danpai,$danpai_u);
				$danpai = array_diff($danpai,$u_danpai);
			}
			if ( $u_duipai )
			{
				$duipai_u = array_merge($u_duipai,$duipai_u);
				$duipai = array_diff($duipai,$u_duipai);
				//拆开的对牌剩余归单张牌
				foreach ( $u_duipai as $kk => $vv )
				{
					$danpai[] = $vv;
				}
			}
			if ( $u_satiao )
			{
				$satiao_u = array_merge($u_satiao,$satiao_u);
				$satiao = array_diff($satiao,$u_satiao);
				//拆开的三条剩余归对牌
				foreach ( $u_satiao as $kk => $vv )
				{
					$duipai[] = $vv;
				}
			}
			$len = count($v);
			$len = $len > 9 ? $len :('0'.$len);
			$val = floor($k/100);
			$val = $val > 9 ? $val :('0'.$val);
			$types['06'.$len.$val] = $v;
			$danshun[] = $v;
		}
		//长度足够的，且头部或尾部在三条里面: 缩短顺子，恢复三条
		foreach ( $danshun as $k => $v )
		{
			//先断尾三条
			$len = $len_ = count($v);
			$len = $len > 9 ? $len :('0'.$len);
			$max_ = max($v);
			$min_ = min($v);
			if ( $duipai && $len_ > 5 && in_array($min_,$duipai) )
			{
				$val = $max_ > 9 ? $max_ :('0'.$max_);
				unset($types['06'.$len.$val]);
				$len_ -=1;
				$len_ = $len_ > 9 ? $len_ :('0'.$len_);
				$duipai = array_diff($duipai,array($min_));
				$satiao[] = $min_;
				array_pop($v);
				$danshun[$k] = $v;
				$types['06'.$len_.$val] = $v;
			}
			//再断头三条
			$len = $len_ = count($v);
			$len = $len > 9 ? $len :('0'.$len);
			$max_ = max($v);
			if ( $duipai && $len_ > 5 && in_array($max_,$duipai) )
			{
				$val = $max_ > 9 ? $max_ :('0'.$max_);
				unset($types['06'.$len.$val]);
				$len_ -=1;
				$len_ = $len_ > 9 ? $len_ :('0'.$len_);
				$val_ = $k-1;
				$val_ = $val_ > 9 ? $val_ :('0'.$val_);
				$duipai = array_diff($duipai,array($max_));
				$satiao[] = $max_;
				array_shift($v);
				$danshun[$k] = $v;
				$types['06'.$len_.$val_] = $v;
			}
		}
		foreach ( $shuangshun as $k => $v )
		{
			//先断尾三条
			$len = $len_ = count($v);
			$len = $len > 9 ? $len :('0'.$len);
			$max_ = max($v);
			$min_ = min($v);
			if ( $danpai && $len_ > 6 && in_array($min_,$danpai) )
			{
				$val = $max_ > 9 ? $max_ :('0'.$max_);
				unset($types['07'.$len.$val]);
				$len_ -=2;
				$len_ = $len_ > 9 ? $len_ :('0'.$len_);
				$danpai = array_diff($danpai,array($min_));
				$satiao[] = $min_;
				array_pop($v);
				array_pop($v);
				$shuangshun[$k] = $v;
				$types['07'.$len_.$val] = $v;
			}
			//再断头三条
			$len = $len_ = count($v);
			$len = $len > 9 ? $len :('0'.$len);
			$max_ = max($v);
			$min_ = min($v);
			if ( $danpai && $len_ > 6 && in_array($max_,$danpai) )
			{
				$val = $max_ > 9 ? $max_ :('0'.$max_);
				unset($types['07'.$len.$val]);
				$len_ -=2;
				$len_ = $len_ > 9 ? $len_ :('0'.$len_);
				$val_ = $max_-1;
				$val_ = $val_ > 9 ? $val_ :('0'.$val_);
				$danpai = array_diff($danpai,array($max_));
				$satiao[] = $max_;
				array_shift($v);
				array_shift($v);
				$shuangshun[$k] = $v;
				$types['07'.$len_.$val_] = $v;
			}
		}
		//下面带牌不用上面已经用过的单张牌对牌
		if ( $satiao ) sort($satiao);
		if ( $duipai ) sort($duipai);
		if ( $danpai ) sort($danpai);
		$duipai_len = count($duipai);
		$danpai_len = count($danpai);
		//11 四带单
		//12 四带对
		foreach ( $sitiao as $k => $v )
		{
			$val = $v>9?$v:('0'.$v);
			//带上两个单
			for ( $i=0; $i < $danpai_len; $i++ )
			{
				if ( isset($danpai[$i+1]) && $danpai[$i] != $v && $danpai[$i+1] != $v )
				{
					$types['1106'.$val] = array($v,$v,$v,$v,$danpai[$i],$danpai[$i+1]);
					//带上的牌归为拆拆牌
					$types['0001'.($danpai[$i]>9?$danpai[$i]:('0'.$danpai[$i]))] = array($danpai[$i]);
					$types['0001'.($danpai[$i+1]>9?$danpai[$i+1]:('0'.$danpai[$i+1]))] = array($danpai[$i+1]);
					break;
				}
			}
			//带上两个对，且非33333333、33334444之类，44443333是可以的
			for ( $i=0; $i < $duipai_len; $i++ )
			{
				if ( isset($duipai[$i+1]) && $duipai[$i] != $v && $duipai[$i+1] != $v && !($duipai[$i] == $duipai[$i+1] && $duipai[$i] >= $v ) )
				{
					$types['1208'.$val] = array($v,$v,$v,$v,$duipai[$i],$duipai[$i],$duipai[$i+1],$duipai[$i+1]);
					//带上的牌归为拆拆牌
					$types['0002'.($duipai[$i]>9?$duipai[$i]:('0'.$duipai[$i]))] = array($duipai[$i],$duipai[$i]);
					$types['0002'.($duipai[$i+1]>9?$duipai[$i+1]:('0'.$duipai[$i+1]))] = array($duipai[$i+1],$duipai[$i+1]);
					break;
				}
			}
		}
		//03 三不带
		//04 三带单
		//05 三带对
		foreach ( $satiao as $k => $v )
		{
			$val = $v>9?$v:('0'.$v);
			$is_bind = 0;
			//带上一个单
			for ( $i=0; $i < $danpai_len; $i++ )
			{
				if ( $danpai[$i] != $v )
				{
					$types['0404'.$val] = array($v,$v,$v,$danpai[$i]);
					//带上的牌归为拆拆牌
					$types['0001'.($danpai[$i]>9?$danpai[$i]:('0'.$danpai[$i]))] = array($danpai[$i]);
					$is_bind = 1;
					break;
				}
			}
			//带上一个对，且非33333之类
			for ( $i=0; $i < $duipai_len; $i++ )
			{
				if ( $duipai[$i] != $v )
				{
					$types['0505'.$val] = array($v,$v,$v,$duipai[$i],$duipai[$i]);
					//带上的牌归为拆拆牌
					$types['0002'.($duipai[$i]>9?$duipai[$i]:('0'.$duipai[$i]))] = array($duipai[$i], $duipai[$i]);
					$is_bind = 1;
					break;
				}
			}
			if ( $is_bind ) {
				//拆拆牌
				$types['0003'.$val] = array($v,$v,$v);
			} else {
				//正常三条
				$types['0303'.$val] = array($v,$v,$v);
			}
		}
		//09 飞机单
		//10 飞机对
		foreach ( $types08 as $k => $v )
		{
			$len = count($v);
			$v = array_merge($v,$v,$v);
			rsort($v);
			$val = floor($k/100);
			$val = $val > 9 ? $val :('0'.$val);
			//带上$len个单
			if ( $danpai_len >= $len )
			{
			for ( $i=0; $i < $danpai_len; $i++ )
			{
				$_v = $v;
				for ( $j=0; $j < $len; $j++)
				{
					if ( isset($danpai[$i+$j]) )
					{
					$_v[]=$danpai[$i+$j];
					}
				}
				$_count = array_count_values($_v);
				rsort($_count);
				$_len = array_sum($_count);
				if ( !( $_count[0] > 4 || (!array_diff($_count,array(4)) && $_len == 8) || (!array_diff($_count,array(3)) && $_len == 12) ) )
				{
					$types['09'.($_len>9?$_len:('0'.$_len)).$val] = $_v;
					break;
				}
			}
			}
			//带上$len个对
			if ( $duipai_len >= $len )
			{
			for ( $i=0; $i < $duipai_len; $i++ )
			{
				$_v = $v;
				for ( $j=0; $j < $len; $j++)
				{
					if ( isset($duipai[$i+$j]) )
					{
					$_v[]=$duipai[$i+$j];
					$_v[]=$duipai[$i+$j];
					}
				}
				$_count = array_count_values($_v);
				rsort($_count);
				$_len = array_sum($_count);
				if ( !( $_count[0] > 4 ) )
				{
					$types['10'.($_len>9?$_len:('0'.$_len)).$val] = $_v;
					break;
				}
			}
			}
		}
		//01 单张牌
		foreach ( $danpai as $k => $v )
		{
			$val = $v>9?$v:('0'.$v);
			$types['0101'.$val] = array($v);
		}
		//02 对牌
		foreach ( $duipai as $k => $v )
		{
			$val = $v>9?$v:('0'.$v);
			$types['0202'.$val] = array($v,$v);
		}
		//没有对牌时 先拆最大三条
		if ( ! $duipai && $satiao )
		{
			rsort($satiao);
			$v = reset($satiao);
			$val = $v>9?$v:('0'.$v);
			$types['0002'.$val] = array($v,$v);
			$duipai[]=$v;
		}
		//没有对牌时 再拆最大连对顶部
		if ( ! $duipai && $shuangshun )
		{
			krsort($shuangshun);
			$tmp = reset($shuangshun);
			$v = reset($tmp);
			$val = $v>9?$v:('0'.$v);
			$types['0002'.$val] = array($v,$v);
			$duipai[]=$v;
		}
		//没有单张牌时 先拆最大对牌
		if ( ! $danpai && $duipai )
		{
			krsort($duipai);
			$v = reset($duipai);
			$val = $v>9?$v:('0'.$v);
			$types['0001'.$val] = array($v);
			$danpai[]=$v;
		}
		//没有单张牌时 先拆最大三条
		if ( ! $danpai && $satiao )
		{
			rsort($satiao);
			$v = reset($satiao);
			$val = $v>9?$v:('0'.$v);
			$types['0001'.$val] = array($v);
			$danpai[]=$v;
		}
		//没有单张牌时 再拆最大单顺
		if ( ! $danpai && $danshun )
		{
			krsort($danshun);
			$shunzi = reset($danshun);
			$v = reset($shunzi);
			$val = $v>9?$v:('0'.$v);
			$types['0001'.$val] = array($v);
			$danpai[]=$v;
		}
		//最后肯定拆双王炸
		if ( isset($types['990214']) )
		{
			$types['000115'] = array('15');
			$types['000114'] = array('14');
			$danpai[] = 15;
			$danpai[] = 14;
		}
		if ( $types ) krsort($types);
		return $types;
	}

	//组装配合牌型 牌面不可复用
	static function cardsFitTypes( $cards )
	{
		if ( ! $cards ) return array();
		$danpai = $duipai = $satiao = $sitiao = array();
		$counts = array_count_values($cards);
		foreach ( $counts as $k => $v )
		{
			if 		 ( $v == 4 ) {
				$sitiao[] = $k;
			} elseif ( $v == 3 ) {
				$satiao[] = $k;
			} elseif ( $v == 2 ) {
				$duipai[] = $k;
			} elseif ( $v == 1 ) {
				$danpai[] = $k;
			} else 				 {
				return array();
			}
		}
		$types = array();
		//99 双王炸
		if ( in_array(15,$danpai) && in_array(14,$danpai) )
		{
			$types['990214'] = array(15, 14);
			$danpai = array_values(array_diff($danpai,array(15, 14)));
		}
		//88 //硬炸弹
		foreach ( $sitiao as $k => $v )
		{
			$types['8804'.($v>9?$v:('0'.$v))] = array($v,$v,$v,$v);
			$danpai = array_values(array_diff($danpai,array($v)));
		}
		//06 单顺
		$type06 = self::cardsCutShunzi($danpai, 5, true);
		foreach ( $type06 as $k => $v )
		{
			$len = $k%100+0;
			$len = $len>9?$len:('0'.$len);
			$val = floor($k/100);
			$val = $val>9?$val:('0'.$val);
			$types['06'.$len.$val] = $v;
			$danpai = array_values(array_diff($danpai,$v));
		}
		//07 连对
		$types07 = self::cardsCutShunzi($duipai, 3, true);
		foreach ( $types07 as $k => $v )
		{
			$len = ($k%100+0)*2;
			$len = $len>9?$len:('0'.$len);
			$val = floor($k/100);
			$val = $val>9?$val:('0'.$val);
			$v = array_merge($v,$v);
			rsort($v);
			$types['07'.$len.$val] = $v;
			$duipai = array_values(array_diff($duipai,$v));
		}
		//08 三顺
		$type08 = self::cardsCutShunzi($satiao,2,true);
		$sanshun = array();
		foreach ( $type08 as $k => $v )
		{
			$sanshun[]=$v;
			$satiao = array_values(array_diff($satiao, $v));
		}
		//10 飞机对
		foreach ( $sanshun as $k => $v )
		{
			$val = $v[0];
			$val = $val>9?$val:('0'.$val);
			$sanshun_len = count(array_unique($v));
			if ( ! $duipai || count($duipai) < $sanshun_len ) break;
			$duipai_ = $duipai;
			sort($duipai_);
			$_v = array();
			for ( $i=0; $i < $sanshun_len; $i++ )
			{
				$_v[] = $duipai_[$i];
				$_v[] = $duipai_[$i];
			}
			$v = array_merge($v,$v,$v);
			rsort($v);
			$types['10'.($sanshun_len > 1 ? '' : '0').($sanshun_len*5).$val] = array_merge($v,$_v);
			$duipai = array_values(array_diff($duipai,$_v));
			unset($sanshun[$k]);
		}
		//09 飞机单
		foreach ( $sanshun as $k => $v )
		{
			$val = $v[0];
			$val = $val>9?$val:('0'.$val);
			$sanshun_len = count(array_unique($v));
			if ( ! $danpai || count($danpai) < $sanshun_len ) break;
			$danpai_ = $danpai;
			sort($danpai_);
			$_v = array();
			for ( $i=0; $i < $sanshun_len; $i++ )
			{
				$_v[] = $danpai_[$i];
			}
			$v = array_merge($v,$v,$v);
			rsort($v);
			$types['09'.($sanshun_len > 2 ? '' : '0').($sanshun_len*4).$val] = array_merge($v,$_v);
			$danpai = array_values(array_diff($danpai,$_v));
			unset($sanshun[$k]);
		}
		//08 三顺
		foreach ( $sanshun as $k => $v )
		{
			$val = $v[0];
			$val = $val>9?$val:('0'.$val);
			$sanshun_len = count(array_unique($v));
			$v = array_merge($v,$v,$v);
			rsort($v);
			$types['08'.($sanshun_len > 3 ? '' : '0').($sanshun_len*3).$val] = $v;
		}
		//05 三带二
		foreach ( $satiao as $k => $v )
		{
			if ( ! $duipai ) break;
			$duipai_ = $duipai;
			sort($duipai_);
			$types['0505'.($v>9?$v:('0'.$v))] = array($v,$v,$v,$duipai_[0],$duipai_[0]);
			unset($satiao[$k]);
			$duipai = array_values(array_diff($duipai,array($duipai_[0])));
		}
		//04 三带一
		foreach ( $satiao as $k => $v )
		{
			if ( ! $danpai ) break;
			$danpai_ = $danpai;
			sort($danpai_);
			$types['0404'.($v>9?$v:('0'.$v))] = array($v,$v,$v,$danpai_[0]);
			unset($satiao[$k]);
			$danpai = array_values(array_diff($danpai,array($danpai_[0])));
		}
		//03 三条
		foreach ( $satiao as $k => $v )
		{
			$types['0303'.($v>9?$v:('0'.$v))] = array($v,$v,$v);
		}
		//02 对牌
		foreach ( $duipai as $k => $v )
		{
			$types['0202'.($v>9?$v:('0'.$v))] = array($v,$v);
		}
		//01 单张牌
		foreach ( $danpai as $k => $v )
		{
			$types['0101'.($v>9?$v:('0'.$v))] = array($v);
		}
		if ( $types ) krsort($types);
		return $types;
	}

	//组装带牌牌面
	//$base		组成关键牌型的基础牌组
	//$pool		用来取出牌值的带牌牌池
	//$len		需要从带牌牌池取出牌值的个数
	//$times	同一张牌值取出N次，用于取对子，比如: 4443332222三带二牌型需要在带牌牌池数组中取出两次对2
	//return	组装出的多个牌组 array(array(4,4,4,3,3,3,2,2),array(4,4,4,3,3,3,2,2,2,2),...)
	static function cardsCombine( $base, $pool, $len, $times=1, $adds=array(), $res=array() )
	{
		if ( ! $len ) {
	        if ( $adds ) {
	            $res_ = array_merge($base, $adds);
	            $res[join('', $res_)] = $res_;
	        }
	        return $res;
	    }
	    $len_ = count($pool);
	    if ( $len_ < $len ) return $res;
	    for ( $i = 0; $i < $len_ - $len + 1; $i++ ) {
	        $one_ = array_shift($pool);
	        $add_ = array();
	        for ( $j = 0; $j < $times; $j++ ) {
	            $add_[] = $one_;
	        }
	        $adds_ = array_merge($adds, $add_);
	        rsort($adds_);
	        $res = self::cardsCombine($base, $pool, $len-1, $times, $adds_, $res);
	    }
	    return $res;
	}

	//组装单双三顺
	//$cards	组装来源
	//$minlen	最小有效长度
	//$is_long	只取最长顺子，默认否
	//return	切好的顺子数组
	//			'1006'=>array(10,9,8,7,6,5), 以10开头的6张不同牌的单顺
	//			'1003'=>array(10,9,8), 以10开头的3张不同牌的双顺(连对)或三顺(飞机)
	static function cardsCutShunzi( $cards, $minlen, $is_long=0 )
	{
		$cards_ = array();
		if ( ! $cards || !in_array($minlen, array(2,3,5)) ) return $cards_;
		rsort($cards);
		//组装最大头开始的最小有效单顺双顺三顺
		foreach ( $cards as $v )
		{
			if ( in_array($v, array(15,14,13)) || isset($cards_[$v+1]) ) continue;
			if 		 ( $minlen == 2 && !isset($cards_[$v]) && in_array($v-1,$cards) ) {
				$cards_[$v] = $v-1;
			} elseif ( $minlen == 3 && !isset($cards_[$v]) && in_array($v-1,$cards) && in_array($v-2,$cards) ) {
				$cards_[$v] = $v-2;
			} elseif ( $minlen == 5 && !isset($cards_[$v]) && in_array($v-1,$cards) && in_array($v-2,$cards) && in_array($v-3,$cards) && in_array($v-4,$cards) ) {
				$cards_[$v] = $v-4;
			}
		}
		if ( ! $cards_ ) return $cards_;
		//获取最长顺子
		foreach ( $cards as $v )
		{
			foreach ( $cards_ as $start => $end )
			{
				if ( $end == $v + 1 ) $cards_[$start] = $v;
			}
		}
		$cards = array();
		//只取最长顺子
		if ( $is_long ) {
			foreach ( $cards_ as $start => $end )
			{
				$cards[$start*100+$start-$end+1] = range($start, $end);
			}
			krsort($cards);
			return $cards;
		}
		//切出所有顺子 //key==1008, 以K(10)开头的8张牌的顺子
		foreach ( $cards_ as $start => $end )
		{
			$offset = $start-$end-$minlen+1;
			for ( ; $offset >= 0; $offset-- )
			{
				$cards[$start*100+$start-$end-$offset+1] = range($start,$end+$offset);
				$start2 = $start - $offset;
				$offset2 = $start2 - $end - $minlen+1;
				for ( ; $offset2 >=0 ; $offset2-- )
				{
					$cards[$start2*100+$start2-$end-$offset2+1] = range($start2,$end+$offset2);
				}
			}
		}
		krsort($cards);
		return $cards;
	}

	static function cardsWeight( $toType, $toLen, $toValue, $mainBreakLen=1, $mainBreakValue=0, $plusBreakLen=1, $plusBreakValue=0, $useJokerNum=0, $useJokerValue=0, $useJokerTo=0 )
	{
		if ( $toType > 86 ) $toType -= 70;
		if ( $toType > 86 ) $toLen += $toLen == 2 ? 2 : 0;
		$toLen = round($toLen / 4) + 1;
		$toValue = round($toValue / 4) + 1;
		$mainBreakValue = round($mainBreakValue / 4) + 1;
		$plusBreakValue = round($plusBreakValue / 3) + 1;
		$useJokerValue = $useJokerValue == 2 ? 2 : 1;
		$jokerNumWeight = $toType == 89 ? 0 : 5;
		$weight = intval($toType * $toLen * $toValue - $mainBreakLen * $mainBreakValue - $plusBreakLen * $plusBreakValue - $jokerNumWeight * $useJokerNum * $useJokerValue * $useJokerTo);
		return $weight > 0 ? $weight : 1;
	}

	//填充为制定牌型
	//$acv 		array 	源牌，无赖子的已倒序无花色十进制牌组，并已经进行过array_count_values操作
	//$t 		int 	目标牌型，[1-12|87-89]
	//$cl 		int 	叫牌长度，0非叫[1-n]
	//$cv 		int 	叫牌牌值，0非叫[1-15]
	//$m 		int 	赖子数，[1-4]
	//$j 		int 	赖子牌无花色十进制牌值，[1-13]
	//$big 		int 	于$cv的比较模式 0不比较 1大于
	//$in	 	int 	0外部调用1内部调用 内部调用，以牌值做为索引 外部调用以tlv和附加权重作为索引
	//$ex	 	int 	0内部不扩展1内部扩展 内部扩展下可以把赖子牌变为任意空缺牌
	//$ml 		int		内部扩展下，有序牌型的最小长度，用于单顺子双顺子飞机等
	//return 	$viable [87040808]=>array(8,8,8,9),[890409]=>array(9,9,9,9),...
	static function cardsFill( $acv, $t, $cl, $cv, $m, $j, $big=0, $in=0, $ex=0, $ml=2 )
	{
		if ( ! $t || ! $m || ! $j ) return array();
		if ( $big && ($cv == 15 || ($cv == 13 && $t > 1 && $t < 86) || ($cv == 12 && $t > 5 && $t < 11)) ) return array();
		$viable = array();
		switch ( $t )
		{
			case 1 :
				$l = $t;
				if ( $in ) {
					foreach ( $acv as $v => $n )
					{
						if ( $big && $cv >= $v ) break;
						$viable[$v] = array('t'=>$t,'l'=>$l,'v'=>$v,'do'=>array($v),'to'=>array(),'cs'=>array($v),'mbl'=>$n-$l,'mbv'=>$v,'pbl'=>0,'pbv'=>0,'ujn'=>0,'ujv'=>0,'ujt'=>0);
					}
					$n = 0; $v = $j;
					if ( $m + $n >= $l && ( ! $big || ! ($big && $cv >= $v) ) ) {
						$viable[$v] = array('t'=>$t,'l'=>$l,'v'=>$v,'do'=>array($j),'to'=>array($v),'cs'=>array($v),'mbl'=>$m+$n-$l,'mbv'=>$j,'pbl'=>0,'pbv'=>0,'ujn'=>$l-$n,'ujv'=>$j,'ujt'=>$v);
					}
					if ( ! $ex ) break;
					$bcv = array_diff(range(1, 13), array_keys($acv), array($j));//others no 15 14 $j
					if ( ! $bcv ) break;
					foreach ( $bcv as $v )
					{
						if ( $big && $cv >= $v ) continue;
						$viable[$v] = array('t'=>21,'l'=>$l,'v'=>$v,'do'=>array($j),'to'=>array($v),'cs'=>array($v),'mbl'=>$m+$n-$l,'mbv'=>$j,'pbl'=>0,'pbv'=>0,'ujn'=>$l-$n,'ujv'=>$j,'ujt'=>$v);
					}
					break;
				}
				foreach ( $acv as $v => $n )
				{
					if ( $big && $cv >= $v ) break;
					$viable['0101'.($v>9?$v:"0{$v}").'999'] = array('t'=>$t,'l'=>$l,'v'=>$v,'do'=>array($v),'to'=>array(),'cs'=>array($v),'mbl'=>$n-$l,'mbv'=>$v,'pbl'=>0,'pbv'=>0,'ujn'=>0,'ujv'=>0,'ujt'=>0);
				}
				$n = 0; $v = $j;
				if ( $m + $n >= $l && ( ! $big || ! ($big && $cv >= $v) ) ) {
					$viable['0101'.($v>9?$v:"0{$v}").'000'] = array('t'=>$t,'l'=>$l,'v'=>$v,'do'=>array($j),'to'=>array($v),'cs'=>array($v),'mbl'=>$m+$n-$l,'mbv'=>$j,'pbl'=>0,'pbv'=>0,'ujn'=>$l-$n,'ujv'=>$j,'ujt'=>$v);
				}
			break;
			case 2 :
				$l = $t;
				if ( $in ) {
					foreach ( $acv as $v => $n )
					{
						if ( $big && $cv >= $v ) break;
						if ( $v > 13 || $m + $n < $l ) continue;//no 15 14 and enough
						if ( $n == 1 ) $viable[$v] = array('t'=>$t,'l'=>$l,'v'=>$v,'do'=>array($v,$j),'to'=>array($v),'cs'=>array($v,$v),'mbl'=>$m+$n-$l,'mbv'=>$j,'pbl'=>0,'pbv'=>0,'ujn'=>$l-$n,'ujv'=>$j,'ujt'=>$v);
						else $viable[$v] = array('t'=>$t,'l'=>$l,'v'=>$v,'do'=>array($v,$v),'to'=>array(),'cs'=>array($v,$v),'mbl'=>$n-$l,'mbv'=>$v,'pbl'=>0,'pbv'=>0,'ujn'=>0,'ujv'=>0,'ujt'=>0);
					}
					$n = 0; $v = $j;
					if ( $m + $n >= $l && ( ! $big || ! ($big && $cv >= $v) ) ) {
						$viable[$v] = array('t'=>$t,'l'=>$l,'v'=>$v,'do'=>array($j,$j),'to'=>array($v,$v),'cs'=>array($v,$v),'mbl'=>$m+$n-$l,'mbv'=>$j,'pbl'=>0,'pbv'=>0,'ujn'=>$l-$n,'ujv'=>$j,'ujt'=>$v);
					}
					if ( ! $ex ) break;
					$bcv = array_diff(range(1, 13), array_keys($acv), array($j));//others no 15 14 $j
					if ( ! $bcv ) break;
					foreach ( $bcv as $v )
					{
						if ( $big && $cv >= $v ) continue;
						$viable[$v] = array('t'=>22,'l'=>$l,'v'=>$v,'do'=>array($j,$j),'to'=>array($v,$v),'cs'=>array($v,$v),'mbl'=>$m+$n-$l,'mbv'=>$j,'pbl'=>0,'pbv'=>0,'ujn'=>$l-$n,'ujv'=>$j,'ujt'=>$v);
					}
					break;
				}
				foreach ( $acv as $v => $n )
				{
					if ( $big && $cv >= $v ) break;
					if ( $v > 13 || $m + $n < $l ) continue;//no 15 14 and enough
					if ( $n == 1 ) $viable['0202'.($v>9?$v:"0{$v}").self::cardsWeight($t,$l,$v,$m+$n-$l,$j,0,0,$l-$n,$j,$v)] = array('t'=>$t,'l'=>$l,'v'=>$v,'do'=>array($v,$j),'to'=>array($v),'cs'=>array($v,$v),'mbl'=>$m+$n-$l,'mbv'=>$j,'pbl'=>0,'pbv'=>0,'ujn'=>$l-$n,'ujv'=>$j,'ujt'=>$v);
					else $viable['0202'.($v>9?$v:"0{$v}").self::cardsWeight($t,$l,$v,$n-$l,$v,0,0,0,0,0)] = array('t'=>$t,'l'=>$l,'v'=>$v,'do'=>array($v,$v),'to'=>array(),'cs'=>array($v,$v),'mbl'=>$n-$l,'mbv'=>$v,'pbl'=>0,'pbv'=>0,'ujn'=>0,'ujv'=>0,'ujt'=>0);
				}
				$n = 0; $v = $j;
				if ( $m + $n >= $l && ( ! $big || ! ($big && $cv >= $v) ) ) {
					$viable['0202'.($v>9?$v:"0{$v}").'000'] = array('t'=>$t,'l'=>$l,'v'=>$v,'do'=>array($j,$j),'to'=>array($v,$v),'cs'=>array($v,$v),'mbl'=>$m+$n-$l,'mbv'=>$j,'pbl'=>0,'pbv'=>0,'ujn'=>$l-$n,'ujv'=>$j,'ujt'=>$v);
				}
			break;
			case 3 :
				$l = $t;
				if ( $in ) {
					foreach ( $acv as $v => $n )
					{
						if ( $big && $cv >= $v ) break;
						if ( $v > 13 || $m + $n < $l ) continue;//no 15 14 and enough
						if ( $n == 1 ) $viable[$v] = array('t'=>$t,'l'=>$l,'v'=>$v,'do'=>array($v,$j,$j),'to'=>array($v,$v),'cs'=>array($v,$v,$v),'mbl'=>$m+$n-$l,'mbv'=>$j,'pbl'=>0,'pbv'=>0,'ujn'=>$l-$n,'ujv'=>$j,'ujt'=>$v);
						elseif ( $n == 2 ) $viable[$v] = array('t'=>$t,'l'=>$l,'v'=>$v,'do'=>array($v,$v,$j),'to'=>array($v),'cs'=>array($v,$v,$v),'mbl'=>$m+$n-$l,'mbv'=>$j,'pbl'=>0,'pbv'=>0,'ujn'=>$l-$n,'ujv'=>$j,'ujt'=>$v);
						else $viable[$v] = array('t'=>$t,'l'=>$l,'v'=>$v,'do'=>array($v,$v,$v),'to'=>array(),'cs'=>array($v,$v,$v),'mbl'=>$n-$l,'mbv'=>$v,'pbl'=>0,'pbv'=>0,'ujn'=>0,'ujv'=>0,'ujt'=>0);
					}
					$n = 0; $v = $j;
					if ( $m + $n >= $l && ( ! $big || ! ($big && $cv >= $v) ) ) {
						$viable[$v] = array('t'=>$t,'l'=>$l,'v'=>$v,'do'=>array($j,$j,$j),'to'=>array($v,$v,$v),'cs'=>array($v,$v,$v),'mbl'=>$m+$n-$l,'mbv'=>$j,'pbl'=>0,'pbv'=>0,'ujn'=>$l-$n,'ujv'=>$j,'ujt'=>$v);
					}
					if ( ! $ex ) break;
					$bcv = array_diff(range(1, 13), array_keys($acv), array($j));//others no 15 14 $j
					if ( ! $bcv ) break;
					foreach ( $bcv as $v )
					{
						if ( $big && $cv >= $v ) continue;
						$viable[$v] = array('t'=>23,'l'=>$l,'v'=>$v,'do'=>array($j,$j,$j),'to'=>array($v,$v,$v),'cs'=>array($v,$v,$v),'mbl'=>$m+$n-$l,'mbv'=>$j,'pbl'=>0,'pbv'=>0,'ujn'=>$l-$n,'ujv'=>$j,'ujt'=>$v);
					}
					break;
				}
				foreach ( $acv as $v => $n )
				{
					if ( $big && $cv >= $v ) break;
					if ( $v > 13 || $m + $n < $l ) continue;//no 15 14 and enough
					if ( $n == 1 ) $viable['0303'.($v>9?$v:"0{$v}").self::cardsWeight($t,$l,$v,$m+$n-$l,$j,0,0,$l-$n,$j,$v)] = array('t'=>$t,'l'=>$l,'v'=>$v,'do'=>array($v,$j,$j),'to'=>array($v,$v),'cs'=>array($v,$v,$v),'mbl'=>$m+$n-$l,'mbv'=>$j,'pbl'=>0,'pbv'=>0,'ujn'=>$l-$n,'ujv'=>$j,'ujt'=>$v);
					elseif ( $n == 2 ) $viable['0303'.($v>9?$v:"0{$v}").self::cardsWeight($t,$l,$v,$m+$n-$l,$j,0,0,$l-$n,$j,$v)] = array('t'=>$t,'l'=>$l,'v'=>$v,'do'=>array($v,$v,$j),'to'=>array($v),'cs'=>array($v,$v,$v),'mbl'=>$m+$n-$l,'mbv'=>$j,'pbl'=>0,'pbv'=>0,'ujn'=>$l-$n,'ujv'=>$j,'ujt'=>$v);
					else $viable['0303'.($v>9?$v:"0{$v}").self::cardsWeight($t,$l,$v,$n-$l,$v,0,0,0,0,0)] = array('t'=>$t,'l'=>$l,'v'=>$v,'do'=>array($v,$v,$v),'to'=>array(),'cs'=>array($v,$v,$v),'mbl'=>$n-$l,'mbv'=>$v,'pbl'=>0,'pbv'=>0,'ujn'=>0,'ujv'=>0,'ujt'=>0);
				}
				$n = 0; $v = $j;
				if ( $m + $n >= $l && ( ! $big || ! ($big && $cv >= $v) ) ) {
					$viable['0303'.($v>9?$v:"0{$v}").'000'] = array('t'=>$t,'l'=>$l,'v'=>$v,'do'=>array($j,$j,$j),'to'=>array($v,$v,$v),'cs'=>array($v,$v,$v),'mbl'=>$m+$n-$l,'mbv'=>$j,'pbl'=>0,'pbv'=>0,'ujn'=>$l-$n,'ujv'=>$j,'ujt'=>$v);
				}
			break;
			case 4 :
				$l = $t;
				$vsatiao = self::cardsFill($acv, 3, $cl, $cv, $m, $j, $big, 1, 0);//使用内部非扩展牌型
				if ( ! $vsatiao ) break;
				$vdanpai = self::cardsFill($acv, 1, 1, 0, $m, $j, 0, 1, 0);//使用内部非扩展牌型
				if ( ! $vdanpai ) break;
				// ksort($vdanpai);
				foreach ( $vsatiao as $v => $oa )
				{
					foreach ( $vdanpai as $w => $ob )
					{
						if ( $v == $w || $oa['ujn'] + $ob['ujn'] > $m ) continue;
						$do = array_merge($oa['do'], $ob['do']);
						$to = array_merge($oa['to'], $ob['to']);
						$cs = array_merge($oa['cs'], $ob['cs']);
						$mbl = $oa['mbl'] + $ob['mbl'];
						$mbv = max($oa['mbv'], $ob['mbv']);
						$pbl = $ob['mbl'];
						$pbv = $ob['pbv'];
						$ujn = $oa['ujn'] + $ob['ujn'];
						$ujt = max($oa['ujt'], $ob['ujt']);
						$viable['0404'.($v>9?$v:"0{$v}").self::cardsWeight($t,$l,$v,$mbl,$mbv,$pbl,$pbv,$ujn,$j,$ujt)] = array('t'=>$t,'l'=>$l,'v'=>$v,'do'=>$do,'to'=>$to,'cs'=>$cs,'mbl'=>$mbl,'mbv'=>$mbv,'pbl'=>$pbl,'pbv'=>$pbv,'ujn'=>$ujn,'ujv'=>$j,'ujt'=>$ujt);
						break;
					}
				}
			break;
			case 5 :
				$l = $t;
				$vsatiao = self::cardsFill($acv, 3, $cl, $cv, $m, $j, $big, 1, 0);//使用内部非扩展牌型
				if ( ! $vsatiao ) break;
				$vduipai = self::cardsFill($acv, 2, 2, 0, $m, $j, 0, 1, 0);//使用内部非扩展牌型
				if ( ! $vduipai ) break;
				// ksort($vduipai);
				foreach ( $vsatiao as $v => $oa )
				{
					foreach ( $vduipai as $w => $ob )
					{
						if ( $v == $w || $oa['ujn'] + $ob['ujn'] > $m ) continue;
						$do = array_merge($oa['do'], $ob['do']);
						$to = array_merge($oa['to'], $ob['to']);
						$cs = array_merge($oa['cs'], $ob['cs']);
						$mbl = $oa['mbl'] + $ob['mbl'];
						$mbv = max($oa['mbv'], $ob['mbv']);
						$pbl = $ob['mbl'];
						$pbv = $ob['pbv'];
						$ujn = $oa['ujn'] + $ob['ujn'];
						$ujt = max($oa['ujt'], $ob['ujt']);
						$viable['0505'.($v>9?$v:"0{$v}").self::cardsWeight($t,$l,$v,$mbl,$mbv,$pbl,$pbv,$ujn,$j,$ujt)] = array('t'=>$t,'l'=>$l,'v'=>$v,'do'=>$do,'to'=>$to,'cs'=>$cs,'mbl'=>$mbl,'mbv'=>$mbv,'pbl'=>$pbl,'pbv'=>$pbv,'ujn'=>$ujn,'ujv'=>$j,'ujt'=>$ujt);
						break;
					}
				}
			break;
			case 6 :
				if ( isset($acv[13]) ) unset($acv[13]);
				if ( isset($acv[14]) ) unset($acv[14]);
				if ( isset($acv[15]) ) unset($acv[15]);
				if ( ! $acv ) break;
				if ( count($acv) + $m < $cl ) break;
				$vdanpai = self::cardsFill($acv, 1, 1, $cv-$cl+2, $m, $j, $big, 1, 1);//使用内部扩展牌型
				if ( ! $vdanpai ) break;
				$types06 = self::cardsCutShunzi(array_keys($vdanpai), 5, 0);//所有长短顺//array('1006'=>array(10,9,8,7,6,5),'1005'=>array(10,9,8,7,6),...)
				if ( ! $types06 ) break;
				foreach ( $types06 as $vl => $cards )
				{
					$v = intval($vl[0].$vl[1]);
					$l = intval($vl[2].$vl[3]);
					if ( ($cl && $l != $cl) || ($big && $v <= $cv) ) continue;
					$do = $to = $cs = array();
					$mbl = $mbv = $pbl = $pbv = $ujn = $ujt = 0;
					$overflow = 0;
					foreach ( $cards as $card )
					{
						$o = $vdanpai[$card];
						$ujn += $o['ujn'];
						if ( $ujn > $m ) {
							$overflow = 1;
							break;
						}
						$do = array_merge($do, $o['do']);
						$to = array_merge($to, $o['to']);
						$cs = array_merge($cs, $o['cs']);
						$mbl += $o['mbl'];
						$mbv = max($mbv, $o['mbv']);
						$ujt = max($ujt, $o['ujt']);
					}
					if ( $overflow ) continue;
					$viable['06'.($l>9?$l:"0{$l}").($v>9?$v:"0{$v}").self::cardsWeight($t,$l,$v,$mbl,$mbv,$pbl,$pbv,$ujn,$j,$ujt)] = array('t'=>$t,'l'=>$l,'v'=>$v,'do'=>$do,'to'=>$to,'cs'=>$cs,'mbl'=>$mbl,'mbv'=>$mbv,'pbl'=>$pbl,'pbv'=>$pbv,'ujn'=>$ujn,'ujv'=>$j,'ujt'=>$ujt);
				}
			break;
			case 7 :
				if ( isset($acv[13]) ) unset($acv[13]);
				if ( isset($acv[14]) ) unset($acv[14]);
				if ( isset($acv[15]) ) unset($acv[15]);
				if ( ! $acv ) break;
				if ( count($acv) + $m < $cl ) break;
				$vduipai = self::cardsFill($acv, 2, 2, $cv-$cl+2, $m, $j, $big, 1, 1);//使用内部扩展牌型
				if ( ! $vduipai ) break;
				$types07 = self::cardsCutShunzi(array_keys($vduipai), 3, 0);//所有长短顺
				if ( ! $types07 ) break;
				foreach ( $types07 as $vl => $cards )
				{
					$v = intval($vl[0].$vl[1]);
					$l = intval($vl[2].$vl[3]);
					if ( ($cl && $l != $cl) || ($big && $v <= $cv) ) continue;
					$do = $to = $cs = array();
					$mbl = $mbv = $pbl = $pbv = $ujn = $ujt = 0;
					$overflow = 0;
					foreach ( $cards as $card )
					{
						$o = $vduipai[$card];
						$ujn += $o['ujn'];
						if ( $ujn > $m ) {
							$overflow = 1;
							break;
						}
						$do = array_merge($do, $o['do']);
						$to = array_merge($to, $o['to']);
						$cs = array_merge($cs, $o['cs']);
						$mbl += $o['mbl'];
						$mbv = max($mbv, $o['mbv']);
						$ujt = max($ujt, $o['ujt']);
					}
					if ( $overflow ) continue;
					$viable['07'.($l>9?$l:"0{$l}").($v>9?$v:"0{$v}").self::cardsWeight($t,$l,$v,$mbl,$mbv,$pbl,$pbv,$ujn,$j,$ujt)] = array('t'=>$t,'l'=>$l,'v'=>$v,'do'=>$do,'to'=>$to,'cs'=>$cs,'mbl'=>$mbl,'mbv'=>$mbv,'pbl'=>$pbl,'pbv'=>$pbv,'ujn'=>$ujn,'ujv'=>$j,'ujt'=>$ujt);
				}
			break;
			case 8 :
				if ( isset($acv[13]) ) unset($acv[13]);
				if ( isset($acv[14]) ) unset($acv[14]);
				if ( isset($acv[15]) ) unset($acv[15]);
				if ( ! $acv ) break;
				if ( count($acv) + $m < $cl ) break;
				$vsatiao = self::cardsFill($acv, 3, 3, $cv&&$cl?($cv-$cl+2):0, $m, $j, $big, 1, 1);//使用内部扩展牌型
				if ( ! $vsatiao ) break;
				$types08 = self::cardsCutShunzi(array_keys($vsatiao), 2, 0);//所有长短顺
				if ( ! $types08 ) break;
				foreach ( $types08 as $vl => $cards )
				{
					$v = intval($vl[0].$vl[1]);
					$l = intval($vl[2].$vl[3]);
					if ( ($cl && $l != $cl) || ($big && $v <= $cv) ) continue;
					$do = $to = $cs = array();
					$mbl = $mbv = $pbl = $pbv = $ujn = $ujt = 0;
					$overflow = 0;
					foreach ( $cards as $card )
					{
						$o = $vsatiao[$card];
						$ujn += $o['ujn'];
						if ( $ujn > $m ) {
							$overflow = 1;
							break;
						}
						$do = array_merge($do, $o['do']);
						$to = array_merge($to, $o['to']);
						$cs = array_merge($cs, $o['cs']);
						$mbl += $o['mbl'];
						$mbv = max($mbv, $o['mbv']);
						$ujt = max($ujt, $o['ujt']);
					}
					if ( $overflow ) continue;
					if ( $in ) {
						$viable[$v] = array('t'=>$t,'l'=>$l*3,'v'=>$v,'do'=>$do,'to'=>$to,'cs'=>$cs,'mbl'=>$mbl,'mbv'=>$mbv,'pbl'=>$pbl,'pbv'=>$pbv,'ujn'=>$ujn,'ujv'=>$j,'ujt'=>$ujt);
					} else {
						$viable['08'.$vl.self::cardsWeight($t,$l*3,$v,$mbl,$mbv,$pbl,$pbv,$ujn,$j,$ujt)] = array('t'=>$t,'l'=>$l*3,'v'=>$v,'do'=>$do,'to'=>$to,'cs'=>$cs,'mbl'=>$mbl,'mbv'=>$mbv,'pbl'=>$pbl,'pbv'=>$pbv,'ujn'=>$ujn,'ujv'=>$j,'ujt'=>$ujt);
					}
				}
			break;
			case 9 :
				$vsashun = self::cardsFill($acv, 8, $cl?floor($cl/4):0, $cv&&$cl?($cv-floor($cl/4)+2):0, $m, $j, $big, 1, 1);//使用内部扩展牌型
				if ( ! $vsashun ) break;
				$vdanpai = self::cardsFill($acv, 1, 1, 0, $m, $j, 0, 1, 0);//使用内部非扩展牌型
				if ( ! $vdanpai ) break;
				// ksort($vdanpai);
				foreach ( $vsashun as $v => $oa )
				{
					$ml = $l = $oa['l'];
					$pl = floor($ml/3);
					$do = $oa['do'];
					$to = $oa['to'];
					$cs = $oa['cs'];
					$mbl = $oa['mbl'];
					$mbv = $oa['mbv'];
					$pbl = $pbv = 0;
					$ujn = $oa['ujn'];
					$ujt = $oa['ujt'];
					$vdanpai_ = $vdanpai;
					if ( ! array_diff($cs, array_keys($vdanpai_)) ) {//排除四带二牌型
						unset($vdanpai_[end($cs)]);
						if ( count($vdanpai_) < $pl ) continue;
					}
					foreach ( $vdanpai_ as $w => $ob )
					{
						if ( $ujn + $ob['ujn'] > $m ) continue;
						$do = array_merge($do, $ob['do']);
						$to = array_merge($to, $ob['to']);
						$cs = array_merge($cs, $ob['cs']);
						$mbl += $ob['mbl'];
						$mbv = max($mbv, $ob['mbv']);
						$pbl += $ob['mbl'];
						$pbv = max($pbv, $ob['pbv']);
						$ujn += $ob['ujn'];
						$ujt = max($ujt, $ob['ujt']);
						$l += $ob['l'];
						$pl--;
						if ( ! $pl ) break;
					}
					if ( $pl ) continue;
					$viable['09'.($l>9?$l:"0{$l}").($v>9?$v:"0{$v}").self::cardsWeight($t,$l,$v,$mbl,$mbv,$pbl,$pbv,$ujn,$j,$ujt)] = array('t'=>$t,'l'=>$l,'v'=>$v,'do'=>$do,'to'=>$to,'cs'=>$cs,'mbl'=>$mbl,'mbv'=>$mbv,'pbl'=>$pbl,'pbv'=>$pbv,'ujn'=>$ujn,'ujv'=>$j,'ujt'=>$ujt);
				}
			break;
			case 10:
				$vsashun = self::cardsFill($acv, 8, $cl?floor($cl/4):0, $cv&&$cl?($cv-floor($cl/4)+2):0, $m, $j, $big, 1, 1);//使用内部扩展牌型
				if ( ! $vsashun ) break;
				$vduipai = self::cardsFill($acv, 2, 2, 0, $m, $j, 0, 1, 0);//使用内部非扩展牌型
				if ( ! $vduipai ) break;
				// ksort($vduipai);
				foreach ( $vsashun as $v => $oa )
				{
					$ml = $l = $oa['l'];
					$pl = floor($ml/3);
					$do = $oa['do'];
					$to = $oa['to'];
					$cs = $oa['cs'];
					$mbl = $oa['mbl'];
					$mbv = $oa['mbv'];
					$pbl = $pbv = 0;
					$ujn = $oa['ujn'];
					$ujt = $oa['ujt'];
					foreach ( $vduipai as $w => $ob )
					{
						if ( $v == $w || $ujn + $ob['ujn'] > $m ) continue;
						$do = array_merge($do, $ob['do']);
						$to = array_merge($to, $ob['to']);
						$cs = array_merge($cs, $ob['cs']);
						$mbl += $ob['mbl'];
						$mbv = max($mbv, $ob['mbv']);
						$pbl += $ob['mbl'];
						$pbv = max($pbv, $ob['pbv']);
						$ujn += $ob['ujn'];
						$ujt = max($ujt, $ob['ujt']);
						$l += $ob['l'];
						$pl--;
						if ( ! $pl ) break;
					}
					if ( $pl ) continue;
					$viable['10'.($l>9?$l:"0{$l}").($v>9?$v:"0{$v}").self::cardsWeight($t,$l,$v,$mbl,$mbv,$pbl,$pbv,$ujn,$j,$ujt)] = array('t'=>$t,'l'=>$l,'v'=>$v,'do'=>$do,'to'=>$to,'cs'=>$cs,'mbl'=>$mbl,'mbv'=>$mbv,'pbl'=>$pbl,'pbv'=>$pbv,'ujn'=>$ujn,'ujv'=>$j,'ujt'=>$ujt);
				}
			break;
			case 11:
				$vsitiao = self::cardsFill($acv, 86, $cl-2, $cv, $m, $j, $big, 1, 1);//使用内部扩展牌型
				if ( ! $vsitiao ) break;
				$vdanpai = self::cardsFill($acv, 1, 1, 0, $m, $j, 0, 1, 0);//使用内部非扩展牌型
				if ( ! $vdanpai ) break;
				// ksort($vdanpai);
				foreach ( $vsitiao as $v => $oa )
				{
					$ml = $l = $oa['l'];
					$pl = floor($ml/2);
					$do = $oa['do'];
					$to = $oa['to'];
					$cs = $oa['cs'];
					$mbl = $oa['mbl'];
					$mbv = $oa['mbv'];
					$pbl = $pbv = 0;
					$ujn = $oa['ujn'];
					$ujt = $oa['ujt'];
					foreach ( $vdanpai as $w => $ob )
					{
						if ( $v == $w || $ujn + $ob['ujn'] > $m ) continue;
						$do = array_merge($do, $ob['do']);
						$to = array_merge($to, $ob['to']);
						$cs = array_merge($cs, $ob['cs']);
						$mbl += $ob['mbl'];
						$mbv = max($mbv, $ob['mbv']);
						$pbl += $ob['mbl'];
						$pbv = max($pbv, $ob['pbv']);
						$ujn += $ob['ujn'];
						$ujt = max($ujt, $ob['ujt']);
						$l += $ob['l'];
						$pl--;
						if ( ! $pl ) break;
					}
					if ( $pl ) continue;
					$viable['11'.($l>9?$l:"0{$l}").($v>9?$v:"0{$v}").self::cardsWeight($t,$l,$v,$mbl,$mbv,$pbl,$pbv,$ujn,$j,$ujt)] = array('t'=>$t,'l'=>$l,'v'=>$v,'do'=>$do,'to'=>$to,'cs'=>$cs,'mbl'=>$mbl,'mbv'=>$mbv,'pbl'=>$pbl,'pbv'=>$pbv,'ujn'=>$ujn,'ujv'=>$j,'ujt'=>$ujt);
				}
			break;
			case 12:
				$vsitiao = self::cardsFill($acv, 86, $cl-4, $cv, $m, $j, $big, 1, 1);//使用内部扩展牌型
				if ( ! $vsitiao ) break;
				$vduipai = self::cardsFill($acv, 2, 2, 0, $m, $j, 0, 1, 0);//使用内部非扩展牌型
				if ( ! $vduipai ) break;
				// ksort($vduipai);
				foreach ( $vsitiao as $v => $oa )
				{
					$ml = $l = $oa['l'];
					$pl = floor($ml/2);
					$do = $oa['do'];
					$to = $oa['to'];
					$cs = $oa['cs'];
					$mbl = $oa['mbl'];
					$mbv = $oa['mbv'];
					$pbl = $pbv = 0;
					$ujn = $oa['ujn'];
					$ujt = $oa['ujt'];
					foreach ( $vduipai as $w => $ob )
					{
						if ( $v == $w || $ujn + $ob['ujn'] > $m ) continue;
						$do = array_merge($do, $ob['do']);
						$to = array_merge($to, $ob['to']);
						$cs = array_merge($cs, $ob['cs']);
						$mbl += $ob['mbl'];
						$mbv = max($mbv, $ob['mbv']);
						$pbl += $ob['mbl'];
						$pbv = max($pbv, $ob['pbv']);
						$ujn += $ob['ujn'];
						$ujt = max($ujt, $ob['ujt']);
						$l += $ob['l'];
						$pl--;
						if ( ! $pl ) break;
					}
					if ( $pl ) continue;
					$viable['12'.($l>9?$l:"0{$l}").($v>9?$v:"0{$v}").self::cardsWeight($t,$l,$v,$mbl,$mbv,$pbl,$pbv,$ujn,$j,$ujt)] = array('t'=>$t,'l'=>$l,'v'=>$v,'do'=>$do,'to'=>$to,'cs'=>$cs,'mbl'=>$mbl,'mbv'=>$mbv,'pbl'=>$pbl,'pbv'=>$pbv,'ujn'=>$ujn,'ujv'=>$j,'ujt'=>$ujt);
				}
			break;
			case 86 ://伪造牌型 四条/虚拟炸
				$l = 4;
				if ( $in ) {
					foreach ( $acv as $v => $n )
					{
						if ( $big && $cv >= $v ) break;
						if ( $v > 13 || $m + $n < $l ) continue;//no 15 14 and enough
						if ( $n == 1 ) $viable[$v] = array('t'=>87,'l'=>$l,'v'=>$v,'do'=>array($v,$j,$j,$j),'to'=>array($v,$v,$v),'cs'=>array($v,$v,$v,$v),'mbl'=>$m+$n-$l,'mbv'=>$j,'pbl'=>0,'pbv'=>0,'ujn'=>$l-$n,'ujv'=>$j,'ujt'=>$v);
						elseif ( $n == 2 ) $viable[$v] = array('t'=>87,'l'=>$l,'v'=>$v,'do'=>array($v,$v,$j,$j),'to'=>array($v,$v),'cs'=>array($v,$v,$v,$v),'mbl'=>$m+$n-$l,'mbv'=>$j,'pbl'=>0,'pbv'=>0,'ujn'=>$l-$n,'ujv'=>$j,'ujt'=>$v);
						elseif ( $n == 3 ) $viable[$v] = array('t'=>87,'l'=>$l,'v'=>$v,'do'=>array($v,$v,$v,$j),'to'=>array($v),'cs'=>array($v,$v,$v,$v),'mbl'=>$m+$n-$l,'mbv'=>$j,'pbl'=>0,'pbv'=>0,'ujn'=>$l-$n,'ujv'=>$j,'ujt'=>$v);
						else $viable[$v] = array('t'=>88,'l'=>$l,'v'=>$v,'do'=>array($v,$v,$v,$v),'to'=>array(),'cs'=>array($v,$v,$v,$v),'mbl'=>$n-$l,'mbv'=>$v,'pbl'=>0,'pbv'=>0,'ujn'=>0,'ujv'=>0,'ujt'=>0);
					}
					$n = 0; $v = $j;
					if ( $m + $n >= $l && ( ! $big || ! ($big && $cv >= $v) ) ) {
						$viable[$v] = array('t'=>89,'l'=>$l,'v'=>$v,'do'=>array($j,$j,$j,$j),'to'=>array($v,$v,$v,$v),'cs'=>array($v,$v,$v,$v),'mbl'=>$m+$n-$l,'mbv'=>$j,'pbl'=>0,'pbv'=>0,'ujn'=>$l-$n,'ujv'=>$j,'ujt'=>$v);
					}
					if ( ! $ex ) break;
					$bcv = array_diff(range(1, 13), array_keys($acv), array($j));//others no 15 14
					if ( ! $bcv ) break;
					foreach ( $bcv as $v )
					{
						if ( $big && $cv >= $v ) continue;
						$viable[$v] = array('t'=>86,'l'=>$l,'v'=>$v,'do'=>array($j,$j,$j,$j),'to'=>array($v,$v,$v,$v),'cs'=>array($v,$v,$v,$v),'mbl'=>$m+$n-$l,'mbv'=>$j,'pbl'=>0,'pbv'=>0,'ujn'=>$l-$n,'ujv'=>$j,'ujt'=>$v);
					}
					break;
				}
			break;
			case 87:
				$l = 4;
				$vsitiao = self::cardsFill($acv, 86, $cl, $cv, $m, $j, $big, 1, 0);//使用内部非扩展牌型
				if ( ! $vsitiao ) break;
				foreach ( $vsitiao as $v => $o )
				{
					if ( $o['t'] != $t ) continue;
					$o['t'] = $t;
					$mbl = $o['mbl'];
					$mbv = $o['mbv'];
					$pbl = $o['pbl'];
					$pbv = $o['pbv'];
					$ujn = $o['ujn'];
					$ujt = $o['ujt'];
					$viable['8704'.($v>9?$v:"0{$v}").self::cardsWeight($t,$l,$v,$mbl,$mbv,$pbl,$pbv,$ujn,$j,$ujt)] = $o;
				}
			break;
			case 88:
				$l = 4;
				$vsitiao = self::cardsFill($acv, 86, $cl, $cv, $m, $j, $big, 1, 0);//使用内部非扩展牌型
				if ( ! $vsitiao ) break;
				foreach ( $vsitiao as $v => $o )
				{
					if ( $o['t'] != $t ) continue;
					$o['t'] = $t;
					$mbl = $o['mbl'];
					$mbv = $o['mbv'];
					$pbl = $o['pbl'];
					$pbv = $o['pbv'];
					$ujn = $o['ujn'];
					$ujt = $o['ujt'];
					$viable['8804'.($v>9?$v:"0{$v}").'999'] = $o;
				}
			break;
			case 89:
				$l = 4;
				$vsitiao = self::cardsFill($acv, 86, $cl, $cv, $m, $j, $big, 1, 0);//使用内部非扩展牌型
				if ( ! $vsitiao ) break;
				foreach ( $vsitiao as $v => $o )
				{
					if ( $o['t'] != $t ) continue;
					$o['t'] = $t;
					$mbl = $o['mbl'];
					$mbv = $o['mbv'];
					$pbl = $o['pbl'];
					$pbv = $o['pbv'];
					$ujn = $o['ujn'];
					$ujt = $o['ujt'];
					$viable['8904'.($v>9?$v:"0{$v}").'999'] = $o;
				}
			break;
			case 99:
				if ( isset($acv[14]) && isset($acv[15]) ) {
					$viable['990215999'] = array('t'=>99,'l'=>2,'v'=>15,'do'=>array(14, 15),'to'=>array(),'cs'=>array(14, 15),'mbl'=>0,'mbv'=>0,'pbl'=>0,'pbv'=>0,'ujn'=>0,'ujv'=>0,'ujt'=>0);
				}
			break;
			default:
			break;
		}
		if ( $viable ) ksort($viable);
		return $viable;
	}

	//填充为指定牌型
	//$hands	array 	源牌，必须指定 已倒序
	//$joker	int 	赖子，默认0没有 [1-13]
	//$type		int 	牌型，默认0不限 [1-12|87-89|99]
	//$len		int 	牌长，默认0不限 [1-n]
	//$value	int 	牌值，默认0不限 [1-15]
	//return 	array 	0没有空缺的有效牌型组合 1有空缺的可填充牌型组合 2空数组
	//			array('880406'=>array(...), '88040606'=>array(...), '8804060606'=>array(...))
	static function cardsFillup( $hands, $joker=0, $type=0, $len=0, $value=0 )
	{
		if ( ! $hands || $type == 99 ) return array();
		$joker = intval($joker); $type = intval($type); $len = intval($len); $value = intval($value);
		if ( ! in_array($joker, range(1, 13)) ) return self::cardsViables($hands);
		$cards = array_diff($hands, array($joker));//优先牌
		$handsnum = count($hands);//手牌数
		$cardsnum = count($cards);//优牌数
		$jokernum = $handsnum - $cardsnum;//赖子数
		if ( ! $jokernum ) return self::cardsViable_(self::cardsAllTypes($hands), $type, $len, $value);
		$handsacv = array_count_values($hands);//手牌各牌值个数
		$cardsacv = array_count_values($cards);//优先牌各牌值个数
		// cardsFill($acv, $t, $cl, $cv, $m, $j, $big=0, $in=0, $ex=0, $ml=2 );
		$viable = $a = array();
		if ( $type && $len && $value ) {
			$viable = self::cardsFill($cardsacv, $type, $len, $value, $jokernum, $joker, 1);
			if ( !$viable ) {
				if ( $type < 86 ) {
					$a = array(87, 88, 89, 99);
				} elseif ( $type == 87 ) {
					$a = array(88, 89, 99);
				} elseif ( $type == 88 ) {
					$a = array(89, 99);
				} elseif ( $type == 89 ) {
					$a = array(99);
				}
			}
		} else {
			$a = array(7, 6, 10, 9, 8, 5, 4, 2, 1, 3, 87, 88, 89, 99);
		}
		if ( ! $viable && $a ) {
			foreach ( $a as $t )
			{
				$viable = self::cardsFill($cardsacv, $t, 0, 0, $jokernum, $joker);
				if ( $viable ) break;
			}
		}
		if ( $viable ) $viable = reset($viable);
		return $viable;
	}


	//适配可用牌型 返回可以叫牌/跟牌的牌型组合
	//$hands	当前适配者的手牌
	//$calls	叫牌者的叫牌内容，没有则意味着被动适配者处于叫牌环节
	//$is_rival	叫牌者是我的对手？
	static function cardsViables( $hands, $calls=array(), $is_rival=0 )
	{
		if ( ! $hands ) return array();
		//处理叫牌
		if ( ! $calls ) return self::cardsBidTypes($hands);
		//叫牌方牌型
		$call = self::cardsCheck($calls);
		//叫牌方无效或双王炸
		if ( ! $call || ! $call['t'] || $call['t'] == 99 ) return array();
		//跟牌方牌型
		$types = $is_rival ? self::cardsAllTypes($hands) : self::cardsFitTypes($hands);
		if ( ! $types ) return array();
		return self::cardsViable_($types, $call['t'], $call['l'], $call['v']);
	}

	static function cardsViable_( $types, $type, $len, $value )
	{
		$viable = array();
		$tl = $type * 100 + $len;
		$tlv = $type * 10000 + $len * 100 + $value;
		foreach ( $types as $k => $cards )
		{
			if ( ( $type > 86 && substr($k, 0, 6) > $tlv ) ||
				 ( $type < 86 && substr($k, 0, 2) > 86 ) ||
				 ( $tl == substr($k, 0, 4) && $value < substr($k, 4, 2) )
			) $viable[$k] = $cards;
		}
		if ( $viable ) krsort($viable);
		return $viable;
	}

	static function jokerToNewDec( $jokerOld )
	{
		$jokall = array('1','2','3','4','5','6','7','8','9','a','b','c','d');
		if ( ! in_array($jokerOld, $jokall) ) return 0;
		$jokerNew = self::cardToNew('6'.$jokerOld);
		return hexdec($jokerNew[1]);
	}

	//叫牌/跟牌逻辑 带赖子
	static function jokerLogic( $table, $prev, $next, $mine )
	{
		$joker = $table['jokerDec'];
		$type = $table['type'];
		//我的手牌
		$mine['hand'] = self::cardsSort($mine['hand']);
		$mine_hand_dec = self::cardsDec($mine['hand']);
		$mine_hane_dec = array_diff($mine['hand'], array($joker));//剔出赖子的十进制手牌
		//上家手牌
		$prev['hand'] = self::cardsSort($prev['hand']);
		$prev_hand_dec = self::cardsDec($prev['hand']);
		//下家手牌
		$next['hand'] = self::cardsSort($next['hand']);
		$next_hand_dec = self::cardsDec($next['hand']);
		//已出牌面
		$table_out_dec = self::cardsDec($table['outs']);
		//叫牌牌面
		$table_bid_dec = self::cardsDec($table['card']);
		//叫牌牌型
		$table_bid_type = self::cardsCheck($table_bid_dec);
		$table_bid_type['t'] = $type;
		//临时代码
		$res = self::cardsFillup($mine_hand_dec, $joker, $table_bid_type['t'], $table_bid_type['l'], $table_bid_type['v']);
		if ( ! $res ) return array();
		$jokto = $res['to'];
		foreach ( $jokto as $k => $v )
		{
			$cardOld = self::cardToOld('1'.dechex($v));
			$jokto[$k] = $cardOld[1];
		}
		$outs['jokto'] = $jokto;
		$outs['plays'] = self::cardsDye($mine['hand'], $res['do'], $mine_hand_dec);//配上花色
		return $outs;


		//1我是地主 0我是农民
		$is_self_lord = intval($table['lord'] == $mine['posi']);
		//1我来叫牌 0我是跟牌
		$is_self_bidr = intval(! $table_bid_dec || ($table['call'] == $mine['posi']));
		//1下家地主 0下家农民
		$is_next_lord = intval($table['lord'] == $next['posi']);
		//1下家叫牌 0下家跟牌
		$is_next_bidr = intval(! $is_self_bidr && $table['call'] == $next['posi']);
		//1上家地主 0上家农民
		$is_prev_lord = intval($table['lord'] == $prev['posi']);
		//1上家叫牌 0上家跟牌
		$is_prev_bidr = intval(! $is_self_bidr && $table['call'] == $prev['posi']);
		//叫家手牌
		$bidr_hand_dec = $is_self_bidr ? $mine_hand_dec : ( $is_prev_bidr ? $prev_hand_dec : $next_hand_dec );
		//1对手叫牌 0非对手或非叫
		$is_bidr_rival = intval(! $is_self_bidr && ($is_self_lord || ($is_prev_lord && $is_prev_bidr) || ($is_next_lord && $is_next_bidr)));
		//1下家对手 0下家同伴
		$is_next_rival = intval($is_self_lord || $is_next_lord);
		$num_bidr_hand = count($bidr_hand_dec);
		$num_prev_hand = count($prev_hand_dec);
		$num_next_hand = count($next_hand_dec);
		$num_mine_hand = count($mine_hand_dec);
		$num_lord_hand = $is_next_lord ? $num_next_hand : $num_prev_hand;
		$num_bid_cards = count($table_bid_dec);
		//我来叫牌下家对手
		if ( $is_self_bidr && $is_next_rival )
		{
			if 		 ( $num_mine_hand > 8 && $num_next_hand > 8 ) {
				$handv = self::cardsViables($mine_hane_dec);
				//去掉赖子 单顺子 > 双顺子 > 对子牌 > 单张牌
				//加上赖子 单顺子 > 双顺子 > 小飞机 > 小三带 > 飞机类 > 软炸弹 > 硬炸弹 > 赖子炸 > 双王炸 > 四带类 > 对子牌 > 单张牌
			} elseif ( $num_mine_hand > 8 && $num_next_hand > 4 ) {
				//去掉赖子 单顺子 > 双顺子 > 对子牌 > 单张牌
				//加上赖子 单顺子 > 双顺子 > 小飞机 > 小三带 > 飞机类 > 软炸弹 > 硬炸弹 > 赖子炸 > 双王炸 > 四带类 > 对子牌 > 单张牌
			} elseif ( $num_mine_hand > 8 && $num_next_hand < 5 ) {
			} elseif ( $num_mine_hand > 4 && $num_next_hand > 8 ) {
			} elseif ( $num_mine_hand > 4 && $num_next_hand > 4 ) {
			} elseif ( $num_mine_hand > 4 && $num_next_hand < 5 ) {
			} elseif ( $num_mine_hand < 5 && $num_next_hand > 8 ) {
				//去掉赖子 单顺子 > 双顺子 > 对子牌 > 单张牌
				//加上赖子 单顺子 > 双顺子 > 小飞机 > 小三带
				//加上赖子 飞机类 > 软炸弹 > 硬炸弹 > 赖子炸 > 双王炸 > 四带类 > 对子牌 > 单张牌
			} elseif ( $num_mine_hand < 5 && $num_next_hand > 4 ) {
			} elseif ( $num_mine_hand < 5 && $num_next_hand < 5 ) {
			} else {
			}
			//加上赖子
			//单顺子 > 飞机类 > 双顺子 > 三带类 > 对子牌 > 单张牌 > 软炸弹 > 硬炸弹 > 赖子炸 > 双王炸 > 四带类
		}
		//我来叫牌下家同伴
		elseif ( $is_self_bidr && ! $is_next_rival )
		{
		}
		//我来跟牌对手叫牌下家对手
		elseif ( ! $is_self_bidr && $is_bidr_rival && $is_next_rival )
		{
		}
		//我来跟牌对手叫牌下家同伴
		elseif ( ! $is_self_bidr && $is_bidr_rival && ! $is_next_rival )
		{
		}
		//我来跟牌同伴叫牌下家对手
		elseif ( ! $is_self_bidr && ! $is_bidr_rival && $is_next_rival )
		{
		}
		//我来跟牌同伴叫牌下家同伴
		else
		{
		}
		return array();
	}

	//叫牌/跟牌逻辑
	static function playLogic( $table, $prev, $next, $mine )
	{
		$joker = $table['jokerDec'];
		$type = $table['type'];
		//我的手牌
		$mine['hand'] = self::cardsSort($mine['hand']);
		$mine_hand_dec = self::cardsDec($mine['hand']);
		//上家手牌
		$prev['hand'] = self::cardsSort($prev['hand']);
		$prev_hand_dec = self::cardsDec($prev['hand']);
		//下家手牌
		$next['hand'] = self::cardsSort($next['hand']);
		$next_hand_dec = self::cardsDec($next['hand']);
		//已出牌面
		$table_out_dec = self::cardsDec($table['outs']);
		//叫牌牌面
		$table_bid_dec = self::cardsDec($table['card']);
		//叫牌牌型
		$table_bid_type = self::cardsCheck($table_bid_dec);
		$table_bid_type['t'] = $type;
		//1我是地主 0我是农民
		$is_self_lord = intval($table['lord'] == $mine['posi']);
		//1我来叫牌 0我是跟牌
		$is_self_bidr = intval(! $table_bid_dec || ($table['call'] == $mine['posi']));
		//1下家地主 0下家农民
		$is_next_lord = intval($table['lord'] == $next['posi']);
		//1下家叫牌 0下家跟牌
		$is_next_bidr = intval(! $is_self_bidr && $table['call'] == $next['posi']);
		//1上家地主 0上家农民
		$is_prev_lord = intval($table['lord'] == $prev['posi']);
		//1上家叫牌 0上家跟牌
		$is_prev_bidr = intval(! $is_self_bidr && $table['call'] == $prev['posi']);
		//叫家手牌
		$bidr_hand_dec = $is_self_bidr ? $mine_hand_dec : ( $is_prev_bidr ? $prev_hand_dec : $next_hand_dec );
		//1对手叫牌 0非对手或非叫
		$is_bidr_rival = intval(! $is_self_bidr && ($is_self_lord || ($is_prev_lord && $is_prev_bidr) || ($is_next_lord && $is_next_bidr)));
		//1下家对手 0下家同伴
		$is_next_rival = intval($is_self_lord || $is_next_lord);
		// print_r($is_self_lord?'我是地主':'我是农民');
		// print_r($is_self_bidr?'叫牌':'跟牌');
		// print_r($is_bidr_rival?'/对手正在叫牌/':'/非对手或非叫/');
		// print_r($is_next_rival?'下家是对手':'下家是同伴');
		//AI技能
		$AI = self::getAiSkill(1);//1
		$num_bidr_hand = count($bidr_hand_dec);
		$num_prev_hand = count($prev_hand_dec);
		$num_next_hand = count($next_hand_dec);
		$num_mine_hand = count($mine_hand_dec);
		$num_lord_hand = $is_next_lord ? $num_next_hand : $num_prev_hand;
		$num_bid_cards = count($table_bid_dec);
		//基础候选牌组
		$viable = self::cardsViables($mine_hand_dec, $table_bid_dec);
		//我叫牌-无可选
		if ( ! $viable && $is_self_bidr ) {
			gerr("自动打牌出错 ".json_encode($mine['hand']));
			return array();
		}
		//对手叫-无可选
		$is_break = 0;
		if ( ! $viable && $is_bidr_rival )
		{
			if ( $AI['is_unpack'] && $num_bidr_hand < 6 ) {
				if ( $viable = self::cardsViables($mine_hand_dec, $table_bid_dec, 1) ) {
					//要不起-拆牌要
					$is_break = 1;
				} else {
					//print_r('“要不起-拆不掉”');
					return array();
				}
			} else {
				//print_r('“要不起-直接过”');
				return array();
			}
		}
		//规整预选牌，规整同时可依据技能直接返回牌型
		//优先牌			必胜牌			回手牌			起手牌			其他牌			拆开牌
		$cards_first = $cards_ender = $cards_rebid = $cards_start = $cards_other = $cards_break = $levels = array();
		$viables = $viable;//bak
		foreach ( $viable as $k => $v )
		{
			$tlv = str_split($k, 2);
			$levels[$k] = self::cardsGetLevel($tlv[0], $tlv[1], $tlv[2]);
			//拆开牌 不与其它牌型组合重合
			if ( $is_break && ($tlv[0] == 1 || $tlv[0] == 2 || $tlv[0] == 3) ) {
				$cards_break[$k] = $v; unset($viable[$k]);	//拆开后的有效单张牌/一对牌/三不带，尽量不使用
				continue;
			}
			//优先牌 可与其他牌型组合重合
			if 		 ( $tlv[0] == 6 && ( $num_mine_hand < 10 || !($tlv[2] == 12) ) ) {
				$cards_first[$k] = $v;	//单顺子
			} elseif ( $tlv[0] == 7 && ( $num_mine_hand < 12 || !($tlv[2] == 12) ) ) {
				$cards_first[$k] = $v;	//双顺子
			} elseif ( in_array($tlv[0], array('09','10')) && ( $num_mine_hand < 14 || !in_array($tlv[2],array('09','10','11')) ) ) {
				$cards_first[$k] = $v;	//飞机单//飞机对
			} elseif ( in_array($tlv[0],array('04','05')) && ( $tlv[2] < 7 || $num_mine_hand < 8 || !array_intersect(array(12,13,14,15),$v) ) ) {
				$cards_first[$k] = $v;	//三带
			}
			//必胜牌//回手牌 不与其它牌型组合重合
			if 		 ( $tlv[0] == 99 ) {
				$cards_ender[$k] = $v; unset($viable[$k]);	//双王炸必胜
			} elseif ( ($AI['is_peekother'] || $AI['is_remember']) && !(self::cardsViables($prev_hand_dec, $v, 1) || self::cardsViables($next_hand_dec, $v, 1)) ) {
				$cards_ender[$k] = $v; unset($viable[$k]);	//偷牌记牌 算出必胜
			} elseif ( false && $AI['is_remember'] ) {
				$cards_ender[$k] = $v; unset($viable[$k]);	//记牌暂时不做单独处理
			} elseif ( $AI['is_remainder'] && count($prev_hand_dec) < $tlv[1] && count($next_hand_dec) < $tlv[1] ) {
				$cards_ender[$k] = $v; unset($viable[$k]);	//算牌算出必胜(依据长度计算)
			} elseif ( $tlv[0] == 88 ) {
				$cards_rebid[$k] = $v; unset($viable[$k]);	//硬炸弹默认回手
			} elseif ( $tlv[0] == 12 || $tlv[0] == 11 ) {
				$cards_rebid[$k] = $v; unset($viable[$k]);	//四带二默认回手
			} elseif ( $tlv[2] == 15 ) {
				$cards_rebid[$k] = $v; unset($viable[$k]);	//大王默认回手
			} elseif ( $tlv[2] == 14 && in_array(15, $table_out_dec) ) {	//这个逻辑暂用，在$AI['is_remember']完成之后抹掉
				$cards_rebid[$k] = $v; unset($viable[$k]);	//小王默认回手
			} elseif ( $tlv[2] == 13 && $tlv[1] > 1 ) {						//这个逻辑暂用，在$AI['is_remember']完成之后抹掉
				$cards_rebid[$k] = $v; unset($viable[$k]);	//22/222默认回手
			} elseif ( $tlv[2] == 12 && in_array(intval($tlv[0]), range(6, 10)) ) {
				$cards_rebid[$k] = $v; unset($viable[$k]);	//单顺子//双顺子//飞机头/飞机
			}
		}
		//起手牌 不与其它牌型组合重合
		foreach ( $viable as $k => $v )
		{
			foreach ( $cards_ender as $kk => $vv )
			{
				$tlv = str_split($kk, 2);
				if ( $tlv[0] > 86 ) continue;//不对各种炸做匹配
				if ( self::cardsCompare($v, $vv) == 2 ) {
					$cards_start[$k] = $v; unset($viable[$k]);
					break;
				}
			}
			foreach ( $cards_rebid as $kk => $vv )
			{
				$tlv = str_split($kk, 2);
				if ( $tlv[0] > 86 ) continue;//不对各种炸做匹配
				if ( self::cardsCompare($v, $vv) == 2 ) {
					$cards_start[$k] = $v; unset($viable[$k]);
					break;
				}
			}
		}
		//其他牌 不与其它牌型组合重合
		foreach ( $viable as $k => $v )
		{
			$cards_other[$k] = $v; unset($viable[$k]);
		}
		//顺序规整
		if ( $cards_first ) krsort($cards_first);
		if ( $cards_ender ) krsort($cards_ender);
		if ( $cards_rebid ) krsort($cards_rebid);
		if ( $cards_start ) krsort($cards_start);
		if ( $cards_other ) krsort($cards_other);
		if ( $cards_break ) krsort($cards_break);
		//出牌逻辑
		$cards = array();
		//1、优先一把出完
		if ( $is_self_bidr && $AI['is_bigout'] )
		{
			$_cards_ender = $cards_ender;
			$_cards_rebid = $cards_rebid;
			$_cards_start = $cards_start;
			$_cards_other = $cards_other;
			foreach ( $_cards_ender as $k => $v )
			{
				$tlv = str_split($k, 2);
				if ( ! in_array($tlv[0], array('04', '05', '09', '10')) ) continue;
				foreach ( $_cards_rebid as $kk => $vv )
				{
					$tlv = str_split($kk, 2);
					if ( in_array($tlv[0], array('01', '02')) && array_intersect($v, $vv) ) {
						unset($_cards_rebid[$kk]);
					}
				}
				foreach ( $_cards_start as $kk => $vv )
				{
					$tlv = str_split($kk, 2);
					if ( in_array($tlv[0], array('01', '02')) && array_intersect($v, $vv) ) {
						unset($_cards_start[$kk]);
					}
				}
				foreach ( $_cards_other as $kk => $vv )
				{
					$tlv = str_split($kk, 2);
					if ( in_array($tlv[0], array('01', '02')) && array_intersect($v, $vv) ) {
						unset($_cards_other[$kk]);
					}
				}
			}
			if ( count($_cards_rebid) + count($_cards_start) + count($_cards_other) < 2 ) {
				$cards = $cards_first ? end($cards_first) : ( $_cards_rebid ? end($_cards_rebid) : ( $cards_ender ? end($cards_ender) : ( $_cards_start ? end($_cards_start) : end($_cards_other) ) ) );
			}
		}
		//1、优先一把出完
		if ( $cards )
		{
			//print_r('“耍大牌-一把完”');
		}
		//2、对手叫我来跟
		elseif ( ! $is_self_bidr && $is_bidr_rival )
		{
			//优先牌 > 起手牌 > 其他牌 > 回手牌 > 必胜牌 > pass
			//对手手牌过少，且下家是对手，且下家手牌少于我时，跟最大，否则跟最小
			if ( $cards_first )
			{
				$cards = ($num_bidr_hand < 6 && $is_next_rival && $num_next_hand < $num_mine_hand) ? reset($cards_first) : end($cards_first);
				//print_r($is_break ? '“拆了压-优先牌”' : '“跟对手-优先牌”');
			}
			elseif ( $cards_start )
			{
				$cards = ($num_bidr_hand < 6 && $is_next_rival && $num_next_hand < $num_mine_hand) ? reset($cards_start) : end($cards_start);
				//print_r($is_break ? '“拆了压-起手牌”' : '“跟对手-起手牌”');
			}
			elseif ( $cards_other )
			{
				$cards = ($num_bidr_hand < 6 && $is_next_rival && $num_next_hand < $num_mine_hand) ? reset($cards_other) : end($cards_other);
				//print_r($is_break ? '“拆了压-其他牌”' : '“跟对手-其他牌”');
			}
			elseif ( $cards_rebid )
			{
				foreach ( $cards_rebid as $k => $v )
				{
					$tlv = str_split($k, 2);
					if ( $tlv[0] != $table_bid_type['t'] && $num_bidr_hand > 6 ) {
						unset($cards_rebid[$k]);
					}
				}
				$cards = $cards_rebid ? end($cards_rebid) : array();
				//print_r($cards ? ($is_break ? '“拆了压-回手牌”' : '“跟对手-回手牌”') : '“跟对手-先不跟”');
			}
			elseif ( $cards_ender )
			{
				foreach ( $cards_ender as $k => $v )
				{
					$tlv = str_split($k, 2);
					if ( $tlv[0] != $table_bid_type['t'] && $num_bidr_hand > 5 ) {
						unset($cards_ender[$k]);
					}
				}
				$cards = $cards_ender ? end($cards_ender) : array();
				//print_r($cards ? ($is_break ? '“拆了压-必胜牌”' : '“跟对手-必胜牌”') : '“跟对手-放一马”');
			}
			elseif ( $cards_break )
			{
				$cards = reset($cards_break);
				//print_r('“跟对手-拆了牌”');
			}
			else
			{
				$cards = array();
				//print_r('“跟对手-过一手”');
			}
		}
		//同伴叫我来跟
		elseif ( ! $is_self_bidr && ! $is_bidr_rival )
		{
			//优先牌 > 起手牌 > 其他牌 > pass
			$val_ = reset($table_bid_dec);
			$next_hand_type = self::cardsCheck($next_hand_dec);
			//如果同伴只剩一张牌且打出的牌数大于地主手牌数，肯定过
			$is_must_pass = false;
			if ( $num_bidr_hand == 1 && $num_bid_cards > $num_lord_hand )
			{
				$cards = array();
				$is_must_pass = true;
				//print_r('“跟同伴-必须过”');
			}
			//如果上家同伴叫牌少，且与地主的手牌牌型同样，且小于地主手牌，一定要管住
			elseif ( $is_next_lord && $num_bid_cards < 4 && $table_bid_type['t'] == $next_hand_type['t'] && $table_bid_type['v'] < $next_hand_type['v'] )
			{
				foreach ( $cards_rebid as $k => $v )
				{
					$tlv = str_split($k, 2);
					if ( $tlv[0] > 80 || $tlv[2] < $next_hand_type['v'] ) {
						unset($cards_rebid[$k]);
					}
				}
				foreach ( $cards_ender as $k => $v )
				{
					$tlv = str_split($k, 2);
					if ( $tlv[0] > 80 || $tlv[2] < $next_hand_type['v'] ) {
						unset($cards_ender[$k]);
					}
				}
				foreach ( $cards_start as $k => $v )
				{
					$tlv = str_split($k, 2);
					if ( $tlv[0] > 80 || $tlv[2] < $next_hand_type['v'] ) {
						unset($cards_start[$k]);
					}
				}
				foreach ( $cards_break as $k => $v )
				{
					$tlv = str_split($k, 2);
					if ( $tlv[0] > 80 || $tlv[2] < $next_hand_type['v'] ) {
						unset($cards_break[$k]);
					}
				}
				foreach ( $cards_other as $k => $v )
				{
					$tlv = str_split($k,2);
					if ( $tlv[0] > 80 || $tlv[2] < $next_hand_type['v'] ) {
						unset($cards_other[$k]);
					}
				}
				$cards = $cards_rebid ? end($cards_rebid) : ( $cards_ender ? end($cards_ender) : ( $cards_start ? reset($cards_start) : ( $cards_break ? reset($cards_break) : ( $cards_other ? reset($cards_other) : array() ) ) ) );
				if ( $cards ) {
					//print_r('“跟同伴-抬的起”');
				} else {
					$viable_ = $AI['is_unpack'] ? self::cardsViables($mine_hand_dec, $table_bid_dec, 1) : array();
					$cards = $viable_ ? reset($viable_) : array();
					//print_r($cards ? '“跟同伴-拆牌抬”' : '“跟同伴-抬不起”');
				}
			}
			elseif ( $cards_first )
			{
				foreach ( $cards_first as $k => $v )
				{
					$tlv = str_split($k, 2);
					if ( $val_ < 7 && $tlv[2] < 12 ) {
						$cards = $v;
					}
				}
				$cards = $cards ? $cards : ( $num_bidr_hand + 3 < $num_mine_hand ? array() : end($cards_first) );
				//print_r($cards ? '“跟同伴-优先牌”' : '“跟同伴-优先过”');
			}
			elseif ( $cards_start )
			{
				foreach ( $cards_start as $k => $v )
				{
					$tlv = str_split($k, 2);
					if ( $val_ < 7 && $tlv[2] < 12 ) {
						$cards = $v;
					}
				}
				$cards = $cards ? $cards : ( $num_bidr_hand + 3 < $num_mine_hand ? array() : end($cards_start) );
				//print_r($cards ? '“跟同伴-起手牌”' : '“跟同伴-起手过”');
			}
			elseif ( $cards_other )
			{
				foreach ( $cards_other as $k => $v )
				{
					$tlv = str_split($k, 2);
					if ( $val_ < 7 && $tlv[2] < 12 ) {
						$cards = $v;
					}
				}
				$cards = $cards ? $cards : ( $num_bidr_hand > $num_mine_hand ? end($cards_other) : array() );
				//print_r($cards ? '“跟同伴-其他牌”' : '“跟同伴-其它过”');
			}
			//没有最佳跟牌策略时，尝试我来代替同伴一把出完
			if ( ! $cards && ! $is_must_pass && $viables )
			{
				//我的叫牌牌型
				$_myall_viable = self::cardsViables($mine_hand_dec);
				$_cards_ender = $_cards_viable = array();
				foreach ( $_myall_viable as $k => $v )
				{
					$tlv = str_split($k, 2);
					if ( ($AI['is_peekother'] || $AI['is_remember']) && !($is_next_rival ? self::cardsViables($next_hand_dec, $v) : self::cardsViables($prev_hand_dec, $v)) )
					{
						//偷牌记牌 算出必胜
						$_cards_ender[$k] = $v;
						unset($_myall_viable[$k]);
					}
					if ( $table_bid_type['t'] == $tlv[0] && $table_bid_type['v'] < $tlv[2] )
					{
						$_cards_viable[$k] = $v;
					}
				}
				$cards = count($_cards_ender) > 0 && count($_myall_viable) < 1 && $_cards_viable ? end($_cards_viable) : array();
				//print_r($cards ? '“跟同伴-让我来”' : '“跟同伴-让他来”');
			}
			elseif ( ! $cards )
			{
				//print_r('“跟同伴-过了吧”');
			}
		}
		//我来叫对手跟
		elseif ( $is_self_bidr && $is_next_rival )
		{
			if ( $num_next_hand < 3 )
			{
				$next_hand_type = self::cardsCheck($next_hand_dec);
				foreach ( $viables as $k => $v )
				{
					$tlv = str_split($k, 2);
					if ( $tlv[1] != $num_next_hand || $tlv[2] >= $next_hand_type['v'] ) {
						$cards = $v;
					}
				}
				if ( ! $cards && $AI['is_unpack'] )
				{
					$viable_ = self::cardsViables($mine_hand_dec, $table_bid_dec, 1);
					foreach ( $viable_ as $k => $v )
					{
						$tlv = str_split($k, 2);
						if ( $tlv[1] != $num_next_hand || $tlv[2] >= $next_hand_type['v'] ) {
							$cards = $v;
						}
					}
				}
			}
			elseif ( $AI['is_unsame'] && $num_next_hand < 6 )
			{
				foreach ( $cards_first as $k => $v )
				{
					if ( substr($k, 2, 2) == $num_next_hand ) {
						unset($cards_first[$k]);
						$cards_other[$k] = $v;
					}
				}
				foreach ( $cards_start as $k => $v )
				{
					if ( substr($k, 2, 2) == $num_next_hand ) {
						unset($cards_start[$k]);
						$cards_other[$k] = $v;
					}
				}
				if ( $cards_other )
				{
					krsort($cards_other);
				}
			}
			//优先牌 > 起手牌 > 其他牌 > 回手牌 > 必胜牌
			if ( $cards )
			{
				//print_r('“憋下家-急死他”');
			}
			elseif ( $cards_first )
			{
				$cards = self::cardsBetter($cards_first, $num_mine_hand);
				// print_r('“憋下家-优先牌”');
			}
			elseif ( $cards_start )
			{
				$cards = self::cardsBetter($cards_start, $num_mine_hand);
				// print_r('“憋下家-起手牌”');
			}
			elseif ( $cards_other )
			{
				if ( $num_next_hand < 4 ) {
					$cards = reset($cards_other);
				} else {
					$maxlen = 0;
					$maxlenkey = '';
					foreach ( $cards_other as $k => $v )
					{
						if ( in_array(min($mine_hand_dec), $v) && max($mine_hand_dec) - min($mine_hand_dec) > 4 ) {
							$maxlenkey = $k;
						}
					}
					if ( ! $maxlenkey ) {
						foreach ( $cards_other as $k => $v )
						{
							$_oldlen = $maxlen;
							$maxlen = max($maxlen, substr($k, 2, 2));
							$maxlenkey = $_oldlen < $maxlen ? $k : $maxlenkey;
						}
					}
					$cards = $cards_other[$maxlenkey];
				}
				//print_r('“憋下家-其他牌”');
			}
			elseif ( $cards_rebid )
			{
				$cards = end($cards_rebid);
				//print_r('“憋下家-回手牌”');
			}
			elseif ( $cards_ender )
			{
				$cards = end($cards_ender);
				//print_r('“憋下家-必胜牌”');
			}
			else
			{
				//print_r('“憋下家-？？？”');
				$cards = end($viables);
			}
		}
		//我来叫同伴跟
		elseif ( $is_self_bidr && ! $is_next_rival )
		{
			$num_next_hand = count($next_hand_dec);
			if ( $cards_first )
			{
				if ( $AI['is_giveup'] && $num_next_hand < 6 )
				{
					ksort($cards_first);
					foreach ( $cards_first as $k => $v )
					{
						$tlv = str_split($k, 2);
						if ( $tlv[1] == $num_next_hand ) {
							$cards = $v;
							break;
						}
					}
				}
				if ( ! $cards ) $cards = self::cardsBetter($cards_first, $num_mine_hand, 1);
				//print_r('“放同伴-优先牌”');
			}
			elseif ( $cards_start )
			{
				if ( $AI['is_giveup'] && $num_next_hand < 6 )
				{
					ksort($cards_start);
					foreach ( $cards_start as $k => $v )
					{
						$tlv = str_split($k, 2);
						if ( $tlv[1] == $num_next_hand ) {
							$cards = $v;
							break;
						}
					}
				}
				if ( ! $cards ) $cards = self::cardsBetter($cards_start, $num_mine_hand, 1);
				//print_r('“放同伴-起手牌”');
			}
			elseif ( $cards_other )
			{
				if ( $AI['is_giveup'] && $num_next_hand < 6 )
				{
					ksort($cards_other);
					foreach ( $cards_other as $k => $v )
					{
						$tlv = str_split($k, 2);
						if ( $tlv[1] == $num_next_hand ) {
							$cards = $v;
							break;
						}
					}
				}
				if ( ! $cards ) $cards = self::cardsBetter($cards_other, $num_mine_hand, 1);
				//print_r('“放同伴-其他牌”');
			}
			elseif ( $cards_rebid )
			{
				$cards = end($cards_rebid);
				//print_r('“放同伴-回手牌”');
			}
			elseif ( $cards_ender )
			{
				$cards = end($cards_ender);
				//print_r('“放同伴-必胜牌”');
			}
			else
			{
				$cards = end($viables);
				//print_r('“放同伴-？？？”');
			}
		}
		else
		{
			$cards = end($viables);
			//print_r('“？？？-？？？”');
		}
		//配上花色
		if ( ! $cards ) return array();
		return self::cardsDye($mine['hand'], $cards, $mine_hand_dec);
	}

	//选出最优叫牌
	//$groups 	array 	来源牌组 tlv=>cards
	//$count 	int 	手牌总数量
	static function cardsBetter( $groups, $count, $nextPartner=0 )
	{
		$plays = $min = array();
		foreach ( $groups as $k => $v )
		{
			$min = array_merge($min, $v);
		}
		$min = min($min);
		$tmp = $tmp2 = array();
		foreach ( $groups as $k => $v )
		{
			$tlv = str_split($k, 2);
			if ( in_array($min, $v) && $tlv[0] == '04' ) {
				$plays = $v;
			}
			if ( $count > 12 && array_intersect(array(13, 14, 15), $v) ) {
				$tmp2[$k] = $v;
				continue;
			} else {
				$tmp[$tlv[1]][$tlv[2]] = $v;
			}
		}
		if ( $plays ) {	//有最小牌的最小的三带单最优先
			return $plays;
		}
		if ( $tmp ) {	//长度最大//等长最小//牌值最小
			krsort($tmp);
			$tmp = reset($tmp);
			ksort($tmp);
			return $nextPartner ? end($tmp) : reset($tmp);
		}
		if ( $tmp2 ) {	//牌多的时候，带2的带w的尽可能放后面
			krsort($tmp2);
			return $nextPartner ? end($tmp2) : reset($tmp2);
		}
		return array();
	}

	//牌组配上花色
	//$hands 	array 	原手牌 带花色
	//$plays 	array 	打出牌 无花色十进制
	//$handsDec	array 	数字牌 原手牌对应的无花色十进制手牌
	//return 	array 	打出牌 带花色
	static function cardsDye( $hands, $plays, $handsDec=array() )
	{
		if ( ! $handsDec ) $handsDec = self::cardsDec($hands);
		foreach ( $plays as $k => $v )
		{
			$j = 0;
			$i = array_search($v, $handsDec);
			while ( isset($cards[$i]) ) { $i++; }
			$cards[$i] = $hands[$i];
		}
		return array_values($cards);
	}

	//比较牌型大小
	//$cards1	已叫牌面
	//$cards2	待跟牌面
	//return	0无法比，1跟牌小，2跟牌大
	static function cardsCompare( $cards1, $cards2, $jokto1=array(), $jokto2=array() )
	{
		$c1 = self::cardsCheck($cards1);
		if ( $c1['t'] == '88' && $jokto1 ) {
			$joktonum = count($jokto1);
			if ( $joktonum == 4 ) {
				$c1['t'] = '89';
			} elseif ( $joktonum ) {
				$c1['t'] = '87';
			}
		}
		$c2 = self::cardsCheck($cards2);
		if ( $c2['t'] == '88' && $jokto2 ) {
			$joktonum = count($jokto2);
			if ( $joktonum == 4 ) {
				$c2['t'] = '89';
			} elseif ( $joktonum ) {
				$c2['t'] = '87';
			}
		}
		if ( ! $c1['t'] || ! $c2['t'] ) return 0;
		if ( $c2['t'] == $c1['t'] && $c2['l'] == $c1['l'] && $c2['v'] > $c1['v'] ) return 2;
		if ( $c2['t'] > 86 && $c1['t'] > 86 && $c2['t'] > $c1['t'] ) return 2;
		if ( $c2['t'] > 86 && $c1['t'] < 86 ) return 2;
		return 1;
	}

	static function cardsShow( $cards )
	{
		foreach ( $cards as $k => $v )
		{
			$cards[$k] = self::cardShow($v);
		}
		return $cards;
	}

	static function cardsShow2( $cards )
	{
		foreach ( $cards as $k => $v )
		{
			$cards[$k] = self::cardShow2($v);
		}
		return $cards;
	}

	static function cardShow( $card )
	{
		$color = array(
			'1'=>'<span class="crd Dmd"><span class="fac">♢</span>num</span>',
			'2'=>'<span class="crd Clb"><span class="fac">♧</span>num</span>',
			'3'=>'<span class="crd Hrt"><span class="fac">♡</span>num</span>',
			'4'=>'<span class="crd Spd"><span class="fac">♤</span>num</span>',
			'5'=>'<span class="crd Sun"><span class="fac">♔</span>num</span>',
			'6'=>'<span class="crd Mon"><span class="fac">♔</span>num</span>',
		);
		$number= array(
			'1'=>'<span class="num">3</span>',
			'2'=>'<span class="num">4</span>',
			'3'=>'<span class="num">5</span>',
			'4'=>'<span class="num">6</span>',
			'5'=>'<span class="num">7</span>',
			'6'=>'<span class="num">8</span>',
			'7'=>'<span class="num">9</span>',
			'8'=>'<span class="num">T</span>',
			'9'=>'<span class="num">J</span>',
			'a'=>'<span class="num">Q</span>',
			'b'=>'<span class="num">K</span>',
			'c'=>'<span class="num">A</span>',
			'd'=>'<span class="num">2</span>',
			'e'=>'<span class="num">w</span>',
			'f'=>'<span class="num">W</span>',
		);
		$card = strlen($card) > 1 ? $card : ('0'.$card);
		$_card = str_split($card);
		if ( $_card[1] == 'e' ) $_card[0] = '6';
		$col = $color[$_card[0]];
		$num = $number[$_card[1]];
		return str_replace('num',$num,$col);
		return $col.$num;
	}

	static function cardShow2( $card )
	{
		$number= array(
			'1'=>'<span class="num">3</span>',
			'2'=>'<span class="num">4</span>',
			'3'=>'<span class="num">5</span>',
			'4'=>'<span class="num">6</span>',
			'5'=>'<span class="num">7</span>',
			'6'=>'<span class="num">8</span>',
			'7'=>'<span class="num">9</span>',
			'8'=>'<span class="num">T</span>',
			'9'=>'<span class="num">J</span>',
			'10'=>'<span class="num">Q</span>',
			'11'=>'<span class="num">K</span>',
			'12'=>'<span class="num">A</span>',
			'13'=>'<span class="num">2</span>',
			'14'=>'<span class="num">w</span>',
			'15'=>'<span class="num">W</span>',
		);
		return $number[$card];
	}

	static function cardsEcho( $cards, $source=array() )
	{
		foreach ( $cards as $k => $v )
		{
			echo $v.' ';
		}
		if ( $source ) {
			echo 'array("'.join('","',$source).'")';
			echo '<div class="hr"></div>';
		}
	}

}//End Class


$isTestClassCard = !defined('ISTESTS');

//start WEB TEST
if ( $isTestClassCard )
{
function debug( $str ) { echo "$str<br/>"; }
define('ISLOCAL', 1);
define('ISTESTS', 1);
date_default_timezone_set('PRC');
ini_set("display_errors","on");
error_reporting(E_ALL);//E_ERROR | E_WARNING | E_PARSE
echo '<!DOCTYPE html>
<html>
<head>
<title>打牌模拟器</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="generator" content="editplus" />
<meta name="author" content="" />
<meta name="keywords" content="" />
<meta name="description" content="" />
<style type="text/css">
body{font-size:14px;font-family:"simsun";line-height:16px;white-space:nowrap;}
.hr{margin:1px;border-bottom:1px solid #eee;height:0;overflow:hidden;}
.div{border:1px solid #333;margin-bottom:5px;}
.crd .num{font-size:16px;font-weight:bold;}
.crd .fac{font-size:14px;line-height:14px;border:1px solid #eee;}
.Sun{color:#F60;}
.Mon{color:#999;}
.Spd{color:#000;}
.Hrt{color:#F00;}
.Clb{color:#06F;}
.Dmd{color:#F90;}
</style>
</head>
<body>';

$pool = Card::newCardPool(0,4);//新版牌库＋N个//硬炸弹

$isTask = 0;//是否触发牌局任务测试逻辑
$isTaskProp = 0;//是否强制只测试可行性任务
$isTaskMiss = 0;//是否强制只测试不可能任务
$taskPos = false;
// $taskPos = 2;
//下面为明显的两个农民的错误任务
// 位置[0]牌局任务[可行]模式 进入运算 ["4e","1c","2b","27","17","07","26","35","25","15","05","33","03","02","21","11","01","0b","29","06"]
// 位置[0]牌局任务[可行]模式 输出任务 [打出//硬炸弹并胜利] {"id":"44","typeid":"0","conds":[{"id":"24","name":"\u6253\u51fa\u70b8\u5f39","value":0,"ctype":"88_99","ccomp":""},{"id":"29","name":"\u8d62\u5f97\u80dc\u5229","value":0}],"prob":"2000","coins":"0","coupon":"5","name":"\u6253\u51fa\u70b8\u5f39\u5e76\u80dc\u5229","is_done":0,"is_new":0}
// 位置[2]牌局任务[可行]模式 进入运算 ["4d","1b","3a","2a","1a","0a","38","28","18","08","36","16","23","30","20","10","00","0b","29","06"]
// 位置[2]牌局任务[可行]模式 输出任务 [打出1连对并胜利] {"id":"43","typeid":"0","conds":[{"id":"19","name":"\u6253\u51fa%s\u8fde\u5bf9","value":1,"ctype":"07","ccomp":"g="},{"id":"29","name":"\u8d62\u5f97\u80dc\u5229","value":0}],"prob":"1300","coins":"0","coupon":"5","name":"\u6253\u51fa1\u8fde\u5bf9\u5e76\u80dc\u5229","is_done":0,"is_new":0}
// 位置[1]牌局任务[可行]模式 进入运算 ["3c","2c","0c","3b","39","19","09","37","34","24","14","04","13","32","22","12","31","0b","29","06"]
// 位置[1]牌局任务[可行]模式 输出任务 [打出一张王并胜利] {"id":"36","typeid":"0","conds":[{"id":"8","name":"\u6253\u51fa\u4e00\u5f20\u738b","value":0,"ctype":"01","ccomp":"v>"},{"id":"29","name":"\u8d62\u5f97\u80dc\u5229","value":0}],"prob":"1000","coins":"0","coupon":"5","name":"\u6253\u51fa\u4e00\u5f20\u738b\u5e76\u80dc\u5229","is_done":0,"is_new":0}
// $pool[0] = array("3c","2c","1c","0c","3a","2a","1a","0a","07","05","24","04","23","31","01","10","00");
// $pool[1] = array("4e","4d","3b","2b","1b","0b","39","19","09","17","36","16","14","13","03","21","30");
// $pool[2] = array("29","38","28","18","08","37","27","26","35","25","34","32","22","12","02","11","20");
// $pool['lord'] = array("06","15","33");

$cardPool[0] = Card::cardsToOld($pool[0]);//旧版牌库
$cardPool[1] = Card::cardsToOld($pool[1]);
$cardPool[2] = Card::cardsToOld($pool[2]);
$cardPool['lord'] = Card::cardsToOld($pool['lord']);
if ( $isTask )
{
	//牌局任务介入 start
	$table['seat0task'] = $table['seat1task'] = $table['seat2task'] = array();
	$table['rate'] = mt_rand(1,20)*100;
	$data_tteskrate_list = array();
	include('./include/data_tteskrate_list.php');
	$data_ttesksource_list = array();
	include('./include/data_ttesksource_list.php');
	$data_ttesk_list = array();
	include('./include/data_ttesk_list.php');
	$table['seats'] = array(0,2,1);
	$table['lordCards'] = $cardPool['lord'];
	foreach( $table['seats'] as $seatId )
	{
		if ( $taskPos !== false && $seatId != $taskPos ) continue;
		$tttype = intval(mt_rand(1,100) < 33);//0正向匹配1反向误导
		if ( $isTaskProp ) $tttype = 0;//强制输出可行性任务
		if ( $isTaskMiss ) $tttype = 1;//强制输出不可能任务
		$ttlist = array();
		foreach ( $data_ttesk_list as $k => $v )
		{
			if ( $v['typeid'] != $tttype || ! $v['prob']) continue;
			$ttlist[$k] = $v;
		}
		echo "位置[$seatId]牌局任务[".($tttype?"误导":"可行")."]模式 进入运算 ".json_encode(array_merge($pool[$seatId], $pool['lord']));
		echo "<br/>";
		if ( $tttype ) {
			$task = Card::getMissTask($cardPool[$seatId], $cardPool['lord'], $ttlist, $data_ttesksource_list, 1);
		} else {
			$task = Card::getProbTask($cardPool[$seatId], $cardPool['lord'], $ttlist, $data_ttesksource_list, 1);
		}
		if ( $task ) {
			unset($task['rooms']);unset($task['users']);unset($task['channels']);
		}
		$table["seat{$seatId}task"] = $task;
		if ( $task ) {
			echo "位置[$seatId]牌局任务[".($tttype?"误导":"可行")."]模式 输出任务 [".$task['name']."] ".json_encode($task);
		} else {
			echo "位置[$seatId]牌局任务[".($tttype?"误导":"可行")."]模式 输出任务 【触发失败】";
			echo "<script>alert('你太牛逼了，连一个任务都不给？')</script>";
		}
		echo "<br/>";
	}
	//牌局任务介入 end
}

$_table['hand'] = array_merge($pool[0],$pool['lord'],$pool[2],$pool[1]);
$_table['outs'] = array();
$_table['card'] = array();
$_table['call'] = 0;
$_table['type'] = 0;
$_table['lord'] = 0;
$_prev['posi'] = 1;
$_prev['hand'] = $pool[1];
$_next['posi'] = 2;
$_next['hand'] = $pool[2];
$_mine['posi'] = 0;
$_mine['hand'] = array_merge($pool[0],$pool['lord']);//我是地主，用$pool[1]的牌，第一把由我叫牌

$jokers = array('1'=>'A','2'=>'2','3'=>'3','4'=>'4','5'=>'5','6'=>'6','7'=>'7','8'=>'8','9'=>'9','a'=>'10','b'=>'J','c'=>'Q','d'=>'K');
$joker = strval(array_rand($jokers));


$is_test_specified_cards = 0;

if ( $is_test_specified_cards ) {
//固定发牌测试
$_prev['hand'] =
array("5e","3b","1b","39","38","18","27","46","36","26","16","45","35","25","24","42","31")
;
$_next['hand'] =
array("5f","4c","3c","2c","1c","2b","47","15","44","43","33","23","13","32","22","41","11")
;
$_mine['hand'] =
array("4d","3d","2d","1d","4b","3a","2a","1a","49","29","19","48","28","17","14","12","21","4a","37","34")
;
$joker = 'b';

//上家: w K K J T T 9 8 8 8 8 7 7 7 6 4 3 array("5e","3b","1b","39","38","18","27","46","36","26","16","45","35","25","24","42","31")
//下家: W A A A A K 9 7 6 5 5 5 5 4 4 3 3 array("5f","4c","3c","2c","1c","2b","47","15","44","43","33","23","13","32","22","41","11")
//轮到: 2 2 2 2 K Q Q Q Q J J J T T 9 9 6 6 4 3 array("4d","3d","2d","1d","4b","3a","2a","1a","49","29","19","48","28","17","14","12","21","4a","37","34")
}

echo "<div class='div'>";
echo "牌值新: ".join(" ",Card::initCardsPool());
echo "</div>";
echo "<div class='div'>";
echo "牌值旧: ".join(" ",Card::initCardsPool(1));
echo "</div>";
echo "<div class='div'>";
echo "赖子牌: ".$jokers[$joker];
echo "</div>";
$_table['joker'] = $joker;
$_table['jokerDec'] = Card::jokerToNewDec($joker);

if ( $isTask )
{
	//牌局任务检查 start
	$_seatId = 0;//地主位置
	foreach( $table['seats'] as $seatId )
	{
		if ( intval($seatId) != intval($_seatId) ) continue;
		$task = $table["seat{$seatId}task"];
		if ( ! $task || (isset($task['is_done']) && $task['is_done']) ) continue;
		$task = Card::checkTaskBcards($table['lordCards'], $task, 1);
		if ( ! $task['is_new'] ) continue;
		$table["seat{$seatId}task"] = $task;
		echo "位置[$seatId]牌局任务[".($task['typeid']?"误导":"可行")."]模式 任务更新 [".$task['name']."] ".json_encode($task);
		echo "<br/>";
		if ( !(isset($task['is_done']) && $task['is_done']) ) continue;
		$table["seat{$seatId}task"] = array();
		echo "位置[$seatId]牌局任务[".($task['typeid']?"误导":"可行")."]模式 任务完成 [".$task['name']."]";
		echo "<br/>";
		if ( $task['typeid'] ) {
			echo "<script>alert('你太牛逼了，完成了一个不可能任务')</script>";
		}
	}
	//牌局任务检查 end
}


$i = 0;
$pos = 0;
$pass = 0;
$poss = array('地主本人','地主上家','地主下家');
$playing = true;
while ( $playing )
{
	$i++;
	echo "<div class='div'>";
	echo "上家: ";
	Card::cardsEcho(Card::cardsShow2(Card::cardsDec($_prev['hand'])),$_prev['hand']);
	echo "下家: ";
	Card::cardsEcho(Card::cardsShow2(Card::cardsDec($_next['hand'])),$_next['hand']);
	echo "轮到: ";
	Card::cardsEcho(Card::cardsShow2(Card::cardsDec($_mine['hand'])),$_mine['hand']);

	if ( $joker && Card::hasJoker(Card::cardsToOld($_mine['hand']), $joker) ) {
		$_outs = Card::jokerLogic($_table, $_prev, $_next, $_mine);
		$jokto = $_outs ? $_outs['jokto'] : array();
		$_outs = $_outs ? $_outs['plays'] : array();
	} else {
		$jokto = array();
		$_outs = Card::playLogic($_table, $_prev, $_next, $_mine);
	}
	$_outs = $_outs ? $_outs : array();
	$cardsPlay = array();
	if ( $_outs ) {
		//解析牌组
		$cardsRes = Card::cardsParse(Card::cardsToOld($_outs), $jokto, $joker);
		$cardsType = intval($cardsRes['t']);//牌型编号
		$cardsLen = $cardsRes['l'];//牌组长度
		$cardsValue = $cardsRes['v'];//牌组取值
		$cardsPlay = $cardsRes['plays'];//扮演牌组
		$cardsReal = $cardsRes['reals'];//实际牌组
		$jokto = $cardsRes['jokto'];//赖子角色
	}
	if ( $isTask && $cardsPlay )
	{
		//牌局任务检查 start
		$_seatId = $pos;//打牌人位置
		if ( in_array($cardsType, array(87, 88, 89, 99)) )
		{
		$rate = $table['rate'] = $table['rate'] * 2;
		foreach ( $table['seats'] as $seatId )
		{
			$task = $table["seat{$seatId}task"];
			if ( ! $task || (isset($task['is_done']) && $task['is_done']) ) continue;
			$count = $is_new = 0;
			foreach ( $task['conds'] as $k => $cond )
			{
				if ( isset($cond['is_done']) && $cond['is_done'] ) {
					$count++;
				} elseif ( (!isset($cond['is_done']) || ! $cond['is_done']) && $cond['id'] == 28 && $cond['value'] <= $rate ) {
					$task['conds'][$k]['is_done'] = $is_new = 1;
					$count++;
				}
			}
			if ( $count == count($task['conds']) ) $task['is_done'] = $is_new = 1;
			if ( ! $is_new ) continue;
			$table["seat{$seatId}task"] = $task;
			echo "位置[$seatId]牌局任务[".($task['typeid']?"误导":"可行")."]模式 任务更新 [".$task['name']."] ".json_encode($task);
			echo "<br/>";
			if ( !(isset($task['is_done']) && $task['is_done']) ) continue;
			$table["seat{$seatId}task"] = array();
			echo "位置[$seatId]牌局任务[".($task['typeid']?"误导":"可行")."]模式 任务完成 [".$task['name']."]";
			echo "<br/>";
			if ( $task['typeid'] ) {
				echo "<script>alert('你太牛逼了，完成了一个不可能任务')</script>";
			}
		}
		}
		foreach ( $table['seats'] as $seatId )
		{
			if ( intval($seatId) != intval($_seatId) ) continue;
			$task = $table["seat{$seatId}task"];
			if ( ! $task || (isset($task['is_done']) && $task['is_done']) ) continue;
			$task = Card::checkTaskDone($cardsPlay, $task, 1);
			if ( ! $task['is_new'] ) continue;
			$table["seat{$seatId}task"] = $task;
			echo "位置[$seatId]牌局任务[".($task['typeid']?"误导":"可行")."]模式 任务更新 [".$task['name']."] ".json_encode($task);
			echo "<br/>";
			if ( !(isset($task['is_done']) && $task['is_done']) ) continue;
			$table["seat{$seatId}task"] = array();
			echo "位置[$seatId]牌局任务[".($task['typeid']?"误导":"可行")."]模式 任务完成 [".$task['name']."]";
			echo "<br/>";
			if ( $task['typeid'] ) {
				echo "<script>alert('你太牛逼了，完成了一个不可能任务')</script>";
			}
		}
		//牌局任务检查 end
	}

	$res = Card::cardsShow($_outs);
	echo '【'.($i>9?$i:('0'.$i)).'】把, 【'.$pos.'】席位, 轮到【'.$poss[$pos].'】 【'.($_table['card']?($_outs?'管住':'不要'):'叫牌').'】: ';
	Card::cardsEcho($res);
	echo "</div>";

	$outs = $_outs;
	$mine_hand = $_mine['hand'];
	foreach ( $mine_hand as $k => $v )
	{
		foreach ( $outs as $kk => $vv )
		{
			if ( $v === $vv )
			{
				unset($outs[$kk]);
				unset($mine_hand[$k]);
				break;
			}
		}
	}
	if ( $_outs ) {
		$pass = 0;
	} else {
		$pass++;
	}
	$pass = $pass == 2 ? 0 : $pass;

	$_mine['hand'] = $mine_hand;
	$_table['outs'] = $_outs ? array_merge($_table['outs'], $_outs) : $_table['outs'];
	$_table['call'] = $_outs ? $_mine['posi'] : $_table['call'];
	$_table['card'] = $_outs ? Card::cardsToNew($cardsPlay) : ($pass ? $_table['card'] : array());
	$_table['type'] = $_outs ? $cardsType : ($pass ? $_table['type'] : array());
	$_table['jokto'] = $_outs ? $jokto : ($pass ? $_table['jokto'] : array());
	$_tmp = $_prev;
	$_prev = $_mine;
	$_mine = $_next;
	$_next = $_tmp;

	$pos--;
	$pos = $pos < 0 ? 2 : $pos;

	if ( $_prev['hand'] && $_next['hand'] && $_mine['hand'] ) continue;
	$playing = false;
}

if ( $isTask )
{
	//牌局任务检查 start
	$lord = 0;
	$winn = $_table['call'];
	$isBoorWin = intval($winn != $lord);
	$seat_winer = array(intval(! $isBoorWin), $isBoorWin, $isBoorWin);
	$isBoorspring = $isLordspring = 0;
	if ( $isBoorWin ) $isBoorspring = intval(mt_rand(1,100) > 80);
	if ( ! $isBoorWin ) $isLordspring = intval(mt_rand(1,100) > 80);
	if ( $isBoorspring ) {
		echo "位置[$winn]牌局任务模拟春天 农民反春";
		echo "<br/>";
	}
	if ( $isLordspring ) {
		echo "位置[$winn]牌局任务模拟春天 地主春天";
		echo "<br/>";
	}
	foreach ( $table['seats'] as $seatId )
	{
		$task = $table["seat{$seatId}task"];
		if ( ! $task || (isset($task['is_done']) && $task['is_done']) ) continue;
		$count = $is_new = 0;
		foreach ( $task['conds'] as $k => $cond )
		{
			if ( isset($cond['is_done']) && $cond['is_done'] ) {
				$count++;
			} elseif ( (!isset($cond['is_done']) || ! $cond['is_done']) && $cond['id'] == 29 && $seat_winer[$seatId] ) {
				$task['conds'][$k]['is_done'] = $is_new = 1;
				$count++;
			} elseif ( (!isset($cond['is_done']) || ! $cond['is_done']) && $cond['id'] == 30 && $lord == $seatId && $isLordspring ) {
				$task['conds'][$k]['is_done'] = $is_new = 1;
				$count++;
			} elseif ( (!isset($cond['is_done']) || ! $cond['is_done']) && $cond['id'] == 31 && $lord != $seatId && $isBoorspring ) {
				$task['conds'][$k]['is_done'] = $is_new = 1;
				$count++;
			}
		}
		if ( $count == count($task['conds']) ) $task['is_done'] = $is_new = 1;
		if ( ! $is_new ) continue;
		$table["seat{$seatId}task"] = $task;
		echo "位置[$seatId]牌局任务[".($task['typeid']?"误导":"可行")."]模式 任务更新 [".$task['name']."] ".json_encode($task);
		echo "<br/>";
		if ( !(isset($task['is_done']) && $task['is_done']) ) continue;
		$table["seat{$seatId}task"] = array();
		echo "位置[$seatId]牌局任务[".($task['typeid']?"误导":"可行")."]模式 任务完成 [".$task['name']."]";
		echo "<br/>";
		if ( $task['typeid'] ) {
			echo "<script>alert('你太牛逼了，完成了一个不可能任务')</script>";
		}
	}
	//牌局任务检查 end
}

echo '</body></html>';
}
//end WEB TEST
