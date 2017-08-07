<?php

$ud = $user['uid'];
$rd = $user['roomId'];
$td = $user['tableId'];
$sd = $user['seatId'];

$code = 212;
$data['errno'] = 0;
$data['error'] = "";

$mails = $this->model->listGetInbox($ud);
$mails = $mails ? $mails : array();
foreach ( $mails as $k => $v )
{
	$mails[$k]['items'] = intval(!!$v['items']);
}
$data['list'] = $mails;

sendToFd($fd, $cmd, $code, $data);


end:{
	// $this->model->getRecord()->action($accode, $rd, $td, $ud, $user);
}
