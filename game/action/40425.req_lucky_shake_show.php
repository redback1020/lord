<?php

//摇摇乐面板

$ud = $user['uid'];
$rd = $user['lastRoomId'] ? $user['lastRoomId'] : $user['roomId'];
$td = $user['tableId'];
$dd = dateid();

$errno = 0;
$last_times = 0;//当日剩余次数
$one_cost = 0;//一次抽奖所需要消费的乐豆
$rules = array();//抽奖规则
$errs= array(
    0=>'操作成功',
    1=>'房间无效',
    2=>'已经达到当日上限',
    3=>'很抱歉，抽奖系统错误，请稍候重试。\n您也可以联系官方QQ群：11032773。',
    4=>'未达到抽奖门槛',
);

if ( ! $rd ) {
	$errno = 1;
} else {
	include(ROOT.'/include/data_lucky_shake_conf.php');
	$data_conf = isset($data_lucky_shake_conf[$rd]) ? $data_lucky_shake_conf[$rd] : array();
	if ( ! $data_conf ) {
	    $errno = 1;
	} else {
	    $times = $this->model->getLuckyShake($ud,$dd,$rd);
	    $last_times = $data_conf['day_times'] - $times;
	    $last_times = $last_times > 0 ? $last_times : 0;
	    $one_cost = $data_conf['one_cost'];
	}
}

$cmd = 4; $code = 426; $send = array('errno'=>$errno,'error'=>$errs[$errno],'rules'=>$rules,'times'=>$last_times,'one_cost'=> $one_cost);
$res = sendToFd($fd, $cmd, $code, $send);


end:{
	// $this->model->record->action($accode, $rd, $td, $ud, $user);
}
