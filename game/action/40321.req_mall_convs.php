<?php

//打开充值乐币面板

$ud = $user['uid'];
$rd = $user['roomId'];
$td = $user['tableId'];
$sd = $user['seatId'];
$channel = $user['channel'];
$Utask = $this->model->getUserTask($ud);

//参数处理
$type = isset($params['type']) ? $params['type'] : 'coupon2mobifee';
//查找列表
$list = $this->model->getlistConvert($channel, $type, 0, $Utask['gold_all']);
//发送结果
$cmd = 4; $code = 322;	$send = array( 'errno'=>0, 'error'=>"", 'list'=>$list );
$res = sendToFd($fd, $cmd, $code, $send);


end:{
	// $this->model->getRecord()->action($accode, $rd, $td, $ud, $user);
}
