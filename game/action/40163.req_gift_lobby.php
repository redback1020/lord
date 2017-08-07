<?php

$ud = $user['uid'];
$rd = $user['roomId'];
$td = $user['tableId'];
$sd = $user['seatId'];
$dd = dateid();

$teski = $this->model->getlistTesk($user);
$Utesk = $teski['usertesk'];
$tesks = $teski['tesklist'];

$goods = $send = array();
//通知 大厅礼包
$teskYiYuanLiBaoId = 10001;//一元礼包任务ID
if ( ! isset($Utesk["teskdone_$teskYiYuanLiBaoId"]) || ! $Utesk["teskdone_$teskYiYuanLiBaoId"] ) {
	$send = $this->model->getGoodsCtrl('datinglibao', $user['channel']);
	if ( ! $goods ) $goods = $this->model->getlistGoods('', 1);
	foreach ( $send as $k => $conf )
	{
		if ( ! isset($goods[$conf['id']]) ) {
			unset($send[$k]);
			continue;
		}
		$send[$k]['price'] = $goods[$conf['id']]['price'];
		$send[$k]['anim'] = 0;
	}
}
$cmd = 4; $code = 164; $send = $send ? array_values($send) : array();
sendToFd($fd, $cmd, $code, $send);


end:{
	// $this->model->getRecord()->action($accode, $rd, $td, $ud, $user);
}
