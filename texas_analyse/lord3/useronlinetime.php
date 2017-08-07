<!DOCTYPE html>
<?php

$sttime = microtime(1);
require_once '../include/database.class.php';
$entime = microtime(1)-$sttime;

echo '<div style="display:none;">';
echo $entime;
echo '</div>';

$pdo = new DB();
$db = $pdo->getDB();
$_REQUEST['type'] = 'day';
$_REQUEST['date'] = date('Y-m-d');
$type = $_REQUEST['type'];
$date = $_REQUEST['date'];
if($type == "day"){
	$start = isset($_REQUEST['start'])?$_REQUEST['start']:date("Y-m-d",(strtotime($date)-30*3600*24)); 
	$sql = "select sum(online_time) as snum,count(*) as cn, `date` as dd from lord_game_loginout where online_time > 60 and `date` >= '".$start."' and `date` < '".$date." 23:59:59' group by dd order by dd desc"; 
	$row = $db -> query($sql) -> fetchAll();
	foreach($row as $val){
		$a[] = array($val['dd'],intval($val['snum']/$val['cn']/60));
	}
	if(count($a)<12) {
		$day = $row[0]['dd'];
		$all = 12-count($a);
		for($i=1;$i<$all;$i++) {
			$day = date("Y-m-d",(strtotime($day)+24*3600));
			$a[] = array($day,'-');
		}
	}

	$sql = "select max(online_time) as snum,count(*) as cn, `date` as dd from lord_game_loginout where online_time > 60 and `date` >= '".$start."' and `date` < '".$date." 23:59:59' group by dd order by dd desc"; 
	$row = $db -> query($sql) -> fetchAll();
	foreach($row as $val){
		$b[] = array($val['dd'],intval($val['snum']/60));
	}
	if(count($b)<12) {
		$day = $row[0]['dd'];
		$all = 12-count($b);
		for($i=1;$i<$all;$i++) {
			$day = date("Y-m-d",(strtotime($day)+24*3600));
			$b[] = array($day,'-');
		}
	}
} 
$entime = microtime(1)-$sttime;

echo '<div style="display:none;">';
echo $entime;
echo '</div>';

?>
<html>
<head>
	<title>用户在线分析</title>
	<link class="include" rel="stylesheet" type="text/css" href="./dist/jquery.jqplot.min.css" />
	<!--[if lt IE 9]><script language="javascript" type="text/javascript" src="./dist/excanvas.js"></script><![endif]-->
	<script class="include" type="text/javascript" src="./dist/jquery.min.js"></script>
	<script src="../js/My97DatePicker/WdatePicker.js" language="javascript"></script>
</head>
<body>
<div class="">
	<form method="post">
		<div >
			<div class="span3">
				<label>日期：</label>
				<input style="height:25px;" value="<?=$start?>" class="span3" type="text" id="start" name="start" class="textbox dtime" readonly onclick="WdatePicker({dateFmt:'yyyy-MM-dd'})" />
				<input style="height:25px;" class="span3" type="text" id="date" name="date" class="textbox dtime" readonly onclick="WdatePicker({dateFmt:'yyyy-MM-dd'})" />
				<input type="submit" value="查&nbsp;&nbsp;询" style="width:60px;height:30px; " />
			</div>
			<div id="chart1" style="height:300px; width:1100px;"></div>
		</div>
	</form>
</div>
 
<script class="code" type="text/javascript">
$(document).ready(function(){
	var formatValue = "<?=$_REQUEST['type']=='hour'?'%R':'%x'?>";
	$("#date").val("<?=$_REQUEST['date']?>");
    var line1 = <?php echo (json_encode($a));?>;
    var line2 = <?php echo (json_encode($b));?>;
	var plot1 = $.jqplot('chart1', [line1,line2], 
		{
			title:'每天在线时长(分) 平均/峰值',
			legend:{
				show:true, 
				location: 'ne',
				labels: ['平均线','峰值线'] ,
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
		}
	);
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
