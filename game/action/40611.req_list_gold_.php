<?php

$code = 612;
$data['errno'] = 0;
$data['error'] = "";

$ud = $user['uid'];
$rd = $user['roomId'];
$td = $user['tableId'];
$sd = $user['seatId'];

$usertask = $this->model->getUserTask($ud);

$dateid = intval(date("Ymd"));//今日
$weekid = intval(date("Ymd",time()-(date("N")-1)*86400));//本周
$monthid = intval(date("Ym01"));//本月

// 今日使用金币排行榜
$list = $this->model->zListUserDayCost($dateid, 10, $ud);
// $list = array();
$data['listA_myval'] = ( isset($list['myval']) && $list['myval'] ) ? $list['myval'] : (isset($usertask['gold_day']) ? $usertask['gold_day'] : 0);
$data['listA_myrank'] = isset($list['myrank']) ? $list['myrank'] : 0;
if ( isset($list['myval']) ) unset($list['myval']);
if ( isset($list['myrank']) ) unset($list['myrank']);
$data['listA'] = array_values($list);

// 今日充值金币排行榜
$list = $this->model->zListUserDayRecharge($dateid, 10, $ud);
// $list = array();
$data['listB_myval'] = ( isset($list['myval']) && $list['myval'] ) ? $list['myval'] : (isset($usertask['recharge_day']) ? $usertask['recharge_day'] : 0);
$data['listB_myrank'] = isset($list['myrank']) ? $list['myrank'] : 0;
if ( isset($list['myval']) ) unset($list['myval']);
if ( isset($list['myrank']) ) unset($list['myrank']);
$data['listB'] = array_values($list);

// 本月累计充值排行榜
$list = $this->model->zListUserMonthRecharge($monthid, 10, $ud);
// $list = array();
$data['listC_myval'] = ( isset($list['myval']) && $list['myval'] ) ? $list['myval'] : (isset($usertask['recharge_month']) ? $usertask['recharge_month'] : 0);
$data['listC_myrank'] = isset($list['myrank']) ? $list['myrank'] : 0;
if ( isset($list['myval']) ) unset($list['myval']);
if ( isset($list['myrank']) ) unset($list['myrank']);
$data['listC'] = array_values($list);

// 测试数据
$data['listA'] = array(
	array('id'=>11211,'nick'=>'今日使用金币榜','val'=>888,'rank'=>1),
	array('id'=>11212,'nick'=>'昵称2','val'=>777,'rank'=>2),
	array('id'=>11213,'nick'=>'昵称3','val'=>666,'rank'=>3),
	array('id'=>11214,'nick'=>'昵称4','val'=>600,'rank'=>4),
	array('id'=>11215,'nick'=>'昵称5','val'=>512,'rank'=>5),
	array('id'=>11216,'nick'=>'昵称6','val'=>500,'rank'=>6),
	array('id'=>11217,'nick'=>'昵称7','val'=>499,'rank'=>7),
	array('id'=>11218,'nick'=>'昵称8','val'=>465,'rank'=>8),
	array('id'=>11219,'nick'=>'昵称9','val'=>377,'rank'=>9),
	array('id'=>11221,'nick'=>'昵称10','val'=>366,'rank'=>10),
);
$data['listA_myval'] = 233;
$data['listA_myrank'] = 88;
$data['listB'] = array(
	array('id'=>11211,'nick'=>'本月充值金币榜','val'=>1120,'rank'=>1),
	array('id'=>11212,'nick'=>'昵称2','val'=>1115,'rank'=>2),
	array('id'=>11213,'nick'=>'昵称3','val'=>1111,'rank'=>3),
	array('id'=>11214,'nick'=>'昵称4','val'=>1100,'rank'=>4),
	array('id'=>11215,'nick'=>'昵称5','val'=>1012,'rank'=>5),
	array('id'=>11216,'nick'=>'昵称6','val'=>1000,'rank'=>6),
	array('id'=>11217,'nick'=>'昵称7','val'=>999,'rank'=>7),
	array('id'=>11218,'nick'=>'昵称8','val'=>965,'rank'=>8),
	array('id'=>11219,'nick'=>'昵称9','val'=>777,'rank'=>9),
	array('id'=>11221,'nick'=>'昵称10','val'=>666,'rank'=>10),
);
$data['listB_myval'] = 111;
$data['listB_myrank'] = 0;
$data['listC'] = array(
	array('id'=>11211,'nick'=>'本月充值金币榜','val'=>11120,'rank'=>1),
	array('id'=>11212,'nick'=>'昵称2','val'=>11115,'rank'=>2),
	array('id'=>11213,'nick'=>'昵称3','val'=>11111,'rank'=>3),
	array('id'=>11214,'nick'=>'昵称4','val'=>11100,'rank'=>4),
	array('id'=>11215,'nick'=>'昵称5','val'=>11012,'rank'=>5),
	array('id'=>11216,'nick'=>'昵称6','val'=>11000,'rank'=>6),
	array('id'=>11217,'nick'=>'昵称7','val'=>1999,'rank'=>7),
	array('id'=>11218,'nick'=>'昵称8','val'=>1965,'rank'=>8),
	array('id'=>11219,'nick'=>'昵称9','val'=>1777,'rank'=>9),
	array('id'=>11221,'nick'=>'昵称10','val'=>1666,'rank'=>10),
);
$data['listC_myval'] = 11120;
$data['listC_myrank'] = 1;

$res = sendToFd($fd, $cmd, $code, $data);


end:{
	// $this->model->getRecord()->action($accode, $rd, $td, $ud, $user);
}
