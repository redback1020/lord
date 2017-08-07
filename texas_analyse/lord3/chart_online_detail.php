<!DOCTYPE html>
<?php
require_once '../include/database.class.php';

$type = isset($_REQUEST['type'])?$_REQUEST['type']:'hour';
$date = isset($_REQUEST['date'])?$_REQUEST['date']:date("Y-m-d");

	$thas = $that = array();
	$ut_fr = strtotime(date("Y-m-d 00:00:00",strtotime($date)));
	$ut_to = $ut_fr + 86400;
	$dt_fr = date("Ymd", $ut_fr);
	$dt_to = date("Ymd", $ut_to);
	$sql = "select * from `lord_online_detail` where `dateid` >= $dt_fr and `dateid` < $dt_to";
	$marr = $db->query($sql)->fetchAll();
	$marr = $marr && is_array($marr) ? $marr : array();
	foreach ( $marr as $k => $v ) {
		if ( !($v['ut']%300) ) {
			$thas[]= $v;
		}
	}
	$ut_fr = $ut_fr - 86400;
	$ut_to = $ut_fr + 86400;
	$dt_fr = date("Ymd", $ut_fr);
	$dt_to = date("Ymd", $ut_to);
	$sql = "select * from `lord_online_detail` where `dateid` >= $dt_fr and `dateid` < $dt_to";
	$marr = $db->query($sql)->fetchAll();
	$marr = $marr && is_array($marr) ? $marr : array();
	foreach ( $marr as $k => $v ) {
		if ( !($v['ut']%300) ) {
			$that[]= $v;
		}
	}

	$c11 = $c21 = $c31 = $c41 = $c51 = $c61 = $c71 = $c81 = $c91 = $ca1 = $cb1 = $cc1 = $cd1 = $ce1 = array();
	foreach ($thas as $val) {
		$c11[] = array(date("H:i", $val['ut']), intval($val['allInTableActive']));
		$c21[] = array(date("H:i", $val['ut']), intval($val['allInTableRobot']));
		$c31[] = array(date("H:i", $val['ut']), intval($val['room0TableActive']));
		$c41[] = array(date("H:i", $val['ut']), intval($val['room0TableRobot']));
		$c51[] = array(date("H:i", $val['ut']), intval($val['room1TableActive']));
		$c61[] = array(date("H:i", $val['ut']), intval($val['room1TableRobot']));
		$c71[] = array(date("H:i", $val['ut']), intval($val['room2TableActive']));
		$c81[] = array(date("H:i", $val['ut']), intval($val['room2TableRobot']));
		$c91[] = array(date("H:i", $val['ut']), intval($val['room3TableActive']));
		$ca1[] = array(date("H:i", $val['ut']), intval($val['room3TableRobot']));
		$cb1[] = array(date("H:i", $val['ut']), intval($val['room4TableActive']));
		$cc1[] = array(date("H:i", $val['ut']), intval($val['room4TableRobot']));
		$cd1[] = array(date("H:i", $val['ut']), intval($val['room6TableActive']));
		$ce1[] = array(date("H:i", $val['ut']), intval($val['room6TableRobot']));
	}
	$c11[] = array(date("H").':55', '-');
	$c21[] = array(date("H").':55', '-');
	$c31[] = array(date("H").':55', '-');
	$c41[] = array(date("H").':55', '-');
	$c51[] = array(date("H").':55', '-');
	$c61[] = array(date("H").':55', '-');
	$c71[] = array(date("H").':55', '-');
	$c81[] = array(date("H").':55', '-');
	$c91[] = array(date("H").':55', '-');
	$ca1[] = array(date("H").':55', '-');
	$cb1[] = array(date("H").':55', '-');
	$cc1[] = array(date("H").':55', '-');
	$cd1[] = array(date("H").':55', '-');
	$ce1[] = array(date("H").':55', '-');
	$c12 = $c22 = $c32 = $c42 = $c52 = $c62 = $c72 = $c82 = $c92 = $ca2 = $cb2 = $cc2 = $cd2 = $ce2 = array();
	foreach ($that as $val) {
		$c12[] = array(date("H:i", $val['ut']), intval($val['allInTableActive']));
		$c22[] = array(date("H:i", $val['ut']), intval($val['allInTableRobot']));
		$c32[] = array(date("H:i", $val['ut']), intval($val['room0TableActive']));
		$c42[] = array(date("H:i", $val['ut']), intval($val['room0TableRobot']));
		$c52[] = array(date("H:i", $val['ut']), intval($val['room1TableActive']));
		$c62[] = array(date("H:i", $val['ut']), intval($val['room1TableRobot']));
		$c72[] = array(date("H:i", $val['ut']), intval($val['room2TableActive']));
		$c82[] = array(date("H:i", $val['ut']), intval($val['room2TableRobot']));
		$c92[] = array(date("H:i", $val['ut']), intval($val['room3TableActive']));
		$ca2[] = array(date("H:i", $val['ut']), intval($val['room3TableRobot']));
		$cb2[] = array(date("H:i", $val['ut']), intval($val['room4TableActive']));
		$cc2[] = array(date("H:i", $val['ut']), intval($val['room4TableRobot']));
		$cd2[] = array(date("H:i", $val['ut']), intval($val['room6TableActive']));
		$ce2[] = array(date("H:i", $val['ut']), intval($val['room6TableRobot']));
	}
	$c12[] = array(date("H").':55', '-');
	$c22[] = array(date("H").':55', '-');
	$c32[] = array(date("H").':55', '-');
	$c42[] = array(date("H").':55', '-');
	$c52[] = array(date("H").':55', '-');
	$c62[] = array(date("H").':55', '-');
	$c72[] = array(date("H").':55', '-');
	$c82[] = array(date("H").':55', '-');
	$c92[] = array(date("H").':55', '-');
	$ca2[] = array(date("H").':55', '-');
	$cb2[] = array(date("H").':55', '-');
	$cc2[] = array(date("H").':55', '-');
	$cd2[] = array(date("H").':55', '-');
	$ce2[] = array(date("H").':55', '-');


?>
<html>
<head>
	<title>图表 - 在线明细 - 日线</title>
	<link class="include" rel="stylesheet" type="text/css" href="./dist/jquery.jqplot.min.css" />
	<!--[if lt IE 9]><script language="javascript" type="text/javascript" src="./dist/excanvas.js"></script><![endif]-->
	<script class="include" type="text/javascript" src="./dist/jquery.min.js"></script>
	<script src="../js/My97DatePicker/WdatePicker.js" language="javascript"></script>
</head>
<body style="padding:0;margin:0;height:500px;overflow:auto;">
<div>
	<form method="post">
		<div >
			<div class="span3">
				<label>选择日期：</label>
				<input style="height:25px;" class="span3" type="text" id="date" name="date" class="textbox dtime" readonly onclick="WdatePicker({dateFmt:'yyyy-MM-dd'})"/>

				<input type="submit" value="查&nbsp;&nbsp;询" style="width:60px;height:30px; " />
			</div>
			<div id="chart1" style="height:400px; width:1400px;margin-bottom:20px;"></div>
			<div id="chart2" style="height:400px; width:1400px;margin-bottom:20px;"></div>
			<div id="chart3" style="height:400px; width:1400px;margin-bottom:20px;"></div>
			<div id="chart7" style="height:400px; width:1400px;margin-bottom:20px;"></div>
			<div id="chart4" style="height:400px; width:1400px;margin-bottom:20px;"></div>
			<div id="chart5" style="height:400px; width:1400px;margin-bottom:20px;"></div>
			<div id="chart6" style="height:400px; width:1400px;margin-bottom:20px;"></div>
		</div>
	</form>
</div>

<script class="code" type="text/javascript">
$(document).ready(function(){
	var formatValue = "<?=$type=='hour'?'%R':'%x'?>";
	$("#date").val("<?=$date?>");
	var cid, ttl, lns, lbs;

	cid = 'chart1'; ttl = '在线明细 - 全服总览'; lns = []; lbs = [];
	<?php if (count($c22)>1){ ?>lns.push(<?php echo (json_encode($c22));?>);lbs.push('昨日假人');<?php } ?>
	<?php if (count($c21)>1){ ?>lns.push(<?php echo (json_encode($c21));?>);lbs.push('当日假人');<?php } ?>
	<?php if (count($c12)>1){ ?>lns.push(<?php echo (json_encode($c12));?>);lbs.push('昨日活跃');<?php } ?>
	lns.push(<?php echo (json_encode($c11));?>);lbs.push('当日活跃');
	$.jqplot( cid, lns, {
		title: ttl,
		legend: { show:true, location: 'ne', labels: lbs, rendererOptions:{ placement: "outside"} },
		seriesDefaults: { show: true, lineWidth: 1.5, markerOptions: { style: 'filledCircle', lineWidth: 1.5, size: 5 } },
		axes: { xaxis: { renderer: $.jqplot.DateAxisRenderer, tickOptions: { formatString:formatValue } } },
		highlighter: { show: true, sizeAdjust: 1 },
		cursor: { show: false }
	});

	cid = 'chart2'; ttl = '在线明细 - 新手场'; lns = []; lbs = [];
	<?php if (count($c42)>1){ ?>lns.push(<?php echo (json_encode($c42));?>);lbs.push('昨日假人');<?php } ?>
	<?php if (count($c41)>1){ ?>lns.push(<?php echo (json_encode($c41));?>);lbs.push('当日假人');<?php } ?>
	<?php if (count($c32)>1){ ?>lns.push(<?php echo (json_encode($c32));?>);lbs.push('昨日活跃');<?php } ?>
	lns.push(<?php echo (json_encode($c31));?>);lbs.push('当日活跃');
	$.jqplot( cid, lns, {
		title: ttl,
		legend: { show:true, location: 'ne', labels: lbs, rendererOptions:{ placement: "outside"} },
		seriesDefaults: { show: true, lineWidth: 1.5, markerOptions: { style: 'filledCircle', lineWidth: 1.5, size: 5 } },
		axes: { xaxis: { renderer: $.jqplot.DateAxisRenderer, tickOptions: { formatString:formatValue } } },
		highlighter: { show: true, sizeAdjust: 1 },
		cursor: { show: false }
	});

	cid = 'chart3'; ttl = '在线明细 - 初级场'; lns = []; lbs = [];
	<?php if (count($c62)>1){ ?>lns.push(<?php echo (json_encode($c62));?>);lbs.push('昨日假人');<?php } ?>
	<?php if (count($c61)>1){ ?>lns.push(<?php echo (json_encode($c61));?>);lbs.push('当日假人');<?php } ?>
	<?php if (count($c52)>1){ ?>lns.push(<?php echo (json_encode($c52));?>);lbs.push('昨日活跃');<?php } ?>
	lns.push(<?php echo (json_encode($c51));?>);lbs.push('当日活跃');
	$.jqplot( cid, lns, {
		title: ttl,
		legend: { show:true, location: 'ne', labels: lbs, rendererOptions:{ placement: "outside"} },
		seriesDefaults: { show: true, lineWidth: 1.5, markerOptions: { style: 'filledCircle', lineWidth: 1.5, size: 5 } },
		axes: { xaxis: { renderer: $.jqplot.DateAxisRenderer, tickOptions: { formatString:formatValue } } },
		highlighter: { show: true, sizeAdjust: 1 },
		cursor: { show: false }
	});


	cid = 'chart7'; ttl = '在线明细 - 进阶场'; lns = []; lbs = [];
	<?php if (count($ce2)>1){ ?>lns.push(<?php echo (json_encode($ce2));?>);lbs.push('昨日假人');<?php } ?>
	<?php if (count($ce1)>1){ ?>lns.push(<?php echo (json_encode($ce1));?>);lbs.push('当日假人');<?php } ?>
	<?php if (count($cd2)>1){ ?>lns.push(<?php echo (json_encode($cd2));?>);lbs.push('昨日活跃');<?php } ?>
	lns.push(<?php echo (json_encode($cd1));?>);lbs.push('当日活跃');
	$.jqplot( cid, lns, {
		title: ttl,
		legend: { show:true, location: 'ne', labels: lbs, rendererOptions:{ placement: "outside"} },
		seriesDefaults: { show: true, lineWidth: 1.5, markerOptions: { style: 'filledCircle', lineWidth: 1.5, size: 5 } },
		axes: { xaxis: { renderer: $.jqplot.DateAxisRenderer, tickOptions: { formatString:formatValue } } },
		highlighter: { show: true, sizeAdjust: 1 },
		cursor: { show: false }
	});


	cid = 'chart4'; ttl = '在线明细 - 中级场'; lns = []; lbs = [];
	<?php if (count($c82)>1){ ?>lns.push(<?php echo (json_encode($c82));?>);lbs.push('昨日假人');<?php } ?>
	<?php if (count($c81)>1){ ?>lns.push(<?php echo (json_encode($c81));?>);lbs.push('当日假人');<?php } ?>
	<?php if (count($c72)>1){ ?>lns.push(<?php echo (json_encode($c72));?>);lbs.push('昨日活跃');<?php } ?>
	lns.push(<?php echo (json_encode($c71));?>);lbs.push('当日活跃');
	$.jqplot( cid, lns, {
		title: ttl,
		legend: { show:true, location: 'ne', labels: lbs, rendererOptions:{ placement: "outside"} },
		seriesDefaults: { show: true, lineWidth: 1.5, markerOptions: { style: 'filledCircle', lineWidth: 1.5, size: 5 } },
		axes: { xaxis: { renderer: $.jqplot.DateAxisRenderer, tickOptions: { formatString:formatValue } } },
		highlighter: { show: true, sizeAdjust: 1 },
		cursor: { show: false }
	});


	cid = 'chart5'; ttl = '在线明细 - 高级场'; lns = []; lbs = [];
	<?php if (count($ca2)>1){ ?>lns.push(<?php echo (json_encode($ca2));?>);lbs.push('昨日假人');<?php } ?>
	<?php if (count($ca1)>1){ ?>lns.push(<?php echo (json_encode($ca1));?>);lbs.push('当日假人');<?php } ?>
	<?php if (count($c92)>1){ ?>lns.push(<?php echo (json_encode($c92));?>);lbs.push('昨日活跃');<?php } ?>
	lns.push(<?php echo (json_encode($c91));?>);lbs.push('当日活跃');
	$.jqplot( cid, lns, {
		title: ttl,
		legend: { show:true, location: 'ne', labels: lbs, rendererOptions:{ placement: "outside"} },
		seriesDefaults: { show: true, lineWidth: 1.5, markerOptions: { style: 'filledCircle', lineWidth: 1.5, size: 5 } },
		axes: { xaxis: { renderer: $.jqplot.DateAxisRenderer, tickOptions: { formatString:formatValue } } },
		highlighter: { show: true, sizeAdjust: 1 },
		cursor: { show: false }
	});


	cid = 'chart6'; ttl = '在线明细 - 竞技场'; lns = []; lbs = [];
	<?php if (count($cc2)>1){ ?>lns.push(<?php echo (json_encode($cc2));?>);lbs.push('昨日假人');<?php } ?>
	<?php if (count($cc1)>1){ ?>lns.push(<?php echo (json_encode($cc1));?>);lbs.push('当日假人');<?php } ?>
	<?php if (count($cb2)>1){ ?>lns.push(<?php echo (json_encode($cb2));?>);lbs.push('昨日活跃');<?php } ?>
	lns.push(<?php echo (json_encode($cb1));?>);lbs.push('当日活跃');
	$.jqplot( cid, lns, {
		title: ttl,
		legend: { show:true, location: 'ne', labels: lbs, rendererOptions:{ placement: "outside"} },
		seriesDefaults: { show: true, lineWidth: 1.5, markerOptions: { style: 'filledCircle', lineWidth: 1.5, size: 5 } },
		axes: { xaxis: { renderer: $.jqplot.DateAxisRenderer, tickOptions: { formatString:formatValue } } },
		highlighter: { show: true, sizeAdjust: 1 },
		cursor: { show: false }
	});

});
</script>



<!-- End example scripts -->

<!-- Don't touch this! -->


    <script class="include" type="text/javascript" src="./dist/jquery.jqplot.min.js"></script>

<!-- End Don't touch this! -->

<!-- Additional plugins go here -->

    <script class="include" language="javascript" type="text/javascript" src="./dist/plugins/jqplot.highlighter.min.js"></script>
    <script class="include" language="javascript" type="text/javascript" src="./dist/plugins/jqplot.cursor.min.js"></script>
    <script class="include" language="javascript" type="text/javascript" src="./dist/plugins/jqplot.dateAxisRenderer.js"></script>

<!-- End additional plugins -->


</body>


</html>
