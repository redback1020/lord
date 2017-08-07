<?php

$code = 102;
$data['errno'] = 0;
$data['error'] = "";

include(ROOT.'/include/data_game_version.php');

$res = sendToFd($fd, $cmd, $code, $data);


end:{}