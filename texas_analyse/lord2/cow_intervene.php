<?php
header("Content-type: text/html; charset=utf-8");
if($_GET["server"] == "test")
{
     $redisConfig = ["host"=>"127.0.0.1","port"=>6379];
}
else
{
     $redisConfig = ["host"=>"10.10.40.48","port"=>6379];
}
$redis = new Redis();
$redis->pconnect($redisConfig["host"],$redisConfig["port"]);

$seatId = $redis->get('cow:intervene')===false?-1:intval($redis->get('cow:intervene'));
if($_GET["action"]=="modify")
{
   $seatId = intval($_GET["seatId"]);
   $redis->set("cow:intervene",$seatId);
}
?>
<body>
<script>
function modify(){
var seatId=myform.name.value;
this.location = 'cow_intervene.php?server=s1&action=modify&seatId=' + seatId;
alert("成功");
}
</script>
<div>
<?php 
echo 
    "<form name=\"myform\">
     <dl>
	<dd>干预位置id :<input class=\"span2\" name=\"name\" type=\"text\" value=\"$seatId\" /><input id=\"modifySeatId\" type=\"button\" value=\"修改\" onclick=\"modify()\"/></dd>
    </dl>
</form>";
?>
</div>
</body>
</html>
