<?php

$ud = $user['uid'];
$md = $user['modelId'];
$rd = $user['roomId'];
$td = $user['tableId'];
$sd = $user['seatId'];

//参数整理
$doLord = intval($params['doLord']); if ( ! in_array($doLord, range(1,2)) ) $doLord = 2;
if ( ! isset($this->rooms[$rd]) || !$td ) {
	debug("叫庄用户失效[$fd|$ud|$td|$sd] roomId=$rd");
	goto end;
}
$res = $this->AUTO_CALL_LORD($td, $user, $doLord);


end:{
	$this->model->record->action($accode, $rd, $td, $ud, $user);
}
