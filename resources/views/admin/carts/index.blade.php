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
        var data_to_print;

        function getRecordDetail() {
            $('#dataTables tbody > tr').click(function () {
                $('#dataTables tbody tr').removeClass("cart-active");
                $(this).addClass("cart-active");
                var cart_code = $(this).find('td:not(:empty):first').text();
                $.ajax({
                    url: "{{route('admin.carts.getCartDetail')}}",
                    data: {
                        cart_code: cart_code
                    },
                    dataType: 'json'
                }).done(function (data) {
                    // console.log(data);
                    data_to_print = data;
                    if (!$.isEmptyObject(data.result["cart"])) {
                        var elCustomerInfo = $(".customer-info-wrapper");
                        if (!$.isEmptyObject(data.result["cart"])) {
                            elCustomerInfo.html(data.html);
                            if (data.result["cart"].status == cart_complete || data.result["cart"].status == cart_canceled) {
                                $("#i-status-list").attr('readonly', true);
                                $("#save-cart-info").attr('disabled', true);
                            }
                        }
                    } else {
                        console.log('Data is null');
                    }
                }).fail(function (jqXHR, textStatus, data) {
                    console.log(data);
                    console.log(textStatus);
                })

            });
        }

        function addCommas(nStr)
        {
            nStr += '';
            x = nStr.split('.');
            x1 = x[0];
            x2 = x.length > 1 ? '.' + x[1] : '';
            var rgx = /(\d+)(\d{3})/;
            while (rgx.test(x1)) {
                x1 = x1.replace(rgx, '$1' + ',' + '$2');
            }
            return x1 + x2;
        }

        // Function remove all character non-digit
        function removeNonDigit(str){
            return str.replace(/\D/g,'');
        }

        function parseSummaryProduct(cart){
            // var html = '<tr><td colspan="4" style="text-align:right" >Tổng cộng:</td><td class="thousand-number" style="text-align:right">'+cart['total_price']+'</td></tr>';
            // html += '<tr><td colspan="4" style="text-align:right">Thuế:</td><td class="thousand-number" style="text-align:right">'+cart['vat_amount']+'</td></tr>';
            // html += '<tr><td colspan="4" style="text-align:right">Phí vận chuyển:</td><td class="thousand-number" style="text-align:right">'+cart['shipping_fee']+'</td></tr>';
            // html += '<tr><td colspan="4" style="text-align:right">Tổng chiết khấu:</td><td class="thousand-number" style="text-align:right">-'+cart['total_discount_amount']+'</td></tr>';
            // html += '<tr><td colspan="4" style="text-align:right">Thành tiền:</td><td class="thousand-number" style="text-align:right">'+cart['price']+'</td></tr>';
            // $('.tbl-list-product > tfoot').html(html);
            $('label.lbl-total-price').text(addCommas(cart.total_price));
            $('label.lbl-total-quantity').text(addCommas(cart.quantity));
            $('label.lbl-discount-amount').text(addCommas(cart.total_discount_amount));
            $('label.lbl-shipping-fee').text(addCommas(cart.shipping_fee));
            $('h4.lbl-price').text(addCommas(cart.price));
        }

        function parseProductTable(arrProducts){
            var path_img_folder = window.location.origin + '/storage/';
            var html = '';
            if (arrProducts.length > 0) {
                $.each(arrProducts, function( index, value ) {
                    product_image = (value.product.photo) ? path_img_folder + value.product.photo : default_image;
                    html += '<tr>';
                    html += '<td>'+value.product.name+'</td>';
                    html += '<td>'+value.product.barcode_text+'</td>';
                    html += '<td><span class="img-wrapper"><img class="img-thumbnail" style="width: 80px; height: 60px;" src="' + product_image + '"/></span></td>';
                    html += '<td class="thousand-number" style="text-align:right;">'+value.price+'</td>';
                    html += '<td style="text-align:right;">'+value.quantity+'</td>';
                    html += '<td class="thousand-number" style="text-align:right;">'+value.total_price+'</td>';
                    html += '</tr>';
                });
            }
            return html;
        }

        function resetResultPrint(){
            html = '<tr style="border-top:3px solid #333;">\
                        <th colspan="6" style="position: relative;">\
                            <div class="row" style="margin-top: 20px;">\
                                <div class="col-sm-7">\
                                </div>\
                                <div class="col-sm-5">\
                                    <div class="form-group">\
                                        <label class="col-sm-8 control-label font-bold">Tổng cộng:</label>\
                                        <label class="col-sm-4 control-label lbl-total-price" style="text-align: right;"></label>\
                                    </div>\
                                </div>\
                            </div>\
                            <div class="row">\
                                <div class="col-sm-7">\
                                </div>\
                                <div class="col-sm-5">\
                                    <div class="form-group">\
                                        <label class="col-sm-8 control-label font-bold">Tổng số lượng:</label>\
                                        <label class="col-sm-4 control-label lbl-total-quantity" style="text-align: right;"></label>\
                                    </div>\
                                </div>\
                            </div>\
                            <div class="row">\
                                <div class="col-sm-7">\
                                </div>\
                                <div class="col-sm-5">\
                                    <div class="form-group">\
                                        <label class="col-sm-8 control-label font-bold">Chiết khấu:</label>\
                                        <label class="col-sm-4 control-label lbl-discount-amount" style="text-align: right;"></label>\
                                    </div>\
                                </div>\
                            </div>\
                            <div class="row" style="margin-bottom: 70px;">\
                                <div class="col-sm-7">\
                                </div>\
                                <div class="col-sm-5">\
                                    <div class="form-group">\
                                        <label class="col-sm-8 control-label font-bold">Phí vận chuyển:</label>\
                                        <label class="col-sm-4 control-label lbl-shipping-fee" style="text-align: right;"></label>\
                                    </div>\
                                </div>\
                            </div>\
                            <div class="row" style="position: absolute; bottom: 0; right: 70px; width: 150px;">\
                                <div class="form-group">\
                                    <h4 class="col-sm-8 control-label font-bold">Thành tiền:</h4>\
                                    <h4 class="col-sm-4 control-label lbl-price" style="text-align: right;"></h4>\
                                </div>\
                            </div>\
                        </th>\
                    </tr>';
            $('.tbl-list-product > tbody').html(html);
        }

        function getDataToPrint(data){
            //---> Apply for print
            $('.lbl-customer-name').text(data.result.cart.customer.name);
            $('.lbl-customer-phone').text(data.result.cart.customer.phone);
            $('.lbl-customer-address').text(data.result.cart.customer.address);
            $('.lbl-customer-created').text(data.result.cart.created_at);
            $('.lbl-customer-code').text(data.result.cart.code);
            resetResultPrint();
            $('.tbl-list-product > tbody').prepend(parseProductTable(data.result.cart.details));
            parseSummaryProduct(data.result.cart);
            setTimeout(function(){
                $('#print-section .thousand-number').simpleMoneyFormat();
                $('#print-section .thousand-number').append(" VNĐ");
            },300);
        }

        var print_el = $("#print-section");
        function printCart(){
            getDataToPrint(data_to_print);
            print_el.removeClass("hidden");
            print_el.printThis({
                header: null,
            });
        }

        function formatMoney(money) {
            var number = 0;
            money = money.replace(" ", "");
            if (money.includes(",")) {
                money = money.replace(/\,/g, "");

                if (money.includes("VNĐ")) {
                    number = parseInt(money.replace("VNĐ", ""));
                } else {
                    number = parseInt(money);
                }

            } else {
                number = parseInt(money);
            }
            console.log(number);
            return number;
        }

        //---> Get customer detail, cart detail
        function updateStatus() {

            var cart_code = $("#code").text();
            var status_val = $("#i-status-list").val();
            var pay_amount_val = ($('input[name="pay_amount"]').val()) ? formatMoney($('input[name="pay_amount"]').val()) : 0;
            var needed_paid_val = formatMoney($('#needed_paid').text());
            var transport_id = $('input[name="transport_id"]').val();
            $.ajax({
                url: "{{route('admin.carts.updateStatus')}}",
                type: 'PUT',
                data: {
                    cart_code: cart_code,
                    status: status_val,
                    pay_amount: pay_amount_val,
                    needed_paid: needed_paid_val,
                    transport_id: transport_id
                },
                dataType: 'json'
            }).done(function (data) {
                console.log(data);
                if (!$.isEmptyObject(data)) {
                    if (data.success == true) {
                        var alert_html = '<div class="alert alert-success alert-dismissable" id="i-alert-response">\
                        <button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>\
                        <span></span>\
                        </div>';
                    } else {
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
                } else {
                    console.log('Data is null');
                }
            }).fail(function (jqXHR, textStatus) {
                console.log(textStatus);
            })
        }

        $.urlParam = function(name){
            var results = new RegExp('[\?&]' + name + '=([^&#]*)').exec(window.location.href);
            if (results==null){
             return null;
         }
         else{
             return decodeURI(results[1]) || 0;
         }
     }

     function activeRecoreByCartCode(){
        var cart_code = $.urlParam("cart_code");
        if (cart_code !== null) {
            var elCartCode = $('#'+cart_code);
            elCartCode.closest("tr").click();
        }
    }

    $(document).ready(function () {

            //---> Show menu on horizontal bar
            var url_index = "{{route('admin.carts.index')}}";
            var cart_code = $.urlParam("cart_code");
            if (location.href == url_index) {
                $(".cart-menu-wrapper .cart-index").addClass("active");
            }

            $('#date_range_picker').datepicker({
                keyboardNavigation: false,
                forceParse: false,
                autoclose: true,
                format: 'dd/mm/yyyy'
            });

            table = $('#dataTables').dataTable({
                responsive: true, searching: false,
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
                        d.payment_status = $('#s-payment-status').val();
                        d.start_date = $('input[name=start]').val();
                        d.end_date = $('input[name=end]').val();
                        d.cart_code = cart_code;
                        d.no_link = true;
                    },
                    complete: function (response) {
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
                                        $(elem).parent().find(".switchery").trigger("click");
                                    }
                                });
                            };
                        });

                        //---> Get customer detail
                        getRecordDetail();
                        activeRecoreByCartCode();
                    },
                },
                columns: [
                {data: 'code'},
                {data: 'customer_name'},
                {data: 'customer_phone'},
                {data: 'created_at'},
                {data: 'platform_name'},
                {data: 'status'},
                {data: 'payment_status'},
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
        function updateCartStatus() {
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
                        <input type="text" placeholder="Nhập tên" name="customer_name" id="s-customer-name"
                        class="form-control"
                        value="{{app('request')->input('customer_name')}}">
                    </div>
                </div>

                <div class="col-sm-3">
                    <div class="form-group">
                        <label>Điện thoại</label>
                        <input type="text" placeholder="Điện thoại" name="customer_phone" id="s-customer-phone"
                        class="form-control"
                        value="{{app('request')->input('customer_phone')}}">
                    </div>
                </div>

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
            </div>
            <div class="row ">
                <div class="col-sm-3">
                    <div class="form-group">
                        <label>Nguồn đơn</label>
                        <select id="s-platform-name" name="platform_name" class="form-control"
                        placeholder="Chọn nguồn đơn">
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
                <div class="form-group">
                    <label>Tình trạng thanh toán</label>
                    <select class="form-control" name="payment_status" id="s-payment-status">
                        <option value=""> -- Tất cả --</option>
                        <option @if(app('request')->has('payment_status') && app('request')->input('payment_status') == NOT_PAYING) selected
                            @endif value="{{NOT_PAYING}}">{{CART_PAYMENT_TEXT[NOT_PAYING]}}
                        </option>
                        <option @if(app('request')->has('payment_status') && app('request')->input('payment_status') == PAYING_NOT_ENOUGH) selected
                            @endif value="{{PAYING_NOT_ENOUGH}}">{{CART_PAYMENT_TEXT[PAYING_NOT_ENOUGH]}}
                        </option>
                        <option @if(app('request')->has('payment_status') && app('request')->input('payment_status') == PAYING_OFF) selected
                            @endif value="{{PAYING_OFF}}">{{CART_PAYMENT_TEXT[PAYING_OFF]}}
                        </option>
                        <option @if(app('request')->has('payment_status') && app('request')->input('payment_status') == RECEIVED_PAYMENT) selected
                            @endif value="{{RECEIVED_PAYMENT}}">{{CART_PAYMENT_TEXT[RECEIVED_PAYMENT]}}
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
        <div class="ibox float-e-margins mb list-carts">
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
                            <th>Tình trạng thanh toán</th>
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
@include('admin._partials._cart_view_print')
@endsection