<?php

$ud = $user['uid'];
$rd = $user['roomId'];
$td = $user['tableId'];
$sd = $user['seatId'];

$code = 142;
$type = 1;
$res = $this->ACT_LIST_SHOW($fd, $cmd, $param, $code, $type, $user);


end:{
	// $this->model->record->action($accode, $rd, $td, $ud, $user);
}
