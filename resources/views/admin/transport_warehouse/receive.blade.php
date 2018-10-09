@extends('admin.layouts.inspinia.master')

@section('title', $title)

@section('js')
<script type="text/javascript">
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    function receiveProduct(id){
        $.ajax({
            url: "{{route('admin.transport_warehouse.receiveProduct')}}",
            data:{
                id:id,
            },
            dataType:'json'
        }).done(function(data) {
            $('#btn-'+id).hide();
            $('#detail_status_'+id).html("{{TRANSPORT_DETAIL_TEXT[TRANSPORT_DETAIL_RECEIVED]}}");
        });
    }
</script>
@endsection
@section('content')
<div class="row">
    <div class="col-lg-12">
        <div class="ibox float-e-margins pl-15 pr-15">
            <div class="row">
                <div class="col-md-8">
                    <div class="ibox-content">
                        <div class="row">
                            <div class="col-md-12">
                                <h2>Nhận hàng</h2>
                            </div>
                        </div>
                        <table class="table table-borderless">
                            <thead>
                                <tr>
                                    <th>Hình ảnh</th>
                                    <th>Tên sản phẩm</th>
                                    <th>Số lượng</th>
                                    <th>Size</th>
                                    <th>Màu</th>
                                    <th>Chuyển từ</th>
                                    <th>Chuyển đến</th>
                                    <th>Trạng thái</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tfoot></tfoot>
                            <tbody>
                                @if(isset($data->details))
                                @foreach ($data->details as $detail)
                                <tr>
                                    <td colspan="1"><img src="@if(isset($detail->product)){{asset('storage/' .$detail->product->photo)}}@endif" width="50" height="auto"></td>
                                    <td class="" colspan="1">@if(isset($detail->product)){{$detail->product->name}}@endif</td>
                                    <td class="" colspan="1">@if(isset($detail)){{$detail->quantity}}@endif</td>
                                    <td class="" colspan="1">@if(isset($detail->productDetail)){{$detail->productDetail->color->name}}@endif</td>
                                    <td class="" colspan="1">@if(isset($detail->productDetail)){{$detail->productDetail->size->name}}@endif</td>
                                    <td class="" colspan="1">@if(isset($detail->fromWarehouse)){{$detail->fromWarehouse->name}}@endif</td>
                                    <td class="" colspan="1">@if(isset($detail->receiveWarehouse)){{$detail->receiveWarehouse->name}}@endif</td>
                                    <td id="detail_status_{{$detail->id}}" class="" colspan="1">@if(isset($detail)){{TRANSPORT_DETAIL_TEXT[$detail->status]}}@endif</td>
                                    <td>
                                        @if($detail->status == 1)
                                        <button class="btn btn-primary" id="btn-{{$detail->id}}" onclick="receiveProduct({{$detail->id}})">{{TRANSPORT_DETAIL_ACTION_TEXT[$detail->status]}}</button>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="ibox-content">
                        <div class="row">
                            <div class="col-md-12">
                                <h2>Thông tin chuyển kho</h2>
                            </div>
                        </div>
                        
                        <div class="group-box">
                            <div class="row">
                                <div class="form-group">
                                    <label class="col-md-5 control-label font-bold">Tên người chuyển</label>
                                    <div class="col-md-7">
                                        <label>@if(isset($data->staff)) {{$data->staff->full_name}} @endif</label>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="form-group">
                                    <label class="col-md-5 control-label font-bold">Số điện thoại</label>
                                    <div class="col-md-7">
                                        <label>@if(isset($data->staff)) {{$data->staff->phone}} @endif</label>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="form-group">
                                    <label class="col-md-5 control-label font-bold">Email</label>
                                    <div class="col-md-7">
                                        <label>@if(isset($data->staff)) {{$data->staff->email}} @endif</label>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="form-group">
                                    <label class="col-md-5 control-label font-bold">Địa chỉ</label>
                                    <div class="col-md-7">
                                        <label>@if(isset($data->staff)) {{$data->staff->address}} @endif</label>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="form-group">
                                    <label class="col-md-5 control-label font-bold">Ngày chuyển</label>
                                    <div class="col-md-7">
                                        <label>@if(isset($data)) {{$data->transport_date}} @endif</label>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="form-group">
                                    <label class="col-md-5 control-label font-bold">Mã chuyển kho</label>
                                    <div class="col-md-7">
                                        <label>@if(isset($data)) {{$data->code}} @endif</label>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="form-group">
                                    <label class="col-md-5 control-label font-bold">Tình trạng</label>
                                    <div class="col-md-7">
                                        <label>@if(isset($data)) {{TRANSPORT_TEXT[$data->status]}} @endif</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection