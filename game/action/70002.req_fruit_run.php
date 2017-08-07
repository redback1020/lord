<?php
/**
 * Created by PhpStorm.
 * User: huangxiufeng
 * Date: 16/8/31
 * Time: 下午7:03
 */

/**
 * @var $this gamer
 */

$cmd = 7;
$code = 2;


$bets = [
    0 => isset($params['bets'][0]) ? intval(abs($params['bets'][0])) : 0,
    1 => isset($params['bets'][1]) ? intval(abs($params['bets'][1])) : 0,
    2 => isset($params['bets'][2]) ? intval(abs($params['bets'][2])) : 0,
    3 => isset($params['bets'][3]) ? intval(abs($params['bets'][3])) : 0,
    4 => isset($params['bets'][4]) ? intval(abs($params['bets'][4])) : 0,
    5 => isset($params['bets'][5]) ? intval(abs($params['bets'][5])) : 0,
    6 => isset($params['bets'][6]) ? intval(abs($params['bets'][6])) : 0,
    7 => isset($params['bets'][7]) ? intval(abs($params['bets'][7])) : 0,
];

$sum = array_sum($bets);

if ($sum <= 0) {
    $rst = [
        'code' => sprintf("%d000%d", $cmd, $code),
        'data' => ([
            'errno' => 1,
            'error' => '请下注!'
        ])
    ];
    sendToFd($fd, $cmd, $code, $rst);
}


$this->fruitMachine->setUser($user);


if ($this->fruitMachine->getCurrRoundWin() > 0) {


   // $this->fruitMachine->printf(json_encode($Utask));

//    $Utask = $this->model->getUserTask($user['uid']);
//    if (!isset($Utesk["teskdone_9"]) || !$Utesk["teskdone_9"]) {
//        $rst = [
//            'code' => sprintf("70334", $cmd, $code),
//            'data' => ([
//                'errno'  => 0,
//                'error'  => '',
//                'isPush' => 1, 'id' => 9, 'price' => 6, 'fileId' => [901, 900]
//            ])
//        ];
//        sendToFd($fd, $cmd, $code, $rst);
//    } else {
//
//    }


    $rst = [
        'code' => sprintf("%d000%d", $cmd, $code),
        'data' => ([
            'errno' => 3,
            'error' => '请先合分再下注!'
        ])
    ];
    sendToFd($fd, $cmd, $code, $rst);
}

if ($this->fruitMachine->getCredit() < $sum) {
    $rst = [
        'code' => sprintf("%d000%d", $cmd, $code),
        'data' => ([
            'errno' => 3,
            'error' => '你没有这么多分!'
        ])
    ];
    sendToFd($fd, $cmd, $code, $rst);
}


$this->fruitMachine->setBetCell($bets);
$this->fruitMachine->run();