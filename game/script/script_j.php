<?php

// CBU 当前用户基数
// DAU 当前活跃用户
// DNU 每日新增
// PU2 每日新增的次日留存率
// DIE 自然总数流失率(相对于基数)
// DAY 天数
// 问题，假设CBU＝4000000，DAU＝100000，DNU＝1000，PU2=0.2，在不计流失率的情况下，算出一个月(30天)后的DAU
//
// y = f(CBU,DAU,DNU,PU2,DIE,)


$CBU = 4000000;
$DAU = 100000;
$DNU = 40000;
$PU2 = 0.2;
$DIE = 0;//
$DIE = 0.001;//0
$DAY = 30;


function fnext( &$CBU, &$DAU, &$DNU, $PU2, $DIE )
{
    $CBU += $DNU;//第二天基数累加
    $DAU += intval($DNU * $PU2 - $CBU * $DIE);//第二天日活累加,去除自然流失率
}
$i = 1;
for (;$i< $DAY;$i++) {
    fnext($CBU, $DAU, $DNU, $PU2, $DIE);
    echo "第{$i}天 -> 用户基数: {$CBU} ;  日活用户: {$DAU} ; \n";
}
