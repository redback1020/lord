<?php

//默认协议 兑换结果
$cmd=4; $code = 324;
//用户信息
$ud = $user['uid'];
$rd = $user['roomId'];
$td = $user['tableId'];
$sd = $user['seatId'];
$channel = $user['channel'];
$Utask = $this->model->getUserTask($ud);

//参数信息
if ( !isset($params['id']) || !$params['id'] ) {
	$res = closeToFd($fd, "[$accode] params=".json_encode($params));
	goto end;
}
$id = intval($params['id']);
$num = 1;//预留 个数因素
$Utask = $this->model->getUserTask($ud);
//加锁 兑换物品ID
$lockId = 'CONVERT_ID_'.$id;
$res = setLock($lockId);
if ( !$res ) {
	gerr("[LOCKON] [$action] lockId=$lockId params=".json_encode($params));
	goto end;
}
$list = $this->model->getlistConvert($channel, '', 1, $Utask['gold_all']);
if ( !isset($list[$id]) ) {
	//通知用户 操作失败
	$data['errno'] = 1;
	$data['error'] = "物品不存在，无法兑换";
	$data['store'] = -2;
	$res = sendToFd($fd, $cmd, $code, $data);
	delLock($lockId);
	goto end;
}
$item = $list[$id];
$type_ = explode('2', $item['type']);
$moneys = array('coupon'=>"乐券");//
$conv_fr  = $type_[0];//'coupon';
$conv_frn = isset($moneys[$conv_fr]) ? $moneys[$conv_fr] : "";
$conv_to  = $type_[1];//'mobifee';
$conv_ton = $item['title'];
if ( !isset($params['mobile']) || !$params['mobile'] || !($mobile = preg_match("/^(1[3-9][0-9]{9})$/", $params['mobile'], $m) ? $m[1] : false) ) {
	//通知用户 操作失败
	$send = array('errno'=>2, 'error'=>"手机号码错误，无法兑换\n$mobile", 'store'=>-2);
	$res = sendToFd($fd, $cmd, $code, $send);
	delLock($lockId);
	goto end;
}
if ( !$type_ || count($type_) != 2 || !$conv_frn ) {
	//通知用户 操作失败
	$data['errno'] = 3;
	$data['error'] = "物品不存在，无法兑换";
	$data['store'] = -2;
	$res = sendToFd($fd, $cmd, $code, $data);
	delLock($lockId);
	goto end;
}
if ( $item['price'] > $user[$conv_fr] ) {
	//通知用户 操作失败
	$data['errno'] = 4;
	$data['error'] = "您的{$conv_frn}不足，无法兑换";
	$data['store'] = -2;
	$res = sendToFd($fd, $cmd, $code, $data);
	delLock($lockId);
	goto end;
}
if ( !$item['store'] ) {
	//通知用户 操作失败
	$data['errno'] = 5;
	$data['error'] = "{$conv_ton}库存不足，无法兑换";
	$data['store'] = intval($item['store']);
	$res = sendToFd($fd, $cmd, $code, $data);
	delLock($lockId);
	goto end;
}
//执行兑换
//用户货币消耗
$cost = intval($item['price'] * $num);
$addU = array($conv_fr=>-$cost);
$res = $this->model->incUserInfo($ud, $addU); unset($addU);
$user[$conv_fr] -= $cost;
$this->model->getRecord()->money('券换实物', $conv_fr, $cost, $ud, $user);
if ( !$res ) {
	//通知用户 操作失败
	$data['errno'] = 6;
	$data['error'] = "兑换操作失败。\n麻烦您拍照发送到QQ客服群，\n或者通过免费客服电话联系我们，谢谢！";
	$data['store'] = -2;
	$res = sendToFd($fd, $cmd, $code, $data);
	delLock($lockId);
	goto end;
}
$newU = $res ? $res['info'] : array();
$send40110 = $res ? $res['send'] : array();
$user[$conv_fr] = $newU[$conv_fr];
//兑换记录写入
$res = $this->model->recordConvert($user, $item, $num, $mobile);
//物品库存变化
$item['store'] = $this->model->ddaItemStore($item, $num);
//通知用户 操作成功
$data['errno'] = 0;
$data['error'] = "您的兑换请求已经提交成功！\n我们将在3个工作日内与您联系\n并发放奖励，请您保持手机畅通！";
$data['id'] = intval($item['id']);
$data['store'] = intval($item['store']);
$res = sendToFd($fd, $cmd, $code, $data);
//通知用户 数据更新 $conv_frn
$send40110 && sendToFd($fd, 4, 110, $send40110);
delLock($lockId);

//去掉红点标记
$exchange = 0;
foreach ( $list as $k => $v )
{
    if ( $v['price'] <= $user['coupon'] ) {
        $exchange = 1;
        break;
    }
}
if ( ! $exchange ) {
	$cmd = 4; $code = 234; $send = array('exchange'=>0,'errno'=>0,'error'=>'');
	sendToFd($fd, $cmd, $code, $send);
}

goto end;


end:{
	// $this->model->getRecord()->action($accode, $rd, $td, $ud, $user);
}
