<?php

//竞技场动态配置	每次修改配置之后，需要传到所有斗地主服务器上，
//并需要在180.150.178.112服务器上执行 php /alidata1/wwwroot/landlord/server3/cron/cron_server_reload.php
// 下面的日期范围(含)之间，报名费用降低至1000乐币，目前不适用其他场次，只使用初级竞技场
$game['is_onsale'] = 0;
if ( time() > strtotime("2015-03-23") && time() <= (strtotime("2015-04-19")+86400) ) {
	$game['is_onsale'] = 1;			//是否有优惠标记
	$game['gameInCoins'] = 1000;	//报名费变化 	源自config.php 
	$game['gameBombAdd'] = 1;		//炸弹增加数 	源自config.php 
	//$game['gamePrizeCoins'] = ?;	//奖励豆变化 	源自config.php
	//$game['gamePrizeCoupon'] = ?;	//奖励券变化 	源自config.php
	//$game['gamePrizePoint'] = ?;	//奖励分变化 	源自config.php
	//$game['gameRule'] = ?;		//规则变化无法调整，因为客户端好像没有使用  	源自config.php
}
