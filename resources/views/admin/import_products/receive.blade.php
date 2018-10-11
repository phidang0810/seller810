@extends('admin.layouts.inspinia.master')

@section('title', $title)

@section('js')
<!-- Page-Level Scripts -->
<script>
    var url_delete = "{{route('admin.import_products.delete')}}";
    var url_print = "{{route('admin.import_products.print')}}";
    var table;
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    function formar_money(){
        $('tbody .thousand-number').simpleMoneyFormat();
        if($('tbody .thousand-number.money').text() != '' || $('.thousand-number.money').text() != null){
            $('tbody .thousand-number.money').append(" VNĐ");
        }
    }
    $(document).ready(function() {
        formar_money();
        table = $('#dataTables').dataTable({
            // responsive: true,
            searching: false,
            processing: true,
            serverSide: true,
            "dom": 'rt<"#pagination"flp>',
            ajax: {
                "url": "{{route('admin.import_products.receive')}}",
                "data": function ( d ) {
                    d.status = $('#s-status').val();
                },
                complete: function(){
                    formar_money();
                }
            },
            columns: [
            {data: 'id'},
            {data: 'code'},
            {data: 'product_name'},
            {data: 'supplier_name'},
            {data: 'product_category'},
            {data: 'quantity'},
            {data: 'total_price'},
            {data: 'status'},
            {data: 'action'}
            ],
            "aoColumnDefs": [
                    // Column index begins at 0
                    { "sClass": "thousand-number money text-align", "aTargets": [ 6 ] },
                    { "sClass": "text-center", "aTargets": [ 7 ] },
                    { "sClass": "text-right", "aTargets": [ 8 ] }
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
    });

    $("#dataTables").on("click", '.bt-delete', function(){
        var name = $(this).attr('data-name');
        var data = {
            ids: [$(this).attr('data-id')]
        };
        swal({
            title: "Cảnh Báo!",
            text: "Bạn có chắc muốn xóa <b>"+name+"</b> ?",
            html:true,
            type: "warning",
            showCancelButton: true,
            confirmButtonClass: "btn-danger",
            confirmButtonText: "Vâng, xóa!",
            closeOnConfirm: false
        },
        function(){
            $.ajax({
                url: url_delete,
                type: 'DELETE',
                data: data,
                dataType:'json',
                success: function(response) {
                    if (response.success) {
                        swal({
                            title: "Thành công!",
                            text: "Sản phẩm " + name + " đã bị xóa.",
                            html: true,
                            type: "success",
                            confirmButtonClass: "btn-primary",
                            confirmButtonText: "Đóng lại."
                        });
                    } else {
                        errorHtml = '<ul class="text-left">';
                        $.each( response.errors, function( key, error ) {
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

    function resetDataPrint(){
        $('label.lbl-customer-name').text("");
        $('label.lbl-customer-created').text("");
        $('label.lbl-customer-phone').text("");
        $('label.lbl-customer-email').text("");
        $('label.lbl-customer-code').text("");
        $('label.lbl-customer-address').text("");
        $('table.tbl-list-product tbody').html("");
        transport_quantity = 0;
    }

    $("#dataTables").on("click", '.bt-print', function(){
        var name = $(this).attr('data-name');
        var data = {
            id: $(this).attr('data-id')
        };

        var transport_quantity = 0;

        $.ajax({
            url: url_print,
            type: 'get',
            data: data,
            dataType:'json',
            success: function(response) {
                if (response.success) {
                    // console.log(response);
                    resetDataPrint();
                    $('label.lbl-customer-name').text(response.import_warehouse.staff.full_name);
                    $('label.lbl-customer-created').text(response.import_warehouse.created_at);
                    $('label.lbl-customer-phone').text(response.import_warehouse.staff.phone);
                    $('label.lbl-customer-email').text(response.import_warehouse.staff.email);
                    $('label.lbl-customer-code').text(response.import_warehouse.code);
                    $('label.lbl-customer-address').text(response.import_warehouse.supplier.name);
                    // if (response.import_warehouse.product.length > 0) {

                    //     $('table.tbl-list-product tbody').html(printTableRows(response.import_warehouse.products));
                    // }
                    var print_el = $("#print-section");
                    print_el.removeClass("hidden");
                    print_el.printThis({
                        header: null,

                    });
                } else {

                }
            }
        });
    });

    function printTableRows(details){
        html = "";
        $.each(details, function(key, detail){
            html_product_name = detail.product.name;
            html_product_code = detail.product.barcode_text;
            html_quantity = detail.quantity;
            html += '<tr><th>'+html_product_name+'</th><th>'+html_product_code+'</th><th style="text-align: right;">'+html_quantity+'</th></tr>';
            transport_quantity += parseInt(detail.quantity);
        });
        $('label.lbl-transport-quantity').text(transport_quantity);
        return html;
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
                    <label>Chọn trạng thái</label>
                    <select class="form-control" name="status" id="s-status">
                        <option value=""> -- Tất cả -- </option>
                        <option @if(app('request')->has('status') && app('request')->input('status') == ACTIVE) selected @endif value="{{ACTIVE}}">Đã kích hoạt</option>
                        <option @if(app('request')->has('status') && app('request')->input('status') == INACTIVE) selected @endif value="{{INACTIVE}}">Chưa kích hoạt</option>
                    </select>
                </div>
            </div>
            <div class="col-sm-3">
                <div class="form-group" style="display: flex">
                    <button class="btn btn-sm btn-warning" type="submit" style="margin-bottom: 0;margin-top: 22px;">
                        <i class="fa fa-search"></i> Tìm kiếm
                    </button>
                    <button class="btn btn-sm btn-default" type="button" id="bt-reset" style="margin-bottom: 0;margin-top: 22px; margin-right:5px">
                        <i class="fa fa-refresh"></i> Làm mới
                    </button>
                </div>

            </div>
        </div>
    </form>
</div>
<div class="row">
    <div class="ibox float-e-margins pl-15 pr-15">
        @include('admin._partials._alert')
        <div class="ibox-content">
            <div class="hr-line-dashed"></div>
            <!-- Account list -->
            <table class="table table-striped table-hover" id="dataTables">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Mã nhập hàng</th>
                        <th>Tên sản phẩm</th>
                        <th>Nhà cung cấp</th>
                        <th>Danh mục</th>
                        <th>Số lượng</th>
                        <th>Tổng giá sản phẩm</th>
                        <th>Trạng thái</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                </tbody>
            </table>

        </div>
    </div>
</div>
@include('admin._partials._import_warehouse_receive')
@endsection