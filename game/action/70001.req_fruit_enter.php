<?php
/**
 * Created by PhpStorm.
 * User: huangxiufeng
 * Date: 16/8/31
 * Time: 下午5:57
 */

/**
 * @var $this gamer
 */

$cmd = 7;
$code = 1;
$hall = $this->model->mysql->getLine("SELECT `first_day`, `credit`, `round`, `total_bet`, `intervene_round`, `curr_round_win`, `total_win`, `persist_round` FROM lord_game_fruit WHERE uid=" . $user['uid']);

//debug(json_encode($hall));


$gold = abs(intval($params['gold']));
$gold = intval($gold / 1000) * 1000;

if ($user['coins'] < $gold) {
    $rst = [
        'code' => sprintf("%d000%d", $cmd, $code),
        'data' => ([
            'errno' => 1,
            'error' => '你没有这么多乐豆!'
        ])
    ];
    sendToFd($fd, $cmd, $code, $rst);
}

setUser($user['uid'], [
    'coins' => $user['coins'] - $gold
]);

$this->fruitMachine->setUser($user);

$this->model->redis->hmset($this->fruitMachine->getKey(), $hall);


$this->fruitMachine->filling($gold);
