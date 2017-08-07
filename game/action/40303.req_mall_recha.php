<?php

//默认协议 充值结果
$cmd=4; $code = 304;
goto end;//预留
//参数信息
if ( !isset($params['id']) || !$params['id'] ) {
	$res = closeToFd($fd, "[$accode] params=".json_encode($params));
	goto end;
}
$id = intval($params['id']);
//用户信息
$channel = $user['channel'];


end:{}