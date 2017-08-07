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


$lockId = 'WAGER' . $user['uid'];
$res = setLock($lockId);
if ($res) {


    /**
     * @var $machine DDZCowMachine
     */
    $machine = $this->cowMachine;
    $num = isset($params['num']) ? intval($params['num']) : 0;
    $allIn = isset($params['allIn']) ? intval($params['allIn']) : 0;

    $position = isset($params['position']) && in_array($params['position'], [1, 2, 3, 4]) ? $params['position'] : 1;


    $machine->wager($user['uid'], $position, $num,  $fd, $allIn);
    debug("百人牛牛下注params:".json_encode($params));
    delLock($lockId);
}else{
    gerr("百人牛牛下注 LOCKON lock=$lock");
}

end:{
}
