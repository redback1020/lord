<?php

$code = 202;
$data['errno'] = 0;
$data['error'] = "";

$uid = $user['uid'];

//待续
$data = $user;

$res = sendToFd($fd, $cmd, $code, $data);


end:{}