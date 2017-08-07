<?php

//打开充值乐币面板

$ud = $user['uid'];
$rd = $user['roomId'];
$td = $user['tableId'];
$sd = $user['seatId'];
$channel = $user['channel'];
//查找列表
$list = $this->model->getlistGold($channel);
foreach ( $list as $k => $v )
{
	//显隐检测
	if ( $v['is_hide'] ) {
		unset($list[$k]);
		continue;
	}
    unset($list[$k]['is_hide']);
}
if ( $list ) $list = array_values($list);
//发送结果
$cmd = 4; $code = 312; $send = array('errno'=>0, 'error'=>"", 'list'=>$list);
sendToFd($fd, $cmd, $code, $send);


end:{
	// $this->model->getRecord()->action($accode, $rd, $td, $ud, $user);
}
