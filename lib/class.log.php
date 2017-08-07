<?php
/**
* PHP logs 类
*/
class Logs{

    private $LogFile;
    private $logLevel;

    function __construct($file, $level=75){
        $this->logLevel = $level;
        if(! strlen($file)) {
            throw new Exception('can\'t set file to empty');
        }
        $this->LogFile[$file] = @fopen($file,'a+');
        if(!is_resource($this->LogFile[$file])){
            throw new Exception('invalid file Stream');
        }
    }

    public static function getInstance($file, $level=75){

        static $obj;
        if(!isset($obj[$file])){
            $obj[$file] = new Logs($file, $level);
        }
        return $obj[$file];
    }

	public function LogContent($file,$content)
	{
		fwrite($this->LogFile[$file], $content."\n");
	}

    public function LogMessage($file, $msg, $logLevel=0, $module = null){
        $time = date("m-d H:i:s");
        $msg = str_replace(array("\n","\t"),array("",""),$msg);
        $level = levelToString($logLevel?$logLevel:$this->logLevel);
        $module && $module = str_replace(array("\n","\t"),array("",""),$module);
        $line = "$time $level $msg $module\n";
        fwrite($this->LogFile[$file],$line);
    }
}
//$logIns = LOG::getInstance();
//$logIns->logMessage("test",LOG::INFO,'myTest');

function debug($data, $roomid=0, $tableid=0){
	if(!ISDEBUG){return false;}
	$file = DEBUG_LOG;
	if ( $roomid !==0 && $tableid !==0 ) {
		$file = str_replace(".log","-".$roomid."-".$tableid.".log",$file);
	}
	$log = Logs::getInstance($file, 100);
	$log->logMessage($file, $data, 100);
	return true;
}

function glog($data, $roomid=0, $tableid=0){
	$file = GAME_LOG;
	if ( $roomid !==0 && $tableid !==0 ) {
		$file = str_replace(".log","-".$roomid."-".$tableid.".log",$file);
	}
	$log = Logs::getInstance($file, 75);
	$log->logMessage($file, $data, 75);
	return true;
}

function gerr($data, $roomid=0, $tableid=0){
	$file = GAME_ERR;
	if ( $roomid !==0 && $tableid !==0 ) {
		$file = str_replace(".log","-".$roomid."-".$tableid.".log",$file);
	}
	$log = Logs::getInstance($file, 10);
	$log->logMessage($file, $data, 10);
	return false;
}

function slog($data, $roomid=0, $tableid=0){
	$file = SERVER_LOG;
	if ( $roomid !==0 && $tableid !==0 ) {
		$file = str_replace(".log","-".$roomid."-".$tableid.".log",$file);
	}
	$log = Logs::getInstance($file, 75);
	$log->logMessage($file, $data, 75);
	return true;
}

function serr($data, $roomid=0, $tableid=0){
	$file = SERVER_ERR;
	if ( $roomid !==0 && $tableid !==0 ) {
		$file = str_replace(".log","-".$roomid."-".$tableid.".log",$file);
	}
	$log = Logs::getInstance($file, 10);
	$log->logMessage($file, $data, 10);
	return false;
}

function trend($start,$end)
{
	$file = ROOT."/log/trend.csv";
	$content = round($end*1000).','.round(($end-$start)*1000);
	$log = Logs::getInstance($file);
	$log->logContent($file, $content);
	return true;
}

function levelToString($logLevel){
	 $ret = '[unknow]';
	 switch ($logLevel){
		case 100:
			 $ret = '[DEBUG]';
			 break;
		case 75:
			 $ret = '[INFOR]';
			 break;
		case 50:
			 $ret = '[NOTICE]';
			 break;
		case 25:
			 $ret = '[WARNING]';
			 break;
		case 10:
			 $ret = '[ERROR]';
			 break;
		case 5:
			 $ret = '[CRITICAL]';
			 break;
	 }
	 return $ret;
}

