<?php
require_once '../manage/checkPriv.php';
require_once '../include/database.class.php';
?>
<script type="text/javascript" src="../js/jquery.js"></script> 
<script src="../js/My97DatePicker/WdatePicker.js" language="javascript"></script>
<link rel="stylesheet" type="text/css" href="../bootstrap/css/bootstrap.min.css" />
<script>
$(function(){ 
	var start = '<?=isset($_POST['start'])?$_POST['start']:date("Y-m-d",strtotime("-6 days"))?>'
	var end = '<?=isset($_POST['end'])?$_POST['end']:date("Y-m-d",time())?>'
	var type = '<?=isset($_POST['type'])?$_POST['type']:"all"?>'
	var channel = '<?=isset($_POST['channel'])?$_POST['channel']:"all"?>'
$("#channel").val(channel);
$("#start").val(start);
		$("#end").val(end);
		$("#type").val(type);
	//query();
});
 

</script>
 <body>
  	<div class="">
  
  	
	<form method="post">
	 
	<div >
		<fieldset>
		<legend>用户的详细信息</legend>	
		<div class="row">
			 
			<div class="span2">
				<label>站点：</label>
				<select class="span2" id="type" name="type">
					<option value="all">全部</option>
					<option value="1">TV</option>
					<option value="0">MP</option> 
				</select>
			</div>
			<?php
			$sql = "select * from lord_game_user  where channel != '' group by channel";
		 
			$row = $db -> query($sql)-> fetchAll();
			?> 
			<div class="span2" >
				<label>渠道号：</label>
				<select class="span2" id="channel" name="channel">
					<option value="all">全部</option>
					<?php
					foreach($row as $val){
						 
						echo '<option value="'.$val['channel'].'">'.$val['channel'].'</option>';
					}
					?>
					 
				</select>
				
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
	</form>
	<?php 
	$where = "";
	if(isset($_POST['channel'])&&$_POST['channel']!="all") {$where .= " and m.channel='".$_POST['channel']."'";}
	if(isset($_POST['start'])&&$_POST['start']!="") {$start=$_POST['start'];}else $start=date("Y-m-d",strtotime("-6 days"));
	if(isset($_POST['end'])&&$_POST['end']!="") {$end=date("Y-m-d",strtotime($_POST['end'])+24*3600);}else $end = date("Y-m-d",time()+24*3600); 
	if(isset($_POST['type'])&&$_POST['type']!="all") {$where .= " and m.is_tv ='".$_POST['type']."'";}
	$begin = $start ;
	$finish = $end ;
	//注册用户数
	$sql = "select count(distinct m.uid) as cn,date(a.add_time) as dd from lord_game_analyse a, lord_game_user m where a.uid = m.uid and a.add_time>='".$start."' and a.add_time<'".$end."'".$where." group by dd";
	//echo $sql;
	$row = $db -> query($sql) -> fetchAll(); 
	foreach($row as $val){
		$array_zc[$val['dd']] = $val['cn']; 
	}
	 
	//次日留存
	while($begin<$finish){
		$start1 = $begin;
		$start2 = date("Y-m-d",(strtotime($begin)+3600*24)); 
		$start3 = date("Y-m-d",(strtotime($begin)+3600*24*2)); 
		/*$sql = "select count(distinct b.uid) as cn from mobile_match_bk b, (select a.uid from user_analyse a, mobile_user m where a.uid = m.uid and a.add_time>='".$start1."' and a.add_time<'".$start2."'".$where.")c where b.uid = c.uid and date(b.add_time)= '".$start2."'";
		$row = $db -> query($sql)->fetchAll();
		foreach($row as $val){
			$array_lc[$start] = $val['cn']; 
		}*/
		$sql = "select count(distinct m.uid) as cn, login_time from lord_log_login m where  login_time>='".$start2."' and login_time<'".$start3."' and reg_time>='".$start1."' and reg_time<'".$start2."'".$where."";
		//echo $sql.'<br>';
		$row = $db -> query($sql)->fetchAll();
		foreach($row as $val){
			$array_lc[$begin] = $val['cn']; 
		}
		//当日转换
		$sql = "select count(distinct m.uid) as cn, login_time from lord_log_login m where  login_time>='".$start1."' and login_time<'".$start2."' and reg_time>='".$start1."' and reg_time<'".$start2."'".$where."";
		//echo $sql.'<br>';
		$row = $db -> query($sql)->fetchAll();
		foreach($row as $val){
			$array_zh[$begin] = $val['cn']; 
		}
		 
		//当日注册付费用户
		$sql = "select count(distinct(m.uid)) as cn,date(a.add_time) as dd from (SELECT uid,time FROM lord_game_charge )c, lord_game_user m , lord_game_analyse a where c.uid = m.uid and m.uid = a.uid and a.add_time>='".$start1."' and a.add_time<'".$start2."'".$where." and c.time>='".$start1."' and c.time<'".$start2."'";
		// echo $sql;
		$row = $db -> query($sql)->fetchAll();
		foreach($row as $val){
			$array_ff[$val['dd']] = $val['cn']; 
		}
		$begin = date("Y-m-d",(strtotime($begin)+3600*24));  
		
		
	}
	
	//活跃用户数
	$sql = "select count(distinct(m.uid)) as cn, date(login_time) as dd from lord_log_login m where  m.login_time>='".$start."' and m.login_time<'".$end."'".$where." group by dd";
	$row = $db -> query($sql)->fetchAll();
	foreach($row as $val){
		$array_hy[$val['dd']] = $val['cn']; 
	}	
		
	
	
	//当日所有付费用户
	$sql = "select count(distinct(m.uid))as cn,date(c.time) as dd from (SELECT uid,time FROM lord_game_charge )c, lord_game_user m  where c.uid = m.uid and c.time>='".$start."' and c.time<'".$end."'".$where." group by dd";
	  
	$row = $db -> query($sql)->fetchAll();
	foreach($row as $val){
		$array_user[$val['dd']] = $val['cn']; 
	}
	//当日付费总额
	$sql = "select sum(c.gold) as cn,date(c.time) as dd from (SELECT uid,time,gold FROM lord_game_charge)c, lord_game_user m  where c.uid = m.uid and c.time>='".$start."' and c.time<'".$end."'".$where." group by dd";
	$row = $db -> query($sql)->fetchAll();
	foreach($row as $val){
		$array_pay[$val['dd']] = $val['cn']; 
	}
	?>
 
	<div>
		<table class="table table-bordered table-condensed table-hover">
			<tr class="info">			
				<td nowrap><strong>日期</strong></td> 
				<td nowrap><strong>当日注册用户</strong></td>
				<td nowrap><strong>次日留存</strong></td>
                <td nowrap><strong>次日留存率</strong></td>
				<td nowrap><strong>当日活跃用户</strong></td>
				<td nowrap><strong>当日用户转化</strong></td> 
				<td nowrap><strong>当日注册当日付费用户</strong></td> 
				<td nowrap><strong>付费转化率</strong></td> 
				<td nowrap><strong>当日所有付费用户</strong></td> 
				<td nowrap><strong>当日付费总额</strong></td> 
				<td nowrap><strong>ARPPU</strong></td> 
			</tr> 
			<?php
			$begin = $start ;
			$finish = $end ;
			$a_zc = $a_ff = $a_user = $a_pay = $a_lc = $a_zh = $a_hy = 0;
			while($begin<$finish){
			?>
			<tr> 
				<td><?=$begin?></td>
				<td><?=$array_zc[$begin]?></td>
				<td><?=$array_lc[$begin]?></td>
                
                <td><?=$array_zc[$begin] / $array_zc[$begin]?></td>
                
				<td><?=$array_hy[$begin]?></td>
				<td><?=$array_zh[$begin]?></td>
				<td><?=$array_ff[$begin]?></td>
				<td><?=round($array_ff[$begin]*100/$array_zc[$begin],2)?>%</td>
				<td><?=$array_user[$begin]?></td>
				<td><?=$array_pay[$begin]?></td>
				<td><?=round($array_pay[$begin]*100/$array_user[$begin],2)/100?></td>
			</tr>
			<?php
				$a_zc += $array_zc[$begin];
				$a_ff += $array_ff[$begin];
				$a_lc += $array_lc[$begin];
				$a_zh += $array_zh[$begin];
				$a_hy += $array_hy[$begin];
				$a_user += $array_user[$begin];
				$a_pay += $array_pay[$begin];
				$begin = date("Y-m-d",(strtotime($begin)+3600*24)); 
			}
			?>
			<tr> 
				<td>总计</td>
				<td><?=$a_zc?></td>
				<td><?=$a_lc?></td>
				<td><?=$a_hy?></td>
				<td><?=$a_zh?></td>
				 
				<td><?=$a_ff?></td>
				<td><?=round($a_ff*100/$a_zc,2)?>%</td>
				<td><?=$a_user?></td>
				<td><?=$a_pay?></td>
				<td><?=round($a_pay*100/$a_user,2)/100?></td>
			</tr>
		</table>
	</div>
	  
	
	</div>
  </body>
