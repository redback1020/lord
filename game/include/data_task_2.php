<?php

// data_task_2.php

$data_task_2 = array(
	'id' => 2,
	'name' => '今日参加100局普通场，奖励一次抽奖机会',
	'actions' => array(),
	'columns' => array(
			'lord_user_task.normal_day_play'=>'',
			'lord_game_user.lottery'=>'',
		),
	'if_get' => 0,
	'if_pre' => array(
			array('key'=>'lord_user_task.normal_day_play','leg'=>'e','val'=>100),
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

