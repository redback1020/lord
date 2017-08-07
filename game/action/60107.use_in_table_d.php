<?php

// 执行争抢牌桌奖励

$ud = $user['uid'];
$md = $user['modelId'];
$rd = $user['roomId'];
$td = $user['tableId'];
$sd = $user['seatId'];

$code = 108;
$data['errno'] = 0;
$data['error'] = "";

//校验参数
$id = isset($params['id']) ? intval($params['id']) : 0;
if ( !$id ) {
	$res = closeToFd( $fd, "抢奖参数无效 params=".json_encode($params) );
	goto end;
}

debug("抢奖请求发起[$fd|$ud|$td|$sd] --- id=$id");
$res = $this->model->bobGRTS($id, $ud);
goto end;


//加事务锁
$lockId = $action.'_'.$id;
$res = setLock($lockId);
if ( !$res ) goto end;
//获取数据
$surprise = $this->redis->hget('redis_user_tesksurprise', $id);
if ( !$surprise || !isset($surprise['tableId']) || $surprise['tableId'] != $td ) {
	debug("抢奖奖品失效[$fd|$ud|$td|$sd] id=$id");
	$res = delLock($lockId);
	goto end;
}
//争抢失败
if ( $surprise['uid'] ) {
	debug("抢奖奖品手慢[$fd|$ud|$td|$sd] not id=$id");
	// $data = $surprise['failed'];
	// $data['error'] = sprintf($data['error'], $surprise['nicks'][$surprise['uid']]);
	// $res = sendToFd($fd, $cmd, $code, $data);
	$res = delLock($lockId);
	goto end;
}
//获取牌桌
$table = $this->model->getTableInfo( $td );
if ( !$table ) {
	debug("抢奖牌桌失效[$fd|$ud|$td|$sd] id=$id");
	$res = delLock($lockId);
	goto end;
}
//发送
foreach ( $table['seats'] as $ud=>$sid ) {
	$player = array('fd'=>$table["seat{$sid}fd"], 'uid'=>$ud, 'tableId'=>$table['tableId']);
	$res = $this->model->sendToPlayer($player, $cmd, $code, $data);
}

$res = sendToFd($fd, $cmd, $code, $data);


end:{
	// $this->model->record->action($accode, $rd, $td, $ud, $user);
}
