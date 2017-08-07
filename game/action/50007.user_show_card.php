<?php

$ud = $user['uid'];
$md = $user['modelId'];
$rd = $user['roomId'];
$td = $user['tableId'];
$sd = $user['seatId'];
//参数整理
$rate_showcard = intval($params['showRate']);//明牌倍数5432
if ( ! in_array($rate_showcard, range(2,5)) ) {
	$res = closeToFd($fd, "明牌参数无效 params=".json_encode($params));
	goto end;
}
if ( ! $rd || ! isset($this->rooms[$rd]) || !$td ) {
	debug("明牌用户失效[$fd|$ud|$td|$sd] roomId=$rd");
	goto end;
}
//获取牌桌
$table = $this->model->getTableInfo($td);
if ( !$table ) {
	debug("明牌牌桌失效[$fd|$ud|$td|$sd] params=".json_encode($params));
	goto end;
}
if ( $table["seat{$sd}show"] ) {
	debug("明牌用户重复[$fd|$ud|$td|$sd] params=".json_encode($params));
	goto end;
}
debug("用户请求明牌[$fd|$ud|$td|$sd]");
//通知牌桌: 有人明牌
$code = 1019;//
$data = array();
$data['showCardId'] = $sd;
$data['showCardInfo'] = $table["seat{$sd}cards"];
$res = $this->model->sendToTable( $table, $cmd, $code, $data, __LINE__ );
//如果还没有人明牌
if ( $table['firstShow'] == 4 ) {
	if ( $rate_showcard > 1 ) {
		//通知牌桌: 倍率变更
		$newT['rate'] = $newT['rate_'] = $table['rate'] = $table['rate_'] = $this->TABLE_NEW_RATE( $table, $sd, $rate_showcard );
	}
	$newT['firstShow'] = $table['firstShow'] = $sd;
}
$newT["seat{$sd}show"] = 1;
//更新牌桌数据
$res = $this->model->setTableInfo($td, $newT);
if ( ! $res ) {
	gerr("明牌执行失败[$td] setTableInfo( $td, ".json_encode($newT)." )");
	goto end;
}


end:{
	$this->model->record->action($accode, $rd, $td, $ud, $user);
}
