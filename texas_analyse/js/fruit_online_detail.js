/**
 * Created by huangxf on 16/5/17.
 */


app.controller('FruitOnlineDetail', function ($scope, $http) {


    $scope.today = new Date().Format("yyyy-MM-dd");
    $scope.refresh = function () {

        $http.post('ajax.php', {
            c: 'fruit',
            a: 'online',
            today: $scope.today,
            url: window.location.search
        }).success(function (result) {

            if (result.status == "succ") {
                $scope.show(result.logs);
            } else {
                alert("失败了,去找程序猿吧~");
            }

        });

    };

    $scope.refresh();

    $scope.show = function (logs) {
        var labels = [];
        var today = [];
        var yesterday = [];
        var lastWeek = [];


        for (var i = 0; i < logs.length; i++) {
            labels.push(logs[i].label);
            today.push(logs[i].today);
            yesterday.push(logs[i].yesterday);
            lastWeek.push(logs[i].lastWeek);
        }

        $("#dailyChartDiv").empty();
        $("#dailyChartDiv").append($('<canvas id="dailyChart" style="height:400px"></canvas>').get(0));


        var lineChartCanvas = $("#dailyChart").get(0).getContext("2d");


        var ctx = lineChartCanvas;//document.getElementById("dailyChart").getContext("2d");
        var data = {
            labels: labels,
            datasets: [
                {
                    label: "今日",
                    fillColor: "rgba(60,141,188,0.0)",
                    strokeColor: "rgba(60,141,188,0.8)",
                    pointColor: "#3b8bba",
                    pointStrokeColor: "rgba(60,141,188,1)",
                    pointHighlightFill: "#fff",
                    pointHighlightStroke: "rgba(60,141,188,1)",
                    data: today
                },
                {
                    label: "昨日",
                    fillColor: "rgba(210, 214, 222, 0.0)",
                    strokeColor: "rgba(210, 214, 222, 1)",
                    pointColor: "rgba(210, 214, 222, 1)",
                    pointStrokeColor: "#c1c7d1",
                    pointHighlightFill: "#fff",
                    pointHighlightStroke: "rgba(220,220,220,1)",
                    data: yesterday
                },
                {
                    label: "上周",
                    fillColor: "rgba(160,0,0,0.0)",
                    strokeColor: "rgba(160,0,0,0.8)",
                    pointColor: "#A00000",
                    pointStrokeColor: "rgba(160,0,0,1)",
                    pointHighlightFill: "#fff",
                    pointHighlightStroke: "rgba(160,0,0,1)",
                    data: lastWeek
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
            //  legendTemplate:"",
            //Boolean - whether to maintain the starting aspect ratio or not when responsive, if set to false, will take up entire container
            maintainAspectRatio: true,
            //Boolean - whether to make the chart responsive to window resizing
            responsive: true,
            labelsFilter: function (value, index, labels) {

                //return value.charAt(value.length - 1) != " ";
                return (index ) % 60 !== 0;
            },
            animation: false
        };

        var myLineChart = new Chart(ctx).Line(data, areaChartOptions);

    };

});

