<?php

// data_task_13.php

$data_task_13 = array(
	'id' => 13,
	'name' => '领取阿里礼包',
	'actions' => array(50149),
	'columns' => array(
			'lord_user_task.task13'=>'',
			'lord_user_task.task13dateid'=>'',
		),
	'if_get' => 0,
	'if_pre' => array(
			array('key'=>'lord_user_task.task13','leg'=>'e','val'=>0),
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

