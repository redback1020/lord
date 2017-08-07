<?php
require_once '../manage/checkPriv.php';
require_once '../include/database.class.php';

$val['cool_num']=$_GET["cool_num"];
$val['uid']=$_GET["uid"];
//var_dump($val['cool_num']);
$uid=$val['uid'];
$cool_num=$val['cool_num'];

$sql="select count(cool_num) from lord_game_user where cool_num=$cool_num";
$res = $db -> query($sql) -> fetch(PDO::FETCH_ASSOC);

if($res[ "count(cool_num)"]>1){
    $sql="update lord_game_user set cool_num={$uid}+1234567 where cool_num=$cool_num and uid=$uid";
$pdo->getDB(1)->query($sql);
    $cool_numnew=$uid+1234567;
}
else{
$cool_numnew=$cool_num;
}
echo $cool_numnew;
