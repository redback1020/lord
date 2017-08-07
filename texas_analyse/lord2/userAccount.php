<?php
require_once '../manage/checkPriv.php';
require_once '../include/database.class.php';

$uid=$_GET["uid"];

//用UID查询用户账户信息


$sql="select user_user.*,user_login.*,lord_game_user.cool_num from (user_user left join user_login on user_user.id=user_login.uid)left join lord_game_user on user_user.id=lord_game_user.uid where user_login.uid={$uid} limit 1 ";
$v = $db -> query($sql)-> fetch(PDO::FETCH_ASSOC);
$array=$uids=array();
$array[]=$v;
$uids[]=$v["uid"];
//用同样的设备号查询
$sql="select user_user.*,user_login.*,lord_game_user.cool_num from (user_user left join user_login on user_user.id=user_login.uid)left join lord_game_user on user_user.id=lord_game_user.uid where user_login.open_id='" . $v["open_id"] . "'";
$res = $db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
foreach ($res as $row) {
    if (!in_array($row["uid"], $uids)) {
        $array[] = $row;
        $uids[] = $row["uid"];
    }
}
//用同样的扩展号查询
if ($v["extend"] != '') {
    $sql="select user_user.*,user_login.*,lord_game_user.cool_num from (user_user left join user_login on user_user.id=user_login.uid)left join lord_game_user on user_user.id=lord_game_user.uid where user_login.extend='" . $v["extend"] . "'";
    $res = $db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    foreach($res as $row){
        if(!in_array($row["uid"],$uids)){
            $array[]=$row;
            $uids[]=$row["uid"];
         }
    }
}

//用同样的靓号查询
$sql="select user_user.*,user_login.*,lord_game_user.cool_num from (user_user left join user_login on user_user.id=user_login.uid)left join lord_game_user on user_user.id=lord_game_user.uid where lord_game_user.cool_num='" . $v["cool_num"] . "'";
$res = $db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
foreach ($res as $row) {
    if (!in_array($row["uid"], $uids)) {
        $array[] = $row;
        $uids[] = $row["uid"];
    }
}


?>
<script type="text/javascript" src="../js/jquery.js"></script>
<script type="text/javascript" src="../js/My97DatePicker/WdatePicker.js"></script>
<link rel="stylesheet" type="text/css" href="../bootstrap/css/bootstrap.min.css" />
<script type="text/javascript">
    $(document).ready(function(){
        $("button").click(function(){
            var _this = $(this);
            var uid = _this.attr('uid');
            var cool_num = _this.attr('cool_num');
            $.get("userchangecoolnum.php?uid="+uid+"&cool_num="+cool_num,function(coolnum){
                alert("操作成功");
                _this.attr("cool_num",coolnum);
                $(_this.parent().parent().find('td').get(1)).html(coolnum);
            });
        });
    });












</script>
<body>
	<div style="padding:8px 10px;">
			<fieldset>
				<legend>用户的详细信息</legend>
				<div class="row">
                </div>
			</fieldset>
		<table class="table table-bordered table-condensed table-hover">
			<tr class="info">
				<td nowrap><strong>uid<br/>UID</strong></td>
                <td nowrap><strong>cool_num<br/>编号ID</strong></td>
				<td nowrap><strong>open_type<br/>账号类型</strong></td>
				<td nowrap><strong>open_id<br/>设备号</strong></td>
                <td nowrap><strong>extend<br/>扩展号</strong></td>
                <td nowrap><strong>account<br/>账号</strong></td>
                <td nowrap><strong>password<br/>密码</strong></td>
                <td nowrap><strong>uuid<br/>UUID</strong></td>
                <td nowrap><strong>channel<br/>渠道</strong></td>
                <td nowrap><strong>state<br/>状态</strong></td>
                <td nowrap><strong>当有相同的<br/>编号ID时</strong></td>
			</tr>
            <?php

            foreach ( $array as $val ) {
            ?>
            <tr>
                <td><?=$val['uid']?></td>
                <td><?=$val['cool_num']?></td>
                <td><?=$val['open_type']?></td>
                <td><?=$val['open_id']?></td>
                <td><?=$val['extend']?$val['extend']:'&nbsp;'?></td>
                <td><?=$val['account']?$val['account']:'&nbsp;'?></td>
                <td><?=$val['password']?></td>
                <td><?=$val['uuid']?></td>
                <td><?=$val['channel']?></td>
                <td><?=$val['state']?></td>

                <td><button uid="<?=$val['uid']?>" cool_num="<?=$val['cool_num']?>">重置编号ID</button></td>
            </tr>
            <?php
            }
            ?>

		</table>

	</div>
</body>
