<?php

//兑换品管理

$apiname = 'convert';
$apiword = '兑换品';
$timenow = time();
$Q = $_REQUEST;
$apitype = isset($Q['apitype']) ? trim($Q['apitype']) : 'select';
$id = isset($Q['id']) ? intval($Q['id']) : 0;
$apitypes = array('online'=>'上线','unline'=>'下线','delete'=>'删除','modify'=>'修改','update'=>'更新','create'=>'创建','insert'=>'插入','select'=>'列表','search'=>'搜索');
if ( !isset($apitypes[$apitype]) ) exit;
$apitypei = array_keys($apitypes);
$searchs = array('type'=>array('name'=>'分类归属','int'=>0,'sel'=>1,'all'=>1));
$types = array('coupon2mobifee'=>'话费','coupon2beitong'=>'数码产品','coupon2fitness'=>'户外健身','coupon2oldman'=>'老人儿童','coupon2other'=>'其他');//兑换类型
$is_onsales = array('0'=>'无促销','1'=>'有促销');//促销状态
$is_recommends = array('0'=>'不推荐','1'=>'有推荐');//推荐状态
$states = array('0'=>'正常','1'=>'离线','2'=>'删除');//上线状态
//base
header("Content-type: text/html; charset=utf-8");
require_once '../manage/checkPriv.php';
require_once '../include/database.class.php';
//online //unline //delete
if ( $apitype == 'online' || $apitype == 'unline' || $apitype == 'delete' ) {
	$sqlp= $apitype == 'delete' ? ', `sort` = 255' : '';
	$sql = "UPDATE `lord_list_$apiname` SET `state` = ".array_search($apitype, array('online', 'unline', 'delete'))." $sqlp WHERE `id` = $id";
	$res = $pdo->getDB(1)->exec($sql);
	$res = $res ? apiGet($apiname, $apitype, array('id'=>$id)) : array('errno'=>8, 'error'=>"查询错误。 $sql");
	echo json_encode($res);
	exit;
}
//search
if ( $apitype == 'search' ) {
	$per_page = $Q['per_page'];
	$cur_page = $Q['cur_page'] * $per_page;
	$where = "1=1 ";
	foreach ( $searchs as $k => $v ) {
		$val = isset($Q[$k]) ? $Q[$k] : ($v['all'] ? 'all' : ($v['int'] ? null :''));
		if ( $v['all'] && $val != 'all' ) { $where .= " AND `$k` = ".($v['int'] ? $val : ("'".$val."'")); }
		elseif ( $val != 'all' && !empty($val) ) { $where .= " AND `$k` = ".($v['int'] ? $val : ("'".$val."'")); }
	}
	$sql = "SELECT * FROM `lord_list_$apiname` WHERE {$where} ORDER BY `type` ASC, `sort` ASC LIMIT {$cur_page}, {$per_page}";
	$res = $db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
	$res = ( $res && is_array($res) ) ? $res : array();
	foreach ( $res as $k => $v )
	{
		$res[$k]['type'] = $types[$v['type']];
		$res[$k]['store'] = $v['store'] > -1 ? $v['store'] : "不限";
		$res[$k]['onsale'] = $v['is_onsale'] ? $v['onsale'] : "&nbsp;";
		$res[$k]['state'] = $v['state'];
		$res[$k]['state_'] = ($v['is_recommend']?('<span class="cred">'.$is_recommends[$v['is_recommend']].'</span>'):($is_recommends[$v['is_recommend']])).' '.$states[$v['state']];
		$res[$k]['create_time'] = $v['create_time'] ? date("Y-m-d H:i:s", $v['create_time']) : "&nbsp;";
		$res[$k]['update_time'] = $v['update_time'] ? date("Y-m-d H:i:s", $v['update_time']) : "&nbsp;";
	}
	$json['data_res'] = $res;
	$sql = "SELECT count(*) as data_num FROM `lord_list_$apiname` WHERE {$where} ";
	$res = $db->query($sql)->fetch(PDO::FETCH_ASSOC);
	$json['data_num'] = $res ? $res['data_num'] : 0;
	echo json_encode($json);
	exit;
}
//update //insert
if ( $apitype == 'update' || $apitype == 'insert' ) {
	$type = isset($Q['type']) && isset($types[$Q['type']]) ? $Q['type'] : 'coupon2mobifee';
	$channel = isset($Q['channel']) ? trim($Q['channel']) : '';
	$title = isset($Q['title']) ? trim($Q['title']) : '';
	$fileId = isset($Q['fileId']) ? intval($Q['fileId']) : 0;
	$value = isset($Q['value']) ? intval($Q['value']) : 0;
	$price = isset($Q['price']) ? intval($Q['price']) : 0;
	$recharge = isset($Q['recharge']) ? intval($Q['recharge']) : 0;
	$store = isset($Q['store']) ? intval($Q['store']) : -1;
	$is_onsale = isset($Q['is_onsale']) && isset($is_onsales[intval($Q['is_onsale'])]) ? intval($Q['is_onsale']) : 0;
	$onsale = isset($Q['onsale']) ? trim($Q['onsale']) : '';
	$is_recommend = isset($Q['is_recommend']) && isset($is_recommends[intval($Q['is_recommend'])]) ? intval($Q['is_recommend']) : 0;
	$state = isset($Q['state']) && isset($states[intval($Q['state'])]) ? intval($Q['state']) : 1;
	$sort = isset($Q['sort']) && $Q['sort'] > 0 && $Q['sort'] < 255  ? intval($Q['sort']) : 99;
	$create_time = $update_time = $timenow;
	if ( $apitype == 'insert' ) $sql = "INSERT INTO `lord_list_$apiname` (`type`,`channel`,`title`,`fileId`,`value`,`price`,`recharge`,`store`,`is_recommend`,`is_onsale`,`onsale`,`sort`,`create_time`,`update_time`) VALUES ('$type','$channel','$title',$fileId,$value,$price,$recharge,$store,$is_recommend,$is_onsale,'$onsale',$sort,$create_time,$update_time)";
	if ( $apitype == 'update' ) $sql = "UPDATE `lord_list_$apiname` SET `type`='$type',`channel`='$channel',`title`='$title',`fileId`=$fileId,`value`=$value,`price`=$price,`recharge`=$recharge,`store`=$store,`is_recommend`=$is_recommend,`is_onsale`=$is_onsale,`onsale`='$onsale',`sort`=$sort,`update_time`=$update_time WHERE `id`=$id";
	$res = $pdo->getDB(1)->exec($sql);
	if ( $res ) {
		if ( $apitype == 'insert' ) $id = $pdo->getDB(1)->lastInsertId();
		$res = apiGet($apiname, $apitype, array('id'=>$id));
		if ( !$res['errno'] ) {
			header("Location: {$apiname}.php");
		}
	} else {
		$res = array('errno'=>8, 'error'=>"查询错误。 $sql");
	}
	echo json_encode($res);
	exit;
}
//modify
if ( $apitype == 'modify' ) {
	$sql = "SELECT * FROM `lord_list_$apiname` WHERE `id` = $id";
	$data = $db->query($sql)->fetch(PDO::FETCH_ASSOC);
	if ( !$data ) {
		echo "查询错误。 $sql";
		exit;
	}
}
//create
if ( $apitype == 'create' ) {
	$data = array();
}
//modify //create
if ( $apitype == 'modify' || $apitype == 'create' ) {
?>
<script type="text/javascript" src="../js/jquery.js"></script>
<script type="text/javascript" src="../js/My97DatePicker/WdatePicker.js"></script>
<link type="text/css" href="../bootstrap/css/bootstrap.min.css" rel="stylesheet" />
<style type="text/css">
.body{position:absolute;left:0;top:0;padding:0 0 0 10px;width:98%;}
legend{margin-bottom: 10px;}
table.table{ font-size: 12px;margin-bottom:8px;width: 100%!important;}
table.table th,table.table td{ white-space: nowrap;}
table.table td,table.table th{line-height:30px!important;}
table.table td select, table.table td input{height: 30px!important;margin:0!important;}
label{display: inline;}
.in_t0{width: 60px;}
.in_t1{width: 120px;}
.in_t2{width: 240px;}
.in_t3{width: 480px;}
.in_a{width: 300px;height: 100px!important;margin:0!important;}
.tdth{width:70px!important;font-size:14px;}
.g{width:200px;float:left;}
</style>
<script>
$(function(){
	//
});
</script>
<body>
<div class="body">

<fieldset>
	<legend><?=$apiword?> - <?=$apitypes[$apitype]?></legend>
	<form action="<?=$apiname?>.php" method="post">
		<input type="hidden" name="apitype" value="<?=$apitypei[array_search($apitype, $apitypei)+1]?>" />
		<input type="hidden" name="id" value="<?php if($data){echo $data['id'];} ?>" />
		<table class="table table-bordered table-condensed table-hover">
			<tr>
				<td class="tdth">分类归属:</td><?php $var = 'type';?>
				<td><select name="<?=$var?>" class="span2"><?php foreach (${$var.'s'} as $k => $v) { echo "<option value='$k'".($data && $data[$var]==$k ? " selected='selected'" : "").">$v</option>"; } ?></select></td>
			</tr>
			<tr>
				<td class="tdth">专属渠道:</td><?php $var = 'channel';?>
				<td><input name="<?=$var?>" value="<?php if($data){echo $data[$var];} ?>" type="text" class="in_t3" />多个渠道之间以英文空格隔开</td>
			</tr>
			<tr>
				<td class="tdth">兑换物品:</td><?php $var = 'title';?>
				<td><input name="<?=$var?>" value="<?php if($data){echo $data[$var];} ?>" type="text" class="in_t1" /></td>
			</tr>
			<tr>
				<td class="tdth">图片编号:</td><?php $var = 'fileId';?>
				<td><input name="<?=$var?>" value="<?php if($data){echo $data[$var];} ?>" type="text" class="in_t0" /></td>
			</tr>
			<tr>
				<td class="tdth">兑换价格:</td><?php $var = 'price';?>
				<td><input name="<?=$var?>" value="<?php if($data){echo $data[$var];} ?>" type="text" class="in_t0" /></td>
			</tr>
			<tr>
				<td class="tdth">兑换数值:</td><?php $var = 'value';?>
				<td><input name="<?=$var?>" value="<?php if($data){echo $data[$var];} ?>" type="text" class="in_t0" />N元话费/N件实物</td>
			</tr>
			<tr>
				<td class="tdth">必须消费:</td><?php $var = 'recharge';?>
				<td><input name="<?=$var?>" value="<?php if($data){echo $data[$var];} ?>" type="text" class="in_t0" />乐币 用户消费达到N乐币以上才显示</td>
			</tr>
			<tr>
				<td class="tdth">库存数量:</td><?php $var = 'store';?>
				<td><input name="<?=$var?>" value="<?php if($data){echo $data[$var];} ?>" type="text" class="in_t0" />: -1无限 0不可购买 >0库存数</td>
			</tr>
			<tr>
				<td class="tdth">促销状态:</td><?php $var = 'is_onsale';?>
				<td><select name="<?=$var?>" class="span2"><?php foreach (${$var.'s'} as $k => $v) { echo "<option value='$k'".($data && $data[$var]==$k ? " selected='selected'" : "").">$v</option>"; } ?></select></td>
			</tr>
			<tr>
				<td class="tdth">促销文字:</td><?php $var = 'onsale';?>
				<td><input name="<?=$var?>" value="<?php if($data){echo $data[$var];} ?>" type="text" class="in_t1" />: 有促销状态时生效，不超过10个中文字符或20个英文字符</td>
			</tr>
			<tr>
				<td class="tdth">推荐状态:</td><?php $var = 'is_recommend';?>
				<td><select name="<?=$var?>" class="span2"><?php foreach (${$var.'s'} as $k => $v) { echo "<option value='$k'".($data && $data[$var]==$k ? " selected='selected'" : "").">$v</option>"; } ?></select></td>
			</tr>
			<tr>
				<td class="tdth">上线状态:</td><?php $var = 'state';?>
				<td><select name="<?=$var?>" class="span2"><?php foreach (${$var.'s'} as $k => $v) { echo "<option value='$k'".($data && $data[$var]==$k ? " selected='selected'" : "").">$v</option>"; } ?></select></td>
			</tr>
			<tr>
				<td class="tdth">显示顺序:</td><?php $var = 'sort';?>
				<td><input name="<?=$var?>" value="<?php if($data){echo $data[$var];} ?>" type="text" class="in_t0" />: 默认99</td>
			</tr>
			<tr>
				<td>&nbsp;</td>
				<td class="span1"><input type="submit" value="提交" class="btn" /></td>
			</tr>
		</table>
	</form>
</fieldset>

</div>
</body>
<?php
}
//select
if ( $apitype == 'select' ) {
?>
<script type="text/javascript" src="../js/jquery.js"></script>
<script type="text/javascript" src="../js/My97DatePicker/WdatePicker.js"></script>
<link type="text/css" href="../bootstrap/css/bootstrap.min.css" rel="stylesheet" />
<style type="text/css">
.body{position:absolute;left:0;top:0;padding:0 0 0 10px;width:98%;}
legend{margin-bottom: 10px;}
table.table{ font-size: 12px;margin-bottom:8px;width: 100%!important;}
table.table th,table.table td{ white-space: nowrap;}
.cred{color:#c00;}
</style>
<script>
var per_page = 20;
var cur_page = 0;
$(function(){ finder(); $('select.finder').change(function(){ finder(); }); });
function linkto( o, to ) { var _a = to.split('/'); var _this = $(o); if ( _a[0] != "http" ) { var _b = self.location.href.split('/'); delete _b[_b.length-1]; to = _b.join('/') + to; } self.location.href=to; }
function ajaxto( o, to ) { var _a = to.split('/'); var _this = $(o); if ( _a[0] != "http" ) { var _b = self.location.href.split('/'); delete _b[_b.length-1]; to = _b.join('/') + to; } $.getJSON(to, function(data){ if ( data ) { if ( data.errno == 0 ) { alert("操作成功。"); finder(); } else { alert("操作失败["+data.errno+"]："+data.error); } } }); }
function previt() { if ( cur_page==0 ) { alert("已经是第一页"); return; } cur_page--; findit(); }
function nextit() { cur_page++; findit(1); }
function finder() { cur_page=0; findit(); }
function findit( is_next ) {
	var type = $('#type').val();
	$.post("<?=$apiname?>.php?apitype=search&per_page="+per_page+"&cur_page="+cur_page, {
		type : type
	}, function( data ) {
		if ( data == null || data == "" ) { alert("获取数据失败！"); return; }
		var datalist = eval("("+data+")");
		var datahtml = "";
		for ( var i=0; i<datalist.data_res.length; i++ ) {
			var o = datalist.data_res[i];
			datahtml += "<tr class='table-body'>";
			datahtml += "<td>"+o.id+"</td>";
			datahtml += "<td>"+(o.channel!=""?o.channel:"&nbsp;")+"</td>";
			datahtml += "<td>"+(o.type!=""?o.type:"&nbsp;")+"</td>";
			datahtml += "<td>"+(o.title!=""?o.title:"&nbsp;")+"</td>";
			datahtml += "<td>"+(o.fileId?("<a href='http://gt2.youjoy.tv/ddzgamefile/mall_exchange/"+o.fileId+".png' target='_blank'>查看</a>"):"&nbsp;")+"</td>";
			datahtml += "<td>"+o.price+"</td>";
			datahtml += "<td>"+o.value+"</td>";
			datahtml += "<td>"+o.recharge+"乐币</td>";
			datahtml += "<td>"+o.store+"</td>";
			datahtml += "<td>"+o.onsale+"</td>";
			datahtml += "<td>"+o.state_+"</td>";
			datahtml += "<td>"+o.sort+"</td>";
			datahtml += "<td>"+o.update_time+"</td>";
			datahtml += "<td>"+(o.state==2?"&nbsp;":("<a href='#' onclick='ajaxto(this,\"<?=$apiname?>.php?apitype=delete&id="+o.id+"\")'>删除</a>&nbsp;&nbsp;<a href='#' onclick='ajaxto(this,\"<?=$apiname?>.php?apitype="+(o.state>0?'online':'unline')+"&id="+o.id+"\")'>"+(o.state==1?'<span style="color:green;">发布</span>':'<span style="color:red;">撤销</span>')+"</a>&nbsp;&nbsp;<a href='#' onclick='linkto(this,\"<?=$apiname?>.php?apitype=modify&id="+o.id+"\")'>修改</a>"))+"</td>";
			datahtml += "</tr>";
		}
		if ( datahtml=="" && is_next ) { alert("已到最后一页"); cur_page--; }
		else { $("#datalist").html(datahtml); $("#data_num").html(datalist.data_num); $("#page_num").html(Math.ceil(datalist.data_num/per_page)); $("#cur_page").html(cur_page+1); $("#pager").show(); }
	});
}
</script>

<body>
<div class="body">

<fieldset>
	<legend><?=$apiword?> - <?=$apitypes[$apitype]?></legend>
	<div class="row">
		<div class="span2">
			<?php $var = 'type'; $varn = '分类: '; ?><select id="<?=$var?>" class="span2 finder"><option value="all"><?=$varn?>全部</option><?php foreach (${$var.'s'} as $k => $v) { echo "<option value='$k'".($data && $data[$var]==$k ? " selected='selected'" : "").">{$varn}$v</option>"; } ?></select></td>
		</div>
		<div span="span1" style="float:right;">
			<input class="btn" type="button" value="查&nbsp;&nbsp;询" onclick="finder()" />
			<input class="btn" type="button" value="创&nbsp;&nbsp;建" onclick="linkto(this,'<?=$apiname?>.php?apitype=create')" />
		</div>
	</div>
</fieldset>

<table class="table table-bordered table-condensed table-hover">
	<tr class="info">
		<th>编号</th>
		<th>渠道</th>
		<th>分类</th>
		<th>名称</th>
		<th>图片</th>
		<th>价格</th>
		<th>数值</th>
		<th>必须消费</th>
		<th>库存</th>
		<th>促销</th>
		<th>状态</th>
		<th>排序</th>
		<th>时间</th>
		<th>操作</th>
	</tr>
	<tbody id="datalist">
	</tbody>
</table>

<table width="98%" border="0" cellpadding="0" cellspacing="0" align="left">
	<tr>
		<td height="25" id="pager" align="center" style="display:none;">
			共 <span id="data_num"></span>条 / <span id="page_num"></span>页&nbsp;
			<div class="btn-group">
				<button class="btn" onclick="previt()">前一页</button>
				<button class="btn" id="cur_page"></button>
				<button class="btn" onclick="nextit()">后一页</button>
			</div>
		</td>
	</tr>
</table>

</div>
</body>
<?php
}
?>
