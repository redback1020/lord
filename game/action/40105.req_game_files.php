<?php

//校验参数
$verfile = isset($params['verfile']) ? intval($params['verfile']) : 0;
// if ( !$verfile ) {
// 	$res = closeToFd( $fd, "素材版号无效 params=".json_encode($params) );
// 	goto end;
// }

// include(ROOT.'/include/data_game_version.php');
// unset($data['version']);
// unset($data['verconf']);
// unset($data['vertips']);

$verdata = $this->model->listGetFile($verfile, $user['channel'], $user['vercode']);
$code = 106;
$data['errno'] = 0;
$data['error'] = "";
$data = array_merge($data, $verdata);
$res = sendToFd($fd, $cmd, $code, $data);


end:{}
