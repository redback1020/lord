<?php

$ud = $user['uid'];
$md = $user['modelId'];
$rd = $user['roomId'];
$td = $user['tableId'];
$sd = $user['seatId'];

if ( ! isset($this->rooms[$rd]) || !$td ) {
	debug("不跟用户失效[$fd|$ud|$td|$sd] roomId=$rd");
	goto end;
}

//获取牌桌
$table = $this->model->getTableInfo( $td );
if ( !$table )
{
	debug("不跟牌桌失效[$fd|$ud|$td|$sd]");
	goto end;
}
//校验牌桌状态、席位轮流、用户托管
elseif ( $table['state'] != 6 || $table['turnSeat'] != $sd )
{
	debug("不跟网络延迟[$fd|$ud|$td|$sd] state6=".$table['state']." turn".$table['turnSeat']."=".$sd);
	goto end;
}
//校验手牌
elseif ( !$table['seat'.$sd.'cards'] )
{
	gerr("不跟手牌无效[$fd|$ud|$td|$sd] table=".json_encode($table));
	goto end;
}
debug("用户选择不跟[$fd|$ud|$td|$sd]");
//通知牌桌: 某人不跟
$cmd = 5; $code = 1018;
$send = array('callId'=>$sd);
$this->model->sendToTable($table, $cmd, $code, $send);
//累加不跟次数
$newT['noFollow'] = ++$table['noFollow'];
//重设叫牌模式
if( $table['noFollow'] == 2 ) {
	$newT['noFollow'] = $table['noFollow'] = 0;
	$newT['lastCards'] = $table['lastCards'] = array();
	$newT['lastJokto'] = $table['lastJokto'] = array();
	$newT['lastType'] = $table['lastType'] = 0;
}
//轮转下家席位
$newT['turnSeat'] = $table['turnSeat'] = $this->model->getSeatNext($sd);
//更新牌桌信息
if ( ! $this->model->setTableInfo($td, $newT) ) {
	gerr("不跟执行失败[$fd|$ud|$td|$sd] setTableInfo( $td, ".json_encode($newT)." )");
	goto end;
}
//轮到下家打牌
$res = $this->TURN_PLAY_CARD($table);


end:{
	// $this->model->record->action($accode, $rd, $td, $ud, $user);
}
