@extends('admin.layouts.inspinia.master')

@section('title', $title)

@section('js')
    <!-- Page-Level Scripts -->
    <script>
        var url_delete = "{{route('admin.users.delete')}}";
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
                    "url": "{{route('admin.users.index')}}",
                    "data": function ( d ) {
                        d.keyword = $('#s-keyword').val();
                        d.role = $('#s-role').val();
                        d.status = $('#s-status').val();
                    }
                },
                columns: [
                    {data: 'id'},
                    {data: 'email'},
                    {data: 'full_name'},
                    {data: 'role'},
                    {data: 'created_at'},
                    {data: 'status'},
                    {data: 'action'}
                ],
                "aoColumnDefs": [
                    // Column index begins at 0
                    { "sClass": "text-center", "aTargets": [ 5 ] },
                    { "sClass": "text-right", "aTargets": [ 6 ] }
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
            var email = $(this).attr('data-email');
            var data = {
                ids: [$(this).attr('data-id')]
            };
            swal({
                    title: "Cảnh Báo!",
                    text: "Bạn có chắc muốn xóa <b>"+email+"</b> ?",
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
                                    text: "Tài khoản " + email + " đã bị xóa.",
                                    html: true,
                                    type: "success",
                                    confirmButtonClass: "btn-primary"
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
                    <label>Email, họ tên</label>
                    <input type="text" placeholder="Nhập email, họ tên" name="keyword" id="s-keyword" class="form-control" value="{{app('request')->input('keyword')}}">
                </div>
            </div>

            <div class="col-sm-3">
                <div class="form-group">
                    <label>Chọn quyền</label>
                    <select class="form-control" name="role" id="s-role">
                        <option value=""> -- Tất cả -- </option>
                        @foreach($roles as $role)
                            <option @if(app('request')->input('role') == $role->id) selected @endif value="{{$role->id}}">{{$role->name}}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="col-sm-3">
                <div class="form-group">
                    <label>Chọn trạng thái</label>
                    <select class="form-control" name="status" id="s-status">
                        <option value=""> -- Tất cả -- </option>
                        <option @if(app('request')->has('status') && app('request')->input('status') == ACTIVE) selected @endif value="{{ACTIVE}}">Active</option>
                        <option @if(app('request')->has('status') && app('request')->input('status') == INACTIVE) selected @endif value="{{INACTIVE}}">Inactive</option>
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
                        <i class="fa fa-refresh"></i> Clear
                    </button>
                </div>

            </div>
        </div>
    </form>
</div>
<div class="row">
        <div class="ibox float-e-margins">
            @include('admin._partials._alert')
            <div class="ibox-content">
                <div class="text-right" style="padding: 10px 10px 0px 10px;">
                    <a href="{{route('admin.users.create')}}" class="btn btn-sm btn-primary"><i class="fa fa-plus"></i> Tạo Tài Khoản</a>
                </div>
                <div class="hr-line-dashed"></div>
                <!-- Account list -->
                <table class="table table-striped table-hover" id="dataTables">
                    <thead>
                    <tr>
                        <th>ID</th>
                        <th>Email</th>
                        <th>Họ Tên</th>
                        <th>Quyền</th>
                        <th>Ngày Tạo</th>
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