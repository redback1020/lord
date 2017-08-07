<?php
require_once '../manage/checkPriv.php';
require_once '../include/database.class.php';

$start = ( isset($_POST['start']) && $_POST['start'] != "" ) ? date("Ymd", strtotime($_POST['start'])) : date("Ymd", time());
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
        var uid = $('#uid').val();
        var cool_num = $('#cool_num').val();
        $.post("userLoginoutSearch.php",{
            pageSize: pageSize,
            pageIndex: index,
            start: start,
            uid:uid,
            cool_num:cool_num
        },function(result){
            if(result!=null && result!=""){
                var dataList=eval("("+result+")");
                var dataListHtml = "";
                for(var i=0;i<dataList.data.length;i++){
                    var o = dataList.data[i];
                    dataListHtml += "<tr class='table-body'>";
                    dataListHtml += "<td><a href=\"userInfo.php?uid="+o.uid+"\">"+o.uid+"</td>";
                    dataListHtml += "<td>"+o.dateid+"</td>";
                    dataListHtml += "<td>"+o.login_coins+"</td>";
                    dataListHtml += "<td>"+o.login_gold +"</td>";
                    dataListHtml += "<td>"+o.login_time+"</td>";
                    dataListHtml += "<td>"+o.last_action+"</td>";
                    dataListHtml += "<td>"+o.last_time+"</td>";
                    dataListHtml += "<td>"+o.logout_coins+"</td>";
                    dataListHtml += "<td>"+o.logout_gold+"</td>";
                    dataListHtml += "<td>"+o.logout_time+"</td>";
                    dataListHtml += "<td>"+o.online_time+"</td>";
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
            <legend>用户登录登出记录</legend>
            <div class="row">
                <div class="span2">
                    <label>UID：</label>
                    <input class="span2" type="text" id="uid" name="uid" style="height:30px"/>
                </div>
                <div class="span2">
                    <label>编号ID：</label>
                    <input class="span2" type="text" id="cool_num" name="cool_num" style="height:30px"/>
                </div>
                <div class="span4">
                    <label>时间：</label>
                    <input style="height:30px;" class="span3" type="text"  id="start" class="textbox dtime" readonly onclick="WdatePicker({dateFmt:'yyyyMMdd '})"/>
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

                <td nowrap><strong>UID</strong></td>
                <td nowrap><strong>日期</strong></td>
                <td nowrap><strong>登入乐豆</strong></td>
                <td nowrap><strong>登入乐币</strong></td>
                <td nowrap><strong>登入时间</strong></td>
                <td nowrap><strong>最后操作</strong></td>
                <td nowrap><strong>操作时间</strong></td>
                <td nowrap><strong>登出乐豆</strong></td>
                <td nowrap><strong>登出乐币</strong></td>
                <td nowrap><strong>登出时间</strong></td>
                <td nowrap><strong>在线时间</strong></td>
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
