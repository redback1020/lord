<?php

$ud = $user['uid'];
$rd = $user['roomId'];
$td = $user['tableId'];
$sd = $user['seatId'];

debug("查看首登界面[$fd|$ud|$td|$sd]");

$res = $this->ACT_LOGIN_DAY0($user);


end:{
	// $this->model->record->action($accode, $rd, $td, $ud, $user);
}
