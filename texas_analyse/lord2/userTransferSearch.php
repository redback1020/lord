<?php
require_once '../manage/checkPriv.php';
require_once '../include/database.class.php';

$cool_num1 = intval(trim($_REQUEST['cool_num1']));//cool_num1必须是整数
$cool_num2 = intval(trim($_REQUEST['cool_num2']));//cool_num2必须是整数
$nick=trim($_REQUEST['nick']);//非空字符串化

if ( !$cool_num1 or !( $cool_num2 or $nick) ) {//没有配对，不再执行，直接返回空数组结果
    $array['data'] = array();
    echo json_encode($array);
    exit();
}

$cool_nums = array();
if ( $cool_num2 && $cool_num2 != $cool_num1 ) {//先把有效的旧编号ID加到数组里
    $cool_nums[]= $cool_num2;
}
if( $nick != '' ) {//使用旧昵称，找到多个旧编号ID
    $sql = "select cool_num from lord_game_user where nick='$nick'";
    $data = $db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    $data = $data ? $data : array();
    foreach ( $data as $k => $v )
    {
        if ( $v['cool_num'] != $cool_num1 ) {//再把通过昵称查找到的有效的旧编号ID加到数组里
            $cool_nums[]=intval($v['cool_num']);
        }
    }
}
if ( $cool_nums ) {//旧编号ID去掉重复的
    $cool_nums = array_unique($cool_nums);
}
$cool_nums[]= $cool_num1;//最后才把新编号ID入数组

$sql = "select u.uid,u.cool_num,u.coins,a.login,a.matches,a.win,u.coupon,u.nick,a.add_time,a.last_login from lord_game_analyse a left join lord_game_user u on a.uid = u.uid where u.cool_num in (".join(',', $cool_nums).")";
//var_dump($sql);
$res = $db -> query($sql) -> fetchAll(PDO::FETCH_ASSOC);
$res = $res ? $res : array();
if ($res) {
    $res_ = $res;
    $res = array();
    foreach ( $cool_nums as $k => $v )
    {
        foreach ($res_ as $key => $value) {
            if ( $value['cool_num'] == $v ) {
				$uid = intval($value['uid']);
			    $ret = apiGet('online', 'getuser', array('uid'=>$uid));
			    $value['isOnline'] = $ret && isset($ret['data']["lord_user_info_$uid"]) && $ret['data']["lord_user_info_$uid"] ? 1 : 0;
                $res[] = $value;//按cool_num的顺序，把查询到的数据，逐个添加到结果数组里面
            }
        }
    }
}
$array['data'] = $res;
$array['cool_num2'] = count($res) == 2 ? $res[0]['cool_num'] : null;
$array['nick'] = count($res) == 2 ? $res[0]['nick'] : null;
echo json_encode($array);
