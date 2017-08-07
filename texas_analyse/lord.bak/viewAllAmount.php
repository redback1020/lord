 
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <title>数据查看 </title> 
    <script src="../js/jquery.js" type="text/javascript"></script> 
    <script lang="javascript" type="text/javascript">
        
		 $(function () {
            $("#myul li div").hide(); 
            $("#myul li span").addClass("hand"); 
            $("#myul li span").click(function () {
                $(this).toggleClass("current");
                $(this).parent().siblings().find("span").removeClass("current");
                $(this).parent().find("div.hid").slideToggle("fast");
                $(this).parent().siblings().find("div").slideUp("fast");   
				
            });
			 
        })
		 
		var bz = 1;var obj ;
		function showTab(flag){
			if(flag != bz){
				 
				$("#t_"+flag).show();
				$("#t_"+bz).hide();
				$('#li'+flag).css('background-color', "yellow");
				$('#li'+bz).css('background-color', "");
				bz = flag; 
				 
				
			}
		}
    </script>
	<style type="text/css">
		body{margin:0; font-size:12px; background:#EFEFEF;}
	#box{width:100%; height:550px; margin:0px auto 0;overflow:auto;}
	#tab_nav{margin:0; padding:0; height:25px; line-height:24px;}
	#tab_nav li{float:left; margin:0 3px; list-style:none; border:1px solid #999; border-bottom:none; height:24px; width:80px; text-align:center; background:#FFF;}
	a{font:bold 14px/24px "微软雅黑", Verdana, Arial, Helvetica, sans-serif; color:green; text-decoration:none;}
	a:hover{color:red;}
	#tab_content{width:99%; height:523px; border:1px solid #999;  text-align:center; background:#FFF; overflow:auto;}
	#t_1,#t_2,#t_3{width:100%; height:523px;}
	#myul{ width:1004px;}
    #myul li { list-style:none;border:1px solid #96C2F1; padding:1px;}
    #myul li span{ list-style:none; background:#B2D3F5; width:980px; height:45px; display:block; padding-left:20px;  }
    #myul li span.hand{ cursor:pointer;background:#B2D3F5 url(img/right.gif) no-repeat 5px center;}
    #myul li span.current{ background:#b2d300 url(img/down.gif) no-repeat 5px center; }
    #myul li div{ background:#EFF7FF;width:990px; padding:0px 5px 5px 5px; }
  
	</style>
</head>
<body>



<div id="box">
	<ul id="tab_nav">
		 
    	<li id="li1" onclick="showTab(1)" style="background-color:yellow"><a style="cursor:pointer">日分时线</a></li>
        <li id="li2" onclick="showTab(2)"><a style="cursor:pointer" >月线</a></li>
          
    </ul>
    <div id="tab_content">
    	<div id="t_1">
		 
				 
		   <div class="hid" >
			 <iframe id="ifr1" src="online_chart.php?date=<?=date("Y-m-d",time())?>&type=hour" width="99%" height="500px;" frameborder=0></iframe>
		   </div>

		</div>
        <div id="t_2" style="display:none">

		   <div class="hid" >
			 <iframe id="ifr2" src="online_chart_day.php?date=<?=date("Y-m-d",time())?>&type=day" width="99%" height="500px;" frameborder=0></iframe>
		   </div>
 
		</div>
         
    </div>
</div>

 
  
 
 
 
 
 
 
    
</body>
</html>
