<?php
/**
 * Created by PhpStorm.
 * User: huangxiufeng
 * Date: 16/9/6
 * Time: 上午10:20
 */

$code = 8; $code=6;

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

$candidates = $machine->getBankerCandidates();

$rst = [
    'code' => $cmd * 10000 + $code,
    'data' => array_merge([
        'errno' => 0,
        'error' => '',
        'candidates'=>$candidates
    ])
];

sendToFd($fd, $cmd, $code, $rst);