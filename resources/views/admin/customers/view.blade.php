@extends('admin.layouts.inspinia.master')

@section('title', $title)

@section('js')
<script></script>
<!-- Page-Level Scripts -->
<script>
    var url_pay = "{{route('admin.customers.pay')}}";
    var table;
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    function readURL(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();

            reader.onload = function(e) {
                $('#preview').attr('src', e.target.result);
            };

            reader.readAsDataURL(input.files[0]);
        }
    }

    $(document).ready(function() {
        table = $('#dataTables').dataTable({
            responsive: true,searching: false,
            processing: true,
            serverSide: true,
            "dom": 'rt<"#pagination"flp>',
            ajax: {
                "url": "{{route('admin.customers.dept')}}",
                "data": function ( d ) {
                    d.customer_id = $('input[name="id"]').val();
                },
                complete: function(){
                }
            },
            columns: [
            {data: 'id'},
            {data: 'code'},
            {data: 'quantity'},
            {data: 'status'},
            {data: 'payment_status'},
            {data: 'created_at'},
            {data: 'needed_paid'},
            {data: 'pay'},
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



        $("#dataTables").on("click", '.bt-pay', function(){
            var name = $(this).attr('data-name');
            var id = $(this).attr('data-id');
            var pay_amount = $('input[name="pay-'+id+'"]').val();
            var data = {
                id: id,
                pay_amount: pay_amount
            };
            swal({
                title: "Cảnh Báo!",
                text: "Bạn có chắc muốn thanh toán " + pay_amount + " đơn hàng " + name + " </b> ?",
                html:true,
                type: "warning",
                showCancelButton: true,
                confirmButtonClass: "btn-danger",
                confirmButtonText: "Vâng, thanh toán!",
                cancelButtonText: "Không",
                closeOnConfirm: false
            },
            function(){
                $.ajax({
                    url: url_pay,
                    type: 'PUT',
                    data: data,
                    dataType:'json',
                    success: function(response) {
                        if (response.success) {
                            swal({
                                title: "Thành công!",
                                text: "Đơn hàng " + name + " đã ghi nhận thanh toán.",
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

        $("#bt-reset").click(function(){
            $("#mainForm")[0].reset();
        });

        $.validator.addMethod("valueNotEquals", function(value, element, arg){
            return arg !== value;
        }, "Value must not equal arg.");

        $("#mainForm").validate({
            rules: {
                code:{
                    maxlength:20
                },
                group_customer_id: { valueNotEquals: "0" }
            },
            messages: {
                group_customer_id: { valueNotEquals: "Vui lòng chọn!" }
            }
        });

        new Cleave('.input-phone', {
            phone: true,
            phoneRegionCode: 'VN'
        });

    });
</script>
@endsection
@section('content')
<div class="row">
    <div class="col-lg-12">
        <div class="ibox float-e-margins pl-15 pr-15">
            @include('admin._partials._alert')
            <form role="form" method="POST" id="mainForm" action="{{route('admin.customers.store')}}" enctype="multipart/form-data">
                {{ csrf_field() }}
                @if (isset($data->id))
                <input type="hidden" name="id" value="{{$data->id}}" />
                @endif
                <div class="ibox-content" style="padding: 20px;">
                    <div class="row m-b">
                        <div class="form-group">
                            <label class="col-md-2 control-label">Nhóm Khách Hàng (<span class="text-danger">*</span>)</label>
                            <div class="col-md-5">
                                <select class="form-control" name="group_customer_id">
                                    <option value="0">-- Chọn nhóm khách hàng --</option>
                                    @foreach($groups as $group)
                                    <option value="{{$group->id}}" @if(isset($data->group_customer_id) && $data->group_customer_id === $group->id) selected @endif>{{$group->name}}</option>
                                    @endforeach
                                </select>
                            </div>
                            @if(isset($data))
                            <div class="col-md-2 text-right"><a class="btn btn-warning" href="{{route('admin.customers.history', [$data->id])}}"><i class="fa fa-history" aria-hidden="true"></i> Xem Lịch Sử</a></div>
                            @endif
                        </div>
                    </div>

                    <div class="row m-b">
                        <div class="form-group">
                            <label class="col-md-2 control-label">Họ Tên (<span class="text-danger">*</span>)</label>
                            <div class="col-md-5">
                                <input type="text" name="name" placeholder="Nhập tên của bạn" class="form-control required" value="@if(isset($data->name)){{$data->name}}@else{{old('name')}}@endif"/>
                            </div>
                        </div>
                    </div>

                    <div class="row m-b">
                        <div class="form-group">
                            <label class="col-md-2 control-label">Email (<span class="text-danger">*</span>)</label>
                            <div class="col-md-5">
                                <input type="text" name="email" placeholder="Nhập email của bạn" class="form-control required email" value="@if(isset($data->email)){{$data->email}}@else{{old('email')}}@endif"/>
                            </div>
                        </div>
                    </div>

                    @if(isset($data))
                    <div class="row m-b">
                        <div class="form-group">
                            <label class="col-md-2 control-label">Mã Khách Hàng (<span class="text-danger">*</span>)</label>
                            <div class="col-md-5">
                                <input type="text" readonly name="code" class="form-control required" value="@if(isset($data->code)){{$data->code}}@else{{old('code')}}@endif"/>
                            </div>
                        </div>
                    </div>
                    @endif

                    <div class="row m-b">
                        <div class="form-group">
                            <label class="col-md-2 control-label">Điện Thoại (<span class="text-danger">*</span>)</label>
                            <div class="col-md-5">
                                <input type="text" name="phone" placeholder="VD: 090 934 128" class="form-control input-phone required" value="@if(isset($data->phone)){{$data->phone}}@else{{old('phone')}}@endif"/>
                            </div>
                        </div>
                    </div>

                    <div class="row m-b">
                        <div class="form-group">
                            <label class="col-md-2 control-label">Địa Chỉ</label>
                            <div class="col-md-5">
                                <input type="text" name="address" placeholder="VD: 5 Lữ Gia, phường 15, quận 11" class="form-control" value="@if(isset($data->address)){{$data->address}}@else{{old('address')}}@endif"/>
                            </div>
                        </div>
                    </div>

                    <div class="row m-b">
                        <div class="form-group">
                            <label class="col-md-2 control-label">Ghi Chú</label>
                            <div class="col-md-5">
                                <textarea class="form-control" name="description" cols="10" rows="5">@if(isset($data->description)){{$data->description}}@else{{old('description')}}@endif</textarea>
                            </div>
                        </div>
                    </div>

                    <div class="row m-b">
                        <div class="form-group">
                            <label class="col-md-2 control-label">Trạng Thái</label>
                            <div class="col-md-3">
                                <select class="form-control" name="active">
                                    <option @if(isset($data->active) && $data->active === ACTIVE || old('active') === ACTIVE) selected @endif value="{{ACTIVE}}">Đã kích hoạt</option>
                                    <option @if(isset($data->active) && $data->active === INACTIVE || old('active') === INACTIVE) selected @endif value="{{INACTIVE}}">Chưa kích hoạt</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    @if (isset($data->id))
                    <table class="table table-striped table-hover" id="dataTables">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Mã</th>
                                <th>Số lượng</th>
                                <th>Tình trạng đơn hàng</th>
                                <th>Tình trạng thanh toán</th>
                                <th>Ngày tạo đơn hàng</th>
                                <th>Chưa thanh toán</th>
                                <th>Thanh toán</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                    @endif

                    <div class="text-right">
                        <a href="{{route('admin.customers.index')}}" class="btn btn-default"><i class="fa fa-arrow-circle-o-left"></i> Trở lại</a>
                        <button type="button" class="btn btn-default" id="bt-reset"><i class="fa fa-refresh"></i> Làm mới</button>
                        <button type="submit" name="action" class="btn btn-primary" value="save"><i class="fa fa-save"></i> Lưu</button>
                        <button type="submit" name="action" class="btn btn-warning" value="save_quit"><i class="fa fa-save"></i> Lưu &amp; Thoát</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection