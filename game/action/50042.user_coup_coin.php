<?php

//用户校验
$ud = $user['uid'];
$md = $user['modelId'];
$rd = $user['roomId'];
$td = $user['tableId'];
$sd = $user['seatId'];

$coupon = intval($params['coupon']);
$coins = intval($coupon * $this->confs['coupon2coin']);//兑换比例
$gold = 0;
$propId = 0;

//预留


end:{
	$this->model->record->action($accode, $rd, $td, $ud, $user);
}
