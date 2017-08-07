<?php

$cmd = 5; $code = 148;
$data['errno'] = 0;
$data['error'] = "";

$ud = $user['uid'];
$rd = $user['roomId'];
$td = $user['tableId'];
$sd = $user['seatId'];

debug("用户查看公告[$fd|$ud|$td|$sd]");

$data_notice_list = array();
include(ROOT.'/include/data_notice_list.php');
$data['noticeList'] = $data_notice_list;

$res = sendToFd($fd, $cmd, $code, $data);


end:{
	// $this->model->record->action($accode, $rd, $td, $ud, $user);
}
