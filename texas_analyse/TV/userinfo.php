<?php
require_once '../include/priv.php';
?>
<script type="text/javascript" src="../js/jquery.js"></script>
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
		data:{
			pageSize: pageSize,
			pageIndex: index,
			type:$('#type').val(),
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
					dataListHtml += "<td>"+o.coins+"</td>";
					dataListHtml += "<td>"+o.gold+"</td>";  
					dataListHtml += "<td>"+o.coins_cost+"</td>"; 
					dataListHtml += "<td>"+o.coins_got+"</td>"; 
					dataListHtml += "<td>"+(o.coins_got-o.coins_cost)+"</td>"; 
					dataListHtml += "<td>"+o.charge_money+"</td>"; 
					dataListHtml += "<td>"+o.add_time+"</td>"; 
					dataListHtml += "<td>"+o.last_time+"</td>";  
					dataListHtml += "<td>TV</td>";
					dataListHtml += "<td>"+o.channel+"</td>";
					dataListHtml += "<td>正常</td>"; 
					var time = "<?=time()?>";
					var sign = "<?=md5('qwe!@#321'.time())?>";
					dataListHtml += "<td><a href='userinfoWechat.php?time="+time+"&sign="+sign+"&type=uid&uid="+o.uid+"'>其他信息</a></td>"; 
					dataListHtml += "</tr>";
				}
				$("#dataList").html(dataListHtml);
				if(dataListHtml=="" && isNext){
					alert("已经是最后一页");
					pageIndex--;
				}else{
					$("#count").html(Math.ceil(dataList.cn/pageSize)); 
					$("#dataList").html(dataListHtml); 
					$("#pageIndex").html(pageIndex+1);
					 
					$("#pagination").show();
				}
				
			 
		}
	});
}
 
</script>
 <body>
  	<div class="">
  
  	
	
	<div>
		<fieldset>
		<legend>用户的详细信息</legend>	
		<div class="row">
			<div class="span2">
				<label>查询方式类型：</label>
				<select class="span2" id="type" >
					<option value="all">全部</option>
					<option value="uid">用户UID</option>
					<option value="coolNum">靓号</option>
					<option value="nickName">昵称</option> 
				</select>
			</div>
			<div class="span2">
				<label>用户信息：</label>
				<input class="span2" type="text" id="data" style="height:30px"/>
			</div>
			 
			<div span="span1" style="float:right;">
				<label>&nbsp;</label>
				<input type="button" value="查&nbsp;&nbsp;询" onclick="query()" class="btn" />
			</div>
		</div>				
	</fieldset>
	</div>
	<?php
	
	?>
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
				<td nowrap id="coinsa"><strong><a style="cursor: pointer" onclick="query('u.coins','coinsa')">筹码</a></strong></td>
				<td nowrap id="coinsd" style="display:none"><strong><a style="cursor: pointer" onclick="query('u.coins','coinsd')">筹码</a></strong></td>
				<td nowrap id="golda"><strong><a style="cursor: pointer" onclick="query('u.gold','golda')">乐币</a></strong></td>
				<td nowrap id="goldd" style="display:none"><strong><a style="cursor: pointer" onclick="query('u.gold','goldd')">乐币</a></strong></td>
				<td nowrap id="costa"><strong><a style="cursor: pointer" onclick="query('a.coins_cost','costa')">消耗筹码</a></strong></td>
				<td nowrap id="costd" style="display:none"><strong><a style="cursor: pointer" onclick="query('a.coins_cost','costd')">消耗筹码</a></strong></td> 
				<td nowrap id="gota"><strong><a style="cursor: pointer" onclick="query('a.coins_got','gota')">收获筹码</a></strong></td>
				<td nowrap id="gotd" style="display:none"><strong><a style="cursor: pointer" onclick="query('a.coins_got','gotd')">收获筹码</a></strong></td>  
				<td nowrap id="difa"><strong><a style="cursor: pointer" onclick="query('dif','difa')">差额</a></strong></td>
				<td nowrap id="difd" style="display:none"><strong><a style="cursor: pointer" onclick="query('dif','difd')">差额</a></strong></td>  
				<td nowrap id="chargea"><strong><a style="cursor: pointer" onclick="query('a.charge_money','chargea')">充值总额</a></strong></td>
				<td nowrap id="charged" style="display:none"><strong><a style="cursor: pointer" onclick="query('a.charge_money','charged')">充值总额</a></strong></td> 
				<td nowrap id="adda"><strong><a style="cursor: pointer" onclick="query('a.add_time','adda')">注册时间</a></strong></td>
				<td nowrap id="addd" style="display:none" ><strong><a style="cursor: pointer" onclick="query('a.add_time','addd')">注册时间</a></strong></td>  
				<td nowrap id="lasta"><strong><a style="cursor: pointer" onclick="query('a.last_login','lasta')">上次登录</a></strong></td>
				<td nowrap id="lastd" style="display:none"><strong><a style="cursor: pointer" onclick="query('a.last_login','lastd')">上次登录</a></strong></td> 
				<td nowrap><strong>站点</strong></td> 
				<td nowrap><strong>用户来源</strong></td> 
				<td nowrap><strong>状态</strong></td> 
				<td nowrap><strong>操作</strong></td> 
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
