<?php
	require_once '../manage/checkPriv.php';
?>
<html>
<head>
    <title>用户图表 - 在线/活跃</title> 
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
	body{margin:0;height: 100%; font-size:12px; background:#EFEFEF;}
	#box{width:100%; height: 100%; margin:0px auto 0;overflow:auto;}
	#tab_nav{margin:0; padding:0; height:25px; line-height:24px;}
	#tab_nav li{float:left; margin:0 3px; list-style:none; border:1px solid #999; border-bottom:none; height:24px; width:80px; text-align:center; background:#FFF;}
	a{font:bold 14px/24px "微软雅黑", Verdana, Arial, Helvetica, sans-serif; color:green; text-decoration:none;}
	a:hover{color:red;}
	#tab_content{width:99%;height: 100%; border:1px solid #999;  text-align:center; background:#FFF; overflow:auto;}
	#t_1,#t_2,#t_3,#t_4{width:100%; height: 100%;}
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
		<li id="li1" onclick="showTab(1)" style="background-color:yellow"><a style="cursor:pointer">日 活跃</a></li>
		<li id="li2" onclick="showTab(2)"><a style="cursor:pointer" >月 活跃</a></li>
		<li id="li3" onclick="showTab(3)"><a style="cursor:pointer" >日 在线</a></li>
		<li id="li4" onclick="showTab(4)"><a style="cursor:pointer" >月 在线</a></li>
		<li id="li5" onclick="showTab(5)"><a style="cursor:pointer" >在线详情</a></li>
		<li id="li6" onclick="showTab(6)"><a style="cursor:pointer" >在线时长</a></li>
	</ul>
    <div id="tab_content">
		<div id="t_1">
			<div class="hid" >
				<iframe id="ifr1" src="chart_playing.php?date=<?=date("Y-m-d")?>&type=hour" width="99%" height="100%" frameborder=0 scroll="auto"></iframe>
			</div>
		</div>
		<div id="t_2" style="display:none">
			<div class="hid" >
				<iframe id="ifr2" src="chart_playing_month.php?date=<?=date("Y-m-d")?>&type=day" width="99%" height="100%" frameborder=0 scroll="auto"></iframe>
			</div>
		</div>
		<div id="t_3" style="display:none">
			<div class="hid" >
				<iframe id="ifr3" src="chart_online.php?date=<?=date("Y-m-d")?>&type=hour" width="99%" height="100%" frameborder=0 scroll="auto"></iframe>
			</div>
		</div>
		<div id="t_4" style="display:none">
			<div class="hid" >
				<iframe id="ifr4" src="chart_online_month.php?date=<?=date("Y-m-d")?>&type=day" width="99%" height="100%" frameborder=0 scroll="auto"></iframe>
			</div>
		</div>
		<div id="t_5" style="display:none">
			<div class="hid" >
				<iframe id="ifr5" src="chart_online_detail.php?date=<?=date("Y-m-d")?>&type=hour" width="99%" height="100%" frameborder=0 scroll="auto"></iframe>
			</div>
		</div>
		<div id="t_6" style="display:none">
			<div class="hid" >
				<iframe id="ifr6" src="chart_online_time.php?date=<?=date("Y-m-d")?>" width="99%" height="100%" frameborder=0 scroll="auto"></iframe>
			</div>
		</div>
    </div>
</div>


</body>
</html>
