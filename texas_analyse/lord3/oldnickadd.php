<?php
require_once '../manage/checkPriv.php';
?>
<script src="../js/jquery.js"></script>
<script src="../js/My97DatePicker/WdatePicker.js" language="javascript"></script>
<link rel="stylesheet" type="text/css" href="../bootstrap/css/bootstrap.min.css" />
<script>
    $(function(){
        var start = '<?=$_POST['start']?>';
        var end = '<?=$_POST['end']?>';
        $("#start").val(start);
        $("#end").val(end);
        query();
    });
    var pageSize = 10;
    var pageIndex = 0;

    function query2(){
        var cool_num = $('#cool_num').val();
        var nick = $('#nick').val();
        $.post("oldnickaddupdate.php",{
            cool_num:cool_num,
            nick:nick
        },function(data){
            if(data.result == true) { alert("操作成功");}
            else {}
        },'json')
    }









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
        var cool_num = $('#cool_num').val();
        var nick = $('#nick').val();
        $.post("oldnickaddsearch.php",{
            pageSize: pageSize,
            pageIndex: index,
            cool_num:cool_num,
            nick:nick
        },function(result){
            if(result!=null && result!=""){
                var dataList=eval("("+result+")");
                var dataListHtml = "";
                for(var i=0;i<dataList.data.length;i++){
                    var o = dataList.data[i];
                    dataListHtml += "<tr class='table-body'>";
                    dataListHtml += "<td>"+o.cool_num+"</td>";
                    dataListHtml += "<td>"+o.nick+"</td>";
                    dataListHtml += "<td>"+o.coins+"</td>";
                    dataListHtml += "<td>"+o.coupon+"</td>";
                    dataListHtml += "<td>"+o.login+"</td>";
                    dataListHtml += "<td>"+o.matches+"</td>";
                    dataListHtml += "<td>"+o.win+"</td>";
                    dataListHtml += "<td>"+o.add_time+"</td>";
                    dataListHtml += "<td>"+o.last_login+"</td>";
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
            <legend>账号转移</legend>
            <div class="row">


                <div class="span2">
                    <label>旧昵称：</label>
                    <input class="span2" type="text" id="nick" name="nick" style="height:30px"/>
                </div>
                <div class="span2">
                    <label>新编号ID：</label>
                    <input class="span2" type="text" id="cool_num" name="cool_num" style="height:30px"/>
                </div>

                <div span="span1" style="float:right;">
                    <label>&nbsp;</label>
                    <input type="button" value="转&nbsp;&nbsp;移" onclick="query2()" class="btn" />
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

                <td nowrap><strong>编号ID</strong></td>
                <td nowrap><strong>昵称</strong></td>
                <td nowrap><strong>乐豆</strong></td>
                <td nowrap><strong>奖券</strong></td>
                <td nowrap><strong>登录次数</strong></td>
                <td nowrap><strong>总场数</strong></td>
                <td nowrap><strong>赢场数</strong></td>
                <td nowrap><strong>注册时间</strong></td>
                <td nowrap><strong>上次登录</strong></td>
            </tr>
            <tbody id="dataList">
            </tbody>
        </table>
    </div>



    </td></tr>
    </table>

</div>
</body>