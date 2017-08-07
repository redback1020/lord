<?php

//房间列表

$ud = $user['uid'];
$md = isset($params['modelId']) ? intval($params['modelId']) : 3;
$rd = $user['roomId'];
$td = $user['tableId'];
$sd = $user['seatId'];

$rooms = $this->match->showRooms($md, $user);
$errno = $this->match->errno;
$errors = $this->match->getError();
if ( $errno == 99 ) gerr("[$accode] F=$fd U=$ud R=$rd T=$td ".json_encode($errors)); 
if ( ! $rooms ) $rooms = array();
$mode = '';
foreach ( $rooms as $rd => $room )
{
	$mode = $room['mode'];
	unset($rooms[$rd]['mode']);
}
$cmd = 5; $code = 202; $send = array('errno'=>$errno,'error'=>'','mode'=>$mode,'rooms'=>$rooms);
sendToFd($fd, $cmd, $code, $send);


end:{
	// $rd = $user['lastRoomId'];
	// $this->model->record->action($accode, $rd, $td, $ud, $user);
}
