<?php

//客户端请求/发送/拉取
return $requests = [
    '3' => [
        '302' => ['act' => 'GET_HORNE', 'name' => '全服广播'],
        '303' => ['act' => 'GET_ALERT', 'name' => '系统警告'],
        '305' => ['act' => 'GET_POPS', 'name' => '系统弹窗'],
        '307' => ['act' => 'GET_TIPS', 'name' => '系统提示'],
        '309' => ['act' => 'GET_MAIL', 'name' => '系统邮箱'],
        '311' => ['act' => 'SET_PROMPT', 'name' => '填空内容'],
        '313' => ['act' => 'SET_CONFIRM', 'name' => '确认结果'],
        '315' => ['act' => 'SET_TELL', 'name' => '交谈内容'],
        '317' => ['act' => 'GET_TOLD', 'name' => '交谈内容'],
    ],
    '4' => [    //辅助协议
        //基础协议
        '101' => ['act' => 'REQ_GAME_VERSI', 'name' => '版本编号'],//version/verconf/verfile/vertips待扩展
        '103' => ['act' => 'REQ_GAME_CONFS', 'name' => '游戏配置'],
        '105' => ['act' => 'REQ_GAME_FILES', 'name' => '游戏素材', 'user' => 1],
        '107' => ['act' => 'REQ_GAME_TIPS_', 'name' => '游戏提示', 'user' => 1],
        '109' => ['act' => 'GET_USER_INFOR', 'name' => '刷新用户', 'user' => 1],
        '111' => ['act' => 'GET_USER_MAILS', 'name' => '刷新邮件', 'user' => 1],
        // '113' => array('act'=>'GET_USER_AVATA',	'name'=>'用户形象',	'user'=>1),
        // '115' => array('act'=>'GET_USER_STATE',	'name'=>'道具加身',	'user'=>1),
        '119' => ['act' => 'REQ_USER_ALERT', 'name' => '用户弹窗', 'user' => 1],
        // '121' => array('act'=>'REQ_DATA_PACK_',	'name'=>'用户背包',	'user'=>1),//pack
        // '123' => array('act'=>'REQ_DATA_RECHA',	'name'=>'充值选项',	'user'=>1),//recharge
        // '125' => array('act'=>'REQ_DATA_CHARG',	'name'=>'兑换选项',	'user'=>1),//charge
        // '127' => array('act'=>'REQ_DATA_PROP_',	'name'=>'道具列表',	'user'=>1),//propertys
        // '129' => array('act'=>'REQ_DATA_TOPIC',	'name'=>'活动列表',	'user'=>1),//topic
        // '131' => array('act'=>'REQ_DATA_NOTIC',	'name'=>'公告列表',	'user'=>1),//notic
        // '133' => array('act'=>'REQ_DATA_TASK_',	'name'=>'任务列表',	'user'=>1),//task
        // '135' => array('act'=>'REQ_LIST_RICH_',	'name'=>'大户榜单'),//rich
        // '137' => array('act'=>'REQ_LIST_GOLD_',	'name'=>'土豪榜单'),//gold
        // '139' => array('act'=>'REQ_LIST_DAILY',	'name'=>'每日榜单'),//daily
        // '141' => array('act'=>'REQ_LIST_WEEKY',	'name'=>'每周榜单'),//weeky
        '163' => ['act' => 'REQ_GIFT_LOBBY', 'name' => '大厅礼包', 'user' => 1],
        '165' => ['act' => 'REQ_GIFT_MSGS_', 'name' => '礼包提示', 'user' => 1],
        '167' => ['act' => 'REQ_GIFT_POPS_', 'name' => '礼包弹窗', 'user' => 1],
        '169' => ['act' => 'REQ_GIFT_BOARD', 'name' => '礼包面板', 'user' => 1],
        //用户相关 资料 邮箱 背包 消费记录
        '201' => ['act' => 'REQ_USER_INDEX', 'name' => '资料面板', 'user' => 1],
        // '203' => array('act'=>'REQ_USER_NICK_',	'name'=>'修改昵称',	'user'=>1),
        // '205' => array('act'=>'REQ_USER_SEX__',	'name'=>'修改性别',	'user'=>1),
        '207' => ['act' => 'REQ_USER_AGE__', 'name' => '修改年龄', 'user' => 1],
        // '209' => array('act'=>'REQ_USER_WORD_',	'name'=>'修改签名',	'user'=>1),
        '211' => ['act' => 'REQ_USER_INBOX', 'name' => '邮箱面板', 'user' => 1],
        '213' => ['act' => 'REQ_MAIL_READ_', 'name' => '阅读邮件', 'user' => 1, 'locku' => 1],
        '215' => ['act' => 'REQ_MAIL_ITEM_', 'name' => '领取邮件', 'user' => 1, 'locku' => 1],
        '217' => ['act' => 'REQ_MAIL_DELET', 'name' => '删除邮件', 'user' => 1, 'locku' => 1],
        '221' => ['act' => 'REQ_USER_PACK_', 'name' => '背包面板', 'user' => 1],
        '223' => ['act' => 'REQ_PROP_MORE_', 'name' => '道具详情', 'user' => 1],
        '225' => ['act' => 'REQ_PROP_MCARD', 'name' => '领包月豆', 'user' => 1, 'locku' => 1],
        '231' => ['act' => 'REQ_USER_COST_', 'name' => '消费记录', 'user' => 1],
        '233' => ['act' => 'REQ_USER_PROMPT_INFO', 'name' => '大厅用户提示列表', 'user' => 1],
        '235' => ['act' => 'REQ_N_REWARD_INFO', 'name' => '大厅n局送乐券信息', 'user' => 1],
        // '241' => array('act'=>'REQ_USER_KEYS_',	'name'=>'按键提示'),
        //商城相关 充值 试衣 兑换 道具
        '301' => ['act' => 'REQ_MALL_COINS', 'name' => '购买乐豆', 'user' => 1],
        '303' => ['act' => 'REQ_MALL_RECOM', 'name' => '推荐商品', 'user' => 1],
        '311' => ['act' => 'REQ_MALL_GOLDS', 'name' => '充值乐币', 'user' => 1],
        '321' => ['act' => 'REQ_MALL_CONVS', 'name' => '兑换中心', 'user' => 1],
        '323' => ['act' => 'REQ_MALL_CONVT', 'name' => '执行兑换', 'user' => 1, 'locku' => 1],
        '331' => ['act' => 'REQ_MALL_GOODS', 'name' => '道具中心', 'user' => 1],
        '333' => ['act' => 'REQ_MALL_GIFTS', 'name' => '新手道具', 'user' => 1, 'locku' => 1],
        '341' => ['act' => 'REQ_MALL_FROOM', 'name' => '试 衣 间', 'user' => 1],//预留
        '343' => ['act' => 'REQ_MALL_DRESS', 'name' => '换穿衣服', 'user' => 1, 'locku' => 1],//预留
        //活动相关 活动 礼包 抽奖 公告
        '401' => ['act' => 'REQ_TOPI_INDEX', 'name' => '活动主页', 'user' => 1],
        // '403' => array('act'=>'REQ_TOPI_LOBBY',	'name'=>'热门活动',	'user'=>1),
        // '411' => array('act'=>'REQ_ACTIV_SHOW',	'name'=>'激活面板',	'user'=>1),
        // '413' => array('act'=>'REQ_ACTIVATION',	'name'=>'激活操作',	'user'=>1),
        // '421' => array('act'=>'REQ_LUCKY_SHOW',	'name'=>'抽奖面板',	'user'=>1),
        // '423' => array('act'=>'REQ_LUCKY_DRAW',	'name'=>'抽奖开始',	'user'=>1),
        '425' => ['act' => 'REQ_LUCKY_SHAKE_SHOW', 'name' => '摇摇乐面板', 'user' => 1],
        '427' => ['act' => 'REQ_LUCKY_SHAKE_DRAW', 'name' => '摇摇乐操作', 'user' => 1, 'locku' => 1],
        '431' => ['act' => 'REQ_TOPI_NOTIC', 'name' => '公告面板', 'user' => 1],
        //任务相关 任务 签到
        '501' => ['act' => 'REQ_TASK_TODAY', 'name' => '每日任务', 'user' => 1],
        '503' => ['act' => 'REQ_TASK_GROWS', 'name' => '成长任务', 'user' => 1],
        '505' => ['act' => 'REQ_TASK_TOPIC', 'name' => '活动任务', 'user' => 1],
        '509' => ['act' => 'REQ_TASK_AWARD', 'name' => '任务领奖', 'user' => 1, 'locku' => 1],
        // '521' => array('act'=>'REQ_LOGIN_DAY0',	'name'=>'签到面板',	'user'=>1),
        // '523' => array('act'=>'REQ_LOGIN_SIGN',	'name'=>'签到操作',	'user'=>1,	'locku'=>1),
        //榜单相关 恶霸榜 富翁榜 每日榜 每周榜
        // '601' => array('act'=>'REQ_LIST_INDEX',	'name'=>'榜单主页'),
        // '603' => array('act'=>'REQ_EVIL_ROBIT',	'name'=>'明抢恶霸'),
        // '611' => array('act'=>'REQ_LIST_GOLD_',	'name'=>'富翁榜单'),
        '621' => ['act' => 'REQ_LIST_DAILY', 'name' => '每日榜单', 'user' => 1],
        '631' => ['act' => 'REQ_LIST_WEEKY', 'name' => '每周榜单', 'user' => 1],
        //帮助相关协议
        '801' => ['act' => 'REQ_HELP_INDEX', 'name' => '帮助面板'],

        '701' => ['act' => 'REQ_IN_HALL', 'name' => '进入大厅', 'user' => 1],


        //监控相关协议
        '901' => ['act' => 'REQ_GOTO_LOBBY', 'name' => '进入大厅', 'user' => 1],
    ],
    '5' => [    //游戏协议
        //牌桌协议
        '0'   => ['act' => 'USER_INTO_ROOM', 'name' => '用户进房', 'user' => 1, 'locku' => 1, 'lock' => 'tableId'],
        '1'   => ['act' => 'USER_GET_READY', 'name' => '用户再局', 'user' => 1, 'locku' => 1, 'lock' => 'tableId'],
        '2'   => ['act' => 'USER_JOIN_TRIO', 'name' => '用户凑桌', 'user' => 1, 'locku' => 1, 'lock' => 'tableId'],
        // '3' => '???',			//闲置
        // '4' => 'GAME_SHUFFLE ',	//发牌
        '5'   => ['act' => 'USER_CALL_LORD', 'name' => '用户叫庄', 'user' => 1, 'locku' => 1, 'lock' => 'tableId'],
        '6'   => ['act' => 'USER_GRAB_LORD', 'name' => '用户抢庄', 'user' => 1, 'locku' => 1, 'lock' => 'tableId'],
        '7'   => ['act' => 'USER_SHOW_CARD', 'name' => '用户明牌', 'user' => 1, 'locku' => 1, 'lock' => 'tableId'],
        '8'   => ['act' => 'USER_SEND_CARD', 'name' => '用户出牌', 'user' => 1, 'locku' => 1, 'lock' => 'tableId'],
        // '9' => 'GAME_FOLLOW',	//跟牌
        '10'  => ['act' => 'USER_NO_FOLLOW', 'name' => '用户不跟', 'user' => 1, 'locku' => 1, 'lock' => 'tableId'],
        // '11'=> 'GAME_COUNT_OVER',//结完
        // '12'=> 'USER_AFK',		//暂离
        '13'  => ['act' => 'USER_EXIT_ROOM', 'name' => '用户退房', 'user' => 1, 'locku' => 1, 'lock' => 'tableId'],
        '14'  => ['act' => 'USER_NEW_TABLE', 'name' => '用户换桌', 'user' => 1, 'locku' => 1, 'lock' => 'tableId'],
        '15'  => ['act' => 'USER_SET_TRUST', 'name' => '用户托管', 'user' => 1],
        '16'  => ['act' => 'USER_OUT_TRUST', 'name' => '用户解托', 'user' => 1],
        '17'  => ['act' => 'USER_DO_DOUBLE', 'name' => '用户加倍', 'user' => 1, 'locku' => 1, 'lock' => 'tableId'],
        '31'  => ['act' => 'USER_SAY_TABLE', 'name' => '用户发言', 'user' => 1],
        //竞技协议
        '101' => ['act' => 'REQ_MODEL_LOOKUP', 'name' => '竞技面板', 'user' => 1],
        '103' => ['act' => 'REQ_MODEL_ENROLL', 'name' => '竞技报名', 'user' => 1, 'locku' => 1],
        '105' => ['act' => 'REQ_MODEL_CANCEL', 'name' => '取消报名', 'user' => 1, 'locku' => 1],
        '109' => ['act' => 'REQ_MODEL_GIVEUP', 'name' => '放弃竞技', 'user' => 1, 'locku' => 1, 'lock' => 'tableId'],
        '111' => ['act' => 'REQ_MODEL_READY', 'name' => '竞技入场', 'user' => 1, 'locku' => 1],
        '127' => ['act' => 'REQ_MODEL_LOOK', 'name' => '刷新竞技', 'user' => 1, 'locku' => 1],
        //用户协议
        '20'  => ['act' => 'USER_EDIT_NICK', 'name' => '用户改名', 'user' => 1],
        '21'  => ['act' => 'USER_EDIT_SEX_', 'name' => '用户改性', 'user' => 1],
        '22'  => ['act' => 'USER_EDIT_WORD', 'name' => '用户改签', 'user' => 1],
        '23'  => ['act' => 'USER_GET_INFOR', 'name' => '用户刷新', 'user' => 1],
        '41'  => ['act' => 'USER_GOLD_COIN', 'name' => '金币兑豆', 'user' => 1, 'locku' => 1],
        '42'  => ['act' => 'USER_COUP_COIN', 'name' => '奖券兑豆', 'user' => 1, 'locku' => 1],
        '123' => ['act' => 'REQ_PROP_DRESS', 'name' => '换穿服装', 'user' => 1],
        '125' => ['act' => 'REQ_PROP_BUYIT', 'name' => '购买服装', 'user' => 1, 'locku' => 1],
        '131' => ['act' => 'REQ_LUCKY_SHOW', 'name' => '抽奖面板', 'user' => 1],
        '133' => ['act' => 'REQ_LUCKY_DRAW', 'name' => '抽奖操作', 'user' => 1, 'locku' => 1],
        '135' => ['act' => 'REQ_LUCKY_HIST', 'name' => '抽奖记录', 'user' => 1],
        '137' => ['act' => 'REQ_LOGIN_DAY0', 'name' => '签到面板', 'user' => 1],
        '139' => ['act' => 'REQ_LOGIN_SIGN', 'name' => '签到操作', 'user' => 1, 'locku' => 1],
        '141' => ['act' => 'REQ_LIST_TODAY', 'name' => '今日榜单', 'user' => 1],
        '143' => ['act' => 'REQ_LIST_TWEEK', 'name' => '本周榜单', 'user' => 1],
        '145' => ['act' => 'REQ_LIST_LWEEK', 'name' => '上周榜单', 'user' => 1],
        '147' => ['act' => 'REQ_BOARD_SHOW', 'name' => '公告面板', 'user' => 1],
        '149' => ['act' => 'REQ_ACTIVATION', 'name' => '激活礼包', 'user' => 1, 'locku' => 1],//
        '151' => ['act' => 'REQ_TRIAL_SHOW', 'name' => '救济面板', 'user' => 1, 'locku' => 1],//50152
        '153' => ['act' => 'REQ_TRIAL_LAPA', 'name' => '拉霸面板', 'user' => 1, 'locku' => 1],//50154 已并入50152协议 //预留
        '155' => ['act' => 'REQ_TRIAL_EXEC', 'name' => '拉霸操作', 'user' => 1, 'locku' => 1],//50156,40110,
        '157' => ['act' => 'REQ_NEW_LUCKY_SHOW', 'name' => '新版抽奖面板', 'user' => 1],     //v180
        '159' => ['act' => 'REQ_NEW_LUCKY_DRAW', 'name' => '新版抽奖', 'user' => 1, 'locku' => 1],     //v180
        '161' => ['act' => 'REQ_EXIT_SHOW', 'name' => '挽留弹框', 'user' => 1],     //v180
        '201' => ['act' => 'MOD3_ALL_ROOMS', 'name' => '新版赛场', 'user' => 1],//v181
        '203' => ['act' => 'MOD3_OPEN_ROOM', 'name' => '查看房间', 'user' => 1],//v181
        '205' => ['act' => 'MOD3_VIEW_ROOM', 'name' => '刷新房间', 'user' => 1],//v181
        '207' => ['act' => 'MOD3_SET_ENTRY', 'name' => '报名参赛', 'user' => 1, 'locku' => 1],//v181
        '209' => ['act' => 'MOD3_OUT_ENTRY', 'name' => '取消参赛', 'user' => 1, 'locku' => 1],//v181
        '217' => ['act' => 'MOD3_GET_AWARD', 'name' => '领奖面板', 'user' => 1],//v181
        '219' => ['act' => 'MOD3_SET_AWARD', 'name' => '处理领奖', 'user' => 1],//v181
        '221' => ['act' => 'MOD3_DO_GIVEUP', 'name' => '放弃比赛', 'user' => 1, 'locku' => 1, 'lock' => 'tableId'],//v190
        '223' => ['act' => 'CHANNEL_VIP', 'name' => '渠道会员特权', 'user' => 1, 'locku' => 1],
        '225' => ['act' => 'REQ_ROUNDS_INFO', 'name' => '获取局数信息', 'user' => 1, 'locku' => 1],
        '227' => ['act' => 'REQ_LOGIN_SIGN', 'name' => '7日签到操作', 'user' => 1, 'locku' => 1],
        '229' => ['act' => 'USER_TABLE_RECONNECT', 'name' => '牌桌重连', 'user' => 1, 'locku' => 1],//v193

        '999' => ['act' => 'REQ_CLIENTTEST', 'name' => '客端测试'],//50998,50996,50994,
    ],
    '6' => [    //道具协议
        '101' => ['act' => 'USE_IN_TABLE_A', 'name' => '使用私有', 'user' => 1, 'locku' => 1],
        '103' => ['act' => 'USE_IN_TABLE_B', 'name' => '使用公共', 'user' => 1, 'locku' => 1],
        '105' => ['act' => 'USE_IN_TABLE_C', 'name' => '使用争抢', 'user' => 1, 'locku' => 1],
        '107' => ['act' => 'USE_IN_TABLE_D', 'name' => '争抢道具', 'user' => 1, 'locku' => 1],
        '109' => ['act' => 'USE_IN_TABLE_E', 'name' => '开关道具', 'user' => 1, 'locku' => 1],
        '111' => ['act' => 'COINS_BUY_PROP', 'name' => '豆买道具', 'user' => 1, 'locku' => 1],
        '201' => ['act' => 'USE_IN_PACK', 'name' => '使用背包', 'user' => 1, 'locku' => 1],
    ],
    '7' => [ //水果机协议
        '1' => ['act' => 'REQ_FRUIT_ENTER', 'name' => '水果机进入', 'user' => 1, 'locku' => 1],
        '2' => ['act' => 'REQ_FRUIT_RUN', 'name' => '水果机下分', 'user' => 1, 'locku' => 1],
        '3' => ['act' => 'REQ_FRUIT_MERGE', 'name' => '水果机合分', 'user' => 1, 'locku' => 1],
        '4' => ['act' => 'REQ_FRUIT_EXIT', 'name' => '水果机退出', 'user' => 1, 'locku' => 1],
        '5' => ['act' => 'REQ_FRUIT_ADD', 'name' => '水果机加分', 'user' => 1, 'locku' => 1],
        '6' => ['act' => 'REQ_FRUIT_CHECK', 'name' => '水果机验证(已经报名人满开赛的不允许进场)', 'user' => 1, 'locku' => 1],
    ],
    '8' => [//百人牛牛协议
        '1'  => ['act' => 'REQ_COW_ENTER', 'name' => '入场', 'user' => 1, 'locku' => 1], //request[ gold:金币]  response[gold:金币 bet:注 config:配置]
        '2'  => ['act' => 'REQ_COW_EXIT', 'name' => '离场', 'user' => 1, 'locku' => 1],//request[] response[gold:金币 bet:注]
        '3'  => ['act' => 'REQ_COW_WAGER', 'name' => '下注', 'user' => 1, 'locku' => 1],//request[position:位置 num:要下得金额] response[ position ,num,  investment:我在这个位置累计下的注数 ]
        '4'  => ['act' => 'REQ_COW_APPLY', 'name' => '申请上庄', 'user' => 1, 'locku' => 1],//request[num:携带的注]
        '5'  => ['act' => 'REQ_COW_QUIT', 'name' => '申请下庄', 'user' => 1, 'locku' => 1],//request[]
        '6'  => ['act' => 'REQ_COW_CANDIDATE', 'name' => '获取庄家候选人列表', 'user' => 1, 'locku' => 1],//request[] response['condidates'=>['name':'昵称','bet':'携带的注']]
        '7'  => ['act' => 'REQ_COW_POSITION_HISTORY', 'name' => '查看位置的历史记录', 'user' => 1, 'locku' => 1],//request[] response['histories'=>[1=>[true,false],2=>[true,false] ...]]
        '8'  => ['act' => 'REQ_COW_ADD', 'name' => '加注', 'user' => 1, 'locku' => 1],//request['gold'] response['gold':0 'bet':0]
        '10' => ['act' => 'REQ_COW_RECONNECT', 'name' => '断线重连', 'user' => 1, 'locku' => 1],//request['gold'] response['gold':0 'bet':0]
        '11' => ['act' => 'REQ_COW_CANDIDATE_QUIT', 'name' => '候选人下庄', 'user' => 1, 'locku' => 1],//request['gold'] response['gold':0 'bet':0]

        //'101'=>广播发牌
        //'102'=>广播有人下注
        //'103'=>广播换庄
        //'104'=>结算信息
        //'105'=>牌桌赌注变化 deposit
        //'106'=>通知被强制下庄
        //'107'=>新一轮开始
        //'108'=>状态变化∂
        //'109'=>大厅上下庄

    ],
    '10' => [//打点协议
        '1'  => ['act' => 'REQ_CLIENT_SIGN_RECORD', 'name' => '客户端打点', 'user' => 0, 'locku' => 0],
    ],
    '16' => [//打点协议
        '1'  => ['act' => 'REQ_CLIENT_SIGN_RECORD', 'name' => '客户端打点', 'user' => 0, 'locku' => 0],
    ],
    
];
//51015 进入房间成功 是否需要返桌
//51027 进房乐豆限制
//51001	开始凑桌成功 客户端挑担子
//51003 我已准备成功 客户端出茶碗
//51004	牌桌正式开始 客户端人坐好
//51019 牌桌有人明牌 客户端发明牌
//51005 牌桌发牌数据 客户端三家牌
//51008 轮到某人叫庄
//51006 有人叫或不叫
//51011 轮到某人抢庄
//51016 有人抢或不抢
//51007 牌桌地主敲定
//51002 牌桌确定赖子
//51012	没有轮到出牌
//51009 轮到某人打牌
//51013 打牌比上家小
//51020 打牌出牌错误
//51017 打牌出牌成功
//51021 牌桌倍率变更
//51022 牌局任务触发
//51018 打牌不跟成功
//51010 牌桌历史发完
//51014 牌桌结算数据
//51025 牌桌解散完毕
//51023 空
//51024 用户需要补豆
//51026 空
//51028 有人开始托管
//51029 有人解除托管
//51030 往后没有
//53000 牌桌发言
//50430 牌桌同步乐豆