<?php
header("Content-type: text/html; charset=utf-8");
require_once '../manage/checkPriv.php';
require_once '../include/database.class.php';
$isReload = intval( isset($_REQUEST['isReload']) && $_REQUEST['isReload'] );
if ( $isReload ) {
	//security
	$api = 'server';//
	$type = 'reload';//
	$res = apiGet($api, $type, array('delay'=>0));
	echo json_encode($res);
	exit;
} else {
?>
<link rel="stylesheet" type="text/css" href="../bootstrap/css/bootstrap.min.css" />
<script type="text/javascript" src="../js/jquery.js"></script>
<script type="text/javascript">
$(function(){
	$(".btn1").click(function(){
		if ( confirm("确定要重载所有服务器？？？") ) {
			var urlTo = 'serverReload.php?isReload=1';
			$.getJSON(urlTo, function(data){
				if ( data ) {
					if ( data.errno == 0 ) {
						alert("操作成功["+data.errno+"]："+data.error);
						// self.location.reload();
					} else {
						alert("操作失败["+data.errno+"]："+data.error);
					}
				}
			});
		}
	});
});
</script>
<body>
<div class="container">
	<input type="submit" value="我保证已确知风险并愿意承担责任，并确定开始重载所有服务器" class="btn btn1" />
<div>
</body>
<?php
}
?>
