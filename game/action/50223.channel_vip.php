<?php
/**
 * Created by PhpStorm.
 * User: huangxiufeng
 * Date: 16/9/8
 * Time: 上午10:23
 */

$cmd = 5;
$code = 223;

$vip_channel = isset($params['vip_channel']) ? strval($params['vip_channel']) : '';
$vip_level = isset($params['vip_level']) ? intval($params['vip_level']) : 0;


setUser($user['uid'], [
    'vip_channel' => $vip_channel,
    'vip_level'   => $vip_level
]);

$rst = [
    'code' => sprintf("%d000%d", $cmd, $code),
    'data' => [
        'errno'       => 0,
        'error'       => '',
        'vip_channel' => $vip_channel,
        'vip_level'   => $vip_level
    ]
];
sendToFd($fd, $cmd, $code, $rst);