<?php

//base
date_default_timezone_set('PRC');
error_reporting(E_ALL);//E_ERROR | E_WARNING | E_PARSE
define('DIR_SERVER', dirname(__DIR__));
define('DIR_PUBLIC', dirname(DIR_SERVER)."/public");
define("ROOT", DIR_SERVER."/game");
//write error log
function writelog($data)
{
	if ( !defined("TAG_NAME") ) {//TAG_NAME 需要在每个cron_*.php文件内define
		define("TAG_NAME", "undefined");
	}
	return error_log(date("Y-m-d H:i:s")." [".TAG_NAME."] $data \n", 3, ROOT."/log/cron.log");
}
//redis | db error log
function serr($data)
{
	return writelog($data);
}
//testing | online
require ROOT."/conf/server.php";
//mysql
function getMysql()
{
	require DIR_SERVER."/conf/mysql.php";
	require DIR_SERVER."/lib/class.mysql.php";
	return new DB;
}
//redis
function getRedis()
{
	require DIR_SERVER."/conf/redis.php";
	require DIR_SERVER."/lib/class.redis.php";
	return new RD;
}
//post|get to url
function urlReq($url, $is_post = false, $data = null, $agent = 0, $cookie = null, $timeout = 10)
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
	$context['http']['method'] = $is_post && is_array($data) ? 'POST' : 'GET';
	$context['http']['header'] = $is_post && is_array($data) ? "Content-Type: application/x-www-form-urlencoded; charset=utf-8" : "Content-Type: text/html; charset=utf-8";
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
