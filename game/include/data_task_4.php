<?php

// data_task_4.php

$data_task_4 = array(
	'id' => 4,
	'name' => '累计使用30乐币，成为金牌用户',
	'actions' => array(50041),
	'columns' => array(
			'lord_user_task.gold_level'=>'',
			'lord_user_task.gold_all'=>'',
		),
	'if_get' => 0,
	'if_pre' => array(
			array('key'=>'lord_user_task.gold_all','leg'=>'ge','val'=>30),
		),
	'if_yes' => array(
		),
	'if_yrs' => array(
			array('key'=>'lord_user_task.gold_level','val'=>'=1'),
		),
	'if_not' => array(),
	'if_nrs' => array(),
	'days' => 0,
	'times' => 1,
	'opening' => array(),
);

