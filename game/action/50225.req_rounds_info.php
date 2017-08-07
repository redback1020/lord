<?php
//获得局数 胜局

$cmd = 5; $code = 226; $send = ["match"=>$user["play"],"win"=>$user["win"]];
$this->model->sendToFd($fd, $cmd, $code, $send);
