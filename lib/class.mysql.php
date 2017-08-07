<?php

//PHP mysqli class

class DB
{
  private $errno = 0;
  private $error = '';
  private $host = MY_HOST;
  private $host2 = '10.10.212.234';
  private $port = MY_PORT;
  private $username = MY_USER;
  private $password = MY_PASS;
  private $dbname = MY_BASE;
  private $charset = MY_CHAR;
  public $db = false;
  public $db2 = false;
  public $iskeep = false;

  function __construct($iskeep=false,$throw=0)
  {
      $this->iskeep = $iskeep;
      try {
          $this->connect($throw);
      } catch (Exception $e) {
          serr("[MYSQLD][db-".__LINE__."] MYSQL construct failed.");
          if ( $throw ) throw new Exception("MYSQL construct failed.");
      }
  }

  private function connect($throw=0)
  {
      if (!$this->db || !mysqli_ping($this->db)) {
          if ($this->db) {
              @mysqli_close($this->db);
          }
          for ($i=0; $i < 2; $i++) {
              $db = mysqli_connect(($this->iskeep?"p:":"").$this->host, $this->username, $this->password, $this->dbname,$this->port);
              if(!$db){
                  $this->db = false;
                  $this->errno = mysqli_connect_errno();
                  $this->error = mysqli_connect_error();
              }
              else {
                  $this->db = $db;
                  $this->errno = 0;
                  $this->error = '';
                  mysqli_set_charset($this->db, $this->charset);
                  return $this->db;
              }
          }
          serr("[MYSQLD][db-".__LINE__."] MySQL reconnect failed ".$this->errno." , ".$this->error);
          if ( $throw ) throw new Exception("MYSQL reconnect failed.");
          return false;
      }
      return $this->db;
  }


  /**
   * @param string $sql
   * @return query_result|false
   */
  public function runSql($sql,$throw=0)
  {
      if ( !$this->db ) {
          try {
              $this->db = $this->connect($throw);
          } catch (Exception $e) {
              if ( $throw ) throw new Exception("MYSQL reconnect failed. $sql");
              serr("[MYSQLD] MySQL reconnect failed. $sql");
              return false;
          }
      }
      $res = false;
      for ( $i = 0; $i < 2; $i++ )
      {
          $res = mysqli_query($this->db, $sql);
          if ( false===$res) {
              $this->errno = $errno = mysqli_errno($this->db);
              $this->error = $error = mysqli_error($this->db);
              if ($errno ==2006 || $error == 2013) {
                  try {
                      $this->db = $this->connect($throw);
                      if ( false === $this->db ) {
                          serr("[MYSQLD] MySQL reconnect false. $sql");
                      } else {
                          slog("[MYSQLD] MySQL reconnect done . $sql");
                      }
                  } catch (Exception $e) {
                      serr("[MYSQLD] MySQL reconnect failed. $sql");
                      if ( $throw ) throw new Exception("MYSQL reconnect failed. $sql");
                      return false;
                  }
              } else {
                  serr("[MYSQLD][db-".__LINE__."] MySQL query failed ".$this->errno." , ".$this->error." , ".$sql);
                  if ( $throw ) throw new Exception("MYSQL query failed.");
                  return false;
              }
          } else {
              return $res;
          }
      }
      return false;
  }


  //处理多条语句
  //sqls        str     以;隔开的1个或多个sql
  //isthrow     int     是否抛出异常 0否1是
  //return      bool    false|true
  public function runSqls( $query, $throw=0 )
  {
    if ( !$query ) return serr("[MYSQLD][db-".__LINE__."] MySQL query failed 0, , ".$query);
    if (!$this->db) {
        try {
            $this->db = $this->connect($throw);
        } catch (Exception $e) {
            if ( $throw ) throw new Exception("MYSQL reconnect failed.");
            return false;
        }
    }
    $res = false;
    for ($i = 0; $i < 2; $i++)
    {
      $res = mysqli_multi_query($this->db, $query);
      if ( false === $res ) {
        $this->errno = $errno = mysqli_errno($this->db);
        $this->error = $error = mysqli_error($this->db);
        if ($errno ==2006 || $error == 2013) {
            try {
                $this->db = $this->connect($throw);
            } catch (Exception $e) {
                if ( $throw ) throw new Exception("MYSQL reconnect failed.");
                return false;
            }
        } else {
            serr("[MYSQLD][db-".__LINE__."] MySQL query failed ".$this->errno." , ".$this->error." , ".$query);
            if ( $throw ) throw new Exception("MYSQL query failed.");
            return false;
        }
      } else {
        //   if ( strpos($query, ';') ) echo $query."\n";
        do {
          if( $result = mysqli_store_result($this->db) ) {
             while( $row = mysqli_fetch_row($this->db, $result) ) {
                //  echo sprintf("mutiple row[0]=%s------------------------------\n",$row[0]);
             }
             mysqli_free_result($this->db, $result);
          }
          if( mysqli_more_results($this->db) ) {
            // echo("mutiple has more results-------------------------------\n");
          } else {
            // echo "mutiple not more results-------------------------------\n";
          }
        } while (mysqli_more_results($this->db) && mysqli_next_result($this->db));
        return true;
      }
    }
    return false;
  }

	//获取受影响的行数
	//return		mix		false|int
	public function affectedRows( $throw=0 )
	{
		if ( ! $this->db ) {
			try {
				$this->db = $this->connect($throw);
			} catch ( Exception $e ) {
				if ( $throw ) throw new Exception("MYSQL reconnect failed.");
				return false;
			}
		}
		return mysqli_affected_rows($this->db);
	}

   //运行Sql,以多维数组方式返回结果集
   //sql 		string	query
   //return		array	array()|array(array()[,...])
	public function getData( $sql, $throw=0 )
	{
		try {
			$res = $this->runSql($sql, $throw, 1);
			if ( $res === false ) return array();
			$data = array();
			while( $arr = mysqli_fetch_array($res, MYSQLI_ASSOC) ) $data[] = $arr;
			mysqli_free_result($res);
			return $data;
		} catch ( Exception $e ) {
			if ( $throw ) throw new Exception("MYSQL ERROR SQL=$sql .");
			return array();
		}
	}

	//运行Sql,以数组方式返回结果集第一条记录
	//sql 		string	query
	//return	array	array()|array(...)
	public function getLine( $sql, $throw=0 )
	{
		try {
			$res = $this->runSql($sql, $throw, 1);
			if ( $res === false ) return array();
			$line = mysqli_fetch_array($res, MYSQLI_ASSOC);
			mysqli_free_result($res);
			return $line ? $line : array();
		} catch ( Exception $e ) {
			if ( $throw ) throw new Exception("MYSQL ERROR SQL=$sql .");
			return array();
		}
	}

	//运行Sql,返回结果集第一条记录的第一个字段值
	//sql 		string	query
	//return	mix		false|value
	public function getVar( $sql, $throw=0 )
	{
		try {
			$res = $this->runSql($sql, $throw, 1);
			if ( $res === false ) return array();
			$line = mysqli_fetch_array($res, MYSQLI_ASSOC);
			mysqli_free_result($res);
			return $line ? reset($line) : false;
		} catch ( Exception $e ) {
			if ( $throw ) throw new Exception("MYSQL ERROR SQL=$sql .");
			return array();
		}
	}

	//获取新增的id
	//return 	int		false|last_id
	public function lastId( $throw=0 )
	{
		if ( ! $this->db ) {
			try {
				$this->db = $this->connect($throw);
			} catch (Exception $e) {
				if ( $throw ) throw new Exception("MYSQL reconnect failed.");
				return false;
			}
		}
		return mysqli_insert_id($this->db);
	}

	//关闭数据库连接
	//return		bool
	public function closeDb()
	{
		$this->db2 && @mysqli_close($this->db2);
		return @mysqli_close($this->db);
	}

	public function close()
	{
		return $this->closeDb();
	}

	public function version( $throw=0 )
	{
		if ( ! $this->db ) {
			try {
				$this->db = $this->connect($throw);
			} catch ( Exception $e ) {
				if ( $throw ) throw new Exception("MYSQL reconnect failed.");
				return false;
			}
		}
		return mysqli_get_client_version($this->db);
	}

	public function errno()
	{
		return $this->errno;
	}

	public function errmsg()
	{
		return $this->error;
	}

}
