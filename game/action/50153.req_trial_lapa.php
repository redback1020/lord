<?php

//本协议代码 预留

$ud = $user['uid'];
$rd = $user['roomId'];
$td = $user['tableId'];
$sd = $user['seatId'];

debug("打开拉霸面板[$fd|$ud|$td|$sd]");
//默认协议
$cmd = 5; $code = 154; $send = array( 'errno' => 0, 'error' => "" );
sendToFd($fd, $cmd, $code, $data);


end:{
	// $this->model->record->action($accode, $rd, $td, $ud, $user);
}
