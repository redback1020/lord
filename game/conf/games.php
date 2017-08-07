<?php

//游戏设置
return $GAME = array(
	'gold2coin' => 10000,	//金币换乐豆
	'coup2coin' => 100,		//奖券换乐豆
	'gold_level1' => 100,	//金牌用户所需累计使用金币
	'user_nick_prev' => '新手',	//昵称的默认前缀
	'user_noob_coins' => ISTESTS?500000:5000,	//新人乐豆
	'user_trial_count' => 1000,	//每次补助乐豆数
	'user_trial_daily' => 4000,	//每天补助乐豆数
	'rate_showcard' => 5,	//明牌倍率
	'rate_belord' => 1,		//叫地主倍率
	'rate_grablord' => 2,	//抢地主倍率
	'rate_lordcard' => 1,	//三张地主牌基础倍率[1普通2单王3双王]*$GAME['rate_lordcard']
	'rate_cardstype88' => 2,//炸弹倍率
	'rate_cardstype99' => 2,//火箭倍率
	'rate_lordspring' => 2,	//春天倍率
	'rate_boorspring' => 2,	//反春倍率
	'time_invite_belord' => 4,	//时限-超时自动邀请叫地主(等待名牌完成)
	'time_auto_lord' => 16+5,		//时限-超时自动叫抢地主 待续，需要140版本上恢复为16秒
	'time_auto_play' => 31,		//时限-超时自动打牌
	'time_trust_play' => 2,		//时限-托管时超时自动叫抢地主|打牌
	'time_table_total' => 4,	//时限-超时自动处理结算后的操作
	'time_enter_again' => 300,	//时限-超时方可重新进入已退房
	'time_user_alert' => 10,		//时限-登录成功n秒后发给用户弹窗消息
	'time_model_game_roomin' => 3,	//通知竞技开始后，超时n秒后自动进入房间
	'time_model_game_start' => 10,	//通知进入房间后，超时n秒后自动开始准备及发牌
	'time_model_game_goon' => 5,	//场赛结束后，超时n秒后自动开始下一局(与结算重叠)
	//'time_model_game_result'=> 15,	//场赛结束后-超时后场赛结果公布(与结算重叠)
	'time_model_game_prize' => 30,	//场赛结束后-超时后场赛发奖(与结算重叠)
	'time_clear_inbox_type0' => 30* 86400,//收件箱中的type0的邮件超过时间清理掉
	'time_clear_inbox_type1' => 7 * 86400,//收件箱中的type1的邮件超过时间清理掉,
    'time_check_double_rate'=>6,   //1.8.0之后的版本增加是否双倍功能
);
