<?php

$ud = $user['uid'];
$rd = $user['roomId'];
$td = $user['tableId'];
$sd = $user['seatId'];

//校验参数
$id = isset($params['id']) ? intval($params['id']) : 0;
if ( !$id ) {
	$res = closeToFd( $fd, "邮件参数无效 params=".json_encode($params) );
	goto end;
}

$cmd = 4; $code = 214; $send = array( 'errno' => 0, 'error' => '', 'mail_unread' => $this->model->listSetInboxRead($ud, $id) );
sendToFd($fd, $cmd, $code, $send);


end:{
	// $this->model->getRecord()->action($accode, $rd, $td, $ud, $user);
}
