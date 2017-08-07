<!DOCTYPE html>
<?php

require_once '../include/database.class.php';

$_REQUEST['type'] = 'day';
$type = $_REQUEST['type'];
$date = ( isset($_REQUEST['date']) && strtotime($_REQUEST['date']) && !(strtotime($_REQUEST['date'])%86400) ) ? $_REQUEST['date'] : date('Y-m-d');
$_REQUEST['date'] = $date;
$start = ( isset($_REQUEST['start']) && strtotime($_REQUEST['start']) < strtotime($_REQUEST['date']) ) ? $_REQUEST['start'] : date("Y-m-d", strtotime($date)-86400*30); 
$_REQUEST['start'] = $start;
$ut_end = strtotime($date);
$ut_start = strtotime($start);
$is_today = intval( $ut_end == strtotime(date("Y-m-d")) );
$days = array();
for ( $day = $ut_start; $day < $ut_end; $day += 86400 ) { 
	$days[]= $day;
}

//数据缓存文件 在线平均
$file = __DIR__.'/data/cache_chart_online_time_a';
$data = is_file($file) ? json_decode(file_get_contents($file),1) : array();
// $data = array();
if ($is_today && $data) {
	unset($data[$ut_end]);
}
foreach ( $days as $k => $day )
{
	if ( !isset($data[$day]) ) {
		// $sql = "select sum(online_time) as snum,count(*) as cn, `date` as dd from lord_game_loginout where online_time > 60 and `date` >= '".date("Y-m-d", $day)."' and `date` < '".date("Y-m-d", $day+86400)."' group by dd order by dd desc"; 
		$sql = "select sum(online_time) as snum,count(id) as cn, `dateid` as dd from lord_game_loginout_201507 where online_time > 60 and `dateid` = ".intval(date("Ymd", $day))." group by dd order by dd desc"; 
		$row = $db -> query($sql) -> fetchAll();
		foreach($row as $val){
			// $data[$day] = array($val['dd'],intval($val['snum']/$val['cn']/60));
				if (!$val['dd']) continue;
				$dd = str_split($val['dd'],2);
				$data[$day] = array($dd[0].$dd[1].'-'.$dd[2].'-'.$dd[3],intval($val['snum']/$val['cn']/60));
		}
	}
}
$res = file_put_contents($file, json_encode($data));
foreach ( $data as $day => $v )
{
	if (!in_array($day, $days)) {
		unset($data[$day]);
	}
}
$a = array_values($data);

//数据缓存文件 在线峰值
$file = __DIR__.'/data/cache_chart_online_time_b';
$data = is_file($file) ? json_decode(file_get_contents($file),1) : array();
// $data = array();
if ($is_today && $data) {
	unset($data[$ut_end]);
}
foreach ( $days as $k => $day )
{
	if ( !isset($data[$day]) ) {
		// $sql = "select max(online_time) as snum,count(*) as cn, `date` as dd from lord_game_loginout where online_time > 60 and `date` >= '".date("Y-m-d", $day)."' and `date` < '".date("Y-m-d", $day+86400)."' group by dd order by dd desc"; 
		$sql = "select max(online_time) as snum,count(id) as cn, `dateid` as dd from lord_game_loginout_201507 where online_time > 60 and `dateid` = ".intval(date("Ymd", $day))." group by dd order by dd desc"; 
		$row = $db -> query($sql) -> fetchAll();
		foreach($row as $val){
			// $data[$day] = array($val['dd'],intval($val['snum']/60));
				if (!$val['dd']) continue;
				$dd = str_split($val['dd'],2);
				$data[$day] = array($dd[0].$dd[1].'-'.$dd[2].'-'.$dd[3],intval($val['snum']/60));
		}
	}
}
$res = file_put_contents($file, json_encode($data));
foreach ( $data as $day => $v )
{
	if (!in_array($day, $days)) {
		unset($data[$day]);
	}
}
$b = array_values($data);

// $type = isset($_REQUEST['type'])?$_REQUEST['type']:'day';
// $date = isset($_REQUEST['date'])?$_REQUEST['date']:date("Y-m-d");
// $start = isset($_REQUEST['start'])?$_REQUEST['start']:date("Y-m-d",(strtotime($date)-30*3600*24));
// $ut_start = strtotime($start);
// $ut_end = strtotime($date);
// $is_today = intval( $ut_end == strtotime(date("Y-m-d")) );
// $days = array();
// for ( $day = $ut_start; $day <= $ut_end; $day += 86400 ) { 
// 	$days[]= $day;
// }

// //数据缓存文件 在线平均
// $file = __DIR__.'/data/cache_chart_online_time_a';
// $data = is_file($file) ? json_decode(file_get_contents($file),1) : array();
// if ($is_today && $data) {
// 	unset($data[$ut_end]);
// }
// foreach ( $days as $k => $day )
// {
// 	if ( !isset($data[$day]) ) {
// 		$monthid = date('Ym',$day);
// 		$monthid = $monthid < 201506 ? "" : ("_".$monthid);
// 		if (!empty($monthid)) {
// 			$sql = "select sum(online_time) as snum,count(*) as cn, `dateid` as dd from lord_game_loginout{$monthid} where online_time > 60 and `dateid` = ".date("Ymd", $day); 
// 			$row = $db -> query($sql) -> fetchAll();
// 			foreach($row as $val){
// 				if (!$val['dd']) continue;
// 				$dd = str_split($val['dd'],2);
// 				$data[$day] = array($dd[0].$dd[1].'-'.$dd[2].'-'.$dd[3],intval($val['snum']/$val['cn']/60));
// 			}
// 		}else{
// 			$sql = "select sum(online_time) as snum,count(*) as cn, `date` as dd from lord_game_loginout where online_time > 60 and `date` = '".date("Y-m-d", $day)."'"; 
// 			$row = $db -> query($sql) -> fetchAll();
// 			foreach($row as $val){
// 				if (!$val['dd']) continue;
// 				$data[$day] = array($var['dd'],intval($val['snum']/$val['cn']/60));
// 			}
// 		}
// 	}
// }
// var_dump($data);
// $res = file_put_contents($file, json_encode($data));
// foreach ( $data as $day => $v )
// {
// 	if (!in_array($day, $days)) {
// 		unset($data[$day]);
// 	}
// }
// $a = array_values($data);

// //数据缓存文件 在线峰值
// $file = __DIR__.'/data/cache_chart_online_time_b';
// $data = is_file($file) ? json_decode(file_get_contents($file),1) : array();
// if ($is_today && $data) {
// 	unset($data[$ut_end]);
// }
// foreach ( $days as $k => $day )
// {
// 	if ( !isset($data[$day]) ) {
// 		$monthid = date('Ym',$day);
// 		$monthid = $monthid < 201506 ? "" : ("_".$monthid);
// 		if (!empty($monthid)) {
// 			$sql = "select max(online_time) as snum,count(*) as cn, `dateid` as dd from lord_game_loginout{$monthid} where online_time > 60 and `dateid` = ".date("Ymd", $day); 
// 			$row = $db -> query($sql) -> fetchAll();
// 			foreach($row as $val){
// 				if (!$val['dd']) continue;
// 				$dd = str_split($val['dd'],2);
// 				$data[$day] = array($dd[0].$dd[1].'-'.$dd[2].'-'.$dd[3],intval($val['snum']/60));
// 			}
// 		}else{
// 			$sql = "select max(online_time) as snum,count(*) as cn, `date` as dd from lord_game_loginout where online_time > 60 and `date` = '".date("Y-m-d", $day)."'"; 
// 			$row = $db -> query($sql) -> fetchAll();
// 			foreach($row as $val){
// 				if (!$val['dd']) continue;
// 				$data[$day] = array($var['dd'],intval($val['snum']/60));
// 			}
// 		}
// 	}
// }
// $res = file_put_contents($file, json_encode($data));
// foreach ( $data as $day => $v )
// {
// 	if (!in_array($day, $days)) {
// 		unset($data[$day]);
// 	}
// }
// $b = array_values($data);

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
				<input style="height:25px;" value="<?=$date?>" class="span3" type="text" id="date" name="date" class="textbox dtime" readonly onclick="WdatePicker({dateFmt:'yyyy-MM-dd'})" />
				<input type="submit" value="查&nbsp;&nbsp;询" style="width:60px;height:30px; " />
			</div>
			<div id="chart1" style="height:400px; width:1400px;margin-bottom:20px;"></div>
		</div>
	</form>
</div>
 
<script class="code" type="text/javascript">
$(document).ready(function(){
	var formatValue = "<?=$type=='hour'?'%R':'%x'?>";
    var line1 = <?php echo (json_encode($a));?>;
    var line2 = <?php echo (json_encode($b));?>;
	var plot1 = $.jqplot('chart1', [line1,line2], 
		{
			title:'在线时长(分) 平均/峰值',
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
