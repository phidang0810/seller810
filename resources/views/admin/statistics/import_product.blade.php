<!-- PHuoc chich -->
@extends('admin.layouts.inspinia.master')

@section('title', $title)

@section('js')
    <!-- Page-Level Scripts -->
    <script>
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
                    "url": "{{route('admin.statistics.importProduct')}}",
                    "data": function ( d ) {
                        d.keyword = $('#s-keyword').val();
                        d.role = $('#s-role').val();
                        d.status = $('#s-status').val();
                    },
                    complete: function(){
                    }
                },
                columns: [
                    {data: 'name'},
                    {data: 'code'},
                    {data: 'category'},
                    {data: 'quantity'},
                    {data: 'created_at'},
                    {data: 'total_price'}
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
            $('#date_range_picker').datepicker({
                keyboardNavigation: false,
                forceParse: false,
                autoclose: true,
                format: 'dd/mm/yyyy'
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
                    <label>Tên hoặc mã sản phẩm</label>
                    <input type="text" placeholder="Nhập tên hoặc mã sản phẩm" name="keyword" id="s-keyword" class="form-control" value="{{app('request')->input('keyword')}}">
                </div>
            </div>

            <div class="col-sm-3">
                <div class="form-group">
                    <label>Chọn danh mục</label>
                    <select class="form-control" name="role" id="s-role">
                        <option value=""> -- Tất cả -- </option>
                        {{$categoriesTree}}
                    </select>
                </div>
            </div>

            <div class="col-sm-3">
                <div class="form-group">
                    <label>Ngày nhập</label>
                    <div class="input-daterange input-group" id="date_range_picker">
                        <input type="text" class="input-sm form-control" name="start" value="">
                        <span class="input-group-addon lbl-to">to</span>
                        <input type="text" class="input-sm form-control" name="end" value="">
                    </div>
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
        <div class="ibox float-e-margins pl-15 pr-15">
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
                        <th>Tên</th>
                        <th>Mã Sản Phẩm</th>
                        <th>Danh Mục</th>
                        <th>Tổng Nhập</th>
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