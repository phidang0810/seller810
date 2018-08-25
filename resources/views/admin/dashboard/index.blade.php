@extends('admin.layouts.inspinia.master')

@section('title', $title)

@section('js')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.4.0/Chart.min.js"></script>
    <script>
        var lineChart = null;
        function number_format(number, decimals, dec_point, thousands_sep) {
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
                url: "{{route('admin.statistics.getPaymentChart')}}",
                data:search,
                success: function(res){
                    if(lineChart) {
                        lineChart.destroy();
                    }
                        lineChart = new Chart(document.getElementById("lineChart"), {
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
                url: "{{route('admin.statistics.getTopProductSell')}}",
                data:{select:'count'},
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
                url: "{{route('admin.statistics.getTopPlatformSell')}}",
                data:{select:'count'},
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
                url: "{{route('admin.statistics.getTopCategorySell')}}",
                data:{select:'count'},
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

            table = $('#dataTables').dataTable({
                responsive: true,
                searching: false,
                processing: true,
                serverSide: true,
                "dom": 'rt<"#pagination"flp>',
                ajax: {
                    "url": "{{route('admin.carts.index')}}",
                    "data": function ( d ) {
                        d.status = $('#s-status').val();
                    },
                    complete: function(){
                    }
                },
                columns: [
                    {data: 'id'},
                    {data: 'code'},
                    {data: 'created_at'},
                    {data: 'platform_name'}
                ],
                "aoColumnDefs": [
                ],
                "language": {
                    "decimal": "",
                    "emptyTable": "Không có dữ liệu hợp lệ",
                    "info": "Hiển thị từ _START_ đến _END_ / _TOTAL_ kết quả",
                    "infoEmpty": "Hiển thị từ 0 đến 0 trên 0 dòng",
                    "infoFiltered": "(filtered from _MAX_ total entries)",
                    "infoPostFix": "",
                    "thousands": ",",
                    "lengthMenu": "Hiển thị _MENU_ kết quả",
                    "loadingRecords": "Đang tải...",
                    "processing": "Đang xử lý...",
                    "search": "Search:",
                    "zeroRecords": "Không có kết quả nào được tìm thấy",
                    "paginate": {
                        "first": "Đầu",
                        "last": "Cuối",
                        "next": "Tiếp",
                        "previous": "Trước"
                    },
                    "aria": {
                        "sortAscending": ": activate to sort column ascending",
                        "sortDescending": ": activate to sort column descending"
                    }
                }

            });
        });
    </script>
@endsection

@section('content')
    <div class="ibox float-e-margins">
        <div class="ibox-content">
            <div class="row tt-number">
                <div class="col-xs-4 col-sm-2 text-center" style="border-right: 1px solid #ccc">
                    <h3>
                        @if(key_exists('product_manager', Auth::user()->permissions))
                            <a href="{{route('admin.carts.index')}}?status={{CART_NEW}}">{{$cart_new}}</a>
                        @else
                            {{$cart_new}}
                        @endif
                    </h3>
                    <small>ĐƠN HÀNG MỚI</small>
                </div>
                <div class="col-xs-4 col-sm-2 text-center" style="border-right: 1px solid #ccc">
                   <h3>
                       @if(key_exists('product_manager', Auth::user()->permissions))
                            <a href="{{route('admin.carts.index')}}?status={{EXCUTING}}">{{$cart_processing}}</a>
                        @else
                            {{$cart_processing}}
                        @endif
                    </h3>
                    <small>ĐƠN CHỜ XÁC NHẬN</small>
                </div>
                <div class="col-xs-4 col-sm-2 text-center" style="border-right: 1px solid #ccc">
                    <h3>
                        @if(key_exists('product_manager', Auth::user()->permissions))
                            <a href="{{route('admin.carts.index')}}?status={{CART_TRANSPORTING}}">{{$cart_transporting}}</a>
                        @else
                            {{$cart_transporting}}
                        @endif
                    </h3>
                    <small>ĐƠN HÀNG ĐANG GIAO</small>
                </div>
                <div class="col-xs-4 col-sm-2 text-center" style="border-right: 1px solid #ccc">
                    <h3>
                        @if(key_exists('product_manager', Auth::user()->permissions))
                            <a href="{{route('admin.product_available.index')}}?private_search=need_import">{{$product_need_import}}</a>
                        @else
                            {{$product_need_import}}
                        @endif
                    </h3>
                    <small>SẢN PHẨM SẮP HẾT</small>
                </div>
                <div class="col-xs-4 col-sm-2 text-center" style="border-right: 1px solid #ccc">
                    <h3>
                        @if(key_exists('product_manager', Auth::user()->permissions))
                            <a href="{{route('admin.product_available.index')}}?private_search=out_of_stock">{{$product_out_of_stock}}</a>
                        @else
                            {{$product_out_of_stock}}
                        @endif
                    </h3>
                    <small>SẢN PHẨM HẾT HÀNG</small>
                </div>
                <div class="col-xs-4 col-sm-2 text-center">
                    <h3>
                        @if(key_exists('user_manager', Auth::user()->permissions))
                            <a href="{{route('admin.users.index')}}">{{$user}}</a>
                        @else
                            {{$user}}
                        @endif
                    </h3>
                    <small>NHÂN VIÊN</small>
                </div>
            </div>

        </div>
    </div>

    <div class="row">
        <div class="col-md-5">
            <div class="ibox float-e-margins mb">
                <div class="ibox-content">
                    <h2 class="tt-page">DANH SÁCH ĐƠN HÀNG MỚI</h2>
                    <table class="table table-striped table-hover" id="dataTables">
                        <thead>
                        <tr>
                            <th>ID</th>
                            <th>Mã Đơn Hàng</th>
                            <th>Ngày Đặt</th>
                            <th>Nguồn Đơn</th>
                        </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
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
                    <h2 class="tt-page">SẢN PHẨM BÁN CHẠY <a href="@if(key_exists('report_manager', Auth::user()->permissions)) {{route('admin.statistics.cartChart')}}?pie_type=product&pie_date=this_week @endif" class="pull-right">Xem tất cả</a></h2>
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
                    <h2 class="tt-page">TOP NGUỒN ĐƠN HÀNG <a href="@if(key_exists('report_manager', Auth::user()->permissions)) {{route('admin.statistics.cartChart')}}?pie_type=platform&pie_date=this_week @endif" class="pull-right">Xem tất cả</a></h2>
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
                    <h2 class="tt-page">TOP DANH MỤC BÁN CHẠY <a href="@if(key_exists('report_manager', Auth::user()->permissions)) {{route('admin.statistics.cartChart')}}?pie_type=category&pie_date=this_week @endif" class="pull-right">Xem tất cả</a></h2>
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