<?php

$code = 402;
$data['errno'] = 0;
$data['error'] = "";

$ud = $user['uid'];
$rd = $user['roomId'];
$td = $user['tableId'];
$sd = $user['seatId'];
$channel = $user['channel'];
// $data['navi'] = array('topic_index','topic_notice');
$data['list'] = $this->model->listGetTopic($channel);

$res = sendToFd($fd, $cmd, $code, $data);

$cmd = 4; $code = 234; $send = array('tips'=>0,'errno'=>0,'error'=>'');
sendToFd($fd, $cmd, $code, $send);


end:{
	// $this->model->getRecord()->action($accode, $rd, $td, $ud, $user);
}
