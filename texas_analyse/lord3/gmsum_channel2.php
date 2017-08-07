<?php
require_once '../manage/checkPriv.php';
require_once '../include/database.class.php';
?>
<script type="text/javascript" src="../js/jquery.js"></script>
<script src="../js/My97DatePicker/WdatePicker.js" language="javascript"></script>
<link rel="stylesheet" type="text/css" href="../bootstrap/css/bootstrap.min.css" />
<script>
<?php
$type = isset($_POST['type']) ? $_POST['type'] : "all";
$channel = isset($_POST['channel']) ? $_POST['channel'] : "all";
$start = ( isset($_POST['start']) && $_POST['start'] != "" ) ? date("Y-m-d", strtotime($_POST['start'])) : date("Y-m-d", time());
$_POST['start'] = $start;
$start = intval(str_replace('-', '', $start));
$end = ( isset($_POST['end']) && $_POST['end'] != "" ) ? date("Y-m-d", strtotime($_POST['end'])) : date("Y-m-d", time());
$_POST['end'] = $end;
$end = intval(date("Ymd", strtotime($end) + 86400));
if ( intval(isset($_POST['isfresh']) && $_POST['isfresh']) ) {
	system(
		"php /alidata1/wwwroot/landlord/server3/cron/cron_total_channel.php"
	);
}
//shawn 20150417 comment 2 lines
// $where = "";
// $is_channel = 0;
//shawn 20150417 add 2 lines
$select = "*";
$where = "";
$groupby = "";
$is_channel = $is_durty = 0;
if ( $type != 'all' ) {
	$where.= " AND `is_tv` = $type";
}
if ( $channel != 'all' ) {
	$where.= " AND `channel` = '$channel'";
	$is_channel = 1;
}
if ( $start == $end ) {
	$where.= " AND `dateid` = $start";
}
elseif ( $channel != 'all' ) {
	$where.= " AND `dateid` >= $start AND `dateid` < $end";
}
else{
	//shawn 20150417 comment 3 lines
	// $where.= " AND `dateid` = $start";
	// $_POST['end'] = $_POST['start'] ;
	// $end = $start;
	//shawn 20150417 add 2 lines
	$select = "`id`, `is_tv`, `channel`, `dateid`, sum(`DNU`) DNU, sum(`DR1`) DR1, sum(`DR2`) DR2, sum(`DR6`) DR6, sum(`DAU`) DAU, sum(`DTU`) DTU, sum(`DNPU`) DNPU, sum(`DNPR`) DNPR, sum(`DPU`) DPU, sum(`DPA`) DPA, sum(`ARPPU`) ARPPU";
	$where.= " AND `dateid` >= $start AND `dateid` < $end";
	$groupby = 'GROUP BY `channel`';
	$is_durty = 1;
}
$order = $is_channel ? "ORDER BY `dateid` ASC" : "ORDER BY DNU DESC, DR1 DESC, DAU DESC";
$sql = "SELECT $select FROM `lord_total_channel` WHERE 1 = 1 $where $order $groupby";
$row = $db->query($sql)->fetchAll();
$row = ( $row && is_array($row) ) ? $row : array();

?>
$(function(){
	// var type = '<?=$_POST['type']?>';
	var channel = '<?=$_POST['channel']?>';
	var start = '<?=$_POST['start']?>';
	var end = '<?=$_POST['end']?>';
	// $("#type").val(type);
	$("#channel").val(channel);
	$("#start").val(start);
	$("#end").val(end);
});
</script>
<body>
<div style="padding:0 10px;">
	<fieldset>
		<legend>各渠道运营明细数据表</legend>
		<form method="post">
			<div class="row">
<!-- 
				<div class="span2">
					<label>站点：</label>
					<select class="span2" id="type" name="type">
						<option value="all">全部</option>
						<option value="1">TV</option>
						<option value="0">MP</option>
					</select>
				</div>
 -->
				<div class="span2" >
					<label>渠道：</label>
					<select class="span2" id="channel" name="channel">
						<option value="all">全部</option>
						<?php
						$file = __DIR__ . "/data/cache_channel";
						$sql = "select `channel` from `lord_game_user` where `channel` != '' group by `channel`";
						if ( is_file($file) && mt_rand(0, 10) ) {
							$channels = json_decode(file_get_contents($file), 1);
						}
						else {
							$channels = $db->query($sql)->fetchAll();
							$res = file_put_contents($file, json_encode($channels));
						}
						foreach ($channels as $val) {
							echo '<option value="'.$val['channel'].'">'.$val['channel'].'</option>';
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
				<div class="span2">
					<label>刷新今日数据</label>
					<label for="isfresh" style="margin-top:7px;"><input type="checkbox" id="isfresh" name="isfresh" style="margin:0;" />&nbsp;我知道这需要等待</label>
				</div>
				<div span="span1" style="float:right;">
					<label>&nbsp;</label>
					<input type="submit" value="查&nbsp;&nbsp;询" class="btn" />
				</div>
			</div>
		</form>
	</fieldset>

	<table class="table table-bordered table-condensed table-hover">
		<tr class="info">
			<td nowrap><strong><?=$is_channel?"统计时间":"渠道名称"?></strong></td>
			<td nowrap><strong>当日注册</strong></td>
			<td nowrap><strong>次日留存</strong></td>
			<td nowrap><strong>三日留存</strong></td>
			<td nowrap><strong>七日留存</strong></td>
			<td nowrap><strong>当日活跃</strong></td>
			<td nowrap><strong>当日转化</strong></td>
			<td nowrap><strong>当日注册当日付费</strong></td>
			<td nowrap><strong>当日注册付费转化率</strong></td>
			<td nowrap><strong>当日所有付费用户</strong></td>
			<td nowrap><strong>当日付费总额</strong></td>
			<td nowrap><strong>ARPPU</strong></td>
		</tr>
		<?php
		$a = $a_zc = $a_lc = $a_hy = $a_zh = $a_zcff = $a_zcffzh = $a_syff = $a_ffze = $a_arppu = 0;
		foreach ( $row as $val ) {
		?>
		<tr>
			<td><?=$is_channel?$val['dateid']:$val['channel']?></td>
			<td><?=$val['DNU']?></td>
			<td><?=$val['DR1']?></td>
			<td><?=isset($val['DR2'])?$val['DR2']:0?></td>
			<td><?=isset($val['DR6'])?$val['DR6']:0?></td>
			<td><?=$val['DAU']?></td>
			<td><?=$val['DTU']?></td>
			<td><?=$val['DNPU']?></td>
			<td><?=$val['DNPR']."%"?></td>
			<td><?=$val['DPU']?></td>
			<td><?=$val['DPA']?></td>
			<td><?=$val['ARPPU']?></td>
		</tr>
		<?php
			$a_zc += $val['DNU'];
			$a_lc2 += $val['DR1'];
			$a_lc3 += $val['DR2'];
			$a_lc7 += $val['DR6'];
			$a_hy += $val['DAU'];
			$a_zh += $val['DTU'];
			$a_zcff += $val['DNPU'];
			//$a_zcffzh = round($a_zcff * 100 / $a_zc, 2)."%";
			$a_syff += $val['DPU'];
			$a_ffze += $val['DPA'];
			//$a_arppu = round($a_ffze / $a_syff, 2);
		 }
		?>
		<tr class="info">
			<td>总计</td>
			<td><?=$a_zc?></td>
			<td><?=$a_lc2?></td>
			<td><?=$a_lc3?></td>
			<td><?=$a_lc7?></td>
			<td><?=$a_hy?></td>
			<td><?=$a_zh?></td>
			<td><?=$a_zcff?></td>
			<td><?=round($a_zcff * 100 / $a_zc, 2)."%"?></td>
			<td><?=$a_syff?></td>
			<td><?=$a_ffze?></td>
			<td><?=round($a_ffze / $a_syff, 2)?></td>
		</tr>
	</table>

</div>
</body>
