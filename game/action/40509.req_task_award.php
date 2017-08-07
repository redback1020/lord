<?php

$ud = $user['uid'];
$rd = $user['roomId'];
$td = $user['tableId'];
$sd = $user['seatId'];

//默认协议 任务领奖
$cmd = 4; $code = 510;
$data['errno'] = 0;
$data['error'] = "";
//参数信息
if ( !isset($params['id']) || !intval($params['id']) ) {
	$res = closeToFd($fd, "[$accode] params=".json_encode($params));
	goto end;
}
$id = intval($params['id']);
//用户任务信息及列表
$res = $this->model->getlistTesk($user);
$ute = $res['usertesk'];
$list = $res['tesklist'];
if ( !isset($list[$id]) || $ute['teskstate_'.$id] != 2 ) {
	debug(json_encode($res));
	$data['errno'] = 1;
	$data['error'] = '任务不存在或已过期';
	$res = sendToFd($fd, $cmd, $code, $data);
	goto end;
}
$tesk = $list[$id];
//执行发奖 获得道具状态
$items = $this->model->userPrize($user['uid'], $tesk['prizes'], $user, '动态任务');
if ( !$items ) {
	$data['errno'] = 2;
	$data['error'] = "操作失败。\n麻烦您拍照发送到QQ客服群，\n或者通过免费客服电话联系我们，谢谢！";
	$res = sendToFd($fd, $cmd, $code, $data);
	goto end;
}
//更新任务数据
$newUTE['teskstate_'.$id] = 3;
foreach ( $list as $k => $v )
{
	// 前置校验
	if ( isset($v['prev']) && $v['prev'] == $id ) {
		$newUTE['teskstate_'.$k] = 1;
	}
}
$res = $this->model->setUserTesk($ud, $newUTE);
//处理响应
$data['error'] = array('恭喜您完成任务，获得：');
foreach ( $tesk['prizes'] as $k => $v )
{
	switch ($k)
	{
		case 'coins':
			$data['error'][]= $v."乐豆";
			break;
		case 'coupon':
			$data['error'][]= $v."乐券";
			break;
		case 'lottery':
			$data['error'][]= $v."次抽奖机会";
			break;
		case 'items':
			foreach ( $v as $kk => $vv )
			{
				$data['error'][]= $vv['name'];
			}
			break;
		case 'propItems':
		      foreach ($v as $kk=>$vv)
		      {
		          $data['error'][]= $vv['name'];
		      }
		      break;
		default:
			break;
	}
}
$data['error'] = join("\n", $data['error']);
$res = sendToFd($fd, $cmd, $code, $data);

$user = getUser($ud);
$key = "task".($tesk['type']+1)."_unaward";
$newU[$key] = $user[$key]--;
$newU[$key] = $user[$key] = $user[$key] > 0 ? $user[$key] : 0;
$res = setUser($ud, $newU);
$cmd = 4; $code = 110;
$dress = $this->model->prop->getMine($ud, 1);
$data = array('coins'=>intval($user['coins']),'coupon'=>intval($user['coupon']),'lottery'=>intval($user['lottery']),"propDress" => $dress,$key=>$newU[$key]);
$res = sendToFd($fd, $cmd, $code, $data);


end:{
	// $this->model->getRecord()->action($accode, $rd, $td, $ud, $user);
}
