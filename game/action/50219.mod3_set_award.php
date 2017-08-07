<?php

//领取奖励

$id = isset($params['id']) ? intval($params['id']) : 0;
$mobi = isset($params['mobi']) ? trim($params['mobi']) : '';
$ud = $user['uid'];
$md = $user['modelId'];
$rd = $user['roomId'];
$td = $user['tableId'];
$sd = $user['seatId'];

$award = $this->match->phone($user, $id, $mobi);
$errno = $this->match->errno;
$errors = $this->match->getError();
if ( $errno == 99 ) gerr("[$accode] F=$fd U=$ud R=$rd T=$td ".json_encode($errors));
if ( ! $award ) $award = array();
debug("设置领奖手机 F=$fd U=$ud R=$rd T=$td");
$award = $this->match->board($user);
$errno = $this->match->errno;
$errors = $this->match->getError();
if ( $errno == 99 ) gerr("[$accode] F=$fd U=$ud R=$rd T=$td ".json_encode($errors)); 
if ( ! $award ) $award = array();

$cmd = 5; $code = 218; $send = array('errno'=>$errno,'error'=>'','award'=>$award);
sendToFd($fd, $cmd, $code, $send);


end:{
	// $rd = $user['lastRoomId'];
	// $this->model->record->action($accode, $rd, $td, $ud, $user);
}
