@extends('admin.layouts.inspinia.master')

@section('title', $title)

@section('js')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.4.0/Chart.min.js"></script>
    <script>
        function number_format(number, decimals, dec_point, thousands_sep) {
// *     example: number_format(1234.56, 2, ',', ' ');
// *     return: '1 234,56'
            number = (number + '').replace(',', '').replace(' ', '');
            var n = !isFinite(+number) ? 0 : +number,
                prec = !isFinite(+decimals) ? 0 : Math.abs(decimals),
                sep = (typeof thousands_sep === 'undefined') ? ',' : thousands_sep,
                dec = (typeof dec_point === 'undefined') ? '.' : dec_point,
                s = '',
                toFixedFix = function (n, prec) {
                    var k = Math.pow(10, prec);
                    return '' + Math.round(n * k) / k;
                };
            // Fix for IE parseFloat(0.55).toFixed(0) = 0;
            s = (prec ? toFixedFix(n, prec) : '' + Math.round(n)).split('.');
            if (s[0].length > 3) {
                s[0] = s[0].replace(/\B(?=(?:\d{3})+(?!\d))/g, sep);
            }
            if ((s[1] || '').length < prec) {
                s[1] = s[1] || '';
                s[1] += new Array(prec - s[1].length + 1).join('0');
            }
            return s.join(dec);
        }

        function getDataLineChart(search)
        {
            $.ajax({
                url: "{{route('admin.payments.getPaymentChart')}}",
                data:search,
                success: function(res){
                    new Chart(document.getElementById("lineChart"), {
                        "type": "line",
                        "data": {
                            "labels": res.result.time,
                            "datasets": [{
                                "label": "Doanh thu",
                                "data": res.result.value,
                                "fill": true,
                                "borderColor": "rgb(75, 192, 192)",
                                "backgroundColor": "rgba(75, 192, 192, 0.2)",
                                "pointBackgroundColor":"rgba(75, 192, 192)",
                                "borderWidth":2,
                                "pointRadius":4,
                                "lineTension": 0.1
                            }]
                        },
                        "options": {
                            scales: {
                                yAxes: [{
                                    ticks: {
                                        beginAtZero:true,
                                        callback: function(value, index, values) {
                                            return number_format(value) + ' VND';
                                        }
                                    }
                                }]
                            },
                            tooltips: {
                                callbacks: {
                                    label: function(tooltipItem, chart){
                                        var datasetLabel = chart.datasets[tooltipItem.datasetIndex].label || '';
                                        return datasetLabel + ': ' + number_format(tooltipItem.yLabel, 2) + ' VND';
                                    }
                                }
                            }
                        }
                    });
                }
            });
        }

        $(document).ready(function(){
            getDataLineChart({
                date_filter: $('input[name="date_filter"]:checked').val()
            });
            $('input[name="date_filter"]').change(function(){
               var search = {date_filter: $(this).val()};
                getDataLineChart(search);
            });


            $.ajax({
                url: "{{route('admin.payments.getTopProductSell')}}",
                success: function(res){
                    var productChart = new Chart(document.getElementById("pieChartProduct"),{
                        type: 'doughnut',
                        data: {
                            "labels": res.result.labels,
                            "datasets": [{
                                "data": res.result.values,
                                "backgroundColor": ['rgb(0, 131, 202)', 'rgb(245, 100, 1)', 'rgb(253, 201, 35)', 'rgb(141, 198, 62)']
                            }]
                        },
                        options: {
                            legendCallback: function (chart) {
                                var text = [];
                                text.push('<ul class="' + chart.id + '-legend" style="list-style:none">');
                                for (var i = 0; i < chart.data.datasets[0].data.length; i++) {
                                    text.push('<li><span class="legend-item" style="background:' + chart.data.datasets[0].backgroundColor[i] + '" />&nbsp;');
                                    if (chart.data.labels[i]) {
                                        text.push(chart.data.labels[i]);
                                        text.push('<span style="float:right">'+chart.data.datasets[0].data[i]+' sản phẩm</span>');
                                    }
                                    text.push('</li>');
                                }
                                text.push('</ul>');

                                return text.join('');
                            },
                            legend: {display: false}
                        }
                    });
                    $("#product-legend").html(productChart.generateLegend());
                }
            });

            $.ajax({
                url: "{{route('admin.payments.getTopPlatformSell')}}",
                success: function(res){
                    var platformChart = new Chart(document.getElementById("pieChartPlatform"),{
                        type: 'doughnut',
                        data: {
                            "labels": res.result.labels,
                            "datasets": [{
                                "data": res.result.values,
                                "backgroundColor": ['rgb(0, 131, 202)', 'rgb(245, 100, 1)', 'rgb(253, 201, 35)', 'rgb(141, 198, 62)']
                            }]
                        },
                        options: {
                            legendCallback: function (chart) {
                                var text = [];
                                text.push('<ul class="' + chart.id + '-legend" style="list-style:none">');
                                for (var i = 0; i < chart.data.datasets[0].data.length; i++) {
                                    text.push('<li><span class="legend-item" style="background:' + chart.data.datasets[0].backgroundColor[i] + '" />&nbsp;');
                                    if (chart.data.labels[i]) {
                                        text.push(chart.data.labels[i]);
                                        text.push('<span style="float:right">'+chart.data.datasets[0].data[i]+' đơn hàng</span>');
                                    }
                                    text.push('</li>');
                                }
                                text.push('</ul>');

                                return text.join('');
                            },
                            legend: {display: false}
                        }
                    });
                    $("#platform-legend").html(platformChart.generateLegend());
                }
            });

            $.ajax({
                url: "{{route('admin.payments.getTopCategorySell')}}",
                success: function(res){
                    var categoryChart = new Chart(document.getElementById("pieChartCategory"),{
                        type: 'doughnut',
                        data: {
                            "labels": res.result.labels,
                            "datasets": [{
                                "data": res.result.values,
                                "backgroundColor": ['rgb(0, 131, 202)', 'rgb(245, 100, 1)', 'rgb(253, 201, 35)', 'rgb(141, 198, 62)']
                            }]
                        },
                        options: {
                            legendCallback: function (chart) {
                                var text = [];
                                text.push('<ul class="' + chart.id + '-legend" style="list-style:none">');
                                for (var i = 0; i < chart.data.datasets[0].data.length; i++) {
                                    text.push('<li><span class="legend-item" style="background:' + chart.data.datasets[0].backgroundColor[i] + '" />&nbsp;');
                                    if (chart.data.labels[i]) {
                                        text.push(chart.data.labels[i]);
                                        text.push('<span style="float:right">'+chart.data.datasets[0].data[i]+' đơn hàng</span>');
                                    }
                                    text.push('</li>');
                                }
                                text.push('</ul>');

                                return text.join('');
                            },
                            legend: {display: false}
                        }
                    });
                    $("#category-legend").html(categoryChart.generateLegend());
                }
            });

        });
    </script>
@endsection

@section('content')
    <div class="ibox float-e-margins">
        <div class="ibox-content">
            <div class="row">
                <div class="col-xs-4 col-sm-2 text-center" style="border-right: 1px solid #ccc">
                    <h3>20</h3>
                    <small>ĐƠN HÀNG MỚI</small>
                </div>
                <div class="col-xs-4 col-sm-2 text-center" style="border-right: 1px solid #ccc">
                    <h3>20</h3>
                    <small>ĐƠN HÀNG MỚI</small>
                </div>
                <div class="col-xs-4 col-sm-2 text-center" style="border-right: 1px solid #ccc">
                    <h3>20</h3>
                    <small>ĐƠN HÀNG MỚI</small>
                </div>
                <div class="col-xs-4 col-sm-2 text-center" style="border-right: 1px solid #ccc">
                    <h3>20</h3>
                    <small>SẢN PHẨM SẮP HẾT</small>
                </div>
                <div class="col-xs-4 col-sm-2 text-center" style="border-right: 1px solid #ccc">
                    <h3>20</h3>
                    <small>ĐƠN HÀNG ĐANG CHỜ XÁC NHẬN</small>
                </div>
                <div class="col-xs-4 col-sm-2 text-center">
                    <h3>20</h3>
                    <small>NHÂN VIÊN</small>
                </div>
            </div>

        </div>
    </div>

    <div class="row">
        <div class="col-md-5">
            <div class="ibox float-e-margins">
                <div class="ibox-content">
                    <h2 class="tt-page">DANH SÁCH ĐƠN HÀNG MỚI</h2>
                </div>
            </div>
        </div>
        <div class="col-md-7">
            <div class="ibox float-e-margins">
                <div class="ibox-content">
                    <h2 class="tt-page">THỐNG KÊ DOANH THU</h2>
                    <div class="row">
                        <div class="col-md-7">
                        </div>
                        <div class="col-md-5 text-right">
                            <div class="btn-group" data-toggle="buttons">
                                <label class="btn btn-default btn-sm">
                                    <input type="radio" name="date_filter" value="last_week"> Tuần trước
                                </label>
                                <label class="btn btn-default btn-sm active">
                                    <input type="radio" name="date_filter" value="this_week"> Tuần này
                                </label>
                                <label class="btn btn-default btn-sm">
                                    <input type="radio" name="date_filter" value="month"> Tháng
                                </label>
                                <label class="btn btn-default btn-sm">
                                    <input type="radio" name="date_filter" value="year"> Năm
                                </label>
                            </div>
                        </div>
                    </div>
                    <canvas id="lineChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-4">
            <div class="ibox float-e-margins">
                <div class="ibox-content">
                    <h2 class="tt-page">SẢN PHẨM BÁN CHẠY <a href="#" class="pull-right">Xem tất cả</a></h2>
                    <div class="pie-chart" style="padding:20px">
                        <div class="pie" style="max-width:200px;margin:auto;margin-bottom: 20px">
                            <canvas id="pieChartProduct"></canvas>
                        </div>
                        <div id="product-legend" class="chart-legend"></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="ibox float-e-margins">
                <div class="ibox-content">
                    <h2 class="tt-page">TOP NGUỒN ĐƠN HÀNG <a href="#" class="pull-right">Xem tất cả</a></h2>
                    <div class="pie-chart" style="padding:20px">
                        <div class="pie" style="max-width:200px;margin:auto;margin-bottom: 20px">
                            <canvas id="pieChartPlatform"></canvas>
                        </div>
                        <div id="platform-legend" class="chart-legend"></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="ibox float-e-margins">
                <div class="ibox-content">
                    <h2 class="tt-page">TOP DANH MỤC BÁN CHẠY <a href="#" class="pull-right">Xem tất cả</a></h2>
                    <div class="pie-chart" style="padding:20px">
                        <div class="pie" style="max-width:200px;margin:auto;margin-bottom: 20px">
                            <canvas id="pieChartCategory"></canvas>
                        </div>
                        <div id="category-legend" class="chart-legend"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection