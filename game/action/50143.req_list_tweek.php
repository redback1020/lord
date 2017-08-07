<?php

$ud = $user['uid'];
$rd = $user['roomId'];
$td = $user['tableId'];
$sd = $user['seatId'];

$code = 144;
$type = 2;
$res = $this->ACT_LIST_SHOW($fd, $cmd, $param, $code, $type, $user);


end:{
	// $this->model->record->action($accode, $rd, $td, $ud, $user);
}
