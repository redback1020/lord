<?php

// data_task_12.php

$data_task_12 = array(
	'id' => 12,
	'name' => '领取微信礼包',
	'actions' => array(50149),
	'columns' => array(
			'lord_user_task.task12'=>'',
			'lord_user_task.task12dateid'=>'',
		),
	'if_get' => 0,
	'if_pre' => array(
			array('key'=>'lord_user_task.task12','leg'=>'e','val'=>0),
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

