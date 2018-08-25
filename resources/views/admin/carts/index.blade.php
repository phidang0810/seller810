@extends('admin.layouts.inspinia.master')

@section('title', $title)

@section('js')
<!-- Page-Level Scripts -->
<script>
    var url_delete = "{{route('admin.carts.delete')}}";
    var url_change_status = "{{route('admin.carts.changeStatus')}}";
    var table;
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    //---> Get customer detail, cart detail
    var cart_complete = "{{CART_COMPLETED}}";
    var cart_canceled = "{{CART_CANCELED}}";
    function getRecordDetail(){
        $('#dataTables tbody > tr').click(function(){
            var cart_code = $(this).find('td:not(:empty):first').text();
            $.ajax({
                url: "{{route('admin.carts.getCartDetail')}}",
                data:{
                    cart_code:cart_code
                },
                dataType:'json'
            }).done(function(data) {
                // console.log(data);
                if (!$.isEmptyObject(data.result["cart"])) {
                    var elCustomerInfo = $(".customer-info-wrapper");
                    if (!$.isEmptyObject(data.result["cart"])) {
                        elCustomerInfo.html(data.html);
                        if (data.result["cart"].status == cart_complete || data.result["cart"].status == cart_canceled ) {
                            $("#i-status-list").attr('readonly', true);
                            $("#save-cart-info").attr('disabled', true);
                        }
                    }

                }else{
                    console.log('Data is null');
                }
            }).fail(function(jqXHR, textStatus, data){
                console.log(data);
                console.log(textStatus);
            })

        });
    }

    function formatMoney(money){
        var number = 0;
        money = money.replace(" ","");
        if (money.includes(",")) {
            money = money.replace(/\,/g, "");
            
            if (money.includes("VNĐ")) {
                number = parseInt(money.replace("VNĐ", ""));
            }else{
                number = parseInt(money);
            }
            
        }else{
            number = parseInt(money);
        }
console.log(number);
        return number;
    }

    //---> Get customer detail, cart detail
    function updateStatus(){

        var cart_code = $("#code").text();
        var status_val = $("#i-status-list").val();
        // var payment_status_val = $("#i-payment-status-list").val();
        // var platform_val = $("#i-platforms-list").val();
        var pay_amount_val = ($('input[name="pay_amount"]').val()) ? formatMoney($('input[name="pay_amount"]').val()) : 0;
        var needed_paid_val = formatMoney($('#needed_paid').text());
        // console.log(pay_amount_val);return;
        $.ajax({
            url: "{{route('admin.carts.updateStatus')}}",
            type: 'PUT',
            data:{
                cart_code:cart_code,
                status: status_val,
                // payment_status: payment_status_val,
                // platform: platform_val,
                pay_amount: pay_amount_val,
                needed_paid: needed_paid_val,
            },
            dataType:'json'
        }).done(function(data) {
            console.log(data);
            if (!$.isEmptyObject(data)) {
                if (data.success == true) {
                    var alert_html = '<div class="alert alert-success alert-dismissable" id="i-alert-response">\
                    <button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>\
                    <span></span>\
                    </div>';
                }else{
                    var alert_html = '<div class="alert alert-warning alert-dismissable" id="i-alert-response">\
                    <button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>\
                    <span></span>\
                    </div>';
                }
                
                $(".alert-wrapper").html(alert_html);
                $("#i-alert-response span").text(data.message);
                $("#i-alert-response").show();
                $('html, body').animate({
                  scrollTop: 200
              }, 800);
            }else{
                console.log('Data is null');
            }
        }).fail(function(jqXHR, textStatus){
            console.log(textStatus);
        })
    }

    $(document).ready(function () {
        //---> Show menu on horizontal bar
        var url_index = "{{route('admin.carts.index')}}";
        if (location.href == url_index) {
            // $(".cart-menu-wrapper").show();
            $(".cart-menu-wrapper .cart-index").addClass("active");
        }
        
        $('#date_range_picker').datepicker({
            keyboardNavigation: false,
            forceParse: false,
            autoclose: true,
            format: 'dd/mm/yyyy'
        });

        table = $('#dataTables').dataTable({
            responsive: true,searching: false,
            processing: true,
            serverSide: true,
            "dom": 'rt<"#pagination"flp>',
            ajax: {
                "url": "{{route('admin.carts.index')}}",
                "data": function (d) {
                    d.code = $('#s-code').val();
                    d.customer_name = $('#s-customer-name').val();
                    d.customer_phone = $('#s-customer-phone').val();
                    d.platform_name = $('#s-platform-name').val();
                    d.status = $('#s-status').val();
                    d.start_date = $('input[name=start]').val();
                    d.end_date = $('input[name=end]').val();
                },
                complete: function () {
                    var inputStatus = document.querySelectorAll('.js-switch');
                    var elems = Array.prototype.slice.call(inputStatus);

                    elems.forEach(function (elem) {
                        var switchery = new Switchery(elem, {size: 'small'});

                        elem.onchange = function () {
                            var id = $(elem).attr('data-id');
                            var name = $(elem).attr('data-name');
                            if (elem.checked) {
                                var status = 'kích hoạt';
                            } else {
                                var status = 'bỏ kích hoạt';
                            }

                            swal({
                                title: "Cảnh Báo!",
                                text: "Bạn có chắc muốn " + status + " <b>" + name + "</b> ?",
                                html: true,
                                type: "warning",
                                showCancelButton: true,
                                confirmButtonClass: "btn-danger",
                                confirmButtonText: "Chắc chắn!",
                                cancelButtonText: "Không",
                                closeOnConfirm: false
                            },
                            function (isConfirm) {
                                if (isConfirm) {
                                    $.ajax({
                                        url: url_change_status,
                                        type: 'PUT',
                                        data: {
                                            id: id,
                                            status: elem.checked
                                        },
                                        dataType: 'json',
                                        success: function (response) {
                                            if (response.success) {
                                                swal({
                                                    title: "Thành công!",
                                                    text: "Bạn đã " + status + " danh mục " + name + " thành công.",
                                                    html: true,
                                                    type: "success",
                                                    confirmButtonClass: "btn-primary",
                                                    confirmButtonText: "Đóng lại."
                                                });
                                            } else {
                                                errorHtml = '<ul class="text-left">';
                                                $.each(response.errors, function (key, error) {
                                                    errorHtml += '<li>' + error + '</li>';
                                                });
                                                errorHtml += '</ul>';
                                                swal({
                                                    title: "Error! Refresh page and try again.",
                                                    text: errorHtml,
                                                    html: true,
                                                    type: "error",
                                                    confirmButtonClass: "btn-danger"
                                                });
                                            }
                                        }
                                    });
                                } else {
                                    $(elem).prop('checked', !elem.checked);
                                    $(elem).parent().find(".switchery").trigger("click");
                                }
                            });
                        };
                    });

                    //---> Get customer detail
                    getRecordDetail();
                },
            },
            columns: [
            {data: 'code'},
            {data: 'customer_name'},
            {data: 'customer_phone'},
            {data: 'created_at'},
            {data: 'platform_name'},
            {data: 'status'},
            ],
            "aoColumnDefs": [
                    // Column index begins at 0
                    {"sClass": "text-center", "aTargets": [2]},
                    {"sClass": "text-right", "aTargets": [3]}
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

$('#fSearch').submit(function () {
    table.fnDraw();
    return false;
});

$('#bt-reset').click(function () {
    $('#fSearch')[0].reset();
    table.fnDraw();
});

});

$("#dataTables").on("click", '.bt-delete', function () {
    var name = $(this).attr('data-name');
    var data = {
        ids: [$(this).attr('data-id')]
    };
    swal({
        title: "Cảnh Báo!",
        text: "Bạn có chắc muốn xóa <b>" + name + "</b> ?",
        html: true,
        type: "warning",
        showCancelButton: true,
        confirmButtonClass: "btn-danger",
        confirmButtonText: "Vâng, xóa!",
        closeOnConfirm: false
    },
    function () {
        $.ajax({
            url: url_delete,
            type: 'DELETE',
            data: data,
            dataType: 'json',
            success: function (response) {
                if (response.success) {
                    swal({
                        title: "Thành công!",
                        text: "Danh mục " + name + " đã bị xóa.",
                        html: true,
                        type: "success",
                        confirmButtonClass: "btn-primary",
                        confirmButtonText: "Đóng lại."
                    });
                } else {
                    errorHtml = '<ul class="text-left">';
                    $.each(response.errors, function (key, error) {
                        errorHtml += '<li>' + error + '</li>';
                    });
                    errorHtml += '</ul>';
                    swal({
                        title: "Error! Refresh page and try again.",
                        text: errorHtml,
                        html: true,
                        type: "error",
                        confirmButtonClass: "btn-danger"
                    });
                }
                table.fnDraw();
            }
        });

    });
});

//---> Update status cart
function updateCartStatus(){
    updateStatus();
    table.fnDraw();
}
</script>
@endsection
@section('content')
<div class="row">
    <!-- Search form -->
    <form role="form" id="fSearch">
        <div class="row ">
            <div class="col-sm-3">
                <div class="form-group">
                    <label>Mã đơn hàng</label>
                    <input type="text" placeholder="Nhập mã" name="code" id="s-code" class="form-control"
                    value="{{app('request')->input('code')}}">
                </div>
            </div>

            <div class="col-sm-3">
                <div class="form-group">
                    <label>Tên khách hàng</label>
                    <input type="text" placeholder="Nhập tên" name="customer_name" id="s-customer-name" class="form-control"
                    value="{{app('request')->input('customer_name')}}">
                </div>
            </div>

            <div class="col-sm-3">
                <div class="form-group">
                    <label>Điện thoại</label>
                    <input type="text" placeholder="Điện thoại" name="customer_phone" id="s-customer-phone" class="form-control"
                    value="{{app('request')->input('customer_phone')}}">
                </div>
            </div>
        </div>
        <div class="row ">
            <div class="col-sm-3">
                <div class="form-group">
                    <label>Ngày bán</label>
                    <div class="input-daterange input-group" id="date_range_picker">
                        <input type="text" class="input-sm form-control" name="start" value="">
                        <span class="input-group-addon lbl-to">to</span>
                        <input type="text" class="input-sm form-control" name="end" value="">
                    </div>
                </div>
            </div>

            <div class="col-sm-3">
                <div class="form-group">
                    <label>Nguồn đơn</label>
                    <!-- <input type="text" placeholder="Nguồn đơn" name="platform_name" id="s-supplier-name" class="form-control"
                        value="{{app('request')->input('platform_name')}}"> -->
                        <select id="s-platform-name" name="platform_name" class="form-control" placeholder="Chọn nguồn đơn">
                            <option value="">-- Chọn nguồn đơn --</option>
                            @foreach ($platforms as $platform)
                            <option value="{{$platform->id}}">{{$platform->name}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="col-sm-3">
                    <div class="form-group">
                        <label>Tình trạng</label>
                        <select class="form-control" name="status" id="s-status">
                            <option value=""> -- Tất cả --</option>
                            <option @if(app('request')->has('status') && app('request')->input('status') == CART_NEW) selected
                                @endif value="{{CART_NEW}}">{{CART_TEXT[CART_NEW]}}
                            </option>
                            <option @if(app('request')->has('status') && app('request')->input('status') == CART_EXCUTING) selected
                                @endif value="{{CART_EXCUTING}}">{{CART_TEXT[CART_EXCUTING]}}
                            </option>
                            <option @if(app('request')->has('status') && app('request')->input('status') == CART_TRANSPORTING) selected
                                @endif value="{{CART_TRANSPORTING}}">{{CART_TEXT[CART_TRANSPORTING]}}
                            </option>
                            <option @if(app('request')->has('status') && app('request')->input('status') == CART_TRANSPORTED) selected
                                @endif value="{{CART_TRANSPORTED}}">{{CART_TEXT[CART_TRANSPORTED]}}
                            </option>
                            <option @if(app('request')->has('status') && app('request')->input('status') == CART_COMPLETED) selected
                                @endif value="{{CART_COMPLETED}}">{{CART_TEXT[CART_COMPLETED]}}
                            </option>
                            <option @if(app('request')->has('status') && app('request')->input('status') == CART_CANCELED) selected
                                @endif value="{{CART_CANCELED}}">{{CART_TEXT[CART_CANCELED]}}
                            </option>
                        </select>
                    </div>
                </div>
                <div class="col-sm-3">
                    <div class="form-group" style="display: flex">
                        <button class="btn btn-sm btn-warning" type="submit" style="margin-bottom: 0;margin-top: 22px;">
                            <i class="fa fa-search"></i> Tìm kiếm
                        </button>
                        <button class="btn btn-sm btn-default" type="button" id="bt-reset"
                        style="margin-bottom: 0;margin-top: 22px; margin-right:5px">
                        <i class="fa fa-refresh"></i> Làm mới
                    </button>
                </div>

            </div>
        </div>
    </form>
</div>
<div class="row">
    <div class="col-md-12 alert-wrapper">
    </div>
    <div class="col-md-8">
        <div class="ibox float-e-margins mb">
            @include('admin._partials._alert')
            <div class="ibox-content">
                <div class="text-left">
                    <h3 class="text-uppercase">Danh sách đơn hàng</h3>
                </div>
                <div class="hr-line-dashed"></div>
                <!-- Account list -->
                <table class="table table-striped table-hover" id="dataTables">
                    <thead>
                        <tr>
                            <th>Mã đơn hàng</th>
                            <th>Tên khách hàng</th>
                            <th>Điện thoại</th>
                            <th>Ngày Tạo</th>
                            <th>Nguồn đơn</th>
                            <th>Tình trạng</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>

            </div>
        </div>
    </div>
    <div class="col-md-4 pl-0">
        <div class="ibox float-e-margins">
            <div class="ibox-content">
                <div class="text-left">
                    <h3 class="text-uppercase">thông tin khách hàng</h3>
                </div>
                <div class="hr-line-dashed"></div>
                <div class="customer-info-wrapper">
                    <div class="lbl-no-info"><span>Dữ liệu rỗng</span></div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection