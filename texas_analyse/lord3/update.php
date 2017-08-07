<?php
require_once '../manage/checkPriv.php';
require_once '../include/database.class.php';
$cool_num1 = $_REQUEST['cool_num1'];
$cool_num2 = $_REQUEST['cool_num2'];
$nick=$_REQUEST['nick'];



if (  !$cool_num1 or($cool_num1 and $cool_num2 and $nick) or ($cool_num1 and !$cool_num2 and !$nick ) ) {
die();
}

else {


    if (!$nick) {

//查旧账号数据
        $sql1 = "select cool_num,coins,login,matches,win,coupon,nick from lord_game_analyse,lord_game_user where lord_game_analyse.uid=(select uid from lord_game_user where cool_num=$cool_num2) and lord_game_user.cool_num={$cool_num2}";
        $row = $pdo->getDB(1)->query($sql1)->fetchAll(PDO::FETCH_ASSOC);
        foreach ($row as $k => $v) {

        }
//新账号uid
        $sql4 = "select * from lord_game_user where cool_num=$cool_num1";
        $result = $pdo->getDB(1)->query($sql4)->fetchAll(PDO::FETCH_ASSOC);
        foreach ($result as $a => $b) {

        }
//var_dump($b["uid"]);
//累加数据到新账号
        $sql2 = "update lord_game_user,lord_game_analyse set win=win+{$v["win"]},matches=matches+{$v["matches"]},login=login+{$v["login"]},coins=coins+{$v["coins"]},coupon=coupon+{$v["coupon"]} where lord_game_analyse.uid={$b["uid"]} and lord_game_user.cool_num={$cool_num1}";

        $pdo->getDB(1)->query($sql2);


//旧数据写入WORD
        $sql5 = "update lord_game_user set word ='{$v["coins"]},{$v["coupon"]},{$v["login"]},{$v["matches"]},{$v["win"]}'where lord_game_user.cool_num=$cool_num2";
        $pdo->getDB(1)->query($sql5);


//旧账号uid
        $sql6 = "select * from lord_game_user where cool_num=$cool_num2";
        $res = $pdo->getDB(1)->query($sql6)->fetchAll(PDO::FETCH_ASSOC);
        foreach ($res as $c => $d) {
        }

        $sql3 = "update lord_game_user,lord_game_analyse set win=0,matches=0,login=0,coins=0,coupon=0,nick='已转到编号ID" . $b["cool_num"] . "' where lord_game_analyse.uid=" . $d["uid"] . " and lord_game_user.cool_num={$cool_num2}";
//var_dump($sql3);
//$ae=$pdo->getDB(1)->query($sql3);
        $pdo->getDB(1)->query($sql3);
//var_dump($ae);


        echo json_encode(['result' => true]);
    } else if (!$cool_num2) {
        //查旧账号数据
        $sql1 = "select cool_num,coins,login,matches,win,coupon,nick from lord_game_analyse,lord_game_user where lord_game_analyse.uid=any(select uid from lord_game_user where nick='$nick') and lord_game_user.nick='$nick'";
        $row = $pdo->getDB(1)->query($sql1)->fetchAll(PDO::FETCH_ASSOC);
        foreach ($row as $k => $v) {

        }
//取新账号uid
        $sql4 = "select * from lord_game_user where cool_num=$cool_num";
        $result = $pdo->getDB(1)->query($sql4)->fetchAll(PDO::FETCH_ASSOC);
        foreach ($result as $a => $b) {

        }
//var_dump($b["uid"]);
//累加数据到新账号
        $sql2 = "update lord_game_user,lord_game_analyse set win=win+{$v["win"]},matches=matches+{$v["matches"]},login=login+{$v["login"]},coins=coins+{$v["coins"]},coupon=coupon+{$v["coupon"]} where lord_game_analyse.uid={$b["uid"]} and lord_game_user.cool_num={$cool_num}";

        $pdo->getDB(1)->query($sql2);


//旧数据写入WORD
        $sql5 = "update lord_game_user set word ='{$v["coins"]},{$v["coupon"]},{$v["login"]},{$v["matches"]},{$v["win"]}'where lord_game_user.nick='$nick'";
        $pdo->getDB(1)->query($sql5);


//取旧账号uid
        $sql6 = "select * from lord_game_user where nick='$nick'";
        $res = $pdo->getDB(1)->query($sql6)->fetchAll(PDO::FETCH_ASSOC);
        foreach ($res as $c => $d) {
        }

        $sql3 = "update lord_game_user,lord_game_analyse set win=0,matches=0,login=0,coins=0,coupon=0,nick='已转到编号ID" . $b["cool_num"] . "' where lord_game_analyse.uid=" . $d["uid"] . " and lord_game_user.nick='$nick'";
//var_dump($sql3);
//$ae=$pdo->getDB(1)->query($sql3);
        $pdo->getDB(1)->query($sql3);
//var_dump($ae);

        echo json_encode(['result' => true]);
    }
}
