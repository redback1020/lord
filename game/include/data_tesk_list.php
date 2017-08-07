<?php $data_tesk_list = [
    10163 =>
        [
            'id'          => 10163,
            'type'        => 0,
            'name'        => '完成10局牌局',
            'prev'        => 0,
            'goto'        => 1,
            'rooms'       =>
                [
                ],
            'channels'    =>
                [
                ],
            'users'       =>
                [
                ],
            'start_time'  => 0,
            'end_time'    => 0,
            'periodName'  => '每天',
            'periodTime'  => 86400,
            'periodId'    => 0,
            'periodStart' => 1446134400,
            'periodEnd'   => 1446220799,
            'sourceId'    => 179,
            'accode'      => 0,
            'action'      => 'GAME_OVER',
            'acttag'      => 'game_over',
            'execut'      =>
                [
                    0 =>
                        [
                            'key' => 'usertesk.teskvalue',
                            'exe' => '+',
                            'par' => 1,
                        ],
                ],
            'condit'      =>
                [
                    0 =>
                        [
                            'key' => 'usertesk.teskvalue',
                            'leg' => '>=',
                            'par' => 10,
                        ],
                ],
            'result'      =>
                [
                    0 =>
                        [
                            'key' => 'usertesk.teskdone',
                        ],
                ],
            'target'      => 10,
            'prizeName'   => '乐豆1000',
            'prizes'      =>
                [
                    'coins' => 1000,
                ],
            'mailSubject' => '',
            'mailContent' => '',
            'mailFileid'  => 0,
            'is_surprise' => 0,
            'is_online'   => 0,
            'is_del'      => 0,
            'sort'        => 11,
            'create_time' => 1445399069,
            'update_time' => 1451296773,
        ],
    10165 =>
        [
            'id'          => 10165,
            'type'        => 0,
            'name'        => '取得3局连胜',
            'prev'        => 0,
            'goto'        => 1,
            'rooms'       =>
                [
                ],
            'channels'    =>
                [
                ],
            'users'       =>
                [
                ],
            'start_time'  => 0,
            'end_time'    => 0,
            'periodName'  => '每天',
            'periodTime'  => 86400,
            'periodId'    => 0,
            'periodStart' => 1446134400,
            'periodEnd'   => 1446220799,
            'sourceId'    => 77,
            'accode'      => 0,
            'action'      => 'GAME_OVER',
            'acttag'      => 'game_over',
            'execut'      =>
                [
                    0 =>
                        [
                            'key' => 'usertesk.teskvalue',
                            'exe' => '*p+p',
                            'par' => 'param',
                        ],
                ],
            'condit'      =>
                [
                    0 =>
                        [
                            'key' => 'usertesk.teskvalue',
                            'leg' => '>=',
                            'par' => 3,
                        ],
                ],
            'result'      =>
                [
                    0 =>
                        [
                            'key' => 'usertesk.teskdone',
                        ],
                ],
            'target'      => 3,
            'prizeName'   => '乐豆1000，乐券10',
            'prizes'      =>
                [
                    'coins'  => 1000,
                    'coupon' => 10,
                ],
            'mailSubject' => '',
            'mailContent' => '',
            'mailFileid'  => 0,
            'is_surprise' => 0,
            'is_online'   => 1,
            'is_del'      => 0,
            'sort'        => 2,
            'create_time' => 1445399216,
            'update_time' => 1469680252,
        ],
    10166 =>
        [
            'id'          => 10166,
            'type'        => 0,
            'name'        => '累计打出5个炸弹',
            'prev'        => 0,
            'goto'        => 1,
            'rooms'       =>
                [
                ],
            'channels'    =>
                [
                ],
            'users'       =>
                [
                ],
            'start_time'  => 0,
            'end_time'    => 0,
            'periodName'  => '每天',
            'periodTime'  => 86400,
            'periodId'    => 0,
            'periodStart' => 1446134400,
            'periodEnd'   => 1446220799,
            'sourceId'    => 182,
            'accode'      => 0,
            'action'      => 'user_send_card',
            'acttag'      => 'user_pct_88',
            'execut'      =>
                [
                    0 =>
                        [
                            'key' => 'usertesk.teskvalue',
                            'exe' => '+',
                            'par' => 'param',
                        ],
                ],
            'condit'      =>
                [
                    0 =>
                        [
                            'key' => 'usertesk.teskvalue',
                            'leg' => '>=',
                            'par' => 5,
                        ],
                ],
            'result'      =>
                [
                    0 =>
                        [
                            'key' => 'usertesk.teskdone',
                        ],
                ],
            'target'      => 5,
            'prizeName'   => '乐豆1000',
            'prizes'      =>
                [
                    'coins' => 1000,
                ],
            'mailSubject' => '',
            'mailContent' => '',
            'mailFileid'  => 0,
            'is_surprise' => 0,
            'is_online'   => 1,
            'is_del'      => 0,
            'sort'        => 2,
            'create_time' => 1445399255,
            'update_time' => 1467890471,
        ],
    10167 =>
        [
            'id'          => 10167,
            'type'        => 0,
            'name'        => '商城进行一次消费',
            'prev'        => 0,
            'goto'        => 3,
            'rooms'       =>
                [
                ],
            'channels'    =>
                [
                ],
            'users'       =>
                [
                ],
            'start_time'  => 0,
            'end_time'    => 0,
            'periodName'  => '每天',
            'periodTime'  => 86400,
            'periodId'    => 0,
            'periodStart' => 1445529600,
            'periodEnd'   => 1445615999,
            'sourceId'    => 183,
            'accode'      => 0,
            'action'      => 'API_GOLD',
            'acttag'      => 'cost_gold',
            'execut'      =>
                [
                    0 =>
                        [
                            'key' => 'usertesk.teskvalue',
                            'exe' => '+',
                            'par' => 1,
                        ],
                ],
            'condit'      =>
                [
                    0 =>
                        [
                            'key' => 'usertesk.teskvalue',
                            'leg' => '>=',
                            'par' => 1,
                        ],
                ],
            'result'      =>
                [
                    0 =>
                        [
                            'key' => 'usertesk.teskdone',
                        ],
                ],
            'target'      => 1,
            'prizeName'   => '乐豆10000，乐券50',
            'prizes'      =>
                [
                    'coins'  => 10000,
                    'coupon' => 50,
                ],
            'mailSubject' => '',
            'mailContent' => '',
            'mailFileid'  => 0,
            'is_surprise' => 0,
            'is_online'   => 1,
            'is_del'      => 0,
            'sort'        => 2,
            'create_time' => 1445399295,
            'update_time' => 1451296911,
        ],
    10200 =>
        [
            'id'          => 10200,
            'type'        => 2,
            'name'        => '购买新手礼包',
            'prev'        => 0,
            'goto'        => 0,
            'rooms'       =>
                [
                ],
            'channels'    =>
                [
                ],
            'users'       =>
                [
                ],
            'start_time'  => 0,
            'end_time'    => 0,
            'periodName'  => '',
            'periodTime'  => 0,
            'periodId'    => 0,
            'periodStart' => 0,
            'periodEnd'   => 0,
            'sourceId'    => 216,
            'accode'      => 0,
            'action'      => 'API_GOLD',
            'acttag'      => 'buy_goods',
            'execut'      =>
                [
                    0 =>
                        [
                            'key' => 'usertesk.teskvalue',
                            'exe' => '=',
                            'par' => 'param',
                        ],
                ],
            'condit'      =>
                [
                    0 =>
                        [
                            'key' => 'usertesk.teskvalue',
                            'leg' => '=',
                            'par' => '9',
                        ],
                ],
            'result'      =>
                [
                    0 =>
                        [
                            'key' => 'usertesk.teskdone',
                        ],
                ],
            'target'      => 1,
            'prizeName'   => '1000乐豆',
            'prizes'      =>
                [
                    'coins' => 1000,
                ],
            'mailSubject' => '',
            'mailContent' => '',
            'mailFileid'  => 0,
            'is_surprise' => 0,
            'is_online'   => 1,
            'is_del'      => 0,
            'sort'        => 1,
            'create_time' => 1448879657,
            'update_time' => 1450736025,
        ],
    10164 =>
        [
            'id'          => 10164,
            'type'        => 0,
            'name'        => '取得10局胜利',
            'prev'        => 0,
            'goto'        => 1,
            'rooms'       =>
                [
                ],
            'channels'    =>
                [
                ],
            'users'       =>
                [
                ],
            'start_time'  => 0,
            'end_time'    => 0,
            'periodName'  => '每天',
            'periodTime'  => 86400,
            'periodId'    => 0,
            'periodStart' => 1446134400,
            'periodEnd'   => 1446220799,
            'sourceId'    => 180,
            'accode'      => 0,
            'action'      => 'GAME_OVER',
            'acttag'      => 'game_over',
            'execut'      =>
                [
                    0 =>
                        [
                            'key' => 'usertesk.teskvalue',
                            'exe' => '+',
                            'par' => 'param',
                        ],
                ],
            'condit'      =>
                [
                    0 =>
                        [
                            'key' => 'usertesk.teskvalue',
                            'leg' => '>=',
                            'par' => 10,
                        ],
                ],
            'result'      =>
                [
                    0 =>
                        [
                            'key' => 'usertesk.teskdone',
                        ],
                ],
            'target'      => 10,
            'prizeName'   => '乐券20',
            'prizes'      =>
                [
                    'coupon' => 20,
                ],
            'mailSubject' => '',
            'mailContent' => '',
            'mailFileid'  => 0,
            'is_surprise' => 0,
            'is_online'   => 1,
            'is_del'      => 0,
            'sort'        => 2,
            'create_time' => 1445399175,
            'update_time' => 1451296877,
        ],
    10168 =>
        [
            'id'          => 10168,
            'type'        => 0,
            'name'        => '抢地主10次',
            'prev'        => 0,
            'goto'        => 1,
            'rooms'       =>
                [
                ],
            'channels'    =>
                [
                ],
            'users'       =>
                [
                ],
            'start_time'  => 0,
            'end_time'    => 0,
            'periodName'  => '每天',
            'periodTime'  => 86400,
            'periodId'    => 0,
            'periodStart' => 1446134400,
            'periodEnd'   => 1446220799,
            'sourceId'    => 184,
            'accode'      => 0,
            'action'      => 'user_grab_lord',
            'acttag'      => 'user_grab_lord',
            'execut'      =>
                [
                    0 =>
                        [
                            'key' => 'usertesk.teskvalue',
                            'exe' => '+',
                            'par' => 1,
                        ],
                ],
            'condit'      =>
                [
                    0 =>
                        [
                            'key' => 'usertesk.teskvalue',
                            'leg' => '>=',
                            'par' => 10,
                        ],
                ],
            'result'      =>
                [
                    0 =>
                        [
                            'key' => 'usertesk.teskdone',
                        ],
                ],
            'target'      => 10,
            'prizeName'   => '乐豆1000',
            'prizes'      =>
                [
                    'coins' => 1000,
                ],
            'mailSubject' => '',
            'mailContent' => '',
            'mailFileid'  => 0,
            'is_surprise' => 0,
            'is_online'   => 1,
            'is_del'      => 0,
            'sort'        => 2,
            'create_time' => 1445399377,
            'update_time' => 1451302378,
        ],
    10169 =>
        [
            'id'          => 10169,
            'type'        => 0,
            'name'        => '成为地主1次',
            'prev'        => 0,
            'goto'        => 1,
            'rooms'       =>
                [
                ],
            'channels'    =>
                [
                ],
            'users'       =>
                [
                ],
            'start_time'  => 0,
            'end_time'    => 0,
            'periodName'  => '每日',
            'periodTime'  => 86400,
            'periodId'    => 0,
            'periodStart' => 1444406400,
            'periodEnd'   => 1444492799,
            'sourceId'    => 185,
            'accode'      => 0,
            'action'      => 'GAME_LORD_DONE',
            'acttag'      => 'be_lord',
            'execut'      =>
                [
                    0 =>
                        [
                            'key' => 'usertesk.teskvalue',
                            'exe' => '+',
                            'par' => 'param',
                        ],
                ],
            'condit'      =>
                [
                    0 =>
                        [
                            'key' => 'usertesk.teskvalue',
                            'leg' => '>=',
                            'par' => 1,
                        ],
                ],
            'result'      =>
                [
                    0 =>
                        [
                            'key' => 'usertesk.teskdone',
                        ],
                ],
            'target'      => 1,
            'prizeName'   => '乐券20',
            'prizes'      =>
                [
                    'coupon' => 20,
                ],
            'mailSubject' => '',
            'mailContent' => '',
            'mailFileid'  => 0,
            'is_surprise' => 0,
            'is_online'   => 1,
            'is_del'      => 0,
            'sort'        => 2,
            'create_time' => 1445405897,
            'update_time' => 1451302509,
        ],
    10092 =>
        [
            'id'          => 10092,
            'type'        => 2,
            'name'        => '风行新手礼包',
            'prev'        => 0,
            'goto'        => 0,
            'rooms'       =>
                [
                ],
            'channels'    =>
                [
                    0 => 'fengxing',
                ],
            'users'       =>
                [
                ],
            'start_time'  => 1450368000,
            'end_time'    => 1467302400,
            'periodName'  => '',
            'periodTime'  => 0,
            'periodId'    => 0,
            'periodStart' => 0,
            'periodEnd'   => 0,
            'sourceId'    => 70,
            'accode'      => 0,
            'action'      => 'LOGIN_GUEST',
            'acttag'      => 'user_login',
            'execut'      =>
                [
                    0 =>
                        [
                            'key' => 'usertesk.teskvalue',
                            'exe' => '+',
                            'par' => 1,
                        ],
                ],
            'condit'      =>
                [
                    0 =>
                        [
                            'key' => 'usertesk.teskvalue',
                            'leg' => '>',
                            'par' => 0,
                        ],
                ],
            'result'      =>
                [
                    0 =>
                        [
                            'key' => 'usertesk.teskdone',
                        ],
                ],
            'target'      => 1,
            'prizeName'   => '风行新手礼包',
            'prizes'      =>
                [
                    'coins'     => 600000,
                    'propItems' =>
                        [
                            14 =>
                                [
                                    'id'   => 14,
                                    'name' => '富翁套装(7天)',
                                    'cd'   => '1',
                                    'num'  => 1,
                                    'ext'  => 0,
                                ],
                        ],
                ],
            'mailSubject' => '风行新手礼包',
            'mailContent' => '恭喜您获得风行新手礼包一份
内含：
600000乐豆
富翁套装(7天)',
            'mailFileid'  => 0,
            'is_surprise' => 0,
            'is_online'   => 1,
            'is_del'      => 0,
            'sort'        => 1,
            'create_time' => 1450420857,
            'update_time' => 1466583347,
        ],
    10209 =>
        [
            'id'          => 10209,
            'type'        => 2,
            'name'        => '充值送好礼，首次消费6-10乐币',
            'prev'        => 0,
            'goto'        => 3,
            'rooms'       =>
                [
                ],
            'channels'    =>
                [
                ],
            'users'       =>
                [
                ],
            'start_time'  => 1452873600,
            'end_time'    => 1453392000,
            'periodName'  => '',
            'periodTime'  => 0,
            'periodId'    => 0,
            'periodStart' => 0,
            'periodEnd'   => 0,
            'sourceId'    => 218,
            'accode'      => 0,
            'action'      => 'API_GOLD',
            'acttag'      => 'cost_gold',
            'execut'      =>
                [
                    0 =>
                        [
                            'key' => 'usertesk.teskvalue',
                            'exe' => '=',
                            'par' => 'param',
                        ],
                ],
            'condit'      =>
                [
                    0 =>
                        [
                            'key' => 'usertesk.teskvalue',
                            'leg' => '>',
                            'par' => 5,
                        ],
                    1 =>
                        [
                            'key' => 'usertesk.teskvalue',
                            'leg' => '<',
                            'par' => 11,
                        ],
                ],
            'result'      =>
                [
                    0 =>
                        [
                            'key' => 'usertesk.teskdone',
                        ],
                ],
            'target'      => 6,
            'prizeName'   => '3万乐豆＋1天记牌器',
            'prizes'      =>
                [
                    'coins'     => 30000,
                    'propItems' =>
                        [
                            7 =>
                                [
                                    'id'   => 7,
                                    'name' => '记牌器(1天)',
                                    'cd'   => '2',
                                    'num'  => 1,
                                    'ext'  => 0,
                                ],
                        ],
                ],
            'mailSubject' => '充值送好礼，首次消费6-10乐币',
            'mailContent' => '恭喜您参与活动：
充值送好礼，首次消费6-10乐币
获得3万乐豆＋1天记牌器',
            'mailFileid'  => 0,
            'is_surprise' => 0,
            'is_online'   => 0,
            'is_del'      => 0,
            'sort'        => 11,
            'create_time' => 1452860283,
            'update_time' => 1452862512,
        ],
    10210 =>
        [
            'id'          => 10210,
            'type'        => 2,
            'name'        => '充值送好礼，首次消费30乐币',
            'prev'        => 0,
            'goto'        => 3,
            'rooms'       =>
                [
                ],
            'channels'    =>
                [
                ],
            'users'       =>
                [
                ],
            'start_time'  => 1452873600,
            'end_time'    => 1453392000,
            'periodName'  => '',
            'periodTime'  => 0,
            'periodId'    => 0,
            'periodStart' => 0,
            'periodEnd'   => 0,
            'sourceId'    => 107,
            'accode'      => 0,
            'action'      => 'API_GOLD',
            'acttag'      => 'cost_gold',
            'execut'      =>
                [
                    0 =>
                        [
                            'key' => 'usertesk.teskvalue',
                            'exe' => '=',
                            'par' => 'param',
                        ],
                ],
            'condit'      =>
                [
                    0 =>
                        [
                            'key' => 'usertesk.teskvalue',
                            'leg' => '=',
                            'par' => 30,
                        ],
                ],
            'result'      =>
                [
                    0 =>
                        [
                            'key' => 'usertesk.teskdone',
                        ],
                ],
            'target'      => 30,
            'prizeName'   => '10万乐豆＋3天记牌器',
            'prizes'      =>
                [
                    'coins'     => 100000,
                    'propItems' =>
                        [
                            8 =>
                                [
                                    'id'   => 8,
                                    'name' => '记牌器(3天)',
                                    'cd'   => '2',
                                    'num'  => 1,
                                    'ext'  => 0,
                                ],
                        ],
                ],
            'mailSubject' => '充值送好礼，首次消费30乐币',
            'mailContent' => '恭喜您参与活动：
充值送好礼，首次消费30乐币
获得10万乐豆＋3天记牌器',
            'mailFileid'  => 0,
            'is_surprise' => 0,
            'is_online'   => 0,
            'is_del'      => 0,
            'sort'        => 11,
            'create_time' => 1452860562,
            'update_time' => 1452862617,
        ],
    10213 =>
        [
            'id'          => 10213,
            'type'        => 2,
            'name'        => '充值送好礼，累计消费100乐币',
            'prev'        => 0,
            'goto'        => 3,
            'rooms'       =>
                [
                ],
            'channels'    =>
                [
                ],
            'users'       =>
                [
                ],
            'start_time'  => 1452873600,
            'end_time'    => 1453392000,
            'periodName'  => '',
            'periodTime'  => 0,
            'periodId'    => 0,
            'periodStart' => 0,
            'periodEnd'   => 0,
            'sourceId'    => 142,
            'accode'      => 0,
            'action'      => 'API_GOLD',
            'acttag'      => 'cost_gold',
            'execut'      =>
                [
                    0 =>
                        [
                            'key' => 'usertesk.teskvalue',
                            'exe' => '+',
                            'par' => 'param',
                        ],
                ],
            'condit'      =>
                [
                    0 =>
                        [
                            'key' => 'usertesk.teskvalue',
                            'leg' => '>=',
                            'par' => 100,
                        ],
                ],
            'result'      =>
                [
                    0 =>
                        [
                            'key' => 'usertesk.teskdone',
                        ],
                ],
            'target'      => 100,
            'prizeName'   => '北通充电宝＋5000乐券',
            'prizes'      =>
                [
                    'coupon' => 5000,
                ],
            'mailSubject' => '充值送好礼，累计消费100乐币',
            'mailContent' => '恭喜您参与活动：
充值送好礼，累计消费100乐币
获得北通充电宝＋5000乐券
获得实物奖励的用户，请加官方客服QQ
4000085665领取奖励',
            'mailFileid'  => 0,
            'is_surprise' => 0,
            'is_online'   => 0,
            'is_del'      => 0,
            'sort'        => 11,
            'create_time' => 1452861648,
            'update_time' => 1452861648,
        ],
    10214 =>
        [
            'id'          => 10214,
            'type'        => 2,
            'name'        => '充值送好礼，累计消费300乐币',
            'prev'        => 0,
            'goto'        => 3,
            'rooms'       =>
                [
                ],
            'channels'    =>
                [
                ],
            'users'       =>
                [
                ],
            'start_time'  => 1452873600,
            'end_time'    => 1453392000,
            'periodName'  => '',
            'periodTime'  => 0,
            'periodId'    => 0,
            'periodStart' => 0,
            'periodEnd'   => 0,
            'sourceId'    => 113,
            'accode'      => 0,
            'action'      => 'API_GOLD',
            'acttag'      => 'cost_gold',
            'execut'      =>
                [
                    0 =>
                        [
                            'key' => 'usertesk.teskvalue',
                            'exe' => '+',
                            'par' => 'param',
                        ],
                ],
            'condit'      =>
                [
                    0 =>
                        [
                            'key' => 'usertesk.teskvalue',
                            'leg' => '>=',
                            'par' => 300,
                        ],
                ],
            'result'      =>
                [
                    0 =>
                        [
                            'key' => 'usertesk.teskdone',
                        ],
                ],
            'target'      => 300,
            'prizeName'   => '北通设备2件＋15000乐券',
            'prizes'      =>
                [
                    'coupon' => 15000,
                ],
            'mailSubject' => '充值送好礼，累计消费300乐币',
            'mailContent' => '恭喜您参与活动：
充值送好礼，累计消费300乐币
获得北通充电宝＋北通蓝牙耳机＋15000乐券
获得实物奖励的用户，请加官方客服QQ
4000085665领取奖励',
            'mailFileid'  => 0,
            'is_surprise' => 0,
            'is_online'   => 0,
            'is_del'      => 0,
            'sort'        => 11,
            'create_time' => 1452861704,
            'update_time' => 1452861704,
        ],
    10215 =>
        [
            'id'          => 10215,
            'type'        => 2,
            'name'        => '充值送好礼，累计消费500乐币',
            'prev'        => 0,
            'goto'        => 3,
            'rooms'       =>
                [
                ],
            'channels'    =>
                [
                ],
            'users'       =>
                [
                ],
            'start_time'  => 1452873600,
            'end_time'    => 1453392000,
            'periodName'  => '',
            'periodTime'  => 0,
            'periodId'    => 0,
            'periodStart' => 0,
            'periodEnd'   => 0,
            'sourceId'    => 4,
            'accode'      => 0,
            'action'      => 'API_GOLD',
            'acttag'      => 'cost_gold',
            'execut'      =>
                [
                    0 =>
                        [
                            'key' => 'usertesk.teskvalue',
                            'exe' => '+',
                            'par' => 'param',
                        ],
                ],
            'condit'      =>
                [
                    0 =>
                        [
                            'key' => 'usertesk.teskvalue',
                            'leg' => '>=',
                            'par' => 500,
                        ],
                ],
            'result'      =>
                [
                    0 =>
                        [
                            'key' => 'usertesk.teskdone',
                        ],
                ],
            'target'      => 500,
            'prizeName'   => '北通游戏手柄＋25000乐券',
            'prizes'      =>
                [
                    'coupon' => 25000,
                ],
            'mailSubject' => '充值送好礼，累计消费500乐币',
            'mailContent' => '恭喜您参与活动：
充值送好礼，累计消费500乐币
获得北通游戏手柄＋25000乐券
获得实物奖励的用户，请加官方客服QQ
4000085665领取奖励',
            'mailFileid'  => 0,
            'is_surprise' => 0,
            'is_online'   => 0,
            'is_del'      => 0,
            'sort'        => 11,
            'create_time' => 1452861990,
            'update_time' => 1452861990,
        ],
    10216 =>
        [
            'id'          => 10216,
            'type'        => 2,
            'name'        => '充值送好礼，累计消费1000乐币',
            'prev'        => 0,
            'goto'        => 3,
            'rooms'       =>
                [
                ],
            'channels'    =>
                [
                ],
            'users'       =>
                [
                ],
            'start_time'  => 1452873600,
            'end_time'    => 1453392000,
            'periodName'  => '',
            'periodTime'  => 0,
            'periodId'    => 0,
            'periodStart' => 0,
            'periodEnd'   => 0,
            'sourceId'    => 114,
            'accode'      => 0,
            'action'      => 'API_GOLD',
            'acttag'      => 'cost_gold',
            'execut'      =>
                [
                    0 =>
                        [
                            'key' => 'usertesk.teskvalue',
                            'exe' => '+',
                            'par' => 'param',
                        ],
                ],
            'condit'      =>
                [
                    0 =>
                        [
                            'key' => 'usertesk.teskvalue',
                            'leg' => '>=',
                            'par' => 1000,
                        ],
                ],
            'result'      =>
                [
                    0 =>
                        [
                            'key' => 'usertesk.teskdone',
                        ],
                ],
            'target'      => 1000,
            'prizeName'   => '北通耳机＋手柄＋40000乐券',
            'prizes'      =>
                [
                    'coupon' => 40000,
                ],
            'mailSubject' => '充值送好礼，累计消费1000乐币',
            'mailContent' => '恭喜您参与活动：
充值送好礼，累计消费1000乐币
获得北通蓝牙耳机＋北通游戏手柄＋
40000乐券
获得实物奖励的用户，请加官方客服QQ
4000085665领取奖励',
            'mailFileid'  => 0,
            'is_surprise' => 0,
            'is_online'   => 0,
            'is_del'      => 0,
            'sort'        => 11,
            'create_time' => 1452862162,
            'update_time' => 1452862162,
        ],
    10211 =>
        [
            'id'          => 10211,
            'type'        => 2,
            'name'        => '充值送好礼，首次消费50乐币',
            'prev'        => 0,
            'goto'        => 3,
            'rooms'       =>
                [
                ],
            'channels'    =>
                [
                ],
            'users'       =>
                [
                ],
            'start_time'  => 1452873600,
            'end_time'    => 1453392000,
            'periodName'  => '',
            'periodTime'  => 0,
            'periodId'    => 0,
            'periodStart' => 0,
            'periodEnd'   => 0,
            'sourceId'    => 108,
            'accode'      => 0,
            'action'      => 'API_GOLD',
            'acttag'      => 'cost_gold',
            'execut'      =>
                [
                    0 =>
                        [
                            'key' => 'usertesk.teskvalue',
                            'exe' => '=',
                            'par' => 'param',
                        ],
                ],
            'condit'      =>
                [
                    0 =>
                        [
                            'key' => 'usertesk.teskvalue',
                            'leg' => '=',
                            'par' => 50,
                        ],
                ],
            'result'      =>
                [
                    0 =>
                        [
                            'key' => 'usertesk.teskdone',
                        ],
                ],
            'target'      => 50,
            'prizeName'   => '20万乐豆＋7天记牌器',
            'prizes'      =>
                [
                    'coins'     => 200000,
                    'propItems' =>
                        [
                            9 =>
                                [
                                    'id'   => 9,
                                    'name' => '记牌器(7天)',
                                    'cd'   => '2',
                                    'num'  => 1,
                                    'ext'  => 0,
                                ],
                        ],
                ],
            'mailSubject' => '充值送好礼，首次消费50乐币',
            'mailContent' => '恭喜您参与活动：
充值送好礼，首次消费50乐币
获得20万乐豆＋7天记牌器',
            'mailFileid'  => 0,
            'is_surprise' => 0,
            'is_online'   => 0,
            'is_del'      => 0,
            'sort'        => 11,
            'create_time' => 1452860738,
            'update_time' => 1452860738,
        ],
    10217 =>
        [
            'id'          => 10217,
            'type'        => 2,
            'name'        => '单笔消费30元额外获赠30天记牌器',
            'prev'        => 0,
            'goto'        => 3,
            'rooms'       =>
                [
                ],
            'channels'    =>
                [
                ],
            'users'       =>
                [
                ],
            'start_time'  => 1453392000,
            'end_time'    => 1454342400,
            'periodName'  => '',
            'periodTime'  => 0,
            'periodId'    => 0,
            'periodStart' => 0,
            'periodEnd'   => 0,
            'sourceId'    => 107,
            'accode'      => 0,
            'action'      => 'API_GOLD',
            'acttag'      => 'cost_gold',
            'execut'      =>
                [
                    0 =>
                        [
                            'key' => 'usertesk.teskvalue',
                            'exe' => '=',
                            'par' => 'param',
                        ],
                ],
            'condit'      =>
                [
                    0 =>
                        [
                            'key' => 'usertesk.teskvalue',
                            'leg' => '=',
                            'par' => 30,
                        ],
                ],
            'result'      =>
                [
                    0 =>
                        [
                            'key' => 'usertesk.teskdone',
                        ],
                ],
            'target'      => 30,
            'prizeName'   => '30天记牌器',
            'prizes'      =>
                [
                    'propItems' =>
                        [
                            10 =>
                                [
                                    'id'   => 10,
                                    'name' => '记牌器(30天)',
                                    'cd'   => '2',
                                    'num'  => 1,
                                    'ext'  => 0,
                                ],
                        ],
                ],
            'mailSubject' => '单笔消费30元额外获赠30天记牌器',
            'mailContent' => '恭喜您参与活动：
单笔消费30元额外获赠30天记牌器
您额外获得：
记牌器(30天)',
            'mailFileid'  => 0,
            'is_surprise' => 0,
            'is_online'   => 0,
            'is_del'      => 0,
            'sort'        => 11,
            'create_time' => 1453373212,
            'update_time' => 1453374236,
        ],
    10218 =>
        [
            'id'          => 10218,
            'type'        => 2,
            'name'        => '单笔消费50元额外获赠永久富翁套装',
            'prev'        => 0,
            'goto'        => 3,
            'rooms'       =>
                [
                ],
            'channels'    =>
                [
                ],
            'users'       =>
                [
                ],
            'start_time'  => 1453392000,
            'end_time'    => 1454342400,
            'periodName'  => '',
            'periodTime'  => 0,
            'periodId'    => 0,
            'periodStart' => 0,
            'periodEnd'   => 0,
            'sourceId'    => 108,
            'accode'      => 0,
            'action'      => 'API_GOLD',
            'acttag'      => 'cost_gold',
            'execut'      =>
                [
                    0 =>
                        [
                            'key' => 'usertesk.teskvalue',
                            'exe' => '=',
                            'par' => 'param',
                        ],
                ],
            'condit'      =>
                [
                    0 =>
                        [
                            'key' => 'usertesk.teskvalue',
                            'leg' => '=',
                            'par' => 50,
                        ],
                ],
            'result'      =>
                [
                    0 =>
                        [
                            'key' => 'usertesk.teskdone',
                        ],
                ],
            'target'      => 50,
            'prizeName'   => '永久富翁套装',
            'prizes'      =>
                [
                    'propItems' =>
                        [
                            4 =>
                                [
                                    'id'   => 4,
                                    'name' => '富翁套装',
                                    'cd'   => '1',
                                    'num'  => 1,
                                    'ext'  => 0,
                                ],
                        ],
                ],
            'mailSubject' => '单笔消费50元额外获赠永久富翁套装',
            'mailContent' => '恭喜您参与活动：
单笔消费50元额外获赠永久富翁套装
您额外获得奖励：
富翁套装',
            'mailFileid'  => 0,
            'is_surprise' => 0,
            'is_online'   => 0,
            'is_del'      => 0,
            'sort'        => 11,
            'create_time' => 1453373446,
            'update_time' => 1453374257,
        ],
    10208 =>
        [
            'id'          => 10208,
            'type'        => 2,
            'name'        => '双旦消费返利',
            'prev'        => 0,
            'goto'        => 0,
            'rooms'       =>
                [
                ],
            'channels'    =>
                [
                    0  => 'hisense',
                    1  => 'ali',
                    2  => 'shafa',
                    3  => 'znds',
                    4  => 'changhong',
                    5  => 'yishitengjx',
                    6  => 'kangjia',
                    7  => 'pptv',
                    8  => 'huan',
                    9  => 'aiyouxi',
                    10 => 'xiaomibox',
                    11 => 'atet',
                    12 => 'skyworth',
                    13 => 'drpeng',
                    14 => 'lenovo',
                    15 => 'wukong',
                    16 => 'youkutv',
                    17 => 'weijing',
                ],
            'users'       =>
                [
                ],
            'start_time'  => 1450886400,
            'end_time'    => 1451923200,
            'periodName'  => '',
            'periodTime'  => 0,
            'periodId'    => 0,
            'periodStart' => 0,
            'periodEnd'   => 0,
            'sourceId'    => 109,
            'accode'      => 0,
            'action'      => 'API_GOLD',
            'acttag'      => 'cost_gold',
            'execut'      =>
                [
                    0 =>
                        [
                            'key' => 'usertesk.teskvalue',
                            'exe' => '+',
                            'par' => 'param',
                        ],
                ],
            'condit'      =>
                [
                    0 =>
                        [
                            'key' => 'usertesk.teskvalue',
                            'leg' => '=',
                            'par' => 100,
                        ],
                ],
            'result'      =>
                [
                    0 =>
                        [
                            'key' => 'usertesk.teskdone',
                        ],
                ],
            'target'      => 0,
            'prizeName'   => '双旦消费返利',
            'prizes'      =>
                [
                    'coins' => 1000000,
                ],
            'mailSubject' => '双旦消费返利',
            'mailContent' => '恭喜您获得双旦消费返利100万乐豆',
            'mailFileid'  => 0,
            'is_surprise' => 0,
            'is_online'   => 0,
            'is_del'      => 0,
            'sort'        => 1,
            'create_time' => 1450859997,
            'update_time' => 1450860013,
        ],
    10207 =>
        [
            'id'          => 10207,
            'type'        => 2,
            'name'        => '双旦消费返利',
            'prev'        => 0,
            'goto'        => 0,
            'rooms'       =>
                [
                ],
            'channels'    =>
                [
                    0  => 'hisense',
                    1  => 'ali',
                    2  => 'shafa',
                    3  => 'znds',
                    4  => 'changhong',
                    5  => 'yishitengjx',
                    6  => 'kangjia',
                    7  => 'pptv',
                    8  => 'huan',
                    9  => 'aiyouxi',
                    10 => 'xiaomibox',
                    11 => 'atet',
                    12 => 'skyworth',
                    13 => 'drpeng',
                    14 => 'lenovo',
                    15 => 'wukong',
                    16 => 'youkutv',
                    17 => 'weijing',
                ],
            'users'       =>
                [
                ],
            'start_time'  => 1450886400,
            'end_time'    => 1451923200,
            'periodName'  => '',
            'periodTime'  => 0,
            'periodId'    => 0,
            'periodStart' => 0,
            'periodEnd'   => 0,
            'sourceId'    => 108,
            'accode'      => 0,
            'action'      => 'API_GOLD',
            'acttag'      => 'cost_gold',
            'execut'      =>
                [
                    0 =>
                        [
                            'key' => 'usertesk.teskvalue',
                            'exe' => '+',
                            'par' => 'param',
                        ],
                ],
            'condit'      =>
                [
                    0 =>
                        [
                            'key' => 'usertesk.teskvalue',
                            'leg' => '=',
                            'par' => 50,
                        ],
                ],
            'result'      =>
                [
                    0 =>
                        [
                            'key' => 'usertesk.teskdone',
                        ],
                ],
            'target'      => 0,
            'prizeName'   => '双旦消费返利',
            'prizes'      =>
                [
                    'coins' => 500000,
                ],
            'mailSubject' => '双旦消费返利',
            'mailContent' => '恭喜您获得双旦消费返利10万乐豆',
            'mailFileid'  => 0,
            'is_surprise' => 0,
            'is_online'   => 0,
            'is_del'      => 0,
            'sort'        => 1,
            'create_time' => 1450859946,
            'update_time' => 1450859946,
        ],
    10206 =>
        [
            'id'          => 10206,
            'type'        => 2,
            'name'        => '双旦消费返利',
            'prev'        => 0,
            'goto'        => 0,
            'rooms'       =>
                [
                ],
            'channels'    =>
                [
                    0  => 'hisense',
                    1  => 'ali',
                    2  => 'shafa',
                    3  => 'znds',
                    4  => 'changhong',
                    5  => 'yishitengjx',
                    6  => 'kangjia',
                    7  => 'pptv',
                    8  => 'huan',
                    9  => 'aiyouxi',
                    10 => 'xiaomibox',
                    11 => 'atet',
                    12 => 'skyworth',
                    13 => 'drpeng',
                    14 => 'lenovo',
                    15 => 'wukong',
                    16 => 'youkutv',
                    17 => 'weijing',
                ],
            'users'       =>
                [
                ],
            'start_time'  => 1450886400,
            'end_time'    => 1451923200,
            'periodName'  => '',
            'periodTime'  => 0,
            'periodId'    => 0,
            'periodStart' => 0,
            'periodEnd'   => 0,
            'sourceId'    => 107,
            'accode'      => 0,
            'action'      => 'API_GOLD',
            'acttag'      => 'cost_gold',
            'execut'      =>
                [
                    0 =>
                        [
                            'key' => 'usertesk.teskvalue',
                            'exe' => '+',
                            'par' => 'param',
                        ],
                ],
            'condit'      =>
                [
                    0 =>
                        [
                            'key' => 'usertesk.teskvalue',
                            'leg' => '=',
                            'par' => 30,
                        ],
                ],
            'result'      =>
                [
                    0 =>
                        [
                            'key' => 'usertesk.teskdone',
                        ],
                ],
            'target'      => 0,
            'prizeName'   => '双旦消费返利',
            'prizes'      =>
                [
                    'coins' => 300000,
                ],
            'mailSubject' => '双旦消费返利',
            'mailContent' => '恭喜您获得双旦消费返利30万乐豆',
            'mailFileid'  => 0,
            'is_surprise' => 0,
            'is_online'   => 0,
            'is_del'      => 0,
            'sort'        => 1,
            'create_time' => 1450859910,
            'update_time' => 1450859910,
        ],
    10205 =>
        [
            'id'          => 10205,
            'type'        => 2,
            'name'        => '双旦消费返利',
            'prev'        => 0,
            'goto'        => 0,
            'rooms'       =>
                [
                ],
            'channels'    =>
                [
                    0  => 'hisense',
                    1  => 'ali',
                    2  => 'shafa',
                    3  => 'znds',
                    4  => 'changhong',
                    5  => 'yishitengjx',
                    6  => 'kangjia',
                    7  => 'pptv',
                    8  => 'huan',
                    9  => 'aiyouxi',
                    10 => 'xiaomibox',
                    11 => 'atet',
                    12 => 'skyworth',
                    13 => 'drpeng',
                    14 => 'lenovo',
                    15 => 'wukong',
                    16 => 'youkutv',
                    17 => 'weijing',
                ],
            'users'       =>
                [
                ],
            'start_time'  => 1450886400,
            'end_time'    => 1451923200,
            'periodName'  => '',
            'periodTime'  => 0,
            'periodId'    => 0,
            'periodStart' => 0,
            'periodEnd'   => 0,
            'sourceId'    => 217,
            'accode'      => 0,
            'action'      => 'API_GOLD',
            'acttag'      => 'cost_gold',
            'execut'      =>
                [
                    0 =>
                        [
                            'key' => 'usertesk.teskvalue',
                            'exe' => '+',
                            'par' => 'param',
                        ],
                ],
            'condit'      =>
                [
                    0 =>
                        [
                            'key' => 'usertesk.teskvalue',
                            'leg' => '=',
                            'par' => 10,
                        ],
                ],
            'result'      =>
                [
                    0 =>
                        [
                            'key' => 'usertesk.teskdone',
                        ],
                ],
            'target'      => 0,
            'prizeName'   => '双旦消费返利',
            'prizes'      =>
                [
                    'coins' => 100000,
                ],
            'mailSubject' => '双旦消费返利',
            'mailContent' => '恭喜您获得双旦消费返利10万乐豆',
            'mailFileid'  => 0,
            'is_surprise' => 0,
            'is_online'   => 0,
            'is_del'      => 0,
            'sort'        => 1,
            'create_time' => 1450859849,
            'update_time' => 1450859849,
        ],
    10204 =>
        [
            'id'          => 10204,
            'type'        => 2,
            'name'        => '乐视双旦消费返利',
            'prev'        => 0,
            'goto'        => 0,
            'rooms'       =>
                [
                ],
            'channels'    =>
                [
                    0 => 'letv',
                ],
            'users'       =>
                [
                ],
            'start_time'  => 1450972800,
            'end_time'    => 1451404800,
            'periodName'  => '',
            'periodTime'  => 0,
            'periodId'    => 0,
            'periodStart' => 0,
            'periodEnd'   => 0,
            'sourceId'    => 109,
            'accode'      => 0,
            'action'      => 'API_GOLD',
            'acttag'      => 'cost_gold',
            'execut'      =>
                [
                    0 =>
                        [
                            'key' => 'usertesk.teskvalue',
                            'exe' => '+',
                            'par' => 'param',
                        ],
                ],
            'condit'      =>
                [
                    0 =>
                        [
                            'key' => 'usertesk.teskvalue',
                            'leg' => '=',
                            'par' => 100,
                        ],
                ],
            'result'      =>
                [
                    0 =>
                        [
                            'key' => 'usertesk.teskdone',
                        ],
                ],
            'target'      => 0,
            'prizeName'   => '乐视双旦消费返利',
            'prizes'      =>
                [
                    'coins' => 1000000,
                ],
            'mailSubject' => '乐视双旦消费返利',
            'mailContent' => '恭喜您获得乐视双旦消费返利100万乐豆',
            'mailFileid'  => 0,
            'is_surprise' => 0,
            'is_online'   => 0,
            'is_del'      => 0,
            'sort'        => 1,
            'create_time' => 1450859083,
            'update_time' => 1450859218,
        ],
    10203 =>
        [
            'id'          => 10203,
            'type'        => 2,
            'name'        => '乐视双旦消费返利',
            'prev'        => 0,
            'goto'        => 0,
            'rooms'       =>
                [
                ],
            'channels'    =>
                [
                    0 => 'letv',
                ],
            'users'       =>
                [
                ],
            'start_time'  => 1450972800,
            'end_time'    => 1451404800,
            'periodName'  => '',
            'periodTime'  => 0,
            'periodId'    => 0,
            'periodStart' => 0,
            'periodEnd'   => 0,
            'sourceId'    => 108,
            'accode'      => 0,
            'action'      => 'API_GOLD',
            'acttag'      => 'cost_gold',
            'execut'      =>
                [
                    0 =>
                        [
                            'key' => 'usertesk.teskvalue',
                            'exe' => '+',
                            'par' => 'param',
                        ],
                ],
            'condit'      =>
                [
                    0 =>
                        [
                            'key' => 'usertesk.teskvalue',
                            'leg' => '=',
                            'par' => 50,
                        ],
                ],
            'result'      =>
                [
                    0 =>
                        [
                            'key' => 'usertesk.teskdone',
                        ],
                ],
            'target'      => 0,
            'prizeName'   => '乐视双旦消费返利',
            'prizes'      =>
                [
                    'coins' => 500000,
                ],
            'mailSubject' => '乐视双旦消费返利',
            'mailContent' => '恭喜您获得乐视双旦消费返利50万乐豆',
            'mailFileid'  => 0,
            'is_surprise' => 0,
            'is_online'   => 0,
            'is_del'      => 0,
            'sort'        => 1,
            'create_time' => 1450859028,
            'update_time' => 1450859028,
        ],
    10202 =>
        [
            'id'          => 10202,
            'type'        => 2,
            'name'        => '乐视双旦消费返利',
            'prev'        => 0,
            'goto'        => 0,
            'rooms'       =>
                [
                ],
            'channels'    =>
                [
                    0 => 'letv',
                ],
            'users'       =>
                [
                ],
            'start_time'  => 1450972800,
            'end_time'    => 1451404800,
            'periodName'  => '',
            'periodTime'  => 0,
            'periodId'    => 0,
            'periodStart' => 0,
            'periodEnd'   => 0,
            'sourceId'    => 107,
            'accode'      => 0,
            'action'      => 'API_GOLD',
            'acttag'      => 'cost_gold',
            'execut'      =>
                [
                    0 =>
                        [
                            'key' => 'usertesk.teskvalue',
                            'exe' => '+',
                            'par' => 'param',
                        ],
                ],
            'condit'      =>
                [
                    0 =>
                        [
                            'key' => 'usertesk.teskvalue',
                            'leg' => '=',
                            'par' => 30,
                        ],
                ],
            'result'      =>
                [
                    0 =>
                        [
                            'key' => 'usertesk.teskdone',
                        ],
                ],
            'target'      => 0,
            'prizeName'   => '乐视双旦消费返利',
            'prizes'      =>
                [
                    'coins' => 300000,
                ],
            'mailSubject' => '乐视双旦消费返利',
            'mailContent' => '恭喜您获得乐视双旦消费返利30万乐豆',
            'mailFileid'  => 0,
            'is_surprise' => 0,
            'is_online'   => 0,
            'is_del'      => 0,
            'sort'        => 1,
            'create_time' => 1450858925,
            'update_time' => 1450858925,
        ],
    10201 =>
        [
            'id'          => 10201,
            'type'        => 2,
            'name'        => '乐视双旦消费返利',
            'prev'        => 0,
            'goto'        => 0,
            'rooms'       =>
                [
                ],
            'channels'    =>
                [
                    0 => 'letv',
                ],
            'users'       =>
                [
                ],
            'start_time'  => 1450972800,
            'end_time'    => 1451404800,
            'periodName'  => '',
            'periodTime'  => 0,
            'periodId'    => 0,
            'periodStart' => 0,
            'periodEnd'   => 0,
            'sourceId'    => 217,
            'accode'      => 0,
            'action'      => 'API_GOLD',
            'acttag'      => 'cost_gold',
            'execut'      =>
                [
                    0 =>
                        [
                            'key' => 'usertesk.teskvalue',
                            'exe' => '+',
                            'par' => 'param',
                        ],
                ],
            'condit'      =>
                [
                    0 =>
                        [
                            'key' => 'usertesk.teskvalue',
                            'leg' => '=',
                            'par' => 10,
                        ],
                ],
            'result'      =>
                [
                    0 =>
                        [
                            'key' => 'usertesk.teskdone',
                        ],
                ],
            'target'      => 0,
            'prizeName'   => '乐视双旦消费返利',
            'prizes'      =>
                [
                    'coins' => 100000,
                ],
            'mailSubject' => '乐视双旦消费返利',
            'mailContent' => '恭喜您获得乐视双旦消费返利10万乐豆',
            'mailFileid'  => 0,
            'is_surprise' => 0,
            'is_online'   => 0,
            'is_del'      => 0,
            'sort'        => 1,
            'create_time' => 1450858846,
            'update_time' => 1450859422,
        ],
    10087 =>
        [
            'id'          => 10087,
            'type'        => 2,
            'name'        => 'TCL三周年消费返利',
            'prev'        => 0,
            'goto'        => 0,
            'rooms'       =>
                [
                ],
            'channels'    =>
                [
                    0 => 'tcl',
                ],
            'users'       =>
                [
                ],
            'start_time'  => 1449158400,
            'end_time'    => 1451923200,
            'periodName'  => '每天',
            'periodTime'  => 86400,
            'periodId'    => 0,
            'periodStart' => 1435680000,
            'periodEnd'   => 1435766399,
            'sourceId'    => 109,
            'accode'      => 0,
            'action'      => 'API_GOLD',
            'acttag'      => 'cost_gold',
            'execut'      =>
                [
                    0 =>
                        [
                            'key' => 'usertesk.teskvalue',
                            'exe' => '=',
                            'par' => 'param',
                        ],
                ],
            'condit'      =>
                [
                    0 =>
                        [
                            'key' => 'usertesk.teskvalue',
                            'leg' => '=',
                            'par' => 100,
                        ],
                ],
            'result'      =>
                [
                    0 =>
                        [
                            'key' => 'usertesk.teskdone',
                        ],
                ],
            'target'      => 0,
            'prizeName'   => 'TCL三周年消费返利',
            'prizes'      =>
                [
                    'coins'   => 150000,
                    'lottery' => 1,
                ],
            'mailSubject' => 'TCL三周年消费返利',
            'mailContent' => '恭喜获得TCL三周年消费返利',
            'mailFileid'  => 0,
            'is_surprise' => 0,
            'is_online'   => 0,
            'is_del'      => 0,
            'sort'        => 99,
            'create_time' => 1449130535,
            'update_time' => 1453374903,
        ],
    10086 =>
        [
            'id'          => 10086,
            'type'        => 2,
            'name'        => 'TCL三周年消费返利',
            'prev'        => 0,
            'goto'        => 0,
            'rooms'       =>
                [
                ],
            'channels'    =>
                [
                    0 => 'tcl',
                ],
            'users'       =>
                [
                ],
            'start_time'  => 1449158400,
            'end_time'    => 1451923200,
            'periodName'  => '每天',
            'periodTime'  => 86400,
            'periodId'    => 0,
            'periodStart' => 1435680000,
            'periodEnd'   => 1435766399,
            'sourceId'    => 108,
            'accode'      => 0,
            'action'      => 'API_GOLD',
            'acttag'      => 'cost_gold',
            'execut'      =>
                [
                    0 =>
                        [
                            'key' => 'usertesk.teskvalue',
                            'exe' => '=',
                            'par' => 'param',
                        ],
                ],
            'condit'      =>
                [
                    0 =>
                        [
                            'key' => 'usertesk.teskvalue',
                            'leg' => '=',
                            'par' => 50,
                        ],
                ],
            'result'      =>
                [
                    0 =>
                        [
                            'key' => 'usertesk.teskdone',
                        ],
                ],
            'target'      => 0,
            'prizeName'   => 'TCL三周年消费返利',
            'prizes'      =>
                [
                    'coins'   => 60000,
                    'lottery' => 1,
                ],
            'mailSubject' => 'TCL三周年消费返利',
            'mailContent' => '恭喜获得TCL三周年消费返利',
            'mailFileid'  => 0,
            'is_surprise' => 0,
            'is_online'   => 0,
            'is_del'      => 0,
            'sort'        => 99,
            'create_time' => 1449130494,
            'update_time' => 1453374889,
        ],
    10085 =>
        [
            'id'          => 10085,
            'type'        => 2,
            'name'        => 'TCL三周年消费返利',
            'prev'        => 0,
            'goto'        => 0,
            'rooms'       =>
                [
                ],
            'channels'    =>
                [
                    0 => 'tcl',
                ],
            'users'       =>
                [
                ],
            'start_time'  => 1449158400,
            'end_time'    => 1451923200,
            'periodName'  => '每天',
            'periodTime'  => 86400,
            'periodId'    => 0,
            'periodStart' => 1435680000,
            'periodEnd'   => 1435766399,
            'sourceId'    => 107,
            'accode'      => 0,
            'action'      => 'API_GOLD',
            'acttag'      => 'cost_gold',
            'execut'      =>
                [
                    0 =>
                        [
                            'key' => 'usertesk.teskvalue',
                            'exe' => '=',
                            'par' => 'param',
                        ],
                ],
            'condit'      =>
                [
                    0 =>
                        [
                            'key' => 'usertesk.teskvalue',
                            'leg' => '=',
                            'par' => 30,
                        ],
                ],
            'result'      =>
                [
                    0 =>
                        [
                            'key' => 'usertesk.teskdone',
                        ],
                ],
            'target'      => 0,
            'prizeName'   => 'TCL三周年消费返利',
            'prizes'      =>
                [
                    'coins'   => 30000,
                    'lottery' => 1,
                ],
            'mailSubject' => 'TCL三周年消费返利',
            'mailContent' => '恭喜获得TCL三周年消费返利',
            'mailFileid'  => 0,
            'is_surprise' => 0,
            'is_online'   => 0,
            'is_del'      => 0,
            'sort'        => 99,
            'create_time' => 1449130216,
            'update_time' => 1453374877,
        ],
    10219 =>
        [
            'id'          => 10219,
            'type'        => 2,
            'name'        => '有乐游戏给乡亲们拜年啦',
            'prev'        => 0,
            'goto'        => 0,
            'rooms'       =>
                [
                ],
            'channels'    =>
                [
                ],
            'users'       =>
                [
                ],
            'start_time'  => 1454860800,
            'end_time'    => 1455552000,
            'periodName'  => '',
            'periodTime'  => 0,
            'periodId'    => 0,
            'periodStart' => 0,
            'periodEnd'   => 0,
            'sourceId'    => 70,
            'accode'      => 0,
            'action'      => 'LOGIN_GUEST',
            'acttag'      => 'user_login',
            'execut'      =>
                [
                    0 =>
                        [
                            'key' => 'usertesk.teskvalue',
                            'exe' => '+',
                            'par' => 1,
                        ],
                ],
            'condit'      =>
                [
                    0 =>
                        [
                            'key' => 'usertesk.teskvalue',
                            'leg' => '>',
                            'par' => 0,
                        ],
                ],
            'result'      =>
                [
                    0 =>
                        [
                            'key' => 'usertesk.teskdone',
                        ],
                ],
            'target'      => 1,
            'prizeName'   => '有乐游戏拜年红包',
            'prizes'      =>
                [
                    'propItems' =>
                        [
                            3 =>
                                [
                                    'id'   => 3,
                                    'name' => '大师套装(7天)',
                                    'cd'   => '1',
                                    'num'  => 1,
                                    'ext'  => 0,
                                ],
                            8 =>
                                [
                                    'id'   => 8,
                                    'name' => '记牌器(3天)',
                                    'cd'   => '2',
                                    'num'  => 1,
                                    'ext'  => 0,
                                ],
                        ],
                ],
            'mailSubject' => '有乐游戏给乡亲们拜年啦',
            'mailContent' => '感谢您一直以来对《有乐斗地主》的支持，
值此新春佳节之际，给您送上新年红包：
（大师套装7天 ＋ 记牌器3天），
祝您猴年吉祥，万事如意！',
            'mailFileid'  => 0,
            'is_surprise' => 0,
            'is_online'   => 0,
            'is_del'      => 0,
            'sort'        => 11,
            'create_time' => 1454481126,
            'update_time' => 1454481126,
        ],
    10220 =>
        [
            'id'          => 10220,
            'type'        => 2,
            'name'        => '购买2元乐豆',
            'prev'        => 0,
            'goto'        => 0,
            'rooms'       =>
                [
                ],
            'channels'    =>
                [
                ],
            'users'       =>
                [
                ],
            'start_time'  => 0,
            'end_time'    => 0,
            'periodName'  => '',
            'periodTime'  => 0,
            'periodId'    => 0,
            'periodStart' => 0,
            'periodEnd'   => 0,
            'sourceId'    => 219,
            'accode'      => 0,
            'action'      => 'API_GOLD',
            'acttag'      => 'buy_coins',
            'execut'      =>
                [
                    0 =>
                        [
                            'key' => 'usertesk.teskvalue',
                            'exe' => '=',
                            'par' => 'param',
                        ],
                ],
            'condit'      =>
                [
                    0 =>
                        [
                            'key' => 'usertesk.teskvalue',
                            'leg' => '=',
                            'par' => '2',
                        ],
                ],
            'result'      =>
                [
                    0 =>
                        [
                            'key' => 'usertesk.teskdone',
                        ],
                ],
            'target'      => 2,
            'prizeName'   => '1000乐豆',
            'prizes'      =>
                [
                    'coins' => 1000,
                ],
            'mailSubject' => '',
            'mailContent' => '',
            'mailFileid'  => 0,
            'is_surprise' => 0,
            'is_online'   => 1,
            'is_del'      => 0,
            'sort'        => 1,
            'create_time' => 1456816059,
            'update_time' => 1456816059,
        ],
    10221 =>
        [
            'id'          => 10221,
            'type'        => 2,
            'name'        => '沙发用户专享-每日首次充值30元',
            'prev'        => 0,
            'goto'        => 0,
            'rooms'       =>
                [
                ],
            'channels'    =>
                [
                    0 => 'shafa',
                ],
            'users'       =>
                [
                ],
            'start_time'  => 1457625600,
            'end_time'    => 1457971200,
            'periodName'  => '每天',
            'periodTime'  => 86400,
            'periodId'    => 0,
            'periodStart' => 1457539200,
            'periodEnd'   => 1457625599,
            'sourceId'    => 107,
            'accode'      => 0,
            'action'      => 'API_GOLD',
            'acttag'      => 'cost_gold',
            'execut'      =>
                [
                    0 =>
                        [
                            'key' => 'usertesk.teskvalue',
                            'exe' => '=',
                            'par' => 'param',
                        ],
                ],
            'condit'      =>
                [
                    0 =>
                        [
                            'key' => 'usertesk.teskvalue',
                            'leg' => '=',
                            'par' => 30,
                        ],
                ],
            'result'      =>
                [
                    0 =>
                        [
                            'key' => 'usertesk.teskdone',
                        ],
                ],
            'target'      => 30,
            'prizeName'   => '15万乐豆＋3天记牌器',
            'prizes'      =>
                [
                    'coins'     => 150000,
                    'propItems' =>
                        [
                            8 =>
                                [
                                    'id'   => 8,
                                    'name' => '记牌器(3天)',
                                    'cd'   => '2',
                                    'num'  => 1,
                                    'ext'  => 0,
                                ],
                        ],
                ],
            'mailSubject' => '感谢参与每日首次充值30元活动',
            'mailContent' => '感谢您参与活动：
沙发用户独家专享 数码套装超值送。
获得奖励如下：
15万乐豆 ＋ 记牌器(3天)',
            'mailFileid'  => 0,
            'is_surprise' => 0,
            'is_online'   => 0,
            'is_del'      => 0,
            'sort'        => 11,
            'create_time' => 1457583744,
            'update_time' => 1457583744,
        ],
    10222 =>
        [
            'id'          => 10222,
            'type'        => 2,
            'name'        => '沙发用户专享-每日首次充值50元',
            'prev'        => 0,
            'goto'        => 0,
            'rooms'       =>
                [
                ],
            'channels'    =>
                [
                    0 => 'shafa',
                ],
            'users'       =>
                [
                ],
            'start_time'  => 1457625600,
            'end_time'    => 1457971200,
            'periodName'  => '每天',
            'periodTime'  => 86400,
            'periodId'    => 0,
            'periodStart' => 1457539200,
            'periodEnd'   => 1457625599,
            'sourceId'    => 108,
            'accode'      => 0,
            'action'      => 'API_GOLD',
            'acttag'      => 'cost_gold',
            'execut'      =>
                [
                    0 =>
                        [
                            'key' => 'usertesk.teskvalue',
                            'exe' => '=',
                            'par' => 'param',
                        ],
                ],
            'condit'      =>
                [
                    0 =>
                        [
                            'key' => 'usertesk.teskvalue',
                            'leg' => '=',
                            'par' => 50,
                        ],
                ],
            'result'      =>
                [
                    0 =>
                        [
                            'key' => 'usertesk.teskdone',
                        ],
                ],
            'target'      => 50,
            'prizeName'   => '25万乐豆＋7天记牌器',
            'prizes'      =>
                [
                    'coins'     => 250000,
                    'propItems' =>
                        [
                            9 =>
                                [
                                    'id'   => 9,
                                    'name' => '记牌器(7天)',
                                    'cd'   => '2',
                                    'num'  => 1,
                                    'ext'  => 0,
                                ],
                        ],
                ],
            'mailSubject' => '感谢参与每日首次充值50元活动',
            'mailContent' => '感谢您参与活动：
沙发用户独家专享 数码套装超值送。
获得奖励如下：
25万乐豆 ＋ 记牌器(7天)',
            'mailFileid'  => 0,
            'is_surprise' => 0,
            'is_online'   => 0,
            'is_del'      => 0,
            'sort'        => 11,
            'create_time' => 1457583957,
            'update_time' => 1457583957,
        ],
    10223 =>
        [
            'id'          => 10223,
            'type'        => 2,
            'name'        => '沙发用户专享-每日首次充值100元',
            'prev'        => 0,
            'goto'        => 0,
            'rooms'       =>
                [
                ],
            'channels'    =>
                [
                    0 => 'shafa',
                ],
            'users'       =>
                [
                ],
            'start_time'  => 1457625600,
            'end_time'    => 1457971200,
            'periodName'  => '每天',
            'periodTime'  => 86400,
            'periodId'    => 0,
            'periodStart' => 1457539200,
            'periodEnd'   => 1457625599,
            'sourceId'    => 109,
            'accode'      => 0,
            'action'      => 'API_GOLD',
            'acttag'      => 'cost_gold',
            'execut'      =>
                [
                    0 =>
                        [
                            'key' => 'usertesk.teskvalue',
                            'exe' => '=',
                            'par' => 'param',
                        ],
                ],
            'condit'      =>
                [
                    0 =>
                        [
                            'key' => 'usertesk.teskvalue',
                            'leg' => '=',
                            'par' => 100,
                        ],
                ],
            'result'      =>
                [
                    0 =>
                        [
                            'key' => 'usertesk.teskdone',
                        ],
                ],
            'target'      => 100,
            'prizeName'   => '50万乐豆＋30天记牌器',
            'prizes'      =>
                [
                    'coins'     => 5000000000,
                    'propItems' =>
                        [
                            10 =>
                                [
                                    'id'   => 10,
                                    'name' => '记牌器(30天)',
                                    'cd'   => '2',
                                    'num'  => 1,
                                    'ext'  => 0,
                                ],
                        ],
                ],
            'mailSubject' => '感谢参与每日首次充值100元活动',
            'mailContent' => '感谢您参与活动：
沙发用户独家专享 数码套装超值送。
获得奖励如下：
50万乐豆 ＋ 记牌器(30天)',
            'mailFileid'  => 0,
            'is_surprise' => 0,
            'is_online'   => 0,
            'is_del'      => 0,
            'sort'        => 11,
            'create_time' => 1457584133,
            'update_time' => 1457584133,
        ],
    10224 =>
        [
            'id'          => 10224,
            'type'        => 2,
            'name'        => '沙发用户专享-每日首次充值300元',
            'prev'        => 0,
            'goto'        => 0,
            'rooms'       =>
                [
                ],
            'channels'    =>
                [
                    0 => 'shafa',
                ],
            'users'       =>
                [
                ],
            'start_time'  => 1457625600,
            'end_time'    => 1457971200,
            'periodName'  => '每天',
            'periodTime'  => 86400,
            'periodId'    => 0,
            'periodStart' => 1457539200,
            'periodEnd'   => 1457625599,
            'sourceId'    => 220,
            'accode'      => 0,
            'action'      => 'API_GOLD',
            'acttag'      => 'cost_gold',
            'execut'      =>
                [
                    0 =>
                        [
                            'key' => 'usertesk.teskvalue',
                            'exe' => '=',
                            'par' => 'param',
                        ],
                ],
            'condit'      =>
                [
                    0 =>
                        [
                            'key' => 'usertesk.teskvalue',
                            'leg' => '=',
                            'par' => 300,
                        ],
                ],
            'result'      =>
                [
                    0 =>
                        [
                            'key' => 'usertesk.teskdone',
                        ],
                ],
            'target'      => 300,
            'prizeName'   => '180万乐豆＋30天记牌器＋充电宝',
            'prizes'      =>
                [
                    'coins'     => 1800000,
                    'propItems' =>
                        [
                            10 =>
                                [
                                    'id'   => 10,
                                    'name' => '记牌器(30天)',
                                    'cd'   => '2',
                                    'num'  => 1,
                                    'ext'  => 0,
                                ],
                        ],
                ],
            'mailSubject' => '感谢参与每日首次充值300元活动',
            'mailContent' => '感谢您参与活动：
沙发用户独家专享 数码套装超值送。
获得奖励如下：
180万乐豆 ＋ 记牌器(30天) ＋ 高档充电宝

高档充电宝，需要加官方客服QQ号：
2397071960 ，联系领取',
            'mailFileid'  => 0,
            'is_surprise' => 0,
            'is_online'   => 0,
            'is_del'      => 0,
            'sort'        => 11,
            'create_time' => 1457584475,
            'update_time' => 1457584475,
        ],
    10225 =>
        [
            'id'          => 10225,
            'type'        => 2,
            'name'        => '沙发用户专享-每日首次充值500元',
            'prev'        => 0,
            'goto'        => 0,
            'rooms'       =>
                [
                ],
            'channels'    =>
                [
                    0 => 'shafa',
                ],
            'users'       =>
                [
                ],
            'start_time'  => 1457625600,
            'end_time'    => 1457971200,
            'periodName'  => '每天',
            'periodTime'  => 86400,
            'periodId'    => 0,
            'periodStart' => 1457539200,
            'periodEnd'   => 1457625599,
            'sourceId'    => 221,
            'accode'      => 0,
            'action'      => 'API_GOLD',
            'acttag'      => 'cost_gold',
            'execut'      =>
                [
                    0 =>
                        [
                            'key' => 'usertesk.teskvalue',
                            'exe' => '=',
                            'par' => 'param',
                        ],
                ],
            'condit'      =>
                [
                    0 =>
                        [
                            'key' => 'usertesk.teskvalue',
                            'leg' => '=',
                            'par' => 500,
                        ],
                ],
            'result'      =>
                [
                    0 =>
                        [
                            'key' => 'usertesk.teskdone',
                        ],
                ],
            'target'      => 500,
            'prizeName'   => '350万乐豆＋30天记牌器＋蓝牙耳机',
            'prizes'      =>
                [
                    'coins'     => 3500000,
                    'propItems' =>
                        [
                            10 =>
                                [
                                    'id'   => 10,
                                    'name' => '记牌器(30天)',
                                    'cd'   => '2',
                                    'num'  => 1,
                                    'ext'  => 0,
                                ],
                        ],
                ],
            'mailSubject' => '感谢参与每日首次充值500元活动',
            'mailContent' => '感谢您参与活动：
沙发用户独家专享 数码套装超值送。
获得奖励如下：
350万乐豆 ＋ 记牌器(30天) ＋ 高档蓝牙耳机

高档蓝牙耳机，需要加官方客服QQ号：
2397071960 ，联系领取',
            'mailFileid'  => 0,
            'is_surprise' => 0,
            'is_online'   => 0,
            'is_del'      => 0,
            'sort'        => 11,
            'create_time' => 1457584677,
            'update_time' => 1457584677,
        ],
    10226 =>
        [
            'id'          => 10226,
            'type'        => 2,
            'name'        => '沙发用户专享-每日首次充值1000元',
            'prev'        => 0,
            'goto'        => 0,
            'rooms'       =>
                [
                ],
            'channels'    =>
                [
                    0 => 'shafa',
                ],
            'users'       =>
                [
                ],
            'start_time'  => 1457625600,
            'end_time'    => 1457971200,
            'periodName'  => '每天',
            'periodTime'  => 86400,
            'periodId'    => 0,
            'periodStart' => 1457539200,
            'periodEnd'   => 1457625599,
            'sourceId'    => 222,
            'accode'      => 0,
            'action'      => 'API_GOLD',
            'acttag'      => 'cost_gold',
            'execut'      =>
                [
                    0 =>
                        [
                            'key' => 'usertesk.teskvalue',
                            'exe' => '=',
                            'par' => 'param',
                        ],
                ],
            'condit'      =>
                [
                    0 =>
                        [
                            'key' => 'usertesk.teskvalue',
                            'leg' => '=',
                            'par' => 1000,
                        ],
                ],
            'result'      =>
                [
                    0 =>
                        [
                            'key' => 'usertesk.teskdone',
                        ],
                ],
            'target'      => 1000,
            'prizeName'   => '700万乐豆＋30天记牌器＋游戏手柄',
            'prizes'      =>
                [
                    'coins'     => 7000000,
                    'propItems' =>
                        [
                            10 =>
                                [
                                    'id'   => 10,
                                    'name' => '记牌器(30天)',
                                    'cd'   => '2',
                                    'num'  => 1,
                                    'ext'  => 0,
                                ],
                        ],
                ],
            'mailSubject' => '感谢参与每日首次充值1000元活动',
            'mailContent' => '感谢您参与活动：
沙发用户独家专享 数码套装超值送。
获得奖励如下：
700万乐豆 ＋ 记牌器(30天) ＋ 高档游戏手柄

高档游戏手柄，需要加官方客服QQ号：
2397071960 ，联系领取',
            'mailFileid'  => 0,
            'is_surprise' => 0,
            'is_online'   => 0,
            'is_del'      => 0,
            'sort'        => 11,
            'create_time' => 1457584866,
            'update_time' => 1457584866,
        ],
    10227 =>
        [
            'id'          => 10227,
            'type'        => 2,
            'name'        => '海信-首次充值10元',
            'prev'        => 0,
            'goto'        => 0,
            'rooms'       =>
                [
                ],
            'channels'    =>
                [
                    0 => 'hisense',
                ],
            'users'       =>
                [
                ],
            'start_time'  => 1457625600,
            'end_time'    => 1458748800,
            'periodName'  => '',
            'periodTime'  => 0,
            'periodId'    => 0,
            'periodStart' => 0,
            'periodEnd'   => 0,
            'sourceId'    => 217,
            'accode'      => 0,
            'action'      => 'API_GOLD',
            'acttag'      => 'cost_gold',
            'execut'      =>
                [
                    0 =>
                        [
                            'key' => 'usertesk.teskvalue',
                            'exe' => '=',
                            'par' => 'param',
                        ],
                ],
            'condit'      =>
                [
                    0 =>
                        [
                            'key' => 'usertesk.teskvalue',
                            'leg' => '=',
                            'par' => 10,
                        ],
                ],
            'result'      =>
                [
                    0 =>
                        [
                            'key' => 'usertesk.teskdone',
                        ],
                ],
            'target'      => 10,
            'prizeName'   => '3万乐豆＋1天记牌器',
            'prizes'      =>
                [
                    'coins'     => 30000,
                    'propItems' =>
                        [
                            7 =>
                                [
                                    'id'   => 7,
                                    'name' => '记牌器(1天)',
                                    'cd'   => '2',
                                    'num'  => 1,
                                    'ext'  => 0,
                                ],
                        ],
                ],
            'mailSubject' => '感谢参与首次充值10元活动',
            'mailContent' => '感谢您参与活动：
充值送海信影视VIP卡。
获得奖励如下：
3万乐豆 ＋ 记牌器(1天)',
            'mailFileid'  => 0,
            'is_surprise' => 0,
            'is_online'   => 0,
            'is_del'      => 0,
            'sort'        => 11,
            'create_time' => 1457586026,
            'update_time' => 1457586026,
        ],
    10228 =>
        [
            'id'          => 10228,
            'type'        => 2,
            'name'        => '海信-首次充值30元',
            'prev'        => 0,
            'goto'        => 0,
            'rooms'       =>
                [
                ],
            'channels'    =>
                [
                    0 => 'hisense',
                ],
            'users'       =>
                [
                ],
            'start_time'  => 1457625600,
            'end_time'    => 1458748800,
            'periodName'  => '',
            'periodTime'  => 0,
            'periodId'    => 0,
            'periodStart' => 0,
            'periodEnd'   => 0,
            'sourceId'    => 107,
            'accode'      => 0,
            'action'      => 'API_GOLD',
            'acttag'      => 'cost_gold',
            'execut'      =>
                [
                    0 =>
                        [
                            'key' => 'usertesk.teskvalue',
                            'exe' => '=',
                            'par' => 'param',
                        ],
                ],
            'condit'      =>
                [
                    0 =>
                        [
                            'key' => 'usertesk.teskvalue',
                            'leg' => '=',
                            'par' => 30,
                        ],
                ],
            'result'      =>
                [
                    0 =>
                        [
                            'key' => 'usertesk.teskdone',
                        ],
                ],
            'target'      => 30,
            'prizeName'   => '10万乐豆＋3天记牌器',
            'prizes'      =>
                [
                    'coins'     => 100000,
                    'propItems' =>
                        [
                            8 =>
                                [
                                    'id'   => 8,
                                    'name' => '记牌器(3天)',
                                    'cd'   => '2',
                                    'num'  => 1,
                                    'ext'  => 0,
                                ],
                        ],
                ],
            'mailSubject' => '感谢参与首次充值30元活动',
            'mailContent' => '感谢您参与活动：
充值送海信影视VIP卡。
获得奖励如下：
10万乐豆 ＋ 记牌器(3天)',
            'mailFileid'  => 0,
            'is_surprise' => 0,
            'is_online'   => 0,
            'is_del'      => 0,
            'sort'        => 11,
            'create_time' => 1457586209,
            'update_time' => 1457586209,
        ],
    10229 =>
        [
            'id'          => 10229,
            'type'        => 2,
            'name'        => '海信-首次充值50元',
            'prev'        => 0,
            'goto'        => 0,
            'rooms'       =>
                [
                ],
            'channels'    =>
                [
                    0 => 'hisense',
                ],
            'users'       =>
                [
                ],
            'start_time'  => 1457625600,
            'end_time'    => 1458748800,
            'periodName'  => '',
            'periodTime'  => 0,
            'periodId'    => 0,
            'periodStart' => 0,
            'periodEnd'   => 0,
            'sourceId'    => 108,
            'accode'      => 0,
            'action'      => 'API_GOLD',
            'acttag'      => 'cost_gold',
            'execut'      =>
                [
                    0 =>
                        [
                            'key' => 'usertesk.teskvalue',
                            'exe' => '=',
                            'par' => 'param',
                        ],
                ],
            'condit'      =>
                [
                    0 =>
                        [
                            'key' => 'usertesk.teskvalue',
                            'leg' => '=',
                            'par' => 50,
                        ],
                ],
            'result'      =>
                [
                    0 =>
                        [
                            'key' => 'usertesk.teskdone',
                        ],
                ],
            'target'      => 50,
            'prizeName'   => '20万乐豆＋7天记牌器',
            'prizes'      =>
                [
                    'coins'     => 200000,
                    'propItems' =>
                        [
                            9 =>
                                [
                                    'id'   => 9,
                                    'name' => '记牌器(7天)',
                                    'cd'   => '2',
                                    'num'  => 1,
                                    'ext'  => 0,
                                ],
                        ],
                ],
            'mailSubject' => '感谢参与首次充值50元活动',
            'mailContent' => '感谢您参与活动：
充值送海信影视VIP卡。
获得奖励如下：
20万乐豆 ＋ 记牌器(7天)',
            'mailFileid'  => 0,
            'is_surprise' => 0,
            'is_online'   => 0,
            'is_del'      => 0,
            'sort'        => 11,
            'create_time' => 1457586329,
            'update_time' => 1457586329,
        ],
    10230 =>
        [
            'id'          => 10230,
            'type'        => 2,
            'name'        => '海信-首次充值100元',
            'prev'        => 0,
            'goto'        => 0,
            'rooms'       =>
                [
                ],
            'channels'    =>
                [
                    0 => 'hisense',
                ],
            'users'       =>
                [
                ],
            'start_time'  => 1457625600,
            'end_time'    => 1458748800,
            'periodName'  => '',
            'periodTime'  => 0,
            'periodId'    => 0,
            'periodStart' => 0,
            'periodEnd'   => 0,
            'sourceId'    => 109,
            'accode'      => 0,
            'action'      => 'API_GOLD',
            'acttag'      => 'cost_gold',
            'execut'      =>
                [
                    0 =>
                        [
                            'key' => 'usertesk.teskvalue',
                            'exe' => '=',
                            'par' => 'param',
                        ],
                ],
            'condit'      =>
                [
                    0 =>
                        [
                            'key' => 'usertesk.teskvalue',
                            'leg' => '=',
                            'par' => 100,
                        ],
                ],
            'result'      =>
                [
                    0 =>
                        [
                            'key' => 'usertesk.teskdone',
                        ],
                ],
            'target'      => 100,
            'prizeName'   => '50万乐豆＋30天记牌器',
            'prizes'      =>
                [
                    'coins'     => 500000,
                    'propItems' =>
                        [
                            10 =>
                                [
                                    'id'   => 10,
                                    'name' => '记牌器(30天)',
                                    'cd'   => '2',
                                    'num'  => 1,
                                    'ext'  => 0,
                                ],
                        ],
                ],
            'mailSubject' => '感谢参与首次充值100元活动',
            'mailContent' => '感谢您参与活动：
充值送海信影视VIP卡。
获得奖励如下：
50万乐豆 ＋ 记牌器(30天)',
            'mailFileid'  => 0,
            'is_surprise' => 0,
            'is_online'   => 0,
            'is_del'      => 0,
            'sort'        => 11,
            'create_time' => 1457586446,
            'update_time' => 1457586446,
        ],
    10231 =>
        [
            'id'          => 10231,
            'type'        => 2,
            'name'        => '微信粉丝专属活动充值10元',
            'prev'        => 0,
            'goto'        => 0,
            'rooms'       =>
                [
                ],
            'channels'    =>
                [
                    0 => '1161-453',
                ],
            'users'       =>
                [
                ],
            'start_time'  => 1457712000,
            'end_time'    => 1458057600,
            'periodName'  => '',
            'periodTime'  => 0,
            'periodId'    => 0,
            'periodStart' => 0,
            'periodEnd'   => 0,
            'sourceId'    => 217,
            'accode'      => 0,
            'action'      => 'API_GOLD',
            'acttag'      => 'cost_gold',
            'execut'      =>
                [
                    0 =>
                        [
                            'key' => 'usertesk.teskvalue',
                            'exe' => '=',
                            'par' => 'param',
                        ],
                ],
            'condit'      =>
                [
                    0 =>
                        [
                            'key' => 'usertesk.teskvalue',
                            'leg' => '=',
                            'par' => 10,
                        ],
                ],
            'result'      =>
                [
                    0 =>
                        [
                            'key' => 'usertesk.teskdone',
                        ],
                ],
            'target'      => 10,
            'prizeName'   => '1天记牌器+1000乐券',
            'prizes'      =>
                [
                    'coupon'    => 1000,
                    'propItems' =>
                        [
                            7 =>
                                [
                                    'id'   => 7,
                                    'name' => '记牌器(1天)',
                                    'cd'   => '2',
                                    'num'  => 1,
                                    'ext'  => 0,
                                ],
                        ],
                ],
            'mailSubject' => '参与微信粉丝专属活动充值10元',
            'mailContent' => '感谢您参与活动：
微信粉丝专属活动。
乐豆奖励已经自动累加到您的帐户。
额外获得奖励如下：
1000乐券 ＋ 记牌器(1天)',
            'mailFileid'  => 0,
            'is_surprise' => 0,
            'is_online'   => 0,
            'is_del'      => 0,
            'sort'        => 11,
            'create_time' => 1457690921,
            'update_time' => 1457691288,
        ],
    10232 =>
        [
            'id'          => 10232,
            'type'        => 2,
            'name'        => '微信粉丝专属活动充值30元',
            'prev'        => 0,
            'goto'        => 0,
            'rooms'       =>
                [
                ],
            'channels'    =>
                [
                    0 => '1161-453',
                ],
            'users'       =>
                [
                ],
            'start_time'  => 1457712000,
            'end_time'    => 1458057600,
            'periodName'  => '',
            'periodTime'  => 0,
            'periodId'    => 0,
            'periodStart' => 0,
            'periodEnd'   => 0,
            'sourceId'    => 107,
            'accode'      => 0,
            'action'      => 'API_GOLD',
            'acttag'      => 'cost_gold',
            'execut'      =>
                [
                    0 =>
                        [
                            'key' => 'usertesk.teskvalue',
                            'exe' => '=',
                            'par' => 'param',
                        ],
                ],
            'condit'      =>
                [
                    0 =>
                        [
                            'key' => 'usertesk.teskvalue',
                            'leg' => '=',
                            'par' => 30,
                        ],
                ],
            'result'      =>
                [
                    0 =>
                        [
                            'key' => 'usertesk.teskdone',
                        ],
                ],
            'target'      => 30,
            'prizeName'   => '3天记牌器+2000乐券',
            'prizes'      =>
                [
                    'coupon'    => 2000,
                    'propItems' =>
                        [
                            8 =>
                                [
                                    'id'   => 8,
                                    'name' => '记牌器(3天)',
                                    'cd'   => '2',
                                    'num'  => 1,
                                    'ext'  => 0,
                                ],
                        ],
                ],
            'mailSubject' => '参与微信粉丝专属活动充值30元',
            'mailContent' => '感谢您参与活动：
微信粉丝专属活动。
乐豆奖励已经自动累加到您的帐户。
额外获得奖励如下：
2000乐券 ＋ 记牌器(3天)',
            'mailFileid'  => 0,
            'is_surprise' => 0,
            'is_online'   => 0,
            'is_del'      => 0,
            'sort'        => 11,
            'create_time' => 1457691179,
            'update_time' => 1457691179,
        ],
    10233 =>
        [
            'id'          => 10233,
            'type'        => 2,
            'name'        => '微信粉丝专属活动充值50元',
            'prev'        => 0,
            'goto'        => 0,
            'rooms'       =>
                [
                ],
            'channels'    =>
                [
                    0 => '1161-453',
                ],
            'users'       =>
                [
                ],
            'start_time'  => 1457712000,
            'end_time'    => 1458057600,
            'periodName'  => '',
            'periodTime'  => 0,
            'periodId'    => 0,
            'periodStart' => 0,
            'periodEnd'   => 0,
            'sourceId'    => 108,
            'accode'      => 0,
            'action'      => 'API_GOLD',
            'acttag'      => 'cost_gold',
            'execut'      =>
                [
                    0 =>
                        [
                            'key' => 'usertesk.teskvalue',
                            'exe' => '=',
                            'par' => 'param',
                        ],
                ],
            'condit'      =>
                [
                    0 =>
                        [
                            'key' => 'usertesk.teskvalue',
                            'leg' => '=',
                            'par' => 50,
                        ],
                ],
            'result'      =>
                [
                    0 =>
                        [
                            'key' => 'usertesk.teskdone',
                        ],
                ],
            'target'      => 50,
            'prizeName'   => '7天记牌器+4000乐券',
            'prizes'      =>
                [
                    'coupon'    => 4000,
                    'propItems' =>
                        [
                            9 =>
                                [
                                    'id'   => 9,
                                    'name' => '记牌器(7天)',
                                    'cd'   => '2',
                                    'num'  => 1,
                                    'ext'  => 0,
                                ],
                        ],
                ],
            'mailSubject' => '参与微信粉丝专属活动充值50元',
            'mailContent' => '感谢您参与活动：
微信粉丝专属活动。
乐豆奖励已经自动累加到您的帐户。
额外获得奖励如下：
4000乐券 ＋ 记牌器(7天)',
            'mailFileid'  => 0,
            'is_surprise' => 0,
            'is_online'   => 0,
            'is_del'      => 0,
            'sort'        => 11,
            'create_time' => 1457691277,
            'update_time' => 1457691277,
        ],
    10234 =>
        [
            'id'          => 10234,
            'type'        => 2,
            'name'        => '微信粉丝专属活动充值100元',
            'prev'        => 0,
            'goto'        => 0,
            'rooms'       =>
                [
                ],
            'channels'    =>
                [
                    0 => '1161-453',
                ],
            'users'       =>
                [
                ],
            'start_time'  => 1457712000,
            'end_time'    => 1458057600,
            'periodName'  => '',
            'periodTime'  => 0,
            'periodId'    => 0,
            'periodStart' => 0,
            'periodEnd'   => 0,
            'sourceId'    => 109,
            'accode'      => 0,
            'action'      => 'API_GOLD',
            'acttag'      => 'cost_gold',
            'execut'      =>
                [
                    0 =>
                        [
                            'key' => 'usertesk.teskvalue',
                            'exe' => '=',
                            'par' => 'param',
                        ],
                ],
            'condit'      =>
                [
                    0 =>
                        [
                            'key' => 'usertesk.teskvalue',
                            'leg' => '=',
                            'par' => 100,
                        ],
                ],
            'result'      =>
                [
                    0 =>
                        [
                            'key' => 'usertesk.teskdone',
                        ],
                ],
            'target'      => 100,
            'prizeName'   => '30天记牌器+8000乐券',
            'prizes'      =>
                [
                    'coupon'    => 8000,
                    'propItems' =>
                        [
                            10 =>
                                [
                                    'id'   => 10,
                                    'name' => '记牌器(30天)',
                                    'cd'   => '2',
                                    'num'  => 1,
                                    'ext'  => 0,
                                ],
                        ],
                ],
            'mailSubject' => '参与微信粉丝专属活动充值100元',
            'mailContent' => '感谢您参与活动：
微信粉丝专属活动。
乐豆奖励已经自动累加到您的帐户。
额外获得奖励如下：
8000乐券 ＋ 记牌器(30天)',
            'mailFileid'  => 0,
            'is_surprise' => 0,
            'is_online'   => 0,
            'is_del'      => 0,
            'sort'        => 11,
            'create_time' => 1457691947,
            'update_time' => 1457691947,
        ],
    10235 =>
        [
            'id'          => 10235,
            'type'        => 2,
            'name'        => '每日首次充值10元',
            'prev'        => 0,
            'goto'        => 0,
            'rooms'       =>
                [
                ],
            'channels'    =>
                [
                    0 => 'aiyouxi',
                    1 => 'letv',
                ],
            'users'       =>
                [
                ],
            'start_time'  => 1458144000,
            'end_time'    => 1458576000,
            'periodName'  => '每天',
            'periodTime'  => 86400,
            'periodId'    => 0,
            'periodStart' => 1457539200,
            'periodEnd'   => 1457625599,
            'sourceId'    => 217,
            'accode'      => 0,
            'action'      => 'API_GOLD',
            'acttag'      => 'cost_gold',
            'execut'      =>
                [
                    0 =>
                        [
                            'key' => 'usertesk.teskvalue',
                            'exe' => '=',
                            'par' => 'param',
                        ],
                ],
            'condit'      =>
                [
                    0 =>
                        [
                            'key' => 'usertesk.teskvalue',
                            'leg' => '=',
                            'par' => 10,
                        ],
                ],
            'result'      =>
                [
                    0 =>
                        [
                            'key' => 'usertesk.teskdone',
                        ],
                ],
            'target'      => 10,
            'prizeName'   => '3万乐豆＋1天记牌器',
            'prizes'      =>
                [
                    'coins'     => 30000,
                    'propItems' =>
                        [
                            7 =>
                                [
                                    'id'   => 7,
                                    'name' => '记牌器(1天)',
                                    'cd'   => '2',
                                    'num'  => 1,
                                    'ext'  => 0,
                                ],
                        ],
                ],
            'mailSubject' => '感谢参与每日首次充值10元活动',
            'mailContent' => '感谢您参与活动：
每日首次充值活动。
获得奖励如下：
3万乐豆 ＋ 记牌器(1天)',
            'mailFileid'  => 0,
            'is_surprise' => 0,
            'is_online'   => 0,
            'is_del'      => 0,
            'sort'        => 11,
            'create_time' => 1458112275,
            'update_time' => 1458112275,
        ],
    10236 =>
        [
            'id'          => 10236,
            'type'        => 2,
            'name'        => '每日首次充值30元',
            'prev'        => 0,
            'goto'        => 0,
            'rooms'       =>
                [
                ],
            'channels'    =>
                [
                    0 => 'aiyouxi',
                    1 => 'letv',
                ],
            'users'       =>
                [
                ],
            'start_time'  => 1458144000,
            'end_time'    => 1458576000,
            'periodName'  => '每天',
            'periodTime'  => 86400,
            'periodId'    => 0,
            'periodStart' => 1457539200,
            'periodEnd'   => 1457625599,
            'sourceId'    => 107,
            'accode'      => 0,
            'action'      => 'API_GOLD',
            'acttag'      => 'cost_gold',
            'execut'      =>
                [
                    0 =>
                        [
                            'key' => 'usertesk.teskvalue',
                            'exe' => '=',
                            'par' => 'param',
                        ],
                ],
            'condit'      =>
                [
                    0 =>
                        [
                            'key' => 'usertesk.teskvalue',
                            'leg' => '=',
                            'par' => 30,
                        ],
                ],
            'result'      =>
                [
                    0 =>
                        [
                            'key' => 'usertesk.teskdone',
                        ],
                ],
            'target'      => 30,
            'prizeName'   => '10万乐豆＋3天记牌器',
            'prizes'      =>
                [
                    'coins'     => 100000,
                    'propItems' =>
                        [
                            8 =>
                                [
                                    'id'   => 8,
                                    'name' => '记牌器(3天)',
                                    'cd'   => '2',
                                    'num'  => 1,
                                    'ext'  => 0,
                                ],
                        ],
                ],
            'mailSubject' => '感谢参与每日首次充值30元活动',
            'mailContent' => '感谢您参与活动：
每日首次充值活动。
获得奖励如下：
10万乐豆 ＋ 记牌器(3天)',
            'mailFileid'  => 0,
            'is_surprise' => 0,
            'is_online'   => 0,
            'is_del'      => 0,
            'sort'        => 11,
            'create_time' => 1458112498,
            'update_time' => 1458112498,
        ],
    10237 =>
        [
            'id'          => 10237,
            'type'        => 2,
            'name'        => '每日首次充值50元',
            'prev'        => 0,
            'goto'        => 0,
            'rooms'       =>
                [
                ],
            'channels'    =>
                [
                    0 => 'aiyouxi',
                    1 => 'letv',
                ],
            'users'       =>
                [
                ],
            'start_time'  => 1458144000,
            'end_time'    => 1458576000,
            'periodName'  => '每天',
            'periodTime'  => 86400,
            'periodId'    => 0,
            'periodStart' => 1457539200,
            'periodEnd'   => 1457625599,
            'sourceId'    => 108,
            'accode'      => 0,
            'action'      => 'API_GOLD',
            'acttag'      => 'cost_gold',
            'execut'      =>
                [
                    0 =>
                        [
                            'key' => 'usertesk.teskvalue',
                            'exe' => '=',
                            'par' => 'param',
                        ],
                ],
            'condit'      =>
                [
                    0 =>
                        [
                            'key' => 'usertesk.teskvalue',
                            'leg' => '=',
                            'par' => 50,
                        ],
                ],
            'result'      =>
                [
                    0 =>
                        [
                            'key' => 'usertesk.teskdone',
                        ],
                ],
            'target'      => 50,
            'prizeName'   => '20万乐豆＋7天记牌器',
            'prizes'      =>
                [
                    'coins'     => 200000,
                    'propItems' =>
                        [
                            9 =>
                                [
                                    'id'   => 9,
                                    'name' => '记牌器(7天)',
                                    'cd'   => '2',
                                    'num'  => 1,
                                    'ext'  => 0,
                                ],
                        ],
                ],
            'mailSubject' => '感谢参与每日首次充值50元活动',
            'mailContent' => '感谢您参与活动：
每日首次充值活动。
获得奖励如下：
20万乐豆 ＋ 记牌器(7天)',
            'mailFileid'  => 0,
            'is_surprise' => 0,
            'is_online'   => 0,
            'is_del'      => 0,
            'sort'        => 11,
            'create_time' => 1458112643,
            'update_time' => 1458112643,
        ],
    10238 =>
        [
            'id'          => 10238,
            'type'        => 2,
            'name'        => '每日首次充值100元',
            'prev'        => 0,
            'goto'        => 0,
            'rooms'       =>
                [
                ],
            'channels'    =>
                [
                    0 => 'aiyouxi',
                    1 => 'letv',
                ],
            'users'       =>
                [
                ],
            'start_time'  => 1458144000,
            'end_time'    => 1458576000,
            'periodName'  => '每天',
            'periodTime'  => 86400,
            'periodId'    => 0,
            'periodStart' => 1457539200,
            'periodEnd'   => 1457625599,
            'sourceId'    => 109,
            'accode'      => 0,
            'action'      => 'API_GOLD',
            'acttag'      => 'cost_gold',
            'execut'      =>
                [
                    0 =>
                        [
                            'key' => 'usertesk.teskvalue',
                            'exe' => '=',
                            'par' => 'param',
                        ],
                ],
            'condit'      =>
                [
                    0 =>
                        [
                            'key' => 'usertesk.teskvalue',
                            'leg' => '=',
                            'par' => 100,
                        ],
                ],
            'result'      =>
                [
                    0 =>
                        [
                            'key' => 'usertesk.teskdone',
                        ],
                ],
            'target'      => 100,
            'prizeName'   => '45万乐豆＋30天记牌器',
            'prizes'      =>
                [
                    'coins'     => 450000,
                    'propItems' =>
                        [
                            10 =>
                                [
                                    'id'   => 10,
                                    'name' => '记牌器(30天)',
                                    'cd'   => '2',
                                    'num'  => 1,
                                    'ext'  => 0,
                                ],
                        ],
                ],
            'mailSubject' => '感谢参与每日首次充值100元活动',
            'mailContent' => '感谢您参与活动：
每日首次充值活动。
获得奖励如下：
45万乐豆 ＋ 记牌器(30天)',
            'mailFileid'  => 0,
            'is_surprise' => 0,
            'is_online'   => 0,
            'is_del'      => 0,
            'sort'        => 11,
            'create_time' => 1458112818,
            'update_time' => 1458112818,
        ],
    10239 =>
        [
            'id'          => 10239,
            'type'        => 2,
            'name'        => '海信-每日首次充值30元',
            'prev'        => 0,
            'goto'        => 0,
            'rooms'       =>
                [
                ],
            'channels'    =>
                [
                    0 => 'hisense',
                ],
            'users'       =>
                [
                ],
            'start_time'  => 1459353600,
            'end_time'    => 1459872000,
            'periodName'  => '每天',
            'periodTime'  => 86400,
            'periodId'    => 0,
            'periodStart' => 1459353600,
            'periodEnd'   => 1459439999,
            'sourceId'    => 107,
            'accode'      => 0,
            'action'      => 'API_GOLD',
            'acttag'      => 'cost_gold',
            'execut'      =>
                [
                    0 =>
                        [
                            'key' => 'usertesk.teskvalue',
                            'exe' => '=',
                            'par' => 'param',
                        ],
                ],
            'condit'      =>
                [
                    0 =>
                        [
                            'key' => 'usertesk.teskvalue',
                            'leg' => '=',
                            'par' => 30,
                        ],
                ],
            'result'      =>
                [
                    0 =>
                        [
                            'key' => 'usertesk.teskdone',
                        ],
                ],
            'target'      => 30,
            'prizeName'   => '6万乐豆＋1天记牌器',
            'prizes'      =>
                [
                    'coins'     => 60000,
                    'propItems' =>
                        [
                            7 =>
                                [
                                    'id'   => 7,
                                    'name' => '记牌器(1天)',
                                    'cd'   => '2',
                                    'num'  => 1,
                                    'ext'  => 0,
                                ],
                        ],
                ],
            'mailSubject' => '感谢参与每日首次充值30元活动',
            'mailContent' => '感谢您参与活动：
充值送海信聚好学家庭专区活动。
获得奖励如下：
6万乐豆 ＋ 记牌器(1天)',
            'mailFileid'  => 0,
            'is_surprise' => 0,
            'is_online'   => 0,
            'is_del'      => 0,
            'sort'        => 11,
            'create_time' => 1459412944,
            'update_time' => 1459412944,
        ],
    10240 =>
        [
            'id'          => 10240,
            'type'        => 2,
            'name'        => '海信-每日首次充值50元',
            'prev'        => 0,
            'goto'        => 0,
            'rooms'       =>
                [
                ],
            'channels'    =>
                [
                    0 => 'hisense',
                ],
            'users'       =>
                [
                ],
            'start_time'  => 1459353600,
            'end_time'    => 1459872000,
            'periodName'  => '每天',
            'periodTime'  => 86400,
            'periodId'    => 0,
            'periodStart' => 1459353600,
            'periodEnd'   => 1459439999,
            'sourceId'    => 108,
            'accode'      => 0,
            'action'      => 'API_GOLD',
            'acttag'      => 'cost_gold',
            'execut'      =>
                [
                    0 =>
                        [
                            'key' => 'usertesk.teskvalue',
                            'exe' => '=',
                            'par' => 'param',
                        ],
                ],
            'condit'      =>
                [
                    0 =>
                        [
                            'key' => 'usertesk.teskvalue',
                            'leg' => '=',
                            'par' => 50,
                        ],
                ],
            'result'      =>
                [
                    0 =>
                        [
                            'key' => 'usertesk.teskdone',
                        ],
                ],
            'target'      => 50,
            'prizeName'   => '10万乐豆＋3天记牌器',
            'prizes'      =>
                [
                    'coins'     => 100000,
                    'propItems' =>
                        [
                            8 =>
                                [
                                    'id'   => 8,
                                    'name' => '记牌器(3天)',
                                    'cd'   => '2',
                                    'num'  => 1,
                                    'ext'  => 0,
                                ],
                        ],
                ],
            'mailSubject' => '感谢参与每日首次充值50元活动',
            'mailContent' => '感谢您参与活动：
充值送海信聚好学家庭专区活动。
获得奖励如下：
10万乐豆 ＋ 记牌器(3天)',
            'mailFileid'  => 0,
            'is_surprise' => 0,
            'is_online'   => 0,
            'is_del'      => 0,
            'sort'        => 11,
            'create_time' => 1459413089,
            'update_time' => 1459413089,
        ],
    10241 =>
        [
            'id'          => 10241,
            'type'        => 2,
            'name'        => '海信-每日首次充值100元',
            'prev'        => 0,
            'goto'        => 0,
            'rooms'       =>
                [
                ],
            'channels'    =>
                [
                    0 => 'hisense',
                ],
            'users'       =>
                [
                ],
            'start_time'  => 1459353600,
            'end_time'    => 1459872000,
            'periodName'  => '每天',
            'periodTime'  => 86400,
            'periodId'    => 0,
            'periodStart' => 1459353600,
            'periodEnd'   => 1459439999,
            'sourceId'    => 109,
            'accode'      => 0,
            'action'      => 'API_GOLD',
            'acttag'      => 'cost_gold',
            'execut'      =>
                [
                    0 =>
                        [
                            'key' => 'usertesk.teskvalue',
                            'exe' => '=',
                            'par' => 'param',
                        ],
                ],
            'condit'      =>
                [
                    0 =>
                        [
                            'key' => 'usertesk.teskvalue',
                            'leg' => '=',
                            'par' => 100,
                        ],
                ],
            'result'      =>
                [
                    0 =>
                        [
                            'key' => 'usertesk.teskdone',
                        ],
                ],
            'target'      => 100,
            'prizeName'   => '30万乐豆',
            'prizes'      =>
                [
                    'coins' => 300000,
                ],
            'mailSubject' => '感谢参与每日首次充值100元活动',
            'mailContent' => '感谢您参与活动：
充值送海信聚好学家庭专区活动。
获得奖励如下：
30万乐豆',
            'mailFileid'  => 0,
            'is_surprise' => 0,
            'is_online'   => 0,
            'is_del'      => 0,
            'sort'        => 11,
            'create_time' => 1459413206,
            'update_time' => 1459413206,
        ],
    10242 =>
        [
            'id'          => 10242,
            'type'        => 2,
            'name'        => '乐视一重礼包',
            'prev'        => 0,
            'goto'        => 0,
            'rooms'       =>
                [
                ],
            'channels'    =>
                [
                    0 => 'letv',
                ],
            'users'       =>
                [
                ],
            'start_time'  => 1460563200,
            'end_time'    => 1460995200,
            'periodName'  => '无',
            'periodTime'  => 0,
            'periodId'    => 0,
            'periodStart' => 0,
            'periodEnd'   => 0,
            'sourceId'    => 223,
            'accode'      => 0,
            'action'      => 'API_GOLD',
            'acttag'      => 'buy_goods',
            'execut'      =>
                [
                    0 =>
                        [
                            'key' => 'usertesk.teskvalue',
                            'exe' => '=',
                            'par' => 'param',
                        ],
                ],
            'condit'      =>
                [
                    0 =>
                        [
                            'key' => 'usertesk.teskvalue',
                            'leg' => '=',
                            'par' => '34',
                        ],
                ],
            'result'      =>
                [
                    0 =>
                        [
                            'key' => 'usertesk.teskdone',
                        ],
                ],
            'target'      => 34,
            'prizeName'   => '1000乐豆',
            'prizes'      =>
                [
                    'coins' => 1000,
                ],
            'mailSubject' => '',
            'mailContent' => '',
            'mailFileid'  => 0,
            'is_surprise' => 0,
            'is_online'   => 0,
            'is_del'      => 0,
            'sort'        => 11,
            'create_time' => 1460560432,
            'update_time' => 1460568246,
        ],
    10243 =>
        [
            'id'          => 10243,
            'type'        => 2,
            'name'        => '乐视二重礼包',
            'prev'        => 0,
            'goto'        => 0,
            'rooms'       =>
                [
                ],
            'channels'    =>
                [
                    0 => 'letv',
                ],
            'users'       =>
                [
                ],
            'start_time'  => 1460563200,
            'end_time'    => 1460995200,
            'periodName'  => '无',
            'periodTime'  => 0,
            'periodId'    => 0,
            'periodStart' => 0,
            'periodEnd'   => 0,
            'sourceId'    => 224,
            'accode'      => 0,
            'action'      => 'API_GOLD',
            'acttag'      => 'buy_goods',
            'execut'      =>
                [
                    0 =>
                        [
                            'key' => 'usertesk.teskvalue',
                            'exe' => '=',
                            'par' => 'param',
                        ],
                ],
            'condit'      =>
                [
                    0 =>
                        [
                            'key' => 'usertesk.teskvalue',
                            'leg' => '=',
                            'par' => '35',
                        ],
                ],
            'result'      =>
                [
                    0 =>
                        [
                            'key' => 'usertesk.teskdone',
                        ],
                ],
            'target'      => 35,
            'prizeName'   => '1000乐豆',
            'prizes'      =>
                [
                    'coins' => 1000,
                ],
            'mailSubject' => '',
            'mailContent' => '',
            'mailFileid'  => 0,
            'is_surprise' => 0,
            'is_online'   => 0,
            'is_del'      => 0,
            'sort'        => 11,
            'create_time' => 1460560674,
            'update_time' => 1460568255,
        ],
    10244 =>
        [
            'id'          => 10244,
            'type'        => 2,
            'name'        => '乐视三重礼包',
            'prev'        => 0,
            'goto'        => 0,
            'rooms'       =>
                [
                ],
            'channels'    =>
                [
                    0 => 'letv',
                ],
            'users'       =>
                [
                ],
            'start_time'  => 1460563200,
            'end_time'    => 1460995200,
            'periodName'  => '无',
            'periodTime'  => 0,
            'periodId'    => 0,
            'periodStart' => 0,
            'periodEnd'   => 0,
            'sourceId'    => 225,
            'accode'      => 0,
            'action'      => 'API_GOLD',
            'acttag'      => 'buy_goods',
            'execut'      =>
                [
                    0 =>
                        [
                            'key' => 'usertesk.teskvalue',
                            'exe' => '=',
                            'par' => 'param',
                        ],
                ],
            'condit'      =>
                [
                    0 =>
                        [
                            'key' => 'usertesk.teskvalue',
                            'leg' => '=',
                            'par' => '36',
                        ],
                ],
            'result'      =>
                [
                    0 =>
                        [
                            'key' => 'usertesk.teskdone',
                        ],
                ],
            'target'      => 36,
            'prizeName'   => '1000乐豆',
            'prizes'      =>
                [
                    'coins' => 1000,
                ],
            'mailSubject' => '',
            'mailContent' => '',
            'mailFileid'  => 0,
            'is_surprise' => 0,
            'is_online'   => 0,
            'is_del'      => 0,
            'sort'        => 11,
            'create_time' => 1460560863,
            'update_time' => 1460568265,
        ],
    10245 =>
        [
            'id'          => 10245,
            'type'        => 2,
            'name'        => '乐视四重礼包',
            'prev'        => 0,
            'goto'        => 0,
            'rooms'       =>
                [
                ],
            'channels'    =>
                [
                    0 => 'letv',
                ],
            'users'       =>
                [
                ],
            'start_time'  => 1460563200,
            'end_time'    => 1460995200,
            'periodName'  => '无',
            'periodTime'  => 0,
            'periodId'    => 0,
            'periodStart' => 0,
            'periodEnd'   => 0,
            'sourceId'    => 226,
            'accode'      => 0,
            'action'      => 'API_GOLD',
            'acttag'      => 'buy_goods',
            'execut'      =>
                [
                    0 =>
                        [
                            'key' => 'usertesk.teskvalue',
                            'exe' => '=',
                            'par' => 'param',
                        ],
                ],
            'condit'      =>
                [
                    0 =>
                        [
                            'key' => 'usertesk.teskvalue',
                            'leg' => '=',
                            'par' => '37',
                        ],
                ],
            'result'      =>
                [
                    0 =>
                        [
                            'key' => 'usertesk.teskdone',
                        ],
                ],
            'target'      => 37,
            'prizeName'   => '1000乐豆',
            'prizes'      =>
                [
                    'coins' => 1000,
                ],
            'mailSubject' => '',
            'mailContent' => '',
            'mailFileid'  => 0,
            'is_surprise' => 0,
            'is_online'   => 0,
            'is_del'      => 0,
            'sort'        => 11,
            'create_time' => 1460561137,
            'update_time' => 1460568275,
        ],
    10246 =>
        [
            'id'          => 10246,
            'type'        => 2,
            'name'        => '赖子玩法新版上线充值30额外送',
            'prev'        => 0,
            'goto'        => 0,
            'rooms'       =>
                [
                ],
            'channels'    =>
                [
                    0 => 'hisense',
                    1 => 'aiyouxi',
                ],
            'users'       =>
                [
                ],
            'start_time'  => 1460649600,
            'end_time'    => 1461340800,
            'periodName'  => '',
            'periodTime'  => 0,
            'periodId'    => 0,
            'periodStart' => 0,
            'periodEnd'   => 0,
            'sourceId'    => 107,
            'accode'      => 0,
            'action'      => 'API_GOLD',
            'acttag'      => 'cost_gold',
            'execut'      =>
                [
                    0 =>
                        [
                            'key' => 'usertesk.teskvalue',
                            'exe' => '=',
                            'par' => 'param',
                        ],
                ],
            'condit'      =>
                [
                    0 =>
                        [
                            'key' => 'usertesk.teskvalue',
                            'leg' => '=',
                            'par' => 30,
                        ],
                ],
            'result'      =>
                [
                    0 =>
                        [
                            'key' => 'usertesk.teskdone',
                        ],
                ],
            'target'      => 30,
            'prizeName'   => '72000乐豆',
            'prizes'      =>
                [
                    'coins' => 72000,
                ],
            'mailSubject' => '赖子玩法新版上线充值30额外送',
            'mailContent' => '感谢参与活动：
赖子玩法新版上线 玩斗地主送北通手柄
您在活动期间首次充值了30元，
请领取额外奖励72000乐豆。',
            'mailFileid'  => 0,
            'is_surprise' => 0,
            'is_online'   => 0,
            'is_del'      => 0,
            'sort'        => 11,
            'create_time' => 1460631397,
            'update_time' => 1460631397,
        ],
    10247 =>
        [
            'id'          => 10247,
            'type'        => 2,
            'name'        => '赖子玩法新版上线充值50额外送',
            'prev'        => 0,
            'goto'        => 0,
            'rooms'       =>
                [
                ],
            'channels'    =>
                [
                    0 => 'hisense',
                    1 => 'aiyouxi',
                ],
            'users'       =>
                [
                ],
            'start_time'  => 1460649600,
            'end_time'    => 1461340800,
            'periodName'  => '',
            'periodTime'  => 0,
            'periodId'    => 0,
            'periodStart' => 0,
            'periodEnd'   => 0,
            'sourceId'    => 108,
            'accode'      => 0,
            'action'      => 'API_GOLD',
            'acttag'      => 'cost_gold',
            'execut'      =>
                [
                    0 =>
                        [
                            'key' => 'usertesk.teskvalue',
                            'exe' => '=',
                            'par' => 'param',
                        ],
                ],
            'condit'      =>
                [
                    0 =>
                        [
                            'key' => 'usertesk.teskvalue',
                            'leg' => '=',
                            'par' => 50,
                        ],
                ],
            'result'      =>
                [
                    0 =>
                        [
                            'key' => 'usertesk.teskdone',
                        ],
                ],
            'target'      => 50,
            'prizeName'   => '12万乐豆',
            'prizes'      =>
                [
                    'coins' => 120000,
                ],
            'mailSubject' => '赖子玩法新版上线充值50额外送',
            'mailContent' => '感谢参与活动：
赖子玩法新版上线 玩斗地主送北通手柄
您在活动期间首次充值了30元，
请领取额外奖励12万乐豆。',
            'mailFileid'  => 0,
            'is_surprise' => 0,
            'is_online'   => 0,
            'is_del'      => 0,
            'sort'        => 11,
            'create_time' => 1460631564,
            'update_time' => 1460631564,
        ],
    10248 =>
        [
            'id'          => 10248,
            'type'        => 2,
            'name'        => '赖子玩法新版上线充值100额外送',
            'prev'        => 0,
            'goto'        => 0,
            'rooms'       =>
                [
                ],
            'channels'    =>
                [
                    0 => 'hisense',
                    1 => 'aiyouxi',
                ],
            'users'       =>
                [
                ],
            'start_time'  => 1460649600,
            'end_time'    => 1461340800,
            'periodName'  => '',
            'periodTime'  => 0,
            'periodId'    => 0,
            'periodStart' => 0,
            'periodEnd'   => 0,
            'sourceId'    => 109,
            'accode'      => 0,
            'action'      => 'API_GOLD',
            'acttag'      => 'cost_gold',
            'execut'      =>
                [
                    0 =>
                        [
                            'key' => 'usertesk.teskvalue',
                            'exe' => '=',
                            'par' => 'param',
                        ],
                ],
            'condit'      =>
                [
                    0 =>
                        [
                            'key' => 'usertesk.teskvalue',
                            'leg' => '=',
                            'par' => 100,
                        ],
                ],
            'result'      =>
                [
                    0 =>
                        [
                            'key' => 'usertesk.teskdone',
                        ],
                ],
            'target'      => 100,
            'prizeName'   => '24万乐豆',
            'prizes'      =>
                [
                    'coins' => 240000,
                ],
            'mailSubject' => '赖子玩法新版上线充值100额外送',
            'mailContent' => '感谢参与活动：
赖子玩法新版上线 玩斗地主送北通手柄
您在活动期间首次充值了100元，
请领取额外奖励24万乐豆。',
            'mailFileid'  => 0,
            'is_surprise' => 0,
            'is_online'   => 0,
            'is_del'      => 0,
            'sort'        => 11,
            'create_time' => 1460631709,
            'update_time' => 1460631709,
        ],
    10249 =>
        [
            'id'          => 10249,
            'type'        => 2,
            'name'        => '购买富翁套装1',
            'prev'        => 0,
            'goto'        => 0,
            'rooms'       =>
                [
                ],
            'channels'    =>
                [
                ],
            'users'       =>
                [
                ],
            'start_time'  => 1461772800,
            'end_time'    => 1462464000,
            'periodName'  => '',
            'periodTime'  => 0,
            'periodId'    => 0,
            'periodStart' => 0,
            'periodEnd'   => 0,
            'sourceId'    => 227,
            'accode'      => 0,
            'action'      => 'API_GOLD',
            'acttag'      => 'buy_goods',
            'execut'      =>
                [
                    0 =>
                        [
                            'key' => 'usertesk.teskvalue',
                            'exe' => '=',
                            'par' => 'param',
                        ],
                ],
            'condit'      =>
                [
                    0 =>
                        [
                            'key' => 'usertesk.teskvalue',
                            'leg' => '=',
                            'par' => '4',
                        ],
                ],
            'result'      =>
                [
                    0 =>
                        [
                            'key' => 'usertesk.teskdone',
                        ],
                ],
            'target'      => 0,
            'prizeName'   => '18万乐豆',
            'prizes'      =>
                [
                    'coins' => 180000,
                ],
            'mailSubject' => '购买富翁套装，奖励18万乐豆',
            'mailContent' => '恭喜您完成任务：
五一期间，购买富翁套装。
奖励乐豆18万。请点击领取。
还有更多活动等你哦。',
            'mailFileid'  => 0,
            'is_surprise' => 0,
            'is_online'   => 0,
            'is_del'      => 0,
            'sort'        => 11,
            'create_time' => 1461749052,
            'update_time' => 1461749052,
        ],
    10250 =>
        [
            'id'          => 10250,
            'type'        => 2,
            'name'        => '购买富翁套装2',
            'prev'        => 0,
            'goto'        => 0,
            'rooms'       =>
                [
                ],
            'channels'    =>
                [
                ],
            'users'       =>
                [
                ],
            'start_time'  => 1461772800,
            'end_time'    => 1462464000,
            'periodName'  => '',
            'periodTime'  => 0,
            'periodId'    => 0,
            'periodStart' => 0,
            'periodEnd'   => 0,
            'sourceId'    => 228,
            'accode'      => 0,
            'action'      => 'API_GOLD',
            'acttag'      => 'buy_goods',
            'execut'      =>
                [
                    0 =>
                        [
                            'key' => 'usertesk.teskvalue',
                            'exe' => '=',
                            'par' => 'param',
                        ],
                ],
            'condit'      =>
                [
                    0 =>
                        [
                            'key' => 'usertesk.teskvalue',
                            'leg' => '=',
                            'par' => '39',
                        ],
                ],
            'result'      =>
                [
                    0 =>
                        [
                            'key' => 'usertesk.teskdone',
                        ],
                ],
            'target'      => 0,
            'prizeName'   => '18万乐豆',
            'prizes'      =>
                [
                    'coins' => 180000,
                ],
            'mailSubject' => '购买富翁套装，奖励18万乐豆',
            'mailContent' => '恭喜您完成任务：
五一期间，购买富翁套装。
奖励乐豆18万。请点击领取。
还有更多活动等你哦。',
            'mailFileid'  => 0,
            'is_surprise' => 0,
            'is_online'   => 0,
            'is_del'      => 0,
            'sort'        => 11,
            'create_time' => 1461749178,
            'update_time' => 1461749178,
        ],
    10251 =>
        [
            'id'          => 10251,
            'type'        => 2,
            'name'        => '五一期间累计消费200乐币',
            'prev'        => 0,
            'goto'        => 0,
            'rooms'       =>
                [
                ],
            'channels'    =>
                [
                    0 => 'yishitengjx',
                ],
            'users'       =>
                [
                ],
            'start_time'  => 1461772800,
            'end_time'    => 1462464000,
            'periodName'  => '',
            'periodTime'  => 0,
            'periodId'    => 0,
            'periodStart' => 0,
            'periodEnd'   => 0,
            'sourceId'    => 203,
            'accode'      => 0,
            'action'      => 'API_GOLD',
            'acttag'      => 'cost_gold',
            'execut'      =>
                [
                    0 =>
                        [
                            'key' => 'usertesk.teskvalue',
                            'exe' => '+',
                            'par' => 'param',
                        ],
                ],
            'condit'      =>
                [
                    0 =>
                        [
                            'key' => 'usertesk.teskvalue',
                            'leg' => '>=',
                            'par' => 200,
                        ],
                ],
            'result'      =>
                [
                    0 =>
                        [
                            'key' => 'usertesk.teskdone',
                        ],
                ],
            'target'      => 200,
            'prizeName'   => '永久记牌器',
            'prizes'      =>
                [
                    'propItems' =>
                        [
                            5 =>
                                [
                                    'id'   => 5,
                                    'name' => '记牌器',
                                    'cd'   => '2',
                                    'num'  => 1,
                                    'ext'  => 0,
                                ],
                        ],
                ],
            'mailSubject' => '五一期间累计消费200乐币',
            'mailContent' => '恭喜您完成任务：
五一期间，累计消费200乐币。
奖励永久记牌器。请点击领取。
还有更多活动等你哦。',
            'mailFileid'  => 0,
            'is_surprise' => 0,
            'is_online'   => 0,
            'is_del'      => 0,
            'sort'        => 11,
            'create_time' => 1461749308,
            'update_time' => 1461749308,
        ],
    10252 =>
        [
            'id'          => 10252,
            'type'        => 2,
            'name'        => '购买地主礼包',
            'prev'        => 0,
            'goto'        => 0,
            'rooms'       =>
                [
                ],
            'channels'    =>
                [
                    0 => 'shiboyun',
                ],
            'users'       =>
                [
                ],
            'start_time'  => 0,
            'end_time'    => 0,
            'periodName'  => '每天',
            'periodTime'  => 86400,
            'periodId'    => 0,
            'periodStart' => 1462377600,
            'periodEnd'   => 1462463999,
            'sourceId'    => 229,
            'accode'      => 0,
            'action'      => 'API_GOLD',
            'acttag'      => 'buy_goods',
            'execut'      =>
                [
                    0 =>
                        [
                            'key' => 'usertesk.teskvalue',
                            'exe' => '=',
                            'par' => 'param',
                        ],
                ],
            'condit'      =>
                [
                    0 =>
                        [
                            'key' => 'usertesk.teskvalue',
                            'leg' => '=',
                            'par' => '40',
                        ],
                ],
            'result'      =>
                [
                    0 =>
                        [
                            'key' => 'usertesk.teskdone',
                        ],
                ],
            'target'      => 1,
            'prizeName'   => '1000乐豆',
            'prizes'      =>
                [
                    'coins' => 1000,
                ],
            'mailSubject' => '',
            'mailContent' => '',
            'mailFileid'  => 0,
            'is_surprise' => 0,
            'is_online'   => 1,
            'is_del'      => 0,
            'sort'        => 1,
            'create_time' => 1462365780,
            'update_time' => 1462786375,
        ],
    10253 =>
        [
            'id'          => 10253,
            'type'        => 2,
            'name'        => '购买农民礼包',
            'prev'        => 0,
            'goto'        => 0,
            'rooms'       =>
                [
                ],
            'channels'    =>
                [
                    0 => 'shiboyun',
                ],
            'users'       =>
                [
                ],
            'start_time'  => 0,
            'end_time'    => 0,
            'periodName'  => '每天',
            'periodTime'  => 86400,
            'periodId'    => 0,
            'periodStart' => 1462377600,
            'periodEnd'   => 1462463999,
            'sourceId'    => 230,
            'accode'      => 0,
            'action'      => 'API_GOLD',
            'acttag'      => 'buy_goods',
            'execut'      =>
                [
                    0 =>
                        [
                            'key' => 'usertesk.teskvalue',
                            'exe' => '=',
                            'par' => 'param',
                        ],
                ],
            'condit'      =>
                [
                    0 =>
                        [
                            'key' => 'usertesk.teskvalue',
                            'leg' => '=',
                            'par' => '41',
                        ],
                ],
            'result'      =>
                [
                    0 =>
                        [
                            'key' => 'usertesk.teskdone',
                        ],
                ],
            'target'      => 1,
            'prizeName'   => '1000乐豆',
            'prizes'      =>
                [
                    'coins' => 1000,
                ],
            'mailSubject' => '',
            'mailContent' => '',
            'mailFileid'  => 0,
            'is_surprise' => 0,
            'is_online'   => 1,
            'is_del'      => 0,
            'sort'        => 1,
            'create_time' => 1462365908,
            'update_time' => 1462786473,
        ],
    10254 =>
        [
            'id'          => 10254,
            'type'        => 2,
            'name'        => '',
            'prev'        => 0,
            'goto'        => 0,
            'rooms'       =>
                [
                ],
            'channels'    =>
                [
                ],
            'users'       =>
                [
                ],
            'start_time'  => 1462550400,
            'end_time'    => 1462809600,
            'periodName'  => '',
            'periodTime'  => 0,
            'periodId'    => 0,
            'periodStart' => 0,
            'periodEnd'   => 0,
            'sourceId'    => 141,
            'accode'      => 0,
            'action'      => 'API_GOLD',
            'acttag'      => 'cost_gold',
            'execut'      =>
                [
                    0 =>
                        [
                            'key' => 'usertesk.teskvalue',
                            'exe' => '+',
                            'par' => 'param',
                        ],
                ],
            'condit'      =>
                [
                    0 =>
                        [
                            'key' => 'usertesk.teskvalue',
                            'leg' => '>=',
                            'par' => 10,
                        ],
                ],
            'result'      =>
                [
                    0 =>
                        [
                            'key' => 'usertesk.teskdone',
                        ],
                ],
            'target'      => 10,
            'prizeName'   => '1千乐券',
            'prizes'      =>
                [
                    'coupon' => 1000,
                ],
            'mailSubject' => '母亲节活动累计消费达10乐币',
            'mailContent' => '感谢您参与母亲节活动，
您的累计消费达到10乐币，
奖励1千乐券。',
            'mailFileid'  => 0,
            'is_surprise' => 0,
            'is_online'   => 0,
            'is_del'      => 0,
            'sort'        => 99,
            'create_time' => 1462439657,
            'update_time' => 1462439657,
        ],
    10255 =>
        [
            'id'          => 10255,
            'type'        => 2,
            'name'        => '母亲节累计消费达10乐币',
            'prev'        => 0,
            'goto'        => 0,
            'rooms'       =>
                [
                ],
            'channels'    =>
                [
                ],
            'users'       =>
                [
                ],
            'start_time'  => 1462550400,
            'end_time'    => 1462896000,
            'periodName'  => '',
            'periodTime'  => 0,
            'periodId'    => 0,
            'periodStart' => 0,
            'periodEnd'   => 0,
            'sourceId'    => 141,
            'accode'      => 0,
            'action'      => 'API_GOLD',
            'acttag'      => 'cost_gold',
            'execut'      =>
                [
                    0 =>
                        [
                            'key' => 'usertesk.teskvalue',
                            'exe' => '+',
                            'par' => 'param',
                        ],
                ],
            'condit'      =>
                [
                    0 =>
                        [
                            'key' => 'usertesk.teskvalue',
                            'leg' => '>=',
                            'par' => 10,
                        ],
                ],
            'result'      =>
                [
                    0 =>
                        [
                            'key' => 'usertesk.teskdone',
                        ],
                ],
            'target'      => 10,
            'prizeName'   => '1千乐券',
            'prizes'      =>
                [
                    'coupon' => 1000,
                ],
            'mailSubject' => '母亲节活动累计消费达10乐币',
            'mailContent' => '感谢您参与母亲节活动，
您的累计消费达到10乐币，
奖励1千乐券。',
            'mailFileid'  => 0,
            'is_surprise' => 0,
            'is_online'   => 0,
            'is_del'      => 0,
            'sort'        => 11,
            'create_time' => 1462439667,
            'update_time' => 1462439705,
        ],
    10256 =>
        [
            'id'          => 10256,
            'type'        => 2,
            'name'        => '母亲节累计消费达50乐币',
            'prev'        => 0,
            'goto'        => 0,
            'rooms'       =>
                [
                ],
            'channels'    =>
                [
                ],
            'users'       =>
                [
                ],
            'start_time'  => 1462550400,
            'end_time'    => 1462896000,
            'periodName'  => '',
            'periodTime'  => 0,
            'periodId'    => 0,
            'periodStart' => 0,
            'periodEnd'   => 0,
            'sourceId'    => 94,
            'accode'      => 0,
            'action'      => 'API_GOLD',
            'acttag'      => 'cost_gold',
            'execut'      =>
                [
                    0 =>
                        [
                            'key' => 'usertesk.teskvalue',
                            'exe' => '+',
                            'par' => 'param',
                        ],
                ],
            'condit'      =>
                [
                    0 =>
                        [
                            'key' => 'usertesk.teskvalue',
                            'leg' => '>=',
                            'par' => 50,
                        ],
                ],
            'result'      =>
                [
                    0 =>
                        [
                            'key' => 'usertesk.teskdone',
                        ],
                ],
            'target'      => 50,
            'prizeName'   => '6千乐券',
            'prizes'      =>
                [
                    'coupon' => 6000,
                ],
            'mailSubject' => '母亲节活动累计消费达50乐币',
            'mailContent' => '感谢您参与母亲节活动，
您的累计消费达到50乐币，
奖励6千乐券。',
            'mailFileid'  => 0,
            'is_surprise' => 0,
            'is_online'   => 0,
            'is_del'      => 0,
            'sort'        => 11,
            'create_time' => 1462439919,
            'update_time' => 1462439919,
        ],
    10257 =>
        [
            'id'          => 10257,
            'type'        => 2,
            'name'        => '母亲节累计消费达100乐币',
            'prev'        => 0,
            'goto'        => 0,
            'rooms'       =>
                [
                ],
            'channels'    =>
                [
                ],
            'users'       =>
                [
                ],
            'start_time'  => 1462550400,
            'end_time'    => 1462896000,
            'periodName'  => '',
            'periodTime'  => 0,
            'periodId'    => 0,
            'periodStart' => 0,
            'periodEnd'   => 0,
            'sourceId'    => 32,
            'accode'      => 0,
            'action'      => 'API_GOLD',
            'acttag'      => 'cost_gold',
            'execut'      =>
                [
                    0 =>
                        [
                            'key' => 'usertesk.teskvalue',
                            'exe' => '+',
                            'par' => 'param',
                        ],
                ],
            'condit'      =>
                [
                    0 =>
                        [
                            'key' => 'usertesk.teskvalue',
                            'leg' => '>=',
                            'par' => 100,
                        ],
                ],
            'result'      =>
                [
                    0 =>
                        [
                            'key' => 'usertesk.teskdone',
                        ],
                ],
            'target'      => 100,
            'prizeName'   => '15000乐券',
            'prizes'      =>
                [
                    'coupon' => 15000,
                ],
            'mailSubject' => '母亲节活动累计消费达100乐币',
            'mailContent' => '感谢您参与母亲节活动，
您的累计消费达到100乐币，
奖励15000乐券。',
            'mailFileid'  => 0,
            'is_surprise' => 0,
            'is_online'   => 0,
            'is_del'      => 0,
            'sort'        => 11,
            'create_time' => 1462440092,
            'update_time' => 1462440092,
        ],
    10258 =>
        [
            'id'          => 10258,
            'type'        => 2,
            'name'        => '母亲节累计消费达500乐币',
            'prev'        => 0,
            'goto'        => 0,
            'rooms'       =>
                [
                ],
            'channels'    =>
                [
                ],
            'users'       =>
                [
                ],
            'start_time'  => 1462550400,
            'end_time'    => 1462896000,
            'periodName'  => '',
            'periodTime'  => 0,
            'periodId'    => 0,
            'periodStart' => 0,
            'periodEnd'   => 0,
            'sourceId'    => 4,
            'accode'      => 0,
            'action'      => 'API_GOLD',
            'acttag'      => 'cost_gold',
            'execut'      =>
                [
                    0 =>
                        [
                            'key' => 'usertesk.teskvalue',
                            'exe' => '+',
                            'par' => 'param',
                        ],
                ],
            'condit'      =>
                [
                    0 =>
                        [
                            'key' => 'usertesk.teskvalue',
                            'leg' => '>=',
                            'par' => 500,
                        ],
                ],
            'result'      =>
                [
                    0 =>
                        [
                            'key' => 'usertesk.teskdone',
                        ],
                ],
            'target'      => 500,
            'prizeName'   => '9万乐券',
            'prizes'      =>
                [
                    'coupon' => 90000,
                ],
            'mailSubject' => '母亲节活动累计消费达500乐币',
            'mailContent' => '感谢您参与母亲节活动，
您的累计消费达到500乐币，
奖励9万乐券。',
            'mailFileid'  => 0,
            'is_surprise' => 0,
            'is_online'   => 0,
            'is_del'      => 0,
            'sort'        => 11,
            'create_time' => 1462440233,
            'update_time' => 1462440233,
        ],
    10259 =>
        [
            'id'          => 10259,
            'type'        => 2,
            'name'        => '母亲节累计消费达1000乐币',
            'prev'        => 0,
            'goto'        => 0,
            'rooms'       =>
                [
                ],
            'channels'    =>
                [
                ],
            'users'       =>
                [
                ],
            'start_time'  => 1462550400,
            'end_time'    => 1462896000,
            'periodName'  => '',
            'periodTime'  => 0,
            'periodId'    => 0,
            'periodStart' => 0,
            'periodEnd'   => 0,
            'sourceId'    => 114,
            'accode'      => 0,
            'action'      => 'API_GOLD',
            'acttag'      => 'cost_gold',
            'execut'      =>
                [
                    0 =>
                        [
                            'key' => 'usertesk.teskvalue',
                            'exe' => '+',
                            'par' => 'param',
                        ],
                ],
            'condit'      =>
                [
                    0 =>
                        [
                            'key' => 'usertesk.teskvalue',
                            'leg' => '>=',
                            'par' => 1000,
                        ],
                ],
            'result'      =>
                [
                    0 =>
                        [
                            'key' => 'usertesk.teskdone',
                        ],
                ],
            'target'      => 1000,
            'prizeName'   => '20万乐券',
            'prizes'      =>
                [
                    'coupon' => 200000,
                ],
            'mailSubject' => '母亲节活动累计消费达1000乐币',
            'mailContent' => '感谢您参与母亲节活动，
您的累计消费达到1000乐币，
奖励20万乐券。',
            'mailFileid'  => 0,
            'is_surprise' => 0,
            'is_online'   => 0,
            'is_del'      => 0,
            'sort'        => 11,
            'create_time' => 1462440370,
            'update_time' => 1462440370,
        ],
    10260 =>
        [
            'id'          => 10260,
            'type'        => 2,
            'name'        => '燃烧五月累计消费达10乐币',
            'prev'        => 0,
            'goto'        => 0,
            'rooms'       =>
                [
                ],
            'channels'    =>
                [
                ],
            'users'       =>
                [
                ],
            'start_time'  => 1462809600,
            'end_time'    => 1464796800,
            'periodName'  => '',
            'periodTime'  => 0,
            'periodId'    => 0,
            'periodStart' => 0,
            'periodEnd'   => 0,
            'sourceId'    => 31,
            'accode'      => 0,
            'action'      => 'API_GOLD',
            'acttag'      => 'cost_gold',
            'execut'      =>
                [
                    0 =>
                        [
                            'key' => 'usertesk.teskvalue',
                            'exe' => '+',
                            'par' => 'param',
                        ],
                ],
            'condit'      =>
                [
                    0 =>
                        [
                            'key' => 'usertesk.teskvalue',
                            'leg' => '>=',
                            'par' => 10,
                        ],
                ],
            'result'      =>
                [
                    0 =>
                        [
                            'key' => 'usertesk.teskdone',
                        ],
                ],
            'target'      => 10,
            'prizeName'   => '1千乐券',
            'prizes'      =>
                [
                    'coupon' => 1000,
                ],
            'mailSubject' => '燃烧五月累计消费达10乐币',
            'mailContent' => '感谢您参与“燃烧五月”活动，
您的累计消费达到10乐币，
奖励1千乐券。',
            'mailFileid'  => 0,
            'is_surprise' => 0,
            'is_online'   => 0,
            'is_del'      => 0,
            'sort'        => 11,
            'create_time' => 1462785976,
            'update_time' => 1462785976,
        ],
    10261 =>
        [
            'id'          => 10261,
            'type'        => 2,
            'name'        => '燃烧五月累计消费达50乐币',
            'prev'        => 0,
            'goto'        => 0,
            'rooms'       =>
                [
                ],
            'channels'    =>
                [
                ],
            'users'       =>
                [
                ],
            'start_time'  => 1462809600,
            'end_time'    => 1464796800,
            'periodName'  => '',
            'periodTime'  => 0,
            'periodId'    => 0,
            'periodStart' => 0,
            'periodEnd'   => 0,
            'sourceId'    => 94,
            'accode'      => 0,
            'action'      => 'API_GOLD',
            'acttag'      => 'cost_gold',
            'execut'      =>
                [
                    0 =>
                        [
                            'key' => 'usertesk.teskvalue',
                            'exe' => '+',
                            'par' => 'param',
                        ],
                ],
            'condit'      =>
                [
                    0 =>
                        [
                            'key' => 'usertesk.teskvalue',
                            'leg' => '>=',
                            'par' => 50,
                        ],
                ],
            'result'      =>
                [
                    0 =>
                        [
                            'key' => 'usertesk.teskdone',
                        ],
                ],
            'target'      => 50,
            'prizeName'   => '6千乐券',
            'prizes'      =>
                [
                    'coupon' => 6000,
                ],
            'mailSubject' => '燃烧五月累计消费达50乐币',
            'mailContent' => '感谢您参与“燃烧五月”活动，
您的累计消费达到50乐币，
奖励6千乐券。',
            'mailFileid'  => 0,
            'is_surprise' => 0,
            'is_online'   => 0,
            'is_del'      => 0,
            'sort'        => 11,
            'create_time' => 1462786139,
            'update_time' => 1462786139,
        ],
    10262 =>
        [
            'id'          => 10262,
            'type'        => 2,
            'name'        => '燃烧五月累计消费达100乐币',
            'prev'        => 0,
            'goto'        => 0,
            'rooms'       =>
                [
                ],
            'channels'    =>
                [
                ],
            'users'       =>
                [
                ],
            'start_time'  => 1462809600,
            'end_time'    => 1464796800,
            'periodName'  => '',
            'periodTime'  => 0,
            'periodId'    => 0,
            'periodStart' => 0,
            'periodEnd'   => 0,
            'sourceId'    => 32,
            'accode'      => 0,
            'action'      => 'API_GOLD',
            'acttag'      => 'cost_gold',
            'execut'      =>
                [
                    0 =>
                        [
                            'key' => 'usertesk.teskvalue',
                            'exe' => '+',
                            'par' => 'param',
                        ],
                ],
            'condit'      =>
                [
                    0 =>
                        [
                            'key' => 'usertesk.teskvalue',
                            'leg' => '>=',
                            'par' => 100,
                        ],
                ],
            'result'      =>
                [
                    0 =>
                        [
                            'key' => 'usertesk.teskdone',
                        ],
                ],
            'target'      => 100,
            'prizeName'   => '15000乐券',
            'prizes'      =>
                [
                    'coupon' => 15000,
                ],
            'mailSubject' => '燃烧五月累计消费达100乐币',
            'mailContent' => '感谢您参与母亲节活动，
您的累计消费达到100乐币，
奖励15000乐券。',
            'mailFileid'  => 0,
            'is_surprise' => 0,
            'is_online'   => 0,
            'is_del'      => 0,
            'sort'        => 11,
            'create_time' => 1462786324,
            'update_time' => 1462786324,
        ],
    10263 =>
        [
            'id'          => 10263,
            'type'        => 2,
            'name'        => '燃烧五月累计消费达500乐币',
            'prev'        => 0,
            'goto'        => 0,
            'rooms'       =>
                [
                ],
            'channels'    =>
                [
                ],
            'users'       =>
                [
                ],
            'start_time'  => 1462809600,
            'end_time'    => 1464796800,
            'periodName'  => '',
            'periodTime'  => 0,
            'periodId'    => 0,
            'periodStart' => 0,
            'periodEnd'   => 0,
            'sourceId'    => 4,
            'accode'      => 0,
            'action'      => 'API_GOLD',
            'acttag'      => 'cost_gold',
            'execut'      =>
                [
                    0 =>
                        [
                            'key' => 'usertesk.teskvalue',
                            'exe' => '+',
                            'par' => 'param',
                        ],
                ],
            'condit'      =>
                [
                    0 =>
                        [
                            'key' => 'usertesk.teskvalue',
                            'leg' => '>=',
                            'par' => 500,
                        ],
                ],
            'result'      =>
                [
                    0 =>
                        [
                            'key' => 'usertesk.teskdone',
                        ],
                ],
            'target'      => 500,
            'prizeName'   => '9万乐券',
            'prizes'      =>
                [
                    'coupon' => 90000,
                ],
            'mailSubject' => '燃烧五月累计消费达500乐币',
            'mailContent' => '恭喜您参与燃烧五月活动达标，
您的累计消费达到500乐币，
奖励9万乐券。',
            'mailFileid'  => 0,
            'is_surprise' => 0,
            'is_online'   => 0,
            'is_del'      => 0,
            'sort'        => 11,
            'create_time' => 1462786672,
            'update_time' => 1462786672,
        ],
    10264 =>
        [
            'id'          => 10264,
            'type'        => 2,
            'name'        => '燃烧五月累计消费达1000乐币',
            'prev'        => 0,
            'goto'        => 0,
            'rooms'       =>
                [
                ],
            'channels'    =>
                [
                ],
            'users'       =>
                [
                ],
            'start_time'  => 1462809600,
            'end_time'    => 1464796800,
            'periodName'  => '',
            'periodTime'  => 0,
            'periodId'    => 0,
            'periodStart' => 0,
            'periodEnd'   => 0,
            'sourceId'    => 114,
            'accode'      => 0,
            'action'      => 'API_GOLD',
            'acttag'      => 'cost_gold',
            'execut'      =>
                [
                    0 =>
                        [
                            'key' => 'usertesk.teskvalue',
                            'exe' => '+',
                            'par' => 'param',
                        ],
                ],
            'condit'      =>
                [
                    0 =>
                        [
                            'key' => 'usertesk.teskvalue',
                            'leg' => '>=',
                            'par' => 1000,
                        ],
                ],
            'result'      =>
                [
                    0 =>
                        [
                            'key' => 'usertesk.teskdone',
                        ],
                ],
            'target'      => 1000,
            'prizeName'   => '20万乐券',
            'prizes'      =>
                [
                    'coupon' => 200000,
                ],
            'mailSubject' => '燃烧五月累计消费达1000乐币',
            'mailContent' => '恭喜您参与燃烧五月活动达标，
您的累计消费达到1000乐币，
奖励20万乐券。',
            'mailFileid'  => 0,
            'is_surprise' => 0,
            'is_online'   => 0,
            'is_del'      => 0,
            'sort'        => 11,
            'create_time' => 1462786836,
            'update_time' => 1462786836,
        ],
    10265 =>
        [
            'id'          => 10265,
            'type'        => 0,
            'name'        => '儿童节每天打16局获得666乐券',
            'prev'        => 0,
            'goto'        => 1,
            'rooms'       =>
                [
                ],
            'channels'    =>
                [
                ],
            'users'       =>
                [
                ],
            'start_time'  => 1464710400,
            'end_time'    => 1465056000,
            'periodName'  => '每天',
            'periodTime'  => 86400,
            'periodId'    => 0,
            'periodStart' => 1464710400,
            'periodEnd'   => 1464796799,
            'sourceId'    => 233,
            'accode'      => 0,
            'action'      => 'GAME_OVER',
            'acttag'      => 'game_over',
            'execut'      =>
                [
                    0 =>
                        [
                            'key' => 'usertesk.teskvalue',
                            'exe' => '+',
                            'par' => 1,
                        ],
                ],
            'condit'      =>
                [
                    0 =>
                        [
                            'key' => 'usertesk.teskvalue',
                            'leg' => '>=',
                            'par' => 16,
                        ],
                ],
            'result'      =>
                [
                    0 =>
                        [
                            'key' => 'usertesk.teskdone',
                        ],
                ],
            'target'      => 16,
            'prizeName'   => '666乐券',
            'prizes'      =>
                [
                    'coupon' => 666,
                ],
            'mailSubject' => '',
            'mailContent' => '',
            'mailFileid'  => 0,
            'is_surprise' => 0,
            'is_online'   => 0,
            'is_del'      => 0,
            'sort'        => 11,
            'create_time' => 1464638102,
            'update_time' => 1464638134,
        ],
    10266 =>
        [
            'id'          => 10266,
            'type'        => 0,
            'name'        => '完成5局牌局(初级场)',
            'prev'        => 0,
            'goto'        => 1,
            'rooms'       =>
                [
                    0 => '1001',
                ],
            'channels'    =>
                [
                ],
            'users'       =>
                [
                ],
            'start_time'  => 0,
            'end_time'    => 0,
            'periodName'  => '每天',
            'periodTime'  => 86400,
            'periodId'    => 0,
            'periodStart' => 1464624000,
            'periodEnd'   => 1464710399,
            'sourceId'    => 231,
            'accode'      => 0,
            'action'      => 'GAME_OVER',
            'acttag'      => 'game_over',
            'execut'      =>
                [
                    0 =>
                        [
                            'key' => 'usertesk.teskvalue',
                            'exe' => '+',
                            'par' => 1,
                        ],
                ],
            'condit'      =>
                [
                    0 =>
                        [
                            'key' => 'usertesk.teskvalue',
                            'leg' => '>=',
                            'par' => 5,
                        ],
                ],
            'result'      =>
                [
                    0 =>
                        [
                            'key' => 'usertesk.teskdone',
                            'exe' => '=',
                            'par' => 0,
                        ],
                    1 =>
                        [
                            'key' => 'usertesk.teskstate',
                            'exe' => '=',
                            'par' => 3,
                        ],
                    2 =>
                        [
                            'key' => 'usertesk.teskvalue',
                            'exe' => '=',
                            'par' => 0,
                        ],
                ],
            'target'      => 5,
            'prizeName'   => '25乐券',
            'prizes'      =>
                [
                    'coupon' => 25,
                ],
            'mailSubject' => '',
            'mailContent' => '',
            'mailFileid'  => 0,
            'is_surprise' => 0,
            'is_online'   => 1,
            'is_del'      => 0,
            'sort'        => 1,
            'create_time' => 1464641447,
            'update_time' => 1464641447,
        ],
    10267 =>
        [
            'id'          => 10267,
            'type'        => 0,
            'name'        => '完成5局牌局 (新手场)',
            'prev'        => 0,
            'goto'        => 1,
            'rooms'       =>
                [
                    0 => '1000',
                ],
            'channels'    =>
                [
                ],
            'users'       =>
                [
                ],
            'start_time'  => 0,
            'end_time'    => 0,
            'periodName'  => '每天',
            'periodTime'  => 86400,
            'periodId'    => 0,
            'periodStart' => 1464624000,
            'periodEnd'   => 1464710399,
            'sourceId'    => 231,
            'accode'      => 0,
            'action'      => 'GAME_OVER',
            'acttag'      => 'game_over',
            'execut'      =>
                [
                    0 =>
                        [
                            'key' => 'usertesk.teskvalue',
                            'exe' => '+',
                            'par' => 1,
                        ],
                ],
            'condit'      =>
                [
                    0 =>
                        [
                            'key' => 'usertesk.teskvalue',
                            'leg' => '>=',
                            'par' => 5,
                        ],
                ],
            'result'      =>
                [
                    0 =>
                        [
                            'key' => 'usertesk.teskdone',
                            'exe' => '=',
                            'par' => 0,
                        ],
                    1 =>
                        [
                            'key' => 'usertesk.teskstate',
                            'exe' => '=',
                            'par' => 3,
                        ],
                    2 =>
                        [
                            'key' => 'usertesk.teskvalue',
                            'exe' => '=',
                            'par' => 0,
                        ],
                ],
            'target'      => 5,
            'prizeName'   => '10乐券',
            'prizes'      =>
                [
                    'coupon' => 10,
                ],
            'mailSubject' => '',
            'mailContent' => '',
            'mailFileid'  => 0,
            'is_surprise' => 0,
            'is_online'   => 1,
            'is_del'      => 0,
            'sort'        => 1,
            'create_time' => 1464641797,
            'update_time' => 1464641797,
        ],
    10268 =>
        [
            'id'          => 10268,
            'type'        => 0,
            'name'        => '每天完成5局游戏(中级场)',
            'prev'        => 0,
            'goto'        => 1,
            'rooms'       =>
                [
                    0 => '1002',
                ],
            'channels'    =>
                [
                ],
            'users'       =>
                [
                ],
            'start_time'  => 0,
            'end_time'    => 0,
            'periodName'  => '每天',
            'periodTime'  => 86400,
            'periodId'    => 0,
            'periodStart' => 1464624000,
            'periodEnd'   => 1464710399,
            'sourceId'    => 231,
            'accode'      => 0,
            'action'      => 'GAME_OVER',
            'acttag'      => 'game_over',
            'execut'      =>
                [
                    0 =>
                        [
                            'key' => 'usertesk.teskvalue',
                            'exe' => '+',
                            'par' => 1,
                        ],
                ],
            'condit'      =>
                [
                    0 =>
                        [
                            'key' => 'usertesk.teskvalue',
                            'leg' => '>=',
                            'par' => 5,
                        ],
                ],
            'result'      =>
                [
                    0 =>
                        [
                            'key' => 'usertesk.teskdone',
                            'exe' => '=',
                            'par' => 0,
                        ],
                    1 =>
                        [
                            'key' => 'usertesk.teskstate',
                            'exe' => '=',
                            'par' => 3,
                        ],
                    2 =>
                        [
                            'key' => 'usertesk.teskvalue',
                            'exe' => '=',
                            'par' => 0,
                        ],
                ],
            'target'      => 5,
            'prizeName'   => '50乐券',
            'prizes'      =>
                [
                    'coupon' => 50,
                ],
            'mailSubject' => '',
            'mailContent' => '',
            'mailFileid'  => 0,
            'is_surprise' => 0,
            'is_online'   => 1,
            'is_del'      => 0,
            'sort'        => 1,
            'create_time' => 1464641874,
            'update_time' => 1464641874,
        ],
    10269 =>
        [
            'id'          => 10269,
            'type'        => 0,
            'name'        => '完成5局牌局 (高级场 )',
            'prev'        => 0,
            'goto'        => 1,
            'rooms'       =>
                [
                    0 => '1003',
                ],
            'channels'    =>
                [
                ],
            'users'       =>
                [
                ],
            'start_time'  => 0,
            'end_time'    => 0,
            'periodName'  => '每天',
            'periodTime'  => 86400,
            'periodId'    => 0,
            'periodStart' => 1464624000,
            'periodEnd'   => 1464710399,
            'sourceId'    => 231,
            'accode'      => 0,
            'action'      => 'GAME_OVER',
            'acttag'      => 'game_over',
            'execut'      =>
                [
                    0 =>
                        [
                            'key' => 'usertesk.teskvalue',
                            'exe' => '+',
                            'par' => 1,
                        ],
                ],
            'condit'      =>
                [
                    0 =>
                        [
                            'key' => 'usertesk.teskvalue',
                            'leg' => '>=',
                            'par' => 5,
                        ],
                ],
            'result'      =>
                [
                    0 =>
                        [
                            'key' => 'usertesk.teskdone',
                            'exe' => '=',
                            'par' => 0,
                        ],
                    1 =>
                        [
                            'key' => 'usertesk.teskstate',
                            'exe' => '=',
                            'par' => 3,
                        ],
                    2 =>
                        [
                            'key' => 'usertesk.teskvalue',
                            'exe' => '=',
                            'par' => 0,
                        ],
                ],
            'target'      => 5,
            'prizeName'   => '100乐券',
            'prizes'      =>
                [
                    'coupon' => 100,
                ],
            'mailSubject' => '',
            'mailContent' => '',
            'mailFileid'  => 0,
            'is_surprise' => 0,
            'is_online'   => 1,
            'is_del'      => 0,
            'sort'        => 1,
            'create_time' => 1464641966,
            'update_time' => 1464641966,
        ],
    10270 =>
        [
            'id'          => 10270,
            'type'        => 0,
            'name'        => '完成5局牌局(新手癞)',
            'prev'        => 0,
            'goto'        => 0,
            'rooms'       =>
                [
                    0 => '1007',
                ],
            'channels'    =>
                [
                ],
            'users'       =>
                [
                ],
            'start_time'  => 0,
            'end_time'    => 0,
            'periodName'  => '每天',
            'periodTime'  => 86400,
            'periodId'    => 0,
            'periodStart' => 1464624000,
            'periodEnd'   => 1464710399,
            'sourceId'    => 231,
            'accode'      => 0,
            'action'      => 'GAME_OVER',
            'acttag'      => 'game_over',
            'execut'      =>
                [
                    0 =>
                        [
                            'key' => 'usertesk.teskvalue',
                            'exe' => '+',
                            'par' => 1,
                        ],
                ],
            'condit'      =>
                [
                    0 =>
                        [
                            'key' => 'usertesk.teskvalue',
                            'leg' => '>=',
                            'par' => 5,
                        ],
                ],
            'result'      =>
                [
                    0 =>
                        [
                            'key' => 'usertesk.teskdone',
                            'exe' => '=',
                            'par' => 0,
                        ],
                    1 =>
                        [
                            'key' => 'usertesk.teskstate',
                            'exe' => '=',
                            'par' => 3,
                        ],
                    2 =>
                        [
                            'key' => 'usertesk.teskvalue',
                            'exe' => '=',
                            'par' => 0,
                        ],
                ],
            'target'      => 5,
            'prizeName'   => '15乐券',
            'prizes'      =>
                [
                    'coupon' => 15,
                ],
            'mailSubject' => '',
            'mailContent' => '',
            'mailFileid'  => 0,
            'is_surprise' => 0,
            'is_online'   => 1,
            'is_del'      => 0,
            'sort'        => 1,
            'create_time' => 1464642052,
            'update_time' => 1464642052,
        ],
    10271 =>
        [
            'id'          => 10271,
            'type'        => 0,
            'name'        => '完成5局牌局(中级癞)',
            'prev'        => 0,
            'goto'        => 0,
            'rooms'       =>
                [
                    0 => '1009',
                ],
            'channels'    =>
                [
                ],
            'users'       =>
                [
                ],
            'start_time'  => 0,
            'end_time'    => 0,
            'periodName'  => '每天',
            'periodTime'  => 86400,
            'periodId'    => 0,
            'periodStart' => 1464624000,
            'periodEnd'   => 1464710399,
            'sourceId'    => 231,
            'accode'      => 0,
            'action'      => 'GAME_OVER',
            'acttag'      => 'game_over',
            'execut'      =>
                [
                    0 =>
                        [
                            'key' => 'usertesk.teskvalue',
                            'exe' => '+',
                            'par' => 1,
                        ],
                ],
            'condit'      =>
                [
                    0 =>
                        [
                            'key' => 'usertesk.teskvalue',
                            'leg' => '>=',
                            'par' => 5,
                        ],
                ],
            'result'      =>
                [
                    0 =>
                        [
                            'key' => 'usertesk.teskdone',
                            'exe' => '=',
                            'par' => 0,
                        ],
                    1 =>
                        [
                            'key' => 'usertesk.teskstate',
                            'exe' => '=',
                            'par' => 3,
                        ],
                    2 =>
                        [
                            'key' => 'usertesk.teskvalue',
                            'exe' => '=',
                            'par' => 0,
                        ],
                ],
            'target'      => 5,
            'prizeName'   => '60乐券',
            'prizes'      =>
                [
                    'coupon' => 60,
                ],
            'mailSubject' => '',
            'mailContent' => '',
            'mailFileid'  => 0,
            'is_surprise' => 0,
            'is_online'   => 1,
            'is_del'      => 0,
            'sort'        => 1,
            'create_time' => 1464642111,
            'update_time' => 1464642111,
        ],
    10272 =>
        [
            'id'          => 10272,
            'type'        => 0,
            'name'        => '完成5局牌局 (初级癞)',
            'prev'        => 0,
            'goto'        => 0,
            'rooms'       =>
                [
                    0 => '1008',
                ],
            'channels'    =>
                [
                ],
            'users'       =>
                [
                ],
            'start_time'  => 0,
            'end_time'    => 0,
            'periodName'  => '每天',
            'periodTime'  => 86400,
            'periodId'    => 0,
            'periodStart' => 1464624000,
            'periodEnd'   => 1464710399,
            'sourceId'    => 231,
            'accode'      => 0,
            'action'      => 'GAME_OVER',
            'acttag'      => 'game_over',
            'execut'      =>
                [
                    0 =>
                        [
                            'key' => 'usertesk.teskvalue',
                            'exe' => '+',
                            'par' => 1,
                        ],
                ],
            'condit'      =>
                [
                    0 =>
                        [
                            'key' => 'usertesk.teskvalue',
                            'leg' => '>=',
                            'par' => 5,
                        ],
                ],
            'result'      =>
                [
                    0 =>
                        [
                            'key' => 'usertesk.teskdone',
                            'exe' => '=',
                            'par' => 0,
                        ],
                    1 =>
                        [
                            'key' => 'usertesk.teskstate',
                            'exe' => '=',
                            'par' => 3,
                        ],
                    2 =>
                        [
                            'key' => 'usertesk.teskvalue',
                            'exe' => '=',
                            'par' => 0,
                        ],
                ],
            'target'      => 5,
            'prizeName'   => '30乐券',
            'prizes'      =>
                [
                    'coupon' => 30,
                ],
            'mailSubject' => '',
            'mailContent' => '',
            'mailFileid'  => 0,
            'is_surprise' => 0,
            'is_online'   => 1,
            'is_del'      => 0,
            'sort'        => 1,
            'create_time' => 1464642178,
            'update_time' => 1464642178,
        ],
    10273 =>
        [
            'id'          => 10273,
            'type'        => 0,
            'name'        => '完成5局牌局 (高级癞)',
            'prev'        => 0,
            'goto'        => 0,
            'rooms'       =>
                [
                    0 => '1010',
                ],
            'channels'    =>
                [
                ],
            'users'       =>
                [
                ],
            'start_time'  => 0,
            'end_time'    => 0,
            'periodName'  => '每天',
            'periodTime'  => 86400,
            'periodId'    => 0,
            'periodStart' => 1464624000,
            'periodEnd'   => 1464710399,
            'sourceId'    => 231,
            'accode'      => 0,
            'action'      => 'GAME_OVER',
            'acttag'      => 'game_over',
            'execut'      =>
                [
                    0 =>
                        [
                            'key' => 'usertesk.teskvalue',
                            'exe' => '+',
                            'par' => 1,
                        ],
                ],
            'condit'      =>
                [
                    0 =>
                        [
                            'key' => 'usertesk.teskvalue',
                            'leg' => '>=',
                            'par' => 5,
                        ],
                ],
            'result'      =>
                [
                    0 =>
                        [
                            'key' => 'usertesk.teskdone',
                            'exe' => '=',
                            'par' => 0,
                        ],
                    1 =>
                        [
                            'key' => 'usertesk.teskstate',
                            'exe' => '=',
                            'par' => 3,
                        ],
                    2 =>
                        [
                            'key' => 'usertesk.teskvalue',
                            'exe' => '=',
                            'par' => 0,
                        ],
                ],
            'target'      => 5,
            'prizeName'   => '120乐券',
            'prizes'      =>
                [
                    'coupon' => 120,
                ],
            'mailSubject' => '',
            'mailContent' => '',
            'mailFileid'  => 0,
            'is_surprise' => 0,
            'is_online'   => 1,
            'is_del'      => 0,
            'sort'        => 1,
            'create_time' => 1464642240,
            'update_time' => 1464642240,
        ],
    10274 =>
        [
            'id'          => 10274,
            'type'        => 2,
            'name'        => '阿里用户每日首次购买乐豆额外送',
            'prev'        => 0,
            'goto'        => 0,
            'rooms'       =>
                [
                ],
            'channels'    =>
                [
                    0 => 'ali',
                ],
            'users'       =>
                [
                ],
            'start_time'  => 1464883200,
            'end_time'    => 1465660800,
            'periodName'  => '每天',
            'periodTime'  => 86400,
            'periodId'    => 0,
            'periodStart' => 1464710400,
            'periodEnd'   => 1464796799,
            'sourceId'    => 234,
            'accode'      => 0,
            'action'      => 'API_GOLD',
            'acttag'      => 'buy_coins',
            'execut'      =>
                [
                    0 =>
                        [
                            'key' => 'usertesk.teskvalue',
                            'exe' => '=',
                            'par' => 'param',
                        ],
                ],
            'condit'      =>
                [
                    0 =>
                        [
                            'key' => 'usertesk.teskvalue',
                            'leg' => '>',
                            'par' => 0,
                        ],
                ],
            'result'      =>
                [
                    0 =>
                        [
                            'key' => 'prizes.n_coins',
                            'exe' => '=',
                            'par' => 'param',
                        ],
                    1 =>
                        [
                            'key' => 'usertesk.teskdone',
                            'exe' => '=',
                            'par' => 1,
                        ],
                ],
            'target'      => 1,
            'prizeName'   => '额外送乐豆',
            'prizes'      =>
                [
                    'coins' => 3000,
                ],
            'mailSubject' => '阿里用户每日首次购买乐豆额外送',
            'mailContent' => '感谢您参与活动
“阿里用户每日首次购买乐豆额外送”
请领取您的额外送出的乐豆。',
            'mailFileid'  => 0,
            'is_surprise' => 0,
            'is_online'   => 0,
            'is_del'      => 0,
            'sort'        => 11,
            'create_time' => 1464844873,
            'update_time' => 1464844873,
        ],
    10001 =>
        [
            'id'          => 10001,
            'type'        => 2,
            'name'        => '购买一元礼包',
            'prev'        => 0,
            'goto'        => 0,
            'rooms'       =>
                [
                ],
            'channels'    =>
                [
                ],
            'users'       =>
                [
                ],
            'start_time'  => 0,
            'end_time'    => 0,
            'periodName'  => '',
            'periodTime'  => 0,
            'periodId'    => 0,
            'periodStart' => 0,
            'periodEnd'   => 0,
            'sourceId'    => 232,
            'accode'      => 0,
            'action'      => 'API_GOLD',
            'acttag'      => 'buy_goods',
            'execut'      =>
                [
                    0 =>
                        [
                            'key' => 'usertesk.teskvalue',
                            'exe' => '=',
                            'par' => 'param',
                        ],
                ],
            'condit'      =>
                [
                    0 =>
                        [
                            'key' => 'usertesk.teskvalue',
                            'leg' => '=',
                            'par' => '65',
                        ],
                ],
            'result'      =>
                [
                    0 =>
                        [
                            'key' => 'usertesk.teskdone',
                        ],
                ],
            'target'      => 0,
            'prizeName'   => '1000乐豆',
            'prizes'      =>
                [
                    'coins' => 1000,
                ],
            'mailSubject' => '',
            'mailContent' => '',
            'mailFileid'  => 0,
            'is_surprise' => 0,
            'is_online'   => 1,
            'is_del'      => 0,
            'sort'        => 1,
            'create_time' => 1432710212,
            'update_time' => 1437126833,
        ],
    10275 =>
        [
            'id'          => 10275,
            'type'        => 2,
            'name'        => '每日首次充值10',
            'prev'        => 0,
            'goto'        => 0,
            'rooms'       =>
                [
                ],
            'channels'    =>
                [
                ],
            'users'       =>
                [
                ],
            'start_time'  => 1466611200,
            'end_time'    => 1469980800,
            'periodName'  => '每天',
            'periodTime'  => 86400,
            'periodId'    => 0,
            'periodStart' => 1466611200,
            'periodEnd'   => 1466697599,
            'sourceId'    => 217,
            'accode'      => 0,
            'action'      => 'API_GOLD',
            'acttag'      => 'cost_gold',
            'execut'      =>
                [
                    0 =>
                        [
                            'key' => 'usertesk.teskvalue',
                            'exe' => '=',
                            'par' => 'param',
                        ],
                ],
            'condit'      =>
                [
                    0 =>
                        [
                            'key' => 'usertesk.teskvalue',
                            'leg' => '=',
                            'par' => 10,
                        ],
                ],
            'result'      =>
                [
                    0 =>
                        [
                            'key' => 'usertesk.teskdone',
                        ],
                ],
            'target'      => 10,
            'prizeName'   => '2万乐豆＋1天记牌器',
            'prizes'      =>
                [
                    'coins'     => 20000,
                    'propItems' =>
                        [
                            7 =>
                                [
                                    'id'   => 7,
                                    'name' => '记牌器(1天)',
                                    'cd'   => '2',
                                    'num'  => 1,
                                    'ext'  => 0,
                                ],
                        ],
                ],
            'mailSubject' => '感谢参与每日首次充值送好礼活动',
            'mailContent' => '感谢您参与活动：
每日首次充值送好礼。
您今日首次消费10元，
获得奖励如下：
2万乐豆＋1天记牌器',
            'mailFileid'  => 0,
            'is_surprise' => 0,
            'is_online'   => 1,
            'is_del'      => 0,
            'sort'        => 1,
            'create_time' => 1466152783,
            'update_time' => 1468066315,
        ],
    10276 =>
        [
            'id'          => 10276,
            'type'        => 2,
            'name'        => '每日首次充值30',
            'prev'        => 0,
            'goto'        => 0,
            'rooms'       =>
                [
                ],
            'channels'    =>
                [
                ],
            'users'       =>
                [
                ],
            'start_time'  => 1466611200,
            'end_time'    => 1469980800,
            'periodName'  => '每天',
            'periodTime'  => 86400,
            'periodId'    => 0,
            'periodStart' => 1466611200,
            'periodEnd'   => 1466697599,
            'sourceId'    => 107,
            'accode'      => 0,
            'action'      => 'API_GOLD',
            'acttag'      => 'cost_gold',
            'execut'      =>
                [
                    0 =>
                        [
                            'key' => 'usertesk.teskvalue',
                            'exe' => '=',
                            'par' => 'param',
                        ],
                ],
            'condit'      =>
                [
                    0 =>
                        [
                            'key' => 'usertesk.teskvalue',
                            'leg' => '=',
                            'par' => 30,
                        ],
                ],
            'result'      =>
                [
                    0 =>
                        [
                            'key' => 'usertesk.teskdone',
                        ],
                ],
            'target'      => 30,
            'prizeName'   => '6万乐豆＋3天记牌器',
            'prizes'      =>
                [
                    'coins'     => 60000,
                    'propItems' =>
                        [
                            8 =>
                                [
                                    'id'   => 8,
                                    'name' => '记牌器(3天)',
                                    'cd'   => '2',
                                    'num'  => 1,
                                    'ext'  => 0,
                                ],
                        ],
                ],
            'mailSubject' => '感谢参与每日首次充值送好礼活动',
            'mailContent' => '感谢您参与活动：
每日首次充值送好礼。
您今日首次消费30元，
获得奖励如下：
6万乐豆＋3天记牌器',
            'mailFileid'  => 0,
            'is_surprise' => 0,
            'is_online'   => 1,
            'is_del'      => 0,
            'sort'        => 1,
            'create_time' => 1466152949,
            'update_time' => 1468066375,
        ],
    10277 =>
        [
            'id'          => 10277,
            'type'        => 2,
            'name'        => '每日首次充值50',
            'prev'        => 0,
            'goto'        => 0,
            'rooms'       =>
                [
                ],
            'channels'    =>
                [
                ],
            'users'       =>
                [
                ],
            'start_time'  => 1466611200,
            'end_time'    => 1469980800,
            'periodName'  => '每天',
            'periodTime'  => 86400,
            'periodId'    => 0,
            'periodStart' => 1466611200,
            'periodEnd'   => 1466697599,
            'sourceId'    => 108,
            'accode'      => 0,
            'action'      => 'API_GOLD',
            'acttag'      => 'cost_gold',
            'execut'      =>
                [
                    0 =>
                        [
                            'key' => 'usertesk.teskvalue',
                            'exe' => '=',
                            'par' => 'param',
                        ],
                ],
            'condit'      =>
                [
                    0 =>
                        [
                            'key' => 'usertesk.teskvalue',
                            'leg' => '=',
                            'par' => 50,
                        ],
                ],
            'result'      =>
                [
                    0 =>
                        [
                            'key' => 'usertesk.teskdone',
                        ],
                ],
            'target'      => 50,
            'prizeName'   => '10万乐豆＋7天记牌器',
            'prizes'      =>
                [
                    'coins'     => 100000,
                    'propItems' =>
                        [
                            9 =>
                                [
                                    'id'   => 9,
                                    'name' => '记牌器(7天)',
                                    'cd'   => '2',
                                    'num'  => 1,
                                    'ext'  => 0,
                                ],
                        ],
                ],
            'mailSubject' => '感谢参与每日首次充值送好礼活动',
            'mailContent' => '感谢您参与活动：
每日首次充值送好礼。
您今日首次消费50元，
获得奖励如下：
10万乐豆＋7天记牌器',
            'mailFileid'  => 0,
            'is_surprise' => 0,
            'is_online'   => 1,
            'is_del'      => 0,
            'sort'        => 1,
            'create_time' => 1466153091,
            'update_time' => 1468066460,
        ],
    10278 =>
        [
            'id'          => 10278,
            'type'        => 2,
            'name'        => '每日首次充值100',
            'prev'        => 0,
            'goto'        => 0,
            'rooms'       =>
                [
                ],
            'channels'    =>
                [
                ],
            'users'       =>
                [
                ],
            'start_time'  => 1466611200,
            'end_time'    => 1469980800,
            'periodName'  => '每天',
            'periodTime'  => 86400,
            'periodId'    => 0,
            'periodStart' => 1466611200,
            'periodEnd'   => 1466697599,
            'sourceId'    => 109,
            'accode'      => 0,
            'action'      => 'API_GOLD',
            'acttag'      => 'cost_gold',
            'execut'      =>
                [
                    0 =>
                        [
                            'key' => 'usertesk.teskvalue',
                            'exe' => '=',
                            'par' => 'param',
                        ],
                ],
            'condit'      =>
                [
                    0 =>
                        [
                            'key' => 'usertesk.teskvalue',
                            'leg' => '=',
                            'par' => 100,
                        ],
                ],
            'result'      =>
                [
                    0 =>
                        [
                            'key' => 'usertesk.teskdone',
                        ],
                ],
            'target'      => 100,
            'prizeName'   => '40万乐豆',
            'prizes'      =>
                [
                    'coins' => 400000,
                ],
            'mailSubject' => '感谢参与每日首次充值送好礼活动',
            'mailContent' => '感谢您参与活动：
每日首次充值送好礼。
您今日首次消费100元，
获得奖励如下：
40万乐豆',
            'mailFileid'  => 0,
            'is_surprise' => 0,
            'is_online'   => 1,
            'is_del'      => 0,
            'sort'        => 1,
            'create_time' => 1466153132,
            'update_time' => 1468066552,
        ],
    10279 =>
        [
            'id'          => 10279,
            'type'        => 2,
            'name'        => '激情一夏双重送好礼300',
            'prev'        => 0,
            'goto'        => 0,
            'rooms'       =>
                [
                ],
            'channels'    =>
                [
                    0 => 'letv',
                    1 => 'hisense',
                ],
            'users'       =>
                [
                ],
            'start_time'  => 1466179200,
            'end_time'    => 1466524800,
            'periodName'  => '',
            'periodTime'  => 0,
            'periodId'    => 0,
            'periodStart' => 0,
            'periodEnd'   => 0,
            'sourceId'    => 113,
            'accode'      => 0,
            'action'      => 'API_GOLD',
            'acttag'      => 'cost_gold',
            'execut'      =>
                [
                    0 =>
                        [
                            'key' => 'usertesk.teskvalue',
                            'exe' => '+',
                            'par' => 'param',
                        ],
                ],
            'condit'      =>
                [
                    0 =>
                        [
                            'key' => 'usertesk.teskvalue',
                            'leg' => '>=',
                            'par' => 300,
                        ],
                ],
            'result'      =>
                [
                    0 =>
                        [
                            'key' => 'usertesk.teskdone',
                        ],
                ],
            'target'      => 300,
            'prizeName'   => '50元话费',
            'prizes'      =>
                [
                    'coins' => 1000,
                    'other' => '50元话费',
                ],
            'mailSubject' => '感谢参与激情一夏双重送好礼活动',
            'mailContent' => '感谢您参与活动：
激情一夏  双重活动送好礼。
您已累计消费300元，
获得奖励如下：
50元话费，
请联系官方客服QQ领取。',
            'mailFileid'  => 0,
            'is_surprise' => 0,
            'is_online'   => 0,
            'is_del'      => 0,
            'sort'        => 11,
            'create_time' => 1466154683,
            'update_time' => 1466154683,
        ],
    10280 =>
        [
            'id'          => 10280,
            'type'        => 2,
            'name'        => '激情一夏双重送好礼500',
            'prev'        => 0,
            'goto'        => 0,
            'rooms'       =>
                [
                ],
            'channels'    =>
                [
                    0 => 'letv',
                    1 => 'hisense',
                ],
            'users'       =>
                [
                ],
            'start_time'  => 1466179200,
            'end_time'    => 1466524800,
            'periodName'  => '',
            'periodTime'  => 0,
            'periodId'    => 0,
            'periodStart' => 0,
            'periodEnd'   => 0,
            'sourceId'    => 4,
            'accode'      => 0,
            'action'      => 'API_GOLD',
            'acttag'      => 'cost_gold',
            'execut'      =>
                [
                    0 =>
                        [
                            'key' => 'usertesk.teskvalue',
                            'exe' => '+',
                            'par' => 'param',
                        ],
                ],
            'condit'      =>
                [
                    0 =>
                        [
                            'key' => 'usertesk.teskvalue',
                            'leg' => '>=',
                            'par' => 500,
                        ],
                ],
            'result'      =>
                [
                    0 =>
                        [
                            'key' => 'usertesk.teskdone',
                        ],
                ],
            'target'      => 500,
            'prizeName'   => '蓝牙耳机＋充电宝',
            'prizes'      =>
                [
                    'coins'     => 1000,
                    'propItems' =>
                        [
                            4 =>
                                [
                                    'id'   => 4,
                                    'name' => '富翁套装',
                                    'cd'   => '1',
                                    'num'  => 1,
                                    'ext'  => 0,
                                ],
                        ],
                    'other'     => '蓝牙耳机\\n充电宝',
                ],
            'mailSubject' => '感谢参与激情一夏双重送好礼活动',
            'mailContent' => '感谢您参与活动：
激情一夏  双重活动送好礼。
您已累计消费500元，
获得奖励如下：
永久富翁套装(游戏道具)＋蓝牙耳机＋充电宝
实物奖励请联系官方客服QQ领取。',
            'mailFileid'  => 0,
            'is_surprise' => 0,
            'is_online'   => 0,
            'is_del'      => 0,
            'sort'        => 11,
            'create_time' => 1466154925,
            'update_time' => 1466154925,
        ],
    10281 =>
        [
            'id'          => 10281,
            'type'        => 2,
            'name'        => '激情一夏双重送好礼1000',
            'prev'        => 0,
            'goto'        => 0,
            'rooms'       =>
                [
                ],
            'channels'    =>
                [
                    0 => 'letv',
                    1 => 'hisense',
                ],
            'users'       =>
                [
                ],
            'start_time'  => 1466179200,
            'end_time'    => 1466524800,
            'periodName'  => '',
            'periodTime'  => 0,
            'periodId'    => 0,
            'periodStart' => 0,
            'periodEnd'   => 0,
            'sourceId'    => 114,
            'accode'      => 0,
            'action'      => 'API_GOLD',
            'acttag'      => 'cost_gold',
            'execut'      =>
                [
                    0 =>
                        [
                            'key' => 'usertesk.teskvalue',
                            'exe' => '+',
                            'par' => 'param',
                        ],
                ],
            'condit'      =>
                [
                    0 =>
                        [
                            'key' => 'usertesk.teskvalue',
                            'leg' => '>=',
                            'par' => 1000,
                        ],
                ],
            'result'      =>
                [
                    0 =>
                        [
                            'key' => 'usertesk.teskdone',
                        ],
                ],
            'target'      => 1000,
            'prizeName'   => '指尖遥控器＋蓝牙耳机＋充电宝',
            'prizes'      =>
                [
                    'coins'     => 1000,
                    'propItems' =>
                        [
                            11 =>
                                [
                                    'id'   => 11,
                                    'name' => '县官套装(30天)',
                                    'cd'   => '1',
                                    'num'  => 1,
                                    'ext'  => 0,
                                ],
                        ],
                    'other'     => '指尖遥控器\\n蓝牙耳机\\n充电宝',
                ],
            'mailSubject' => '感谢参与激情一夏双重送好礼活动',
            'mailContent' => '感谢您参与活动：
激情一夏  双重活动送好礼。
您已累计消费1000元，
获得奖励如下：
30天县官套装(游戏道具)＋
指尖遥控器＋蓝牙耳机＋充电宝
实物奖励请联系官方客服QQ领取。',
            'mailFileid'  => 0,
            'is_surprise' => 0,
            'is_online'   => 0,
            'is_del'      => 0,
            'sort'        => 11,
            'create_time' => 1466155023,
            'update_time' => 1466155023,
        ],
    10282 =>
        [
            'id'          => 10282,
            'type'        => 2,
            'name'        => '6月累计消费100',
            'prev'        => 0,
            'goto'        => 0,
            'rooms'       =>
                [
                ],
            'channels'    =>
                [
                ],
            'users'       =>
                [
                ],
            'start_time'  => 1466611200,
            'end_time'    => 1467388800,
            'periodName'  => '',
            'periodTime'  => 0,
            'periodId'    => 0,
            'periodStart' => 0,
            'periodEnd'   => 0,
            'sourceId'    => 142,
            'accode'      => 0,
            'action'      => 'API_GOLD',
            'acttag'      => 'cost_gold',
            'execut'      =>
                [
                    0 =>
                        [
                            'key' => 'usertesk.teskvalue',
                            'exe' => '+',
                            'par' => 'param',
                        ],
                ],
            'condit'      =>
                [
                    0 =>
                        [
                            'key' => 'usertesk.teskvalue',
                            'leg' => '>=',
                            'par' => 100,
                        ],
                ],
            'result'      =>
                [
                    0 =>
                        [
                            'key' => 'usertesk.teskdone',
                        ],
                ],
            'target'      => 100,
            'prizeName'   => '8888乐券',
            'prizes'      =>
                [
                    'coupon' => 8888,
                ],
            'mailSubject' => '感谢参与累计充值送乐券活动',
            'mailContent' => '感谢您参与活动：
累计充值送乐券。
您已累计充值100元，
获得奖励如下：
8888乐券',
            'mailFileid'  => 0,
            'is_surprise' => 0,
            'is_online'   => 0,
            'is_del'      => 0,
            'sort'        => 11,
            'create_time' => 1466584767,
            'update_time' => 1466584767,
        ],
    10283 =>
        [
            'id'          => 10283,
            'type'        => 2,
            'name'        => '6月累计消费200',
            'prev'        => 0,
            'goto'        => 0,
            'rooms'       =>
                [
                ],
            'channels'    =>
                [
                ],
            'users'       =>
                [
                ],
            'start_time'  => 1466611200,
            'end_time'    => 1467388800,
            'periodName'  => '',
            'periodTime'  => 0,
            'periodId'    => 0,
            'periodStart' => 0,
            'periodEnd'   => 0,
            'sourceId'    => 3,
            'accode'      => 0,
            'action'      => 'API_GOLD',
            'acttag'      => 'cost_gold',
            'execut'      =>
                [
                    0 =>
                        [
                            'key' => 'usertesk.teskvalue',
                            'exe' => '+',
                            'par' => 'param',
                        ],
                ],
            'condit'      =>
                [
                    0 =>
                        [
                            'key' => 'usertesk.teskvalue',
                            'leg' => '>=',
                            'par' => 200,
                        ],
                ],
            'result'      =>
                [
                    0 =>
                        [
                            'key' => 'usertesk.teskdone',
                        ],
                ],
            'target'      => 200,
            'prizeName'   => '18888乐券',
            'prizes'      =>
                [
                    'coupon' => 18888,
                ],
            'mailSubject' => '感谢参与累计充值送乐券活动',
            'mailContent' => '感谢您参与活动：
累计充值送乐券。
您已累计充值200元，
获得奖励如下：
18888乐券',
            'mailFileid'  => 0,
            'is_surprise' => 0,
            'is_online'   => 0,
            'is_del'      => 0,
            'sort'        => 11,
            'create_time' => 1466585097,
            'update_time' => 1466585097,
        ],
    10284 =>
        [
            'id'          => 10284,
            'type'        => 2,
            'name'        => '6月累计消费500',
            'prev'        => 0,
            'goto'        => 0,
            'rooms'       =>
                [
                ],
            'channels'    =>
                [
                ],
            'users'       =>
                [
                ],
            'start_time'  => 1466611200,
            'end_time'    => 1467388800,
            'periodName'  => '',
            'periodTime'  => 0,
            'periodId'    => 0,
            'periodStart' => 0,
            'periodEnd'   => 0,
            'sourceId'    => 4,
            'accode'      => 0,
            'action'      => 'API_GOLD',
            'acttag'      => 'cost_gold',
            'execut'      =>
                [
                    0 =>
                        [
                            'key' => 'usertesk.teskvalue',
                            'exe' => '+',
                            'par' => 'param',
                        ],
                ],
            'condit'      =>
                [
                    0 =>
                        [
                            'key' => 'usertesk.teskvalue',
                            'leg' => '>=',
                            'par' => 500,
                        ],
                ],
            'result'      =>
                [
                    0 =>
                        [
                            'key' => 'usertesk.teskdone',
                        ],
                ],
            'target'      => 500,
            'prizeName'   => '38888乐券',
            'prizes'      =>
                [
                    'coupon' => 38888,
                ],
            'mailSubject' => '感谢参与累计充值送乐券活动',
            'mailContent' => '感谢您参与活动：
累计充值送乐券。
您已累计充值500元，
获得奖励如下：
38888乐券',
            'mailFileid'  => 0,
            'is_surprise' => 0,
            'is_online'   => 0,
            'is_del'      => 0,
            'sort'        => 11,
            'create_time' => 1466586084,
            'update_time' => 1466586084,
        ],
    10285 =>
        [
            'id'          => 10285,
            'type'        => 2,
            'name'        => '6月累计消费1000',
            'prev'        => 0,
            'goto'        => 0,
            'rooms'       =>
                [
                ],
            'channels'    =>
                [
                ],
            'users'       =>
                [
                ],
            'start_time'  => 1466611200,
            'end_time'    => 1467388800,
            'periodName'  => '',
            'periodTime'  => 0,
            'periodId'    => 0,
            'periodStart' => 0,
            'periodEnd'   => 0,
            'sourceId'    => 114,
            'accode'      => 0,
            'action'      => 'API_GOLD',
            'acttag'      => 'cost_gold',
            'execut'      =>
                [
                    0 =>
                        [
                            'key' => 'usertesk.teskvalue',
                            'exe' => '+',
                            'par' => 'param',
                        ],
                ],
            'condit'      =>
                [
                    0 =>
                        [
                            'key' => 'usertesk.teskvalue',
                            'leg' => '>=',
                            'par' => 1000,
                        ],
                ],
            'result'      =>
                [
                    0 =>
                        [
                            'key' => 'usertesk.teskdone',
                        ],
                ],
            'target'      => 1000,
            'prizeName'   => '88888乐券',
            'prizes'      =>
                [
                    'coupon' => 88888,
                ],
            'mailSubject' => '感谢参与累计充值送乐券活动',
            'mailContent' => '感谢您参与活动：
累计充值送乐券。
您已累计充值1000元，
获得奖励如下：
88888乐券',
            'mailFileid'  => 0,
            'is_surprise' => 0,
            'is_online'   => 0,
            'is_del'      => 0,
            'sort'        => 11,
            'create_time' => 1466586530,
            'update_time' => 1466586530,
        ],
    10286 =>
        [
            'id'          => 10286,
            'type'        => 2,
            'name'        => '累计消费100',
            'prev'        => 0,
            'goto'        => 0,
            'rooms'       =>
                [
                ],
            'channels'    =>
                [
                ],
            'users'       =>
                [
                ],
            'start_time'  => 1467302400,
            'end_time'    => 1468252800,
            'periodName'  => '',
            'periodTime'  => 0,
            'periodId'    => 0,
            'periodStart' => 0,
            'periodEnd'   => 0,
            'sourceId'    => 142,
            'accode'      => 0,
            'action'      => 'API_GOLD',
            'acttag'      => 'cost_gold',
            'execut'      =>
                [
                    0 =>
                        [
                            'key' => 'usertesk.teskvalue',
                            'exe' => '+',
                            'par' => 'param',
                        ],
                ],
            'condit'      =>
                [
                    0 =>
                        [
                            'key' => 'usertesk.teskvalue',
                            'leg' => '>=',
                            'par' => 100,
                        ],
                ],
            'result'      =>
                [
                    0 =>
                        [
                            'key' => 'usertesk.teskdone',
                        ],
                ],
            'target'      => 100,
            'prizeName'   => '8888乐券',
            'prizes'      =>
                [
                    'coupon' => 8888,
                ],
            'mailSubject' => '感谢参与累计充值送乐券活动',
            'mailContent' => '感谢您参与活动：
累计充值送乐券。
您已累计充值100元，
获得奖励如下：
8888乐券',
            'mailFileid'  => 0,
            'is_surprise' => 0,
            'is_online'   => 1,
            'is_del'      => 0,
            'sort'        => 1,
            'create_time' => 1467283199,
            'update_time' => 1467283199,
        ],
    10287 =>
        [
            'id'          => 10287,
            'type'        => 2,
            'name'        => '累计消费200',
            'prev'        => 0,
            'goto'        => 0,
            'rooms'       =>
                [
                ],
            'channels'    =>
                [
                ],
            'users'       =>
                [
                ],
            'start_time'  => 1467302400,
            'end_time'    => 1468252800,
            'periodName'  => '',
            'periodTime'  => 0,
            'periodId'    => 0,
            'periodStart' => 0,
            'periodEnd'   => 0,
            'sourceId'    => 3,
            'accode'      => 0,
            'action'      => 'API_GOLD',
            'acttag'      => 'cost_gold',
            'execut'      =>
                [
                    0 =>
                        [
                            'key' => 'usertesk.teskvalue',
                            'exe' => '+',
                            'par' => 'param',
                        ],
                ],
            'condit'      =>
                [
                    0 =>
                        [
                            'key' => 'usertesk.teskvalue',
                            'leg' => '>=',
                            'par' => 200,
                        ],
                ],
            'result'      =>
                [
                    0 =>
                        [
                            'key' => 'usertesk.teskdone',
                        ],
                ],
            'target'      => 200,
            'prizeName'   => '18888乐券',
            'prizes'      =>
                [
                    'coupon' => 18888,
                ],
            'mailSubject' => '感谢参与累计充值送乐券活动',
            'mailContent' => '感谢您参与活动：
累计充值送乐券。
您已累计充值200元，
获得奖励如下：
18888乐券',
            'mailFileid'  => 0,
            'is_surprise' => 0,
            'is_online'   => 1,
            'is_del'      => 0,
            'sort'        => 1,
            'create_time' => 1467283771,
            'update_time' => 1467283771,
        ],
    10288 =>
        [
            'id'          => 10288,
            'type'        => 2,
            'name'        => '累计消费500',
            'prev'        => 0,
            'goto'        => 0,
            'rooms'       =>
                [
                ],
            'channels'    =>
                [
                ],
            'users'       =>
                [
                ],
            'start_time'  => 1467302400,
            'end_time'    => 1468252800,
            'periodName'  => '',
            'periodTime'  => 0,
            'periodId'    => 0,
            'periodStart' => 0,
            'periodEnd'   => 0,
            'sourceId'    => 4,
            'accode'      => 0,
            'action'      => 'API_GOLD',
            'acttag'      => 'cost_gold',
            'execut'      =>
                [
                    0 =>
                        [
                            'key' => 'usertesk.teskvalue',
                            'exe' => '+',
                            'par' => 'param',
                        ],
                ],
            'condit'      =>
                [
                    0 =>
                        [
                            'key' => 'usertesk.teskvalue',
                            'leg' => '>=',
                            'par' => 500,
                        ],
                ],
            'result'      =>
                [
                    0 =>
                        [
                            'key' => 'usertesk.teskdone',
                        ],
                ],
            'target'      => 500,
            'prizeName'   => '38888乐券',
            'prizes'      =>
                [
                    'coupon' => 38888,
                ],
            'mailSubject' => '感谢参与累计充值送乐券活动',
            'mailContent' => '感谢您参与活动：
累计充值送乐券。
您已累计充值500元，
获得奖励如下：
38888乐券',
            'mailFileid'  => 0,
            'is_surprise' => 0,
            'is_online'   => 1,
            'is_del'      => 0,
            'sort'        => 1,
            'create_time' => 1467283982,
            'update_time' => 1467283982,
        ],
    10289 =>
        [
            'id'          => 10289,
            'type'        => 2,
            'name'        => '累计消费1000',
            'prev'        => 0,
            'goto'        => 0,
            'rooms'       =>
                [
                ],
            'channels'    =>
                [
                ],
            'users'       =>
                [
                ],
            'start_time'  => 1467302400,
            'end_time'    => 1468252800,
            'periodName'  => '',
            'periodTime'  => 0,
            'periodId'    => 0,
            'periodStart' => 0,
            'periodEnd'   => 0,
            'sourceId'    => 114,
            'accode'      => 0,
            'action'      => 'API_GOLD',
            'acttag'      => 'cost_gold',
            'execut'      =>
                [
                    0 =>
                        [
                            'key' => 'usertesk.teskvalue',
                            'exe' => '+',
                            'par' => 'param',
                        ],
                ],
            'condit'      =>
                [
                    0 =>
                        [
                            'key' => 'usertesk.teskvalue',
                            'leg' => '>=',
                            'par' => 1000,
                        ],
                ],
            'result'      =>
                [
                    0 =>
                        [
                            'key' => 'usertesk.teskdone',
                        ],
                ],
            'target'      => 1000,
            'prizeName'   => '88888乐券',
            'prizes'      =>
                [
                    'coupon' => 88888,
                ],
            'mailSubject' => '感谢参与累计充值送乐券活动',
            'mailContent' => '感谢您参与活动：
累计充值送乐券。
您已累计充值1000元，
获得奖励如下：
88888乐券',
            'mailFileid'  => 0,
            'is_surprise' => 0,
            'is_online'   => 1,
            'is_del'      => 0,
            'sort'        => 1,
            'create_time' => 1467284116,
            'update_time' => 1467284116,
        ],
    10290 =>
        [
            'id'          => 10290,
            'type'        => 2,
            'name'        => '累计消费1000',
            'prev'        => 0,
            'goto'        => 0,
            'rooms'       =>
                [
                ],
            'channels'    =>
                [
                ],
            'users'       =>
                [
                ],
            'start_time'  => 1468166400,
            'end_time'    => 1470067200,
            'periodName'  => '',
            'periodTime'  => 0,
            'periodId'    => 0,
            'periodStart' => 0,
            'periodEnd'   => 0,
            'sourceId'    => 142,
            'accode'      => 0,
            'action'      => 'API_GOLD',
            'acttag'      => 'cost_gold',
            'execut'      =>
                [
                    0 =>
                        [
                            'key' => 'usertesk.teskvalue',
                            'exe' => '+',
                            'par' => 'param',
                        ],
                ],
            'condit'      =>
                [
                    0 =>
                        [
                            'key' => 'usertesk.teskvalue',
                            'leg' => '>=',
                            'par' => 100,
                        ],
                ],
            'result'      =>
                [
                    0 =>
                        [
                            'key' => 'usertesk.teskdone',
                        ],
                ],
            'target'      => 100,
            'prizeName'   => '8888乐券',
            'prizes'      =>
                [
                    'coupon' => 8888,
                ],
            'mailSubject' => '感谢参与累计充值送乐券活动',
            'mailContent' => '感谢您参与活动：
累计充值送乐券。
您已累计充值100元，
获得奖励如下：
8888乐券',
            'mailFileid'  => 0,
            'is_surprise' => 0,
            'is_online'   => 1,
            'is_del'      => 0,
            'sort'        => 1,
            'create_time' => 1468067511,
            'update_time' => 1468067511,
        ],
    10291 =>
        [
            'id'          => 10291,
            'type'        => 2,
            'name'        => '累计消费200',
            'prev'        => 0,
            'goto'        => 0,
            'rooms'       =>
                [
                ],
            'channels'    =>
                [
                ],
            'users'       =>
                [
                ],
            'start_time'  => 1468166400,
            'end_time'    => 1470067200,
            'periodName'  => '',
            'periodTime'  => 0,
            'periodId'    => 0,
            'periodStart' => 0,
            'periodEnd'   => 0,
            'sourceId'    => 3,
            'accode'      => 0,
            'action'      => 'API_GOLD',
            'acttag'      => 'cost_gold',
            'execut'      =>
                [
                    0 =>
                        [
                            'key' => 'usertesk.teskvalue',
                            'exe' => '+',
                            'par' => 'param',
                        ],
                ],
            'condit'      =>
                [
                    0 =>
                        [
                            'key' => 'usertesk.teskvalue',
                            'leg' => '>=',
                            'par' => 200,
                        ],
                ],
            'result'      =>
                [
                    0 =>
                        [
                            'key' => 'usertesk.teskdone',
                        ],
                ],
            'target'      => 200,
            'prizeName'   => '18888乐券',
            'prizes'      =>
                [
                    'coupon' => 18888,
                ],
            'mailSubject' => '感谢参与累计充值送乐券活动',
            'mailContent' => '感谢您参与活动：
累计充值送乐券。
您已累计充值200元，
获得奖励如下：
18888乐券',
            'mailFileid'  => 0,
            'is_surprise' => 0,
            'is_online'   => 1,
            'is_del'      => 0,
            'sort'        => 1,
            'create_time' => 1468067769,
            'update_time' => 1468067769,
        ],
    10292 =>
        [
            'id'          => 10292,
            'type'        => 2,
            'name'        => '累计消费500',
            'prev'        => 0,
            'goto'        => 0,
            'rooms'       =>
                [
                ],
            'channels'    =>
                [
                ],
            'users'       =>
                [
                ],
            'start_time'  => 1468166400,
            'end_time'    => 1470067200,
            'periodName'  => '',
            'periodTime'  => 0,
            'periodId'    => 0,
            'periodStart' => 0,
            'periodEnd'   => 0,
            'sourceId'    => 4,
            'accode'      => 0,
            'action'      => 'API_GOLD',
            'acttag'      => 'cost_gold',
            'execut'      =>
                [
                    0 =>
                        [
                            'key' => 'usertesk.teskvalue',
                            'exe' => '+',
                            'par' => 'param',
                        ],
                ],
            'condit'      =>
                [
                    0 =>
                        [
                            'key' => 'usertesk.teskvalue',
                            'leg' => '>=',
                            'par' => 500,
                        ],
                ],
            'result'      =>
                [
                    0 =>
                        [
                            'key' => 'usertesk.teskdone',
                        ],
                ],
            'target'      => 500,
            'prizeName'   => '38888乐券',
            'prizes'      =>
                [
                    'coupon' => 38888,
                ],
            'mailSubject' => '感谢参与累计充值送乐券活动',
            'mailContent' => '感谢您参与活动：
累计充值送乐券。
您已累计充值500元，
获得奖励如下：
38888乐券',
            'mailFileid'  => 0,
            'is_surprise' => 0,
            'is_online'   => 1,
            'is_del'      => 0,
            'sort'        => 1,
            'create_time' => 1468067954,
            'update_time' => 1468067954,
        ],
    10293 =>
        [
            'id'          => 10293,
            'type'        => 0,
            'name'        => '手机用户每日福利',
            'prev'        => 0,
            'goto'        => 0,
            'rooms'       =>
                [
                ],
            'channels'    =>
                [
                    0 => 'sjyoujoy',
                    1 => 'sjyishiteng',
                    2 => 'sjiosappstore',
                    3 => 'sjzimo',
                    4 => 'sjiosxy',
                    5 => 'sjaiyouxi',
                    6 => 'sjmigu',
                ],
            'users'       =>
                [
                ],
            'start_time'  => 1468166400,
            'end_time'    => 1470067200,
            'periodName'  => '每天',
            'periodTime'  => 86400,
            'periodId'    => 0,
            'periodStart' => 1468166400,
            'periodEnd'   => 1468252799,
            'sourceId'    => 70,
            'accode'      => 0,
            'action'      => 'LOGIN_GUEST',
            'acttag'      => 'user_login',
            'execut'      =>
                [
                    0 =>
                        [
                            'key' => 'usertesk.teskvalue',
                            'exe' => '+',
                            'par' => 1,
                        ],
                ],
            'condit'      =>
                [
                    0 =>
                        [
                            'key' => 'usertesk.teskvalue',
                            'leg' => '>',
                            'par' => 0,
                        ],
                ],
            'result'      =>
                [
                    0 =>
                        [
                            'key' => 'usertesk.teskdone',
                        ],
                ],
            'target'      => 1,
            'prizeName'   => '1000乐豆',
            'prizes'      =>
                [
                    'coins' => 1000,
                ],
            'mailSubject' => '',
            'mailContent' => '',
            'mailFileid'  => 0,
            'is_surprise' => 0,
            'is_online'   => 1,
            'is_del'      => 0,
            'sort'        => 1,
            'create_time' => 1468225312,
            'update_time' => 1468225312,
        ],
    10294 =>
        [
            'id'          => 10294,
            'type'        => 0,
            'name'        => '手机用户每日福利',
            'prev'        => 0,
            'goto'        => 0,
            'rooms'       =>
                [
                ],
            'channels'    =>
                [
                    0  => 'skyworth',
                    1  => 'hisense',
                    2  => 'znds',
                    3  => 'drpeng',
                    4  => 'shafa',
                    5  => 'huan',
                    6  => 'tcl',
                    7  => 'aiyouxi',
                    8  => 'atet',
                    9  => 'kangjia',
                    10 => 'xiaomibox',
                ],
            'users'       =>
                [
                ],
            'start_time'  => 1468512000,
            'end_time'    => 1470067200,
            'periodName'  => '每天',
            'periodTime'  => 86400,
            'periodId'    => 0,
            'periodStart' => 1468166400,
            'periodEnd'   => 1468252799,
            'sourceId'    => 147,
            'accode'      => 0,
            'action'      => 'GAME_OVER',
            'acttag'      => 'game_over',
            'execut'      =>
                [
                    0 =>
                        [
                            'key' => 'usertesk.teskvalue',
                            'exe' => '+',
                            'par' => 1,
                        ],
                ],
            'condit'      =>
                [
                    0 =>
                        [
                            'key' => 'usertesk.teskvalue',
                            'leg' => '>=',
                            'par' => 1000,
                        ],
                ],
            'result'      =>
                [
                    0 =>
                        [
                            'key' => 'usertesk.teskdone',
                        ],
                ],
            'target'      => 1,
            'prizeName'   => '1000乐豆',
            'prizes'      =>
                [
                    'coins' => 1000,
                ],
            'mailSubject' => '',
            'mailContent' => '',
            'mailFileid'  => 0,
            'is_surprise' => 0,
            'is_online'   => 1,
            'is_del'      => 0,
            'sort'        => 1,
            'create_time' => 1468583627,
            'update_time' => 1468584288,
        ],
    10295 =>
        [
            'id'          => 10295,
            'type'        => 0,
            'name'        => '完成5局牌局(无限场)',
            'prev'        => 0,
            'goto'        => 1,
            'rooms'       =>
                [
                    0 => '1006',
                ],
            'channels'    =>
                [
                ],
            'users'       =>
                [
                ],
            'start_time'  => 0,
            'end_time'    => 0,
            'periodName'  => '每天',
            'periodTime'  => 86400,
            'periodId'    => 0,
            'periodStart' => 1464624000,
            'periodEnd'   => 1464710399,
            'sourceId'    => 231,
            'accode'      => 0,
            'action'      => 'GAME_OVER',
            'acttag'      => 'game_over',
            'execut'      =>
                [
                    0 =>
                        [
                            'key' => 'usertesk.teskvalue',
                            'exe' => '+',
                            'par' => 1,
                        ],
                ],
            'condit'      =>
                [
                    0 =>
                        [
                            'key' => 'usertesk.teskvalue',
                            'leg' => '>=',
                            'par' => 5,
                        ],
                ],
            'result'      =>
                [
                    0 =>
                        [
                            'key' => 'usertesk.teskdone',
                            'exe' => '=',
                            'par' => 0,
                        ],
                    1 =>
                        [
                            'key' => 'usertesk.teskstate',
                            'exe' => '=',
                            'par' => 3,
                        ],
                    2 =>
                        [
                            'key' => 'usertesk.teskvalue',
                            'exe' => '=',
                            'par' => 0,
                        ],
                ],
            'target'      => 5,
            'prizeName'   => '100乐券',
            'prizes'      =>
                [
                    'coupon' => 100,
                ],
            'mailSubject' => '',
            'mailContent' => '',
            'mailFileid'  => 0,
            'is_surprise' => 0,
            'is_online'   => 1,
            'is_del'      => 0,
            'sort'        => 1,
            'create_time' => 1471400906,
            'update_time' => 1471401882,
        ],
    10296 =>
        [
            'id'          => 10296,
            'type'        => 0,
            'name'        => '完成5局牌局(无限赖)',
            'prev'        => 0,
            'goto'        => 0,
            'rooms'       =>
                [
                    0 => '1011',
                ],
            'channels'    =>
                [
                ],
            'users'       =>
                [
                ],
            'start_time'  => 0,
            'end_time'    => 0,
            'periodName'  => '每天',
            'periodTime'  => 86400,
            'periodId'    => 0,
            'periodStart' => 1464624000,
            'periodEnd'   => 1464710399,
            'sourceId'    => 231,
            'accode'      => 0,
            'action'      => 'GAME_OVER',
            'acttag'      => 'game_over',
            'execut'      =>
                [
                    0 =>
                        [
                            'key' => 'usertesk.teskvalue',
                            'exe' => '+',
                            'par' => 1,
                        ],
                ],
            'condit'      =>
                [
                    0 =>
                        [
                            'key' => 'usertesk.teskvalue',
                            'leg' => '>=',
                            'par' => 5,
                        ],
                ],
            'result'      =>
                [
                    0 =>
                        [
                            'key' => 'usertesk.teskdone',
                            'exe' => '=',
                            'par' => 0,
                        ],
                    1 =>
                        [
                            'key' => 'usertesk.teskstate',
                            'exe' => '=',
                            'par' => 3,
                        ],
                    2 =>
                        [
                            'key' => 'usertesk.teskvalue',
                            'exe' => '=',
                            'par' => 0,
                        ],
                ],
            'target'      => 5,
            'prizeName'   => '100乐券',
            'prizes'      =>
                [
                    'coupon' => 100,
                ],
            'mailSubject' => '',
            'mailContent' => '',
            'mailFileid'  => 0,
            'is_surprise' => 0,
            'is_online'   => 1,
            'is_del'      => 0,
            'sort'        => 1,
            'create_time' => 1471402378,
            'update_time' => 1471402378,
        ],
];