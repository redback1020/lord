/**
 * Created by huangxiufeng on 16/9/1.
 */


app.controller('StatFruitBasicController', function ($scope, $http) {


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
    $scope.orderBy = "logday";

    $('#reservationtime').daterangepicker({timePicker: true, timePickerIncrement: 30, format: 'YYYY-MM-DD H:mm '});


    $scope.Refresh = function (page) {

        $scope.loading = true;

        if (page != undefined) {
            $scope.page = page;
        }


        var where = [];

        if ($scope.playerId != undefined && $scope.playerId != 0) {
            where.push({field: "playerId", value: $scope.playerId, condition: "="});
        }


        if ($scope.playerName != undefined && $scope.playerName != "") {
            where.push({field: "playerName", value: $scope.playerName, condition: "like"});
        }

        var date_ranges = $scope.date_range.split(" - ");

        where.push({field: "logdate", value: date_ranges[0], condition: ">="});
        where.push({field: "logdate", value: date_ranges[1], condition: "<="});

        $http.post('ajax.php', {
            c: 'fruit',
            a: 'analyseBasic',
            date: $scope.date,
            page: $scope.page,
            pagesize: $scope.pageSize,
            orderAsc: $scope.orderAsc,
            orderBy: $scope.orderBy,
            where: where,
            fields: '*,   round( coalesce( (bet-win) /bet ,0),2) * 100 as rate',
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


    $scope.show = function (logs) {

        var labels = [];
        var daus = [];
        var drus = [];

        // for (var i = 0; i < logs.length; i++) {
        //     labels.push(logs[i].logdate);
        //     daus.push(logs[i].dau);
        //     drus.push(logs[i].dru);
        // }


        //--------------
        //- AREA CHART -
        //--------------

        // Get context with jQuery - using jQuery's .get() method.
        var areaChartCanvas = $("#areaChart").get(0).getContext("2d");
        // This will get the first returned node in the jQuery collection.
        var areaChart = new Chart(areaChartCanvas);

        var areaChartData = {
            labels: labels.reverse(),
            datasets: [
                {
                    label: "Electronics",
                    fillColor: "rgba(210, 214, 222, 1)",
                    strokeColor: "rgba(210, 214, 222, 1)",
                    pointColor: "rgba(210, 214, 222, 1)",
                    pointStrokeColor: "#c1c7d1",
                    pointHighlightFill: "#fff",
                    pointHighlightStroke: "rgba(220,220,220,1)",
                    data: daus.reverse()
                },
                {
                    label: "Digital Goods",
                    fillColor: "rgba(60,141,188,0.9)",
                    strokeColor: "rgba(60,141,188,0.8)",
                    pointColor: "#3b8bba",
                    pointStrokeColor: "rgba(60,141,188,1)",
                    pointHighlightFill: "#fff",
                    pointHighlightStroke: "rgba(60,141,188,1)",
                    data: drus.reverse()
                }
            ]
        };

        var areaChartOptions = {
            //Boolean - If we should show the scale at all
            showScale: true,
            //Boolean - Whether grid lines are shown across the chart
            scaleShowGridLines: true,
            //String - Colour of the grid lines
            scaleGridLineColor: "rgba(20,20,20,.05)",
            //Number - Width of the grid lines
            scaleGridLineWidth: 1,
            //Boolean - Whether to show horizontal lines (except X axis)
            scaleShowHorizontalLines: true,
            //Boolean - Whether to show vertical lines (except Y axis)
            scaleShowVerticalLines: true,
            //Boolean - Whether the line is curved between points
            bezierCurve: true,
            //Number - Tension of the bezier curve between points
            bezierCurveTension: 0.3,
            //Boolean - Whether to show a dot for each point
            pointDot: false,
            //Number - Radius of each point dot in pixels
            pointDotRadius: 4,
            //Number - Pixel width of point dot stroke
            pointDotStrokeWidth: 1,
            //Number - amount extra to add to the radius to cater for hit detection outside the drawn point
            pointHitDetectionRadius: 20,
            //Boolean - Whether to show a stroke for datasets
            datasetStroke: true,
            //Number - Pixel width of dataset stroke
            datasetStrokeWidth: 2,
            //Boolean - Whether to fill the dataset with a color
            datasetFill: true,
            //String - A legend template
            legendTemplate: "<ul class=\"<%=name.toLowerCase()%>-legend\"><% for (var i=0; i<datasets.length; i++){%><li><span style=\"background-color:<%=datasets[i].lineColor%>\"></span><%if(datasets[i].label){%><%=datasets[i].label%><%}%></li><%}%></ul>",
            //Boolean - whether to maintain the starting aspect ratio or not when responsive, if set to false, will take up entire container
            maintainAspectRatio: true,
            //Boolean - whether to make the chart responsive to window resizing
            responsive: true
        };

        //Create the line chart
        areaChart.Line(areaChartData, areaChartOptions);
    };

});