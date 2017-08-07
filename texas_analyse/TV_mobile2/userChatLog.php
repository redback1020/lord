<?php
require_once '../manage/checkPriv.php';
require_once '../include/database.class.php';
?>
<script type="text/javascript" src="../js/jquery.js"></script> 
<script src="../js/My97DatePicker/WdatePicker.js" language="javascript"></script>
<link rel="stylesheet" type="text/css" href="../bootstrap/css/bootstrap.min.css" />
<script>
$(function(){ 
	
	query();
});
var pageSize = 50;
var pageIndex = 0;
 
function query(a,str){
	 
	pageIndex = 0;
	queryByPage(pageIndex);
}

function prePage(){
	if(pageIndex==0){
		alert("已经是第一页");
		return;
	}
	pageIndex--;
	queryByPage(pageIndex);
}

function nextPage(){
	pageIndex++;
	queryByPage(pageIndex,true);
}

function queryByPage(index,isNext){
	$.ajax({
		url:'searchUserchat.php',  
		type:'post',
		data:{
			pageSize: pageSize,
			pageIndex: index,
			type:$('#type').val(), 
			start:$('#start').val(),
			end:$('#end').val(), 
			channel:$('#channel').val(), 
			data:$('#data').val()
		},
		success:function(data){ 
			var dataList=eval("("+data+")");
			  
				var dataListHtml = "";
				for(var i=0;i<dataList.data.length;i++){
					var o = dataList.data[i]; 
					dataListHtml += "<tr>"; 
					dataListHtml += "<td>"+o.uid+"</td>";
				 
					dataListHtml += "<td>"+o.nick+"</td>"; 
					 			
					dataListHtml += "<td>"+o.cool_num+"</td>";
					  
					dataListHtml += "<td>"+o.value+"</td>";  
					dataListHtml += "<td>"+(o.is_tv==1?"TV":"MP")+"</td>";
					dataListHtml += "<td>"+o.channel+"</td>";
					dataListHtml += "<td>"+o.add_time+"</td>";
					  
					dataListHtml += "</tr>";
				}
				$("#dataList").html(dataListHtml);
				if(dataListHtml=="" && isNext){
					alert("已经是最后一页");
					pageIndex--;
				}else{
					$("#num").html(dataList.cn); 
					$("#count").html(Math.ceil(dataList.cn/pageSize)); 
					$("#dataList").html(dataListHtml); 
					$("#pageIndex").html(pageIndex+1);
					 
					$("#pagination").show();
				}
				
			 
		}
	});
}
 function 	myKeyPress(e){
    var key = window.event ? e.keyCode : e.which;
	if(key==13)query() ;
	  
    	
     
}


</script>
 <body>
  	<div class="">
  
  	
	<form onkeypress="myKeyPress(event)">
	
	<div >
		<fieldset>
		<legend>用户聊天记录</legend>	
		<div class="row">
			 
			<div class="span2">
				<label>站点：</label>
				<select class="span2" id="type" >
					<option value="all">全部</option>
					<option value="1">TV</option>
					<option value="0">MP</option> 
				</select>
			</div>
			<div class="span2">
				<label>用户来源：</label>
				<?php
					
					$str1 = "";
					 
					$sql = "SELECT `channel` FROM `mobile_user` WHERE channel !=''  GROUP BY `channel`"; 
					$row = $db -> query($sql)-> fetchAll();  
					foreach($row as $val){
						$str1.= '<option value="'.$val['channel'].'">'.$val['channel'].'</option>';
					}
					 
				?>
				<select class="span2" id="channel" >
					<option value="all">全部</option>
					
					<?=$str1?>
					 
				</select> 
				
			</div>
			<div class="span2">
				<label>帐号/昵称/靓号：</label>
				<input class="span2" type="text" id="data" name="data" style="height:30px"/>
			</div>
			 
			<div class="span3">
				<label>操作时间：</label>
				<input style="height:30px;" class="span3" type="text" id="start" class="textbox dtime" readonly onclick="WdatePicker({dateFmt:'yyyy-MM-dd'})"/>
				<input style="height:30px;" class="span3" type="text" id="end" class="textbox dtime" readonly onclick="WdatePicker({dateFmt:'yyyy-MM-dd'})"/>
			</div>
			 
			<div span="span1" style="float:right;">
				<label>&nbsp;</label>
				<input type="button" value="查&nbsp;&nbsp;询" onclick="query()" class="btn" />
			</div>
		</div>				
	</fieldset>
	</div>
	</form>
	<?php
	
	?>
	<div>总条数:<span id="num"></span></div>
	<div>
		<table class="table table-bordered table-condensed table-hover">
			<tr class="info">			
				<td nowrap><strong>用户ID</strong></td> 
				<td nowrap><strong>昵称</strong></td>
				<td nowrap><strong>靓号</strong></td> 
				<td nowrap><strong>内容</strong></td> 
				<td nowrap><strong>站点</strong></td> 
				<td nowrap><strong>渠道号</strong></td>  
				<td nowrap><strong>时间</strong></td>  
			</tr> 
			<tbody id="dataList">
			</tbody>
		</table>
	</div>
	<table width="920" border="0" cellpadding="5" cellspacing="0" align="center">
	<tr><td height="25" id="pagination" align="center" style="display:none;"> 
		<div class="btn-group">
		  <button class="btn" onclick="prePage()">前一页</button>
		  <span id="page">
		  <button class="btn" id="pageIndex"></button>
		  </span>
		  <button class="btn" onclick="nextPage()">后一页</button>
		  
		</div>
		共<span id="count"></span>页
	</td></tr>
	</table>
	
	
	</div>
  </body>
