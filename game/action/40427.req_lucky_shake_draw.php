<?php

$cmd = 4;
$code = 428; //回馈摇摇乐结果
$errno = 0;
$last_times = 0;//当日剩余次数
$cards = array(); //摇摇乐获得的牌
$goods = array(); //未达到抽奖门槛弹出的商品
$win_coins = 0;   //一次抽奖获得的乐豆
$win_type = 0; //0:乐豆，1:乐券
$type = 0;        //摇摇乐牌局类型 1 单张 2对子 3 顺子 4 同花 5 同花顺 6 豹子 7 豹子A
$errcode= array(
    0 => '操作成功',
    1 => '房间无效',
    2 => "抱歉，您的抽奖次数已使用完毕，\n请明天再来吧！",
    3 => '很抱歉，抽奖系统错误，请稍候重试。\n您也可以联系官方QQ群：11032773。',
    4 => '乐豆不够',
    5 => '未达到抽奖门槛'
);

include(ROOT.'/include/data_lucky_shake_conf.php');
include_once(ROOT.'/include/data_lucky_shake_control.php');
$ud = $user['uid'];
$rd = $user['lastRoomId'] ? $user['lastRoomId'] : $user['roomId'];
$td = $user['tableId'];
$dd = intval(date("Ymd"));
$data_conf = isset($data_lucky_shake_conf[$rd]) ? $data_lucky_shake_conf[$rd] : null;
$times = $this->model->getLuckyShake($ud,$dd,$rd);
$left_times = $data_conf['day_times'] - $times;
if ( ! $rd || ! $data_conf ) {
    $errno = 1;
}
elseif($user['coins'] < $data_conf['threshold']){
	//针对IOS评审的强制更改
	if ( $user['channel'] == 'sjiosappstore' ) {
		$data_conf['goods_id'] = 6;
		$data_conf['goods_name'] = '30元礼包';
		$data_conf['price'] = 30;
	}
     $errno = 5;
     $errcode[$errno] = '您的乐豆不足'.$data_conf['threshold']."无法参与抽奖，\n现在充值".$data_conf['price'].'元可立获'.($data_conf['price']*10000).'乐豆哦！';
     $goods['id'] = $data_conf['goods_id'];
     $goods['name'] = $data_conf['goods_name'];
     $goods['price'] = $data_conf['price'];
     //弹出礼包
}
elseif($times < 0 || $left_times <= 0){
    $errno = 2;
}
elseif($user['coins'] < $data_conf['one_cost']){
    $errno = 4;
}
else{
    /* step1. 扣乐豆
     * step2. 执行摇摇乐
     * step3. 增加乐豆
     */
    $addU['coins'] = $data_conf['one_cost'] * (-1);
    $res = $this->model->incUserInfo($ud, $addU);
    if($res){
		$user['coins'] += $addU['coins'];//临时改写 待续优化
		$this->model->record->money('赌二投币', 'coins', $data_conf['one_cost'], $ud, $user);

        //随机一个数值，得到类型
        $playCard = new PlayCards();
        $type = $playCard->prob($data_lucky_shake_rates);
        $cards = $playCard->getCards($type);
  //      $type = $playCard->getType();
        $coins = 0;
        $odds = 0 ;

        $odds = $playCard->getOdds($type);
        if(!$odds){
            $win_coins = $coins = $data_conf['other_win'];
            $win_type = $data_conf['other_win_type'];
            if($win_type == 1){
                $addU['coupon'] = $win_coins;
				$user['coupon'] += $addU['coupon'];
                $addU['coins'] = 0;
            }elseif($win_type == 0){
                $addU['coins'] = $win_coins;
				$user['coins'] += $addU['coins'];
            }
        }else{
            $win_coins = $coins = $data_conf['one_cost'] * $odds;
            $addU['coins'] = $coins;
			$user['coins'] += $addU['coins'];
        }
		if ( $addU['coins'] ) {
			$this->model->record->money('赌二中奖', 'coins', $addU['coins'], $ud, $user);
		} else {
			$this->model->record->money('赌二中奖', 'coupon', $addU['coupon'], $ud, $user);
		}

        if(!$this->model->incUserInfo($ud, $addU))
        {
            $errno = 3;
        }

        $res = $this->model->doLuckyShake($ud,$dd,$rd,$data_conf['one_cost'],$coins,$type,++$times);
        $left_times--;
        debug("用户摇摇乐成功[$fd|$ud|$rd|$type|$coins|$left_times");
		if ( $type > 5 ) {
			sendHorn("幸运爽翻天，恭喜·".$user['nick']."·在摇摇乐内摇到豹子，获得{$coins}乐豆，这次赚大啦！", 1);
		}

    }
    if(!$res){
        $errno = 3;
    }
}

$data = array(
	'errno' => $errno,
	'error' => $errcode[$errno],
    'left_times' => $left_times, //今日剩余次数
    'coins' => $user['coins'],//用户剩余乐币
    'win_coins' => $win_coins,
    'cards' => $cards,
    'type'  => $type,
    'goods' => $goods,
    'win_type' =>$win_type,
    'coupon' =>$user['coupon'],//用户剩余乐
);
$res = sendToFd($fd, $cmd, $code, $data);


end:{
	$this->model->record->action($accode, $rd, $td, $ud, $user);
}
