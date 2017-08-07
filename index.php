<?php

define('ISTESTS',1);

require './game/class.card.php';

$a = Card::newCardPool(1);

print_r($a);
exit;
function num2UInt32Str( $num )
{
    $str = '';
    for ( $i = 4; $i > 0; $i-- ) {//32/8
        $str.= pack("C", $i <= 1 ? floor($num%(16*16)) : floor($num/pow(16*16,$i-1)));
    }
    return $str;
}
function UInt32Binary2Int ( $binArr )
{
    return hexdec(sprintf("%02s%02s%02s%02s", $binArr[0], $binArr[1], $binArr[2], $binArr[3]));
}

ini_set( 'display_errors', 'On' );
error_reporting(E_ALL);
$client = new swoole_client(SWOOLE_SOCK_TCP,SWOOLE_SOCK_ASYNC);
$client->on("Connect", function(swoole_client $cli) {
    login($cli );

    intoroom($cli);

   getready($cli);
});



$client->on("Receive", function(swoole_client $cli, $data){
    echo "接收到";

    //$cmd = UInt32Binary2Int(array_values(unpack("C*", substr($data, 4, 4))));//cmd
    //$cme = UInt32Binary2Int(array_values(unpack("C*", substr($data, 8, 4))));//扩展
   // $cmf = UInt32Binary2Int(array_values(unpack("C*", substr($data, 12, 4))));//扩展

    $data_ = substr($data, 16);
    $data = json_decode($data_, 1);
  print_r($data);
    // $redis = new Redis();
    // $redis->connect("127.0.0.1","6379");
    // $redis->publish('tv1',$data ); //发布
    //$cli->send(str_repeat('A', 100)."\n");
    //sleep(1);

});
$client->on("Error", function(swoole_client $cli){
    echo "error\n";
});
$client->on("Close", function(swoole_client $cli){
    echo "Connection close\n";
});
$fp = $client->connect('127.0.0.1', 9000);



function send($cli,$cmd,$array){
    $msg = json_encode($array);
    $len = num2UInt32Str(strlen($msg) + 12);
    $cmd = num2UInt32Str($cmd);
    $cme = num2UInt32Str(0);
    $cmf = num2UInt32Str(0);
    $pack = $len . $cmd . $cme . $cmf . $msg;
    $cli->send($pack);
}

function regin( $cli ){
    send($cli,0, [
        't'=>10006,
        'type' => 'guest',
        'd'=> '123456',     //设备 原始设备号md5( $uuid . $sign )
        'e'=>'abcdefg',     //设备 扩展设备号，用做串号校验
        'sdkid'=>'device', //sdkid
        'v' => '2.0',   //版本
        'c'=>'self',//渠道
        'u'=>'redack'   //设备 原始设备号
    ]);
}


function login($cli ){

    send($cli,0, [
        't'=>10000,
        'd'=> '123456',     //设备 原始设备号md5( $uuid . $sign )
        'e'=>'abcdefg',     //设备 扩展设备号，用做串号校验
        'f'=>'device', //设备 支付设备号，SDKUID不会串号
        'v' => '2.0',   //版本
        'c'=>'self',//渠道
        'u'=>'redack',   //设备 原始设备号
        'wn' => '',//第三方昵称
        'robot' => 0,
        'p'=> '331255'//密码
    ]);
}

function intoroom($cli){
    send($cli,5, [
        't'=>0,
        'modelId'=> 0,
        'roomId'=>1003,
        'isContinue'=>0, //是否重返牌桌

    ]);

}

function getready($cli){
    send($cli,5, [
        't'=>1,
        'showcard'=> 0,
    ]);

}