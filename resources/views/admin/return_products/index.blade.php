@extends('admin.layouts.inspinia.master')

@section('title', $title)

@section('js')
<!-- Page-Level Scripts -->
<script>
    var url_delete = "{{route('admin.return_products.delete')}}";
    var url_returned = "{{route('admin.return_products.returned')}}";
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

        $('#date_range_picker').datepicker({
            keyboardNavigation: false,
            forceParse: false,
            autoclose: true,
            format: 'dd/mm/yyyy'
        });

        formar_money();

        table = $('#dataTables').dataTable({
            // responsive: true,
            searching: false,
            processing: true,
            serverSide: true,
            "dom": 'rt<"#pagination"flp>',
            ajax: {
                "url": "{{route('admin.return_products.index')}}",
                "data": function ( d ) {
                    d.status = $('#s-status').val();
                    d.code = $('#s-code').val();
                    d.start_date = $('input[name=start]').val();
                    d.end_date = $('input[name=end]').val();
                },
                complete: function(){
                    formar_money();
                }
            },
            columns: [
            {data: 'id'},
            {data: 'code'},
            {data: 'quantity'},
            {data: 'staff_name'},
            {data: 'return_date'},
            {data: 'status'},
            {data: 'action'}
            ],
            "aoColumnDefs": [
                    // Column index begins at 0
                    // { "sClass": "thousand-number money text-align", "aTargets": [ 4 ] },
                    // { "sClass": "text-center", "aTargets": [ 5 ] },
                    { "sClass": "text-right", "aTargets": [ 6,2 ] }
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

$("#dataTables").on("click", '.bt-returned', function(){
    var name = $(this).attr('data-name');
    var data = {
        id: $(this).attr('data-id')
    };
    swal({
        title: "Cảnh Báo!",
        text: "Bạn đã trả xong hàng của mã hàng <b>"+name+"</b> ?",
        html:true,
        type: "warning",
        showCancelButton: true,
        confirmButtonClass: "btn-primary",
        confirmButtonText: "Vâng, nhập!",
        closeOnConfirm: false
    },
    function(){
        $.ajax({
            url: url_returned,
            type: 'get',
            data: data,
            dataType:'json',
            success: function(response) {
                if (response.success) {
                    swal({
                        title: "Thành công!",
                        text: "Mã trả hàng " + name + " đã trả hàng xong.",
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
                        <option @if(app('request')->has('status') && app('request')->input('status') == RETURN_RETURNING) selected @endif value="{{RETURN_RETURNING}}">{{RETURN_TEXT[RETURN_RETURNING]}}</option>
                        <option @if(app('request')->has('status') && app('request')->input('status') == RETURN_RETURNED) selected @endif value="{{RETURN_RETURNED}}">{{RETURN_TEXT[RETURN_RETURNED]}}</option>
                    </select>
                </div>
            </div>
            <div class="col-sm-3">
                <div class="form-group">
                    <label>Mã trả hàng</label>
                    <input type="text" placeholder="Nhập mã" name="code" id="s-code" class="form-control"
                    value="{{app('request')->input('code')}}">
                </div>
            </div>
            <div class="col-sm-3">
                <div class="form-group">
                    <label>Ngày trả</label>
                    <div class="input-daterange input-group" id="date_range_picker">
                        <input type="text" class="input-sm form-control" name="start" value="">
                        <span class="input-group-addon lbl-to">to</span>
                        <input type="text" class="input-sm form-control" name="end" value="">
                    </div>
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
            <div class="text-right" style="padding: 10px 10px 0px 10px;">
                <a href="{{route('admin.return_products.create')}}" class="btn btn-sm btn-primary"><i class="fa fa-plus"></i> Tạo trả hàng</a>
            </div>
            <div class="hr-line-dashed"></div>
            <!-- Account list -->
            <table class="table table-striped table-hover" id="dataTables">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Mã trả hàng</th>
                        <th>Số lượng trả</th>
                        <th>Người phụ trách</th>
                        <th>Ngày trả</th>
                        <th>Tình trạng</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                </tbody>
            </table>

        </div>
    </div>
</div>
@endsection