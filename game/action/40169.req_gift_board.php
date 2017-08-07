<?php



$send['errno'] = 0;
$send['error'] = "";

$ud = $user['uid'];
$rd = $user['roomId'];
$td = $user['tableId'];
$sd = $user['seatId'];
$dd = dateid();

$teski = $this->model->getlistTesk($user);
$Utesk = $teski['usertesk'];
$tesks = $teski['tesklist'];
$teskXinShouLibaoId = 10200;//新手礼包任务ID

$list = $this->model->redis->hget('lord_libao_'.$dd, $ud);
if ( ! $list ) {
	$list = array();
} else {
	foreach ( $list as $k => $v )
	{
		$list[$k]['anim'] = 0;
	}
	krsort($list);
	$list = array_values($list);
}
//新手礼包
$goods = array();
if ( ! isset($Utesk["teskdone_$teskXinShouLibaoId"]) || ! $Utesk["teskdone_$teskXinShouLibaoId"] ) {
	$conf = $this->model->getGoodsCtrl('xinshoulibao', $user['channel']);
	if ( ! $goods ) $goods = $this->model->getlistGoods('', 1);
	if ( $conf && isset($goods[$conf['id']]) ) {
		$list[] = array('isPush'=>1, 'id'=>$conf['id'], 'price'=>$goods[$conf['id']]['price'], 'fileId'=>$conf['fileId'], 'title'=>$conf['title'], 'bar'=>$conf['bar'], 'anim'=>$conf['anim'], 'goto'=>$conf['goto']);
	}
}

$cmd = 4; $code = 170; $send = array('errno'=>0, 'error'=>'', 'sec'=>strtotime(date('Y-m-d 00:00:00', time()+86400))-time(), 'list'=>$list);
$res = sendToFd($fd, $cmd, $code, $send);


end:{
	// $this->model->getRecord()->action($accode, $rd, $td, $ud, $user);
}
