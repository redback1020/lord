<?php

//抢地主/不抢

$doLord = $params['grabLord'];
$doLord = in_array($doLord, range(1,2)) ? $doLord : 2;

$ud = $user['uid'];
$md = $user['modelId'];
$rd = $user['roomId'];
$td = $user['tableId'];
$sd = $user['seatId'];
if ( ! isset($this->rooms[$rd]) || !$td ) {
	debug("抢庄用户失效[$fd|$ud|$td|$sd] roomId=$rd");
	goto end;
}

$res = $this->AUTO_GRAB_LORD($td, $user, $doLord);

//动态任务
if ( $doLord == 1 ) {
	$tesk = new tesk($this->mysql, $this->redis, $accode, $action);
	$utesk = array();
	$param = 1;
	$table = $this->model->getTableInfo($td);
	if ( $addU = $tesk->execute('user_grab_lord', $user, $utesk, $param, $table) ) {
		foreach ( $addU as $k => $v ) $this->model->record->money('动态任务', $k, $v, $ud, $user);
		if ( ($res = $this->model->incUserInfo($ud, $addU)) && $res['send'] ) sendToFd($fd, 4, 110, $res['send']);
	}
}


end:{
	$this->model->record->action($accode, $rd, $td, $ud, $user);
}
