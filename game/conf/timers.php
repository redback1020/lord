<?php

// 配置定时闹钟
// act      string  定时器的方法标记。会在对应毫秒后执行$gamer->runEvent($act)
// delay    int     定时器的间隔毫秒。小于86400000
// isMaster int 	0(默认)在所有服务器上分别执行；1只在Master服务器上执行
// 新增或修改定时器，需要执行reload()后生效，或会在进程自动重载后自动生效
return $timers = array(
	array('act'=>'TIMERM_10S_ROOMS', 'delay'=>10000, 'isMaster'=>1),//10秒钟主服单独处理
	array('act'=>'TIMERA_10S_OTHER', 'delay'=> 5000, 'isMaster'=>0),//5 秒钟全服各自处理
	array('act'=>'TIMERM_1M',        'delay'=>60000, 'isMaster'=>1),//每分钟主服单独处理
 // array('act'=>'TIMERA_1M',        'delay'=>60000, 'isMaster'=>0),//每分钟全服各自处理
	array('act'=>'TIMERM_1M_ONLINE', 'delay'=>60000, 'isMaster'=>1),//每分钟主服单独处理
);
