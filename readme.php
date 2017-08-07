<?php

//可用常量

ROOT	游戏代码根目录
HOST	本机的内网IP，用作本应用服务的标记
PORT	本服务的侦听端口，用作本应用服务的标记
HOSTID	把HOST和PROT通过_(英文下划线)连接，用作本应用服务的标记
ISLOCAL	0线上运行，1测试运行
ISDEBUG	0关闭调试，1开启调试
ISROBOT	0无机器人，1有机器人
ISPRESS	0常规逻辑，1压测逻辑

//可用函数

//向某个http-url发起POST或GET请求
//url 			str 		目标http地址
//data 			mix 		null时为get｜arr时为post的data
//timeout 	int 		超时上限
//agent 		mix 		模拟agent标示
//cookie 		mix 		模拟cookie标示
//return 		str 		请求的结果
urlReq( $url, $data=null, $timeout=10, $agent=0, $cookie=null )

//用指定长度，截取中英文混合型的字符串
//返回结果
utf8substr( $str, $start, $len )

//调试变量
//返回void，把变量串行化后，打印到log/sweety.log中
dump($mix)

//打印游戏调试日志
//返回void，把字符串打印到log/game.log
debug($string)

//打印游戏普通日志
//返回void，把字符串打印到log/game.log
glog($string)

//打印游戏错误日志
//返回void，把字符串打印到log/game.log
gerr($string)

//打印系统错误日志
//返回void，把字符串打印到log/game.log
slog($string)

//打印系统错误日志
//返回void，把字符串打印到log/game.log
serr($string)

//向本服的所有在线用户发送广播
//	data 	array 	要发送的数据
//	return 	true|false
srvHorn($data)

//直接向本服用户发送信息(不推荐使用，跨服或不确定是否跨服时请使用$this->model->sendToFd(...)来替代)
//	fd 		string 	用户连接, 比如：10.10.11.212_9000_45678
//	cmd 	int 	协议族,
//	code 	int 	协议号,
//	data 	array 	要发送的数据
//	return 	true|false
srvSend($fd, $cmd, $code, $data)

//直接断开本服用户(不推荐使用，跨服或不确定是否跨服时请使用$this->model->closeToFd(...)来替代)
//	fd 		string 	用户连接, 比如：10.10.11.212_9000_45678
//	return 	true|false
srvClose($fd)

//可用全局变量
$server	//系统对象
	->
