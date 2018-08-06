@extends('admin.layouts.inspinia.master')

@section('title', $title)

@section('js')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.4.0/Chart.min.js"></script>
    <script>
        new Chart(document.getElementById("myChart"), {
            "type": "line",
            "data": {
                "labels": ["January", "February", "March", "April", "May", "June", "July"],
                "datasets": [{
                    "label": "My First Dataset",
                    "data": [65, 59, 80, 81, 56, 55, 40],
                    "fill": true,
                    "borderColor": "rgb(75, 192, 192)",
                    "backgroundColor": "rgba(75, 192, 192, 0.2)",
                    "pointBackgroundColor":"rgba(75, 192, 192)",
                    "borderWidth":2,
                    "pointRadius":4,
                    "lineTension": 0.1
                }]
            },
            "options": {}
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
                    <small>ĐƠN HÀNG MỚI</small>
                </div>
                <div class="col-xs-4 col-sm-2 text-center" style="border-right: 1px solid #ccc">
                    <h3>20</h3>
                    <small>ĐƠN HÀNG MỚI</small>
                </div>
                <div class="col-xs-4 col-sm-2 text-center">
                    <h3>20</h3>
                    <small>ĐƠN HÀNG MỚI</small>
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
                                    <input type="radio" name="date_filter" value="day" checked> Ngày
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
                    <canvas id="myChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    </div>
@endsection