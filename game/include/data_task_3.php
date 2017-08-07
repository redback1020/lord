<?php

// data_task_3.php

$data_task_3 = array(
	'id' => 3,
	'name' => '今日竞技场积分达到20分，奖励一次抽奖机会',
	'actions' => array(),
	'columns' => array(
			'lord_user_task.match_day_point'=>'',
			'lord_game_user.lottery'=>'',
		),
	'if_get' => 0,
	'if_pre' => array(
			array('key'=>'lord_user_task.match_day_point','leg'=>'ge','val'=>20),
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

