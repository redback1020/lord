<?php

$ud = $user['uid'];
$rd = $user['roomId'];
$td = $user['tableId'];
$sd = $user['seatId'];
$channel = $user['channel'];

$isDone = 0;

debug("用户领取救济 F=$fd U=$ud R=$rd T=$td");

$cmd = 5;
$code = 156; //回馈救济结果
$data['errno'] = 0;
$data['error'] = "";

$error = "操作失败[%d]，\n麻烦您拍照发送到QQ客服群，\n或者通过免费客服电话联系我们，谢谢！";

//获取今日已领取次数、冷却秒数
$trial = $this->model->getdataUserTrial($ud);
if (!$trial) {
    //通知用户 操作失败
    $data['errno'] = 2;
    $data['error'] = sprintf($error, $data['errno']);
    $res = sendToFd($fd, $cmd, $code, $data);
    goto end;
}
$trial_count = $trial['trial_count'];
$trial_cooldown = $trial['trial_cooldown'];
//校验本次领取冷却秒数
if ($trial_cooldown) {
    //通知用户 操作失败
    $data['errno'] = 1;
    $data['error'] = "救济金还没有到领取时间哦，\n如果确实已经到了领取时间，请联系我们的客服。";
    $res = sendToFd($fd, $cmd, $code, $data);
    goto end;
}

// if ( ! $trial_count ) {//只在每天第一次领取时，才检查IP
// 	if ( $this->model->ippban('trial', $user['ip']) ) {
// 		debug("用户救济受限 F=$fd U=$ud R=$rd T=$td IP=".$user['ip']);
// 		goto end;
// 	}
// }

//获取救济基数和倍率
$list = $this->model->getlistTrialCoins($channel, $trial_count);
if (!$list) {
    //通知用户 操作失败
    $data['errno'] = 3;
    $data['error'] = sprintf($error, $data['errno']);
    $res = sendToFd($fd, $cmd, $code, $data);
    goto end;
}
//计算本次领取的金额
$data['trial_value'] = $data['trial_multiple'] = 0;
$probability = 0;
foreach ($list as $k => $v) {
    $probability += $v['probability'];
}
$seed = mt_rand(1, $probability);
$step = 0;
foreach ($list as $k => $v) {
    if ($seed > $step && $seed <= $v['probability'] + $step) {
        $data['trial_value'] = $v['value'];
        $data['trial_multiple'] = $v['multiple'];
        break;
    }
    $step += $v['probability'];
}
$data['trial_coins'] = $coins = intval($data['trial_value'] * $data['trial_multiple']);
//救济金投放到用户
$addU = ['coins' => $coins];
$user['coins'] += $coins;
$res = $this->model->incUserInfo($ud, $addU);
foreach ($addU as $k => $v) $this->model->getRecord()->money('领取救济', $k, $v, $ud, $user);
$coins = intval($res['info']['coins']);
if ($td) {
    $lock = $td;
    $res = setLock($lock);
    if (!$res) {
        gerr("执行拉霸操作 LOCKON lock=$lock");
    } else {
        $table = $this->model->getTableInfo($td);
        if ($table) {
            $newT["seat{$sd}coins"] = $coins;
            $res = $this->model->setTableInfo($td, $newT);
        }
    }
    $res = delLock($lock);
}
//更新冷却时间
$res = $this->model->setdataUserTrial($ud, $channel, $trial_count + 1);
//通知用户 操作成功
$res = sendToFd($fd, $cmd, $code, $data);
//通知用户 数据更新 乐豆
$cmd = 4;
$code = 110;
$send = ['coins' => $coins];
$res = sendToFd($fd, $cmd, $code, $send);

$isDone = 1;

if (!in_array($user['channel'], ['sjyoujoy', 'sjyishiteng', 'sjiosappstore', 'sjzimo', 'sjiosxy', 'sjaiyouxi', 'sjmigu', 'sjweichat', 'sjiosappstore', 'sjyybweikandian'])) {

    if ($trial_count >= 2) {

        sendToFd($fd, 4, 120, ['id' => 111, 'price' => 0, 'goto' => 0, 'msg' => "", 'title' => '扫码下载手机版', 'sub' => '领取10000乐豆', 'type' => 0, 'img' => 'http://gt2.youjoy.tv/ddzgamefile/qrcode/2.png']);

    }
}

end:{
    if ($isDone)
        $this->model->record->action($accode, $rd, $td, $ud, $user);
}
