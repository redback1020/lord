<?php

function nextSeat( $seatId )
{
	if ( ! in_array($seatId, range(0, 2)) ) return 0;
	return --$seatId == -1 ? 2 : $seatId;
}

function num2UInt32Str( $num )
{
	$str = '';
	for ( $i = 4; $i > 0; $i-- ) {//32/8
		$str.= pack("C", $i <= 1 ? floor($num%(16*16)) : floor($num/pow(16*16,$i-1)));
	}
	return $str;
}

function UInt32Binary2Int ( $binArr )
{
	return hexdec(sprintf("%02s%02s%02s%02s", $binArr[0], $binArr[1], $binArr[2], $binArr[3]));
}

function utf8substr( $str, $start, $len )
{
	$res = "";
	$strlen = $start + $len;
	for ( $i = 0; $i < $strlen; $i++ )
	{
		if ( ord(substr($str, $i, 1)) > 127 ) {
			$res .= substr($str, $i, 3); $i+=2;
		} else {
			$res .= substr($str, $i, 1);
		}
	}
	return $res;
}

function dump( $data )
{
	echo "[".date("m-d H:i:s")."] ".(is_array($data) ? json_encode($data) : strval($data))."\n";
}

//post|get to url
function urlReq( $url, $data=null, $timeout=10, $agent=0, $cookie=null )
{
	if ( $agent && is_int($agent) ) {
		$user_agent = ini_get('user_agent'); ini_set('user_agent', 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; SV1; .NET CLR 2.0.50727;)');
	} elseif ( $agent && is_array($agent) ) {
		$user_agent = ini_get('user_agent'); ini_set('user_agent', $agent[array_rand($agent)]);
	} elseif ( is_string($agent) ) {
		$user_agent = ini_get('user_agent'); ini_set('user_agent', $agent);
	} else {
		$user_agent = false;
	}
	$context['http']['method'] = $data && is_array($data) ? 'POST' : 'GET';
	$context['http']['header'] = $data && is_array($data) ? "Content-Type: application/x-www-form-urlencoded; charset=utf-8" : "Content-Type: text/html; charset=utf-8";
	$context['http']['timeout'] = $timeout;
	if ( $context['http']['method'] == 'POST' )
	{
		if ( $cookie && is_string($cookie) ) {
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
	if ( $user_agent !== false ) ini_set('user_agent', $user_agent);
	return $res;
}

function str_is_path( $filename, $is_hard_path=1 )
{
	$tmpname = strtolower($filename);
	$tmparray = array('://',"\0");
	if ($is_hard_path) $tmparray[] = '..';
	if (str_replace($tmparray, '', $tmpname) != $tmpname) return false;
	return true;
}

function dir_mk( $dir, $mode=0777 )
{
	if ( is_dir($dir) || @mkdir($dir, $mode) ) return true;
	if ( ! dir_mk(dirname($dir), $mode) ) return false;
	return @mkdir($dir, $mode);
}

function file_write( $filename, $data, $method='rb+', $is_lock=1, $is_hard_path=1, $is_chmod=1 )
{
	if ( ! str_is_path($filename, $is_hard_path) ) return false;
	! is_dir(dirname($filename)) && dir_mk(dirname($filename));
	touch($filename);
	$handle = fopen($filename, $method);
	$is_lock && flock($handle, LOCK_EX);
	$is_writen = fwrite($handle, $data);
	$method == 'rb+' && ftruncate($handle, strlen($data));
	fclose($handle);
	$is_chmod && @chmod($filename, 0777);
	return $is_writen;
}

function dateid( $date='' )
{
	if ( ! $date || ! strtotime($date) ) return intval(date("Ymd"));
	return intval(str_replace('-', '', substr_replace($date, '', 10)));
}

function isSameDay($time1,$time2)
{
    if(is_string($time1)) $time1 = strtotime($time1);
    if(is_string($time2)) $time2 = strtotime($time2);
    if(date("Y-m-d",$time1) != date("Y-m-d",$time2))
    {
        return true;    
    }
    return false;
}
