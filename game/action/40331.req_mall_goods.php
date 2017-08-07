<?php

//打开道具面板

$time = time();
$dd = dateid();
$ud = $user['uid'];
$rd = $user['roomId'];
$td = $user['tableId'];
$sd = $user['seatId'];
$channel = $user['channel'];
$Utask = $this->model->getUserTask($ud);
$res = $this->model->getlistTesk($user);
$tesk = $res['usertesk'];

//查找列表
$list = $this->model->getlistGoods($channel);
$mtem = $this->model->getuserItem($ud, 1);
$pdSec = array();
foreach ( $mtem as $k => $v )
{
	if ( $v['state'] < 2 ) {
		$sec = intval($v['sec'] > 0 ? $v['sec'] : max(0, $v['end'] > 0 ? ($v['end']-$time) : 0));
		$pdSec[$v['pd']] = $sec + (isset($pdSec[$v['pd']]) ? $pdSec[$v['pd']] : 0);
	}
}
foreach ( $pdSec as $k => $v )
{
	if ( ! $v ) $pdSec[$k] = $user['vercode'] < 10800 ? (999 * 86400) : -1;
}
foreach ( $list as $k => $v )
{
	//任务检测
	if ( $v['taskid'] && isset($tesk['teskdone_'.$v['taskid']]) && $tesk['teskdone_'.$v['taskid']] ) {
		unset($list[$k]);
		continue;
	}
	//显隐检测
	if ( $v['is_hide'] ) {
		unset($list[$k]);
		continue;
	}
	//版本检测
	if ( $v['id'] == 22 && isset($user['vercode']) && $user['vercode'] < 10600 ) {
		unset($list[$k]);
		continue;
	}
	$list[$k]['sec'] = isset($pdSec[$v['ipd']]) && $pdSec[$v['ipd']] && ($pdSec[$v['ipd']] < 0 || $pdSec[$v['ipd']] >= 60) ? $pdSec[$v['ipd']] : 0;
	unset($list[$k]['iid']);
	unset($list[$k]['icd']);
	unset($list[$k]['ipd']);
	unset($list[$k]['buyto']);
	unset($list[$k]['taskid']);
	unset($list[$k]['is_hide']);
}
if ( $list ) $list = array_values($list);
//发送结果
$cmd = 4; $code = 332; $send = array('errno'=>0, 'error'=>"", 'list'=>$list);
sendToFd($fd, $cmd, $code, $send);


end:{
	// $this->model->getRecord()->action($accode, $rd, $td, $ud, $user);
}
