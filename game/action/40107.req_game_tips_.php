<?php

//校验参数
$vertips = isset($params['vertips']) ? intval($params['vertips']) : 0;
// if ( !$vertips ) {
// 	$res = closeToFd( $fd, "提示版号无效 params=".json_encode($params) );
// 	goto end;
// }

$uid = $user['uid'];
$channel = $user['channel'];
$verdata = $this->model->listGetTips($vertips, $channel);

$cmd = 4; $code = 108;
$data['errno'] = 0;
$data['error'] = "";
$data = array_merge($data,$verdata);
$res = sendToFd($fd, $cmd, $code, $data);


end:{}