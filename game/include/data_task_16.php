<?php

// data_task_16.php

$data_task_16 = array(
	'id' => 16,
	'name' => '领取钻石礼包',
	'actions' => array(50149),
	'columns' => array(
			'lord_user_task.task16'=>'',
			'lord_user_task.task16dateid'=>'',
		),
	'if_get' => 0,
	'if_pre' => array(
			array('key'=>'lord_user_task.task16','leg'=>'e','val'=>0),
		),
	'if_yes' => array(
		),
	'if_yrs' => array(
		),
	'if_not' => array(),
	'if_nrs' => array(),
	'days' => 0,
	'times' => 1,
	'opening' => array(),
);

