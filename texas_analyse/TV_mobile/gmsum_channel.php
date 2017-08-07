<?php
require_once '../include/priv.php';
require_once '../include/database.class.php';
?>
<script type="text/javascript" src="../js/jquery.js"></script> 
<script src="../js/My97DatePicker/WdatePicker.js" language="javascript"></script>
<link rel="stylesheet" type="text/css" href="../bootstrap/css/bootstrap.min.css" />
<script>
$(function(){ 
	 
	var start = '<?=isset($_POST['start'])?$_POST['start']:date("Y-m-d",time())?>'
	var end = '<?=isset($_POST['end'])?$_POST['end']:date("Y-m-d",time())?>'
	var type = '<?=isset($_POST['type'])?$_POST['type']:"all"?>'
	$("#start").val(start); 
	$("#end").val(end); 
	$("#type").val(type); 
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
	$pdo = new DB();
	$db = $pdo->getDB();
	$where = ""; 
	 
	if(isset($_POST['start'])&&$_POST['start']!="") {$start=$_POST['start'];}else $start=date("Y-m-d",time());
	if(isset($_POST['end'])&&$_POST['end']!="") {$end=date("Y-m-d",strtotime($_POST['end'])+24*3600);$start3=date("Y-m-d",strtotime($_POST['end'])+24*3600*2);}else {$end = date("Y-m-d",time()+24*3600);$start3 = date("Y-m-d",time()+24*3600*2); }
	
	$diff = (strtotime($end) - strtotime($start))/(24*3600); 
	if(isset($_POST['type'])&&$_POST['type']!="all") {$where .= " and m.is_tv ='".$_POST['type']."'";}
	$begin = $start ;
	$finish = $end ;
	//注册用户数
	$sql = "select count(distinct m.uid) as cn,m.channel from mobile_analyse a, mobile_user m where a.uid = m.uid and a.add_time>='".$start."' and a.add_time<'".$end."'".$where." group by channel order by cn desc ";	  
	$row = $db -> query($sql)->fetchAll(); 
	foreach($row as $val){
		$array_zc[$val['channel']] = $val['cn']; 
	}
	 
	//次日留存
	if($diff == 1){
		$sql = "select count(distinct m.uid) as cn, channel from mobile_game_login m where  add_time>='".$end."' and add_time<'".$start3."' and reg_time>='".$start."' and reg_time<'".$end."'".$where." group by channel";
		 
		$row = $db -> query($sql)->fetchAll();
		foreach($row as $val){
			$array_lc[$val['channel']] = $val['cn']; 
		}
		//当日转换
		$sql = "select count(distinct m.uid) as cn, channel from mobile_game_login m where  add_time>='".$start."' and add_time<'".$end."' and reg_time>='".$start."' and reg_time<'".$end."'".$where." group by channel";
		//echo $sql.'<br>';
		$row = $db -> query($sql)->fetchAll();
		foreach($row as $val){
			$array_zh[$val['channel']] = $val['cn']; 
		}
	}
	
	//当日注册付费用户
	$sql = "select count(distinct(m.uid)) as cn,m.channel  from ((SELECT uid,add_time FROM mobile_charge where sts = 1)UNION ALL (SELECT uid,add_time FROM mobile_charge_log where money>0 and (misc = 'web' or misc='taobao')))c, mobile_user m , user_analyse a where c.uid = m.uid and m.uid = a.uid and a.add_time>='".$start."' and a.add_time<'".$end."'".$where." and c.add_time>='".$start."' and c.add_time<'".$end."' group by m.channel";
	
	$row = $db -> query($sql)->fetchAll();
	foreach($row as $val){
		$array_ff[$val['channel']] = $val['cn']; 
	}
	 
		
	 
	 
	//活跃用户数
	$sql = "select count(distinct(m.uid)) as cn, channel from mobile_game_login m where  m.add_time>='".$start."' and m.add_time<'".$end."'".$where." group by channel";
	$row = $db -> query($sql)->fetchAll();
	foreach($row as $val){
		$array_hy[$val['channel']] = $val['cn']; 
	}
	
	//当日所有付费用户
	$sql = "select count(distinct(m.uid))as cn,m.channel from ((SELECT uid,add_time FROM mobile_charge where sts = 1))c, mobile_user m  where c.uid = m.uid and c.add_time>='".$start."' and c.add_time<'".$end."'".$where." group by m.channel order by cn desc";
	  
	$row = $db -> query($sql)->fetchAll();
	foreach($row as $val){
		$array_user[$val['channel']] = $val['cn']; 
	}
	//当日付费总额
	$sql = "select sum(c.in_money) as cn,m.channel from ((SELECT uid,add_time,in_money FROM mobile_charge where sts = 1))c, mobile_user m  where c.uid = m.uid and c.add_time>='".$start."' and c.add_time<'".$end."'".$where." group by m.channel";
	$row = $db -> query($sql)->fetchAll();
	foreach($row as $val){
		$array_pay[$val['channel']] = $val['cn']; 
	}
	$array_all = array_merge($array_hy,$array_zc);
	arsort($array_all);
	?>
 
	<div>
		<table class="table table-bordered table-condensed table-hover">
			<tr class="info">			
				<td nowrap><strong>渠道名称</strong></td> 
				<td nowrap><strong>注册用户</strong></td>
				<td nowrap><strong>次日留存</strong></td>
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

			foreach($array_all as $begin=>$val){
			?>
			<tr> 
				<td><?=$begin?></td>
				<td><?=$array_zc[$begin]?></td> 
				<td><?=$diff==1?$array_lc[$begin]:'-'?></td>
				<td><?=$array_hy[$begin]?></td>
				<td><?=$diff==1?$array_zh[$begin]:'-'?></td>
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
				<td><?=$diff==1?$a_lc:'-'?></td>
				<td><?=$a_hy?></td>
				<td><?=$diff==1?$a_zh:'-'?></td>
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
