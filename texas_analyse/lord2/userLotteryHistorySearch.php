<?php

require_once '../manage/checkPriv.php';
require_once '../include/database.class.php';

$pageIndex = 10 * intval($_REQUEST['pageIndex']);
$pageSize = intval($_REQUEST['pageSize']);
$type = trim($_REQUEST['type']);
$cool_num = intval($_REQUEST['cool_num']);
$start = intval($_REQUEST['start']);

$where = " where 1=1 ";
if ( in_array($type, array('gold', 'coupon', 'coins', 'propid')) ) {
    $where .= " and $type > 0";
}
if ( $start ) {
    $where .= " and dateid = $start";
}
if ( $cool_num ) {
    $where .= " and cool_num = $cool_num";
}

$sql = "select * from lord_user_lotteryrecord $where order by id desc limit ".$pageIndex.",".$pageSize ;
$res = $db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
if ( !$res ) { $res = array(); }
$props = array('2'=>'高手套装','3'=>'大师套装','4'=>'大师套装');
$uids = array();
foreach($res as $k=>$v){
    if ( isset($props[$v['propid']]) ) {
        $res[$k]['prop'] = $props[$v['propid']];
    } else{
        $res[$k]['prop'] = '&nbsp;';
    }
    $res[$k]['date'] = date("Y-m-d H:i:s", $v['ut_create']);
    $uids[$v['uid']] = 1;
}
$uids = array_keys($uids);
if ( count($uids) == 1 ) {
    $uid = reset($uids);
} else {
    $uid = '';
}
$array['uid'] = $uid;
$array['data'] = $res;

$sql = "select count(*) as cn from lord_user_lotteryrecord $where";
$res = $db->query($sql)->fetch(PDO::FETCH_ASSOC);
if ( !$res ) { $res = array('cn'=>0); }
$array['cn'] = $res['cn'];

echo json_encode($array);
exit;
