<?php

require_once '../include/database.class.php';
require_once '../manage/checkPriv.php';
require_once '../manage/getipCity.php';

$pageIndex = 10*$_REQUEST['pageIndex'];
$pageSize = $_REQUEST['pageSize'];
$where = "";

if ( $data_priv != 'all' ) {
	if ( substr_count($data_priv,",")>0 ) {
		$where .= " AND u.channel IN (".$data_priv.")";
	} else {
		$where .= " AND u.channel = '$data_priv'";
	}
}
$channel = trim($_REQUEST['channel']);
if ( $channel && $channel != 'all' ) $where .= " AND u.channel = '$channel'";
$uid = intval($_REQUEST['uid']);
if ( $uid > 0 ) $where .= " AND u.uid = $uid";
$cool_num = intval($_REQUEST['cool_num']);
if ( $cool_num > 0 ) $where .= " AND u.cool_num = $cool_num";
$start = trim($_REQUEST['start']);
$end = trim($_REQUEST['end']);
if ( $start && !$end ) $where .= " AND a.add_time>='$start'";
elseif ( !$start && $end ) $where .= " AND a.add_time<='$end 23:59:59'";
elseif ( $start && $end ) $where .= " AND a.add_time>='$start' AND a.add_time<='$end 23:59:59'";
$last_start = trim($_REQUEST['last_start']);
$last_end = trim($_REQUEST['last_end']);
if ( $last_start && !$last_end ) $where .= " AND a.last_login >= '$last_start'";
elseif ( !$last_start && $last_end ) $where .= " AND a.last_login <= '$last_end 23:59:59'";
elseif ( $last_start && $last_end ) $where .= " AND a.last_login >= '$last_start' AND a.last_login <= '$last_end 23:59:59'";

$sql = "select u.uid, u.cool_num, u.nick, u.gold, u.coins, u.coupon, u.lottery, u.channel, a.matches, a.win, a.add_time, a.last_login, a.last_ip FROM lord_game_user u LEFT JOIN lord_game_analyse a ON a.uid = u.uid WHERE 1=1 $where ORDER BY u.uid DESC LIMIT $pageIndex, $pageSize";
$row = $db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
$ipCity = new ipCity();
foreach ( $row as $k => $v )
{
	$row[$k]['last_ip'] = $v['last_ip'].' '.$ipCity->getCity($v['last_ip']);
}
$arrays['data'] = $row;

if ( $start || $end || $last_start || $last_end ) {
	$sql = "select count(u.uid) as cn FROM lord_game_user u LEFT JOIN lord_game_analyse a ON a.uid = u.uid WHERE 1=1 $where";
} else {
	$sql = "SELECT count(uid) as cn FROM lord_game_user u WHERE 1=1 $where";
}
$res = $db->query($sql)->fetch(PDO::FETCH_ASSOC);
$arrays['cn'] = $res['cn'];

echo json_encode($arrays);
