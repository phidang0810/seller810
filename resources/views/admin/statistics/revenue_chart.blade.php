<!-- PHuoc chich -->
@extends('admin.layouts.inspinia.master')

@section('title', $title)

@section('js')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.4.0/Chart.min.js"></script>
    <!-- Page-Level Scripts -->
    <script>
        var table;
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
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

            /*$.ajax({
                url: "{{route('admin.payments.getTopPlatformSell')}}",
                success: function(res){
                    var platformChart = new Chart(document.getElementById("pieChart"),{
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
                    $("#pie-legend").html(platformChart.generateLegend());
                }
            });*/

            var mixedChart = new Chart(document.getElementById("pieChart"), {
                type: 'bar',
                data: {
                    datasets: [
                        {
                            label: "Set 1",
                            data: [ 40, 60, 45 ],
                            backgroundColor: 'rgba(54, 162, 235, 0.2)',
                            borderColor: 'rgba(54, 162, 235, 1)',
                            borderWidth: 1
                        },
                        {
                            label: "Set 2",
                            data: [ 60, 70, 45 ],
                            backgroundColor: 'rgba(255, 99, 132, 0.2)',
                            borderColor: 'rgba(255,99,132,1)',
                            borderWidth: 1
                        },
                        {
                            label: "Set 3",
                            data: [ 7000, 9000, 2000 ],
                            backgroundColor: 'rgba(255, 99, 132, 0.2)',
                            borderColor: 'rgba(255,99,132,1)',
                            borderWidth: 1,
                            type: 'line',
                            yAxisID: 'y-axis-1'
                        }
                    ],
                    labels: ['January', 'February', 'March', 'April']
                },
                options: {
                    scales: {
                        yAxes: [{
                            ticks: {
                                beginAtZero:true
                            }
                        }, {
                                id: 'y-axis-1',
                                position: 'right',
                                ticks: {
                                    beginAtZero: true,
                                    callback: function(value, index, values) {
                                        return value + '%';
                                    }
                                }
                            }]
                    }
                }
            });
        });
    </script>
@endsection
@section('content')
<div class="row">
    <div class="col-sm-12">
        <div class="ibox float-e-margins pl-15 pr-15">
            <div class="ibox-content">

            </div>
        </div>
    </div>

    <div class="col-sm-12">
        <div class="ibox float-e-margins pl-15 pr-15">
            <div class="ibox-content">
                <h2 class="tt-page">DANH SÁCH ĐƠN HÀNG MỚI</h2>
                <div class="row">
                    <div class="col-md-6">
                        <select name="pie-type" class="form-control">
                            <option>Danh mục</option>
                            <option>Sản phẩm</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <select name="pie-time" class="form-control">
                            <option>Tuần này</option>
                            <option>Tuần trước</option>
                        </select>
                    </div>
                </div>
                <div class="pie-chart" style="padding:20px">
                    <div class="pie" style="margin:auto;margin-bottom: 20px">
                        <canvas id="pieChart"></canvas>
                    </div>
                    <div id="pie-legend" class="chart-legend"></div>
                </div>
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