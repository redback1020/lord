<?php

// 设置Linux-CronJobs
// act      string  定时任务的方法标记。会在对应毫秒后执行$gamer->runCront($act)
// date   	string  定时任务的时间设置，等同linux的crontab的简单时间格式，但不支持“/”字符
// isMaster	int 	0(默认)在所有服务器上分别执行；1只在Master服务器上执行
// 每半分钟会检查一次本脚本是否有执行任务。 只推荐固定分时执行的任务在这里设置。
// 简单的任务或秒钟任务，比如每N秒、每N分钟、每N小时运行，请直接使用ticks.php来设置
// 新增更改定时任务，至少1分钟内生效。
return $crontab = array(
	array('act'=>'WEEK_MODEL_PRIZE', 'date'=>"50 23 * * 7", 'isMaster'=>1),
	array('act'=>'DAY_CREATE_TABLE', 'date'=>"40 23 * * *", 'isMaster'=>1),
	// array('act'=>'HOUR_TOTAL_CHANNEL', 'date'=>"4 * * * *", 'isOnly'=>1),
	array('act'=>'HOUR_TOTAL_TOPLIST', 'date'=>"11 * * * *", 'isMaster'=>1),
);
