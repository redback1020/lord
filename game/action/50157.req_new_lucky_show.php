<?php

$cmd = 5;
$code = 158; //回馈抽奖界面
$errno = 0;
$error = "操作成功。";

$ud = $user['uid'];

//参数校验
$version = isset($params['version']) ? $params['version'] : 0;//版本号
$now_version = $this->model->getPrizeVersion();
$luckyPrize =  array();
if($version < $now_version){
    $list = $this->model->getPrizeList($now_version);
	ksort($list);
    foreach($list as $key=>$val){
        $item = array();
        foreach($val as $tmp){
            $item[] = array('id'=>$tmp['picture_id'],'name'=>$tmp['name']);
        }
        shuffle($item);
        $items = array('type'=>$key,'list'=>$item);
        $luckyPrize[] = $items;
    }
}

//获取用户任务信息，如果任务信息跨天，会重新矫正抽奖机会
$dateid = dateid();
$Utask = $this->model->getUserTask($ud);
$maxtimes = $Utask['gold_level'] ? 200 : 100;
$user_lottery = $this->model->redis->hget('lord_user_lottery', $ud);
if ( ! $user_lottery ) $user_lottery = $dateid.'_0';
$user_lottery = explode('_', $user_lottery);
if ( $user_lottery[0] != $dateid ) {
	$user_lottery[0] = $dateid;
	$user_lottery[1] = 0;
}
$left_lottery = $maxtimes - $user_lottery[1];
$left_lottery = $left_lottery > 0 ? $left_lottery : 0;

include(ROOT.'/include/data_lottery_rules.php');
$luckyRules = $data_lottery_rules;
$luckyRecord = $this->model->getNewUserLottery($ud);
$result = $this->model->getLuckyDrawStaticsResult($ud,$dateid);

debug("查看抽奖界面[$fd|$ud]");

$data = array(
	'errno' => $errno,
	'error' => $error,
	'coupon'  => $user['coupon'],		//奖券
	'lottery' => $user['lottery'],		//免费抽奖次数
	'luckyRules' => $luckyRules,		//抽奖规则
	'luckyPrize' => $luckyPrize,		//奖品列表
	'luckyRecord'=> $luckyRecord,		//抽奖记录
    'got_coins'  => $result['coins'],        //今日获得乐豆数
    'got_coupon'  => $result['coupon'],        //今日获得乐卷数
    'version' =>$now_version,
    'left_lottery' => $left_lottery         ,//剩余乐豆抽奖次数
);
$res = sendToFd($fd, $cmd, $code, $data);


end:{}
