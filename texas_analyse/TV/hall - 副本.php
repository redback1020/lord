<script type="text/javascript" src="../bootstrap/js/bootstrap.min.js"></script>
<link rel="stylesheet" type="text/css" href="../bootstrap/css/bootstrap.min.css" />
<?php
require_once '../include/database.class.php';
  
 /* $pdo = new DB();
  $db = $pdo->getDB();
  $sql = "select count(*) as cn from tb_user where reg_time>'2013-10-10 1:00:30'";
	$row = $db -> query($sql)-> fetch(PDO::FETCH_NUM);*/ 
?>
 <body>
  	<div class="container">
  
  	
	
	<div>
		<fieldset>
		<legend>大厅公告</legend>	
		<div class="row">
			<div class="span2">
				<label>用户ID：</label>
				<input class="span2" type="text" id="userId" />
			</div>
			<div class="span2">
				<label>平台ID：</label>
				<select class="span2" id="platformId">
					<option value="">全部</option>
					<option value="4">4</option>
					<option value="5">5</option>
					<option value="12">12</option>
				</select>
			</div>
			<div class="span2">
				<label>充值金额：</label>
				<input class="span1" type="text" id="minAmount"/>&nbsp;-&nbsp;<input class="span1" type="text" id="maxAmount"/>
			</div>
			<div class="span4">
				<label>时间段：</label>
				<input class="span2" type="text" id="startTime" class="textbox dtime" readonly onclick="WdatePicker({dateFmt:'yyyy-MM-dd HH:mm:ss'})"/>&nbsp;-&nbsp;<input class="span2" type="text" id="endTime" class="textbox dtime" readonly onclick="WdatePicker({dateFmt:'yyyy-MM-dd HH:mm:ss'})"/>
			</div>
			<div span="span1" style="float:right;">
				<label>&nbsp;</label>
				<input type="button" value="查&nbsp;&nbsp;询" onclick="query()" class="btn" />
			</div>
		</div>				
	</fieldset>
	</div>

	<div>
		<table class="table table-bordered table-condensed table-hover">
			<tr class="info">			
				<td width="10%"><strong>订单号</strong></td>
				<td width="20%"><strong>用户ID</strong></td>
				<td width="30%"><strong>时间点</strong></td>
				<td width="15%"><strong>充值金额</strong></td>
				<td width="15%"><strong>充值元宝</strong></td>
				<td width="10%"><strong>平台ID</strong></td>
			</tr>
			<tbody id="dataList">
			</tbody>
		</table>
	</div>
	<table width="920" border="0" cellpadding="5" cellspacing="0" align="center">
	<tr><td height="25" id="pagination" align="center" style="display:none;"> 
		<div class="btn-group">
		  <button class="btn" onclick="prePage()">前一页</button>
		  <button class="btn" id="pageIndex"></button>
		  <button class="btn" onclick="nextPage()">后一页</button>
		</div>
		
		<div style="float:right;"><span class="label label-success">充值总金额：<span class="badge badge-warning" id="totalAmount"></span></span></div>
	</td></tr>
	</table>
	
	</div>
  </body>
