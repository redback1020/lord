<?php
/**
* PHP 各服务器配置文件操作
*/
class file
{
	public $mysql = null;
	public $redis = null;
	public $local = null;
	public $file = "";
	public $data = array();

	public function __construct( $mysql=null, $redis=null )
	{
		$this->mysql = $mysql;
		$this->redis = $redis;
	}

	public function getMysql()
	{
		if ( $this->mysql === null ) {
			$this->mysql = new DB;
		}
		return $this->mysql;
	}

	public function getRedis()
	{
		if ( $this->redis === null ) {
			$this->redis = new RD;
		}
		return $this->redis;
	}

	public function operate( $file, $type, $data )
	{
		$this->file = $file;
		$this->data = $data;
		switch ( $type ) {
			// case 'add':
			// break;
			default://一勺烩
				if ( $data['mysql'] ) {
					$explode = isset($data['explode'])?$data['explode']:array();
					$mysql = $this->getMysql();
					$res = $mysql->getData($data['mysql']);
					if ( !$res ) $res = array();
					$list = array();
					foreach ( $res as $k => $v )
					{
						foreach ( $v as $kk => $vv )
						{
							if ( in_array($kk, $explode) ) {
								$v[$kk] = $vv ? explode(' ', $vv) : array();
							}
						}
						$list[$v['id']] = $v;
					}
					if ( $data['redis'] && $list ) {
						$redis = $this->getRedis();
						$res = $redis->hmset($data['redis'], $list);
					}
					$tmp = ROOT.'/include/_'.$file.'.php';
					$filename = ROOT.'/include/'.$file.'.php';
					$ret = file_write($tmp, '<?php $'.$file.' = '.var_export($list,true).';');
					$ret = shell_exec("mv $tmp $filename");
					return true;
				}
			break;
		}
	}
}
