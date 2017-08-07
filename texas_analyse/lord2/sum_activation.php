<?php
require_once '../manage/checkPriv.php';
require_once '../include/database.class.php';
?>
<script type="text/javascript" src="../js/jquery.js"></script>
<script src="../js/My97DatePicker/WdatePicker.js" language="javascript"></script>
<link rel="stylesheet" type="text/css" href="../bootstrap/css/bootstrap.min.css" />
<?php
$cateid = isset($_POST['cateid']) ? intval($_POST['cateid']) : "all";
$start = ( isset($_POST['start']) && $_POST['start'] != "" ) ? date("Y-m-d", strtotime($_POST['start'])) : date("Y-m-d", time());
$_POST['start'] = $start;
$start = intval(str_replace('-', '', $start));
$end = ( isset($_POST['end']) && $_POST['end'] != "" ) ? date("Y-m-d", strtotime($_POST['end'])) : date("Y-m-d", time());
$_POST['end'] = $end;
$end = intval(date("Ymd", strtotime($end) + 86400));
$cates = $contents = array();
$data_activation_gift_list = array();
include('/data/sweety/game/include/data_activation_gift_list.php');
foreach ( $data_activation_gift_list as $k => $v )
{
	$cates[$v['id']] = $v['name'];
	if ($v['coins']) $contents[$v['id']][] = '乐豆['.$v['coins'].']';
	if ($v['propid']) $contents[$v['id']][] = '道具['.($v['propid']==3 ? '大师套装' : '富豪套装').']';
	//if (other) ;
}
$status = array('0'=>'未发','1'=>'已发','2'=>'已用');
$where = " WHERE 1 = 1";
if ( $cateid != 'all' ) {
	$where.= " AND `cateid` = $cateid";// . " AND `status` > 0";
}
$order = " GROUP BY `cateid`, `status` ";
$sql = "SELECT `cateid`, `status`, COUNT(*) as num  FROM `lord_game_activation` $where $order";
$row = $db->query($sql)->fetchAll();
$row = ( $row && is_array($row) ) ? $row : array();
$list = array();
foreach ( $row as $k => $v )
{
	$list[$v['cateid']]['编号'] = $v['cateid'];
	$list[$v['cateid']]['名称'] = !isset($cates[$v['cateid']]) ? ('编号'.$v['cateid']) : $cates[$v['cateid']];
	$list[$v['cateid']][$status[$v['status']]] = $v['num'] + 0;
}
foreach ( $list as $k => $v )
{
	$list[$k]['内含'] = isset($contents[$k]) ? join(', ', $contents[$k]) : '&nbsp;';
	$list[$k]['未发'] = $v['未发'] = isset($v['未发']) ? $v['未发'] : 0;
	$list[$k]['已发'] = $v['已发'] = isset($v['已发']) ? $v['已发'] : 0;
	$list[$k]['已用'] = $v['已用'] = isset($v['已用']) ? $v['已用'] : 0;
	$list[$k]['总数'] = $v['总数'] = $v['未发'] + $v['已发'] + $v['已用'];
	$list[$k]['发放'] = $v['发放'] = $v['已发'] + $v['已用'];
	$list[$k]['激活率'] = $v['激活率'] = ($v['发放'] ? round($v['已用'] * 100 / $v['发放']) : '0') . '%';
}

?>
<script>
// $(function(){
// 	var cateid = '<?=$_POST['cateid']?>';
// 	var start = '<?=$_POST['start']?>';
// 	var end = '<?=$_POST['end']?>';
// 	$("#cateid").val(cateid);
// 	$("#start").val(start);
// 	$("#end").val(end);
// });
</script>
<body>
<div style="padding:0 10px;">
	<fieldset>
		<legend>活动礼包发放及激活情况</legend>
		<form method="post">
			<div class="row">
<!--
				<div class="span2" >
					<label>礼包名称：</label>
					<select class="span2" id="cateid" name="cateid">
						<option value="all">全部</option>
						<?php
						foreach ($cates as $id => $name ) {
							echo "<option value='$id'>$name</option>";
						}
						?>
					</select>
				</div>
 				<div class="span2">
					<label>日期：</label>
					<input style="height:30px;" class="span2" type="text" id="start" name="start" class="textbox dtime" readonly onclick="WdatePicker({dateFmt:'yyyy-MM-dd'})"/>
				</div>
 				<div class="span2">
					<label>-</label>
					<input style="height:30px;" class="span2" type="text" id="end" name="end" class="textbox dtime" readonly onclick="WdatePicker({dateFmt:'yyyy-MM-dd'})"/>
				</div>
				<div span="span1" style="float:right;">
					<label>&nbsp;</label>
					<input type="submit" value="查&nbsp;&nbsp;询" class="btn" />
				</div>
-->
			</div>
		</form>
	</fieldset>

	<table class="table table-bordered table-condensed table-hover">
		<tr class="info">
			<td nowrap><strong>编号</strong></td>
			<td nowrap><strong>名称</strong></td>
			<td nowrap><strong>内含</strong></td>
			<td nowrap><strong>发放数</strong></td>
			<td nowrap><strong>未用数</strong></td>
			<td nowrap><strong>已用数</strong></td>
			<td nowrap><strong>激活率</strong></td>
		</tr>
		<?php
		foreach ( $list as $val ) {
		?>
		<tr>
			<td><?=$val['编号']?></td>
			<td><?=$val['名称']?></td>
			<td><?=$val['内含']?></td>
			<td><?=$val['发放']?></td>
			<td><?=$val['已发']?></td>
			<td><?=$val['已用']?></td>
			<td><?=$val['激活率']?></td>
		</tr>
		<?php
		}
		?>
	</table>

</div>
</body>
