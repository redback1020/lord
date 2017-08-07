<?php

//刷新房间

$ud = $user['uid'];
$md = $user['modelId'];
$rd = isset($params['roomId']) ? intval($params['roomId']) : 0;
$td = $user['tableId'];
$sd = $user['seatId'];

$room = $this->match->showRoom($rd, $user);
$errno = $this->match->errno;
$errors = $this->match->getError();
if ( $errno == 99 ) gerr("[$accode] F=$fd U=$ud R=$rd T=$td ".json_encode($errors));
if ( ! $room ) $room = array('brief'=>'', 'state'=>0);
$view = array('brief'=>$room['brief'], 'state'=>$room['state']);
$cmd = 5; $code = 206; $send = array('errno'=>$errno,'error'=>''); $send = array_merge($send, $view);
sendToFd($fd, $cmd, $code, $send);


end:{
	// $rd = $user['lastRoomId'];
	// $this->model->record->action($accode, $rd, $td, $ud, $user);
}
