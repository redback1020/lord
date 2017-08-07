<?php
/**
 * Created by PhpStorm.
 * User: huangxiufeng
 * Date: 16/9/6
 * Time: 上午10:17
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


$gold = abs(intval($params['gold']));

/**
 * @var $machine DDZCowMachine
 */
$machine = $this->cowMachine;

$machine->enter($user['uid'], $fd, $gold);
