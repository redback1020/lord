<?php
require_once '../include/database.class.php';
$date0 = isset($_REQUEST['date']) ? $_REQUEST['date'] : date("Y-m-d");
$date1 = date("Y-m-d", strtotime($date0) - 86400 * 1);
$date7 = date("Y-m-d", strtotime($date0) - 86400 * 7);
$date30 = date("Y-m-d", strtotime($date0) - 86400 * 30);
$dd0 = intval(str_replace('-', '', $date0));
$dd1 = intval(str_replace('-', '', $date1));
$dd7 = intval(str_replace('-', '', $date7));
$dd30 = intval(str_replace('-', '', $date30));

$dds = array(0,1,7);
$rooms = array(1000,1001,1002,1003,1007,1008,1009,1010,1004,3011,3012,3013);
$rooms = array(1007,1008,1009,1010);
foreach ( $dds as $dd )
{
	$sql = "SELECT * FROM `lord_online_detail` WHERE `dateid` = ".${"dd$dd"};
	$rows = $db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
	foreach ( $rows as $row )
	{
		foreach ( $rooms as $rd )
		{
			${"rd{$rd}playing{$dd}"}[] = array(date("H:i", $row['ut']), intval($row["room{$rd}TableActive"]));
			// ${"rd{$rd}playoff{$dd}"}[] = array(date("H:i", $row['ut']), intval($row["room{$rd}TableOffline"]));
		}
	}
	$rd = 1011;
	$sql = "SELECT * FROM `lord_online_room` WHERE `dateid` = ".${"dd$dd"}." AND `roomId` = $rd";
	$rows = $db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
	if ( ! $rows || ! is_array($rows) ) $rows = array();
	foreach ( $rows as $row )
	{
		${"rd{$rd}playing{$dd}"}[] = array(date("H:i", $row['ut']), intval($row["tableActive"]));
		// ${"rd{$rd}playoff{$dd}"}[] = array(date("H:i", $row['ut']), intval($row["room{$rd}TableOffline"]));
	}
}
?>
<!DOCTYPE html>
<html>
<head>
	<title>图表 - 赖子场活跃明细 - 日线</title>
	<link type="text/css" rel="stylesheet" href="./dist/jquery.jqplot.min.css" />
	<!--[if lt IE 9]><script type="text/javascript" src="./dist/excanvas.js"></script><![endif]-->
	<script type="text/javascript" src="./dist/jquery.min.js"></script>
	<script type="text/javascript" src="../js/My97DatePicker/WdatePicker.js"></script>
	<style type="text/css">.chart{height:400px; width:1360px;margin-bottom:20px;}</style>
</head>
<body style="padding:10px;margin:0;">
<form method="post">
	<div class="span3">
		<label>选择日期：</label>
		<input style="height:25px;" class="span3" type="text" id="date" name="date" class="textbox dtime" readonly onclick="WdatePicker({dateFmt:'yyyy-MM-dd'})"/>
		<input type="submit" value="查&nbsp;&nbsp;询" style="width:60px;height:30px; " />
	</div>
	<div id="chart1" class="chart"></div>
	<div id="chart2" class="chart"></div>
	<div id="chart3" class="chart"></div>
	<div id="chart4" class="chart"></div>
	<div id="chart5" class="chart"></div>
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
cid = 'chart1'; ttl = '活跃明细 - 赖子新手场'; lns = []; lbs = []; stl = 'filledCircle'; fmt = '%R';
<?php if (count($rd1007playing7)>1){ ?>lns.push(<?php echo (json_encode($rd1007playing7));?>);lbs.push('上周');<?php } ?>
<?php if (count($rd1007playing1)>1){ ?>lns.push(<?php echo (json_encode($rd1007playing1));?>);lbs.push('上日');<?php } ?>
<?php if (count($rd1007playing0)>1){ ?>lns.push(<?php echo (json_encode($rd1007playing0));?>);lbs.push('当日');<?php } ?>
makeChart(cid, ttl, lns, lbs, stl, fmt);
//chart2
cid = 'chart2'; ttl = '活跃明细 - 赖子初级场'; lns = []; lbs = []; stl = 'filledCircle'; fmt = '%R';
<?php if (count($rd1008playing7)>1){ ?>lns.push(<?php echo (json_encode($rd1008playing7));?>);lbs.push('上周');<?php } ?>
<?php if (count($rd1008playing1)>1){ ?>lns.push(<?php echo (json_encode($rd1008playing1));?>);lbs.push('上日');<?php } ?>
<?php if (count($rd1008playing0)>1){ ?>lns.push(<?php echo (json_encode($rd1008playing0));?>);lbs.push('当日');<?php } ?>
makeChart(cid, ttl, lns, lbs, stl, fmt);
//chart3
cid = 'chart3'; ttl = '活跃明细 - 赖子中级场'; lns = []; lbs = []; stl = 'filledCircle'; fmt = '%R';
<?php if (count($rd1009playing7)>1){ ?>lns.push(<?php echo (json_encode($rd1009playing7));?>);lbs.push('上周');<?php } ?>
<?php if (count($rd1009playing1)>1){ ?>lns.push(<?php echo (json_encode($rd1009playing1));?>);lbs.push('上日');<?php } ?>
<?php if (count($rd1009playing0)>1){ ?>lns.push(<?php echo (json_encode($rd1009playing0));?>);lbs.push('当日');<?php } ?>
makeChart(cid, ttl, lns, lbs, stl, fmt);
//chart4
cid = 'chart4'; ttl = '活跃明细 - 赖子高级场'; lns = []; lbs = []; stl = 'filledCircle'; fmt = '%R';
<?php if (count($rd1010playing7)>1){ ?>lns.push(<?php echo (json_encode($rd1010playing7));?>);lbs.push('上周');<?php } ?>
<?php if (count($rd1010playing1)>1){ ?>lns.push(<?php echo (json_encode($rd1010playing1));?>);lbs.push('上日');<?php } ?>
<?php if (count($rd1010playing0)>1){ ?>lns.push(<?php echo (json_encode($rd1010playing0));?>);lbs.push('当日');<?php } ?>
makeChart(cid, ttl, lns, lbs, stl, fmt);
//chart5
cid = 'chart5'; ttl = '活跃明细 - 赖子无限场'; lns = []; lbs = []; stl = 'filledCircle'; fmt = '%R';
<?php if (count($rd1011playing7)>1){ ?>lns.push(<?php echo (json_encode($rd1011playing7));?>);lbs.push('上周');<?php } ?>
<?php if (count($rd1011playing1)>1){ ?>lns.push(<?php echo (json_encode($rd1011playing1));?>);lbs.push('上日');<?php } ?>
<?php if (count($rd1011playing0)>1){ ?>lns.push(<?php echo (json_encode($rd1011playing0));?>);lbs.push('当日');<?php } ?>
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
