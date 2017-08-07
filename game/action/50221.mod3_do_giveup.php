<?php

//放弃比赛

$ud = $user['uid'];
$md = $user['modelId'];
$rd = $user['roomId'];
$td = $user['tableId'];
$gd = $user['gameId'];
// $md = isset($params['modelId']) ? intval($params['modelId']) : ($user['md'] ? $user['md'] : 3);

$isDone = 0;

if ( $md != 3 || ! $rd || ! $td || ! $gd || ! ($T = $this->model->getTableInfo($td)) ) {
	goto end;
}
$ret = $this->match->alter($user, $T);
$errno = $this->match->errno;
$errors = $this->match->getError();
if ( $errno == 99 ) gerr("[$accode] F=$fd U=$ud R=$rd T=$td ".json_encode($errors));
if ( ! $ret ) {
	goto end;
}
$newU = $ret['newU'];
$user = array_merge($user, $newU);
setUser($ud, $newU);
$newT = $ret['newT'];
$T = array_merge($T, $newT);
$this->model->setTableInfo($td, $newT);

debug("用户放弃比赛 F=$fd U=$ud R=$rd T=$td G=$gd");

$isDone = 1;

end:{
	if ( $isDone )
	$this->model->record->action($accode, $rd, $td, $ud, $user);
}
