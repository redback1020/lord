<?php

$code = 104;
$data['errno'] = 0;
$data['error'] = "";

//校验参数
$verconf = isset($params['verconf']) ? intval($params['verconf']) : 0;
// if ( !$verconf ) {
// 	$res = closeToFd( $fd, "配置版号无效 params=".json_encode($params) );
// 	goto end;
// }

$uid = $user['uid'];
$channel = $user['channel'];
//随时可能会有依据channel识别某个菜单或者场次的展现

$data['verconf'] = 1;

$data['update']['navi'] = array( 	//大厅菜单栏目
	'user_index' => 1, //>0:排序|0:不显示
	'user_inbox' => 2,
	'mall_index' => 3,
	'topic_index' => 4,
	'topic_lucky' => 5,
	'list_gold' => 6,
	'task_check' => 7,
	'topic_activity' => 8,
	'user_wechat' => 9,
	'setting' => 10,
	'help' => 11,
);

if ( $user['coins'] <= 5000 ) {
$data['update']['room']['1000'] = $this->rooms['1000'];
}
$data['update']['room']['1001'] = $this->rooms['1001'];
$data['update']['room']['1002'] = $this->rooms['1002'];
$data['update']['room']['1003'] = $this->rooms['1003'];
if ( $user['coins'] > 5000 ) {
$data['update']['room']['1004']['modelId'] 	  = $this->rooms['1004']['modelId'];
$data['update']['room']['1004']['roomId']     = $this->rooms['1004']['roomId'];
$data['update']['room']['1004']['baseCoins']  = $this->rooms['1004']['baseCoins'];
$data['update']['room']['1004']['rate']       = $this->rooms['1004']['rate'];
$data['update']['room']['1004']['rateMax']    = $this->rooms['1004']['rateMax'];
$data['update']['room']['1004']['limitCoins'] = $this->rooms['1004']['limitCoins'];
$data['update']['room']['1004']['rake']       = $this->rooms['1004']['rake'];
$data['update']['room']['1004']['enterLimit'] = $this->rooms['1004']['enterLimit'];
$data['update']['room']['1004']['enterLimit_']= $this->rooms['1004']['enterLimit_'];
$data['update']['room']['1004']['gameInCoins']= $this->rooms['1004']['gameInCoins'];
}

$res = sendToFd($fd, $cmd, $code, $data);


end:{}
