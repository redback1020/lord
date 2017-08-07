<?php

// data_task_15.php

$data_task_15 = array(
	'id' => 15,
	'name' => '领取水晶礼包',
	'actions' => array(50149),
	'columns' => array(
			'lord_user_task.task15'=>'',
			'lord_user_task.task15dateid'=>'',
		),
	'if_get' => 0,
	'if_pre' => array(
			array('key'=>'lord_user_task.task15','leg'=>'e','val'=>0),
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

