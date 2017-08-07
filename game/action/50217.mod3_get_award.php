<?php

//领奖面板

$rd = isset($params['roomId']) ? intval($params['roomId']) : 0;
$gd = isset($params['gameId']) ? intval($params['gameId']) : 0;
$ud = $user['uid'];
$md = $user['modelId'];
$td = $user['tableId'];
$sd = $user['seatId'];

$award = $this->match->board($user, $rd, $gd);
$errno = $this->match->errno;
$errors = $this->match->getError();
if ( $errno == 99 ) gerr("[$accode] F=$fd U=$ud R=$rd T=$td ".json_encode($errors));
if ( ! $award ) $award = array();
foreach ( $award as $k => $v )
{
	$award[$k]['time'] = date("Y年m月d日 H:i:s", $v['time']);
}
// debug("查看领奖面板 F=$fd U=$ud R=$rd T=$td");
$cmd = 5; $code = 218; $send = array('errno'=>$errno,'error'=>'','award'=>$award);
sendToFd($fd, $cmd, $code, $send);


end:{
	// $rd = $user['lastRoomId'];
	// $this->model->record->action($accode, $rd, $td, $ud, $user);
}
