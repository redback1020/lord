<?php

// data_task_1.php

$data_task_1 = array(
	'id' => 1,
	'name' => '新手初登送一次抽奖机会',
	'actions' => array(10000),
	'columns' => array(
			'lord_user_task.task1'=>" int(10) unsigned not null default '0' comment '任务1:新手初登送一次抽奖机会'",
			'lord_user_info.is_noob'=>" int(10) unsigned not null default '0' comment '是否新手初登'",
			'lord_user_info.lottery'=>" int(10) unsigned not null default '0' comment '抽奖当前可用次数'",
		),
	'if_get' => 0,
	'if_pre' => array(
			array('key'=>'lord_user_info.is_noob','leg'=>'e','val'=>1),
		),
	'if_yes' => array(
		),
	'if_yrs' => array(
			array('key'=>'lord_user_info.is_noob','leg'=>'e','val'=>0),
			array('key'=>'lord_user_info.lottery','val'=>'+1'),
		),
	'if_not' => array(),
	'if_nrs' => array(),
	'days' => 0,
	'times' => 1,
	'opening' => array(),
);

