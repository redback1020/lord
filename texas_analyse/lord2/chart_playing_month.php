<!DOCTYPE html>
<?php
require_once '../include/database.class.php';

$type = isset($_REQUEST['type'])?$_REQUEST['type']:'day';
$date = isset($_REQUEST['date'])?$_REQUEST['date']:date("Y-m-d");
$start = isset($_REQUEST['start'])?$_REQUEST['start']:date("Y-m-d",(strtotime($date)-30*3600*24));
if($type == "day"){
	$sql = "select sum(playing) as snum,count(*) as cn,date(add_time) as dd from lord_online where add_time >= '".$start."' and add_time<'".$date." 23:59:59' group by dd order by dd desc"; 
	$row = $db -> query($sql) -> fetchAll();
	foreach($row as $val){
		$a[] = array($val['dd'],intval(floor($val['snum']/$val['cn'])));
	}
	if(count($a)<14) {
		$day = $row[0]['dd'];
		$all = 12-count($a);
		for($i=1;$i<$all;$i++) {
			$day = date("Y-m-d",(strtotime($day)+24*3600));
			$a[] = array($day,'-');
		}
	}
	$sql = "select max(playing) as snum,count(*) as cn,date(add_time) as dd from lord_online where add_time >= '".$start."' and add_time<'".$date." 23:59:59' group by dd order by dd desc"; 
	$row = $db -> query($sql) -> fetchAll();
	foreach($row as $val){
		$b[] = array($val['dd'],intval($val['snum']));
	}
	if(count($b)<14) {
		$day = $row[0]['dd'];
		$all = 12-count($b);
		for($i=1;$i<$all;$i++) {
			$day = date("Y-m-d",(strtotime($day)+24*3600));
			$b[] = array($day,'-');
		}
	}
	//新版
	$startid = str_replace('-', '', $start);
	$endid = str_replace('-', '', $date);
	$sql = "select sum(`allInTableActive`) as snum,count(*) as cn,dateid as dd from lord_online_detail where dateid >= $startid and dateid< $endid group by dd order by dd desc"; 
	$row = $db -> query($sql) -> fetchAll();
	foreach($row as $val){
		$dd = str_split($val['dd'], 2);
		$a_[] = array($dd[0].$dd[1].'-'.$dd[2].'-'.$dd[3], intval(floor($val['snum']/$val['cn'])));
	}
	if ( count($a_)<14 ) {
		$day = $row[0]['dd'];$all = 14-count($a_);
		$dd = str_split($day, 2);
		$day = $dd[0].$dd[1].'-'.$dd[2].'-'.$dd[3];
		for($i=1;$i<$all;$i++){
			$day = date("Y-m-d",(strtotime($day)+24*3600));
			$a_[] = array($day,'-');
		}
	}
	$sql = "select max(`allInTableActive`) as snum,count(*) as cn,dateid as dd from lord_online_detail where dateid >= $startid and dateid< $endid group by dd order by dd desc";  
	$row = $db -> query($sql) -> fetchAll();
	foreach($row as $val){
		$dd = str_split($val['dd'], 2);
		$b_[] = array($dd[0].$dd[1].'-'.$dd[2].'-'.$dd[3], intval($val['snum']));
	}
	if ( count($b_)<14 ) {
		$day = $row[0]['dd'];$all = 14-count($b_);
		$dd = str_split($day, 2);
		$day = $dd[0].$dd[1].'-'.$dd[2].'-'.$dd[3];
		for($i=1;$i<$all;$i++){
			$day = date("Y-m-d",(strtotime($day)+24*3600));
			$b_[] = array($day,'-');
		}
	}
} 

?>
<html>
<head>
	<title>图表 - 活跃用户 - 月线</title>
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
				<label>日期范围：</label>
				<input style="height:25px;" value="<?=$start?>" class="span3" type="text" id="start" name="start" class="textbox dtime" readonly onclick="WdatePicker({dateFmt:'yyyy-MM-dd'})" />
				<input style="height:25px;" class="span3" type="text" id="date" name="date" class="textbox dtime" readonly onclick="WdatePicker({dateFmt:'yyyy-MM-dd'})" />
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
	var line1, line2, plot1, plot2;
    line1 = <?php echo (json_encode($a));?>;
    line2 = <?php echo (json_encode($b));?>;
	plot1 = $.jqplot('chart1', [line1,line2], {
		title:'活跃用户 - 月线',
		legend:{
			show:true, 
			location: 'ne',
			labels: ['月平均','月峰值'] ,
			rendererOptions:{ placement: "outside"}
		},
		seriesDefaults: {
			show: true,     // wether to render the series. 
			lineWidth: 1.5,	// Width of the line in pixels. 
			markerOptions: { 
				style: 'filledCircle',  // circle, diamond, square, filledCircle. 
				lineWidth: 1.5,		   // width of the stroke drawing the marker.
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
    line1 = <?php echo (json_encode($a_));?>;
    line2 = <?php echo (json_encode($b_));?>;
	plot2 = $.jqplot('chart2', [line1,line2], {
		title:'活跃用户 - 月线(新)',
		legend:{
			show:true, 
			location: 'ne',
			labels: ['月平均','月峰值'] ,
			rendererOptions:{ placement: "outside"}
		},
		seriesDefaults: {
			show: true,     // wether to render the series. 
			lineWidth: 1.5,	// Width of the line in pixels. 
			markerOptions: { 
				style: 'filledCircle',  // circle, diamond, square, filledCircle. 
				lineWidth: 1.5,		   // width of the stroke drawing the marker.
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
