<?php

$ud = $user['uid'];
$md = $user['modelId'];
$rd = $user['roomId'];
$td = $user['tableId'];
$sd = $user['seatId'];

//用户只有在牌桌时或进入过队列时才会向服务端发送此协议

if ( ! isset($this->rooms[$rd]) ) {
	debug("退房用户失效[$fd|$ud|$td|$sd] roomId=$rd");
	goto end;
}
if ( ! $td && isset($user['gameStart']) && $user['gameStart'] > 0 ) {
	if ( $this->model->ddaJoinTrio($ud, $rd) ) {
		debug("退房退队成功[$fd|$ud|$td|$sd] roomId=$rd");
		setUser($ud, array('gameStart'=>0,'roomId'=>0));
	} else {
		debug("退房退队失败[$fd|$ud|$td|$sd] roomId=$rd");
	}
	goto end;
}
if ( ! $td ) {
	setUser($ud, array('roomId'=>0));
	goto end;
}
//获取牌桌
$table = $this->model->getTableInfo($td);
if ( !$table ) {
	debug("退房牌桌失效[$fd|$ud|$td|$sd]");
}
//检测如果在游戏中，执行退房托管，并return
elseif ( in_array( $table['state'], array(3,4,5,6) ) ) {
	debug("退房托管开始[$fd|$ud|$td|$sd]");
	$table = $this->USER_ENTRUST($fd, $table, $sd, 3 );//3退房托管
}
//检测如果在比赛中，执行退房托管，并return
elseif ( $table['gameId'] ) {
	debug("退房赛桌托管[$fd|$ud|$td|$sd]");
	$table = $this->USER_ENTRUST($fd, $table, $sd, 3 );//3退房托管
}
//正在等待开始或其他，执行散桌
elseif ( $table['state'] != 7 ) {
	debug("退房散桌开始[$fd|$ud|$td|$sd]");
	$res = $this->TABLE_BREAK($table, 1);
}


end:{
	$this->model->record->action($accode, $rd, $td, $ud, $user);
}
