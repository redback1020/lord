<?php

//默认协议 刷新用户数据
$cmd = 4; $code = 110;
$data = array(
	'errno' => 0,
	'error' => '',
	'coins' => intval($user['coins']),
	'coupon' => intval($user['coupon']),
	'lottery' => intval($user['lottery']),
	'mail_unread' => intval($user['mail_unread']),
	'checkin_undo' => intval($user['checkin_undo']),
	'task1_unaward' => intval($user['task1_unaward']),
	'task2_unaward' => intval($user['task2_unaward']),
	'task3_unaward' => intval($user['task3_unaward']),
);
$res = sendToFd($fd, $cmd, $code, $data);


end:{}