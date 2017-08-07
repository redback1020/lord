<?php
require_once '../manage/checkPriv.php';
?>
<script type="text/javascript" src="../js/jquery.js"></script>
<script src="../js/My97DatePicker/WdatePicker.js" language="javascript"></script>

<link rel="stylesheet" type="text/css" href="../bootstrap/css/bootstrap.min.css" />
<script>
function query(date){
	if(date == 'search')date=$('#time').val();
	var dateobj = new Date(date);
	var week; 
if(dateobj.getDay()==0)          week="星期日" 
if(dateobj.getDay()==1)          week="星期一" 
if(dateobj.getDay()==2)          week="星期二" 
if(dateobj.getDay()==3)          week="星期三" 
if(dateobj.getDay()==4)          week="星期四" 
if(dateobj.getDay()==5)          week="星期五" 
if(dateobj.getDay()==6)          week="星期六" 
//document.write("今天是："+date.getYear()+"年 "+(date.getMonth()+1)+" 月 "+date.getDate()+" 日 "+week+" "+date.getHours()+" 点 "+date.getMinutes()+" 分"); 
  
	$("#day").html(date+" "+week)
	$.ajax({
		url:'getHistory.php?date='+date,  
		success:function(data){ 
			$("#dataList").html(data);
		}
	});
}

</script>
 <body>
  	<div class="container">
  
  	
	
	<div>
		<fieldset>
		<legend>争霸赛历史排名榜单</legend>	
		<div class="row">
			<?php
			$day1 = date("Y-m-d",strtotime("-1 days"));
			$day2 = date("Y-m-d",strtotime("-2 days"));
			$day3 = date("Y-m-d",strtotime("-3 days"));
			$day4 = date("Y-m-d",strtotime("-4 days"));
			$day5 = date("Y-m-d",strtotime("-5 days"));
			?>
			<div class="span4" style="width:800px;">
				<label>时间：</label>
				<input style="height:30px;" class="span3" type="text" id="time" class="textbox dtime" readonly onclick="WdatePicker({dateFmt:'yyyy-MM-dd'})"/>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
				<input type="button" value="一天前" onclick="query('<?=$day1?>')" class="btn" />&nbsp;
				<input type="button" value="两天前" onclick="query('<?=$day2?>')" class="btn" />&nbsp;
				<input type="button" value="三天前" onclick="query('<?=$day3?>')" class="btn" />&nbsp;
				<input type="button" value="四天前" onclick="query('<?=$day4?>')" class="btn" />&nbsp;
				<input type="button" value="五天前" onclick="query('<?=$day5?>')" class="btn" />
			</div>
			<div span="span1" style="float:right;">
				<label>&nbsp;</label>
				<input type="button" value="查&nbsp;&nbsp;询" onclick="query('search')" class="btn" />
			</div>
			 
		</div>				
	</fieldset>
	</div>

	<div>
	<p>注:有头像的为真实用户&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;具体操作日期为:<span id="day"></span></p>
		<table class="table table-bordered table-condensed table-hover" style="font-size:12px;">
			<tr class="info">			
				<td width="10%"><strong>编号</strong></td>
				<td width="10%"><strong>uid</strong></td>
				<td width="10%"><strong>nick</strong></td>
				<td width="10%"><strong>等级</strong></td>
				<td width="20%"><strong>筹码</strong></td>
				 
			</tr>
			<tbody id="dataList">
			 
			</tbody>
		</table>
	</div>
	  
	</div>
  </body>
