<?php
require_once '../manage/checkPriv.php';
require_once '../include/database.class.php';
$pageIndex = 20*$_REQUEST['pageIndex'];
$pageSize = $_REQUEST['pageSize'];
$type = $_REQUEST['type'];
$uid = intval($_REQUEST['uid']);
$cool_num = intval($_REQUEST['cool_num']);

if(strtotime($_SESSION['time'])>strtotime($_REQUEST['start'])){
    $start = $_SESSION['time'];
}else{
    $start = $_REQUEST['start'];
}

$data = $_REQUEST['data'];

$where = " where 1=1 ";
if ( $type != 'all' ) {
    $where .= " and a.type=$type";
}
if ( $uid ) {
    $where .= " and a.uid = $uid";
} elseif ( $cool_num ) {
    $sql = "select uid from lord_game_user where cool_num = $cool_num";
    $res = $db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    if ( count($res) > 1 ) {
        $uids = array();
        foreach ( $res as $row ) {
            $uids[]= " a.uid = ".$row['uid'];
        }
        $where .= " and ( ".join(' or ', $uids)." )";
    } elseif ( $res ){
        $where .= " and a.uid = ".intval($res[0]['uid']);
    }
}

$sql = "select a.*, b.cool_num, b.nick from lord_record_coins_".$start." a left join lord_game_user b on a.uid = b.uid ".$where." order by a.id desc limit ".$pageIndex.",".$pageSize ;
$res = $db->query($sql)->fetchAll(PDO::FETCH_ASSOC);


foreach($res as $k=>$v){
    if ($v['type']==0) {
        $res[$k]['type'] = "未知操作";
    }
    else if ($v['type']==1) {
        $res[$k]['type'] = "牌桌输赢";
    }
    else if ($v['type']==2) {
        $res[$k]['type'] = "自动补豆";
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
        $res[$k]['type'] = "竞技场奖";
    }
    else if ($v['type']==13) {
        $res[$k]['type'] = "后台重设";
    }
    else if ($v['type']==14) {
        $res[$k]['type'] = "兑换中心";
    }
    else if ($v['type']==15) {
        $res[$k]['type'] = "领取救济";
    }
    else if ($v['type']==16) {
        $res[$k]['type'] = "竞技周奖";
    }
}




$array['data'] = $res;



$sql="select count(a.id) as cn from lord_record_coins_".$start." a ".$where." ";
$res = $db -> query($sql) -> fetch(PDO::FETCH_ASSOC);
$array['cn'] = $res['cn'];
echo json_encode($array);


?>
