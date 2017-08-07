<?php

$ud = $user['uid'];
$rd = $user['roomId'];
$td = $user['tableId'];
$sd = $user['seatId'];

//默认协议 救济面板
$cmd = 5;
$code = 152;
$data['errno'] = 0;
$data['error'] = "";

$channel = $user['channel'];
debug("打开救济面板[$fd|$ud|$td|$sd]");
//获取今日已领取次数、冷却秒数
$trial = $this->model->getdataUserTrial($ud);
$data['trial_count'] = $trial['trial_count'];
$data['trial_cooldown'] = $trial['trial_cooldown'];
//获取本次领取的基数倍率列表
$list = $this->model->getlistTrialCoins($channel, $data['trial_count']);
foreach ($list as $k => $v) {
    unset($v['probability']);
    $list[$k] = $v;
}
$data['trial_list'] = $list;

//通知用户 操作成功
$res = sendToFd($fd, $cmd, $code, $data);

if (!in_array($user['channel'], ['sjyoujoy', 'sjyishiteng', 'sjiosappstore', 'sjzimo', 'sjiosxy', 'sjaiyouxi', 'sjmigu', 'sjweichat', 'sjiosappstore', 'sjyybweikandian'])) {
    if ($data['trial_count'] >= 2) {
        sendToFd($fd, 4, 120, ['id' => 111, 'price' => 0, 'goto' => 0, 'msg' => "", 'title' => '扫码下载手机版', 'sub' => '领取10000乐豆', 'type' => 0, 'img' => 'http://gt2.youjoy.tv/ddzgamefile/qrcode/2.png']);
    }
}

//非手机版用户，用户在没有点击“不再提示”按钮的情况下，经典场癞子场的新手场第四次领取救济金的时候，弹出一个二维码。
// if ( in_array($rd,array(1000,1007)) && ! ( strpos($user['channel'], 'sj') === 0 ) ) {
// 	$qrcodeid = 2;
// 	if ( $trial['trial_count'] == 3 ) {
// 		$cmd = 4; $code = 120; $send = array('type'=>$qrcodeid,'title'=>'可领取10000乐豆！','sub'=>'微信扫码下载手机版','img'=>'http://gt2.youjoy.tv/ddzgamefile/qrcode/001.png');
// 		sendToFd($fd, $cmd, $code, $send);
// 	}
// }


end:{
    // $this->model->record->action($accode, $rd, $td, $ud, $user);
}
