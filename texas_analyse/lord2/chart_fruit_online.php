<?php
require_once '../include/database.class.php';

$date0 = isset($_REQUEST['date']) ? $_REQUEST['date'] : date("Y-m-d");
$date1 = date("Y-m-d", strtotime($date0) - 86400 * 1);
$date7 = date("Y-m-d", strtotime($date0) - 86400 * 7);
$date30 = date("Y-m-d", strtotime($date0) - 86400 * 90);

$dd0=$date0;
$dd1=$date1;
$dd7=$date7;
/*
$dd0 = intval(str_replace('-', '', $date0));
$dd1 = intval(str_replace('-', '', $date1));
$dd7 = intval(str_replace('-', '', $date7));
$dd30 = intval(str_replace('-', '', $date30));
*/
//在线
$tag = 'num';

//当日
$dd0_playing = array();
$sql = "SELECT * FROM `lord_stat_fruit_online` WHERE `logday` = '$dd0'";
$rows = $db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
foreach ( $rows as $row ) { $dd0_playing[] = array(date("H:i", $row['time']), intval($row[$tag])); }
$dd0_playing[] = array('00:00','-');

//上日
$dd1_playing = array();
$sql = "SELECT * FROM `lord_stat_fruit_online` WHERE `logday` = '$dd1'";
$rows = $db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
foreach ( $rows as $row ) { $dd1_playing[] = array(date("H:i", $row['time']), intval($row[$tag])); }
$dd1_playing[] = array('00:00','-');

//上周
$dd7_playing = array();
$sql = "SELECT * FROM `lord_stat_fruit_online` WHERE `logday` = '$dd7'";
$rows = $db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
foreach ( $rows as $row ) { $dd7_playing[] = array(date("H:i", $row['time']), intval($row[$tag])); }
$dd7_playing[] = array('00:00','-');
?>
<!DOCTYPE html>
<html>
<head>
	<title>图表 - 水果机在线用户</title>
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
cid = 'chart1'; ttl = '在线用户 - 日线'; lns = []; lbs = []; stl = 'filledCircle'; fmt = '%R';
<?php if (count($dd7_playing)>1){ ?>lns.push(<?php echo (json_encode($dd7_playing));?>);lbs.push('上周');<?php } ?>
<?php if (count($dd1_playing)>1){ ?>lns.push(<?php echo (json_encode($dd1_playing));?>);lbs.push('上日');<?php } ?>
<?php if (count($dd0_playing)>1){ ?>lns.push(<?php echo (json_encode($dd0_playing));?>);lbs.push('当日');<?php } ?>
makeChart(cid, ttl, lns, lbs, stl, fmt);
//jquery ready end
});
</script>
<!-- plugins -->
<script class="include" type="text/javascript" src="./dist/jquery.jqplot.min.js"></script>
<script class="include" language="javascript" type="text/javascript" src="./dist/plugins/jqplot.highlighter.min.js"></script>
<script class="include" language="javascript" type="text/javascript" src="./dist/plugins/jqplot.cursor.min.js"></script>
<script class="include" language="javascript" type="text/javascript" src="./dist/plugins/jqplot.dateAxisRenderer.js"></script>
</body>
</html>
