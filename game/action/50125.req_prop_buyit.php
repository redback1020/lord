<?php

//购买道具

$ud = $user['uid'];
$md = $user['modelId'];
$rd = $user['roomId'];
$td = $user['tableId'];
$sd = $user['seatId'];

//校验参数
$id = isset($params['propId']) ? intval($params['propId']) : 0;
if ( !$id ) {
	$res = closeToFd($fd, "购道参数无效 params=".json_encode($params));
	goto end;
}
//执行购买
$errno = $this->model->buyGoods($user, $id);
switch ( $errno )
{
	case 1:
		$error = "购买失败，请稍候重试。";//不可预知失败
		break;
	case 2:
		$error = "购买失败，此道具已经不存在了哦。";//不存在
		break;
	case 3:
		$error = "此道具只能从游戏中获得。";//不可购买
		break;
	case 4:
		$error = "您购买过这个啦，不用重复购买哦。";//已经买过
		break;
	case 5:
		$error = "您的金额不足，需要充值。";//乐豆或乐币不足
		break;
	case 6:
		$error = "此道具暂时不可购买。";//已下架
		break;
	case 7:
		$error = "此道具暂时不可购买。";//已售罄
		break;
	default:
		$result = $errno;
		$errno = 0;
		$error = "购买成功。";
		break;
}
debug("用户购买道具 F=$fd U=$ud T=$td S=$sd P=$id errno=$errno");
//发送结果
$cmd = 5; $code = 126;
$data = array(
	"errno" => $errno,
	"error" => $error,
	"gold" => $errno ? $user['gold'] : $result['gold'],
	"coins" => $errno ? $user['coins'] : $result['coins'],
	// "coupon" => $errno ? $user['coupon'] : $result['coupon'],
	// "gold_" => $errno ? 0 : $result['gold_'],
	// "coins_" => $errno ? 0 : $result['coins_'],
	// "coupon_" => $errno ? 0 : $result['coupon_'],
	"propDress" => $errno ? $user['propDress'] : $result['propDress'],
	"propItems" => $errno ? $user['propItems'] : $result['propItems'],
);
$res = sendToFd($fd, $cmd, $code, $data);
//写入记录
if ( !$errno ) {
	$type = strtolower($action);
	$date = date("Y-m-d H:i:s");
	$dateid = date("Ymd");
	$time = time();
	$sql = "INSERT INTO lord_user_cost (`dateid`,`type`,`channel`,`uid`,`gold`,`coins`,`coupon`,`propId`,`ip`,`date`,`time`) VALUES ";
	$sql.= "($dateid,'$type','".$user['channel']."',$ud,".($result['gold_'] * -1 + 0).",".($result['coins_'] * -1 + 0).",".($result['coupon_'] * -1 + 0).",$id,'','$date',$time)";
	bobSql($sql);
}


end:{
	$this->model->record->action($accode, $rd, $td, $ud, $user);
}
