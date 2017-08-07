<?php

//打开道具面板

$ud = $user['uid'];
$rd = $user['roomId'];
$td = $user['tableId'];
$sd = $user['seatId'];
$channel = $user['channel'];

$time = time();
$prop = $this->model->getlistProp();
$mtem = $this->model->getuserItem($ud, 1);
$list = array();
foreach ( $mtem as $k => $v )
{
	if ( !isset($prop[$v['pd']]) ) continue;
	if ( $v['state'] < 2 ) {
		$mtem[$k]['sec'] = $v['sec'] = intval($v['sec'] > 0 ? $v['sec'] : max(0, $v['end'] > 0 ? ($v['end']-$time) : 0));
	}
	if ( !$v['sec'] || $v['sec'] >= 60 ) {
		$list[$v['cd'].'_'.$v['pd']] = array('propId'=>$v['pd'],'name'=>$prop[$v['pd']]['name'],'fileId'=>$prop[$v['pd']]['fileId'],'sec'=>$v['sec']-1,'desc'=>$prop[$v['pd']]['resume'],'state'=>$v['state']);
	}
}
if ( $list ) {
	ksort($list);
	$list = array_values($list);
}
//发送结果
$cmd=4; $code = 222;	$send = array( 'errno'=>0, 'error'=>"", 'list'=>$list );
$res = sendToFd($fd, $cmd, $code, $send);


end:{
	// $this->model->getRecord()->action($accode, $rd, $td, $ud, $user);
}
