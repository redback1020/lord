<?php

//用户换装

$ud = $user['uid'];
$md = $user['modelId'];
$rd = $user['roomId'];
$td = $user['tableId'];
$sd = $user['seatId'];

//校验参数
$id = isset($params['propId']) ? intval($params['propId']) : 0;
$props = $this->model->getlistProp();
if ( ! isset($props[$id]) ) {
	$res = closeToFd($fd, "道具参数无效 params=".json_encode($params));
	goto end;
}
//执行换装
$dress = $this->model->itemDress($ud, $id);
debug("用户执行换装 F=$fd U=$ud T=$td S=$sd I=$id dress=".json_encode($dress));
//发送结果
$cmd = 5; $code = 124;	$send = array( "errno" => 0, "error" => "套装穿戴成功。", "propDress" => $dress );
$res = sendToFd($fd, $cmd, $code, $send);


end:{
	// $this->model->record->action($accode, $rd, $td, $ud, $user);
}
