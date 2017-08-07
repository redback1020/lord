<?php

//游戏配置
$confs = array(
	'gold2coin' => 10000,	//金币换乐豆
	'coup2coin' => 100,		//奖券换乐豆
	'gold_level1' => 100,	//金牌用户所需累计使用金币
	'lottery_cost_lottery' => 1,//抽奖耗费抽奖数
	'lottery_cost_coins' => 2000,//抽奖耗费乐豆数
	'coins_buy_5' => 1500,//购买单次道具ID5(记牌器)耗费乐豆数
	'table_buy_gd' => 12,//牌桌内购买记牌器商品id
	'user_nick_prev' => '新手',	//昵称的默认前缀
	'user_noob_coins' => 1000,	//新人乐豆
	'user_trial_count' => 1000,	//每次补助乐豆数
	'user_trial_daily' => 4000,	//每天补助乐豆数
	'rate_showcard' => 5,	//明牌倍率
	'rate_belord' => 1,		//叫地主倍率
	'rate_grablord' => 2,	//抢地主倍率
	'rate_lordcard' => 1,	//三张地主牌基础倍率[1普通2单王3双王] * $confs['rate_lordcard']
	'rate_cardstype87' => 2,//炸弹倍率
	'rate_cardstype88' => 2,//炸弹倍率
	'rate_cardstype89' => 2,//炸弹倍率
	'rate_cardstype99' => 2,//火箭倍率
	'rate_lordspring' => 2,	//春天倍率
	'rate_boorspring' => 2,	//反春倍率
	'time_invite_belord' => 4,	//时限-超时自动邀请叫地主(等待赖子动画或明牌完成)
	'time_auto_lord' => 16,		//时限-超时自动叫抢地主
	'time_auto_play' => 31,		//时限-超时自动打牌
    'time_trust_play' => 2,		//时限-托管时超时自动叫抢地主|打牌
	'time_table_total' => 1,	//时限-超时自动处理结算后的操作
    'time_match_table_total' => 4,	//时限-超时自动处理结算后的操作比赛场
	'time_enter_again' => 300,	//时限-超时方可重新进入已退房
	'time_user_alert' => 10,		//时限-登录成功n秒后发给用户弹窗消息
	'time_model_game_roomin' => 3,	//通知竞技开始后，超时n秒后自动进入房间
	'time_model_game_start' => 10,	//通知进入房间后，超时n秒后自动开始准备及发牌
	'time_model_game_goon' => 5,	//场赛结束后，超时n秒后自动开始下一局(与结算重叠)
	//'time_model_game_result'=> 15,	//场赛结束后-超时后场赛结果公布(与结算重叠)
	'time_model_game_prize' => 30,	//场赛结束后-超时后场赛发奖(与结算重叠)
	'time_clear_inbox_type0' => 30* 86400,//收件箱中的type0的邮件超过时间清理掉
	'time_clear_inbox_type1' => 7 * 86400,//收件箱中的type1的邮件超过时间清理掉
	'time_check_double_rate'=>6,   //1.8.0之后的版本增加是否双倍功能
	'time_match_before_start'=>600,//比赛模式下的定时赛开赛前的n秒内不可以进行常规牌局
    'time_auto_play_new_version' => 10903,		//时限-超时自动打牌
    'time_client'=>array(
               'lord' => 15,            //时限-超时自动叫抢地主
               'send' => 25,            //时限-超时自动打牌
               'giveup' => 15,      //时限-要不起(客户端) 
               'double' => 3.5,         //时限-双倍
    ),
    'login_got_config' =>array(//7日登陆奖励
        1=>array("coins"=>500,"coupon"=>0,"lottery"=>0,"props"=>array()),
        2=>array("coins"=>800,"coupon"=>0,"lottery"=>0,"props"=>array()),
        3=>array("coins"=>1000,"coupon"=>0,"lottery"=>0,"props"=>array()),
        4=>array("coins"=>0,"coupon"=>0,"lottery"=>0,"props"=>array(2)),
        5=>array("coins"=>1000,"coupon"=>0,"lottery"=>0,"props"=>array(7)),
        6=>array("coins"=>1000,"coupon"=>0,"lottery"=>1,"props"=>array()),
        7=>array("coins"=>1500,"coupon"=>0,"lottery"=>0,'props'=>array(1,24))
    ),
);

//条件式调整
if ( ISLOCAL ) $confs['user_noob_coins'] = 500000;

return $confs;
