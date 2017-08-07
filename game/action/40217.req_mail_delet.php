<?php

$code = 218;
$data['errno'] = 0;
$data['error'] = "";

//校验参数
$id = isset($params['id']) ? intval($params['id']) : 0;
if ( !$id ) {
	$res = closeToFd( $fd, "邮件参数无效 params=".json_encode($params) );
	goto end;
}

$ud = $user['uid'];
$rd = $user['roomId'];
$td = $user['tableId'];
$sd = $user['seatId'];

$res = $this->model->listDelInbox($id);

// 不用返回成功与否
// $res = sendToFd($fd, $cmd, $code, $data);


end:{
	// $this->model->getRecord()->action($accode, $rd, $td, $ud, $user);
}
