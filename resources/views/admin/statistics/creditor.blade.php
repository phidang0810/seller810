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
            search.select = 'number_cart';
            $.ajax({
                url: "{{route('admin.statistics.getCreditorBarChart')}}",
                data:search,
                success: function(res){
                    if(lineChart) {
                        lineChart.destroy();
                    }
                    lineChart = new Chart(document.getElementById("lineChart"), {
                        "type": "bar",
                        "data": {
                            "labels": res.result.time,
                            "datasets": [
                                {
                                "label": "Tiền Nợ",
                                "data": res.result.total,
                                "fill": true,
                                "borderColor": "rgb(75, 192, 192)",
                                "backgroundColor": "rgba(75, 192, 192, 0.2)",
                                "pointBackgroundColor":"rgba(75, 192, 192)",
                                "borderWidth":2,
                                "pointRadius":4,
                                "lineTension": 0.1
                            }
                                ]
                        },
                        "options": {
                            scales: {
                                yAxes: [{
                                    ticks: {
                                        beginAtZero:true,
                                        callback: function(value, index, values) {
                                            return number_format(value);
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

        $(document).ready(function() {

            getDataLineChart({
                date_filter: $('input[name="date_filter"]:checked').val()
            });
            $('input[name="date_filter"]').change(function(){
                var search = {date_filter: $(this).val()};
                getDataLineChart(search);
            });
        });
    </script>
@endsection
@section('content')
<div class="row">
    <div class="col-sm-12">
        <div class="ibox float-e-margins pl-15 pr-15">
            <div class="ibox-content">
                <h2 class="tt-page">THỐNG KÊ NỢ</h2>
                <div style="padding-left: 10px;">Tổng Nợ: <span style="font-weight:bold;font-size: 14px">{{format_price($total->total)}}</span> - Còn: <span style="color: red;font-size: 14px">{{format_price($total_not_paid->total)}}</span></div>
                <div class="row">
                    <div class="col-md-7">
                    </div>
                    <div class="col-md-5 text-right">
                        <div class="btn-group" data-toggle="buttons">
                            <label class="btn btn-default btn-sm">
                                <input type="radio" name="date_filter" value="month"> Tháng
                            </label>
                            <label class="btn btn-default btn-sm active">
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
</div>

@endsection