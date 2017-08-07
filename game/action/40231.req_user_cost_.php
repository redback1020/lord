<?php

$code = 232;
$data['errno'] = 0;
$data['error'] = "";

$ud = $user['uid'];
$rd = $user['roomId'];
$td = $user['tableId'];
$sd = $user['seatId'];

// $items = $this->model->getlistItem(1);//list
// if ( !$items ) $items = array();
$goods = $this->model->getlistGoods('', 1);
if ( !$goods ) $goods = array();
$res = $this->model->listGetCost($ud);
$res = $res ? $res : array();

$list = array();
$totals = array('gold'=>0,'coins'=>0,'coupon'=>0);
foreach ( $res as $k => $v )
{
	$vv['id'] = $v['id']+0;
	$cost = array();
	if ( $v['gold'] < 0) { $cost[]= abs($v['gold'])."乐币"; $totals['gold']+=abs($v['gold']); }
	if ( $v['coins'] < 0) { $cost[]= abs($v['coins'])."乐豆"; $totals['coins']+=abs($v['coins']); }
	if ( $v['coupon'] < 0) { $cost[]= abs($v['coupon'])."乐券"; $totals['coupon']+=abs($v['coupon']); }
	if ( !$cost ) { $cost[]= "管理员送出"; }
	$vv['cost'] = join(' ', $cost);
	$got = array();
	if ( $v['gold'] > 0) { $got[]= $v['gold']."乐币"; }
	if ( $v['coins'] > 0) { $got[]= $v['coins']."乐豆"; }
	if ( $v['coupon'] > 0) { $got[]= $v['coupon']."乐券"; }
	if ( $v['propId'] > 0) { $got[]= isset($goods[$v['propId']]) ? ($goods[$v['propId']]['name']." ✖️ 1") : "未知道具 ✖️ 1"; }
	$vv['got'] = join(' ', $got);
	$vv['date'] = $v['date'];
	$list[] = $vv;
}
$totals_ = array();
foreach ( $totals as $k => $v )
{
	if ( $v > 0 ) {
		switch ($k) {
			case 'gold':
				$totals_[]= $v."乐币";
				break;
			case 'coins':
				$totals_[]= $v."乐豆";
				break;
			case 'coupon':
				$totals_[]= $v."乐券";
				break;
			default:
				# code...
				break;
		}
	}
}
$data['total'] = $totals_ ? join(', ', $totals_) : '0';
$data['list'] = $list;

$res = sendToFd($fd, $cmd, $code, $data);


end:{
	// $this->model->getRecord()->action($accode, $rd, $td, $ud, $user);
}
