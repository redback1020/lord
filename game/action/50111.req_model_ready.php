<?php

$ud = $user['uid'];
$md = $user['modelId'];
$rd = $user['roomId'];
$td = $user['tableId'];
$sd = $user['seatId'];

$fdinfo['is_lock'] = 0;
setBind($fd, $fdinfo);

//通知牌桌: 竞技已准备
$cmd = 5; $code = 128; $send = array();
sendToFd($fd, $cmd, $code, $send);


end:{
	// $this->model->record->action($accode, $rd, $td, $ud, $user);
}
