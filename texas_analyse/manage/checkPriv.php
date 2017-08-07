<?php

ini_set('session.gc_maxlifetime','43200');

session_start();

if ( !isset($_SESSION['last_access']) || time() - $_SESSION['last_access'] > 43200 ) {
	echo '<script>alert("对不起, 登陆已超时,请重新登陆!");location.href="/manage/login.php"</script>';
	exit;
}
$_SESSION['last_access'] = time();

$self = $_SERVER['PHP_SELF'];
$array = explode("/",$self);
$dir = $array[1];
$self = $array[count($array)-1];
$menu['TV_mobile2']['online.php'] = 'a11';
$menu['TV_mobile2']['userinfo.php'] = 'a12';
$menu['TV_mobile2']['user.php'] = 'a13';
$menu['TV_mobile2']['vipList.php'] = 'a14';
$menu['TV_mobile2']['announce.php'] = 'a21';
$menu['TV_mobile2']['sendmsg.php'] = 'a22';
$menu['TV_mobile2']['enter.php'] = 'a31';
$menu['TV_mobile2']['weixin_user.php'] = 'a41';
$menu['TV_mobile2']['weixin_nologin.php'] = 'a42';
$menu['TV_mobile2']['truePlayer.php'] = 'a51';
$menu['TV_mobile2']['bangdan.php'] = 'a52';
$menu['TV_mobile2']['history.php'] = 'a53';
$menu['TV_mobile2']['addData.php'] = 'a54';
$menu['TV_mobile2']['charge.php'] = 'a61';
$menu['TV_mobile2']['pay.php'] = 'a62';
$menu['TV_mobile2']['gmsum.php'] = 'a71';
$menu['TV_mobile2']['gmsum_channel.php'] = 'a72,b13';
$menu['TV_mobile2']['viewAllAmount.php'] = 'a73';
$menu['TV_mobile2']['sys_log.php'] = 'a81';
$menu['TV_mobile2']['channel_userinfo.php'] = 'b11';
$menu['TV_mobile2']['channelLog.php'] = 'b12';

$menu['lord2']['userOnlineList.php'] = "c11";
$menu['lord2']['userInfo.php'] = "c12";
$menu['lord2']['sendmsg.php'] = "c21";
$menu['lord2']['charge.php'] = "c31";
$menu['lord2']['pay.php'] = "c32";
$menu['lord2']['gmsum.php'] = "c41";
$menu['lord2']['gmsum_channel.php'] = "c42,d13";
$menu['lord2']['viewAllAmount.php'] = "c43";
$menu['lord2']['sys_log.php'] = "c51";
$menu['lord2']['channel_userinfo.php'] = 'd11';
$menu['lord2']['channelLog.php'] = 'd12';

$priv = $menu[$dir][$self];$flag = 0;
//$_SESSION['access_priv'] = "a";

$data_priv = $_SESSION['data_priv'];
if(substr_count($data_priv,",")>0){
	$arr = explode(",",$data_priv);
	$data_priv = "'".implode("','",$arr)."'";
}
if($data_priv == ""){$data_priv = "all";}
if($priv != ""){
	$array2 = explode(",",$_SESSION['access_priv']);
	foreach($array2 as $val){
		if($val!=''&&substr_count($priv,$val)>0){
			$flag = 1;break;
		}
	}
	if($flag == 0){
		echo '<script>alert("对不起,暂无权限,请联系管理员!");</script>';
		exit;
	}
}


$top = "a";
$mid['a'] = "a1";
$end['a1'] = "a11,a12,a13,a14";
$str = "online.php:a11|userinfo.php:a12|user.php:a13|vipList.php:a14|";
$lang['a'] = "有乐德州";
$lang['b'] = "有乐德州(渠道)";
$lang['c'] = "有乐斗地主";
$lang['d'] = "有乐地主(渠道)";
$lang['e'] = "有乐地主-测试";
$lang['z'] = "后台权限管理";
$lang['y'] = "个人信息设置";

/*$lang['a'] = "游戏后台管理";
$lang['a1'] = "基础工具";
$lang['a11'] = "在线列表";
$lang['a12'] = "用户的详细信息";
$lang['a13'] = "查询用户的uid";
$lang['a14'] = "VIP用户列表";*/
