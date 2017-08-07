<?php

//执行停用/启用某道具ID

$ud = $user['uid'];
$rd = $user['roomId'];
$td = $user['tableId'];
$sd = $user['seatId'];

//校验参数
$id = isset($params['propId']) ? intval($params['propId']) : 0;
$st = isset($params['state']) ? intval($params['state']) : 0;
$ids = array(5);//
$sts = array(0, 1);//
if ( !in_array($id, $ids) || !in_array($st, $sts) ) {
	$res = closeToFd($fd, "道具参数无效 params=".json_encode($params));
	goto end;
}
//执行切换
// $state = $this->model->itemShift($ud, $id, $st, $user);
$state = $st;
debug("用户切换道具 F=$fd U=$ud T=$td S=$sd I=$id $st<>$state");
//发送结果
$cmd = 6; $code = 110;	$send = array( "errno" => 0, "error" => "", "propId" => $id, "state" => $state );
$res = sendToFd($fd, $cmd, $code, $send);


end:{
	// $this->model->record->action($accode, $rd, $td, $ud, $user);
}
