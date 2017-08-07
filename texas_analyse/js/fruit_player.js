/**
 * Created by huangxiufeng on 16/9/1.
 */

app.controller('StatFruitPlayerController', function ($scope, $http) {


    $scope.bag = [];
    $scope.index = -1;
    $scope.send = {
        content: ""
    };
    $scope.keyWords = "";
    $scope.date = new Date();
    $scope.start_date = new Date();
    $scope.start_date.setMonth($scope.start_date.getMonth() - 2);

    $scope.date_range = $scope.start_date.Format("yyyy-MM-dd 00:00") + " - " + $scope.date.Format("yyyy-MM-dd 24:00");

    $scope.list = [];
    $scope.page = 1;
    $scope.begin = 1;
    $scope.end = 10;
    $scope.total = 100;
    $scope.table = "";
    $scope.pageSize = "100";
    $scope.pageNum = 0;
    $scope.pages = [];
    $scope.loading = false;
    $scope.orderAsc = false;
    $scope.orderBy = "fruit_start_time";

    $('#reservationtime').daterangepicker({timePicker: true, timePickerIncrement: 30, format: 'YYYY-MM-DD H:mm '});


    $scope.Refresh = function (page) {

        $scope.loading = true;

        if (page != undefined) {
            $scope.page = page;
        }


        var where = [];

        if ($scope.playerId != undefined && $scope.playerId != 0) {
            where.push({field: "id", value: $scope.playerId, condition: "="});
        }


        if ($scope.playerName != undefined && $scope.playerName != "") {
            where.push({field: "playerName", value: $scope.playerName, condition: "like"});
        }

        var date_ranges = $scope.date_range.split(" - ");

        // where.push({field: "logdate", value: date_ranges[0], condition: ">="});
        // where.push({field: "logdate", value: date_ranges[1], condition: "<="});

        where.push({field: "fruit_first_time", value: 0, condition: ">"});
        where.push({field: "fruit_persist_round", value: 0, condition: "!="});



        $http.post('ajax.php', {
            c: 'fruit',
            a: 'analysePlayer',
            date: $scope.date,
            page: $scope.page,
            pagesize: $scope.pageSize,
            orderAsc: $scope.orderAsc,
            orderBy: $scope.orderBy,
            where: where,
            fields:'id,ip,fruit_persist_round,reg_date,payed,nickname,gold,fruit_total_bet,fruit_total_win,fruit_total_bet-fruit_total_win as fruit_total_recovery,round( coalesce( (( fruit_total_bet-fruit_total_win)/ fruit_total_bet),0 )*100,2) as fruit_total_recovery_rate ,FROM_UNIXTIME( fruit_first_time ,"%Y-%m-%d  %H:%i:%s") as first_time, FROM_UNIXTIME( fruit_start_time,"%Y-%m-%d %H:%i:%s") as last_time ',
            url:window.location.search

        }).success(function (data) {

            $scope.loading = false;

            if (data.status = 'succ') {
                $scope.data = data.list;
                $scope.table = data.table;
                $scope.date = data.date;
                $scope.page = data.page;
                $scope.pageNum = data.pages;
                $scope.begin = data.begin;
                $scope.end = data.end;
                $scope.total = data.total;
                $scope.pages = [];
                $scope.pages.push({name: 'Previous', index: data.page - 1});
                for (var i = 1; i <= data.pages + 1 && i <= 15; i++) {
                    if (i == $scope.page) {
                        $scope.pages.push({name: i, index: i, style: 'color:red;'});
                    } else {
                        $scope.pages.push({name: i, index: i, style: ""});
                    }
                }
                $scope.pages.push({name: 'Next', index: data.page + 1});
                $scope.pages.push({name: 'End', index: data.pageNum + 1});
            }

        });

    };

    $scope.Search = function () {
    };

    $scope.Page = function () {
    };

    $scope.Refresh();

    $scope.Jump = function ($event) {
        if (event.keyCode != 13) {
            return;
        }
        $scope.Refresh();
    };

});