<?php

//乐豆购买道具 ID

$ud = $user['uid'];
$rd = $user['roomId'];
$td = $user['tableId'];
$sd = $user['seatId'];

$cmd = 6; $code = 112; //回馈购买结果

//校验参数
$id = isset($params['propId']) ? intval($params['propId']) : 0;
$ids = array(5);//
$num = $this->model->getUserNumItem($ud, $id);
if ( ! in_array($id, $ids) || ! isset($this->confs["coins_buy_{$id}"]) ) {
	$send = array( "propId" => $id, "propNum" => $num, "propItems" => $user['propItems'], 'coins' => $user['coins'], 'propPrice' => 0, 'errno' => 1, 'error' => "很抱歉，您购买的乐豆道具已下架。\n还可以到商城，看看有没有。" );
	sendToFd($fd, $cmd, $code, $send);
	goto end;
}
if ( ! $rd ) {
	$send = array( "propId" => $id, "propNum" => $num, "propItems" => $user['propItems'], 'coins' => $user['coins'], 'propPrice' => 0, 'errno' => 1, 'error' => "很抱歉，道具目前不存在。\n请再次进入房间尝试。" );
	sendToFd($fd, $cmd, $code, $send);
	goto end;
}
//校验乐豆
$cost = isset($this->rooms[$rd]) ? $this->rooms[$rd]["cost{$id}"] : 1500;
if ( $user['coins'] < $cost ) {
	$send = array( "propId" => $id, "propNum" => $num, "propItems" => $user['propItems'], 'coins' => $user['coins'], 'propPrice' => 0, 'errno' => 2, 'error' => "您的乐豆不足，请先到商城购买乐豆。" );
	sendToFd($fd, $cmd, $code, $send);
	goto end;
}
//处理购买
$addT["seat{$sd}coins"] = $addU['coins'] = -$cost;
$res = $this->model->incUserInfo($ud, $addU);
$user['coins'] = $res['info']['coins'];
$user = $this->model->addUserNumItem($user, $id, 1);
foreach ( $addU as $k => $v ) $this->model->getRecord()->money('豆买道具', $k, abs($v), $ud, $user);
if ( $td && setLock($td) ) {
	if ( $table = $this->model->getTableInfo($td) ) {
		$res = $this->model->incTableInfo($td, $addT);
	}
	$res = delLock($td);
}
debug("用户豆买道具 F=$fd U=$ud T=$td S=$sd I=$id C=$cost N=".$user["propNum"]);
//发送结果
$send = array( "propId" => $id, "propNum"=>$user["propNum"], "propItems" => $user['propItems'], 'coins' => $user['coins'], 'propPrice' => $cost, "errno" => 0, "error" => "" );
sendToFd($fd, $cmd, $code, $send);


end:{
	$this->model->record->action($accode, $rd, $td, $ud, $user);
}
