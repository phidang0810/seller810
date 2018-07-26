<!-- PHuoc chich -->
@extends('admin.layouts.inspinia.master')

@section('title', $title)

@section('js')
    <!-- Page-Level Scripts -->
    <script>
        var url_delete = "{{route('admin.groupCustomer.delete')}}";
        var url_change_status = "{{route('admin.groupCustomer.changeStatus')}}";
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
                    "url": "{{route('admin.groupCustomer.index')}}",
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
                                                            text: "Bạn đã " + status + " nhóm " + name + " thành công.",
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
                    {data: 'name'},
                    {data: 'discount_amount'},
                    {data: 'count'},
                    {data: 'status'},
                    {data: 'action'}
                ],
                "aoColumnDefs": [
                    // Column index begins at 0
                    { "sClass": "text-center", "aTargets": [ 4 ] },
                    { "sClass": "text-right", "aTargets": [ 5 ] }
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
                    cancelButtonText: "Không",
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
                                    text: "Nhóm " + name + " đã bị xóa.",
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
                                    title: "Lỗi!",
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
        <div class="ibox float-e-margins pl-15 pr-15">
            @include('admin._partials._alert')
            <div class="ibox-content">
                <div class="text-right" style="padding: 10px 10px 0px 10px;">
                    <a href="{{route('admin.groupCustomer.create')}}" class="btn btn-sm btn-primary"><i class="fa fa-plus"></i> Tạo Nhóm</a>
                </div>
                <div class="hr-line-dashed"></div>
                <!-- Account list -->
                <table class="table table-striped table-hover" id="dataTables">
                    <thead>
                    <tr>
                        <th>ID</th>
                        <th>Tên</th>
                        <th>Chiết Khấu</th>
                        <th>Số Lượng Khách Hàng</th>
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