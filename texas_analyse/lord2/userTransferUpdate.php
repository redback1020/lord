<?php
require_once '../manage/checkPriv.php';
require_once '../include/database.class.php';

//只获取这两个编号ID的数据，不再获取昵称的数据
$cool_num1 = intval(trim($_REQUEST['cool_num1']));
$cool_num2 = intval(trim($_REQUEST['cool_num2']));

//再次检查旧新两个编号ID
if ( !$cool_num1 || !$cool_num2 || $cool_num1 == $cool_num2 ) {
    echo json_encode(array('result' => false));
    die();
}

//再次检查旧新两个编号ID的数据，是否各自唯一
$sql = "select uid, cool_num from lord_game_user where cool_num = $cool_num1 or cool_num = $cool_num2";
$data = $db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
if ( !$data || !is_array($data) || count($data) != 2 ) {
    echo json_encode(array('result' => false));
    die();
}

//再次检查旧新两个编号ID都处于离线状态
$api = 'online';
$type = 'getuser';
foreach ( $data as $k => $user_ ) {
    $uid = intval($user_['uid']);
    $res = apiGet($api, $type, array('uid'=>$uid));
    $online = $res && isset($res['data']["lord_user_info_$uid"]) ? $res['data']["lord_user_info_$uid"] : array();
    if ( $online ) {
        echo json_encode(array('result' => false));
        die();
    }
}

//找到新旧UID
$uid_old = $uid_new = 0;
foreach ( $data as $k => $user_ ) {
    if ( $user_['cool_num'] == $cool_num2 ) $uid_old = intval($user_['uid']);
    if ( $user_['cool_num'] == $cool_num1 ) $uid_new = intval($user_['uid']);
}
if ( !$uid_old || !$uid_new ) {
    echo json_encode(array('result' => false));
    die();
}

$transfer_log = array();

//查旧账号数据
$sql = "select u.uid,u.cool_num,u.coins,a.login,a.matches,a.win,u.coupon,u.nick,a.add_time from lord_game_user u left join lord_game_analyse a on u.uid=a.uid where u.cool_num = {$cool_num2}";
$old = $db->query($sql)->fetch(PDO::FETCH_ASSOC);

//查新账号数据
$sql = "select u.uid,u.cool_num,u.coins,a.login,a.matches,a.win,u.coupon,u.nick,a.add_time from lord_game_user u left join lord_game_analyse a on u.uid=a.uid where u.cool_num = {$cool_num1}";
$new = $db->query($sql)->fetch(PDO::FETCH_ASSOC);

//累加数据到新账号
$sql = "update lord_game_user u, lord_game_analyse a set a.win = a.win+".$old["win"].",a.matches=a.matches+".$old["matches"].",a.login=a.login+".$old["login"].",u.coins=u.coins+".$old["coins"].",u.coupon=u.coupon+".$old["coupon"]." where a.uid=".$new["uid"]." and u.cool_num=".$new['cool_num'];
$pdo->getDB(1)->query($sql);

//改写旧账号数据
$sql = "update lord_game_user u, lord_game_analyse a set a.win=0, a.matches=0, a.login=0, u.coins=0, u.coupon=0, u.nick='已转到编号ID".$new['cool_num']."', u.word='".$old["coins"].",".$old["coupon"].",".$old["login"].",".$old["matches"].",".$old["win"]."' where a.uid=".$old["uid"]." and u.cool_num=".$old['cool_num'];
$pdo->getDB(1)->query($sql);

$transfer_log["old_uid"] = $old["uid"];
$transfer_log["old_id"] = $cool_num2;
$transfer_log["old_name"] = "'".$old["nick"]."'";
$transfer_log["old_coins"] = $old["coins"];
$transfer_log["old_matches"] = $old["matches"];
$transfer_log["old_win"] = $old["win"];
$transfer_log["old_reg"] = "'".$old["add_time"]."'";
$transfer_log["new_uid"] = $new["uid"];
$transfer_log["new_id"] = $cool_num2;
$transfer_log["new_name"] = "'".$new["nick"]."'";
$transfer_log["new_coins"] = $new["coins"];
$transfer_log["new_matches"] = $new["matches"];
$transfer_log["new_win"] = $new["win"];
$transfer_log["new_reg"] = $new["add_time"];
$transfer_log["create_time"] = "'".date("Y-m-d H:i:s")."'";
$transfer_log["`user`"] = "'".$_SESSION["admin_name"]."'";
$sql = "insert into user_transfer_log(".join(',', array_keys($transfer_log)).")values(".join(',', array_values($transfer_log)).");";
$res = $pdo->getDB(1)->query($sql);


//转移旧账号的金牌到新帐号 只处理旧为金牌但新不为金牌的
$sql = "select gold_level from lord_user_task where uid = $uid_old";
$old = $db->query($sql)->fetch(PDO::FETCH_ASSOC);
if ( $old && $old['gold_level'] ) {
    $sql = "select gold_level from lord_user_task where uid = $uid_new";
    $new = $db->query($sql)->fetch(PDO::FETCH_ASSOC);
    if ( $new && !$new['gold_level'] ) {
        $sql = "update lord_user_task set gold_level = ".$old['gold_level']." where uid = $uid_new";
        $res = $pdo->getDB(1)->query($sql);
        if ( $res ) {
            $sql = "update lord_user_task set gold_level = 0 where uid = $uid_old";
            $res = $pdo->getDB(1)->query($sql);
            if ( !$res ) {
                echo json_encode(array('result' => false));
                die();
            }
        }
    }
}

//转移旧账号的道具到新帐号
$sql = "select * from lord_user_item where uid = $uid_old";
$old = $db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
if ( $old ) {
    $sql = "select * from lord_user_item where uid = $uid_new";
    $new = $db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    if ( $new ) {
        $old_ = $old; $old = array();
        foreach ( $old_ as $k => $item ) {
            if ( isset($old[$item['pd']]) ) {
                echo json_encode(array('result' => false));
                die();
            }
            $old[$item['pd']] = $item;
        }
        $new_ = $new; $new = array();
        foreach ( $new_ as $k => $item ) {
            if ( isset($new[$item['pd']]) ) {
                echo json_encode(array('result' => false));
                die();
            }
            $new[$item['pd']] = $item;
        }
        $time = time();
        foreach ( $old as $pd => $item ) {
            if ( isset($new[$pd]) && ( $sec = $new[$pd]['end'] ? max(0, $time-$new[$pd]['end']) : ( $new[$pd]['sec'] ? $new[$pd]['sec'] : 0 ) ) ) {
                if ( $item['end'] ) {
                    $sql = "update lord_user_item set `end` = `end` + $sec where id = ".$item['id'];
                    $res = $pdo->getDB(1)->query($sql);
                    if ( !$res ) {
                        echo json_encode(array('result' => false));
                        die();
                    } else {
                        $sql = "delete from lord_user_item where id = ".$new[$pd]['id'];
                        $res = $pdo->getDB(1)->query($sql);
                        if ( !$res ) {
                            echo json_encode(array('result' => false));
                            die();
                        }
                    }
                } elseif ( $item['sec'] ) {
                    $sql = "update lord_user_item set `sec` = `sec` + $sec where id = ".$item['id'];
                    $res = $pdo->getDB(1)->query($sql);
                    if ( !$res ) {
                        echo json_encode(array('result' => false));
                        die();
                    } else {
                        $sql = "delete from lord_user_item where id = ".$new[$pd]['id'];
                        $res = $pdo->getDB(1)->query($sql);
                        if ( !$res ) {
                            echo json_encode(array('result' => false));
                            die();
                        }
                    }
                }
            }
        }
    }
    $oldids = array();
    foreach ( $old as $pd => $item ) {
        $oldids[]=$item['id'];
    }
    $sql = "update lord_user_item set uid = $uid_new where id IN (".join(',', $oldids).")";
    $res = $pdo->getDB(1)->query($sql);
    if ( !$res ) {
        echo json_encode(array('result' => false));
        die();
    }
}

echo json_encode(array('result' => true));
