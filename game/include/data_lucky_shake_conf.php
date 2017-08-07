<?php

// data_lottery_rules.php

$data_lucky_shake_rules = array('K以上单张'=>1.2,'对子'=>2,'顺子(花色不同)'=>4,'同花(花色相同但非顺子)'=>6,'同花顺(花色相同)'=>50,'豹子(除A外的三张牌)'=>88,'豹子A（3张A）'=>188);
$data_lucky_shake_conf = array(
    //新手场    other_win_type 0:乐豆，1:乐券
    '1000'=>array('threshold'=>2000,'one_cost'=>200,'day_times'=>20,'prize'=>2,'goods_id'=>24,'goods_name'=>'2元礼包','price'=>2,'other_win'=>1,'other_win_type'=>1),
    //初级场
    '1001'=>array('threshold'=>5000,'one_cost'=>1000,'day_times'=>50,'prize'=>6,'goods_id'=>24,'goods_name'=>'2元礼包','price'=>2,'other_win'=>5,'other_win_type'=>1),
    //中级场
    '1002'=>array('threshold'=>50000,'one_cost'=>5000,'day_times'=>100,'prize'=>10,'goods_id'=>5,'goods_name'=>'10元礼包','price'=>10,'other_win'=>25,'other_win_type'=>1),
    //高级场
	'1003'=>array('threshold'=>200000,'one_cost'=>10000,'day_times'=>100,'prize'=>50,'goods_id'=>7,'goods_name'=>'50元礼包','price'=>50,'other_win'=>50,'other_win_type'=>1),
	'1006'=>array('threshold'=>200000,'one_cost'=>10000,'day_times'=>100,'prize'=>50,'goods_id'=>7,'goods_name'=>'50元礼包','price'=>50,'other_win'=>50,'other_win_type'=>1),
    //癞子新手场
    '1007'=>array('threshold'=>2000,'one_cost'=>200,'day_times'=>20,'prize'=>2,'goods_id'=>24,'goods_name'=>'2元礼包','price'=>2,'other_win'=>1,'other_win_type'=>1),
    //初级场
    '1008'=>array('threshold'=>5000,'one_cost'=>1000,'day_times'=>50,'prize'=>6,'goods_id'=>25,'goods_name'=>'6元礼包','price'=>6,'other_win'=>5,'other_win_type'=>1),
    //中级场
    '1009'=>array('threshold'=>50000,'one_cost'=>5000,'day_times'=>100,'prize'=>10,'goods_id'=>5,'goods_name'=>'10元礼包','price'=>10,'other_win'=>25,'other_win_type'=>1),
    //高级场
	'1010'=>array('threshold'=>200000,'one_cost'=>10000,'day_times'=>100,'prize'=>50,'goods_id'=>7,'goods_name'=>'50元礼包','price'=>50,'other_win'=>50,'other_win_type'=>1),
	'1011'=>array('threshold'=>200000,'one_cost'=>10000,'day_times'=>100,'prize'=>50,'goods_id'=>7,'goods_name'=>'50元礼包','price'=>50,'other_win'=>50,'other_win_type'=>1),

);
//0 无 1 单张 2对子 3 顺子 4 同花 5 同花顺 6 豹子 7 豹子A
$data_lucky_shake_rates = array(

        '0'=>60000,
        '1'=>30000,
        '2'=>7000,
        '3'=>2000,
        '4'=>1000,
        '5'=>5,
        '6'=>2,
        '7'=>1,

);
