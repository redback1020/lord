<?php
require_once '../include/priv.php';
require_once '../include/database.class.php';
?>
<script type="text/javascript" src="../js/jquery.js"></script> 
<script src="../js/My97DatePicker/WdatePicker.js" language="javascript"></script>
<link rel="stylesheet" type="text/css" href="../bootstrap/css/bootstrap.min.css" />
<script>
$(function(){ 
	var per = '<?=isset($_POST['_recPerPage'])?$_POST['_recPerPage']:50?>'
	var start = '<?=isset($_POST['start'])?$_POST['start']:date("Y-m-d",time())?>'
	var end = '<?=isset($_POST['end'])?$_POST['end']:date("Y-m-d",time())?>' 
	var keywords = '<?=isset($_POST['keywords'])?$_POST['keywords']:""?>' 
$("#start").val(start);
		$("#end").val(end); 
		$("#keywords").val(keywords); 
		$("#_recPerPage").val(per);
	//query();
});
 

</script>
 <body>
  	<div class="">
  
  	
	<form method="post" name="form1">
	 
	<div >
		<fieldset>
		<legend>操作日志</legend>	
		<div class="row">
			 
			<div class="span2">
				<label>站点：</label>
				<select class="span2" id="type" name="type">
					<option value="all">全部</option>
					<option value="1">TV</option>
					<option value="0">MP</option> 
				</select>
			</div>
			<div class="span2">
				<label>关键字：</label>
				<input class="span2" type="text" id="keywords" name="keywords" style="height:30px"/>
			</div>  
			<div class="span3">
				<label>日期：</label>
				<input style="height:30px;" class="span3" type="text" id="start" name="start" class="textbox dtime" readonly onclick="WdatePicker({dateFmt:'yyyy-MM-dd'})"/>
				<input style="height:30px;" class="span3" type="text" id="end" name="end" class="textbox dtime" readonly onclick="WdatePicker({dateFmt:'yyyy-MM-dd'})"/>
			</div>
			 
			<div span="span1" style="float:right;">
				<label>&nbsp;</label>
				<input type="submit" value="查&nbsp;&nbsp;询" class="btn" />
			</div>
		</div>				
	</fieldset>
	</div>
	
	<?php
	$pdo = new DB();
	$db = $pdo->getDB();
	$where = " and game='德州扑克'";
	if(isset($_POST['start'])&&$_POST['start']!="") {$start=$_POST['start'];}else $start=date("Y-m-d",time());
	if(isset($_POST['end'])&&$_POST['end']!="") {$end=date("Y-m-d",strtotime($_POST['end'])+24*3600);}else $end = date("Y-m-d",time()+24*3600); 
	if(isset($_POST['keywords'])&&$_POST['keywords']!="") {$where .= " and (msg like '%".$_POST['keywords']."%' or operator like '%".$_POST['keywords']."%' )";}
	$start_time = $start;
	$sql = "select count(*) as cn from mobile_op_log where add_time>='".$start."' and add_time<'".$end."'".$where;
	$res = $db -> query($sql) -> fetch();
	$pageSize = isset($_REQUEST['_recPerPage'])?$_REQUEST['_recPerPage']:50;
	$pageId = isset($_REQUEST['_pageID'])&&$_REQUEST['_pageID']>1?$_REQUEST['_pageID']:1;
	$start = ($pageId-1)*$pageSize;
	$pageTotal = ceil($res['cn']/$pageSize);
	
	$sql = "select * from mobile_op_log where add_time>='".$start_time."' and add_time<'".$end."'".$where." order by add_time desc limit  ".$start.", ".$pageSize; 
	$row = $db -> query($sql) -> fetchAll(); 
	 
	?>
 
	<div>
		<table class="table table-bordered table-condensed table-hover">
			<tr class="info">			
				<td nowrap><strong>编号</strong></td> 
				<td nowrap><strong>操作者</strong></td>
				<td nowrap><strong>操作ip</strong></td>
				<td nowrap><strong>操作内容</strong></td>
				<td nowrap><strong>操作时间</strong></td>  
			</tr> 
			<?php
			$par = getPri();
			foreach($row as $key => $val){
 			?>
			<tr> 
				<td><?=($key+1)?></td>
				<td><?=$val['operator']?></td>
				<td><?=$val['ip']?></td>
				<?php
				if($val['uid']==0){
				?>
				<td><?=$val['msg']?></td>
				<?php
				}else{
				?>
				<td><?=$val['msg']?>&nbsp;用户ID:<a href="userinfo.php?uid=<?=$val['uid']?>&<?=$par?>"><?=$val['uid']?></a>&nbsp;&nbsp;数量:<?=$val['value']?></td>
				<?php
				}
				?>
				
				<td><?=$val['add_time']?></td>
				 
			</tr>
			<?php 
			}
			?>
		<tfoot>	   
	     <tr  style="font-size:14px;height:25px;" bgcolor="#FFFFFF" onMouseOver="this.style.background='#ccc'" onMouseOut="this.style.background='#fff'">
				<td colspan="6">
					<div style="float:right; clear:none;" class="pager">共<strong><?=$res['cn']?></strong>条记录，每页 <strong><select name="_recPerPage" id="_recPerPage" onchange="submitPage('go');">
						<option value="1">1</option>
						<option value="5">5</option>
						<option value="10">10</option>
						<option value="15">15</option>
						<option value="20" selected="selected">20</option>
						<option value="25">25</option>
						<option value="30">30</option>
						<option value="35">35</option>
						<option value="40">40</option>
						<option value="45">45</option>
						<option value="50">50</option>
						<option value="100">100</option>
						<option value="200">200</option>
						<option value="500">500</option>
						<option value="1000">1000</option>
						</select>
						</strong>条，<strong><?=$pageId?>/<?=$pageTotal?></strong> <a href="#" onclick="submitPage(1)">首页</a>&nbsp;&nbsp;<a href="#" onclick="submitPage(<?=($pageId-1)?>)">上页&nbsp;&nbsp;</a><a href="#" onclick="submitPage(<?=($pageId+1)?>)">下页&nbsp;&nbsp;</a><a href="#" onclick="submitPage(<?=$pageTotal?>)">末页</a>    
						 
						<input type="text" id="_pageID" name="_pageID" value="<?=$pageId?>" style="text-align:center;width:30px;"> 
						<input type="button" id="goto" value="GO!" onclick="submitPage('go');">       
						<script>
								 
								function submitPage(pageId)
								{
								
									var pageTotal = "<?=$pageTotal?>";
									var _recPerPage = $("#_recPerPage").val();
									if(pageId == 0)pageId = 1;
									if(pageId == 'go')pageId = $('#_pageID').val();
									if(pageId>pageTotal)pageId = pageTotal;
									$("#_pageID").val(pageId);
									$("#_recPerPage").val(_recPerPage);
									//location.href = "orderlist.php?_pageID="+pageId+"&_recPerPage="+_recPerPage;
									form1.submit();
								}
								</script></div>
				</td>
				
			</tr>
	<tfoot>		 
		</table>
	</div>
	 </form> 
	
	</div>
  </body>
