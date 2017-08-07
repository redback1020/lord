<?php
require_once '../manage/checkPriv.php';
require_once '../include/database.class.php';
$pageIndex = 10*$_REQUEST['pageIndex'];
$pageSize = $_REQUEST['pageSize'];

if(strtotime($_SESSION['time'])>strtotime($_REQUEST['start'])){
$start = $_SESSION['time'];
}else{
  $start = $_REQUEST['start'];
}


if(strtotime($_SESSION['time'])>strtotime($_REQUEST['end'])){
    $end = $_SESSION['time'];
}else{
    $end = $_REQUEST['end'];
}


$a=strtotime($_REQUEST['start']);
$b=$a + 86400;

$data = $_REQUEST['data'];


   
//var_dump($a);






$sql = "select * from lord_model_games where gameStart>=$a and gameOver<=$b order by gameId desc limit ".$pageIndex.",".$pageSize ;
//var_dump($sql);
$res = $db -> query($sql)-> fetchAll(PDO::FETCH_ASSOC);


foreach($res as $k=>$v){

        $res[$k]['gameStart'] = date('Y-m-d H:i:s', $res[$k]['gameStart']);


        $res[$k]['gameOver'] = date('Y-m-d H:i:s', $res[$k]['gameOver']);

    $res[$k]['gameScore'] = json_decode($v['gameScore'],true);
    $res[$k]['gamePrizeCoins'] = json_decode($v['gamePrizeCoins'],true);
    $res[$k]['gamePrizePoint'] = json_decode($v['gamePrizePoint'],true);
    $res[$k]['gamePrizeProps'] = json_decode($v['gamePrizeProps'],true);
}












$array['data'] = $res;

$sql = "SELECT count(*) as cn from lord_model_games where gameStart>=$a and gameOver<=$b";
$res = $db -> query($sql) ->fetch(PDO::FETCH_ASSOC);
$array['cn'] = $res['cn'];

echo json_encode($array);


?>
 

