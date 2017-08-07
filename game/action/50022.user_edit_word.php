<?php

//用户校验
$ud = $user['uid'];
$md = $user['modelId'];
$rd = $user['roomId'];
$td = $user['tableId'];
$sd = $user['seatId'];

$col = 'word'; $ccn = '改签';
$res = $this->EXEC_EDIT_USER($fd, $cmd, $params, $col, $ccn, $user);


end:{
	// $this->model->record->action($accode, $rd, $td, $ud, $user);
}
