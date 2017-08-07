<?php

$cmd = 5; $code = 110; //竞技逃跑
//V10000前后版本兼容
if ( $user['vercode'] == 10000 ) {
	goto end;
}

$ud = $user['uid'];
$md = $user['modelId'];
$rd = 0;
$wd = intval(date("Ymd",time()-(date("N")-1)*86400));
$gd = $user['gameId'];
$td = $user['tableId'];
$sd = $user['seatId'];
$score = $user['score'];
$game = $gd ? $this->model->getModelGame($md, $rd, $wd, $gd) : array();
if ( !$gd || !$game ) {
	$data['errno'] = 1;
	$data['error'] = '牌局正在结算中，请稍候重试。';
	$res = sendToFd($fd, $cmd, $code, $data);
	goto end;
}
if ( !$td || !($table = $this->model->getTableInfo($td)) ) {
	$data['errno'] = 2;
	$data['error'] = '竞技场正在轮桌，请稍候重试。';
	$res = sendToFd($fd, $cmd, $code, $data);
	goto end;
}
if ( $table["seat{$sd}giveup"] ) {
	$data['errno'] = 3;
	$data['error'] = "您已从竞技赛中逃跑，\n请耐心等待本局牌桌比赛结束。";
	$res = sendToFd($fd, $cmd, $code, $data);
	goto end;
}
$newT["seat{$sd}giveup"] = $table["seat{$sd}giveup"] = 1;
$res = $this->model->setTableInfo($td, $newT);
if ( $res ) {
	$data['errno'] = 0;
	$data['error'] = "竞技赛逃跑成功。\n本局比赛结束前，您暂时无法进行进场操作。";
	$res = sendToFd($fd, $cmd, $code, $data);
	goto end;
}


end:{
	$this->model->record->action($accode, $rd, $td, $ud, $user);
}
