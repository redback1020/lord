<?php

//用户校验
$ud = $user['uid'];
$md = $user['modelId'];
$rd = $user['roomId'];
$td = $user['tableId'];
$sd = $user['seatId'];

if ( ! isset($this->rooms[$rd]) || !$td ) {
	debug("发言用户失效[$fd|$ud|$td|$sd] roomId=$rd");
	goto end;
}
if ( $user['last_action'] === 'SAY_TO_TABLE' && (microtime(1)-$user['last_time']) < 3 ) {
	// debug("发言过快[$fd|$ud|$td|$sd] user=".json_encode($user));
	goto end;
}

//参数整理
$word = isset( $params['word']) ? $params['word'] : '' ;
if ( !$word ) {
	goto end;
}

//获取牌桌
$table = $this->model->getTableInfo($td);
if ( ! $table ) {
	debug("发言牌桌失效[$fd|$ud|$td|$sd] client-".__LINE__);
	goto end;
}
//校验牌桌
elseif ( ! in_array($table['state'], array(3,4,5,6)) ) {
	debug("发言网络延迟[$fd|$ud|$td|$sd] state3456=".$table['state']);
	goto end;
}

debug("用户牌桌发言[$fd|$ud|$td|$sd]");

//通知牌桌
$cmd = 5; $code = 3000; $send = array( 'word' => $word, 'sayId' => intval($sd) );
$this->model->sendToTable($table, $cmd, $code, $send);


end:{
	// $this->model->record->action($accode, $rd, $td, $ud, $user);
}
