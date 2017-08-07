<?php
require_once '../manage/checkPriv.php';
require_once '../include/database.class.php';
$pageIndex = 50*$_REQUEST['pageIndex'];
$pageSize = $_REQUEST['pageSize'];
$type = $_REQUEST['type'];

if(strtotime($_SESSION['time'])>strtotime($_REQUEST['start'])){
    $start = $_SESSION['time'];
}else{
    $start = $_REQUEST['start'];
}


$data = $_REQUEST['data'];



$where = "";
if ( $type != 'all' ) {
    $where .= " where type='" . $type . "'";
}



$sql = "select * from lord_user_coinsrecord_".$start." ".$where." limit ".$pageIndex.",".$pageSize ;
//var_dump($sql);
//die();
$res = $db -> query($sql) -> fetchAll(PDO::FETCH_ASSOC);


foreach($res as $k=>$v){
    if ($v['type']==1) {
        $res[$k]['type'] = "牌桌输赢";
    }
    else if ($v['type']==2) {
        $res[$k]['type'] = "自动补豆";
    }
    else if ($v['type']==0) {
        $res[$k]['type'] = "未知操作";
    }
    else if ($v['type']==3) {
        $res[$k]['type'] = "竞技报名";
    }
    else if ($v['type']==4) {
        $res[$k]['type'] = "竞技取消";
    }
    else if ($v['type']==5) {
        $res[$k]['type'] = "购买道具";
    }
    else if ($v['type']==6) {
        $res[$k]['type'] = "任务发奖";
    }
    else if ($v['type']==7) {
        $res[$k]['type'] = "活动发奖";
    }
    else if ($v['type']==8) {
        $res[$k]['type'] = "乐币兑换";
    }
    else if ($v['type']==9) {
        $res[$k]['type'] = "每日签到";
    }
    else if ($v['type']==10) {
        $res[$k]['type'] = "激活礼包";
    }
    else if ($v['type']==11) {
        $res[$k]['type'] = "领取邮件";
    }
    else if ($v['type']==12) {
        $res[$k]['type'] = "竞技发奖";
    }
    else if ($v['type']==13) {
        $res[$k]['type'] = "后台重设";
    }
}




$array['data'] = $res;



$sql="select count(*) as cn from lord_user_coinsrecord_".$start." ".$where." ";
$res = $db -> query($sql) -> fetch(PDO::FETCH_ASSOC);
$array['cn'] = $res['cn'];
echo json_encode($array);


?>

