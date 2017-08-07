<?php

/*
 * Hi, Shawn
 * 这里有一个新的任务，你有时间吗？你能挤出时间吗？我能占用你一点儿时间吗？我能排的更靠前吗？……
 */

 date_default_timezone_set('PRC');
 ini_set("display_errors","on");
 error_reporting(E_ALL);//E_ERROR | E_WARNING | E_PARSE

//函数－html-head
function echoHead()
{
    echo '
    <!DOCTYPE html>
    <html>
    <head>
    <title>Hi, Shawn. 我有一个新需求</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="generator" content="Atom" />
    <meta name="author" content="shawn kao" />
    <meta name="keywords" content="任务,排期,时间" />
    <meta name="description" content="任务管理" />
    <style type="text/css">
    body{font-size:16px;font-family:"simsun";line-height:20px;white-space:nowrap;}
    div{margin-bottom:5px;padding:5px;border:1px solid #333;}
    p{margin-left:46px;padding:0;}
    span{padding:0 5px;color:#666;}
    a{margin:0 5px;color:#369;}
    .fl{float:left;}
    .fr{float:right;}
    .hr{height:0;margin:1px;border-bottom:1px solid #eee;overflow:hidden;}
    .box{width:600px;height:200px;}
    .box .in{width:500px;}
    .todo{background:#ccc;}
    .doing{background:#9c9;}
    </style>
    <script src="http://op.youjoy.tv/js/jquery.js"></script>
    <script type="text/javascript">
        $(document).ready(function(){
            $(".todo:first").addClass("doing");
        });
    </script>
    </head>
    <body>
    <section style="width:1000px;margin:0 auto;">
    ';
}
//函数－html-foot
function echoFoot()
{
    echo '
    </section>
    </body>
    </html>
    ';
}
//函数－输出任务头部
function echoSubject( $subject, $time=0, $done=0 )
{
    $tip = $time ? date("Y-m-d H:i:s - ", $time) : '';
    echo "<div".(!$done?" class='todo'":"").">{$tip}{$subject}</div>";
}
//函数－输出任务记录
function echoComment( $comment, $time=0 )
{
    $tip = $time ? date("m-d H:i:s - ", $time) : '';
    echo "<p>{$tip}{$comment}</p>";
}
//函数－输出操控项
function echoOperate( $id=0, $istasker=0, $admin=0 )
{
    global $url;
    if ( ! $id ) {
        echo "<a class='fr op' href='$url?action=passnew'>设置⎈</a>";
        echo "<a class='fr op' href='$url?action=taskadd'>发布☰</a>";
        return 1;
    }
    if ( $istasker ) {
        echo "<a class='fr op' href='$url?action=taskdot&id=$id'>完成⇩</a>";
    }
    if ( $admin ) {
        echo "<a class='fr op' href='$url?action=tasktop&id=$id'>置顶⇧</a>";
        echo "<a class='fr op' href='$url?action=tasksee&id=$id'>修改☷</a>";
    }
    echo "<a class='fr op' href='$url?action=taskcmt&id=$id'>备注※</a>";
}
//函数－输出刷新脚本
function echoRefresh()
{
    echo "
    <script type='text/javascript'>
    function refresh(){
        self.location.href = self.location.href;
    }
    var t = setTimeout('refresh()', 30000);
    </script>
    ";
}

//函数－处理日志
function after( $action, $acinfo='', $taskid=0, $jump=0 )
{
    if ( in_array($action ,array('taskrun','tasksee','loginon','passnew','taskcmt')) ) exit;
    global $uid, $url;
    $db = getdb();
    $sql = "INSERT INTO `hs_logs` (`uid`, `action`, `acinfo`, `taskid`, `tmcr`) VALUES ($uid, '$action', '$acinfo', $taskid, ".time().")";
    $db->exec($sql);
    if ( $jump ) {
        header("location:".$url);
    }
    exit;
}
//函数－连接pdo-mysql
function getdb()
{
    $db = new PDO("mysql:host=10.10.13.141;port=3306;dbname=dbx5415j5nf05kqn", "dbx5415j5nf05kqn", "TYxYpysG8fR8PQdp");
    $db->exec("SET NAMES 'utf8'");
    return $db;
}

//初始化
session_start();
$db = null;
$url = 'http://'.$_SERVER['HTTP_HOST'].':'.$_SERVER['SERVER_PORT'].$_SERVER['SCRIPT_NAME'];
$uid = isset($_SESSION['hs_uid']) ? $_SESSION['hs_uid'] : 0;
$username = isset($_SESSION['hs_username']) ? $_SESSION['hs_username'] : '未知';
$istasker = isset($_SESSION['hs_istasker']) ? $_SESSION['hs_istasker'] : 0;
$tasker = 1;//固定一个人用？多人用时？
$actime = time();
$action = isset($_REQUEST['action']) && trim($_REQUEST['action']) ? preg_replace('/[^a-z]/','',trim($_REQUEST['action'])) : 'taskrun';
//创建数据库
// if ( false && $action == 'builddb' )
if ( $action == 'builddb' )
{
    $db = getdb();
    $sql = "DROP TABLE `hs_user`;DROP TABLE `hs_task`;DROP TABLE `hs_logs`";
    $res = $db->exec($sql);
    $sql = "
    CREATE TABLE IF NOT EXISTS `hs_task` (
		`id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
		`tasker` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '执行方',
		`uid` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '需求方',
        `subject` varchar(255) NOT NULL DEFAULT '' COMMENT '任务名称',
		`rsort` int(10) unsigned NOT NULL DEFAULT '999' COMMENT '权重',
		`done` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否完成',
        `tmcr` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
        `tmup` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '更新时间',
		PRIMARY KEY (`id`),
		INDEX `s1` (`tasker`,`uid`,`done`),
		INDEX `s2` (`tasker`,`done`,`rsort`)
	) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='任务表' AUTO_INCREMENT=1
    ";
    $res = $db->exec($sql);
    $sql = "
    CREATE TABLE IF NOT EXISTS `hs_logs` (
		`id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
        `uid` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '操作人',
        `action` varchar(32) NOT NULL DEFAULT '' COMMENT '操作名称',
        `acinfo` varchar(255) NOT NULL DEFAULT '' COMMENT '操作内容',
        `taskid` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '任务ID',
        `tmcr` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
		PRIMARY KEY (`id`),
        INDEX `s1` (`uid`),
        INDEX `s2` (`action`),
		INDEX `s3` (`taskid`)
	) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='日志表' AUTO_INCREMENT=1
    ";
    $res = $db->exec($sql);
    $sql = "
    CREATE TABLE IF NOT EXISTS `hs_user` (
		`uid` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
        `username` varchar(32) NOT NULL DEFAULT '' COMMENT '账号',
        `password` varchar(32) NOT NULL DEFAULT '' COMMENT '密码',
        `tmcr` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
		PRIMARY KEY (`uid`),
		INDEX `s1` (`username`, `password`)
	) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='用户表' AUTO_INCREMENT=1
    ";
    $res = $db->exec($sql);
    $sql = "
    INSERT INTO `hs_user` (`uid`, `username`, `password`, `tmcr`) 
    VALUES (1,'gaoshang','".md5('Dm32828282')."',$actime)
    , (2,'fengjianguang','".md5('fengjianguang')."',$actime)
    , (3,'yinianhua','".md5('yinianhua')."',$actime)
    , (4,'xushoujiang','".md5('xushoujiang')."',$actime)
    , (5,'zhoutianhang','".md5('zhoutianhang')."',$actime)
    , (6,'daini','".md5('daini')."',$actime)
    , (7,'lixingran','".md5('lixingran')."',$actime)
    , (8,'wangjianwei','".md5('wangjianwei')."',$actime)
    , (9,'luoye','".md5('luoye')."',$actime)
    ";
    $res = $db->exec($sql);
    $action = 'loginon';
}

//处理登录校验
if ( ! in_array($action, array('loginon', 'logindo')) && ( ! $uid || ! isset($_SESSION['hs_last_actime']) || $actime - $_SESSION['hs_last_actime'] > 3600 * 9 ) ) {
    $action = 'loginon';
}
$_SESSION['hs_last_actime'] = $actime;
$_SESSION['hs_last_action'] = $action;
if ( isset($_SESSION['hs_uid']) && $_SESSION['hs_uid'] ) $uid = $_SESSION['hs_uid'];
if ( isset($_SESSION['hs_username']) && $_SESSION['hs_username'] ) $name = $_SESSION['hs_username'];
if ( ! in_array($action, array('loginon', 'taskadd')) ) {
    $db = getdb();
}

//处理登录面板
if ( $action == 'loginon' )
{
    echoHead();
    ?>
    <div class="box">
        <form method="post" action="hishawn.php">
            <input type="hidden" name="action" value="logindo" />
            <br>帐号：<input type="text" name="username" /><br>
            <br>密码：<input type="password" name="password" /><br>
            <br><input type="submit" value="登 录" />
        </form>
    </div>
    <?php
    $_SESSION['hs_uid'] = 0;
    $_SESSION['hs_username'] = '';
    $_SESSION['hs_istasker'] = 0;
    echoFoot();
    after($action);
}

//处理登录操作
if ( $action == 'logindo' )
{
    $username = preg_replace('/[^a-z0-9]/','',$_REQUEST['username']);
    $password = md5(preg_replace('/[^a-zA-Z0-9]/','',$_REQUEST['password']));
    $sql = "SELECT * FROM `hs_user` WHERE `username` = '$username' AND `password` = '$password'";
    if ( ($res = $db->query($sql)) && ($row = $res->fetch(PDO::FETCH_ASSOC)) ) {
        $_SESSION['hs_uid'] = $uid = $row['uid'];
        $_SESSION['hs_username'] = $username = $row['username'];//用户名？账户名？
        $_SESSION['hs_istasker'] = intval($row['uid'] == $tasker);
    }
    after($action, $username, 0, 1);
}

//处理密码面板
if ( $action == 'passnew' )
{
    echoHead();
    ?>
    <div class="box">
        <form method="post" action="hishawn.php">
            <input type="hidden" name="action" value="passset" />
            <br>帐号：<?=$username?><br>
            <br>密码：<input type="password" name="password" /><br>
            <br><input type="submit" value="重 设" />
        </form>
    </div>
    <?php
    echoFoot();
    after($action);
}


//处理登录操作
if ( $action == 'passset' )
{
    $password = md5(preg_replace('/[^a-zA-Z0-9]/','',$_REQUEST['password']));
    $sql = "SELECT * FROM `hs_user` WHERE `username` = '$username' AND `uid` = $uid";
    if ( ($res = $db->query($sql)) && ($row = $res->fetch(PDO::FETCH_ASSOC)) ) {
        $sql = "UPDATE `hs_user` SET `password` = '$password' WHERE `uid` = $uid";
        $res = $db->exec($sql);
        $_SESSION['hs_uid'] = 0;
        $_SESSION['hs_username'] = '';
        $_SESSION['hs_istasker'] = 0;
    }
    after($action, $username, 0, 1);
}
//处理任务列表
if ( $action == 'taskrun' ) {
    echoHead();
    $sql = "SELECT * FROM `hs_user` WHERE `uid` = $tasker";
    if ( ($res = $db->query($sql)) && ($row = $res->fetch(PDO::FETCH_ASSOC)) ) {
        $taskuser = $row['username'];
    } else {
        $taskuser = '未知';
    }
    echoOperate();
    echo "<span class='fr'>$username, 您好。</span>";
    echo "<h3>开发者 $taskuser 的任务状态列表</h3>";
    $sql = "SELECT a.*, u.`username` FROM `hs_task` a LEFT JOIN `hs_user` u ON a.`uid` = u.`uid` WHERE a.`tasker` = $tasker ORDER BY a.`rsort` DESC, a.`done` ASC, a.`tmup` DESC LIMIT 10";// AND a.`done` = 0
    echo "<div>";
    $actions = array('taskins'=>'发布','taskupd'=>'修改','tasktop'=>'置顶','taskfob'=>'被顶','taskcmd'=>'备注','taskdot'=>'完成');
    if ( ($res = $db->query($sql)) && ($rows = $res->fetchAll(PDO::FETCH_ASSOC)) ) {
        $i = 0;
        foreach ( $rows as $row )
        {
            $i++;
            if ( $i > 9 ) {
                echo "and so on.";
                break;
            }
            ! $row['done'] && echoOperate($row['id'], intval($uid==$row['tasker']), intval($uid==$row['uid']));
            echoSubject($row['username']." : ".$row['subject'], $row['tmcr'], $row['done']);
            $sql = "SELECT a.*, u.`username` FROM `hs_logs` a LEFT JOIN `hs_user` u ON a.`uid` = u.`uid` WHERE a.`taskid` = ".$row['id']." ORDER BY a.`tmcr` DESC";
            if ( ($res = $db->query($sql)) && ($rets = $res->fetchAll(PDO::FETCH_ASSOC)) ) {
                foreach ( $rets as $ret )
                {
                    if ( isset($actions[$ret['action']]) ) {
                        echoComment($actions[$ret['action']]." - ".$ret['username']." : ".$ret['acinfo'], $ret['tmcr']);
                    }
                }
            }
        }
    } else {
        echo "没有数据。";
    }
    echo "</div>";
    echoRefresh();
    echoFoot();
    after($action);
}

//处理任务增加
if ( $action == 'taskadd' ) {
    echoHead();
    ?>
    <div class="box">
        <form method="post" action="hishawn.php">
            <input type="hidden" name="action" value="taskins" />
            <br>任务名称：<br>
            <br><input class="in" type="text" name="subject" /><br>
            <br><input type="submit" value="发 布" />
        </form>
    </div>
    <?php
    echoFoot();
    after($action);
}

//处理任务插入
if ( $action == 'taskins' ) {
    $subject = trim($_REQUEST['subject']);
    $sql = "INSERT INTO `hs_task` (`tasker`, `uid`, `subject`, `rsort`, `done`, `tmcr`, `tmup`) VALUES ($tasker, $uid, '$subject', 1, 0, $actime, 0)";
    $res = $db->exec($sql);
    $id = $db->lastInsertId();
    after($action, '', $id, 1);
}

//任务内容面板
if ( $action == 'tasksee' ) {
    $id = intval($_REQUEST['id']);
    $sql = "SELECT * FROM `hs_task` WHERE `uid` = $uid AND `tasker` = $tasker AND `id` = $id";
    echoHead();
    if ( ($res = $db->query($sql)) && ($row = $res->fetch(PDO::FETCH_ASSOC)) ) {
        $subject = $row['subject'];
        ?>
        <div class="box">
            <form method="post" action="hishawn.php">
                <input type="hidden" name="action" value="taskupd" />
                <input type="hidden" name="id" value="<?=$id?>" />
                <br>任务名称：<br>
                <br><input class="in" type="text" name="subject" value="<?=$subject?>" /><br>
                <br><input type="submit" value="修 改" />
            </form>
        </div>
        <?php
    } else {
        echo "操作失败。";
    }
    echoFoot();
    after($action);
}

//处理任务更新
if ( $action == 'taskupd' ) {
    $id = intval($_REQUEST['id']);
    $sql = "SELECT * FROM `hs_task` WHERE `uid` = $uid AND `tasker` = $tasker AND `id` = $id";
    $row = $db->query($sql)->fetch(PDO::FETCH_ASSOC);
    if ( ($res = $db->query($sql)) && ($row = $res->fetch(PDO::FETCH_ASSOC)) ) {
        $subject = trim($_REQUEST['subject']);
        $sql = "UPDATE `hs_task` SET `subject` = '$subject', `tmup` = $actime WHERE `uid` = $uid AND `tasker` = $tasker AND `id` = $id";
        $res = $db->exec($sql);
    }
    after($action, '旧: '.$row['subject'], $id, 1);
}

//任务备注
if ( $action == 'taskcmt' ) {
    $id = intval($_REQUEST['id']);
    $sql = "SELECT * FROM `hs_task` WHERE `tasker` = $tasker AND `id` = $id";
    echoHead();
    if ( ($res = $db->query($sql)) && ($row = $res->fetch(PDO::FETCH_ASSOC)) ) {
        ?>
        <div class="box">
            <form method="post" action="hishawn.php">
                <input type="hidden" name="action" value="taskcmd" />
                <input type="hidden" name="id" value="<?=$id?>" />
                <br>任务名称：
                <br><?=$row['subject']?>
                <br>备注内容：
                <br><input class="in" type="text" name="comment" value="" />
                <br><input type="submit" value="备 注" />
            </form>
        </div>
        <?php
    } else {
        echo "操作失败。";
    }
    echoFoot();
    after($action);
}

//执行任务备注
if ( $action == 'taskcmd' ) {
    $id = intval($_REQUEST['id']);
    $comment = trim($_REQUEST['comment']);
    $sql = "SELECT * FROM `hs_task` WHERE `tasker` = $tasker AND `id` = $id";
    if ( ($res = $db->query($sql)) && ($row = $res->fetch(PDO::FETCH_ASSOC)) ) {
        //
    } else {
        $id = 0;
    }
    after($action, $comment, $id, 1);
}

//处理任务置顶
if ( $action == 'tasktop' ) {
    $id = intval($_REQUEST['id']);
    $sql = "SELECT * FROM `hs_task` WHERE `uid` = $uid AND `tasker` = $tasker AND `id` = $id";
    $row = $db->query($sql)->fetch(PDO::FETCH_ASSOC);
    if ( ($res = $db->query($sql)) && ($row = $res->fetch(PDO::FETCH_ASSOC)) ) {
        $sql = "SELECT max(`rsort`) rsort FROM `hs_task` WHERE `tasker` = $tasker AND `done` = 0";
        if ( ($res = $db->query($sql)) && ($row = $res->fetch(PDO::FETCH_ASSOC)) ) {
            $rsort = $row['rsort'] + 1;
            $sql = "UPDATE `hs_task` SET `rsort` = $rsort, `tmup` = $actime WHERE `uid` = $uid AND `tasker` = $tasker AND `id` = $id";
            $res = $db->exec($sql);
        }
    }
    after($action, '', $id, 1);
}

//处理任务完成
if ( $action == 'taskdot' ) {
    $id = intval($_REQUEST['id']);
    if ( $uid == $tasker ) {
        $sql = "UPDATE `hs_task` SET `done` = 1, `rsort` = 1, `tmup` = $actime WHERE `tasker` = $uid AND `id` = $id";
        $res = $db->exec($sql);
    }
    after($action, '', $id, 1);
}
