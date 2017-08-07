<?php
require_once '../include/database.class.php';
$pdo = new DB();
$db = $pdo->getDB();
$file = "login.log";
$content = file_get_contents($file);

preg_match_all("/ID:\[(.*?)\]/", $content, $ids);
//print_r($ids[1]);
preg_match_all("/IP:(.*?) is_tv/", $content, $IPs);
//print_r($IPs[1]);
 
$uidarray = array_unique($ids[1]);
$uidstr = implode("','",$uidarray);
$sql = "select m.uid,u.channel,a.add_time,m.is_robot from mobile_user m , mobile_analyse a,user_user u where m.uid = a.uid and a.uid = u.id and m.uid in('".$uidstr."') ";
$result = $db -> query($sql) -> fetchAll();
foreach($result as $val){
	$channel[$val['uid']] = $val['channel'];
	$add[$val['uid']] = $val['add_time'];
	$robot[$val['uid']] = $val['is_robot'];
}


$asd = explode("is_tv:",$content);
$str = $asd[0];
unset($asd[0]);
  
$ar = array();
$i=0;$flag = 0; $day = '2014-04-18';$sqlarray = array();
foreach($asd as $k=>$v)
{
	$a_temp = array();
	if($k>1)
	$a_temp['date'] = substr($asd[$k-1],3,8);
	else
	$a_temp['date'] = substr($str,1,8);
	$a_temp['id'] = $ids[1][$i];
	$a_temp['ip'] = $IPs[1][$i];
	$a_temp['is_tv'] = substr($v,0,1);
	array_push($ar,$a_temp);
	//if(date("H",strtotime($a_temp['date']))=="00"&&$flag==0){$day = date("Y-m-d",(strtotime($day)+24*3600));$flag=1;}
	//if(date("H",strtotime($a_temp['date']))=="23"&&$flag==1){$flag=0;}  
	//$sqlarray[] = "('".$ids[1][$i]."','".$IPs[1][$i]."','".$a_temp['is_tv']."','".date("Y-m-d",time())." ".$a_temp['date']."','".$channel[$ids[1][$i]]."')";
	//if(date("H",strtotime($a_temp['date']))=="23"&&$flag==0){$day = date("Y-m-d",(strtotime($day)-24*3600));;$flag=1;}
	if($robot[$ids[1][$i]] == 0)
	$sqlarray[] = "('".$ids[1][$i]."','".$IPs[1][$i]."','".$a_temp['is_tv']."','".$day." ".$a_temp['date']."','".$channel[$ids[1][$i]]."','".$add[$ids[1][$i]]."')";
	
	$i++;
}
if(count($sqlarray)>0){
	$strsql = implode(",",$sqlarray);
	$sql = "insert into mobile_game_login(uid,ip,is_tv,add_time,channel,reg_time) values".$strsql; 
	$db -> query($sql); 
	$fp =  fopen($file,"w"); 
	$p = fwrite($fp,"");
	fclose($fp);
}

//print_r($ar);
?>