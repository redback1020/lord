<?php
require_once '../manage/checkPriv.php';
$start = ( isset($_POST['start']) && $_POST['start'] != "" ) ? date("Y-m-d ", strtotime($_POST['start'])) : date("Y-m-d ", time());
$_POST['start'] = $start;
?>


<script src="../js/jquery.js"></script>
<script src="../js/My97DatePicker/WdatePicker.js" language="javascript"></script>
<link rel="stylesheet" type="text/css" href="../bootstrap/css/bootstrap.min.css" />
<script>
$(function(){
    var start = '<?=$_POST['start']?>';

    $("#start").val(start);

    query();
});
var pageSize = 10;
var pageIndex = 0;
function query(){
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
      var start = $('#start').val();

	    $.post("matchHistorySearch.php",{
		    pageSize: pageSize,
		    pageIndex: index,
            start: start


        },function(result) {
		    if(result!=null && result!=""){
			    var dataList=eval("("+result+")");
			    var dataListHtml = "";
			    for(var i=0;i<dataList.data.length;i++){
				    var o = dataList.data[i];

                    var gameScore = new Array();
                    if(typeof o.gameScore == 'object') {
                        for(var k in o.gameScore) {
                            gameScore.push("uid"+ k + ":" + o.gameScore[k]+ "分");
                        }
                    }
                    var gamePrizeCoins = new Array();
                    if(typeof o.gamePrizeCoins == 'object') {
                        for(var k in o.gamePrizeCoins) {
                            gamePrizeCoins.push("uid"+ k + ":" + o.gamePrizeCoins[k]+ "乐豆");
                        }
                    }
                    var gamePrizePoint = new Array();
                    if(typeof o.gamePrizePoint == 'object') {
                        for(var k in o.gamePrizePoint) {
                            gamePrizePoint.push("uid"+ k + ":" + o.gamePrizePoint[k]+ "分");
                        }
                    }





                    var gamePrizeProps = new Array();
                    for(var uid in o.gamePrizeProps) {
                        var user_props = new Array();
                        for(var k in o.gamePrizeProps[uid]) {
                            user_props.push(o.gamePrizeProps[uid][k]);
                        }
                        gamePrizeProps.push(uid + ':' + user_props.join('|'));
                    }

				    dataListHtml += "<tr class='table-body'>";
                    dataListHtml += "<td>"+o.gamesId+"</td>";
                   //dataListHtml += "<td>"+o.gameLevel+"</td>";
                    dataListHtml += "<td>"+o.gamePool+"</td>";
                    dataListHtml += "<td>"+o.gamePerson+"</td>";
				    dataListHtml += "<td>"+o.gamePlay+"</td>";
                    dataListHtml += "<td style=\"white-space:nowrap\">"+gameScore.join(',')+"</td>";
                    dataListHtml += "<td style=\"white-space:nowrap\">"+gamePrizeCoins.join(',')+"</td>";
                    dataListHtml += "<td style=\"white-space:nowrap\">"+gamePrizePoint.join(',')+"</td>";
                    dataListHtml += "<td style=\"white-space:nowrap\">"+gamePrizeProps.join(',')+"</td>";
                    dataListHtml += "<td>"+o.gameStart+"</td>";
                    dataListHtml += "<td>"+o.gameOver+"</td>";
                    dataListHtml += "</tr>";

			}
			if(dataListHtml=="" && isNext){
				alert("已经是最后一页");
				pageIndex--;
			}else{
				$("#count").html(Math.ceil(dataList.cn/pageSize));


				$("#dataList").html(dataListHtml);
				$("#pageIndex").html(pageIndex+1);

				$("#pagination").show();
			}
		}else{
			alert("获取数据失败！");
		}
	});
  }


</script>
<body>
  	<div class="">
        <div>
		<fieldset>
		<legend>竞技场记录</legend>
		<div class="row">

            <div class="span4">
				<label>日期：</label>
                <input style="height:30px;" class="span3" type="text"  id="start" class="textbox dtime" readonly onclick="WdatePicker({dateFmt:'yyyy-MM-dd '})"/>
            </div>


            <div span="span1" style="float:right;">
				<label>&nbsp;</label>
				<input type="button" value="查&nbsp;&nbsp;询" onclick="query()" class="btn" />
			</div>
		</div>
	</fieldset>
	</div>

	<div>

		<table class="table table-bordered table-condensed table-hover" style="font-size:12px;">
			<tr class="info">

                <td nowrap><strong>非普通模式ID_房间ID_周ID_场次ID</strong></td>
                
                <td nowrap><strong>本场奖池</strong></td>
                <td nowrap><strong>几人报名</strong></td>
                <td nowrap><strong>剩余几人存活</strong></td>
                <td nowrap><strong>本场得分</strong></td>
                <td nowrap><strong>本场奖励乐豆</strong></td>
                <td nowrap><strong>本场奖励积分</strong></td>
                <td nowrap><strong>本场奖励道具</strong></td>
                <td nowrap><strong>本场开始时间</strong></td>
                <td nowrap><strong>本场结束时间</strong></td>
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


