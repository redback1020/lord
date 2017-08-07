<?php
//重连接

//用户信息
$ud = $user['uid'];
$rd = $user['roomId'];
$td = $user['tableId'];
//$sd = $user['seatId'];

//校验房间
if ( ! isset($this->rooms[$rd]) || ! $td ) {
    debug("出牌用户失效 F=$fd U=$ud R=$rd T=$td roomId=$rd");
    goto end;
}
//获取牌桌
$table = $this->model->getTableInfo($td);
if ( !$table ) {
    debug("牌桌不存在 F=$fd U=$ud R=$rd T=$td params=".json_encode($params));
    goto end;
}

$data = array();
for($sd=0;$sd<3;$sd++)
{
    $data["seat{$sd}cards"] = $table["seat{$sd}cards"];
    $data["seat{$sd}show"] = $table["seat{$sd}show"];
    $data["seat{$sd}buff"] = $table["seat{$sd}buff"];
    $data["seat{$sd}info"] = $table["seat{$sd}info"];
}
$data["lastCards"] = $table["lastCards"];
$data["lastCall"] = $table["lastCall"];
$data["noteCards"] = $table["noteCards"];

$cmd = 5;
$code = 330;
$res = sendToFd($fd, $cmd, $code, $data);
end:{
    
}

?>