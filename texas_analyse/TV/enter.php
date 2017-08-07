<?php
require_once '../include/priv.php';
?>
<script type="text/javascript" src="../js/jquery.js"></script> 
<link rel="stylesheet" type="text/css" href="../bootstrap/css/bootstrap.min.css" />
<script>
function selectAll(obj){
 
	if(obj.checked){
		$("input[name='checktips[]']").attr("checked",true); 
	}else{
		$("input[name='checktips[]']").attr("checked",false); 
	}
}

function deleteTips(flag){
	if(confirm("你确定删除吗?")){
		$.ajax({
			url:'deleteTips.php?filename=enter&flag='+flag,  
			success:function(data){ 
				alert("删除成功!");
			}
		});
	}	
}
var flag = 0;
function addTips(){
	++flag;
	var dataListHtml;
	dataListHtml += "<tr class='table-body' id='flag"+flag+"'>";
	dataListHtml += "<td><input type=\"checkbox\"></td>";
	dataListHtml += "<td><input type=\"text\" name=\"tips[]\" style=\"width: 750px;height:30px;margin-top:5px;margin-bottom:5px;\"></td>";
	dataListHtml += "<td>&nbsp;&nbsp;&nbsp;&nbsp;<a style=\"margin-top:15px;\" onclick='deleteTr("+flag+")' >删除</a></td>"; 
	dataListHtml += "</tr>";
	
	var table = $('table');  
	var row = $("<tr class='table-body' id='flag"+flag+"'></tr>"); 
	var td1 = $("<td></td>"); 
		td1.append($("<input type=\"checkbox\">") 
	); 
	row.append(td1);
	var td2 = $("<td></td>"); 
		td2.append($("<input type=\"text\" name=\"tips[]\" style=\"width: 750px;height:30px;margin-top:5px;margin-bottom:5px;\"></td>") 
	); 
	row.append(td2);
	var td3 = $("<td></td>"); 
		td3.append($("&nbsp;&nbsp;&nbsp;&nbsp;<a style=\"margin-top:15px;\" onclick='deleteTr("+flag+")' >删除</a>") 
	); 
	row.append(td3);
	
	table.append(row); 
	//$('#dataList').html($('#dataList').html()+dataListHtml);
}
function deleteTr(trIndex){ 
	$("#flag"+trIndex).html('');
	$("#flag"+trIndex).hide();
}
function deleteAll(){
	var arr = new Array();
	var va = 0;
	var arrChk=$("input[name='checktips[]']:checked");
	$(arrChk).each(function(){
		va = this.value
		arr.push(this.value);                      
	});  
	$('#key').val(arr.join(","));
	 
	if($('#key').val() != ""){
		location.href="deleteTips.php?file=<?=$_GET['file']?>&key="+$('#key').val();
	}
	
}
</script>
<?php
$file = $_GET['file'];
$sysini = "../include/sys.ini";  //系统的配置文件
if (file_exists($sysini))
{
	$sysini_array = parse_ini_file($sysini);
	if(isset($sysini_array[$file]))
	{
	   $url = filter_var($sysini_array[$file],FILTER_SANITIZE_STRING); 
	}
	else 
	{
		echo "读取系统的配置文件有误"; 
		exit();       
	} 
}
else
{
	
	echo "数据库连接的配置文件不存在"; 
	exit("请与管理员联系");
}
$data = file_get_contents($url);  
$array = explode(",",$data);   
?>
 <body>
	<form action="saveEnter.php" method="post">
  	<div class="container">
  	
	<div>
		<fieldset>
		<legend>进牌桌过渡页tips</legend>	
		 			
		</fieldset>
		 
		
	</div>
	<div span="span1" style="float:left;">
			<label>&nbsp;</label> 
			<p><input type="button" value="添&nbsp;&nbsp;加" class="btn" onclick="addTips()"/></p>
		</div>
	<div>
		<table class="table table-bordered table-condensed table-hover">
			<tr class="info">			
				<td width="5%"><strong><input type="checkbox" onclick="selectAll(this)"></strong></td>
				<td width="85%"><strong>内容</strong></td>
				<td width="15%"><strong>操作</strong></td>
			</tr>
			<tbody id="dataList">
			
			
			<?php
			foreach($array as $key =>$val){
				//$tips = str_replace("tips:","",$val);
				$tips = $val;
				
			?>
			<tr>
			<td><input type="checkbox" name="checktips[]" value="<?=$key?>"></td>
			<td><input type="text" name="tips[]" value="<?=$tips?>" style="width: 750px;height:30px;margin-top:5px;margin-bottom:5px;"></td>
			<td>&nbsp;&nbsp;&nbsp;&nbsp;<a style="margin-top:15px;" href="deleteTips.php?file=<?=$file?>&key=<?=$key?>">删除</a></td>
			</tr>
			<?php
			}
			?>
			
			
			
			</tbody>
		</table>
	</div>
	<div span="span1" style="float:left;">
			<label>&nbsp;</label>
			<p><input type="button" value="删除所选" class="btn" onclick="deleteAll()"/></p>
			<p><input type="submit" value="保&nbsp;&nbsp;存" class="btn" /></p>
		</div>
	 
	 <input type="hidden" id="key" name="key" value="">
	 <input type="hidden" id="file" name="file" value="<?=$file?>">
	
	</div>
	</form>
  </body>
