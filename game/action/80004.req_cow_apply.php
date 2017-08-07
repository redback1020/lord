<?php
/**
 * Created by PhpStorm.
 * User: huangxiufeng
 * Date: 16/9/6
 * Time: 上午10:18
 */

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

$cmd = 8;
$code = 4;


/**
 * @var $machine DDZCowMachine
 */
$num = isset($params['num']) ? intval($params['num']) : 0;

$machine = $this->cowMachine;
$machine->applyForBanker($user['uid'], $user['nick'], $user['sex'], $num, $fd);


