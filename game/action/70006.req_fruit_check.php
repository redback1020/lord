<?php
/**
 * Created by PhpStorm.
 * User: huangxiufeng
 * Date: 16/9/2
 * Time: 下午2:34
 */

$cmd = 7;
$code = 6;

/**
 * @var $this gamer
 */

if ($user['roomId'] != 0 || !isset($user['roomId'])) {
    $roomId = $user['roomId'];
    $rooms = $this->model->rooms;
    if(isset($rooms[$roomId]) && $rooms[$roomId]["modelId"]==3)
    {
        $room = $rooms[$roomId];
        $game = $this->match->getGame($room['modelId'], $roomId);
        $state = $this->match->getState($room, $game, $user);
        slog(sprintf('STATE:%s USER=%s ROOM=%s GAME=%s', json_encode($state), json_encode($user), json_encode($room), json_encode($game)));
        $canCancel = $state == 3;
        
        $rst = [
            'code' => sprintf("%d000%d", $cmd, $code),
            'data' => ([
                'errno'     => 0,
                'error'     => '',
                'roomId'    => $roomId,
                'roomName'  => $room['name'],
                'canCancel' => $canCancel
        
            ])
        ];
        
        //当比赛场报名了定时赛,并且可以取消报名 => 不需要提示用户需要报名
        if ($state == 3 && $room['start'] != 0) {
            $rst['data']['roomId'] = 0;
            $rst['data']['roomName'] = $rst['data']['canCancel'] = "";
        }
    }else
    {
        $rst = [
            'code' => sprintf("%d000%d", $cmd, $code),
            'data' => ([
                'errno'     => 0,
                'error'     => '',
                'roomId'    => 0,
                'roomName'  => '',
                'canCancel' => true
            ])
        ];
        
    }
    
} else {
    $rst = [
        'code' => sprintf("%d000%d", $cmd, $code),
        'data' => ([
            'errno'     => 0,
            'error'     => '',
            'roomId'    => 0,
            'roomName'  => '',
            'canCancel' => true
        ])
    ];
}


sendToFd($fd, $cmd, $code, $rst);