<?php

//打开购买乐豆面板

$time = time();
$dd = dateid();
$ud = $user['uid'];
$rd = $user['roomId'];
$td = $user['tableId'];
$sd = $user['seatId'];
$channel = $user['channel'];
$Utask = $this->model->getUserTask($ud);
$res = $this->model->getlistTesk($user);
$tesk = $res['usertesk'];
$lista = $listb = array();

//查找列表
$list = $this->model->getlistGoods($channel);
$mtem = $this->model->getuserItem($ud, 1);
$pdSec = array();
foreach ( $mtem as $k => $v )
{
	if ( $v['state'] < 2 ) {
		$sec = intval($v['sec'] > 0 ? $v['sec'] : max(0, $v['end'] > 0 ? ($v['end']-$time) : 0));
		$pdSec[$v['pd']] = $sec + (isset($pdSec[$v['pd']]) ? $pdSec[$v['pd']] : 0);
	}
}
foreach ( $pdSec as $k => $v )
{
	if ( ! $v ) $pdSec[$k] = $user['vercode'] < 10800 ? (999 * 86400) : -1;
}
foreach ( $list as $k => $v )
{
	if ( ! $v['is_recommend'] ) {
		unset($list[$k]);
		continue;
	}
	//任务检测
	if ( $v['taskid'] && isset($tesk['teskdone_'.$v['taskid']]) && $tesk['teskdone_'.$v['taskid']] ) {
		unset($list[$k]);
		continue;
	}
	//显隐检测
	if ( $v['is_hide'] ) {
		unset($list[$k]);
		continue;
	}
	//版本检测
	if ( $v['id'] == 22 && isset($user['vercode']) && $user['vercode'] < 10600 ) {
		unset($list[$k]);
		continue;
	}
	$list[$k]['sec'] = isset($pdSec[$v['ipd']]) && $pdSec[$v['ipd']] && ($pdSec[$v['ipd']] < 0 || $pdSec[$v['ipd']] >= 60) ? $pdSec[$v['ipd']] : 0;
	unset($list[$k]['iid']);
	unset($list[$k]['icd']);
	unset($list[$k]['ipd']);
	unset($list[$k]['buyto']);
	unset($list[$k]['taskid']);
	unset($list[$k]['is_hide']);
}
if ( $list ) $lista = array_values($list);

//查找列表
$list = $this->model->getlistRecharge($channel, 0, 1);
foreach ( $list as $k => $v )
{
	if ( ! $v['is_recommend'] ) {
		unset($list[$k]);
		continue;
	}
	//任务检测
	if ( $v['taskid'] && ( $Utask['gold_all'] || (isset($tesk['teskdone_'.$v['taskid']]) && $tesk['teskdone_'.$v['taskid']]) ) ) {
		unset($list[$k]);
		continue;
	}
	//显隐检测
	if ( $v['is_hide'] ) {
		unset($list[$k]);
		continue;
	}
	//首冲逻辑 修改下面配置逻辑，需要全项目搜索关键字“ 修改首冲逻辑 ”，防止漏掉。乐币到乐豆的每日首冲/每周首冲/用户首冲
	// if ( ! $Utask['gold_all'] ) {
	// 	$list[$k]['is_onsale'] = 1;
	// 	$list[$k]['onsale'] = '首次购买 + 100%';
	// }
	// elseif ( ! $Utask['gold_week'] ) {
	// 	$list[$k]['is_onsale'] = 1;
	// 	$list[$k]['onsale'] = '每周首次购买 + 20%';
	// }
	// elseif ( ! $Utask['gold_day'] ) {
	// 	$list[$k]['is_onsale'] = 1;
	// 	if ( (! ISTESTS && strtotime('2016-04-28') < time() && time() < strtotime('2016-05-05')) || (ISTESTS && time() < strtotime('2016-05-05')) ) {
	// 		$list[$k]['onsale'] = '每日首次购买 + 100%';
	// 	} else {
	// 		$list[$k]['onsale'] = '每日首次购买 + 20%';
	// 	}
	// }
	if ( ! $Utask['gold_day'] ) {
		$list[$k]['is_onsale'] = 1;
		if(intval($list[$k]['price']) == 30)
		  $list[$k]['onsale'] = '首次购买 + 10%';
		elseif(intval($list[$k]['price']) == 50)
		  $list[$k]['onsale'] = '首次购买 + 15%';
		elseif(intval($list[$k]['price']) >= 100)
		  $list[$k]['onsale'] = '首次购买 + 20%';
	}
	elseif ( $time < strtotime('2016-06-01') && $this->model->redis->hget('lord_gold_day_2'.$dd, $ud) < 2  ) {
		$list[$k]['is_onsale'] = 1;
		$list[$k]['onsale'] = '前两次购买 + 100%';
	}
	//乐豆图片在这里强制更改
	$list[$k]['fileId']*=10000;
	//乐豆名称在这里强制更改
	$list[$k]['name'].= '万乐豆';
	unset($list[$k]['taskid']);
	unset($list[$k]['is_hide']);
}
if ( $list ) $listb = array_values($list);

$list = array_merge($lista, $listb);
//发送结果
$cmd = 4; $code = 304; $send = array('errno'=>0, 'error'=>"", 'list'=>$list);
sendToFd($fd, $cmd, $code, $send);


end:{
	// $this->model->getRecord()->action($accode, $rd, $td, $ud, $user);
}
