<?php

$code = 216;
$data['errno'] = 0;
$data['error'] = "";

//校验参数
$id = isset($params['id']) ? intval($params['id']) : 0;
if ( !$id ) {
	$res = closeToFd( $fd, "邮件参数无效 params=".json_encode($params) );
	goto end;
}

$ud = $user['uid'];
$rd = $user['roomId'];
$td = $user['tableId'];
$sd = $user['seatId'];

$res = $this->model->listSetInboxGetItems($user, $id);
if ( $res && is_array($res) ) {
	if ( isset($res['props']) ) {
		$res['propItems'] = array_values($res['props']);
		unset($res['props']);
	}
	elseif ( isset($res['items']) ) {
		$res['propItems'] = array_values($res['items']);
		unset($res['items']);
	}
	$user = $this->model->getUserInfo($ud);
	if ( $user )
	{
		$data = array_merge($data, $res, array('propDress'=>$user['propDress']));
		sendToFd($fd, $cmd, $code, $data);
		$cmd = 4; $code = 110;
		$send = array('coins'=>$user['coins'],'coupon'=>$user['coupon'],'lottery'=>$user['lottery'],'propDress'=>$user['propDress'],'propItems'=>$user['propItems']);
		sendToFd($fd, $cmd, $code, $send);
		goto end;
	} else {
		$res = 1;
	}
}

$errors = array('1'=>'操作失败，请稍候重试。');
$data['errno'] = $res;
$data['error'] = $errors[$res];
$res = sendToFd($fd, $cmd, $code, $data);


end:{
	// $this->model->getRecord()->action($accode, $rd, $td, $ud, $user);
}
