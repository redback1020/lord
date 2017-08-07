<?php
require_once '../include/database.class.php';

$date0 = isset($_REQUEST['date']) ? $_REQUEST['date'] : date("Y-m-d");
$date1 = date("Y-m-d", strtotime($date0) - 86400 * 1);
$date7 = date("Y-m-d", strtotime($date0) - 86400 * 7);
$date30 = date("Y-m-d", strtotime($date0) - 86400 * 90);
$dd0 = intval(str_replace('-', '', $date0));
$dd1 = intval(str_replace('-', '', $date1));
$dd7 = intval(str_replace('-', '', $date7));
$dd30 = intval(str_replace('-', '', $date30));

//活跃
$tag = 'allInTableActive';
$off = 'allInTableOffline';

//当日
$dd0_playing = $dd0_playoff = array();
$sql = "SELECT * FROM `lord_online_detail` WHERE `dateid` = $dd0";
$rows = $db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
foreach ( $rows as $row ) {
	$dd0_playing[] = array(date("H:i", $row['ut']), intval($row[$tag]));
	$dd0_playoff[] = array(date("H:i", $row['ut']), intval($row[$off]));
}
$dd0_playing[] = array('00:00','-');
$dd0_playoff[] = array('00:00','-');

//上日
$dd1_playing = $dd1_playoff = array();
$sql = "SELECT * FROM `lord_online_detail` WHERE `dateid` = $dd1";
$rows = $db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
foreach ( $rows as $row ) {
	$dd1_playing[] = array(date("H:i", $row['ut']), intval($row[$tag]));
	$dd1_playoff[] = array(date("H:i", $row['ut']), intval($row[$off]));
}
$dd1_playing[] = array('00:00','-');
$dd1_playoff[] = array('00:00','-');

//上周
$dd7_playing = $dd7_playoff = array();
$sql = "SELECT * FROM `lord_online_detail` WHERE `dateid` = $dd7";
$rows = $db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
foreach ( $rows as $row ) {
	$dd7_playing[] = array(date("H:i", $row['ut']), intval($row[$tag]));
	$dd7_playoff[] = array(date("H:i", $row['ut']), intval($row[$off]));
}
$dd7_playing[] = array('00:00','-');
$dd7_playoff[] = array('00:00','-');

//月平均
$a30_playing = $a30_playoff = array();
$sql = "SELECT SUM(`$tag`) AS ssum, COUNT(*) AS snum, `dateid` AS dd FROM `lord_online_detail` WHERE `dateid` >= $dd30 AND dateid <= $dd0 GROUP BY dd ORDER BY dd DESC";
$rows = $db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
foreach ( $rows as $row ) {
	$dds = str_split($row['dd'], 2);
	$a30_playing[] = array($dds[0].$dds[1].'-'.$dds[2].'-'.$dds[3], intval(floor($row['ssum']/$row['snum'])));
}
if ( count($a30_playing) < 14 ) {
	$day = $rows[0]['dd']; $all = 14 - count($a30_playing);
	$dds = str_split($day, 2);
	$day = $dds[0].$dds[1].'-'.$dds[2].'-'.$dds[3];
	for ( $i = 1; $i < $all; $i++ ) {
		$day = date("Y-m-d", strtotime($day)+86400);
		$a30_playing[] = array($day, '-');
	}
}
$a30_playing[] = array(date('Y-m-d', strtotime("+1 day")),'-');
$sql = "SELECT SUM(`$off`) AS ssum, COUNT(*) AS snum, `dateid` AS dd FROM `lord_online_detail` WHERE `dateid` >= $dd30 AND dateid <= $dd0 GROUP BY dd ORDER BY dd DESC";
$rows = $db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
foreach ( $rows as $row ) {
	$dds = str_split($row['dd'], 2);
	$a30_playoff[] = array($dds[0].$dds[1].'-'.$dds[2].'-'.$dds[3], intval(floor($row['ssum']/$row['snum'])));
}
if ( count($a30_playoff) < 14 ) {
	$day = $rows[0]['dd']; $all = 14 - count($a30_playoff);
	$dds = str_split($day, 2);
	$day = $dds[0].$dds[1].'-'.$dds[2].'-'.$dds[3];
	for ( $i = 1; $i < $all; $i++ ) {
		$day = date("Y-m-d", strtotime($day)+86400);
		$a30_playoff[] = array($day, '-');
	}
}
$a30_playoff[] = array(date('Y-m-d', strtotime("+1 day")),'-');

//月峰值
$p30_playing = $p30_playoff = array();
$sql = "SELECT MAX(`$tag`) AS smax, COUNT(*) AS snum, `dateid` AS dd FROM `lord_online_detail` WHERE `dateid` >= $dd30 AND dateid <= $dd0 GROUP BY dd ORDER BY dd DESC";
$rows = $db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
foreach ( $rows as $row ) {
	$dds = str_split($row['dd'], 2);
	$p30_playing[] = array($dds[0].$dds[1].'-'.$dds[2].'-'.$dds[3], intval($row['smax']));
}
if ( count($p30_playing) < 14 ) {
	$day = $rows[0]['dd']; $all = 14 - count($p30_playing);
	$dds = str_split($day, 2);
	$day = $dds[0].$dds[1].'-'.$dds[2].'-'.$dds[3];
	for ( $i = 1; $i < $all; $i++ ) {
		$day = date("Y-m-d", strtotime($day)+86400);
		$p30_playing[] = array($day, '-');
	}
}
$p30_playing[] = array(date('Y-m-d', strtotime("+1 day")),'-');
$sql = "SELECT MAX(`$off`) AS smax, COUNT(*) AS snum, `dateid` AS dd FROM `lord_online_detail` WHERE `dateid` >= $dd30 AND dateid <= $dd0 GROUP BY dd ORDER BY dd DESC";
$rows = $db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
foreach ( $rows as $row ) {
	$dds = str_split($row['dd'], 2);
	$p30_playoff[] = array($dds[0].$dds[1].'-'.$dds[2].'-'.$dds[3], intval($row['smax']));
}
if ( count($p30_playoff) < 14 ) {
	$day = $rows[0]['dd']; $all = 14 - count($p30_playoff);
	$dds = str_split($day, 2);
	$day = $dds[0].$dds[1].'-'.$dds[2].'-'.$dds[3];
	for ( $i = 1; $i < $all; $i++ ) {
		$day = date("Y-m-d", strtotime($day)+86400);
		$p30_playoff[] = array($day, '-');
	}
}
$p30_playoff[] = array(date('Y-m-d', strtotime("+1 day")),'-');
?>
<!DOCTYPE html>
<html>
<head>
	<title>图表 - 活跃用户|在桌掉线 - 日线|月线</title>
	<link type="text/css" rel="stylesheet" href="./dist/jquery.jqplot.min.css" />
	<!--[if lt IE 9]><script type="text/javascript" src="./dist/excanvas.js"></script><![endif]-->
	<script type="text/javascript" src="./dist/jquery.min.js"></script>
	<script type="text/javascript" src="../js/My97DatePicker/WdatePicker.js"></script>
	<style type="text/css">.chart{height:400px; width:1360px;margin-bottom:20px;}</style>
</head>
<body style="padding:10px;margin:0;">
<form method="post">
	<div class="span3">
		<label>日期：</label>
		<input style="height:25px;" class="span3" type="text" id="date" name="date" class="textbox dtime" readonly onclick="WdatePicker({dateFmt:'yyyy-MM-dd'})"/>
		<input type="submit" value="查&nbsp;&nbsp;询" style="width:60px;height:30px; " />
	</div>
	<div id="chart1" class="chart"></div>
	<div id="chart2" class="chart"></div>
	<div id="chart3" class="chart"></div>
	<div id="chart4" class="chart"></div>
</form>
<script type="text/javascript">
$(document).ready(function(){
//jquery ready start
function makeChart(_id, _title, _lines, _labels, _style, _format)
{
	$.jqplot( _id, _lines, {
		title:_title,
		legend:{ show:true, location:'ne', labels:_labels, rendererOptions:{ placement:"inside" } },
		seriesDefaults:{ show:true, lineWidth:1, markerOptions:{ style:_style,/*circle,diamond,square,filledCircle*/ lineWidth:2, size:3 } },
		axes:{ xaxis:{ renderer:$.jqplot.DateAxisRenderer, tickOptions:{ formatString:_format/*formatString:'%b&nbsp;%#d'*/ } } },
		highlighter:{ show:true, sizeAdjust:1 },
		cursor:{ show:false }
	});
}
$("#date").val("<?=$date0?>");
var cid, ttl, lns, lbs, stl, fmt;
//chart1
cid = 'chart1'; ttl = '活跃用户 - 日线'; lns = []; lbs = []; stl = 'filledCircle'; fmt = '%R';
<?php if (count($dd7_playing)>1){ ?>lns.push(<?php echo (json_encode($dd7_playing));?>);lbs.push('上周');<?php } ?>
<?php if (count($dd1_playing)>1){ ?>lns.push(<?php echo (json_encode($dd1_playing));?>);lbs.push('上日');<?php } ?>
<?php if (count($dd0_playing)>1){ ?>lns.push(<?php echo (json_encode($dd0_playing));?>);lbs.push('当日');<?php } ?>
makeChart(cid, ttl, lns, lbs, stl, fmt);
//chart2
cid = 'chart2'; ttl = '在桌掉线 - 日线'; lns = []; lbs = []; stl = 'filledCircle'; fmt = '%R';
<?php if (count($dd7_playoff)>1){ ?>lns.push(<?php echo (json_encode($dd7_playoff));?>);lbs.push('上周');<?php } ?>
<?php if (count($dd1_playoff)>1){ ?>lns.push(<?php echo (json_encode($dd1_playoff));?>);lbs.push('上日');<?php } ?>
<?php if (count($dd0_playoff)>1){ ?>lns.push(<?php echo (json_encode($dd0_playoff));?>);lbs.push('当日');<?php } ?>
makeChart(cid, ttl, lns, lbs, stl, fmt);
//chart3
cid = 'chart3'; ttl = '活跃用户 - 月线'; lns = []; lbs = []; stl = 'circle'; fmt = '%m-%d';
<?php if (count($a30_playing)>1){ ?>lns.push(<?php echo (json_encode($a30_playing));?>);lbs.push('平均');<?php } ?>
<?php if (count($p30_playing)>1){ ?>lns.push(<?php echo (json_encode($p30_playing));?>);lbs.push('峰值');<?php } ?>
makeChart(cid, ttl, lns, lbs, stl, fmt);
//chart4
cid = 'chart4'; ttl = '在桌掉线 - 月线'; lns = []; lbs = []; stl = 'circle'; fmt = '%m-%d';
<?php if (count($a30_playoff)>1){ ?>lns.push(<?php echo (json_encode($a30_playoff));?>);lbs.push('平均');<?php } ?>
<?php if (count($p30_playoff)>1){ ?>lns.push(<?php echo (json_encode($p30_playoff));?>);lbs.push('峰值');<?php } ?>
makeChart(cid, ttl, lns, lbs, stl, fmt);
//jquery ready end
});
</script>
<!-- plugins -->
<script type="text/javascript" src="./dist/jquery.jqplot.min.js"></script>
<script type="text/javascript" src="./dist/plugins/jqplot.highlighter.min.js"></script>
<script type="text/javascript" src="./dist/plugins/jqplot.cursor.min.js"></script>
<script type="text/javascript" src="./dist/plugins/jqplot.dateAxisRenderer.js"></script>
</body>
</html>
