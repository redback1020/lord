<!DOCTYPE html>
<?php
require_once '../include/database.class.php';
$pdo = new DB();
$db = $pdo->getDB();
$type = $_REQUEST['type'];
$date = $_REQUEST['date'];
if($type == "day"){
	$start = isset($_REQUEST['start'])?$_REQUEST['start']:date("Y-m-d",(strtotime($date)-30*3600*24)); 
	$sql = "select sum(num) as snum,count(*) as cn,date(add_time) as dd from lord_online where add_time >= '".$start."' and add_time<'".$date." 23:59:59' group by dd order by dd desc"; 
	$row = $db -> query($sql) -> fetchAll();
	foreach($row as $val){
		$a[] = array($val['dd'],intval(floor($val['snum']/$val['cn'])));
	}
	if(count($a)<12){
		$day = $row[0]['dd'];$all = 12-count($a);
		for($i=1;$i<$all;$i++){
			$day = date("Y-m-d",(strtotime($day)+24*3600));
			 
			$a[] = array($day,'-');
		}
	}
 
} 

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
				<label>在线时间：</label>
				<?php
				if($_REQUEST['type'] == 'day'){
				echo '<input style="height:25px;" value="'.$start.'" class="span3" type="text" id="start" name="start" class="textbox dtime" readonly onclick="WdatePicker({dateFmt:\'yyyy-MM-dd\'})"/>';
				}
				?>
				<input style="height:25px;" class="span3" type="text" id="date" name="date" class="textbox dtime" readonly onclick="WdatePicker({dateFmt:'yyyy-MM-dd'})"/>
				
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
  var plot1 = $.jqplot('chart1', [line1], {
      title:'在线用户分时线',
	   
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
