<?php
/**
 * Created by PhpStorm.
 * User: huangxiufeng
 * Date: 16/9/1
 * Time: 下午8:01
 */

$code = 5;
$cmd = 7;

/**
 * @var $this gamer
 */

$gold = $user['coins'];
//$gold = abs(intval($params['gold']));
$gold = intval($gold / 1000) * 1000;


if ($user['coins'] < $gold) {
    $rst = [
        'code' => sprintf("%d000%d", $cmd, $code),
        'data' => [
            'errno' => 1,
            'error' => '你没有这么多乐豆!'
        ]
    ];
    sendToFd($fd, $cmd, $code, $rst);
}

setUser($user['uid'], [
    'coins' => $user['coins'] - $gold
]);

$this->fruitMachine->setUser($user);
$this->fruitMachine->changeCredit(intval($gold * $this->fruitMachine->fill_ratio));
$rst = [
    'code' => sprintf("%d000%d", $cmd, $code),
    'data' => [
        'errno' => 0,
        'error' => '',
        'gold'  => $gold,
        'bet'   => $this->fruitMachine->getCredit()
    ]
];
sendToFd($fd, $cmd, $code, $rst);