<?php

//用户领月卡豆
$ud = $user['uid'];
$rd = $user['roomId'];
$td = $user['tableId'];
$sd = $user['seatId'];

//执行领取
$this->model->useMcard($user);


end:{
	// $this->model->getRecord()->action($accode, $rd, $td, $ud, $user);
}
