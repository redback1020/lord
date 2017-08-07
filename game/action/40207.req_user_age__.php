<?php

$ud = $user['uid'];
$rd = $user['roomId'];
$td = $user['tableId'];
$sd = $user['seatId'];

$code = 208;
$data['errno'] = 0;
$data['error'] = "";

//校验参数
$age = isset($params['age']) ? intval($params['age']) : 0;
if ( !in_array($age, range(0,7)) ) {//[0]未知 [1]1～12 [2]13～18  [3]19～26  [4]27～36  [5]37～50  [6]50～60  [7]61～120
	$res = closeToFd($fd, "params=".json_encode($params));
	goto end;
}

setUser($ud, array('age'=>$age));
sendToFd($fd, $cmd, $code, $data);


end:{
	// $this->model->getRecord()->action($accode, $rd, $td, $ud, $user);
}
