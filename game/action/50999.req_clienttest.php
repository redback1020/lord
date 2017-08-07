<?php

//注意 本action仅用于模拟重载全服进程时的压力监控
//请在服务端上执行 tail -f /alidata1/wwwroot/landlord/sweety/game/log/sweety.log | grep -E "内核|ERR|php"

$ud = $user['uid'];
$md = $user['modelId'];
$rd = $user['roomId'];
$td = $user['tableId'];
$sd = $user['seatId'];

$codes = array(994, 996, 998);
// usleep(100000);
// $times = 1;
$times = mt_rand(1,2);
$this->redis->hincrby('client_test_protocol', 'req_times', 1);
for ( $i = 0; $i < $times; $i++ ) {
	// $code = 994;
    $code = $codes[array_rand($codes)];
    $send = $this->redis->hgetall('client_test_protocol');
    $send = array_merge($send, $params);
    $send = $params;
    $res = sendToFd($fd, $cmd, $code, $send);
    if ( $res ) {
        $this->redis->hincrby('client_test_protocol', 'res_times', 1);
    } else {
        $this->redis->hincrby('client_test_protocol', 'bad_times', 1);
    }
}


end:{
	$this->model->record->action($accode, $rd, $td, $ud, $user);
}
