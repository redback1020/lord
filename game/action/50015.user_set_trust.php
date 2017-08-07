<?php

//用户校验
$ud = $user['uid'];
$md = $user['modelId'];
$rd = $user['roomId'];
$td = $user['tableId'];
$sd = $user['seatId'];

if ( ! isset($this->rooms[$rd]) || !$td ) {
	debug("托管用户失效[$fd|$ud|$td|$sd] roomId=$rd");
	goto end;
}

//获取牌桌
$table = $this->model->getTableInfo($td);
if ( !$table ) {
	debug("托管牌桌失效[$fd|$ud|$td|$sd] client-".__LINE__);
}
//校验状态
elseif ( !in_array($table['state'], array(3,4,5,6)) ) {
	debug("托管网络延迟[$fd|$ud|$td|$sd] state3456=".$table['state']);
}
//执行托管 1主动托管
else {
	debug("用户开始托管[$fd|$ud|$td|$sd]");
	$table = $this->USER_ENTRUST($fd, $table, $sd, 1 );
}


end:{
	// $this->model->record->action($accode, $rd, $td, $ud, $user);
}
