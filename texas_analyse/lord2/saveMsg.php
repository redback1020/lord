<link rel="stylesheet" type="text/css" href="../bootstrap/css/bootstrap.min.css" />
<?php
header("Content-type: text/html; charset=utf-8");
require_once '../manage/checkPriv.php';
require_once '../include/database.class.php';
//security
$api = isset($_REQUEST['api']) ? trim($_REQUEST['api']) : 'broadcast';//
$type = isset($_REQUEST['type']) ? trim($_REQUEST['type']) : 'add';//
//params
$msg = isset($_REQUEST['msg']) ? trim($_REQUEST['msg']) : '';
$level = isset($_REQUEST['level']) ? intval($_REQUEST['level']) : 1;
//execute
$res = apiGet($api, $type, array('msg'=>$msg, 'level'=>$level));
//respond
//
//??
$time = time();
$key = "qwe!@#321";
$sign = md5($key.$time);
?>
<body>
<div class="container">
	<fieldset>
		<legend>系统提示</legend>
		<div class="">发送成功, 将在10秒内显示<a href="sendmsg.php?time=<?=$time?>&sign=<?=$sign?>" style="font-size:20px;	">返回</a>继续操作</div>
	</fieldset>
</div>
</body>
