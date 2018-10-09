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
                responsive: true,
                searching: false,
                processing: true,
                serverSide: true,
                "dom": 'rt<"#pagination"flp>',
                ajax: {
                    "url": "{{route('admin.statistics.productQuantity')}}",
                    "data": function ( d ) {
                        d.name = $('#s-keyword').val();
                        d.category_id = $('#s-category').val();
                        d.warehouse_id = $('#s-warehouse').val()
                    },
                    complete: function(){
                    }
                },
                columns: [
                    {   // Responsive control column
                        data: null,
                        defaultContent: '',
                        className: 'control',
                        orderable: false
                    },
                    {data: 'warehouse_name'},
                    {data: 'product_code'},
                    {data: 'product_name'},
                    {data: 'category'},
                    {data: 'quantity'},
                    {data: 'quantity_available'},
                    {data: 'quantity_sell'}
                ],
                "aoColumnDefs": [
                    // Column index begins at 0
                    //{ "sClass": "text-right", "aTargets": [ 5,6 ] }
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
        <div class="row">
            <div class="col-sm-2">
                <div class="form-group">
                    <label>Tên sản phẩm</label>
                    <input type="text" placeholder="Nhập tên hoặc mã sản phẩm" name="name" id="s-keyword" class="form-control" value="{{app('request')->input('keyword')}}">
                </div>
            </div>

            <div class="col-sm-2">
                <div class="form-group">
                    <label>Chọn danh mục</label>
                    <select class="form-control" name="category" id="s-category">
                        <option value=""> -- Tất cả -- </option>
                        {!! $categoriesTree !!}
                    </select>
                </div>
            </div>


            <div class="col-sm-2">
                <div class="form-group">
                    <label>Nguồn đơn</label>
                    <select class="form-control" id="s-warehouse" name="s-warehouse">
                        <option value=""> -- Tất cả -- </option>
                        @foreach($warehouses as $item)
                            <option @if(app('request')->input('warehouse') == $item->id) selected @endif value="{{$item->id}}">{{$item->name}}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="col-sm-3">
                <div class="form-group" style="display: flex">
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
                    <a href="{{route('admin.statistics.exportProductQuantity')}}" class="btn btn-sm btn-primary"><i class="fa fa-file-excel-o" aria-hidden="true"></i> Export Excel</a>
                </div>

                <div class="hr-line-dashed"></div>
                <!-- Account list -->
                <table class="table table-striped responsive table-hover" id="dataTables">
                    <thead>
                    <tr>
                        <th></th>
                        <th>Kho Hàng</th>
                        <th>Mã SP</th>
                        <th>Tên SP</th>
                        <th>Danh Mục</th>
                        <th>Tổng SL</th>
                        <th>SL Còn Bán</th>
                        <th>SL Đã Bán</th>
                    </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>

            </div>
        </div>
</div>
@endsection