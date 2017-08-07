<?php

$code = 632;
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

// 每周胜局排行榜		普通场每周赢场排行榜
$list = $this->model->zListNormalWeekWin($weekid, 10, $ud);
// $list = array();
$data['listA_myval'] = ( isset($list['myval']) && $list['myval'] ) ? $list['myval'] : (isset($usertask['normal_week_win']) ? $usertask['normal_week_win'] : 0);
$data['listA_myrank'] = isset($list['myrank']) ? $list['myrank'] : 0;
if ( isset($list['myval']) ) unset($list['myval']);
if ( isset($list['myrank']) ) unset($list['myrank']);
$data['listA'] = array_values($list);

// 每周赢取排行榜		普通场每周赢钱排行榜
$list = $this->model->zListNormalWeekEarn($weekid, 10, $ud);
// $list = array();
$data['listB_myval'] = ( isset($list['myval']) && $list['myval'] ) ? $list['myval'] : (isset($usertask['normal_week_earn']) ? $usertask['normal_week_earn'] : 0);
$data['listB_myrank'] = isset($list['myrank']) ? $list['myrank'] : 0;
if ( isset($list['myval']) ) unset($list['myval']);
if ( isset($list['myrank']) ) unset($list['myrank']);
$data['listB'] = array_values($list);

// 每周积分排行榜		竞技场每周积分排行榜
$list = $this->model->zListMatchWeekPoint($weekid, 10, $ud);
// $list = array();
$data['listC_myval'] = ( isset($list['myval']) && $list['myval'] ) ? $list['myval'] : (isset($usertask['match_week_point']) ? $usertask['match_week_point'] : 0);
$data['listC_myrank'] = isset($list['myrank']) ? $list['myrank'] : 0;
if ( isset($list['myval']) ) unset($list['myval']);
if ( isset($list['myrank']) ) unset($list['myrank']);
$data['listC'] = array_values($list);

$weekid = intval(date("Ymd",time()-(date("N")-1)*86400-7*86400));//上周

// 上周胜局排行榜		普通场每周赢场排行榜
$list = $this->model->zListNormalWeekWin($weekid, 10, $ud);
// $list = array();
$data['listD_myval'] = ( isset($list['myval']) && $list['myval'] ) ? $list['myval'] : (isset($usertask['normal_week_win']) ? $usertask['normal_week_win'] : 0);
$data['listD_myrank'] = isset($list['myrank']) ? $list['myrank'] : 0;
if ( isset($list['myval']) ) unset($list['myval']);
if ( isset($list['myrank']) ) unset($list['myrank']);
$data['listD'] = array_values($list);

// 上周赢取排行榜		普通场每周赢钱排行榜
$list = $this->model->zListNormalWeekEarn($weekid, 10, $ud);
// $list = array();
$data['listE_myval'] = ( isset($list['myval']) && $list['myval'] ) ? $list['myval'] : (isset($usertask['normal_week_earn']) ? $usertask['normal_week_earn'] : 0);
$data['listE_myrank'] = isset($list['myrank']) ? $list['myrank'] : 0;
if ( isset($list['myval']) ) unset($list['myval']);
if ( isset($list['myrank']) ) unset($list['myrank']);
$data['listE'] = array_values($list);

// 上周积分排行榜		竞技场每周积分排行榜
$list = $this->model->zListMatchWeekPoint($weekid, 10, $ud);
// $list = array();
$data['listF_myval'] = ( isset($list['myval']) && $list['myval'] ) ? $list['myval'] : (isset($usertask['match_week_point']) ? $usertask['match_week_point'] : 0);
$data['listF_myrank'] = isset($list['myrank']) ? $list['myrank'] : 0;
if ( isset($list['myval']) ) unset($list['myval']);
if ( isset($list['myrank']) ) unset($list['myrank']);
$data['listF'] = array_values($list);

$res = sendToFd($fd, $cmd, $code, $data);


end:{
	// $this->model->getRecord()->action($accode, $rd, $td, $ud, $user);
}
