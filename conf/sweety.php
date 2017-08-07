<?php

define("SW_CONNECT", 100);//最大连接数  10240    win机器最大连接256
define("SW_BACKLOG", 1024 );//最大排队数
define("SW_DISPATCH", 2   );//FD分配模式
define("SW_WORKER", 8     );//协议进程数
define("SW_TASKER", 10   );//任务进程数
define("SW_REQUEST", 30000);//进程回收数
define("SW_INTERVAL", 10  );//心跳频率秒
define("SW_IDLETIME", 100 );//心跳寿命秒
