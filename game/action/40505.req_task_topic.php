<?php

$ud = $user['uid'];
$rd = $user['roomId'];
$td = $user['tableId'];
$sd = $user['seatId'];

//默认协议 活动任务
$cmd = 4; $code = 506;
$data['errno'] = 0;
$data['error'] = "";
//用户任务信息及列表
$res = $this->model->getlistTesk($user);
$ut = $res['usertesk'];
$tesklist = $res['tesklist'];
//组装任务
$list = array();
foreach ( $tesklist as $k => $v )
{
	// 类别校验
	if ( $v['type'] != 2 ) {
		unset($tesklist[$k]); continue;
	}
	// 前置校验
	if ( isset($v['prev']) && $v['prev'] && !(isset($ut["teskdone_".$v['prev']]) && $ut["teskdone_".$v['prev']]) ) {
		unset($tesklist[$k]); continue;
	}
	// 激活校验
	if ( !isset($ut["teskstate_".$v['id']]) || !$ut["teskstate_".$v['id']] ) {
		unset($tesklist[$k]); continue;
	}
	if ( isset($ut["teskstate_".$v['id']]) && $ut["teskstate_".$v['id']] == 2 ) {
		$list[$k] = $v;
		unset($tesklist[$k]);
	}
}
foreach ( $tesklist as $k => $v )
{
	if ( isset($ut["teskstate_".$v['id']]) && $ut["teskstate_".$v['id']] == 1 ) {
		$list[$k] = $v;
		unset($tesklist[$k]);
	}
}
foreach ( $tesklist as $k => $v )
{
	$list[$k] = $v;
}
foreach ( $list as $k => $v )
{
	//服装道具组合
	if ( isset($v['prizes']['propItems']) && $v['prizes']['propItems'] ) {
		$propDress = $propItems = array();
		foreach ( $v['prizes']['propItems'] as $kk => $vv )
		{
			if ( $vv['categoryid'] == 1 ) { //服装
				$propDress[$vv['id']] = $vv['name'].$vv['ext']."天*".$vv['num'];
			} else { //道具
				$propItems[$vv['id']] = $vv['name']."*".$vv['num'];
			}
		}
		unset($v['prizes']['propItems']);
		if ( $propDress ) $v['prizes']['propDress'] = $propDress;
		if ( $propItems ) $v['prizes']['propItems'] = $propItems;
	}
	//任务数据重整
	$tesk = array(
		'id' => $v['id'],
		'name' => $v['name'],
		'target' => $v['target'],
		'current' => isset($ut["teskvalue_".$v['id']]) ? $ut["teskvalue_".$v['id']] : 0,//默认值为0
		'prizes' => $v['prizes'],
		'state' => $ut["teskstate_".$v['id']],
		'goto' => $v['goto'],
	);
	$list[$k] = $tesk;
}
$data['list'] = array_values($list);

$res = sendToFd($fd, $cmd, $code, $data);


end:{
	// $this->model->getRecord()->action($accode, $rd, $td, $ud, $user);
}
