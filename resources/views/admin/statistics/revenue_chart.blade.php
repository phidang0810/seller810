<!-- PHuoc chich -->
@extends('admin.layouts.inspinia.master')

@section('title', $title)

@section('js')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.4.0/Chart.min.js"></script>
    <!-- Page-Level Scripts -->
    <script>
        var table;
        var lineChart = null;
        var pieChart = null;
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        function getDataLineChart(search)
        {
            $.ajax({
                url: "{{route('admin.payments.getPaymentChart')}}",
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

        function getTopProductChart(search)
        {
            $.ajax({
                url: "{{route('admin.payments.getTopProductSell')}}",
                data:search,
                success: function(res){
                    if(pieChart) {
                        pieChart.destroy();
                    }
                    if (res.result.values.length === 0) {
                        $('#pie-error').text('Dữ liệu rỗng');
                        $('#pie-legend').html('');
                        return;
                    } else {
                        $('#pie-error').text('');
                    }
                    pieChart = new Chart(document.getElementById("pieChart"),{
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
                                        text.push('<span style="float:right">'+number_format(chart.data.datasets[0].data[i])+' VND</span>');
                                    }
                                    text.push('</li>');
                                }
                                text.push('</ul>');

                                return text.join('');
                            },
                            legend: {display: false}
                        }
                    });
                    $("#pie-legend").html(pieChart.generateLegend());
                }
            });

        }

        function getTopCategoryChart(search)
        {
            $.ajax({
                url: "{{route('admin.payments.getTopCategorySell')}}",
                data:search,
                success: function(res){
                    if(pieChart) {
                        pieChart.destroy();
                    }
                    if (res.result.values.length === 0) {
                        $('#pie-error').text('Dữ liệu rỗng');
                        $('#pie-legend').html('');
                        return;
                    } else {
                        $('#pie-error').text('');
                    }
                    pieChart = new Chart(document.getElementById("pieChart"),{
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
                                        text.push('<span style="float:right">'+number_format(chart.data.datasets[0].data[i])+' VND</span>');
                                    }
                                    text.push('</li>');
                                }
                                text.push('</ul>');

                                return text.join('');
                            },
                            legend: {display: false}
                        }
                    });
                    $("#pie-legend").html(pieChart.generateLegend());
                }
            });
        }

        function getTopPlatformChart(search)
        {
            $.ajax({
                url: "{{route('admin.payments.getTopPlatformSell')}}",
                data:search,
                success: function(res){
                    if(pieChart) {
                        pieChart.destroy();
                    }
                    if (res.result.values.length === 0) {
                        $('#pie-error').text('Dữ liệu rỗng');
                        $('#pie-legend').html('');
                        return;
                    } else {
                        $('#pie-error').text('');
                    }
                    pieChart = new Chart(document.getElementById("pieChart"),{
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
                                        text.push('<span style="float:right">'+number_format(chart.data.datasets[0].data[i])+' VND</span>');
                                    }
                                    text.push('</li>');
                                }
                                text.push('</ul>');

                                return text.join('');
                            },
                            legend: {display: false}
                        }
                    });
                    $("#pie-legend").html(pieChart.generateLegend());
                }
            });
        }

        function loadPieChart()
        {
            var search = {
                date: $('select[name="pie_date"]').val(),
                select: 'amount'
            };

            var type = $('select[name="pie_type"]').val();
            if(type === 'category') {
                getTopCategoryChart(search);
            }

            if(type === 'product') {
                getTopProductChart(search);
            }

            if(type === 'platform') {
                getTopPlatformChart(search);
            }
        }
        $(document).ready(function() {
            table = $('#dataTables').dataTable({
                searching: false,
                processing: true,
                serverSide: true,
                "dom": 'rt<"#pagination"flp>',
                ajax: {
                    "url": "{{route('admin.statistics.revenue')}}",
                    "data": function ( d ) {
                        d.keyword = $('#s-keyword').val();
                        d.platform_id = $('#s-platform').val();
                        d.category_id = $('#s-category').val();
                        d.date_from = $('#date_from').val();
                        d.date_to = $('#date_to').val();
                    },
                    complete: function(){
                    }
                },
                columns: [
                    {data: 'name'},
                    {data: 'code'},
                    {data: 'category'},
                    {data: 'total_cart'},
                    {data: 'total_price'},
                    {data: 'profit'}
                ],
                "aoColumnDefs": [
                    // Column index begins at 0
                    { "sClass": "text-right", "aTargets": [ 3,4,5 ] }
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

            $('#fSearch').submit(function(){
                table.fnDraw();
                return false;
            });

            $('#bt-reset').click(function(){
                $('#fSearch')[0].reset();
                table.fnDraw();
            });
            $('#date_range_picker').datepicker({
                keyboardNavigation: false,
                forceParse: false,
                autoclose: true,
                format: 'dd/mm/yyyy'
            });

            getDataLineChart({
                date_filter: $('input[name="date_filter"]:checked').val()
            });
            $('input[name="date_filter"]').change(function(){
                var search = {date_filter: $(this).val()};
                getDataLineChart(search);
            });

            $('select[name="pie_type"]').change(function(){
                loadPieChart();
            });
            $('select[name="pie_date"]').change(function(){
                loadPieChart();
            });
            loadPieChart();
        });
    </script>
@endsection
@section('content')
<div class="row">
    <div class="col-sm-8">
        <div class="ibox float-e-margins pl-15 pr-15">
            <div class="ibox-content">
                <h2 class="tt-page">DANH SÁCH ĐƠN HÀNG MỚI</h2>
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
                <div class="" style="padding:20px">
                    <canvas id="lineChart"></canvas>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-4">
        <div class="ibox-content">
            <h2 class="tt-page">THÔNG KÊ DOANH THU THEO TOP</h2>
            <div class="row">
                <div class="col-md-6">
                    <select name="pie_type" class="form-control">
                        <option value="category" @if(app('request')->input('pie_type') == "category") selected @endif>Danh mục</option>
                        <option value="product" @if(app('request')->input('pie_type') == "product") selected @endif>Sản phẩm</option>
                        <option value="platform" @if(app('request')->input('pie_type') == "platform") selected @endif>Nguồn đơn</option>
                    </select>
                </div>
                <div class="col-md-6">
                    <select name="pie_date" class="form-control">
                        <option value="last_weed" @if(app('request')->input('pie_date') == "last_weed") selected @endif>Tuần trước</option>
                        <option value="this_week" @if(app('request')->input('pie_date') == "this_week") selected @endif>Tuần này</option>
                        <option value="last_month" @if(app('request')->input('pie_date') == "last_month") selected @endif>Tháng trước</option>
                        <option value="this_month" @if(app('request')->input('pie_date') == "this_month") selected @endif>Tháng này</option>
                        <option value="last_year" @if(app('request')->input('pie_date') == "last_year") selected @endif>Năm trước</option>
                        <option value="this_year" @if(app('request')->input('pie_date') == "this_year") selected @endif>Năm nay</option>
                    </select>
                </div>
            </div>
            <div class="pie-chart" style="padding:20px">
                <div class="pie" style="margin:auto;margin-bottom: 20px">
                    <div id="pie-error" class="text-center"></div>
                    <canvas id="pieChart"></canvas>
                </div>
                <div id="pie-legend" class="chart-legend"></div>
            </div>
        </div>
    </div>
</div>

<div class="row">
        <div class="ibox float-e-margins pl-15 pr-15">
            <div class="ibox-content">
                <form role="form" id="fSearch">
                    <div class="row v-center">
                        <div class="col-sm-3">
                            <div class="form-group">
                                <label>Tên hoặc mã sản phẩm</label>
                                <input type="text" placeholder="Nhập tên hoặc mã sản phẩm" name="keyword" id="s-keyword" class="form-control" value="{{app('request')->input('keyword')}}">
                            </div>
                        </div>

                        <div class="col-sm-3">
                            <div class="form-group">
                                <label>Chọn danh mục</label>
                                <select class="form-control" name="category" id="s-category">
                                    <option value=""> -- Tất cả -- </option>
                                    {!! $categoriesTree !!}
                                </select>
                            </div>
                        </div>

                        <div class="col-sm-3">
                            <div class="form-group">
                                <label>Ngày nhập</label>
                                <div class="input-daterange input-group" id="date_range_picker">
                                    <input type="text" class="input-sm form-control" id="date_from" name="start" value="">
                                    <span class="input-group-addon lbl-to">to</span>
                                    <input type="text" class="input-sm form-control" id="date_to" name="end" value="">
                                </div>
                            </div>
                        </div>

                        <div class="col-sm-3">
                            <div class="form-group">
                                <label></label>
                                <button class="btn btn-sm btn-warning" type="submit" style="margin-bottom: 0;margin-top: 22px;">
                                    <i class="fa fa-search"></i> Tìm kiếm
                                </button>
                                <button class="btn btn-sm btn-default" type="button" id="bt-reset" style="margin-bottom: 0;margin-top: 22px; margin-right:5px">
                                    <i class="fa fa-refresh"></i> Clear
                                </button>
                            </div>

                        </div>
                    </div>
                </form>
                <!-- Account list -->
                <table class="table table-striped table-hover" id="dataTables">
                    <thead>
                    <tr>
                        <th>Tên</th>
                        <th>Mã Sản Phẩm</th>
                        <th>Danh Mục</th>
                        <th>Số Lượng Đơn</th>
                        <th>Doanh Thu</th>
                        <th>Lợi Nhuận</th>
                    </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>

            </div>
        </div>
</div>
@endsection