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
                    "url": "{{route('admin.customers.history', [1])}}",
                    "data": function ( d ) {
                        d.keyword = $('#s-keyword').val();
                        d.group_customer_id = $('#s-group').val();
                        d.status = $('#s-status').val();
                    },
                    complete: function(){

                    }
                },
                columns: [
                    {data: 'code'},
                    {data: 'total_price'},
                    {data: 'created_at'},
                    {data: 'status'},
                    {data: 'platform_name'}
                ],
                /*"aoColumnDefs": [
                    // Column index begins at 0
                    { "sClass": "text-center", "aTargets": [ 3 ] },
                    { "sClass": "text-right", "aTargets": [ 4 ] }
                ],*/
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
        <div class="col-md-8" style="padding-left: 0">
            <form role="form" id="fSearch">
                <div class="row v-center">
                    <div class="col-sm-3">
                        <div class="form-group">
                            <label>Tìm kiếm</label>
                            <input type="text" placeholder="Nhập email, SDT, họ tên" name="keyword" id="s-keyword" class="form-control" value="{{app('request')->input('keyword')}}">
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
    </div>
<div class="row">
    <div class="col-md-8">
        <div class="ibox">
            @include('admin._partials._alert')
            <div class="ibox-content">
                <!-- Account list -->
                <table class="table table-striped table-hover" id="dataTables">
                    <thead>
                    <tr>
                        <th>Mã Đơn Hàng</th>
                        <th>Tổng Thành Tiền</th>
                        <th>Ngày Mua</th>
                        <th>Tình Trạng</th>
                        <th>Nguồn Đơn</th>
                    </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>

            </div>
        </div>
    </div>
    <div class="col-md-4" style="padding-left: 0">
        <div class="ibox">
            <div class="ibox-content">
                <h3>THÔNG TIN KHÁCH HÀNG</h3>
                <div class="row m-b">
                    <div class="form-group">
                        <label class="col-md-4 control-label">Tên Khách Hàng:</label>
                        <div class="col-md-8"><b>{{$customer->name}}</b></div>
                    </div>
                </div>
                <div class="row m-b">
                    <div class="form-group">
                        <label class="col-md-4 control-label">Số Điện Thoại:</label>
                        <div class="col-md-8"><b>{{$customer->phone}}</b></div>
                    </div>
                </div>
                <div class="row m-b">
                    <div class="form-group">
                        <label class="col-md-4 control-label">Email:</label>
                        <div class="col-md-8"><b>{{$customer->email}}</b></div>
                    </div>
                </div>
                <div class="row m-b">
                    <div class="form-group">
                        <label class="col-md-4 control-label">Địa Chỉ:</label>
                        <div class="col-md-8"><b>{{$customer->address}}</b></div>
                    </div>
                </div>
                <div class="row m-b">
                    <div class="form-group">
                        <label class="col-md-4 control-label">Thành Phố:</label>
                        <div class="col-md-8"><b>{{$customer->name}}</b></div>
                    </div>
                </div>
                <div class="hr-line-dashed"></div>
                <h3>THÔNG TIN KHÁC</h3>
                <div class="row m-b">
                    <div class="form-group">
                        <label class="col-md-4 control-label">Nhóm:</label>
                        <div class="col-md-8"><b>{{$customer->group->name}}</b></div>
                    </div>
                </div>
                <div class="row m-b">
                    <div class="form-group">
                        <label class="col-md-4 control-label">Chiết Khấu:</label>
                        <div class="col-md-8"><b>{{format_price($customer->group->discount_amount)}}</b></div>
                    </div>
                </div>
                <div class="row m-b">
                    <div class="form-group">
                        <label class="col-md-4 control-label">Nợ:</label>
                        <div class="col-md-8"><b>{{$customer->group->name}}</b></div>
                    </div>
                </div>
                <div class="row m-b">
                    <div class="form-group">
                        <label class="col-md-4 control-label">Ghi Chú:</label>
                        <div class="col-md-8"><b>{{$customer->description}}</b></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection