

<!DOCTYPE html>
<html lang="zh-CN" ng-app="hall">
<head php-include="static/head"></head>
<?php require 'js_include.php'; ?>

<script src="../js/fruit_player.js"></script>
<!--<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.11.2/moment.min.js"></script>-->
<script src="../plugins/moment/moment.js"></script>
<script src="../plugins/daterangepicker/daterangepicker.js"></script>

<link rel="stylesheet" href="../plugins/daterangepicker/daterangepicker-bs3.css">

<body class="hold-transition skin-blue sidebar-mini">
<div class="wrapper" ng-controller="StatFruitPlayerController">


    <!-- Content Wrapper. Contains page content -->

    <div class="content-wrapper">
        <section class="content">

            <div>

                <form role="form" name="myForm">

                    <div class="box box-info">
                        <div class="box-body">
                            <div class="row">
                                <div class="col-xs-1">
                                    <input type="number" class="form-control" placeholder="ID" ng-model="playerId">
                                </div>
                                <div class="col-xs-1">
                                    <input type="text" class="form-control" placeholder="昵称" ng-model="playerName">
                                </div>
                                <div class="col-xs-2">
                                    <div class="input-group">
                        <span class="input-group-addon">
                          <select style="width: 60px;height: 20px; padding: 0;color: #999;" class="form-control"
                                  ng-model="gold_condition">
                              <option value=">">大于</option>
                              <option value="<">小于</option>
                              <option value="=">等于</option>
                          </select>
                        </span>
                                        <input type="number" class="form-control" placeholder="金额"
                                               ng-model="gold">
                                    </div>

                                </div>


                                <div class="col-xs-3">
                                    <div class="input-group">
                                        <div class="input-group-addon">
                                            <i class="fa fa-clock-o"></i>
                                        </div>
                                        <input type="text" class="form-control pull-right active" id="reservationtime"
                                               ng-model="date_range">
                                    </div>
                                </div>

                                <div class="col-xs-1">
                                    <input type="button" class="btn btn-block btn-info" ng-click="Refresh()"
                                           value="Go!"/>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!--<div class="box-body">-->
                    <!--<div class="row">-->
                    <!--<div class="col-lg-4">-->
                    <!--<label>日期</label>-->
                    <!--<input type="type" class="form-control"-->
                    <!--<button type="submit" class="btn btn-primary" ng-click="Submit(myForm.$valid)">提交</button>-->
                    <!--</div>-->
                    <!--</div>-->
                    <!--</div>-->

                </form>
            </div>

            <div class="box">
                <div class="box-header">
                    <h3 class="box-title">水果机玩家列表</h3>
                </div>
                <!-- /.box-header -->
                <div class="box-body">
                    <div id="example1_wrapper" class="dataTables_wrapper form-inline dt-bootstrap">
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="dataTables_length" id="example1_length"><label>显示
                                        <select name="example1_length"
                                                class="form-control input-sm" ng-model="pageSize" ng-change="Refresh()">
                                            <option value="10">10</option>
                                            <option value="25">25</option>
                                            <option value="50">50</option>
                                            <option value="100">100</option>
                                            <option value="200">200</option>
                                            <option value="500">500</option>
                                            <option value="1000">1000</option>
                                            <option value="10000">10000</option>
                                        </select> 条记录</label></div>
                            </div>

                            <div class="col-sm-6">
                                <div id="example1_filter" class="dataTables_filter" style="float: right;"><label>跳转到:
                                        <input type="number" ng-model="page" ng-keyup="Jump($event);"
                                               class="form-control input-sm"
                                               placeholder="页号"
                                               aria-controls="example1"></label>
                                </div>
                            </div>

                        </div>
                        <div class="row">
                            <div class="col-sm-12">
                                <i class="fa fa-refresh fa-spin" ng-show="loading"></i>
                            </div>
                            <div class="col-sm-12">
                                <table id="example1" class="table table-bordered  dataTable table-hover" role="grid"
                                       aria-describedby="example1_info">
                                    <thead>
                                    <tr role="row">
                                        <th>玩家ID</th>
                                        <th>玩家昵称</th>
                                        <th>持有金币</th>
                                        <th>累计投分</th>
                                        <th>累计得分</th>
                                        <th>累计回收</th>
                                        <th>回收率</th>
                                        <th>累计充值</th>
                                        <th>本局回合</th>
                                        <th>首次玩水果机时间</th>
                                        <th>最近玩水果机时间</th>
                                        <th>注册玩家</th>
                                        <th>IP</th>


                                    </tr>
                                    </thead>
                                    <tbody>

                                    <tr ng-repeat="item in data">
                                        <td>{{item.id}}</td>
                                        <td>{{item.nickname}}</td>
                                        <td>{{item.gold}}</td>
                                        <td>{{item.fruit_total_bet}}</td>
                                        <td>{{item.fruit_total_win}}</td>
                                        <td>{{item.fruit_total_recovery}}</td>
                                        <td>{{item.fruit_total_recovery_rate}}%</td>
                                        <td>{{item.payed}}</td>
                                        <td>{{item.fruit_persist_round}}</td>
                                        <td>{{item.first_time}}</td>
                                        <td>{{item.last_time}}</td>
                                        <td>{{item.reg_date}}</td>
                                        <td>
                                            <i class="icon fa fa-exclamation" style="color: red;"
                                               ng-show="item.ip == '140.207.91.206'"></i>
                                            {{item.ip}}
                                        </td>

                                    </tr>
                                    </tbody>
                                    <tfoot>
                                    <!--<tr>-->
                                    <!--<th rowspan="1" colspan="1">Rendering engine</th>-->
                                    <!--<th rowspan="1" colspan="1">Browser</th>-->
                                    <!--<th rowspan="1" colspan="1">Platform(s)</th>-->
                                    <!--<th rowspan="1" colspan="1">Engine version</th>-->
                                    <!--<th rowspan="1" colspan="1">CSS grade</th>-->
                                    <!--</tr>-->
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-5">
                                <div class="dataTables_info" id="example1_info" role="status" aria-live="polite">显示
                                    {{begin}} 到
                                    {{end}} 条记录
                                    总共条 {{total}} 记录 分为{{pageNum+1}}页
                                </div>
                            </div>
                            <div class="col-sm-7">
                                <div class="dataTables_paginate paging_simple_numbers" id="example1_paginate">
                                    <ul class="pagination">
                                        <li class="paginate_button " ng-repeat="page in pages">
                                            <a href="#" aria-controls="example1" data-dt-idx="2"
                                               ng-click="Refresh(page.index);"
                                               tabindex="0" style="{{page.style}}">{{page.name}}</a></li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- /.box-body -->
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