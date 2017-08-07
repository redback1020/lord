<?php
/**
 * Created by PhpStorm.
 * User: huangxiufeng
 * Date: 16/9/21
 * Time: 下午6:29
 */


/**
 * @var $fd string
 */

/**
 * @var $this gamer
 */

/**
 * @var $params array
 */

/**
 * @var $user array
 */


/**
 * @var $machine DDZCowMachine
 */

//百人牛牛断线重连
$bet = $this->cowMachine->retrace($user['uid']);
if ($bet != -1) sendToFd($fd, 4, 110, ['coins' => $bet]);


if ($this->model->redis->redis->get('cow:banker:id') == $user['uid']) {

    $this->model->redis->redis->hSet("cow:banker:info", "new_fd", $fd);

    //{"code":80109,"data":{"errno":0,"error":"","is_banker":false}}
    sendToFd($fd, 8, 109, [
        "code" => 80109,
        "data" => [
            "errno"     => 0,
            "error"     => "",
            'is_banker' => true
        ]

    ]);

}
