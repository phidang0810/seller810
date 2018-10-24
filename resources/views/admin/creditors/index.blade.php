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
                    "url": "{{route('admin.creditors.index')}}",
                    "data": function ( d ) {
                        d.keyword = $('#s-keyword').val();
                        d.status = $('#s-status').val();
                        d.date_from = $('#date_from').val();
                        d.date_to = $('#date_to').val();
                        d.date_from = $('#date_from').val();
                        d.code = $('#s-code').val();
                    },
                    complete: function(){}
                },
                columns: [
                    {data: 'supplier_name'},
                    {data: 'supplier_code'},
                    {data: 'code'},
                    {data: 'full_name'},
                    {data: 'phone'},
                    {data: 'total'},
                    {data: 'paid'},
                    {data: 'date'},
                    {data: 'status'},
                    {data: 'action'}
                ],
                "aoColumnDefs": [
                    // Column index begins at 0
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

    </script>
@endsection
@section('content')
    <div class="row">
        <!-- Search form -->
        <form role="form" id="fSearch">
            <div class="row ">
                <div class="col-sm-2">
                    <div class="form-group">
                        <label>Nhà Cung Cấp</label>
                        <input type="text" placeholder="Tên nhà cung cấp" name="keyword" id="s-keyword" class="form-control" value="{{app('request')->input('keyword')}}">
                    </div>
                </div>

                <div class="col-sm-2">
                    <div class="form-group">
                        <label>Mã Phiếu Nợ</label>
                        <input type="text" placeholder="Mã phiếu nợ" name="keyword" id="s-code" class="form-control" value="{{app('request')->input('code')}}">
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

                <div class="col-sm-2">
                    <div class="form-group">
                        <label>Chọn trạng thái</label>
                        <select class="form-control" name="status" id="s-status">
                            <option value=""> -- Tất cả -- </option>
                            <option @if(app('request')->has('status') && app('request')->input('status') == CREDITOR_NOT_PAID) selected @endif value="{{CREDITOR_NOT_PAID}}">Chưa Trả</option>
                            <option @if(app('request')->has('status') && app('request')->input('status') == CREDITOR_PAYING) selected @endif value="{{CREDITOR_PAYING}}">Đang Trả</option>
                            <option @if(app('request')->has('status') && app('request')->input('status') == CREDITOR_PAID) selected @endif value="{{CREDITOR_PAID}}">Đang Trả</option>
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
                <div class="text-right" style="padding: 10px 10px 0px 10px;">
                    <a href="{{route('admin.creditors.view')}}" class="btn btn-sm btn-primary"><i class="fa fa-plus"></i> Ghi Nợ</a>
                </div>
                <div class="hr-line-dashed"></div>
                <!-- Account list -->
                <table class="table table-striped table-hover" id="dataTables">
                    <thead>
                    <tr>
                        <th>Nhà Cung Cấp</th>
                        <th>Mã Công Ty</th>
                        <th>Mã Phiếu Nợ</th>
                        <th>Tên Người Trả</th>
                        <th>SDT</th>
                        <th>Tổng Nợ</th>
                        <th>Đã Trả</th>
                        <th>Ngày Nợ¶•</th>
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