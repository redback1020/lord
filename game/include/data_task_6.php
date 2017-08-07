<?php

// data_task_6.php

$data_task_6 = array(
	'id' => 6,
	'name' => '每天累计使用超过100乐币，获得一次抽奖机会',
	'actions' => array(50041),
	'columns' => array(
			'lord_user_task.gold_day'=>'',
			'lord_game_user.lottery'=>'',
		),
	'if_get' => 0,
	'if_pre' => array(
			array('key'=>'lord_user_task.gold_day','leg'=>'ge','val'=>100),
		),
	'if_yes' => array(
		),
	'if_yrs' => array(
			array('key'=>'lord_game_user.lottery','val'=>'+1'),
		),
	'if_not' => array(),
	'if_nrs' => array(),
	'days' => 1,
	'times' => 1,
	'opening' => array(),
);

