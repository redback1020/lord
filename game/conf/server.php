<?php

define("ISTESTS", 1);	//0生产模式1测试模式
define("ISDEBUG", 1);	//0关闭调试1开启调试
define("ISLOCAL", 1);	//0线上模式1本地模式
define("ISROBOT", 1);	//0无机器人1有机器人
define("ISPRESS", 0);	//0常规逻辑1压测逻辑
define("LOGTAG", "GAME-DDZ");//线上模式运行时的远程集中日志前缀标记
define("ROE_LOGIN", 0.6  );	//用户登入超过N秒时，记录ERROR
define("ROE_LOGOUT", 0.6 );	//用户登出超过N秒时，记录ERROR
define("ROE_JUMPIN", 0.5 );	//用户跳入超过N秒时，记录ERROR
define("ROE_JUMPOUT", 0.4);	//用户跳出超过N秒时，记录ERROR
define("ROE_ACTION", 0.4 );	//协议执行超过N秒时，记录ERROR
define("ROE_EVENT", 0.4  );	//任务执行超过N秒时，记录ERROR
define("ROE_TIMER", 1    );	//事件运行溢出N倍时，记录ERROR
define("ROE_CRONTAB", 60 );	//定时运行溢出N秒时，记录ERROR
define("ROE_SEND", 0.02  );	//跨服转发时间，无效，暂时保留此设置
define("ROE_LOCK", 10    );	//最大锁长，超时将被自动解锁
// define("KEY_USER_", 'user_info_');	//REDIS_KEY_PREFIX_ for userdata
define("KEY_USER_", 'lord_user_info_');	//REDIS_KEY_PREFIX_ for userdata
//基础协议组合号 =$cmd ? ($cmd * 10000 + $code) : sprintf("%1$05d", $code)，下同 < 20000
define("ACT_HEART", '00000');  //心跳协议
define("ACT_LOGIN", '10000');  //登入协议
define("ACT_LOGOUT", '10005'); //登出协议
define("ACT_REG", "10006");//注册
define("ACT_JUMPIN", '20001'); //进入分区
define("ACT_JUMPOUT", '20003');//返回大厅
define("ACT_HORN", '30301');   //广播协议
define("ACT_SPEED", '50127');  //协议[,协议...]频率间隔过快会断开用户
