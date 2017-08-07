<?php

$cmd = 5;
$code = 162; //回馈退出挽留界面
$data = array(
    'errno' => 0,
    'error' => '操作成功。',
    'login_got_coupon'  => 0,		//登陆获取的乐券
    'task_target' => 5,          //满多少局可以打开宝箱
    'task_left'   =>5,             //还剩下多少局完成任务
    'is_new_dialog' =>1,           //是否弹出新风格的对话框
    'tip_info' =>'',               //旧版对话框提示信息
);


$ud = $user['uid'];
$rd = $user['lastRoomId'];
$td = $user['tableId'];
$sd = $user['seatId'];

debug("用户挽留面板 F=$fd U=$ud R=$rd T=$td");

$testInfo = $this->model->getUserTesk($ud);

include(ROOT.'/include/data_tesk_id_list.php');
if(!empty($data_tesk_id_list[$rd])){
    $data['task_target'] = $data_tesk_id_list[$rd]['target'];
    $teskid= $data_tesk_id_list[$rd]['id'];
    $key = "teskvalue_$teskid";
    $times = isset($testInfo[$key]) ? $testInfo[$key] : 0;
    $data['task_left'] = $data['task_target'] - $times;
}
$data['login_got_coupon'] = $user['login_got_coupon'];
$res = sendToFd($fd, $cmd, $code, $data);

// //这里测试一下40120协议
// $cmd = 4; $code = 120;
// $send = array('id'=>11,'price'=>3, 'title'=>'获得特权','sub'=>"",'msg'=>"恭喜您获得3天记牌器购买特权",'button'=>1,'goto'=>1);
// $qrcodeid = 1;
// $cmd = 4; $code = 120; $send = array('type'=>$qrcodeid,'title'=>'title测试','sub'=>'sub测试','img'=>'http://www.youjoy.tv/images/316_159/316-159-有乐斗地主.png');
// sendToFd($fd, $cmd, $code, $send);


end:{
	$this->model->record->action($accode, $rd, $td, $ud, $user);
}
