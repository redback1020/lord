<?php

//base
date_default_timezone_set('PRC');
error_reporting(E_ALL);//E_ERROR | E_WARNING | E_PARSE
define('DIR_SERVER', dirname(__DIR__));
define('DIR_PUBLIC', dirname(DIR_SERVER)."/public");
define('CRONLOG',DIR_SERVER.'/cron/cron.log');
function cronlog($data)
{
	$res = error_log(date("Y-m-d H:i:s")." [".CRONTAG."] $data \n",3,CRONLOG);
}


//redis
// define("REDIS_HOST", "127.0.0.1");
define("REDIS_HOST", "10.10.40.48");
define("REDIS_PORT", 6379);
$class_redis_file = DIR_SERVER.'/lib/class.rediscls.php';
require_once ( $class_redis_file );

//db
// define("MYSQL_HOST", "127.0.0.1");
define("MYSQL_HOST", "10.10.13.141");
define("MYSQL_PORT", 3306);
define("MYSQL_USERNAME", "dbx5415j5nf05kqn");
define("MYSQL_PASSWORD", "TYxYpysG8fR8PQdp");
define("MYSQL_DBNAME", "dbx5415j5nf05kqn");
define("MYSQL_CHARSET", "UTF8");
$class_mysql_file = DIR_SERVER.'/lib/class.db.php';
require_once ( $class_mysql_file );

//redis|db errorlog
function serr($data)
{
	return cronlog($data);
}

//post|get to url 
function urlReq($url, $is_get = true, $data = null, $agent = 0, $cookie = null, $timeout = 3)
{
	if ($agent && is_int($agent)) {
		$user_agent = ini_get('user_agent');
		ini_set('user_agent', 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; SV1; .NET CLR 2.0.50727;)');
	}
	elseif ($agent && is_array($agent)) {
		$user_agent = ini_get('user_agent');
		ini_set('user_agent', $agent[array_rand($agent)]);
	}
	elseif (is_string($agent)) {
		$user_agent = ini_get('user_agent');
		ini_set('user_agent', $agent);
	}
	else {
		$user_agent = false;
	}
	$context['http']['method'] = (!$is_get && is_array($data)) ? 'POST' : 'GET';
	$context['http']['header'] = (!$is_get && is_array($data)) ? "Content-Type: application/x-www-form-urlencoded; charset=utf-8" : "Content-Type: text/html; charset=utf-8";
	$context['http']['timeout'] = $timeout;
	if ( $context['http']['method'] == 'POST' )
	{
		if ( $cookie && is_string($cookie) )
		{
			$context['http']['header'] .= PHP_EOL.$cookie;
		}
		if ( strpos($url, 'https://') === 0 && isset($data['https_user']) && isset($data['https_password']) )
		{
			$context['http']['header'] .= PHP_EOL."Authorization: Basic ".base64_encode($data['https_user'].":".$data['https_password']);
			unset($data['https_user']);
			unset($data['https_password']);
		}
		$context['http']['content'] = http_build_query($data, '', '&');
	}
	$res = file_get_contents($url, false, stream_context_create($context));
	$user_agent !== false && ini_set('user_agent', $user_agent);
	return $res;
}
