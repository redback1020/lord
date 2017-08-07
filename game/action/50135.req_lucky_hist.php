<?php

$cmd = 5;
$code = 136; //回馈抽奖记录
$errno = 0;
$error = "操作成功。";

$uid = $user['uid'];
$tableId = $user['tableId'];
$seatId = $user['seatId'];

// $luckyRecord = array( //一周内的未知条数
// 	array('id'=>1003,'name'=>'伪造记录1','datetime'=>date("m-d H:i")),
// );
if($user['vercode'] >= 10800){
    $luckyRecord = $this->model->getNewUserLottery($uid);
}else{
    $luckyRecord = $this->model->getUserLottery($uid);
}


debug("查看抽奖记录[$fd|$uid|$tableId|$seatId]");

$data = array(
	'errno' => $errno,
	'error' => $error,
	'luckyRecord'=> $luckyRecord,			//抽奖记录
);
$res = sendToFd($fd, $cmd, $code, $data);


end:{}