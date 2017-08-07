<!DOCTYPE html>
<html lang="zh-CN" ng-app="hall">
<head php-include="static/head"></head>
<?php require 'js_include.php'; ?>

<script src="../js/fruit_online_detail.js"></script>
<!--<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.11.2/moment.min.js"></script>-->
<script src="../plugins/moment/moment.js"></script>
<script src="../plugins/daterangepicker/daterangepicker.js"></script>

<link rel="stylesheet" href="../plugins/daterangepicker/daterangepicker-bs3.css">

<body class="hold-transition skin-blue sidebar-mini">
<div class="wrapper" ng-controller="FruitOnlineDetail">

    <!-- Content Wrapper. Contains page content -->

    <div class="content-wrapper">
        <section class="content">

            <input type="text" id="today" value="{{today}}"/>
            <input type="button" value="刷新" ng-click="refresh()"/>

            <div class="chart" style="background-color: #ffffff;padding: 13px;margin: 15px;">
                <a href="#" data-skin="skin-red-light" class="btn btn-xs"
                   style="background-color: #3b8bba;border-color: #9e9e9e; padding: 2px;width: 16px;height: 16px;"><i
                        class="fa"></i></a>
                今日
                &nbsp;&nbsp;&nbsp;
                <a href="#" data-skin="skin-red-light" class="btn btn-xs"
                   style="background-color: #c1c7d1;border-color: #9e9e9e; padding: 2px;width: 16px;height: 16px;"><i
                        class="fa"></i></a>
                昨日
                &nbsp;&nbsp;&nbsp;
                <a href="#" data-skin="skin-red-light" class="btn btn-xs"
                   style="background-color: #A00000;border-color: #9e9e9e; padding: 2px;width: 16px;height: 16px;"><i
                        class="fa"></i></a>
                上周
                <div id="dailyChartDiv">

                </div>
            </div>


        </section>

    </div>
    <!-- /.content-wrapper -->
    <footer class="main-footer">

    </footer>

</div>
<!-- ./wrapper -->
</body>
</html>
<script src="../plugins/chartjs/Chart.js"></script>
