@extends('admin.layouts.inspinia.master')

@section('title', $title)

@section('js')
<script type="text/javascript">
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    function performDetail(id){
        $.ajax({
            url: "{{route('admin.import_products.confirm')}}",
            data:{
                id:id,
                quantity:$('input[name=quantity-'+id+']').val()
            },
            dataType:'json'
        }).done(function(data) {
            $('#btn-'+id).hide()
            if (data.all_confirmed == "true") {
                $('button[name=action][value=save_complete]').removeAttr('disabled');
            }
        })
    }
</script>
@endsection
@section('content')
<div class="row">
    <div class="col-lg-12">
        <div class="ibox float-e-margins pl-15 pr-15">
            @include('admin._partials._alert')
            <form role="form" method="POST" id="mainForm" action="{{route('admin.import_products.check_completed')}}"
            enctype="multipart/form-data">
            {{ csrf_field() }}
            @if (isset($data->id))
            <input type="hidden" name="id" value="{{$data->id}}"/>
            @endif
            <div class="row">
                <div class="col-md-8">
                    <div class="ibox-content">
                        <div class="row">
                            <div class="col-md-12">
                                <h2>Kiểm hàng</h2>
                            </div>
                        </div>
                        <table class="table table-borderless">
                            <thead>
                                <tr>
                                    <th>Hình ảnh</th>
                                    <th>Tên sản phẩm</th>
                                    <th>Mã sản phẩm</th>
                                    <th>Màu sắc</th>
                                    <th>Kích thước</th>
                                    <th>Số lượng</th>
                                    <th>Đơn giá</th>
                                    <th>Thành tiền</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tfoot></tfoot>
                            <tbody>
                                @if(isset($data->details))
                                @foreach ($data->details as $detail)
                                <tr>
                                    <td colspan="1"><img src="@if(isset($data->photo) && $data->photo != ''){{asset('storage/' .$data->photo)}}@endif" width="50" height="auto"></td>
                                    <td class="" colspan="1">@if(isset($data)){{$data->name}}@endif</td>
                                    <td class="" colspan="1">@if(isset($data)){{$data->barcode_text}}@endif</td>
                                    <td class="" colspan="1">@if(isset($detail->color)){{$detail->color->name}}@endif</td>
                                    <td class="" colspan="1">@if(isset($detail->size)){{$detail->size->name}}@endif</td>
                                    <td class="thousand-number text-right" colspan="1">
                                        <input type="text" name="quantity-{{$detail->id}}" value="{{$detail->quantity}}">
                                    </td>
                                    <td class="thousand-number money text-right" colspan="1">{{$data->price}}</td>
                                    <td class="thousand-number money text-right" colspan="1">{{$data->price * $detail->quantity}}</td>
                                    <td>
                                        @if($detail->status == IMPORT_DETAIL_UNCONFIMRED)
                                        <a href="#" class="btn btn-primary" id="btn-{{$detail->id}}" onclick="performDetail({{$detail->id}})">{{IMPORT_DETAIL_ACTION_TEXT[$detail->status]}}</a>
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
                                <h2>Thông tin đơn hàng nhập</h2>
                            </div>
                        </div>
                        
                        <div class="group-box">
                            <div class="row">
                                <div class="form-group">
                                    <label class="col-md-5 control-label font-bold">Tên người nhập</label>
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
                                    <label class="col-md-5 control-label font-bold">Ngày nhập</label>
                                    <div class="col-md-7">
                                        <label>@if(isset($data)) {{$data->created_at}} @endif</label>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="form-group">
                                    <label class="col-md-5 control-label font-bold">Mã đơn hàng</label>
                                    <div class="col-md-7">
                                        <label>@if(isset($data)) {{$data->code}} @endif</label>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="form-group">
                                    <label class="col-md-5 control-label font-bold">Nhà cung cấp</label>
                                    <div class="col-md-7">
                                        <label>@if(isset($data->supplier)) {{$data->supplier->name}} @endif</label>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="form-group">
                                    <label class="col-md-5 control-label font-bold">Tình trạng</label>
                                    <div class="col-md-7">
                                        <label>@if(isset($data)) {{IMPORT_TEXT[$data->status]}} @endif</label>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="form-group">
                                    <label class="col-md-5 control-label font-bold">Ghi chú</label>
                                    <div class="col-md-7">
                                        <label>@if(isset($data)) {{$data->note}} @endif</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="group-box">
                            <div class="row">
                                <div class="form-group">
                                    <label class="col-md-5 control-label font-bold">Số lượng</label>
                                    <div class="col-md-7">
                                        <label>@if(isset($data)) {{$data->quantity}} @endif</label>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="form-group">
                                    <label class="col-md-5 control-label font-bold">Thành tiền</label>
                                    <div class="col-md-7">
                                        <label>@if(isset($data)) {{$data->price}} @endif</label>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="form-group">
                                    <label class="col-md-5 control-label font-bold">Tổng cộng</label>
                                    <div class="col-md-7">
                                        <label>@if(isset($data)) {{$data->total_price}} @endif</label>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-12">
                                    <div class="text-right">
                                        <a href="{{route('admin.import_products.receive')}}" class="btn btn-default"><i class="fa fa-arrow-circle-o-left"></i> Trở lại</a>
                                        <button name="action" class="btn btn-primary" value="save_complete" @if(!$all_confirmed)disabled="disabled"@endif><i
                                            class="fa fa-save"></i> Kiểm hàng xong
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
</div>
@endsection