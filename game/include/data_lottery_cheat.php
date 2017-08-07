<?php

// data_lottery_cheat.php
$cheat_uid = 1;
if ( $cheat_uid === intval($user['uid']) && isset($prizes['4003']) ) {
	$cheat = array(
		'4003' => array(	// 奖品id => 
			'isAbs' => 1,		// 是否无视概率绝对中奖，0否1是。
		),
	);
}

// $data_lottery_prizes = array(//cateid: 0未分类1筹码2奖券3金币4道具5实物6礼包
// '2004' => array('id'=>2004, 'name'=>"1000乐豆",   	'cateid'=>1, 'icon'=>"0", 'chance'=>100000,	'gold'=>0, 'coupon'=>0, 'coins'=>1000,	'propid'=>0, 'filter'=>array(), 'info'=>"恭喜您人品大爆发，抽中1000乐豆！\n再接再厉，实物大奖等你拿！", 'is_lock'=>0, 'is_del'=>0, 'ut_create'=>0, 'ut_update'=>0),
// '2006' => array('id'=>2006, 'name'=>"5000乐豆",   	'cateid'=>1, 'icon'=>"0", 'chance'=>30000,	'gold'=>0, 'coupon'=>0, 'coins'=>5000,	'propid'=>0, 'filter'=>array(), 'info'=>"恭喜您人品大爆发，抽中5000乐豆！\n再接再厉，实物大奖等你拿！", 'is_lock'=>0, 'is_del'=>0, 'ut_create'=>0, 'ut_update'=>0),
// '2007' => array('id'=>2007, 'name'=>"30000乐豆",  	'cateid'=>1, 'icon'=>"0", 'chance'=>10000,	'gold'=>0, 'coupon'=>0, 'coins'=>30000,	'propid'=>0, 'filter'=>array(), 'info'=>"恭喜您人品大爆发，抽中30000乐豆！\n再接再厉，实物大奖等你拿！", 'is_lock'=>0, 'is_del'=>0, 'ut_create'=>0, 'ut_update'=>0),
// '1007' => array('id'=>1007, 'name'=>"50000乐豆",  	'cateid'=>1, 'icon'=>"0", 'chance'=>5000,	'gold'=>0, 'coupon'=>0, 'coins'=>50000,	'propid'=>0, 'filter'=>array(), 'info'=>"恭喜您人品大爆发，抽中50000乐豆！\n再接再厉，实物大奖等你拿！", 'is_lock'=>0, 'is_del'=>0, 'ut_create'=>0, 'ut_update'=>0),
// '3001' => array('id'=>3001, 'name'=>"有乐大师套装",	'cateid'=>4, 'icon'=>"0", 'chance'=>500, 	'gold'=>0, 'coupon'=>0, 'coins'=>0, 	'propid'=>3, 'filter'=>array(), 'info'=>"恭喜您人品大爆发，抽中有乐大师套装！\n再接再厉，实物大奖等你拿！", 'is_lock'=>0, 'is_del'=>0, 'ut_create'=>0, 'ut_update'=>0),
// '3002' => array('id'=>3002, 'name'=>"有乐富翁套装",	'cateid'=>4, 'icon'=>"0", 'chance'=>100, 	'gold'=>0, 'coupon'=>0, 'coins'=>0, 	'propid'=>4, 'filter'=>array(), 'info'=>"恭喜您人品大爆发，抽中有乐富翁套装！\n再接再厉，实物大奖等你拿！", 'is_lock'=>0, 'is_del'=>0, 'ut_create'=>0, 'ut_update'=>0),
// '1002' => array('id'=>1002, 'name'=>"300乐券",    	'cateid'=>2, 'icon'=>"0", 'chance'=>100,	'gold'=>0, 'coupon'=>300, 'coins'=>0, 	'propid'=>0, 'filter'=>array(), 'info'=>"恭喜您人品大爆发，抽中300乐券！\n再接再厉，3000乐券换话费！", 'is_lock'=>0, 'is_del'=>0, 'ut_create'=>0, 'ut_update'=>0),
// '1028' => array('id'=>1028, 'name'=>"10元话费",   	'cateid'=>5, 'icon'=>"0", 'chance'=>75, 	'gold'=>0, 'coupon'=>0, 'coins'=>0, 	'propid'=>0, 'filter'=>array(), 'info'=>"恭喜您人品大爆发，抽中10元话费！\n请加入官方QQ群：10436211 联系领取！", 'is_lock'=>0, 'is_del'=>0, 'ut_create'=>0, 'ut_update'=>0),
// '1029' => array('id'=>1029, 'name'=>"30元话费",   	'cateid'=>5, 'icon'=>"0", 'chance'=>15, 	'gold'=>0, 'coupon'=>0, 'coins'=>0, 	'propid'=>0, 'filter'=>array(), 'info'=>"恭喜您人品大爆发，抽中30元话费！\n请加入官方QQ群：10436211 联系领取！", 'is_lock'=>0, 'is_del'=>0, 'ut_create'=>0, 'ut_update'=>0),
// '1030' => array('id'=>1030, 'name'=>"50元话费",   	'cateid'=>5, 'icon'=>"0", 'chance'=>7,		'gold'=>0, 'coupon'=>0, 'coins'=>0, 	'propid'=>0, 'filter'=>array(), 'info'=>"恭喜您人品大爆发，抽中50元话费！\n请加入官方QQ群：10436211 联系领取！", 'is_lock'=>0, 'is_del'=>0, 'ut_create'=>0, 'ut_update'=>0),
// '1031' => array('id'=>1031, 'name'=>"100元话费",  	'cateid'=>5, 'icon'=>"0", 'chance'=>1,		'gold'=>0, 'coupon'=>0, 'coins'=>0, 	'propid'=>0, 'filter'=>array(), 'info'=>"恭喜您人品大爆发，抽中100元话费！\n请加入官方QQ群：10436211 联系领取！", 'is_lock'=>0, 'is_del'=>0, 'ut_create'=>0, 'ut_update'=>0),
// '1013' => array('id'=>1013, 'name'=>"Ipad air 2",	'cateid'=>5, 'icon'=>"0", 'chance'=>0,		'gold'=>0, 'coupon'=>0, 'coins'=>0, 	'propid'=>0, 'filter'=>array(), 'info'=>"恭喜您人品大爆发，抽中Ipad air 2！\n请加入官方QQ群：10436211 联系领取！", 'is_lock'=>0, 'is_del'=>0, 'ut_create'=>0, 'ut_update'=>0),
// '4001' => array('id'=>4001, 'name'=>"大白公仔",   	'cateid'=>5, 'icon'=>"0", 'chance'=>15,		'gold'=>0, 'coupon'=>0, 'coins'=>0, 	'propid'=>0, 'filter'=>array(), 'info'=>"恭喜您人品大爆发，抽中大白公仔！\n请加入官方QQ群：10436211 联系领取！", 'is_lock'=>0, 'is_del'=>0, 'ut_create'=>0, 'ut_update'=>0),
// '4002' => array('id'=>4002, 'name'=>"有乐公仔",   	'cateid'=>5, 'icon'=>"0", 'chance'=>7,		'gold'=>0, 'coupon'=>0, 'coins'=>0, 	'propid'=>0, 'filter'=>array(), 'info'=>"恭喜您人品大爆发，抽中有乐公仔！\n请加入官方QQ群：10436211 联系领取！", 'is_lock'=>0, 'is_del'=>0, 'ut_create'=>0, 'ut_update'=>0),
// '4003' => array('id'=>4003, 'name'=>"IWatch",   	'cateid'=>5, 'icon'=>"0", 'chance'=>0,		'gold'=>0, 'coupon'=>0, 'coins'=>0, 	'propid'=>0, 'filter'=>array(), 'info'=>"恭喜您人品大爆发，抽中了IWatch！\n请加入官方QQ群：10436211 联系领取！", 'is_lock'=>0, 'is_del'=>0, 'ut_create'=>0, 'ut_update'=>0),
// );
