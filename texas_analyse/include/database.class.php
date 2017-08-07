<?php

if ( strpos($_SERVER['REQUEST_URI'], 'TV_mobile2') > -1 ) {
	define("INI_FILE", "../include/sys_mobile.ini");
	define("URL_BASE", "");
	define("API_BASE", "");
} elseif ( strpos($_SERVER['REQUEST_URI'], 'lord3') > -1 ) {
	define("INI_FILE", "../include/sys_test.ini");//测试服务器
	define("URL_BASE", "http://180.150.178.112/");
	define("API_BASE", URL_BASE."api.php?api=");
} else {
	define("INI_FILE", "../include/sys.ini");
	define("URL_BASE", "http://ddzprotocal.51864.com/");
	define("API_BASE", URL_BASE."api.php?api=");
}

final class DB
{
	private $host = null;//地址
	private $host2 = null;//从库地址
    private $port = null;//端口
    private $name = null;//库名
    private $user = null;//帐号
    private $pass = null;//密码
	private $_db = null;//主库连接
	private $_db2 = null;//从库连接

	function __construct()
	{
		file_exists(INI_FILE) || die("读取系统的配置文件有误");
		$a = parse_ini_file(INI_FILE);
		( isset($a['ip']) && isset($a['port']) && isset($a['databasename']) && isset($a['username']) && isset($a['password']) ) || die("数据库连接的配置文件不存在");
		$this->host = filter_var($a['ip'],FILTER_SANITIZE_STRING);
		if ( isset($a['ip2']) && date("Ymd") > 20160728 ) $this->host2 = filter_var($a['ip2'],FILTER_SANITIZE_STRING);
		$this->port = filter_var($a['port'],FILTER_SANITIZE_NUMBER_INT);
		$this->name = filter_var($a['databasename'],FILTER_SANITIZE_STRING);
		$this->user = filter_var($a['username'],FILTER_SANITIZE_STRING);
		$this->pass = filter_var($a['password'],FILTER_SANITIZE_STRING);
	}

	public function getDB($isMater=0)
	{
		try {
			if ( $isMater ) {
				if ( $this->_db ) return $this->_db;
			} else {
				if ( $this->_db2 ) return $this->_db2;
				if ( $this->host2 ) {
					$this->_db2 = new PDO("mysql:host=".$this->host2.";port= ".$this->port.";dbname=".$this->name, $this->user, $this->pass);
					$this->_db2->exec("SET NAMES 'utf8'");
					return $this->_db2;
				}
			}
			$this->_db = new PDO("mysql:host=".$this->host.";port= ".$this->port.";dbname=".$this->name, $this->user, $this->pass);
			$this->_db->exec("SET NAMES 'utf8'");
			return $this->_db;
		} catch (PDOException $e) {
			die("Error!: " . $e->getMessage() . "<br/>");
		}
	}
}
//post|get to url
function urlReq( $url, $data=null, $timeout=3, $agent=0, $cookie=null)
{
	if ( $agent && is_int($agent) ) {
		$user_agent = ini_get('user_agent');
		ini_set('user_agent', 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; SV1; .NET CLR 2.0.50727;)');
	} elseif ( $agent && is_array($agent) ) {
		$user_agent = ini_get('user_agent');
		ini_set('user_agent', $agent[array_rand($agent)]);
	} elseif ( is_string($agent) ) {
		$user_agent = ini_get('user_agent');
		ini_set('user_agent', $agent);
	} else {
		$user_agent = false;
	}
	$context['http']['method'] = $data && is_array($data) ? 'POST' : 'GET';
	$context['http']['header'] = $data && is_array($data) ? "Content-Type: application/x-www-form-urlencoded; charset=utf-8" : "Content-Type: text/html; charset=utf-8";
	$context['http']['timeout'] = $timeout;
	if ( $context['http']['method'] == 'POST' ) {
		if ( $cookie && is_string($cookie) ) $context['http']['header'] .= PHP_EOL.$cookie;
		$context['http']['content'] = http_build_query($data, '', '&');
	}
	$res = file_get_contents($url, false, stream_context_create($context));
	$user_agent !== false && ini_set('user_agent', $user_agent);
	return $res;
}

function apiPost( $api, $type, $data )
{
	$sign = 'YouLeg@me888';
	$time = time();
	$mac = md5($api."&".$type."&&".$sign."&&&".$time);
	$url = API_BASE.$api."&type=$type&sign=$sign&time=$time&mac=$mac";
	$res = urlReq($url, array('data'=>json_encode($data)));
	if ( $res ) $res = json_decode($res, 1);
	if ( ! is_array($res) ) $res = array('errno'=>10,'error'=>'接口请求错误 url='.$url.' data='.json_encode($data),'data'=>array());
	return $res;
}
function apiGet( $api, $type, $data )
{
	$sign = 'YouLeg@me888';
	$time = time();
	$mac = md5($api."&".$type."&&".$sign."&&&".$time);
	$url = API_BASE.$api."&type=$type&sign=$sign&time=$time&mac=$mac"; foreach ( $data as $k => $v ) { $url.= "&$k=$v";}
	$res = urlReq($url);
	if ( $res ) $res = json_decode($res, 1);
	if ( ! is_array($res) ) $res = array('errno'=>10,'error'=>'接口请求错误 url='.$url.' data='.json_encode($data),'data'=>array());
	return $res;
}


$pdo = new DB();
$db = $pdo->getDB();
