<?php

//用户道具详情

//用户数据
$ud = $user['uid'];
$rd = $user['roomId'];
$td = $user['tableId'];
$sd = $user['seatId'];
$channel = $user['channel'];

$time = time();
$goods = $this->model->getlistGoods('', 1);
$conf = $this->model->getGoodsCtrl('baoyuelibao', $channel);
if ( !$conf || !isset($goods[$conf['id']]) ) {
	$cmd = 4; $code = 224; $send = array('errno'=>1, 'error'=>"很抱歉，包月礼包已下架，暂时不能购买。", 'isPush'=>0);
	$res = sendToFd($fd, $cmd, $code, $send);
	goto end;
}
$mtem = $this->model->getuserItem($ud, 1);
$pd = 7;
$pdSec = 0;
foreach ( $mtem as $k => $v )
{
	if ( $v['state'] < 2 && $v['pd'] == $pd ) {
		$pdSec += intval($v['sec'] > 0 ? $v['sec'] : max(0, $v['end'] > 0 ? ($v['end']-$time) : 0));
	}
}

//发送结果
$cmd = 4; $code = 224; $send = array('errno'=>0, 'error'=>"", 'isPush'=>0, 'state'=>intval($this->model->getMcard($ud)), 'id'=>$conf['id'], 'price'=>$goods[$conf['id']]['price'], 'fileId'=>$conf['fileId'], 'sec'=>$pdSec);
$res = sendToFd($fd, $cmd, $code, $send);


end:{
	// $this->model->getRecord()->action($accode, $rd, $td, $ud, $user);
}
