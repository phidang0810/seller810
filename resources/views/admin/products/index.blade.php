@extends('admin.layouts.inspinia.master')

@section('title', $title)

@section('js')
<!-- Page-Level Scripts -->
<script>
    var url_delete = "{{route('admin.products.delete')}}";
    var url_change_status = "{{route('admin.products.changeStatus')}}";
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
                "url": "{{route('admin.products.index')}}",
                "data": function ( d ) {
                    d.keyword = $('#s-keyword').val();
                    d.status = $('#s-status').val();
                },
                complete: function(){
                    var inputStatus = document.querySelectorAll('.js-switch');
                    var elems = Array.prototype.slice.call(inputStatus);

                    elems.forEach(function(elem) {
                        var switchery = new Switchery(elem, { size: 'small' });

                        elem.onchange = function() {
                            var id = $(elem).attr('data-id');
                            var name = $(elem).attr('data-name');
                            if (elem.checked) {
                                var status = 'kích hoạt';
                            } else {
                                var status = 'bỏ kích hoạt';
                            }

                            swal({
                                title: "Cảnh Báo!",
                                text: "Bạn có chắc muốn "+status+" <b>"+name+"</b> ?",
                                html:true,
                                type: "warning",
                                showCancelButton: true,
                                confirmButtonClass: "btn-danger",
                                confirmButtonText: "Chắc chắn!",
                                cancelButtonText: "Không",
                                closeOnConfirm: false
                            },
                            function(isConfirm){
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

                }
            },
            columns: [
            {data: 'id'},
            {data: 'photo'},
            {data: 'name'},
            {data: 'code'},
            {data: 'category'},
            {data: 'quantity_available'},
            {data: 'price'},
            {data: 'sizes'},
            {data: 'sell_price'},
            {data: 'status'},
            {data: 'action'}
            ],
            "aoColumnDefs": [
                    // Column index begins at 0
                    { "sClass": "text-center", "aTargets": [ 8 ] },
                    { "sClass": "text-right", "aTargets": [ 9 ] }
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
                        text: "Màu sắc " + name + " đã bị xóa.",
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
        <div class="row v-center">
            <div class="col-sm-3">
                <div class="form-group">
                    <label>Tên</label>
                    <input type="text" placeholder="Nhập tên" name="keyword" id="s-keyword" class="form-control" value="{{app('request')->input('keyword')}}">
                </div>
            </div>

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
                <div class="form-group">
                    <label></label>
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
                <a href="{{route('admin.products.create')}}" class="btn btn-sm btn-primary"><i class="fa fa-plus"></i> Tạo Sản Phẩm</a>
            </div>
            <div class="hr-line-dashed"></div>
            <!-- Account list -->
            <table class="table table-striped table-hover" id="dataTables">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Hình ảnh</th>
                        <th>Tên sản phẩm</th>
                        <th>Mã sản phẩm</th>
                        <th>Danh mục</th>
                        <th>Số lượng tồn</th>
                        <th>Giá bán</th>
                        <th>Size</th>
                        <th>Giá bán buôn</th>
                        <th>Trạng Thái</th>
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