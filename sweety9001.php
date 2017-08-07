<?php
//base
date_default_timezone_set('PRC');
error_reporting(E_ALL);		//E_ERROR | E_WARNING | E_PARSE
set_time_limit(0);
define("ROOT", __DIR__."/game");
define("SW_LOG", ROOT."/log/sweety.log");//PHP-ERR/SW-ERR/ECHO/DUMP/..
//conf
require ROOT."/conf/server.php";
//host
require __DIR__."/conf/host.php";
define("PORT", 9001);
define("HOSTID", HOST."_".PORT);
//mysql
require __DIR__."/conf/mysql.php";
//redis
require __DIR__."/conf/redis.php";
//conf
require __DIR__."/conf/sweety.php";
//start
require __DIR__."/sweety.php";
$sweety = new Sweety;
$sweety->start();
