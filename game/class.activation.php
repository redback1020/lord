<?php
/**
* PHP activation class
*/

if ( !function_exists("serr") )
{
	define("MY_HOST", isset($is_activationtest) && $is_activationtest ? "127.0.0.1" : "10.10.13.141");
	define("MY_PORT", 3306);
	define("MY_USER", "dbx5415j5nf05kqn");
	define("MY_PASS", "TYxYpysG8fR8PQdp");
	define("MY_BASE", "dbx5415j5nf05kqn");
	define("MY_CHAR", "UTF8");
	function serr($data)
	{
		$res = error_log( date("Y-m-d H:i:s")." [CLASS.ACTIVATION] $data \n", 3, __DIR__."/game/log/activation.log" );
	}
}

class activation
{
	public $errno = 0;
	public $error = 'done.';
	public $mysql = null;
	public $tablename = 'lord_game_activation';
	public $baseid = 12345678;
	public $limitid = 99999999;
	public $id = 0;

	public function __construct( $mysql=null )
	{
		$mysql && $this->mysql = $mysql;
		if ( !$this->mysql ) {
			require_once(__DIR__."/../lib/class.mysql.php");
			$this->mysql = new DB;
			if ( !$this->mysql ) serr("[MYSQL] new failed.");
		}
	}

	public function gets( $cateid=1, $number=1 )
	{
		$tablename = $this->tablename;
		$cateid = min(100,max(1, intval($cateid)));
		$number = min(100,max(1, intval($number)));
		// id, code, cateid, status, ut_create, ut_update
		//status 0未发 1已发 2已用
		$codes = $ids = array();
		$sql = "SELECT count(`id`) FROM `$tablename` WHERE `cateid` = $cateid AND `status` = 0";
		$numall = $this->mysql->getVar($sql);
		if ( $numall < 100 ) {
			$this->make($cateid, $number * 2);
		}
		$sql = "SELECT `id`, `code` FROM `$tablename` WHERE `cateid` = $cateid AND `status` = 0";
		$ret = $this->mysql->getData($sql);
		if ( ! $ret ) $ret = array();
		foreach ( $ret as $k => $v )
		{
			$codes[$v['id']] = $v['code'];
		}
		$ids = array_rand($codes, min($number,count($codes)));
		if ( ! is_array($ids) ) $ids = array($ids);
		foreach ( $codes as $k => $v )
		{
			if ( ! in_array($k, $ids) ) unset($codes[$k]);
		}
		// for ( $i=0; $i < $number; $i++ )
		// {
		// 	$sql = "SELECT t1.* FROM `$tablename` AS t1 JOIN (SELECT ROUND(RAND() * ((SELECT MAX(id) FROM `$tablename` WHERE `cateid`=$cateid AND `status`=0)-(SELECT MIN(id) FROM `$tablename` WHERE `cateid`=$cateid AND `status`=0))+(SELECT MIN(id) FROM `$tablename` WHERE `cateid`=$cateid AND `status`=0)) AS id) AS t2 WHERE t1.id >= t2.id AND t1.cateid = $cateid AND t1.`status` = 0 ORDER BY t1.id LIMIT 1";
		// 	$res = $this->mysql->getLine($sql);
		// 	if ( $res ) $codes[$res['id']]=$res['code'];
		// }
		// if ( count($codes) < $number ) {
		// 	$res = $this->make($cateid, $number * 2);
		// 	$codes = array();
		// 	for ( $i=0; $i < $number; $i++ )
		// 	{
		// 		$sql = "SELECT t1.* FROM `$tablename` AS t1 JOIN (SELECT ROUND(RAND() * ((SELECT MAX(id) FROM `$tablename` WHERE `cateid`=$cateid AND `status`=0)-(SELECT MIN(id) FROM `$tablename` WHERE `cateid`=$cateid AND `status`=0))+(SELECT MIN(id) FROM `$tablename` WHERE `cateid`=$cateid AND `status`=0)) AS id) AS t2 WHERE t1.id >= t2.id AND t1.cateid = $cateid AND t1.`status` = 0 ORDER BY t1.id LIMIT 1";
		// 		$res = $this->mysql->getLine($sql);
		// 		if ( $res ) $codes[$res['id']]=$res['code'];
		// 	}
		// }
		$ids = array_keys($codes);
		$sql = "UPDATE `$tablename` SET `status` = 1, `ut_update` = unix_timestamp() WHERE `id` IN (".join(',', $ids).")";
		$res = $this->mysql->runSql($sql);
		if ( !$res ) {
			serr($sql);
			return array();
		}
		return array_values($codes);
	}

	// 生成code，存入数据库
	public function make( $cateid=1, $number=1 )
	{
		$tablename = $this->tablename;
		$baseid = $this->baseid;
		$limitid = $this->limitid;
		$cateid = min(100,max(1, intval($cateid)));
		$number = min(100,max(1, intval($number)));
		$sql = "SELECT max(`id`) FROM `$tablename`";
		$maxid = $this->mysql->getVar($sql) + 1;
		$minid = $maxid > $baseid ? $maxid : $baseid;
		$maxid = $minid + $number;
		if ( $maxid > $limitid ) return 0;
		$ut_now = time();
		$sql = "INSERT INTO `$tablename` (`id`,`cateid`,`code`,`status`,`ut_create`,`ut_update`) VALUES ";
		for ( $i = $minid; $i < $maxid; $i++ )
		{
			$sql.="( $i, $cateid, '".$this->encode($i)."', 0, $ut_now, $ut_now ),";
		}
		$res = $this->mysql->runSql(trim($sql, ','));
		if ( !$res ) serr($sql);
		return $res ? 1 : 0;
	}

	// 检查code的有效性，并在更新数据为已用状态后，成功返回code的cateid，失败返回0，无效返回-1
	public function check( $code )
	{
		$tablename = $this->tablename;
		$code = strtoupper($code);
		$id = $this->decode($code);
		if ( !$id ) return -1;
		$sql = "SELECT * FROM `$tablename` WHERE `id` = $id AND `status` = 1";
		$data = $this->mysql->getLine($sql);
		if ( !$data || !is_array($data) || !isset($data['cateid']) ) return -1;
		$sql = "UPDATE `$tablename` SET `status` = 2, `ut_update` = unix_timestamp() WHERE `id` = $id";
		$res = $this->mysql->runSql($sql);
		if ( !$res ) {
			serr($sql);
			return 0;
		}
		$this->id = $id;
		return intval($data['cateid']);
	}

	// 重置code状态，默认为未发状态
	public function reset( $status=0 )
	{
		$status = min(2,max(0, intval($status)));
		$tablename = $this->tablename;
		$id = $this->id;
		if ( !$id ) return 0;
		$sql = "UPDATE `$tablename` SET `status` = $status, `ut_update` = unix_timestamp() WHERE `id` = $id";
		$res = $this->mysql->runSql($sql);
		if ( !$res ) {
			serr($sql);
			return 0;
		}
		return $id;
	}

	private function encode( $num )
	{	// AMBJ1D0PBP
		$str = strval($num);
		$len = strlen($str);
		$m = $str[0];
		$n = $str[$len-1];
		$s = '';
		for ( $i=0; $i < $len; $i+=2 )
		{
			$nn = intval($str[$i].$str[$i+1]);
			$s.= chr(65+intval($nn/25));
			$s.= chr(65+$nn%25);
		}
		$mi = mt_rand(0,$len-2);
		$ni = mt_rand($mi+1,$len-1);
		return substr_replace(substr_replace($s, $m, $mi, 0), $n, $ni, 0);
	}

	private function decode( $str )
	{	// 12345678
		$str = trim($str);
		$len = strlen($str);
		if ( $len != ( strlen($this->limitid) + 2 ) ) return 0;
		$nn = $ss = '';
		$m = $n = false;
		for ( $i=0; $i < $len; $i++ )
		{
			if ( is_numeric($str[$i]) ) {
				if ( $m===false ) $m = intval($str[$i]);
				elseif ( $n===false ) $n = intval($str[$i]);
				else return 0;
			} else {
				$ss.=$str[$i];
			}
		}
		$str = $ss;
		$len = strlen($str);
		if ( $len != strlen($this->limitid) ) return 0;
		for ( $i=0; $i < $len; $i+=2 ) {
			$n_ = intval((ord($str[$i])-65) * 25 + ord($str[$i+1])-65);
			$nn.= $n_ > 9 ? $n_ : ('0'.$n_);
		}
		if ( $nn[0] != $m || $nn[$len-1] != $n ) return 0;
		return intval($nn);
	}

}
