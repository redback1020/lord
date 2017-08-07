<?php

$global['adm_username']["letv"] = "letv";
$global['adm_username']["rabbitdesktop"] = "rabbitdesktop";
$global['adm_username']["ijia"] = "ijia";
$global['adm_username']["hisense"] = "hisense";
$global['adm_username']["xiaomi"] = "xiaomi";
$global['adm_username']["7po"] = "7po";
$global['adm_username']["lenovo"] = "lenovo";
$global['adm_username']["17fox"] = "17fox";
$global['adm_username']["wuzhijian"] = "all";
$global['adm_username']["tanyiming"] = "all";
$global['adm_username']["fengjianguang"] = "all";
$global['adm_username']["admin0"] = "all";


$adm_username = isset($global['adm_username'][$_COOKIE['adm_username']])?$global['adm_username'][$_COOKIE['adm_username']]:"";
