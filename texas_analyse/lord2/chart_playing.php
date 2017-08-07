<!DOCTYPE html>
<?php
require_once '../include/database.class.php';

$type = isset($_REQUEST['type'])?$_REQUEST['type']:'hour';
$date = isset($_REQUEST['date'])?$_REQUEST['date']:date("Y-m-d");
$start = isset($_REQUEST['start'])?$_REQUEST['start']:date("Y-m-d",(strtotime($date)-30*3600*24));
if($type == "day"){
	$sql = "select sum(playing) as snum,count(*) as cn,date(add_time) as dd from lord_online where add_time >= '".$start."' and add_time<'".$date." 23:59:59' group by dd order by dd desc"; 
	$row = $db -> query($sql) -> fetchAll();
	foreach($row as $val){
		$a[] = array($val['dd'],intval(floor($val['snum']/$val['cn'])));
	}
	if(count($a)<14){
		$day = $row[0]['dd'];$all = 14-count($a);
		for($i=1;$i<$all;$i++){
			$day = date("Y-m-d",(strtotime($day)+24*3600));
			$a[] = array($day,'-');
		}
	}
	//新版
	$startid = str_replace('-', '', $start);
	$endid = str_replace('-', '', $date);
	$sql = "select sum(`allInTableActive`) as snum,count(*) as cn,dateid as dd from lord_online_detail where dateid >= $startid and dateid< $endid group by dd order by dd desc"; 
	$row = $db -> query($sql) -> fetchAll();
	foreach($row as $val){
		$dd = str_split($val['dd'], 2);
		$b[] = array($dd[0].$dd[1].'-'.$dd[2].'-'.$dd[3], intval(floor($val['snum']/$val['cn'])));
	}
	if ( count($b)<14 ) {
		$day = $row[0]['dd'];$all = 14-count($b);
		$dd = str_split($day, 2);
		$day = $dd[0].$dd[1].'-'.$dd[2].'-'.$dd[3];
		for($i=1;$i<$all;$i++){
			$day = date("Y-m-d",(strtotime($day)+24*3600));
			$b[] = array($day,'-');
		}
	}
}else if($type == "hour"){
	$sql = "select * from lord_online where date(add_time) = '".$date."'"; 
	$row = $db -> query($sql) -> fetchAll();
	foreach($row as $val){
		$a[] = array(date("H:i",strtotime($val['add_time'])),intval($val['playing']));
	}
	$a[] = array(date("H").':55','-');
	$date2 = date("Y-m-d",strtotime($date)-24*3600);
	$last_date = date('Y-m-d H:55:00',time()-24*3600);
	$sql = "select * from lord_online where date(add_time) = '".$date2."' AND add_time <= '".$last_date."'"; 
	$row = $db -> query($sql) -> fetchAll();
	foreach($row as $val){
		$a2[] = array(date("H:i",strtotime($val['add_time'])),intval($val['playing']));
	}
	$a2[] = array(date("H").':55','-');
	$date3 = date("Y-m-d",strtotime($date)-7*24*3600);
	$last_date = date('Y-m-d H:55:00',time()-7*24*3600);
	$sql = "select * from lord_online where date(add_time) = '".$date3."' AND add_time <= '".$last_date."'";  
	$row = $db -> query($sql) -> fetchAll();
	foreach($row as $val){
		$a3[] = array(date("H:i",strtotime($val['add_time'])),intval($val['playing']));
	}
	$a3[] = array(date("H").':55','-');
	//新版
	$dateid = str_replace('-', '', $date);
	$sql = "select * from lord_online_detail where dateid = $dateid";
	$row = $db -> query($sql) -> fetchAll();
	foreach($row as $val){
		$b[] = array(date("H:i",$val['ut']),intval($val['allInTableActive']));
	}
	// $b[] = array(date("H").':55','-');
	$dateid2 = date("Ymd",strtotime($date)-24*3600);
	$sql = "select * from lord_online_detail where dateid = $dateid2";
	$row = $db -> query($sql) -> fetchAll();
	foreach($row as $val){
		$b2[] = array(date("H:i",$val['ut']),intval($val['allInTableActive']));
	}
	// $b2[] = array(date("H").':55','-');
	$dateid3 = date("Ymd",strtotime($date)-7*24*3600);
	$sql = "select * from lord_online_detail where dateid = $dateid3";
	$row = $db -> query($sql) -> fetchAll();
	foreach($row as $val){
		$b3[] = array(date("H:i",$val['ut']),intval($val['allInTableActive']));
	}
	// $b3[] = array(date("H").':55','-');
}

?>
<html>
<head>
	<title>图表 - 活跃用户 - 日线</title>
	<link class="include" rel="stylesheet" type="text/css" href="./dist/jquery.jqplot.min.css" />
	<!--[if lt IE 9]><script language="javascript" type="text/javascript" src="./dist/excanvas.js"></script><![endif]-->
	<script class="include" type="text/javascript" src="./dist/jquery.min.js"></script>
	<script src="../js/My97DatePicker/WdatePicker.js" language="javascript"></script>
</head>
<body style="padding:0;margin:0;">
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
		</div>
	</form>
</div>
 
<script class="code" type="text/javascript">
$(document).ready(function(){
	var formatValue = "<?=$type=='hour'?'%R':'%x'?>";
	$("#date").val("<?=$date?>");
	var lns, lbs, plot1, plot2;
	lns = [], lbs = [];
	<?php if (count($a3)>1){ ?>lns.push(<?php echo (json_encode($a3));?>);lbs.push('上周');<?php } ?>
	<?php if (count($a2)>1){ ?>lns.push(<?php echo (json_encode($a2));?>);lbs.push('昨日');<?php } ?>
	lns.push(<?php echo (json_encode($a));?>);lbs.push('当日');
	plot1 = $.jqplot( 'chart1', lns, {
		title:'活跃用户 - 日线',
		legend:{
			show:true, 
			location: 'ne',
			labels: lbs ,
			rendererOptions:{ placement: "outside"}
		},
		seriesDefaults: {
			show: true,     // wether to render the series. 
			lineWidth: 1,	// Width of the line in pixels. 
			markerOptions: { 
				style: 'filledCircle',  // circle, diamond, square, filledCircle. 
				lineWidth: 3,		   // width of the stroke drawing the marker.
				size: 5            // size (diameter, edge length, etc.) of the marker.
			}
		},
		axes:{
			xaxis:{
				renderer:$.jqplot.DateAxisRenderer,
				tickOptions:{
					// formatString:'%b&nbsp;%#d'
					formatString:formatValue
				} 
			}
		},
		highlighter: {
			show: true,
			sizeAdjust: 1
		},
		cursor: {
			show: false
		}
	});
	lns = [], lbs = [];
	<?php if (count($b3)>1){ ?>lns.push(<?php echo (json_encode($b3));?>);lbs.push('上周');<?php } ?>
	<?php if (count($b2)>1){ ?>lns.push(<?php echo (json_encode($b2));?>);lbs.push('昨日');<?php } ?>
	lns.push(<?php echo (json_encode($b));?>);lbs.push('当日');
	plot2 = $.jqplot( 'chart2', lns, {
		title:'活跃用户 - 日线(新)',
		legend:{
			show:true, 
			location: 'ne',
			labels: lbs ,
			rendererOptions:{ placement: "outside"}
		},
		seriesDefaults: {
			show: true,     // wether to render the series. 
			lineWidth: 1,	// Width of the line in pixels. 
			markerOptions: { 
				style: 'filledCircle',  // circle, diamond, square, filledCircle. 
				lineWidth: 1.5,		   // width of the stroke drawing the marker.
				size: 3            // size (diameter, edge length, etc.) of the marker.
			}
		},
		axes:{
			xaxis:{
				renderer:$.jqplot.DateAxisRenderer,
				tickOptions:{
					// formatString:'%b&nbsp;%#d'
					formatString:formatValue
				} 
			}
		},
		highlighter: {
			show: true,
			sizeAdjust: 1
		},
		cursor: {
			show: false
		}
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
