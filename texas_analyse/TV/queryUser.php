<?php
require_once '../include/database.class.php';
require_once 'curl.php';   
$array['sign'] = 'jlfsd87912hjk312h90f!@fsjdkl!23';

if($_REQUEST['type']=='coolNum'){$array['isCoolNum']=1;$array['uid'] = $_REQUEST['data'];}
else{$array['uid'] = $_REQUEST['data'];}
$obj = fetch_page('http://10.160.12.92:8106/get',$array);
$arrays['data'] = $obj;


if($obj['code'] == 0){
	$pdo = new DB();
	$db = $pdo->getDB();
	$uid = $obj['data']['uid'];
	$sql = "select * from t_wechat_user where poker_id = ".$uid;
    $res = $db -> query($sql)-> fetch();
	$arrays['wechat'] = $res;
	
	$sql = "select * from charge_log where uid =".$uid; 
	$row = $db -> query($sql)-> fetchAll();
	$arrays['charge'] = $row;
	
	$sql = "select * from game_charge where uid = ".$uid;
	$row = $db -> query($sql)-> fetchAll();
	$arrays['game_charge'] = $row;
}
echo json_encode($arrays);
?>
  