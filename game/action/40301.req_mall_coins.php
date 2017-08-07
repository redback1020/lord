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

//查找列表
$list = $this->model->getlistRecharge($channel);
foreach ( $list as $k => $v )
{
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
	unset($list[$k]['is_hide']);
}
if ( $list ) $list = array_values($list);
//发送结果
$cmd = 4; $code = 302; $send = array('errno'=>0, 'error'=>"", 'list'=>$list);
sendToFd($fd, $cmd, $code, $send);


end:{
	// $this->model->getRecord()->action($accode, $rd, $td, $ud, $user);
}
