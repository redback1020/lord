<?php

$ud = $user['uid'];
$rd = $user['roomId'];
$td = $user['tableId'];
$sd = $user['seatId'];

$cmd = 4; $code = 112;
$data['errno'] = 0;
$data['error'] = "";
$data['list'] = $this->model->listGetNewMail($ud);
$data['mail_unread'] = $user['mail_unread'];
sendToFd($fd, $cmd, $code, $data);


end:{
	// $this->model->getRecord()->action($accode, $rd, $td, $ud, $user);
}
