<?php

$ud = $user['uid'];
$rd = $user['roomId'];
$td = $user['tableId'];
$sd = $user['seatId'];
$dd = dateid();

$cmd = 4; $code = 166; $send = array('id'=>111, 'price'=>10, 'goto'=>0, 'msg'=>"测试\n一下。");
sendToFd($fd, $cmd, $code, $send);


end:{
	// $this->model->getRecord()->action($accode, $rd, $td, $ud, $user);
}
