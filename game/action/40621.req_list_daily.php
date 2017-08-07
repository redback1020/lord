<?php

$code = 622;
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

// 今日胜局排行榜		普通场每日赢场排行榜
$list = $this->model->zListNormalDayWin($dateid, 10, $ud);
// $list = array();
$data['listA_myval'] = ( isset($list['myval']) && $list['myval'] ) ? $list['myval'] : (isset($usertask['normal_day_win']) ? $usertask['normal_day_win'] : 0);
$data['listA_myrank'] = isset($list['myrank']) ? $list['myrank'] : 0;
if ( isset($list['myval']) ) unset($list['myval']);
if ( isset($list['myrank']) ) unset($list['myrank']);
$data['listA'] = array_values($list);

// 今日赢取排行榜		普通场每日赢钱排行榜
$list = $this->model->zListNormalDayEarn($dateid, 10, $ud);
// $list = array();
$data['listB_myval'] = ( isset($list['myval']) && $list['myval'] ) ? $list['myval'] : (isset($usertask['normal_day_earn']) ? $usertask['normal_day_earn'] : 0);
$data['listB_myrank'] = isset($list['myrank']) ? $list['myrank'] : 0;
if ( isset($list['myval']) ) unset($list['myval']);
if ( isset($list['myrank']) ) unset($list['myrank']);
$data['listB'] = array_values($list);

// 今日倍率排行榜		普通场每日倍率排行榜
$list = $this->model->zListNormalDayMaxrate($dateid, 10, $ud);
// $list = array();
$data['listC_myval'] = ( isset($list['myval']) && $list['myval'] ) ? $list['myval'] : (isset($usertask['normal_day_maxrate']) ? $usertask['normal_day_maxrate'] : 0);
$data['listC_myrank'] = isset($list['myrank']) ? $list['myrank'] : 0;
if ( isset($list['myval']) ) unset($list['myval']);
if ( isset($list['myrank']) ) unset($list['myrank']);
$data['listC'] = array_values($list);

$res = sendToFd($fd, $cmd, $code, $data);


end:{
	// $this->model->getRecord()->action($accode, $rd, $td, $ud, $user);
}
