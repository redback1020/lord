<?php
require_once '../include/priv.php';
?> 
<link rel="stylesheet" type="text/css" href="../bootstrap/css/bootstrap.min.css" />
 
 <body>
 <form action="saveData.php" method="post">
  	<div class="container">
  
  	
	
	<div>
		<fieldset>
		<legend>加入人为数据</legend>	
				
	</fieldset>
	</div>

	<div>
		<table class="table table-bordered table-condensed table-hover">
			<tr>			
				<td width="10%">筹码</td>
				<td width="90%"><input type="text" id="chips" name="chips" style="width:150px;height:30px;"></td>
				 
				 
			</tr>
			 
		</table>
	</div>
		<div span="span1" style="float:left;">
			<label>&nbsp;</label>
			 
			<p><input type="submit" value="保&nbsp;&nbsp;存" class="btn" /></p>
		</div>  
	</div>
</form>
  </body>
