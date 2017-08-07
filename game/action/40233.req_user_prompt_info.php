<?php
$cmd = 4;
$code = 234;
$data = array(
    'errno' => 0,
    'error' => '',

    'store' => 0,  //是否有新的商品
    'exchange'=> 0,//是否可以兑奖
    'tips'  => 0,  //是否有公告
    'rewards'=> 0, //礼包
    'salary' => 0, //俸禄
);
$data['errno'] = 0;
$data['error'] = "";
$time = time();
$ud = $user['uid'];
$dateid = intval(date("Ymd"));
$Utask = $this->model->getUserTask($ud);

//用户任务

//商店
$goods = $this->model->getGoods();//获取商品列表，最近三天上线
if($goods){
    foreach($goods as $k => $v){
        if($v['update_time'] > ($time - 3600*24*3)){
            $data['store'] = 1;
            break;
        }
    }
}
//活动公告
$channel = $user['channel'];
$topic = $this->model->listGetTopic($channel);
if ( $topic ) {
    foreach ( $topic as $k => $v ) {
        if ( isset($v['update_time']) && $v['update_time'] > ($time - 3600*24*3)) {
            $data['tips'] = 1;
            break;
        }
    }
}
//包月卡，是否已经领取俸禄
if($this->model->getMcard($ud) == 1){
    $data['salary'] = 1;
}
//兑换
$changeList = $this->model->getlistConvert($channel, '', 1, $Utask['gold_all']);
if($changeList){
    foreach($changeList as $k => $v){
        if($v['price'] <= $user['coupon']){
            $data['exchange'] = 1;
            break;
        }
    }
}
//用户礼包
$tmp = $this->model->redis->hget('lord_libao_'.$dateid, $ud);
if(!empty($tmp)){
    $data['rewards'] = 1;
}
$res = sendToFd($fd, $cmd, $code, $data);


//临时弹出一个活动替代图片,用作客户端的10800的热门活动图弹不出的bug
$conf = $this->model->getGoodsCtrl('huodongtitu', $channel);
if ( $user['vercode'] == 10800 && $conf && !isset($user['topic40168']) ) {
	$cmd = 4; $code = 168; $send = array('isPush'=>1, 'id'=>$conf['id'], 'fileId'=>$conf['fileId'], 'title'=>$conf['title'], 'bar'=>$conf['bar'], 'anim'=>$conf['anim'], 'goto'=>$conf['goto']);
	if ( $channel == 'shiboyun' ) {
		$send['id'] = 0;
		$send['goto'] = 1;
	} else {
		$goods = $this->model->getlistGoods('', 1);
		if ( ! isset($goods[$conf['id']]) ) goto end;
		$send['price'] = $goods[$conf['id']]['price'];
		$send['goto'] = 0;
	}
	$res = sendToFd($fd, $cmd, $code, $send);
	setUser($ud, array('topic40168'=>1));
	//下面代码为延迟协议案例代码, 尽量保留, 目前直接使用协议
	// setEvent('SEND_HUODONG_TITU', array('fd'=>$fd,'cmd'=>$cmd,'code'=>$code,'send'=>$send), 3000);
	// function SEND_HUODONG_TITU( $params )
	// {
	// 	sendToFd($params['fd'], $params['cmd'], $params['code'], $params['send']);
	// }
}



end:{}
