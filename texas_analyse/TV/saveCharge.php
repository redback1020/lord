 <link rel="stylesheet" type="text/css" href="../bootstrap/css/bootstrap.min.css" />

<?php
require_once 'curl.php';   
$array = $_POST;
$obj = fetch_page('http://112.124.4.59:9898/charge',$array);
$code = $obj['code'];

if($code == 0){
	$data = $obj['data']['coins'];
	$msg = $obj['msg']."now value:".$data['val'].", old value:".$data['oldValue'];
}else{
	$msg = $obj['msg'];
} 
  $time = time();
$key = "qwe!@#321";
$sign = md5($key.$time);
?>
 <body>
	 
  	<div class="container">
  	
	<div>
		<fieldset>
		<legend>系统提示</legend>	
		<div class="">
			<div class="">
				 
				<?=$msg?><a href="charge.php?time=<?=$time?>&sign=<?=$sign?>" style="font-size:20px;	">返回</a>继续操作
			</div>
			 
			
		</div>				
		</fieldset>
		 
		
	</div>
	  
  </body> 