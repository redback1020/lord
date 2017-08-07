<?php

//用户校验
$ud = $user['uid'];
$md = $user['modelId'];
$rd = $user['roomId'];
$td = $user['tableId'];
$sd = $user['seatId'];

if ( ! isset($this->rooms[$rd]) || !$td ) {
	debug("解托用户失效[$fd|$ud|$td|$sd] roomId=$rd");
	goto end;
}

//获取牌桌
$table = $this->model->getTableInfo($td);
if ( !$table ) {
	debug("解托牌桌失效[$fd|$ud|$td|$sd] client-".__LINE__);
}
//校验状态
elseif ( !in_array($table['state'], array(3,4,5,6)) ) {
	debug("解托网络延迟[$fd|$ud|$td|$sd] state3456=".$table['state']);
}
//执行解托 0主动解托
else {
	debug("用户开始解托[$fd|$ud|$td|$sd]");
	$res = $this->USER_DETRUST($fd, $table, $sd, 0 );
}


end:{
	// $this->model->record->action($accode, $rd, $td, $ud, $user);
}
