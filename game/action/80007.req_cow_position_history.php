<?php
/**
 * Created by PhpStorm.
 * User: huangxiufeng
 * Date: 16/9/6
 * Time: 上午10:22
 */

$cmd = 8;
$code = 7;

/**
 * @var $fd string
 */

/**
 * @var $this $gamer
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
$machine = $this->cowMachine;
$histories = $machine->getPositionHistory();
rsort($histories);
$rst = [
    'code' => $cmd * 10000 + $code,
    'data' => array_merge([
        'errno'     => 0,
        'error'     => '',
        'histories' => $histories
    ])
];

sendToFd($fd, $cmd, $code, $rst);