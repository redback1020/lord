<?php

use Zend\Code\Scanner\Util;

function my_swap(&$a, &$b)
{
    $c = $b;
    $a = $b;
    $b = $c;
}


class CowCard
{
    protected $str = '';

    public function __construct($str = '')
    {
        $this->str = $str;
    }

    public function getValue()
    {
        return substr($this->str, 1, 1);
    }

    public function getColor()
    {
        return substr($this->str, 0, 1);
    }

    public function getValueToInt()
    {
        return hexdec($this->getValue());
    }

    public function getColorToInt()
    {
        return hexdec($this->getColor());
    }

    public function getWeight()
    {
        if ($this->str == '00' || $this->str == '01') {
            return 10;
        }

        $value = $this->getValue();
        $value = hexdec($value);
        if ($value >= 10) {
            return 10;
        } else {
            return hexdec($value);
        }
    }

    function __toString()
    {
        return $this->str;
    }


    public function toText()
    {
        switch ($this->getColor()) {
            case 0:
                $color = '小丑';
                break;
            case 1:
                $color = '方块';
                break;
            case 2:
                $color = '梅花';
                break;
            case 3:
                $color = '红桃';
                break;
            case 4:
                $color = '黑桃';
                break;
            default:
                throw new \Exception("错误的花色");
                break;
        }

        switch ($this->getValue()) {
            case '1':
                $value = 'A';
                break;
            case '2':
            case '3':
            case '4':
            case '5':
            case '6':
            case '7':
            case '8':
            case '9':
                $value = $this->getValue();
                break;
            case 'a':
                $value = '10';
                break;
            case 'b':
                $value = 'J';
                break;
            case 'c':
                $value = 'Q';
                break;
            case 'd':
                $value = 'K';
                break;
            default:
                if ($color == '小丑') {
                    $value = $this->str == '00' ? '小王' : '大王';
                } else {
                    throw new \Exception('错误的牌值!');
                }

                break;
        }
        return $color . $value;
    }

    public function toInt()
    {
        if ($this->str == '00') {
            return 254;
        }

        if ($this->str == '01') {
            return 255;
        }

        return intval($this->getValueToInt().$this->getColorToInt());
    }

    static function handfulToText(array $cards)
    {
        $rst = [];
        foreach ($cards as $card) {
            $card = new CowCard($card);
            $rst[] = $card->toText();
        }
        return $rst;
    }
}


abstract class BaseCowMachine
{

    //region config

    static $config_candidate_limit = 20;//候选人最大人数
    static $config_candidate_options = [5000000, 10000000, 20000000, 50000000];//候选人上分选项
    static $config_banker_round_limit = 10;//最多坐庄次数
    static $rate_configs = [12 => 5, 11 => 4, 10 => 3, 9 => 2, 8 => 2, 7 => 2, 6 => 1, 5 => 1, 4 => 1, 3 => 1, 2 => 1, 1 => 1, 0 => 1];//每一种牌型的翻倍值
    static $system_rake = 0.05;//系统抽成
    static $pot_rake = 0.01;//奖池抽成
    static $send_card_cd = 15;//发牌等待
    static $count_card_cd = 18;//结算等待
    static $show_count_cd = 12;//新牌局等待

    //花色 0=王 1=方块 2=梅花 3=红桃 4=黑桃    大王>小王>黑桃>红桃>梅花>方块
    //值  1-9=正常 a=10 b=J c=Q d=K
    //小王=00 大王=01
    static $str_cards = [
        '00', '01',
        '11', '12', '13', '14', '15', '16', '17', '18', '19', '1a', '1b', '1c', '1d',
        '21', '22', '23', '24', '25', '26', '27', '28', '29', '2a', '2b', '2c', '2d',
        '31', '32', '33', '34', '35', '36', '37', '38', '39', '3a', '3b', '3c', '3d',
        '41', '42', '43', '44', '45', '46', '47', '48', '49', '4a', '4b', '4c', '4d'
    ];

    static $static_log_redis_key = [
        "open_player"=>"cow:open:player",                       //打开人数 sadd
        "open_num"=>"cow:open:num",                             //打开次数 incr
        "game_player"=>"cow:game:player",                       //游戏人数 sadd
        "valid_round"=>"cow:valid:round",                       //有效局数 incr
        "total_fee"=>"cow:total:fee",                           //总服务费 incr
        "last_day_pot"=>"cow:last:day:pot",                     //上一天奖池 set
        "wager_pos1_player"=>"cow:wager:pos1:player",           //押天人数 sadd
        "wager_pos2_player"=>"cow:wager:pos2:player",           //押地人数 sadd
        "wager_pos3_player"=>"cow:wager:pos3:player",           //押玄人数 sadd
        "wager_pos4_player"=>"cow:wager:pos4:player",           //押黄人数 sadd
        "wager_pos1_num"=>"cow:wager:pos1:num",           		//押天次数 incr
        "wager_pos2_num"=>"cow:wager:pos2:num",           		//押地次数 incr
        "wager_pos3_num"=>"cow:wager:pos3:num",           		//押玄次数 incr
        "wager_pos4_num"=>"cow:wager:pos4:num",           		//押黄次数 incr
        "wager_all_pos1_num"=>"cow:wager:all:pos1:num",   		//全押天次数 incr
        "wager_all_pos2_num"=>"cow:wager:all:pos2:num",   		//全押地次数 incr
        "wager_all_pos3_num"=>"cow:wager:all:pos3:num",   		//全押玄次数 incr
        "wager_all_pos4_num"=>"cow:wager:all:pos4:num",   		//全押黄次数 incr
        "apply_banker_player"=>"cow:apply:banker:player",       //申请庄家人数 sadd
        "apply_banker_num"=>"cow:apply:num",                    //申请庄家次数 incr
        "banker_player"=>"cow:banker:player",       			//庄家人数 sadd
        "protect_banker_num"=>"cow:protect:banker:num",			//保护庄家次数 incr
        "flower_feedback_num"=>"cow:flower:feedback:num",       //五花牛中奖次数 incr
        "flower_feedback_fee"=>"cow:flower:feedback:fee",       //五花牛中奖返奖费 incr
        "bobm_feedback_num"=>"cow:bobm:feedback:num",           //炸弹牛中奖次数 incr
        "bobm_feedback_fee"=>"cow:bobm:feedback:fee",           //炸弹牛中奖返奖费 incr
        "online_player"=>"cow:online:player",                   //在线人数 sadd
        "robot_banker_profit"=>"cow:robot:banker:profit",       //系统庄，净收入（只记录真实玩家的下分情况）
        "robot_player_profit"=>"cow:robot:player:profit",       //系统闲，净收入（只记录真实玩家坐庄情况）
    ];
    
    //endregion

    //region tools

    public function sendToFd($fd, $action, $data)
    {

    }

    public function printf()
    {
        $args = func_get_args();
        $args[0] = $args[0] . "\n";
        echo call_user_func_array('sprintf', $args);
    }

    public function broadcast($action, $data, $exception = [])
    {
        $fds = $this->getAllFd();
        foreach ($fds as $player_id => $fd) {
            if (in_array($player_id, $exception) || $fd <= 0) {
                continue;
            }
            $this->sendToFd($fd, $action, $data);
        }
    }

    public function notice($msg)
    {
        $this->printf('发送跑马灯:' . $msg);
        $this->broadcast('notice', [
            "state"  => 0,
            "result" => [
                "msg" => $msg
            ]
        ]);
    }

    public function log($table, $fields = [])
    {
        
    }

    //endregion

    //region logic
    /**
     * 洗牌
     * @return array
     */
    protected function shuffle()
    {
        $cards = self::$str_cards;
        
        shuffle($cards);
        $selectded = [
            [$cards[0], $cards[1], $cards[2], $cards[3], $cards[4]],
            [$cards[5], $cards[6], $cards[7], $cards[8], $cards[9]],
            [$cards[10], $cards[11], $cards[12], $cards[13], $cards[14]],
            [$cards[15], $cards[16], $cards[17], $cards[18], $cards[19]],
            [$cards[20], $cards[21], $cards[22], $cards[23], $cards[24]]];
        return $selectded;
    }


    /**
     * 发牌
     */
    public function sendCard()
    {
        $this->round++;

        $handful_cards = $this->shuffle();
        $format_handful_cards = [];
        foreach ($handful_cards as $key => $handful_card) {
            $type = $this->getCardType($handful_card);
            $max = $this->getMax($handful_card, $type);
            $format_handful_cards[] = [
                'cards' => $handful_card,
                'type'  => $type,
                'max'   => strval($max),
                'max_i' => $max->toInt(),
            ];
        }
        unset($handful_cards, $handful_card, $key, $max, $type);
        //$format_handful_cards = $this->intervene(2, $format_handful_cards);
        foreach ($format_handful_cards as $position => $handful_card) {
            if ($position > 0) {
                if ($format_handful_cards[$position]['type'] == $format_handful_cards[0]['type']) {
                    $win = $format_handful_cards[$position]['max_i'] > $format_handful_cards[0]['max_i'];
                } else {
                    $win = $format_handful_cards[$position]['type'] > $format_handful_cards[0]['type'];
                }
                $format_handful_cards[$position]['win'] = $win;
                $format_handful_cards[$position]['rate'] = $win ? self::$rate_configs[$format_handful_cards[$position]['type']] : self::$rate_configs[$format_handful_cards[0]['type']];
            }
            $this->printf("位置 %s 发牌 %s 牛%d 最大%d", $position, implode(' ', CowCard::handfulToText($format_handful_cards[$position]['cards'])), $format_handful_cards[$position]['type'],
                $format_handful_cards[$position]['max_i']
            );
        }
        
        //手动干预
        $intervene = $this->getIntervene();
        //自动干预
        $rounds = $this->incrRound(1);
        $banker_id = $this->getBankerId();
        $win = true;
        if(!in_array($intervene, [0, 1, 2, 3, 4, ])){
            if(mt_rand(1,100)<6){
                if($banker_id < 1000){
                    $intervene = 0;
                    //统计使用
                    $this->staticLog("incr", self::$static_log_redis_key["protect_banker_num"],0,1);
                }else{
                    $intervene = 0;
                    $win = false;
                }
            }
        }
        /*
        if($rounds%10 == 0 && !in_array($intervene, [0, 1, 2, 3, 4, ]))
        {
            if(round(1,100)<2)$intervene = 0;
            //统计使用
            $this->staticLog("incr", self::$static_log_redis_key["protect_banker_num"],0,1);
        }
        */
        if (in_array($intervene, [0, 1, 2, 3, 4])) {
            $format_handful_cards = $this->intervene($intervene, $format_handful_cards, $win);
            foreach ($format_handful_cards as $position => $handful_card) {
                $this->printf("干预后位置 %s 发牌 %s 牛%d 最大%d", $position, implode(' ', CowCard::handfulToText($format_handful_cards[$position]['cards'])), $format_handful_cards[$position]['type'],
                    $format_handful_cards[$position]['max_i']
                );
            }
            $format_handful_cards["intervene"] = $intervene;
        }

        $positions = $this->getPositions();
        $this->broadcast('deposits', [
            'state'  => 0,
            'result' => [
                'deposits' => [
                    1 => array_sum($positions[1]),
                    2 => array_sum($positions[2]),
                    3 => array_sum($positions[3]),
                    4 => array_sum($positions[4])
                ]
            ]
        ]);
        
        $this->setHandfulCards($format_handful_cards);
        unset($format_handful_cards["intervene"]);
        $this->broadcast('sendCard', [
            "state"  => 0,
            'result' => [
                "handful_cards" => $format_handful_cards
            ]
        ]);
 
        return $format_handful_cards;
    }

    /**
     * 干预
     * 把最好的牌给 $position
     * @param $position
     * @param $handful_cards
     * @return int
     */
    public function intervene($position, $format_handful_cards, $win = true)
    {
        //$handful_cards = $this->getHandfulCards();
        if (count($format_handful_cards) != 5) {
            return [];
        }
        if($win){
            $max = 0;
            for ($i = 0; $i < 5; $i++) {
                if ($format_handful_cards[$i]['type'] == $format_handful_cards[$max]['type']) {
                    if ($format_handful_cards[$i]['max_i'] > $format_handful_cards[$max]['max_i']) {
                        $max = $i;
                    }
                } elseif ($format_handful_cards[$i]['type'] > $format_handful_cards[$max]['type']) {
                    $max = $i;
                }
            }
            if ($max != $position) {
                $c = $format_handful_cards[$position];
                $format_handful_cards[$position] = $format_handful_cards[$max];
                $format_handful_cards[$max] = $c;
                $this->printf("干预:  %d(牛%d) => %d(牛%d) ", $position, $format_handful_cards[$position]['type'], $max, $format_handful_cards[$max]['type']);
            }
        }else{
            $min = 0;
            for ($i = 0; $i < 5; $i++) {
                if ($format_handful_cards[$i]['type'] == $format_handful_cards[$min]['type']) {
                    if ($format_handful_cards[$i]['max_i'] < $format_handful_cards[$min]['max_i']) {
                        $min = $i;
                    }
                } elseif ($format_handful_cards[$i]['type'] < $format_handful_cards[$min]['type']) {
                    $min = $i;
                }
            }
            if ($min != $position) {
                $c = $format_handful_cards[$position];
                $format_handful_cards[$position] = $format_handful_cards[$min];
                $format_handful_cards[$min] = $c;
                $this->printf("干预:  %d(牛%d) => %d(牛%d) ", $position, $format_handful_cards[$position]['type'], $max, $format_handful_cards[$max]['type']);
            }
        }
        foreach ($format_handful_cards as $position => $handful_card) {
            if ($position > 0) {
                if ($format_handful_cards[$position]['type'] == $format_handful_cards[0]['type']) {
                    $win = $format_handful_cards[$position]['max_i'] > $format_handful_cards[0]['max_i'];
                } else {
                    $win = $format_handful_cards[$position]['type'] > $format_handful_cards[0]['type'];
                }
                $format_handful_cards[$position]['win'] = $win;
                $format_handful_cards[$position]['rate'] = $win ? self::$rate_configs[$format_handful_cards[$position]['type']] : self::$rate_configs[$format_handful_cards[0]['type']];
            }
        }

        return $format_handful_cards;

    }

    /**
     * 计算牌局
     * @param $handful_cards
     */
    public function cardCounting($handful_cards)
    {
        $this->printf("结算开始");


        $this->addPositionHistory([
            'time' => date('Y-m-d H:i:s'),
            '1'    => $handful_cards[1]['win'],
            '2'    => $handful_cards[2]['win'],
            '3'    => $handful_cards[3]['win'],
            '4'    => $handful_cards[4]['win']
        ]);


        $this->setState('counting');
        $candidates = $this->getBankerCandidates();
        $gamer = $this->getGamer();
        
        $banker_profit_before_cut = 0;
        $banker_profit = 0;
        $tax = 0;
        $banker_system_cut = 0;
        $banker_gift = 0;
        $players_pot_gift = 0;
        $pot_gfit = 0;
        $winPotPostion = -1;
        $all_player_profit = 0;
        $pot_gift = 0;
        $banker_pot_cut = 0;
        $player_results = [];
        $subtotal = []; //记录庄家各个闲家总收益
        $players_profit = [];//记录闲家收益
        $players_pot_cut = [];//记录闲家奖池抽成
        $players_system_cut = [];//记录闲家系统抽成
        
        $players = $this->getAllPlayer();
        $positions = $this->extractPositions();
        $banker_id = $this->getBankerId();
        $banker_fd = $this->getFdByPlayerId($banker_id);
        $rounds = $this->getRound();
        
        if ($banker_id != 0) {
            $banker_round = $this->incrBankerRound();
        } else {
            $banker_round = 0;
        }
        
        //统计使用(个人结算数据)
        $players_static = [];
        //服务费
        $fee = 0;
        //奖池增加
        $pot_add = 0;
        
        //先算牌面
        foreach ($players as $player_id => $player_info) {
            if ($player_id == $this->getBankerId()) {
                continue;
            }
            
            $this->printf("计算闲家 %s 的得分", $player_id);
            $my_profit = 0;//闲家的盈亏
            $my_all_bet = 0;
            $player_system_cut = $player_pot_cut = 0;
            
            for ($position = 1; $position <= 4; $position++) {
                $system_cut = $pot_cut = $profit=0;
                isset($subtotal[$position])?:$subtotal[$position]=0;
                $wager = @intval($positions[$position][$player_id]);
                if($wager<=0) continue;
                //统计使用(个人结算数据)
                if(!isset($players_static[$player_id]))
                $players_static[$player_id] = ["player_id"=>$player_id,"round"=>$rounds,"bank_id"=>$banker_id,"wager1"=>0,"wager2"=>0,"wager3"=>0,"wager4"=>0,"profit1"=>0,"profit2"=>0,"profit3"=>0,"profit4"=>0]; 
                $my_all_bet += $wager;
                $win = $handful_cards[$position]['win'];
                $rate = $handful_cards[$position]['rate'];
                $my_position_profit = $win?intval($wager * $rate):intval($wager * $rate)*-1;
                
                $system_cut = intval(abs($my_position_profit) * self::$system_rake);//系统抽成
                $pot_cut = intval(abs($my_position_profit) * self::$pot_rake); //奖池抽成
                //$this->printf("%s 闲家系统抽成=%d 闲家奖池抽成=%d", $player_id, $system_cut, $pot_cut);
                $this->incrThePot($pot_cut);
                
                $tax += ($system_cut + $pot_cut);
                if($win)
                {
                    $banker_profit-=$my_position_profit;
                    $my_position_profit -= ($system_cut + $pot_cut);
                    $my_profit += $my_position_profit + $wager;
                    $player_system_cut += $system_cut;
                    $player_pot_cut += $pot_cut; 
                }
                else
                {
                    $my_profit += $my_position_profit;
                    $my_position_profit += ($system_cut + $pot_cut);
                    $banker_profit-=$my_position_profit;
                    $banker_system_cut += $system_cut;
                    $banker_pot_cut += $pot_cut;
                }
                $subtotal[$position] += $my_position_profit;
                $this->printf("在'%d'位下注了%s 翻%d倍", $position, $win ? "赢了" : '输了', $rate);
                //统计使用
                $players_static[$player_id]["wager{$position}"] = $wager;
                $players_static[$player_id]["profit{$position}"] = $my_position_profit>0?$my_position_profit+$wager:0;
                $fee += $system_cut;
                $pot_add += $pot_cut;
            }
            $this->printf("%s 闲家在四个位置上的总盈亏:%d fd=%s", $player_id, $my_profit, $player_info['fd']);
        
            $all_player_profit += $my_profit;
            $players_profit[$player_id] = $my_profit;
            $players_system_cut[$player_id] = $player_system_cut;
            $players_pot_cut[$player_id] = $player_pot_cut;
            
            if($player_id < 10000 && $banker_id > 10000){
                $this->staticLog("incr", self::$static_log_redis_key["robot_player_profit"],0,$my_profit);
            }
            if($player_id < 10000 || !$my_all_bet)continue;
            
            $user = $gamer->model->getUserInfo($player_id);
            $user["roomId"] = $rounds;
            $gamer->model->record->money('牛牛下注', 'coins', -$my_all_bet, $player_id, $user);
            $user["coins"] = $this->addBetByPlayerId($player_id, $my_profit);
            $gamer->model->record->money('牛牛得分', 'coins', $my_profit, $player_id, $user);
            $gamer->model->record->money('牛牛抽水', 'coins', -$player_system_cut, $player_id, $user);
        }
        if($banker_id > 1000){
            $user = $gamer->model->getUserInfo($banker_id);
            $banker_bet = $this->getBankerBet();
            $user["coins"] = $banker_bet;
            $user["roomId"] = $rounds;
            $gamer->model->record->money('牛牛得分', 'coins', $banker_profit, $banker_id, $user);
            $gamer->model->record->money('牛牛抽水', 'coins', $banker_system_cut, $banker_id, $user);
        }
        //统计使用
         $this->staticLog("incr", self::$static_log_redis_key["total_fee"],0, $fee);
         
         //计算奖池
         $pot = $this->getThePot();
         for($position = 0; $position <= 4; $position++)
         {
             if(in_array($handful_cards[$position]['type'], [11, 12]))
             {
                 $this->printf("计算奖池");
                 $this->printf("p1:".array_sum($positions[1])." p2:".array_sum($positions[2])." p3:".array_sum($positions[3])." p4:".array_sum($positions[4]));
                if((array_sum($positions[1]) || array_sum($positions[2]) || array_sum($positions[3]) || array_sum($positions[4])) && 
                    ( $winPotPostion <0 || $handful_cards[$position]['type'] > $handful_cards[$winPotPostion]['type'] || 
                        ($handful_cards[$position]['type'] == $handful_cards[$winPotPostion]['type'] && $handful_cards[$position]["max_i"] > $handful_cards[$winPotPostion]["max_i"])))
                {
                   $winPotPostion = $position;   
                 }
             }
         }
         if($winPotPostion > -1)
         {
             $rate = $handful_cards[$winPotPostion]['type'] == 12 ? 0.2 : 0.1;
             $pot_gift = intval($pot * $rate );
             $this->incrThePot($pot_gift * -1);
             //统计使用(五花牛炸弹牛)
             $key = $handful_cards[$winPotPostion]['type'] == 12?self::$static_log_redis_key["flower_feedback_num"]:self::$static_log_redis_key["bobm_feedback_num"];
             $this->staticLog("incr", $key,0, 1);
             $key = $handful_cards[$winPotPostion]['type'] == 12?self::$static_log_redis_key["flower_feedback_fee"]:self::$static_log_redis_key["bobm_feedback_fee"];
             $this->staticLog("incr", $key, 0, $pot_gfit);
         }
         //统计使用(有效局)
         if($banker_profit)$this->staticLog("incr", self::$static_log_redis_key["valid_round"], 0, 1);
         foreach ($players as $player_id => $player_info) {
            //统计使用(有效玩家)
            if($banker_profit)$this->staticLog("sAdd", self::$static_log_redis_key["game_player"], $player_id);
            
            $splice_gift = 0;
            if ($player_id == $this->getBankerId())continue;
            if($winPotPostion>0 && isset($positions[$winPotPostion][$player_id]))
            {
                $percent = $positions[$winPotPostion][$player_id] / array_sum($positions[$winPotPostion]);
                $add = intval($pot_gift *  $percent);
                $splice_gift += $add;
                $subtotal[$winPotPostion] += $add;
                $players_static[$player_id]["profit{$winPotPostion}"] += $add;
                $user = $gamer->model->getUserInfo($player_id);
                $user["roomId"] = $rounds;
                $gamer->model->record->money('牛牛奖池', 'coins', $add, $player_id, $user);
            }
            if ($player_info['fd'] == 0) {
                $this->removePlayer($player_id);
            } elseif ($player_info['fd'] == -1 && $my_all_bet != 0) {
                $player_settlement = [
                    'role'       => 'player',
                    'win'        => $players_profit[$player_id] > 0,
                    'system_cut' => $players_system_cut[$player_id],
                    'pot_cut'    => $players_pot_cut[$player_id],
                    'profit'     => $players_profit[$player_id],
                    'pot_gift'   => $splice_gift,
                    'bet'        => $this->getBetByPlayerId($player_id)
                ];
                $this->printf("$player_id 闲家掉线结算:" . json_encode($player_settlement));
                $this->addPlayerOfflineSettlement($player_id, $player_settlement);
                $this->removePlayer($player_id);
            } else {
                $player_results[$player_info['fd']] = [
                    'win'              => $players_profit[$player_id] > 0,
                    'system_cut'       => $players_system_cut[$player_id],
                    'pot_cut'          => $players_pot_cut[$player_id],
                    'profit'           => $players_profit[$player_id],
                    'pot_gift'         => $splice_gift,
                    'bet'              => $this->getBetByPlayerId($player_id),
                    'banker_round'     => $banker_round,
                    'is_in_candidates' => $this->isInCandidates($player_id, $candidates)
                ];
            }
            if($splice_gift)
            {
                $this->addBetByPlayerId($player_id, $splice_gift);
            }
            
            //统计使用(闲家结算日志)
            $this->log("log_cow_player_settlement",$players_static[$player_id]);
        }
        
        if($winPotPostion == 0)
        {
            $banker_gift = $pot_gift;
            if($banker_id > 1000){
                $user = $gamer->model->getUserInfo($banker_id);
                $banker_bet = $this->getBankerBet();
                $user["coins"] = $banker_bet;
                $user["roomId"] = $rounds;
                $gamer->model->record->money('牛牛奖池', 'coins', $pot_gift, $banker_id, $user);
            }
        }
        $banker_profit +=  $banker_gift;
        $subtotal[0] = $banker_profit;
        
        $banker_bet = $this->incrBankerBet($banker_profit);
        if(count($players) > 3){
            $this->staticLog("incr", self::$static_log_redis_key["robot_banker_profit"],0,$banker_profit);
        }
        //$banker_bet = $this->getBankerBet();
        $pot_remain = $this->getThePot();

        $this->printf("$banker_id 庄家总盈亏=%d  现值=%d", $banker_profit, $banker_bet);
        if ($banker_bet < 0) {
            throw new Exception("庄家金额异常!!");
        }

        $banker_settlement = [
            'role'         => 'banker',
            'win'          => $banker_profit > 0, //胜负
            'system_cut'   => $banker_system_cut,
            'pot_cut'      => $banker_pot_cut,
            'profit'       => $banker_profit,
            'pot_gift'     => $banker_gift,
            'bet'          => $this->getBetByPlayerId($banker_id),
            'banker_bet'   => $banker_bet,
            'banker_round' => $banker_round,
            'pot_remain'   => $pot_remain,
            'subtotal'     => $subtotal
        ];

        if ($banker_fd == 0) {
        } else if ($banker_fd == -1) {
            //$this->printf("$banker_id 庄家掉线日志:" . json_encode($banker_settlement));
            // $this->addPlayerOfflineSettlement($banker_id, $banker_settlement);
        } else {
            $this->sendToFd($banker_fd, 'settlement', [
                'state'  => 0,
                'result' => $banker_settlement
            ]); 
        }

        foreach ($player_results as $fd => $player_result) {
            $player_result['banker_bet'] = $banker_bet;
            $player_result['pot_remain'] = $pot_remain;
            $player_result["subtotal"] = $subtotal;
            $this->sendToFd($fd, 'settlement', [
                'state'  => 0,
                'result' => $player_result
            ]);
        }

        if ($banker_round >= self::$config_banker_round_limit) {
            $this->printf("$banker_id 坐庄次数到了 强制下庄" . self::$config_banker_round_limit);
            $this->quitForBanker($this->getBankerId(), $this->getFdByPlayerId($this->getBankerId()));
        }

        if ($banker_round >= 3 && $banker_fd == -1) {
            $this->printf("$banker_id 掉线玩家 3轮 强制下庄");
            $this->quitForBanker($this->getBankerId(), $this->getFdByPlayerId($this->getBankerId()));
        }

        //乐豆数低于200万,下庄
        if ($banker_bet < 2000000) {
            $this->quitForBanker($banker_id, $banker_fd, true);
        }

        //机器人上庄设定，如有用户排队，则机器人坐满三盘及下庄，如排队列表内没有用户，则一直坐庄
        if($banker_fd==0 && $banker_round > 3 && $candidates)
        {
            $this->printf("有用户排队 机器人玩家 3轮 强制下庄");
            $this->quitForBanker($this->getBankerId(), $this->getFdByPlayerId($this->getBankerId()));
        }
        
        if ($this->getBankerFlag() == 'need_quit') {
            $this->setBankerFlag("");
            $this->printf("庄家要求下庄 结算后强制下庄");
            $this->quitForBanker($this->getBankerId(), $this->getFdByPlayerId($this->getBankerId()));
        }
        /*反本金
        if ((intval($all_player_profit) + intval($banker_profit) + intval($tax)) != 0) {
            $this->printf("ALL_PLAYER_PROFIT=%d BANKER_PROFIT=%d", $all_player_profit, $banker_profit);
            throw new Exception("计算错误");
        }
        */
        //$subtotal 数组没有索引 1 2 3 4 的bug 这里判断下
        if(!is_array($subtotal) || count($subtotal) < 5 ){
            return;
        }

        $cow_static = ["bank_id"=>$banker_id,"round"=>$rounds,
            "banker_info"=>json_encode(["profit"=>$subtotal[0],"cards"=>["type"=>$handful_cards[0]['type'],"max"=>$handful_cards[0]["max_i"],"info"=>$handful_cards[0]["cards"]]]),
            "player1_info"=>json_encode(["profit"=>$subtotal[1],"cards"=>["type"=>$handful_cards[1]['type'],"max"=>$handful_cards[1]["max_i"],"info"=>$handful_cards[1]["cards"]]]),
            "player2_info"=>json_encode(["profit"=>$subtotal[2],"cards"=>["type"=>$handful_cards[2]['type'],"max"=>$handful_cards[2]["max_i"],"info"=>$handful_cards[2]["cards"]]]),
            "player3_info"=>json_encode(["profit"=>$subtotal[3],"cards"=>["type"=>$handful_cards[3]['type'],"max"=>$handful_cards[3]["max_i"],"info"=>$handful_cards[3]["cards"]]]),
            "player4_info"=>json_encode(["profit"=>$subtotal[4],"cards"=>["type"=>$handful_cards[4]['type'],"max"=>$handful_cards[4]["max_i"],"info"=>$handful_cards[4]["cards"]]]),
            "fee"=>$fee,"pot_add"=>$pot_add,"pot_feedback"=>$pot_gift,"pot_feedback_id"=>$winPotPostion,"intervene"=>isset($handful_cards["intervene"])?$handful_cards["intervene"]:-1,
        ];
        $this->log("log_cow_settlement", $cow_static);
        //$this->setTimer(self::$count_card_cd + $now, 'startNew', []);
    }

    /**
     * 统计状态
     * 
     */
    public function staticLog($action, $key, $player_id=0, $value=0)
    {
        if(!$action || !$key)return ;
        $redis= $this->getRedis();
        if($action == "sAdd")
        {
            $redis->redis->sAdd($key, $player_id);
        }elseif($action == "incr")
        {
            $redis->redis->incr($key,$value);
        }elseif($action == "sRemove")
        {
            $redis->redis->sRemove($key, $player_id);
        }
     }
    
    /**
     * 入场
     * @param $player_id
     * @param $fd
     * @param $num
     */
    public function enter($player_id, $fd, $num)
    {
        $this->staticLog("sAdd", self::$static_log_redis_key["online_player"], $player_id);
        $this->staticLog("sAdd", self::$static_log_redis_key["open_player"], $player_id);
        $this->staticLog("incr", self::$static_log_redis_key["open_num"], 0, 1);
        
        $this->printf("玩家%s进场了 FD=%s", $player_id, $fd);

        $state = $this->getState();

        //$this->printf("玩家%s 买注%d", $player_id, $num);
        $this->addPlayer($player_id, $fd);
        //$this->setPlayer($player_id, 'bet', $num);

        $bet = $this->getBetByPlayerId($player_id);

        $pot = $this->getThePot();

        $bankerId = $this->getBankerId();

        $result = [
            "state"  => 0,
            'result' => [
                'gold'             => $bet,
                'bet'              => $bet,
                'pot_remain'       => $pot,
                'bankerIsMe'       => $bankerId == $player_id,
                'state'            => $state,
                'is_in_candidates' => $this->isInCandidates($player_id),
                'configs'          => [
                    "count_card_cd"             => self::$count_card_cd,
                    "send_card_cd"              => self::$send_card_cd,
                    "config_banker_round_limit" => self::$config_banker_round_limit,
                    "rate_configs"              => self::$rate_configs,
                    "config_candidate_options"  => self::$config_candidate_options,
                    "config_candidate_limit"    => self::$config_candidate_limit,
                ]
            ]
        ];


        if (!empty($this->getBankerId())) {
            $result['result']['banker'] = $this->getBankerInfo();
            $result['result']['banker']['round'] = $this->getBankerRound();

        }

        if ($state == 'sending' || $state == 'counting') {
            $handful_cards = $this->getHandfulCards();
            $result['result']['handful_cards'] = $handful_cards;
        }

        $positions = $this->getPositions();
        $result['result']['positions'] = [
            1 => array_sum($positions[1]),
            2 => array_sum($positions[2]),
            3 => array_sum($positions[3]),
            4 => array_sum($positions[4]),
        ];

        $result['result']['investments'] = [
            1 => @intval($positions[1][$player_id]),
            2 => @intval($positions[2][$player_id]),
            3 => @intval($positions[3][$player_id]),
            4 => @intval($positions[4][$player_id]),
        ];


        $this->sendToFd($fd, 'enter', $result);
        /* $this->log('log_cow_enter', [
            'uid'  => $player_id,
            'gold' => $num,
            'bet'  => $num
        ]); */
    }

    /**
     * 离场
     * @param $player_id
     * @param $fd
     */
    public function escape($player_id, $fd)
    {
        if ($this->getWagerByPosition($player_id, 1) > 0 ||
            $this->getWagerByPosition($player_id, 2) > 0 ||
            $this->getWagerByPosition($player_id, 3) > 0 ||
            $this->getWagerByPosition($player_id, 4) > 0
        ) {

            $this->sendToFd($fd, 'exit', [
                'errno' => 1,
                'error' => '已押注 当前轮没有结束不允许退出!'
            ]);
            return false;
        }

        if ($this->getBankerId() == $player_id) {
            $this->sendToFd($fd, 'exit', [
                'errno' => 2,
                'error' => '你是庄家 请先下庄后再退出!'
            ]);
            return false;
        }
        
        $this->staticLog("sRemove", self::$static_log_redis_key["online_player"], $player_id);
        
        
        $bet = intval($this->getBetByPlayerId($player_id));
        $candidate = null;
        $removeFromCandidate = $this->removeCandidate($player_id, $candidate);
        
        $this->printf("候选人下庄%s", json_encode($candidate));
        
        if ($candidate != null) {
            $bet = $this->addBetByPlayerId($player_id, $candidate['num']);
        }
        $this->sendToFd($fd, 'exit', [
            'state'  => 0,
            'result' => [
                'isRemoveFromCandidate' => $removeFromCandidate,
                'gold'                  => $bet,
                'bet'                   => 0
            ]
        ]);

        $this->printf("玩家 %s %d 离开了 ", $player_id, $fd);
       /*  $this->log('log_cow_exit', [
            'uid'  => $player_id,
            'gold' => $bet,
            'bet'  => 0,
        ]); */

        return true;
    }


    /**
     * 三数和算法 找出5张牌中可以组成10的倍数的3张牌
     * @param array $cards
     * @return array
     * @throws \Exception
     */
    static function threeSum($cards = [], $nums = [])
    {
        $len = count($cards);
        if ($len != 5) throw new \Exception("要5张牌!");
        foreach ($cards as $key => $card) {
            $c = new CowCard($card);
            $cards[$key] = $c;
            $nums[] = $c->getWeight();
        }
        for ($i = 0; $i < $len; $i++) {
            for ($j = $i+1; $j < $len; $j++) {
                for ($k = $j+1; $k < $len; $k++) {
                    if (in_array(array_sum([$nums[$i], $nums[$j], $nums[$k]]), [10, 20, 30])) {
                        return [strval($cards[$i]), strval($cards[$j]), strval($cards[$k])];
                    }
                }
            }
        }
        return [];
    }

    /**
     * 是否是五花牛
     * @param array $cards
     * @return bool
     */
    static function isFlowerCow($cards = [])
    {
        foreach ($cards as $card) {
            if (hexdec(substr($card, 1, 1)) <= 10) {
                return false;
            }
        }
        return true;
    }

    /**
     * 是否是炸弹牛
     * @param array $cards
     * @return bool
     */
    static function isBobmCow($cards = [])
    {
        $card_values = [];
        foreach ($cards as $card) {
            $card_values[] = hexdec(substr($card, 1, 1));
        }
        $combined_card_values = array_count_values($card_values);
        foreach ($combined_card_values as $value => $times) {
            if ($times >= 4) {
                return $value;
            }
        }
        return false;
    }


    /**
     * 获取牌型
     * 五花牛>炸弹牛>牛牛>牛九>牛八>牛七>牛六>牛五>牛四>牛三>牛二>牛一>没牛
     * 五花牛=12
     * 炸弹牛=11
     * 牛牛=10
     * 牛九=9
     * 牛八=8
     * 牛七=7
     * 牛六=6
     * 牛五=5
     * 牛四=4
     * 牛三=3
     * 牛二=2
     * 牛一=1
     * 没牛=0
     */
    static function getCardType(&$cards = [])
    {
        if (self::isFlowerCow($cards)) {
            $type = 12;
        } else if (self::isBobmCow($cards)) {
            $type = 11;
        } else {
            $cow = self::threeSum($cards);
            if (count($cow) == 3) {
                $sub = array_diff($cards, $cow);
                $type = self::cardSum($sub);
                //把组成牛的放在前面
                //$cards = array_merge(array_values($cow), array_values($sub));
                if ($type > 10) {
                    $type -= 10;
                }
            } else {
                $type = 0;
            }
        }
        return $type;
    }

    /**
     * 多张牌的牌值求和
     * @param $cards
     * @return number
     */
    static function cardSum($cards)
    {
        $card_values = [];
        foreach ($cards as $card) {
            $card = new CowCard($card);
            $card_values[] = $card->getWeight();
        }
        return array_sum($card_values);
    }


    /**
     * 找出手牌中最大的那一张
     * @param $cards
     * @return CowCard
     */
    static function getMax($cards, $type=0)
    {
        //炸弹 炸弹牌面
        if($type == 11)return self::isBobmCow($cards);
        
        $max = new CowCard($cards[0]);
        for ($i = 1; $i < count($cards); $i++) {
            $next = new CowCard($cards[$i]);
            $max = $max->toInt() > $next->toInt() ? $max : $next;
        }
        return $max;
    }

    /**
     * 下注
     * @param int $player_id 玩家ID
     * @param int $position 位置ID 庄家=0 天=1 地=2 玄=3 黄=4
     * @param int $num 下注的数量
     * @param string $fd
     */
    public function wager($player_id, $position = 0, $num = 0, $fd, $allIn=0)
    {
        $target_num = $num;
        if ($this->getState() != 'wagering') {
            $this->sendToFd($fd, 'wager', [
                'errno' => 1,
                'error' => ''
            ]);
            return;
        }

        if (!in_array($position, [1, 2, 3, 4])) {
            $this->printf("只能在天,地,玄,黄这四个格子内下注!");
            $this->sendToFd($fd, 'wager', [
                'errno' => 1,
                'error' => '只能在天,地,玄,黄这四个格子内下注!'
            ]);
            return;
        }

        if (empty($this->getBankerId())) {
            $this->printf("没有庄家的时候不可下注");
            $this->sendToFd($fd, 'wager', [
                'errno' => 2,
                'error' => '没有庄家的时候不可下注!'
            ]);
            return;
        }

        if ($this->getBankerId() == $player_id) {
            $this->printf("你是庄家的时候不可下注");
            $this->sendToFd($fd, 'wager', [
                'errno' => 2,
                'error' => '你是庄家的时候不可下注!'
            ]);
            return;
        }

        $my_bet = $this->getBetByPlayerId($player_id);

        if ($num > $my_bet) {
            $this->printf("你没那么多注!");
            $num = $my_bet;
        }


        $positions = $this->getPositions();
        $banker_bet = $this->getBankerBet();

        $all1 = array_sum($positions[1]);
        $all2 = array_sum($positions[2]);
        $all3 = array_sum($positions[3]);
        $all4 = array_sum($positions[4]);
        $all = $all1 + $all2 + $all3 + $all4;


        $now_bet_all =
            @intval($positions[1][$player_id]) +
            @intval($positions[2][$player_id]) +
            @intval($positions[3][$player_id]) +
            @intval($positions[4][$player_id]);

        $my_limit = intval(($now_bet_all + $my_bet) * 0.2);//自己的五分之一
        $banker_limit = intval(($banker_bet * 0.2));//庄家的五分之一

        $can_my_limit = $my_limit - $now_bet_all;
        $can_banker_limit = $banker_limit - $all;


        $num = min([$num, $can_banker_limit, $can_my_limit]);

        if ($num < 0) {
            $num = 0;
        }

        if ($num == 0) {
            $msg = "下注总数不可超过庄家或者自己总注的五分之一";
        } else if ($num < $target_num) {
            $msg = "你已成功下注" . $num . '乐豆';
        } else {
            $msg = "";
        }

        $this->printf("玩家%s 在 %d 位置上 下注%d ", $player_id, $position, $num);
        $this->addPosition($position, $player_id, $num);
        $now_bet = $this->addBetByPlayerId($player_id, $num * -1);


        $investment = @intval($positions[$position][$player_id]) + intval($num);
        //$now_bet = $this->getBetByPlayerId($player_id);
        $rst = [
            'msg'              => $msg,
            'position'         => $position,
            'num'              => $num,
            'target_num'       => $target_num,
            'investment'       => $investment,
            'bet'              => $now_bet,
            'my_limit'         => $my_limit,
            'banker_limit'     => $banker_limit,
            'now_bet_all'      => $now_bet_all,
            'can_my_limit'     => $can_my_limit,
            'can_banker_limit' => $can_banker_limit,
        ];

        $this->printf("下注结果:" . json_encode($rst));

        $this->sendToFd($fd, 'wager', [
            'state'  => 0,
            'result' => $rst
        ]);
        
        //统计使用
        $this->staticLog("incr", self::$static_log_redis_key["wager_pos{$position}_num"], 0, 1);
        $this->staticLog("sAdd", self::$static_log_redis_key["wager_pos{$position}_player"], $player_id);
        if($allIn)$this->staticLog("incr", self::$static_log_redis_key["wager_all_pos{$position}_num"], 0, 1);
        /* $this->log('log_cow_wager', [
            'uid'        => $player_id,
            'position'   => $position,
            'num'        => $num,
            'bet'        => $now_bet,
            'investment' => $investment,
            'round'      => $this->round
        ]); */
    }

    /**
     * 申请下庄
     * @param $player_id
     * @param $fd
     */
    public function quitForBanker($player_id, $fd, $force = true)
    {
        if ($player_id == 0 || $player_id == null) {
            return;
        }

        $this->printf("玩家 %s 申请下庄", $player_id);

        if ($force == false) {
            $state = $this->getState();

            if ($state != 'counting') {
                $this->setBankerFlag("need_quit");
                $this->sendToFd($fd, 'wager', [
                    'errno' => 1,
                    'error' => '本轮结算后自动下庄!'
                ]);
                return;
            }

            $round = $this->getBankerRound();
            if ($round <= 3) {
                $this->sendToFd($fd, 'wager', [
                    'errno' => 1,
                    'error' => '3轮以内不允许下庄!'
                ]);
                return;
            }
        }

        $bankerInfo = $this->getBankerInfo();
        $this->setBankerInfo([
            "id"     => 0,
            "name"   => '暂无人上庄',
            "sex"    => 1,
            "bet"    => 0,
            "new_fd" => 0
        ]);
        $bankerBet = $this->getBankerBet();
        $this->setBankerBet(0);
        $this->setBankerId(0);


        if ($fd == -1) {

            $quit_settlement = [
                'role'     => 'quit',
                'profit'   => $bankerBet,
                'pot_gift' => 0,
            ];
            $this->printf("下庄掉线 UID=$player_id " . json_encode($quit_settlement));


            if (isset($bankerInfo['new_fd']) && $bankerInfo['new_fd'] != 0) {
                $this->setBankerInfo([
                    "id"     => 0,
                    "name"   => '暂无人上庄',
                    "sex"    => 1,
                    "bet"    => 0,
                    "new_fd" => 0
                ]);
                $this->sendToFd($bankerInfo['new_fd'], 'HALL_FD', [
                    'state'  => 0,
                    'result' => [
                        'is_banker' => false
                    ]
                ]);
                $now_bet = $this->addBetByPlayerId($player_id, $bankerBet);

                sendToFd($bankerInfo['new_fd'], 4, 110, [
                    'coins' => $now_bet
                ]);
            } else {
                $this->addPlayerOfflineSettlement($player_id, $quit_settlement);
            }


            $this->removePlayer($player_id);

            //$hall_fd = $this->getHallFd($player_id);

            // $this->printf("HALL_FD=$hall_fd");


            //sendToFd($hall_fd, 8, 109, ['is_banker' => false]);

            //if ($hall_fd != null && $hall_fd != 0 && $hall_fd != '') {


//                global $sweety;
//                return $sweety->sendToFd($hall_fd, 8, 109,  [
//                    'is_banker' => false
//                ]);
            //}

        } else {
            $now_bet = $this->addBetByPlayerId($player_id, $bankerBet);

            $this->sendToFd($fd, $force ? 'forceQuit' : 'quit', [
                "state"  => '0',
                "result" => [
                    "bet" => $now_bet
                ]
            ]);
        }


        //todo:如果的是托管状态下 下庄 自动帮他退出
        //todo:如果玩家在上线时发现还在托管 自动拉进牛牛

        $candidate = $this->popBankerCandidate();
        if ($candidate != null) {
            $this->broadcast('newBanker', [
                "state"  => 0,
                "result" => [
                    "banker" => [
                        "id"         => $candidate['id'],
                        "name"       => $candidate['name'],
                        "sex"        => $candidate['sex'],
                        "bet"        => $candidate['num'],
                        "bankerIsMe" => false
                    ]
                ]
            ], [$candidate['id']]);

            $candidate_fd = $this->getFdByPlayerId($candidate['id']);
            $this->sendToFd($candidate_fd, 'newBanker', [
                "state"  => 0,
                "result" => [
                    "banker" => [
                        "id"         => $candidate['id'],
                        "name"       => $candidate['name'],
                        "sex"        => $candidate['sex'],
                        "bet"        => $candidate['num'],
                        "bankerIsMe" => true
                    ]
                ]
            ]);


            $this->setBankerId($candidate['id']);
            $this->setBankerBet($candidate['num']);
            //$this->addBetByPlayerId($candidate['id'], $candidate['num'] * -1);
            $this->setBankerInfo([
                "id"     => $candidate['id'],
                "name"   => $candidate['name'],
                "sex"    => $candidate['sex'],
                "bet"    => $candidate['num'],
                "new_fd" => 0
            ]);
            $this->setBankerRound(1);
            //统计使用
            $this->staticLog("sAdd", self::$static_log_redis_key["banker_player"], $candidate['id']);
        } else {
            $this->setBankerId(0);
            $this->broadcast('newBanker', [
                "state"  => 0,
                "result" => [
                    "banker" => [
                        "id"         => 0,
                        "name"       => '暂无人上庄',
                        "sex"        => 1,
                        "bet"        => 0,
                        "bankerIsMe" => false
                    ]
                ]
            ]);
            $this->setBankerRound(0);
        }
    }

    public function quitForCandidate($player_id, $fd)
    {
        $this->printf("候选人下庄 UID=%s", $player_id);
        $candidate = null;
        $succ = $this->removeCandidate($player_id, $candidate);
        $candidates = $this->getBankerCandidates();
        
        
        $this->printf("候选人下庄%s", json_encode($candidate));

        if ($candidate != null) {
            $bet = $this->addBetByPlayerId($player_id, $candidate['num']);
        } else {
            $bet = $this->getBetByPlayerId($player_id);
        }

        $this->sendToFd($fd, 'quitForCandidate', [
            "state"  => 0,
            "result" => [
                "succ"       => $succ,
                "bet"        => $bet,
                "candidates" => $candidates
            ]
        ]);
    }

    /**
     * 获取上庄候选人列表
     */
    public
    function getBankerCandidates()
    {
        return [];
    }

    /**
     * 抛出首个候选人
     * @return array
     */
    public
    function popBankerCandidate()
    {
        return array_shift($this->candidates);
    }

    /**
     * @return int
     */
    public
    function lenBankerCandidate()
    {
        return count($this->candidates);
    }

    public
    function removeCandidate($player_id, &$info = null)
    {
        return $player_id == 1;
    }

    /**
     * @param $player_id
     * @param $sex 1=男 2=女
     * @param $num
     */
    public
    function addBankerCandidate($player_id, $name, $sex, $num)
    {
        $this->candidates[] = [
            'id'   => $player_id,
            'sex'  => $sex,
            'name' => $name,
            'num'  => $num
        ];
    }

    /**
     * 申请上庄
     * 庄家最低上庄条件：500万
     * @param $player_id
     * @param $sex
     * @param $num
     * @param $fd
     */
    public function applyForBanker($player_id, $name, $sex, $num, $fd)
    {
        $this->printf("玩家%s申请上庄,注%d !", $player_id, $num);


        if ($num < 5000000) {
            $this->printf("玩家%s申请上庄最少需要500w 拒绝!", $player_id);
            $this->sendToFd($fd, 'applyForBanker', [
                'errno' => 1,
                'error' => '申请上庄最少需要500w!'
            ]);
            return;
        }

        $positions = $this->getPositions();
        if(array_sum([@$positions[1][$player_id],@$positions[2][$player_id],@$positions[3][$player_id],@$positions[4][$player_id]]))
        {
            $this->sendToFd($fd, 'applyForBanker', [
                'errno' => 5,
                'error' => '本局您已经押分，不能申请上庄!'
            ]);
            return;
        }
        $player = $this->getPlayerById($player_id);
        if ($player['bet'] < $num) {
            $this->printf("玩家%s申请上庄 但是注不足%d 拒绝!", $player_id, $num);
            $this->sendToFd($fd, 'applyForBanker', [
                'errno' => 2,
                'error' => '钱不够'
            ]);
            return;
        }

        if ($this->lenBankerCandidate() >= 20) {
            $this->printf("玩家%s申请上庄 但是人数太多了 拒绝!", $player_id);
            $this->sendToFd($fd, 'applyForBanker', [
                'errno' => 3,
                'error' => '申请上庄的人太多了,请等一会再来吧'
            ]);
            return;
        }

        if ($this->getBankerId() == $player_id) {
            $this->printf("玩家%s申请上庄 重复申请上庄1 拒绝!", $player_id);
            $this->sendToFd($fd, 'applyForBanker', [
                'errno' => 4,
                'error' => '你已经是庄家了,请不要重复上庄!'
            ]);
            return;
        }
        
        if (empty($this->getBankerId()))
        {
            $this->printf('目前没有庄家,直接上庄!');
            $this->setBankerId($player_id);
            $rounds = 1;
            if($this->getHandfulCards())
            {
                $rounds = 0;
            }
            $this->setBankerRound($rounds);
            $this->setBankerBet($num);
            $this->setBankerInfo([
                "id"   => $player_id,
                "name" => $name,
                "sex"  => $sex,
                "bet"  => $num
            ]);

            $bet = $this->addBetByPlayerId($player_id, $num * -1);

            $this->sendToFd($fd, 'applyForBanker', [
                "state"  => 0,
                "result" => [
                    'bet'            => $bet,
                    'beTheCandidate' => false
                ]
            ]);


            $this->broadcast('newBanker', [
                "state"  => 0,
                "result" => [
                    "banker" => [
                        "id"         => $player_id,
                        "name"       => $name,
                        "sex"        => $sex,
                        "bet"        => $num,
                        "bankerIsMe" => false
                    ]
                ]
            ], [$player_id]);

            $this->sendToFd($fd, 'newBanker', [
                "state"  => 0,
                "result" => [
                    "banker" => [
                        "id"         => $player_id,
                        "name"       => $name,
                        "sex"        => $sex,
                        "bet"        => $num,
                        "bankerIsMe" => true
                    ]
                ]
            ]);
            //统计使用
            if($player_id > 1000)$this->staticLog("sAdd", self::$static_log_redis_key["banker_player"], $player_id);
        } 
        else
        {

            $candidates = $this->getBankerCandidates();
            $candidates_id = array_column($candidates, 'id');
            if (in_array($player_id, $candidates_id)) {
                $this->printf("玩家%s申请上庄 重复申请上庄2 拒绝!", $player_id);
                $this->sendToFd($fd, 'applyForBanker', [
                    'errno' => 5,
                    'error' => '你已经是庄家候选人了,请不要重复上庄!'
                ]);
                return;
            }

            $bet = $this->addBetByPlayerId($player_id, $num * -1);

            $this->sendToFd($fd, 'applyForBanker', [
                "state"  => 0,
                "result" => [
                    'bet'            => $bet,
                    'beTheCandidate' => true
                ]
            ]);
            $this->addBankerCandidate($player_id, $name, $sex, $num);
        }
        
        //统计使用
        if($player_id){
            $this->staticLog("sAdd", self::$static_log_redis_key["apply_banker_player"], $player_id);
            $this->staticLog("incr", self::$static_log_redis_key["apply_banker_num"], 0, 1);
        }
    }

    /**
     * 买注
     * @param $player_id
     * @param int $num
     * @param $fd
     */
    public function buyBet($player_id, $num = 0, $fd)
    {
//        if ($num < 20000) {
//            $this->printf("进场携带数小于20000 拒绝!");
//            $this->sendToFd($fd, 'buyBet', [
//                'errno' => 1,
//                'error' => '进场携带数小于20000 拒绝!'
//            ]);
//            return;
//        }

        //$this->printf("玩家%s 买注%d", $player_id, $num);
        //$this->setPlayer($player_id, 'bet', $num);
    }

    /**
     * 返回当前状态 init 初始化 wagering 下注中 counting 结算中
     * @return string
     */
    public
    function getState()
    {
//        if ($this->getSendCardTime() == 0 && $this->getCountCardTime() == 0) {
//            return 'init';
//        }
//
//        if ($now > $this->getSendCardTime() && $now < $this->getCountCardTime()) {
//            return 'wagering';
//        } else {
//            return 'counting';
//        }

    }

    public
    function getStateTime()
    {

    }

    public
    function setState($state)
    {
        $this->broadcast('change_state', [
            "state"  => 0,
            "result" => [
                'new_state' => $state
            ]
        ]);
    }

    public
    function onSendCard()
    {
        $this->sendCard();
        $this->setState("sending");
        //$this->setTimer(self::$show_count_cd + $now, 'cardCounting', $handful_cards);

    }

    public
    function startNew()
    {
        $this->setState('wagering');
        // $this->setTimer($now + self::$send_card_cd, "onSendCard", []);
        $this->broadcast('start_new', [
            'state'  => 0,
            'result' => []
        ]);
    }

    public
    function onCallBack()
    {
        $this->printf("房间思考");
        $now = time();
        //$this->runTimer($now);
        $state = $this->getState();
        $ttl = ($now - $this->getStateTime()) + 1;
        $this->printf("当前状态%s %d", $state, $ttl);

        if ($state == '') {
            $state = 'wagering';
            $this->startNew();
            return;
        }

        if ($state == 'wagering' && $ttl > self::$send_card_cd) {
            $this->onSendCard();
            return;
        }

        if ($state == 'sending' && $ttl > self::$count_card_cd) {
            $this->cardCounting($this->getHandfulCards());
            return;
        }

        if ($state == 'counting' && $ttl > self::$show_count_cd) {
            $this->startNew();
            return;
        }


        if ($state == 'wagering') {
            $positions = $this->getPositions();
            $this->broadcast('deposits', [
                'state'  => 0,
                'result' => [
                    'deposits' => [
                        1 => array_sum($positions[1]),
                        2 => array_sum($positions[2]),
                        3 => array_sum($positions[3]),
                        4 => array_sum($positions[4])
                    ]
                ]
            ]);
        }
    }

    public function retrace($player_id)
    {
        $bet = -1;
        $offline_settlements = $this->popPlayerOfflineSettlement($player_id);
        foreach ($offline_settlements as $offline_settlement) {
//            [
//                'win'        => $my_profit > 0,
//                'system_cut' => $system_cut,
//                'pot_cut'    => $pot_cut,
//                'profit'     => $profit,
//                'pot_gift'   => $splice_gift,
//                'bet'        => $this->getBetByPlayerId($player_id)
//            ]

            $this->printf("掉线恢复 UID=$player_id:" . json_encode($offline_settlement));

            if ($offline_settlement['role'] == 'player') {
                $bet = $this->addBetByPlayerId($player_id, $offline_settlement['profit'] + $offline_settlement['pot_gift']);

            }

            //:{"role":"quit","profit":10000000,"pot_gift":0}
            if ($offline_settlement['role'] == 'quit') {
                $this->printf("庄家恢复" . $offline_settlement['profit']);
                $bet = $this->addBetByPlayerId($player_id, $offline_settlement['profit']);
            }


//            if ($offline_settlement['role'] == 'banker' && $this->getBankerId() != $player_id) {
//                $this->addBetByPlayerId($player_id, $offline_settlement['profit'] + $offline_settlement['pot_gift']);
//            }

        }

        return $bet;
    }

    public function reconnect($player_id, $fd)
    {
        $this->printf("玩家%s重连了 FD=%s", $player_id, $fd);


//        if ($num < 10000) {
//            $this->printf("下注数小于10000 拒绝!");
//            $this->sendToFd($fd, 'enter', [
//                'errno' => 1,
//                'error' => '下注数小于10000 拒绝!'
//            ]);
//            return;
//        }

//        if ($num < 20000) {
//            $num = 0;
//        }

        $state = $this->getState();

        //$this->printf("玩家%s 买注%d", $player_id, $num);
        $this->addPlayer($player_id, $fd);
        //$this->setPlayer($player_id, 'bet', $num);

        $bet = $this->getBetByPlayerId($player_id);

        $pot = $this->getThePot();

        $bankerId = $this->getBankerId();

        $result = [
            "state"  => 0,
            'result' => [
                'gold'             => $bet,
                'bet'              => $bet,
                'pot_remain'       => $pot,
                'bankerIsMe'       => $bankerId == $player_id,
                'state'            => $state,
                'is_in_candidates' => $this->isInCandidates($player_id),
                'configs'          => [
                    "count_card_cd"             => self::$count_card_cd,
                    "send_card_cd"              => self::$send_card_cd,
                    "config_banker_round_limit" => self::$config_banker_round_limit,
                    "rate_configs"              => self::$rate_configs,
                    "config_candidate_options"  => self::$config_candidate_options,
                    "config_candidate_limit"    => self::$config_candidate_limit,
                ]
            ]
        ];


        if (!empty($this->getBankerId())) {
            $result['result']['banker'] = $this->getBankerInfo();
            $result['result']['banker']['round'] = $this->getBankerRound();

        }

        if ($state == 'sending') {
            $handful_cards = $this->getHandfulCards();
            $result['result']['handful_cards'] = $handful_cards;
        }

        $positions = $this->getPositions();
        $result['result']['positions'] = [
            1 => array_sum($positions[1]),
            2 => array_sum($positions[2]),
            3 => array_sum($positions[3]),
            4 => array_sum($positions[4]),
        ];

        $result['result']['investments'] = [
            1 => @intval($positions[1][$player_id]),
            2 => @intval($positions[2][$player_id]),
            3 => @intval($positions[3][$player_id]),
            4 => @intval($positions[4][$player_id]),
        ];


        $this->sendToFd($fd, 'reconnect', $result);
//        $this->log('log_cow_enter', [
//            'uid'  => $player_id,
//            'gold' => $num,
//            'bet'  => $num
//        ]);
    }

    public function isInCandidates($player_id, $cache = [])
    {
        if (count($cache) == 0) {
            $cache = $this->getBankerCandidates();
        }

        foreach ($cache as $candidate) {
            if ($candidate['id'] == $player_id) {
                return true;
            }
        }

        return false;
    }

//endregion


    public
    function __construct()
    {

    }

//region get set

    private
        $banker = null;
    private
        $players = [];
    private
        $positions = [1 => [], 2 => [], 3 => [], 4 => []];
    private
        $banker_round = 0;
    private
        $pot = 0;
    private
        $handful_cards = [];
    private
        $candidates = [];
    private
        $round = 1;

    public function addPlayerOfflineSettlement($player_id, $settlement)
    {

    }

    public
    function getHandfulCards()
    {
        return $this->handful_cards;
    }

    public
    function setHandfulCards($handful_cards)
    {
        $this->handful_cards = $handful_cards;
    }


    public
    function getThePot()
    {
        return $this->pot;
    }

    public
    function incrThePot($value)
    {
        return $this->pot += $value;
    }


    protected
    function getPlayerById($player_id)
    {
        return $this->players[$player_id];
    }

    protected
    function addPlayer($player_id, $fd)
    {
        $this->players[$player_id] = [
            'bet' => 0,
            'fd'  => $fd
        ];
    }

    public
    function setPlayer($player_id, $key, $value)
    {
        $this->players[$player_id][$key] = $value;
        return $this;
    }

    public function removePlayer($player_id)
    {

    }

    public function getHallFd($player_id)
    {
        return '';
    }

    protected
    function extractPositions()
    {
        return [];
    }

    public
    function getPositions()
    {
        return $this->positions;
    }

    protected
    function addPosition($position, $player_id, $num)
    {
        $this->positions[$position][$player_id] = $num;
    }

    protected
    function getWagerByPosition($player_id, $position)
    {
        return isset($this->positions[$position][$player_id]) ? $this->positions[$position][$player_id] : 0;
    }

    public
    function getPositionHistory()
    {
        //return $this->historyPipe->getAll();

    }

    public
    function getBankerId()
    {
        return $this->banker;
    }

    public
    function setBankerId($banker_id)
    {
        $this->banker = $banker_id;
        return $this;
    }

    /**
     * @return int
     */
    public
    function getBankerRound()
    {
        return $this->banker_round;
    }

    /**
     * @param $banker_round
     * @return $this
     */
    public
    function setBankerRound($banker_round)
    {
        $this->banker_round = $banker_round;
        return $this;
    }

    public
    function incrBankerRound()
    {
        $this->banker_round++;
        return $this;
    }

    public
    function getBankerBet()
    {
        return 0;
    }

    public
    function setBankerBet($value)
    {

    }

    public
    function incrBankerBet($value)
    {
        return $value;
    }
    
    public
    function getRound()
    {
        return 0;
    }
    
    public
    function incrRound($value)
    {
        return $value;
    }
    
    public
    function getAllFd()
    {
        return [];
    }

    public
    function getFdByPlayerId($player_id)
    {
        return $player_id;
    }

    public
    function getBetByPlayerId($player_id)
    {
        return $player_id;
    }

    public
    function getNameByPlayerId($player_id)
    {
        return $player_id;
    }

    public
    function addBetByPlayerId($player_id, $num)
    {
        return $this->players[] = [
            'id'  => $player_id,
            'bet' => $num
        ];
    }

    protected
        $sendCardTime = 0;
    protected
        $countCardTime = 0;

    /**
     * @return int
     */
    public
    function getSendCardTime()
    {
        return $this->sendCardTime;
    }

    /**
     * @param int $sendCardTime
     */
    public
    function setSendCardTime($sendCardTime)
    {
        $this->sendCardTime = $sendCardTime;
    }


    /**
     * @return int
     */
    public
    function getCountCardTime()
    {
        return $this->countCardTime;
    }

    /**
     * @param int $countCardTime
     */
    public
    function setCountCardTime($countCardTime)
    {
        $this->countCardTime = $countCardTime;
    }

    protected
        $position_histories = [];


    public
    function addPositionHistory($history)
    {
        $this->position_histories[] = $history;
    }

    public
    function getAllPlayer()
    {
        return $this->players;
    }

    public
    function setBankerInfo(array $value)
    {

    }

    public
    function getBankerInfo()
    {
        return [];
    }

    public
    function setTimer($time, $func, $param)
    {

    }

    public
    function runTimer($now)
    {

    }

    public function setBankerFlag($flag)
    {
        return $flag;
    }

    public function getBankerFlag()
    {
        return "";
    }

    public function popPlayerOfflineSettlement($player_id)
    {
        return [];
    }

    public function getIntervene()
    {
        return -1;
    }

//endregion
}


/**
 * 百人牛牛机器人
 * Class CowRobot
 */
abstract class CowRobot
{
    protected $playerId;
    protected $playerName;

    public $robotList = [];
    public $mysql;
    public $redis;
    
    public $fd = 0;

    /**
     * @var $machine BaseCowMachine
     */
    protected $machine = null;

    /**
     * @return BaseCowMachine
     */
    public function getMachine()
    {
        return $this->machine;
    }

    /**
     * @param $machine
     * @return $this
     */
    public function setMachine($machine, $mysql=null, $redis=null)
    {
        $this->machine = $machine;
        $this->mysql = $mysql;
        $this->redis = $redis;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getPlayerId()
    {
        return $this->playerId;
    }

    /**
     * @param mixed $playerId
     */
    public function setPlayerId($playerId)
    {
        $this->playerId = $playerId;
    }
    
    /**
     * @author Tidel 20161103
     * 牛牛机器人
     * 
     */
    public function getRobotList()
    {
        if(!$this->robotList)
        {
            $sql = "select uid,nick,sex from lord_cow_robot";
            $rows = $this->mysql->getData($sql);
            $list = [];
            foreach ($rows as $value)
            {
                $list[$value["uid"]] = $value;
                $data=["coins"=>0,"uid"=>$value["uid"],"nick"=>$value["nick"],"sex"=>$value["sex"]];
                $this->redis->hmset("lord_user_info_".$value["uid"], $data);
            }
            $this->robotList = $list;
        }
    }
}

/**
 * 百人牛牛庄家机器人
 * Class CowBankerRobot
 */
class CowBankerRobot extends CowRobot
{

    public $stayRound = 0;
    
    public function __construct()
    {
    }
    
    public function throwRobot()
    {
        if(!$this->machine->getBankerId())
        {
            $bet = 100000000;
            $this->redis->hSet('lord_user_info_50','coins',$bet);
            $this->redis->hSet('cow:players:fd', 50, 0);
            $this->redis->set("cow:banker:robot:stayRound",10);
            $this->machine->applyForBanker(50, "系统小优", 1, $bet, 0);
        }
        return ;
        
        $robotsID = array_keys($this->robotList);
        $bankerID = $this->machine->getBankerId();
        if (in_array($this->machine->getBankerId(),$robotsID)) 
        {
            if ($this->machine->getBankerRound() > intval($this->redis->get("cow:banker:robot:stayRound")))
            {
                $this->machine->quitForBanker($bankerID, 0, false);
            }
        }
        if(!$this->machine->getBankerId())
        {
            $now = time();
            $throwTime = $now < strtotime("00:30") || $now > strtotime("06:30")? true : false;
            if($throwTime)
            {
                $bet_config = [5000000, 10000000, 20000000, rand(5000000,50000000)];
                $old_bankerRobotID=$bankerRobotID = intval($this->redis->get("cow:banker:robot:id"));
                $bet = $this->redis->hget("lord_user_info_{$bankerRobotID}", "coins");
                $change_robot_time = $this->redis->get("cow:banker:robot:time");
                if($bet<min($bet_config) || $change_robot_time < $now)$bankerRobotID += 1;
                if($bankerRobotID >= max($robotsID)) $bankerRobotID = 1;
                if($old_bankerRobotID != $bankerRobotID)
                {
                    $bet = $bet_config[rand(0, count($bet_config) - 1)];
                    $this->redis->set("cow:banker:robot:time", $now+rand(5400,9000));
                    $this->redis->set("cow:banker:robot:id", $bankerRobotID);
                    
                }
                $this->setPlayerId($bankerRobotID);
                //当上庄排队人数小于等于2时进行排队
                //如没有人上庄则机器人直接上庄
                if (count($this->machine->getBankerCandidates()) <= 2)
                {
                    $this->redis->hSet('lord_user_info_'.$this->playerId,'coins',$bet);
                    $this->redis->hSet('cow:players:fd', $this->playerId, 0);
                    //机器人上庄局数设定为3~10局随机
                    $this->redis->set("cow:banker:robot:stayRound", rand(3,10));
                    $this->machine->applyForBanker($this->playerId, $this->robotList[$this->playerId]["nick"], $this->robotList[$this->playerId]["sex"], $bet, 0);
                }
            }
        }
    }
    
    public function onCallBack()
    {
        $this->machine->printf("百人牛牛庄家机器人 思考");
        $this->throwRobot();
    }
}

/**
 * 百人牛牛闲家机器人
 * Class CowPlayerRobot
 */
class CowPlayerRobot extends CowRobot
{
    public function __construct()
    {
        
    }

    public function onCallBack()
    {


        $this->machine->printf("百人牛牛闲家机器人 思考");
        //$this->robotList 是空数组造成max报错的bug ，这里判断下
        if ($this->robotList && $this->machine->getState() == 'wagering') {
            $robotsID = array_keys($this->robotList);
            $fakePlayerID = max($robotsID);
            $this->redis->hSet('lord_user_info_'.$fakePlayerID,'coins',100000000);
            $this->redis->hSet('cow:players:fd', $fakePlayerID, 0);
            $my = $this->machine->getBetByPlayerId($fakePlayerID);
            $banker_bet = $this->getMachine()->getBankerBet();
            $positions = $this->getMachine()->getPositions();
            $all_investment = array_sum($positions[1]) + array_sum($positions[2]) + array_sum($positions[3]) + array_sum($positions[4]);
            $can1 = intval(($banker_bet * 0.2) - $all_investment);
            $can2 = intval($my * 0.2);
            $can_wager_num = $can1 >= $can2 ? $can2 : $can1;
            $position = rand(1, 4);
            $bet = intval((rand(3, 10) / 100) * $can_wager_num);
            $this->machine->wager($fakePlayerID, $position, $bet, 0);
            $position = rand(1, 4);
            $bet = intval((rand(3, 10) / 100) * $can_wager_num);
            $this->machine->wager($fakePlayerID, $position, $bet, 0);
        }
        //机器人可在桌并进行下注
        //下注时间共为15秒，每三秒进行下注，下注金额为可下注金额的3%~15%
    }
}

class DDZCowMachine extends BaseCowMachine
{
    protected static $instance = null;

    public static function getInstance()
    {
        if (self::$instance == null) {
            self::$instance = new DDZCowMachine();
        }
        return self::$instance;
    }

    /**
     * @var gamer
     */
    private $gamer = null;

    /**
     * @var RD
     */
    private $redis = null;

    /**
     * @var DB
     */
    private $mysql = null;

    /**
     * @return null
     */
    public function getGamer()
    {
        return $this->gamer;
    }

    /**
     * @param $gamer gamer
     * @return $this
     */
    public function setGamer($gamer)
    {
        $this->gamer = $gamer;
        $this->mysql = $gamer->mysql;
        return $this;
    }

    /**
     * @return RD
     */
    public function getRedis()
    {
        return $this->redis;
    }

    /**
     * @param $redis
     * @return $this
     */
    public function setRedis($redis)
    {
        $this->redis = $redis;
        return $this;
    }


    public function printf()
    {
        $args = func_get_args();
        $rst = call_user_func_array('sprintf', $args);
        glog("COW " . $rst);
    }

    public function getAllFd()
    {
        return $this->redis->redis->hGetAll('cow:players:fd');
    }

    public function escape($player_id, $fd)
    {

        $rst = parent::escape($player_id, $fd);

        // $bet = $this->redis->redis->hGet('cow:players:bet', $player_id);
        if ($rst) {
            $this->redis->redis->hDel('cow:players:fd', $player_id);
        }
//        $this->sendToFd($fd, 'exit', [
//            'state'  => 0,
//            'result' => [
//                'gold' => $bet,
//                'bet'  => $bet
//            ]
//        ]);
    }

    public function addBetByPlayerId($player_id, $num)
    {
        $coin = $this->getBetByPlayerId($player_id);
        $coin_now = $coin + $num;
        setUser($player_id, [
            'coins' => $coin_now
        ]);
        return $coin_now;
        //return $this->redis->redis->hIncrBy('lord_user_info_' . $player_id, 'coins', $num);
        //return $this->redis->redis->hIncrBy('cow:players:bet', $player_id, $num);
    }

    public function getBetByPlayerId($player_id)
    {
        return intval($this->redis->redis->hGet('lord_user_info_' . $player_id, 'coins'));
    }

    public function log($table, $fields = [])
    {
        $fields['create_time'] = time();

        $keys = array_keys($fields);
        $values = array_values($fields);
        $col = implode("`, `", $keys);
        $val = implode("', '", $values);
        $sql = "INSERT INTO `$table` (`$col`) VALUES ('$val');";

        $this->mysql->runSql($sql);
    }

    public function notice($msg)
    {
        $this->printf('发送跑马灯:' . $msg);
        sendHorn($msg);
    }


    public function sendToFd($fd, $action, $data)
    {
        if ($fd <= 0) {
            return;
        }

        $cmd = 8;
        switch ($action) {
            case 'enter'://进场
                $code = 1;
                break;
            case 'exit'://离场
                $code = 2;
                break;
            case 'wager'://下注
                $code = 3;
                break;
            case 'position'://位置上的赌资改变
                $code = 105;
                break;
            case 'history'://位置历史
                $code = 7;
                break;
            case 'candidates'://候选人
                $code = 6;
                break;
            case 'applyForBanker'://申请上
                $code = 4;
                break;
            case 'quit'://下庄
                $code = 5;
                break;
            case 'notice'://跑马灯
                $code = 9;
                break;
            case 'reconnect'://重连
                $code = 10;
                break;
            case 'quitForCandidate'://候选人下庄
                $code = 11;
                break;
            case 'sendCard'://发牌
                $code = 101;
                break;
            case 'newBanker': //换庄
                $code = 103;
                break;
            case 'buyBet'://加注
                $code = 8;
                break;
            case 'deposits'://牌桌赌注变化
                $code = 105;
                break;
            case 'settlement':  //结算
                $code = 104;
                break;
            case 'forceQuit': //强制下庄
                $code = 106;
                break;
            case 'start_new':
                $code = 107;
                break;
            case 'change_state':
                $code = 108;
                break;
            case 'HALL_FD':
                $code = 109;
                break;
            default:
                throw new \Exception("百人牛牛有没实现的接口" . $action);
                break;
        }


        if (isset($data['state']) && $data['state'] == 0) {
            $rst = [
                'code' => $cmd * 10000 + $code,
                'data' => array_merge([
                    'errno' => 0,
                    'error' => ''
                ], isset($data['result']) ? $data['result'] : [])
            ];
        } else if (isset($data['errno'])) {
            $rst = [
                'code' => $cmd * 10000 + $code,
                'data' => [
                    'errno' => $data['errno'],
                    'error' => $data['error']
                ]
            ];
        } else {
            throw new Exception('错误的数据' . json_encode($data));
        }

        sendToFd($fd, $cmd, $code, $rst);

    }

    public function setTimer($time, $func, $params)
    {
        $this->redis->redis->hSet('cow:timer', $time, json_encode([
            'func'   => $func,
            'params' => $params
        ]));
    }

    public function runTimer($now)
    {
//        $all = $this->redis->redis->hGetAll('cow:timer');
//
//        foreach ($all as $ttl => $timer) {
//            if ($ttl >= $now) {
//                $this->printf("TIMER %d %s", $ttl, $timer);
//                $this->redis->redis->hDel('cow:timer', $ttl);
//                $timer = json_decode($timer, true);
//                $func = $timer['func'];
//                $params = $timer['params'];
//                $this->{$func}($params);
//            }
//        }

        $timer = $this->redis->redis->hGet('cow:timer', $now);
        if ($timer != null) {
            $this->redis->redis->hDel('cow:timer', $now);

            $timer = json_decode($timer, true);
            $func = $timer['func'];
            $params = $timer['params'];
            $this->{$func}($params);
        }
    }


    //region get set


    public function getHandfulCards()
    {
        return json_decode($this->redis->get('cow:handful_cards'), true);
    }

    public function setHandfulCards($handful_cards)
    {
        if (count($handful_cards) == 0) {
            $this->redis->del('cow:handful_cards');
        } else {
            $this->redis->set('cow:handful_cards', json_encode($handful_cards));
        }
    }


    public function getThePot()
    {
        return $this->redis->get('cow:pot');
    }

    public function incrThePot($value)
    {
        return $this->redis->redis->incrBy('cow:pot', $value);
    }


    protected function getPlayerById($player_id)
    {
        // $bet = $this->redis->redis->hGet('cow:players:bet', $player_id);
        $bet = intval($this->redis->redis->hGet('lord_user_info_' . $player_id, 'coins'));
        $this->printf("%s=%d", 'lord_user_info_' . $player_id, $bet);
        $fd = $this->redis->redis->hGet('cow:players:fd', $player_id);
        return [
            'fd'  => $fd,
            'bet' => $bet
        ];
    }

    protected function addPlayer($player_id, $fd)
    {
        // $this->redis->redis->hSet('cow:players:bet', $player_id, 0);
        $this->redis->redis->hSet('cow:players:fd', $player_id, $fd);
    }

    public function removePlayer($player_id)
    {
        $this->redis->redis->hDel('cow:players:fd', $player_id);
    }

    public function setPlayer($player_id, $key, $value)
    {
        $this->redis->redis->hSet("cow:players:$key", $player_id, $value);
        return $this;
    }
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                            
    public function getPositions()
    {
        $positions1 = $this->redis->hgetall('cow:positions_1');
        $positions2 = $this->redis->hgetall('cow:positions_2');
        $positions3 = $this->redis->hgetall('cow:positions_3');
        $positions4 = $this->redis->hgetall('cow:positions_4');
        $res = [
            1 => is_array($positions1) ? $positions1 : [],
            2 => is_array($positions2) ? $positions2 : [],
            3 => is_array($positions3) ? $positions3 : [],
            4 => is_array($positions4) ? $positions4 : []
        ];
        return $res;
    }


    protected function addPosition($position, $player_id, $num)
    {
        return $this->redis->redis->hIncrByFloat('cow:positions_' . $position, $player_id, $num);
    }

    protected function getWagerByPosition($player_id, $position)
    {
        return $this->redis->redis->hGet('cow:positions_' . $position, $player_id);
    }   

    public function getPositionHistory()
    {
        $list = $this->redis->lrange('cow:position_history', 0, -1);
        return $list;
    }

    public function addPositionHistory($history)
    {
        $this->printf("添加历史记录:" . json_encode($history));
        $this->redis->redis->rPush('cow:position_history', json_encode($history));
        $len = $this->redis->redis->lLen('cow:position_history');
        if ($len > 10) {
            for ($i = 0; $i < $len - 10; $i++) {
                $this->redis->lpop('cow:position_history');
            }
        }
    }

    public function getBankerId()
    {
        return $this->redis->get('cow:banker:id');
    }

    public function setBankerId($banker_id)
    {
        $this->redis->set('cow:banker:id', $banker_id);
        return $this;
    }

    /**
     * @return int
     */
    public function getBankerRound()
    {
        return $this->redis->redis->get('cow:banker:round');
    }

    /**
     * @param $banker_round
     * @return $this
     */
    public function setBankerRound($banker_round)
    {
        $this->redis->redis->set('cow:banker:round', $banker_round);
        return $this;
    }

    public function incrBankerRound()
    {
        return intval($this->redis->redis->incrBy('cow:banker:round', 1));
    }

    public function getBankerCandidates()
    {
        $candidates = $this->redis->lrange('cow:candidates', 0, -1);
        return $candidates == null ? [] : $candidates;
    }

    public function popBankerCandidate()
    {
        $candidate = $this->redis->redis->lPop('cow:candidates');
        return $candidate == null ? null : json_decode($candidate, true);
    }

    public function lenBankerCandidate()
    {
        return $this->redis->llen('cow:candidates');
    }

    public function removeCandidate($player_id, &$info = null)
    {
        $candidates = $this->redis->redis->lRange('cow:candidates', 0, -1);
        foreach ($candidates as $index => $candidate) {
            $c = json_decode($candidate, true);
            if ($c['id'] == $player_id) {
                $info = $c;
                $this->redis->redis->lRemove('cow:candidates', $candidate, 1);
                return true;
            }
        }
        return false;
    }

    public function addBankerCandidate($player_id, $name, $sex, $num)
    {
        $this->redis->redis->rPush('cow:candidates', json_encode([
            'id'   => $player_id,
            'sex'  => $sex,
            'name' => $name,
            'num'  => $num
        ]));
    }

    public function getBankerBet()
    {
        $bet = intval($this->redis->redis->get('cow:banker:bet'));
        if ($bet < 0) {
            $bet = 0;
        }
        return $bet;
    }

    public function setBankerBet($value)
    {
        return $this->redis->redis->set('cow:banker:bet', $value);
    }


    public function incrBankerBet($value)
    {
        return $this->redis->redis->incrBy('cow:banker:bet', $value);
    }

    public function getRound()
    {
        return $bet = intval($this->redis->redis->get('cow:round'));
    }
    
    public function incrRound($value)
    {
        return $this->redis->redis->incrBy('cow:round', $value);
    }
    
    public function getAllPlayer()
    {
        //$bets = $this->redis->redis->hGetAll("cow:players:bet");
        $fds = $this->redis->redis->hGetAll('cow:players:fd');
        $rst = [];
        foreach ($fds as $player_id => $fd) {
            $rst[$player_id] = [
                'id'  => $player_id,
                'fd'  => $fd,
                'bet' => $this->getBetByPlayerId($player_id)
            ];
        }
        return $rst;
    }

    public function getFdByPlayerId($player_id)
    {
        return $this->redis->redis->hGet('cow:players:fd', $player_id);
    }

    protected function extractPositions()
    {
        $this->redis->redis->set('cow:lock:positions', time() + 5);
        $res = $this->getPositions();
        $this->redis->redis->del('cow:positions_1', 'cow:positions_2', 'cow:positions_3', 'cow:positions_4', 'cow:lock:positions');
        //  $this->redis->redis->del('cow:lock:positions');
        return $res;
    }

    public function setBankerInfo(array $value)
    {
        return $this->redis->redis->hMset('cow:banker:info', $value);
    }

    public function getBankerInfo()
    {
        $info = $this->redis->redis->hGetAll('cow:banker:info');
        $info['bet'] = $this->getBankerBet();
        return $info;
    }


    public function getLock()
    {
        // while ($this->redis->redis->get('cow:lock:positions'))
    }

    public function getState()
    {
        return $this->redis->redis->get('cow:state');
    }

    public function getStateTime()
    {
        return $this->redis->redis->get('cow:statetime');
    }

    public function setState($state)
    {
        parent::setState($state);
        $this->redis->redis->set('cow:statetime', time());
        $this->redis->redis->set('cow:state', $state);
    }

    public function linesGone($player_id)
    {
        if ($this->redis->redis->hGet('cow:players:fd', $player_id) > 0) {
            $this->printf("玩家%s掉线了", $player_id);
            $this->staticLog("sRemove", self::$static_log_redis_key["online_player"], $player_id);
            return $this->redis->redis->hSet('cow:players:fd', $player_id, -1);
        }
    }

    public function addPlayerOfflineSettlement($player_id, $settlement)
    {
        return $this->redis->redis->lPush('offline_settlement:' . $player_id, json_encode($settlement));
    }

    public function popPlayerOfflineSettlement($player_id)
    {
        $rst = [];
        $key = 'offline_settlement:' . $player_id;
        $len = $this->redis->redis->lLen($key);
        for ($i = 0; $i < $len; $i++) {
            $item = $this->redis->lpop($key);
            $rst[] = $item;
        }

        return $rst;
    }

    public function setBankerFlag($value)
    {
        return $this->redis->redis->set('cow:banker:flag', $value);
    }


    public function getBankerFlag()
    {
        return strval($this->redis->redis->get('cow:banker:flag'));
    }

    public function getIntervene()
    {
        $i = $this->redis->redis->get('cow:intervene');
        if ($i == null) {
            return -1;
        } else {
            return intval($i);
        }
    }

    public function getHallFd($player_id)
    {
        return strval($this->redis->redis->hGet('lord_user_info_' . $player_id, 'fd'));
    }

    //endregion
}


