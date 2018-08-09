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

    function formatDate(date) {
     var d = new Date(date),
     hour = d.getHours();
     minute = d.getMinutes();
     month = '' + (d.getMonth() + 1),
     day = '' + d.getDate(),
     year = d.getFullYear();

     if (month.length < 2) month = '0' + month;
     if (day.length < 2) day = '0' + day;

     return [hour, minute].join(':')+' '+[day, month, year].join('/');
 }

    //---> Get customer detail, cart detail
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

                if (!$.isEmptyObject(data.result["cart"])) {
                    $("#customer_name").text(data.result["cart"].customer_name);
                    $("#customer_phone").text(data.result["cart"].customer_phone);
                    $("#customer_email").text(data.result["cart"].customer_email);
                    $("#customer_address").text(data.result["cart"].customer_address);
                    var dateFormat = formatDate(data.result["cart"].created_at);
                    $("#cart_created").text(dateFormat);
                    $("#supplier_name").text(data.result["cart"].supplier_name);
                    $("#transport_name").text(data.result["cart"].transport_name);
                    $("#transport_id").text(data.result["cart"].transport_id);
                    $("#code").text(data.result["cart"].code);

                    var htmlTable = parseTableCartDetail(data.result["cart_detail"]);
                    $('.cart-detail-wrapper').html(htmlTable);
                    $('.c-total-money').text(getSummaryCart(data.result["cart_detail"])['total_price'].toLocaleString() + ' đ');
                    $('.c-shipping-fee').text(getSummaryCart(data.result["cart_detail"])['shipping_fee'].toLocaleString() + ' đ');
                    $('.c-amount').text(getSummaryCart(data.result["cart_detail"])['amount'].toLocaleString() + ' đ');
                    $('#i-status-list').val(data.result["cart"].status);
                }else{
                    console.log('Data is null');
                }
            }).fail(function(jqXHR, textStatus){
                console.log(textStatus);
            })

        });
    }

    //---> Get customer detail, cart detail
    function updateStatus(){

        var cart_code = $("#code").text();
        var status_val = $("#i-status-list").val();
        var alert_html = '<div class="alert alert-success alert-dismissable" id="i-alert-response">\
                            <button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>\
                            <span></span>\
                        </div>';
        $.ajax({
            url: "{{route('admin.carts.updateStatus')}}",
            type: 'PUT',
            data:{
                cart_code:cart_code,
                status: status_val
            },
            dataType:'json'
        }).done(function(data) {
            if (!$.isEmptyObject(data)) {
                console.log(data);
                $(".alert-wrapper").html(alert_html);
                $("#i-alert-response span").text(data.message);
                $("#i-alert-response").show();
            }else{
                console.log('Data is null');
            }
        }).fail(function(jqXHR, textStatus){
            console.log(textStatus);
        })
    }

    function parseTableCartDetail(arrCartDetails){
        var html = '';

        if (arrCartDetails.length > 0) {
            $.each(arrCartDetails, function( index, value ) {
              html += '<tr>'
              +'<td>'+value['barcode']+'</td>'
              +'<td>'+value['product_code']+'</td>'
              +'<td>'+value['price']+'</td>'
              +'<td>'+value['quantity']+'</td>'
              +'</tr>';
          });
        }

        return html;
    }

    function getSummaryCart(arrCartDetails){
        var totalPrice = 0;
        var shippingFee = 0;
        var amount = 0;
        totalPrice = arrCartDetails[0]["total_price"];
        shippingFee = arrCartDetails[0]["shipping_fee"];
        amount = parseFloat(totalPrice) + parseFloat(shippingFee);
        var objSummary = {
            total_price: totalPrice,
            shipping_fee: shippingFee,
            amount: amount
        };
        return objSummary;
    }

    $(document).ready(function () {
        $('#date_range_picker').datepicker({
            keyboardNavigation: false,
            forceParse: false,
            autoclose: true,
            format: 'dd/mm/yyyy'
        });

        var is_first_load = true;
        table = $('#dataTables').dataTable({
            searching: false,
            processing: true,
            serverSide: true,
            "dom": 'rt<"#pagination"flp>',
            ajax: {
                "url": "{{route('admin.carts.index')}}",
                "data": function (d) {
                    d.code = $('#s-code').val();
                    d.customer_name = $('#s-customer-name').val();
                    d.customer_phone = $('#s-customer-phone').val();
                    d.supplier_name = $('#s-supplier-name').val();
                    d.status = $('#s-status').val();
                    d.start_date = $('input[name=start]').val();
                    d.end_date = $('input[name=end]').val();
                    // console.log($('input[name=start]').val());
                    // console.log($('input[name=end]').val());
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
                    if (is_first_load) {
                        $('#dataTables tbody > tr:first').click();
                        is_first_load = false;
                    }
                    
                },
            },
            columns: [
            {data: 'code'},
            {data: 'customer_name'},
            {data: 'customer_phone'},
            {data: 'created_at'},
            {data: 'supplier_name'},
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
$("#save-cart-info").click(function(){
    updateStatus();
    table.fnDraw();
});
</script>
@endsection
@section('content')
<div class="row">
    <!-- Search form -->
    <form role="form" id="fSearch">
        <div class="row v-center">
            <div class="col-sm-2 pr-0">
                <div class="form-group">
                    <label>Mã đơn hàng</label>
                    <input type="text" placeholder="Nhập mã" name="code" id="s-code" class="form-control"
                    value="{{app('request')->input('code')}}">
                </div>
            </div>

            <div class="col-sm-2 pr-0 pl-10">
                <div class="form-group">
                    <label>Tên khách hàng</label>
                    <input type="text" placeholder="Nhập tên" name="customer_name" id="s-customer-name" class="form-control"
                    value="{{app('request')->input('customer_name')}}">
                </div>
            </div>

            <div class="col-sm-2 pr-0 pl-10">
                <div class="form-group">
                    <label>Điện thoại</label>
                    <input type="text" placeholder="Điện thoại" name="customer_phone" id="s-customer-phone" class="form-control"
                    value="{{app('request')->input('customer_phone')}}">
                </div>
            </div>

            <div class="col-sm-4 pr-0 pl-10 w-28-per">
                <div class="form-group">
                    <label>Ngày bán</label>
                    <div class="input-daterange input-group" id="date_range_picker">
                        <input type="text" class="input-sm form-control" name="start" value="">
                        <span class="input-group-addon lbl-to">to</span>
                        <input type="text" class="input-sm form-control" name="end" value="">
                    </div>
                </div>
            </div>

            <div class="col-sm-2 pr-0 pl-10">
                <div class="form-group">
                    <label>Nguồn đơn</label>
                    <input type="text" placeholder="Nguồn đơn" name="supplier_name" id="s-supplier-name" class="form-control"
                    value="{{app('request')->input('supplier_name')}}">
                </div>
            </div>

            <div class="col-sm-2 pr-0 pl-10">
                <div class="form-group">
                    <label>Tình trạng</label>
                    <select class="form-control" name="status" id="s-status">
                        <option value=""> -- Tất cả --</option>
                        <option @if(app('request')->has('status') && app('request')->input('status') == CART_NEW) selected
                            @endif value="{{CART_NEW}}">Mới
                        </option>
                        <option @if(app('request')->has('status') && app('request')->input('status') == CART_COMPLETE) selected
                            @endif value="{{CART_COMPLETE}}">Hoàn thành
                        </option>
                        <option @if(app('request')->has('status') && app('request')->input('status') == CART_IN_PROGRESS) selected
                            @endif value="{{CART_IN_PROGRESS}}">Đang giao
                        </option>
                        <option @if(app('request')->has('status') && app('request')->input('status') == CART_CANCELED) selected
                            @endif value="{{CART_CANCELED}}">Đã hủy
                        </option>
                    </select>
                </div>
            </div>
            <div class="col-sm-3 pl-10">
                <div class="form-group">
                    <label></label>
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
        <div class="ibox float-e-margins">
            @include('admin._partials._alert')
            <div class="ibox-content">
                <div class="text-left">
                    <h3 class="text-uppercase">Danh sách đơn hàng</h3>
                </div>
                <!-- <div class="text-right" style="padding: 10px 10px 0px 10px;">
                    <a href="{{route('admin.carts.create')}}" class="btn btn-sm btn-primary"><i
                        class="fa fa-plus"></i> Tạo Danh Mục</a>
                    </div> -->
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
                    <form class="form-horizontal">
                        <div class="form-group">
                            <label class="col-lg-5 control-label" style="text-align: left; padding-right: 0; width: 33.666667%;">Tên khách hàng:</label>
                            <label id="customer_name" class="col-lg-7 text-left control-label" style="padding-left: 0; text-align: left;"></label>
                        </div>

                        <div class="form-group">
                            <label class="col-lg-5 control-label" style="text-align: left; padding-right: 0; width: 33.666667%;">Số điện thoại:</label>
                            <label id="customer_phone" class="col-lg-7 text-left control-label" style="padding-left: 0; text-align: left;"></label>
                        </div>

                        <div class="form-group">
                            <label class="col-lg-5 control-label" style="text-align: left; padding-right: 0; width: 33.666667%;">Email:</label>
                            <label id="customer_email" class="col-lg-7 text-left control-label" style="padding-left: 0; text-align: left;"></label>
                        </div>

                        <div class="form-group">
                            <label class="col-lg-5 control-label" style="text-align: left; padding-right: 0; width: 33.666667%;">Địa chỉ:</label>
                            <label id="customer_address" class="col-lg-7 text-left control-label" style="padding-left: 0; text-align: left;"></label>
                        </div>

                        <div class="form-group">
                            <label class="col-lg-5 control-label" style="text-align: left; padding-right: 0; width: 33.666667%;">Ngày mua:</label>
                            <label id="cart_created" class="col-lg-7 text-left control-label" style="padding-left: 0; text-align: left;"></label>
                        </div>

                        <div class="form-group">
                            <label class="col-lg-5 control-label" style="text-align: left; padding-right: 0; width: 33.666667%;">Nguồn đơn:</label>
                            <label id="supplier_name" class="col-lg-7 text-left control-label" style="padding-left: 0; text-align: left;"></label>
                        </div>
                        
                        <div class="form-group">
                            <label class="col-lg-5 control-label" style="text-align: left; padding-right: 0; width: 33.666667%;">Nhà vận chuyển:</label>
                            <label id="transport_name" class="col-lg-7 text-left control-label" style="padding-left: 0; text-align: left;"></label>
                        </div>

                        <div class="form-group">
                            <label class="col-lg-5 control-label" style="text-align: left; padding-right: 0; width: 33.666667%;">Mã vận đơn:</label>
                            <label id='transport_id' class="col-lg-7 text-left control-label" style="padding-left: 0; text-align: left;"></label>
                        </div>

                        <div class="form-group">
                            <label class="col-lg-5 control-label" style="text-align: left; padding-right: 0; width: 33.666667%;">Mã đơn hàng:</label>
                            <label id="code" class="col-lg-7 text-left control-label" style="padding-left: 0; text-align: left;"></label>
                        </div>

                        <div class="form-group">
                            <label class="col-lg-5 control-label" style="text-align: left; padding-right: 0; width: 33.666667%;">Tình trạng:</label>
                            <div class="col-lg-7" style="padding-left: 0; text-align: left;">
                                <select id="i-status-list" class="form-control m-b" name="status">
                                    <option value="{{CART_NEW}}">Mới</option>
                                    <option value="{{CART_IN_PROGRESS}}">Đang giao</option>
                                    <option value="{{CART_COMPLETE}}">Hoàn thành</option>
                                    <option value="{{CART_CANCELED}}">Đã hủy</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="text-left">
                            <h3 class="text-uppercase">thông tin đơn hàng</h3>
                        </div>
                        <div class="hr-line-dashed"></div>
                        <div class="ibox-content m-b">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Mã barcode</th>
                                        <th>Mã sản phẩm</th>
                                        <th>Đơn giá</th>
                                        <th>Số lượng</th>
                                    </tr>
                                </thead>
                                <tfoot>
                                    <tr>
                                        <td colspan="3">Tổng cộng</td>
                                        <td><span class="c-total-money"></span></td>
                                    </tr>
                                    <tr>
                                        <td colspan="3">Phí vận chuyển</td>
                                        <td><span class="c-shipping-fee"></span></td>
                                    </tr>
                                    <tr>
                                        <td colspan="3" style="font-weight: 700;">Thành tiền</td>
                                        <td><span class="c-amount"></span></td>
                                    </tr>
                                </tfoot>
                                <tbody class="cart-detail-wrapper">
                                </tbody>
                            </table>
                        </div>
                        <div class="form-group">
                            <div class="col-lg-offset-2 col-lg-10 text-right">
                                <button id="save-cart-info" class="btn btn-sm btn-primary" type="button">Lưu</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    @endsection