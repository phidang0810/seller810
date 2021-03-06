<!-- PHuoc chich -->
@extends('admin.layouts.inspinia.master')

@section('title', $title)

@section('js')
    <!-- Page-Level Scripts -->
    <script>
        var url_delete = "{{route('admin.payslips.delete')}}";
        var table;
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $(document).ready(function() {
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
                    "url": "{{route('admin.payslips.index')}}",
                    "data": function ( d ) {
                        d.keyword = $('#s-keyword').val();
                        d.date_from = $('#date_from').val();
                        d.date_to = $('#date_to').val();
                    },
                    complete: function(){
                    }
                },
                columns: [
                    {data: 'group_name'},
                    {data: 'code'},
                    {data: 'description'},
                    {data: 'created_at'},
                    {data: 'price'},
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
                                    text: "Phiếu chi " + name + " đã bị xóa.",
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
        <!-- Search form -->
        <form role="form" id="fSearch">
            <div class="row ">
                <div class="col-sm-3">
                    <div class="form-group">
                        <label>Tìm kiếm</label>
                        <input type="text" placeholder="Nhập mã" name="keyword" id="s-keyword" class="form-control" value="{{app('request')->input('keyword')}}">
                    </div>
                </div>


                <div class="col-sm-3">
                    <div class="form-group">
                        <label>Ngày nhập</label>
                        <div class="input-daterange input-group" id="date_range_picker">
                            <input type="text" class="input-sm form-control" id="date_from" name="start" value="">
                            <span class="input-group-addon lbl-to">to</span>
                            <input type="text" class="input-sm form-control" id="date_to" name="end" value="">
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
                    <a href="{{route('admin.payslips.create')}}" class="btn btn-sm btn-primary"><i class="fa fa-plus"></i> Thêm Phiếu Chi</a>
                </div>
                <div class="hr-line-dashed"></div>
                <!-- Account list -->
                <table class="table table-striped table-hover" id="dataTables">
                    <thead>
                    <tr>
                        <th>Loại Phiếu Chi</th>
                        <th>Mã</th>
                        <th>Mô tả</th>
                        <th>Ngày Chi</th>
                        <th>Chi Phí</th>
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