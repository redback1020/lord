<?php

//查看房间

$ud = $user['uid'];
$md = $user['modelId'];
$rd = isset($params['roomId']) ? intval($params['roomId']) : 0;
$td = $user['tableId'];
$sd = $user['seatId'];

$room = $this->match->showRoom($rd, $user);
$errno = $this->match->errno;
$errors = $this->match->getError();
if ( $errno == 99 ) gerr("[$accode] F=$fd U=$ud R=$rd T=$td ".json_encode($errors));
if ( ! $room ) $room = array();
$cmd = 5; $code = 204; $send = array('errno'=>$errno,'error'=>''); $send = array_merge($send, $room);
sendToFd($fd, $cmd, $code, $send);


end:{
	// $rd = $user['lastRoomId'];
	// $this->model->record->action($accode, $rd, $td, $ud, $user);
}
