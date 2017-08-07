<?php
require_once '../include/database.class.php';
  $pageIndex = 50*$_REQUEST['pageIndex'];
  $pageSize = $_REQUEST['pageSize'];
  

  $sql = "select a.`uid`,a.`nick`,b.wechat_nickname,a.`gold`,a.`coins`,a.`offline_gold`,a.`offline_coins`,a.`fruit_free`,
  a.`vip_lv`,a.`level`,a.`channel`,b.add_time as add_time,c.last_time as last_login, c.add_time as reg_time
  FROM `mobile_user` a, t_wechat_user b,mobile_analyse c where a.uid=b.poker_id and b.poker_id=c.uid and b.poker_id>0 GROUP BY a.`uid`

	order by b.add_time desc limit ".$pageIndex.",".$pageSize;
  $row = $db -> query($sql)-> fetchAll();
  $array['data'] = $row;
  
  $sql="select count(*) as cn
  FROM `mobile_user` a, t_wechat_user b,mobile_analyse c where a.uid=b.poker_id and b.poker_id=c.uid and b.poker_id>0 GROUP BY a.`uid`
";
  $res = $db -> query($sql) -> fetch();
  $array['cn'] = $res['cn'];
	echo json_encode($array);
	 
		 
?>
 
