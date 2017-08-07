<?php

$isRecord = 1;
$ud = $user['uid'];
$md = $user['modelId'];
$rd = $user['roomId'];
$td = $user['tableId'];
$sd = $user['seatId'];

//获取牌桌
$table = $this->model->getTableInfo($td);
if ( ! $table ) {
	debug("双倍牌桌失效[$fd|$ud|$td|$sd]");
	$isRecord = 0;
	goto end;
}

if ( isset($table["seat{$sd}double"]) && $table["seat{$sd}double"] ) {
	debug("双倍不可重复[$fd|$ud|$td|$sd]");
	$isRecord = 0;
	goto end;
} else {
	$newT["seat{$sd}double"] = 1;
}

debug("用户申请双倍[$fd|$ud|$td|$sd]");

$newT["rate"] = $this->TABLE_NEW_RATE($table, $sd, 2 ,1);
$res = $this->model->setTableInfo($td, $newT);

end:{
	if ( $isRecord ) {
	$this->model->record->action($accode, $rd, $td, $ud, $user);
	}
}
