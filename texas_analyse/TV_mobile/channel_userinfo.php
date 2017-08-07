<?php
require_once '../include/priv.php';
require_once '../include/database.class.php';
require_once './global.php';
?>
<script type="text/javascript" src="../js/jquery.js"></script> 
<script src="../js/My97DatePicker/WdatePicker.js" language="javascript"></script>
<link rel="stylesheet" type="text/css" href="../bootstrap/css/bootstrap.min.css" />
<script>
$(function(){ 
	if("<?=$_GET['uid']?>"){
		$('#type').val('uid')
		$('#data').val("<?=$_GET['uid']?>");
		
	}
	query();
});
var pageSize = 50;
var pageIndex = 0;
var orderby = "a.add_time";
var by = "desc";
function query(a,str){
	var b="";var c="";
	if(a){
		orderby = a;
		b=str.substr(-1);
		c = str.substr(0,str.length-1);
		 
		if(b == "a"){
			by="asc";
			$("#"+c+"a").hide();
			$("#"+c+"d").show();
		}else{
			by="desc";
			$("#"+c+"d").hide();
			$("#"+c+"a").show();
		}
	}
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
		url:'searchUserinfo.php',  
		type:'post',
		data:{
			pageSize: pageSize,
			pageIndex: index,
			type:$('#type').val(),
			min:$('#min').val(),
			max:$('#max').val(),
			start:$('#start').val(),
			end:$('#end').val(),
			last_start:$('#last_start').val(),
			last_end:$('#last_end').val(),
			channel: $('#channel').val() ,
			orderby:orderby,
			by:by,
			data:$('#data').val()
		},
		success:function(data){ 
			var dataList=eval("("+data+")");
			  
				var dataListHtml = "";
				for(var i=0;i<dataList.data.length;i++){
					var o = dataList.data[i]; 
					dataListHtml += "<tr>"; 
					dataListHtml += "<td>"+o.uid+"</td>";
					//if(o.is_robot==1)
					dataListHtml += "<td>"+o.nick+"</td>"; 
					//else
					//dataListHtml += "<td><img src=\"../bootstrap/images/man.jpg\">"+o.nick+"</td>"; 				
					dataListHtml += "<td>"+o.cool_num+"</td>";
					 
					dataListHtml += "<td>"+o.level+"</td>";  
					dataListHtml += "<td>"+o.vip_lv+"</td>";	
					dataListHtml += "<td>"+o.play+"</td>";	
					dataListHtml += "<td>"+o.win+"</td>";	
					dataListHtml += "<td>"+o.trial_coins+"</td>";	
					dataListHtml += "<td>"+o.coins+"</td>";
					dataListHtml += "<td>"+o.gold+"</td>";  
					
					dataListHtml += "<td>"+o.add_time+"</td>"; 
					dataListHtml += "<td>"+o.last_time+"</td>";  
					dataListHtml += "<td>"+(o.is_tv==1?"TV":"MP")+"</td>";
					dataListHtml += "<td>"+o.channel+"</td>";
					dataListHtml += "<td>正常</td>"; 
					 
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
function getChannel(val){
	if(val == 1){
		$("#channel_tv").show();
		$("#channel_mp").hide();
		$("#channel_all").hide();
	}else if(val == 0){
		$("#channel_tv").hide();
		$("#channel_mp").show();
		$("#channel_all").hide();
	}else if(val == 'all'){
		$("#channel_all").show();
		$("#channel_tv").hide();
		$("#channel_mp").hide(); 
	}
}

</script>
 <body>
  	<div class="">
  
  	
	<form onkeypress="myKeyPress(event)">
	<input type="hidden" id="channel" name="channel">
	<div >
		<fieldset>
		<legend>用户的详细信息</legend>	
		<div class="row">
			 
			<div class="span2">
				<label>站点：</label>
				<select class="span2" id="type" onchange="getChannel(this.value)">
					<option value="all">全部</option>
					<option value="1">TV</option>
					<option value="0">MP</option> 
				</select>
			</div>
			<div class="span2">
				<label>用户来源：</label>
				<?php
					
					$str1 = $str2 = $str = "";
					 
					if($adm_username!='all'){substr_count($adm_username,",")>0?$where = " and channel in (".$adm_username.")":$where = " and channel = '".$adm_username."'";}
	
					//if($adm_username!='all')$where = " and channel = '".$adm_username."'";
					$sql = "SELECT `channel` FROM `mobile_user` WHERE channel !='' and is_tv=1".$where." GROUP BY `channel`";
					
					$row = $db -> query($sql)-> fetchAll();  
					foreach($row as $val){
						$str1.= '<option value="'.$val['channel'].'" name="tv">'.$val['channel'].'</option>';
					}
					
					$sql = "SELECT `channel` FROM `mobile_user` WHERE channel !='' and is_tv=0".$where." GROUP BY `channel`"; 
					$row = $db -> query($sql)-> fetchAll();  
					foreach($row as $val){
						$str2.= '<option value="'.$val['channel'].'" name="mp">'.$val['channel'].'</option>';
					}
					
					$sql = "SELECT `channel` FROM `mobile_user` WHERE channel !=''".$where." GROUP BY `channel`"; 
					$row = $db -> query($sql)-> fetchAll();  
					foreach($row as $val){
						$str.= '<option value="'.$val['channel'].'" name="mp">'.$val['channel'].'</option>';
					} 
				?>
				<select class="span2" id="channel_tv" style="display:none" onchange="$('#channel').val(this.value)">
					<option value="all">全部</option>
					
					<?=$str1?>
					 
				</select>
				<select class="span2" id="channel_mp" style="display:none" onchange="$('#channel').val(this.value)">
					<option value="all">全部</option>
					
					<?=$str2?>
					 
				</select>
				<select class="span2" id="channel_all" onchange="$('#channel').val(this.value)">
					<option value="all">全部</option>
					
					<?=$str?>
					 
				</select>
				
			</div>
			<div class="span2">
				<label>帐号/昵称/靓号：</label>
				<input class="span2" type="text" id="data" name="data" style="height:30px"/>
			</div>
			<div class="span2" style="width:90px;">
				<label>充值总额</label>
				<input style="height:30px; width:80px;" class="span2" type="text" id="min"/>
				<input style="height:30px; width:80px;" class="span2" type="text" id="max"/>
			</div>
			<div class="span3">
				<label>注册时间：</label>
				<input style="height:30px;" class="span3" type="text" id="start" class="textbox dtime" readonly onclick="WdatePicker({dateFmt:'yyyy-MM-dd'})"/>
				<input style="height:30px;" class="span3" type="text" id="end" class="textbox dtime" readonly onclick="WdatePicker({dateFmt:'yyyy-MM-dd'})"/>
			</div>
			<div class="span3">
				<label>上次登陆：</label>
				<input style="height:30px;" class="span3" type="text" id="last_start" class="textbox dtime" readonly onclick="WdatePicker({dateFmt:'yyyy-MM-dd'})"/>
				<input style="height:30px;" class="span3" type="text" id="last_end" class="textbox dtime" readonly onclick="WdatePicker({dateFmt:'yyyy-MM-dd'})"/>
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
				 
				<td nowrap id="levela"><strong><a style="cursor: pointer" onclick="query('u.level','levela')">等级</a></a></strong></td>
				<td nowrap id="leveld" style="display:none"><strong><a style="cursor: pointer" onclick="query('u.level','leveld')">等级</a></a></strong></td>
				<td nowrap id="vipa"><strong><a style="cursor: pointer" onclick="query('u.vip_lv','vipa')">VIP</a></strong></td>
				<td nowrap id="vipd" style="display:none"><strong><a style="cursor: pointer" onclick="query('u.vip_lv','vipd')">VIP</a></strong></td> 
				<td nowrap id="playa"><strong><a style="cursor: pointer" onclick="query('a.play','playa')">play</a></strong></td>
				<td nowrap id="playd" style="display:none"><strong><a style="cursor: pointer" onclick="query('a.play','playd')">play</a></strong></td>
				<td nowrap id="wina"><strong><a style="cursor: pointer" onclick="query('a.win','wina')">win</a></strong></td>
				<td nowrap id="wind" style="display:none"><strong><a style="cursor: pointer" onclick="query('a.win','wind')">win</a></strong></td>
				
				<td nowrap id="trial_coinsa"><strong><a style="cursor: pointer" onclick="query('u.trial_coins','trial_coinsa')">体验币</a></strong></td>
				<td nowrap id="trial_coinsd" style="display:none"><strong><a style="cursor: pointer" onclick="query('u.trial_coins','trial_coinsd')">体验币</a></strong></td>
				<td nowrap id="coinsa"><strong><a style="cursor: pointer" onclick="query('u.coins','coinsa')">筹码</a></strong></td>
				<td nowrap id="coinsd" style="display:none"><strong><a style="cursor: pointer" onclick="query('u.coins','coinsd')">筹码</a></strong></td>
				<td nowrap id="golda"><strong><a style="cursor: pointer" onclick="query('u.gold','golda')">乐币</a></strong></td>
				<td nowrap id="goldd" style="display:none"><strong><a style="cursor: pointer" onclick="query('u.gold','goldd')">乐币</a></strong></td>
				
				<td nowrap id="adda"><strong><a style="cursor: pointer" onclick="query('a.add_time','adda')">注册时间</a></strong></td>
				<td nowrap id="addd" style="display:none" ><strong><a style="cursor: pointer" onclick="query('a.add_time','addd')">注册时间</a></strong></td>  
				<td nowrap id="lasta"><strong><a style="cursor: pointer" onclick="query('a.last_login','lasta')">上次登录</a></strong></td>
				<td nowrap id="lastd" style="display:none"><strong><a style="cursor: pointer" onclick="query('a.last_login','lastd')">上次登录</a></strong></td> 
				<td nowrap><strong>站点</strong></td> 
				<td nowrap><strong>用户来源</strong></td> 
				<td nowrap><strong>状态</strong></td> 
				 
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
