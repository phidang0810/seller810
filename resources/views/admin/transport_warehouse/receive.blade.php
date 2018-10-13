@extends('admin.layouts.inspinia.master')

@section('title', $title)

@section('js')
<script type="text/javascript">
    var url_print = "{{route('admin.transport_warehouse.print')}}";

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
            if (data.all_received == "true") {
                $('button[name=action][value=save_complete]').removeAttr('disabled');
            }
        });
    }

    function print(id) {
        var transport_quantity = 0;

        var data = {
            id: id
        };

        $.ajax({
            url: url_print,
            type: 'get',
            data: data,
            dataType:'json',
            success: function(response) {
                if (response.success) {
                    resetDataPrint();
                    $('label.lbl-customer-name').text(response.transportWarehouse.staff.full_name);
                    $('label.lbl-customer-created').text(response.transportWarehouse.transport_date);
                    $('label.lbl-customer-phone').text(response.transportWarehouse.staff.phone);
                    $('label.lbl-customer-email').text(response.transportWarehouse.staff.email);
                    $('label.lbl-customer-code').text(response.transportWarehouse.code);
                    if (response.transportWarehouse.details.length > 0) {
                        $('label.lbl-customer-address').text(response.transportWarehouse.details[0].receive_warehouse.address);
                        $('table.tbl-list-product tbody').html(printTableRows(response.transportWarehouse.details));
                    }
                    var print_el = $("#print-section");
                    print_el.removeClass("hidden");
                    print_el.printThis({
                        header: null,

                    });
                } else {

                }
            }
        });
    };

    function resetDataPrint(){
        $('label.lbl-customer-name').text("");
        $('label.lbl-customer-created').text("");
        $('label.lbl-customer-phone').text("");
        $('label.lbl-customer-email').text("");
        $('label.lbl-customer-code').text("");
        $('label.lbl-customer-address').text("");
        $('table.tbl-list-product tbody').html("");
        transport_quantity = 0;
    }

    function printTableRows(details){
        html = "";
        $.each(details, function(key, detail){
            html_product_name = detail.product.name;
            html_product_code = detail.product.barcode_text;
            html_quantity = detail.quantity;
            html_color = detail.product_detail.color.name;
            html_size = detail.product_detail.size.name;
            html += '<tr><th>'+html_product_name+'</th><th>'+html_product_code+'</th><th>'+html_color+'</th><th>'+html_size+'</th><th style="text-align: right;">'+html_quantity+'</th></tr>';
            transport_quantity += parseInt(detail.quantity);
        });
        $('label.lbl-transport-quantity').text(transport_quantity);
        return html;
    }

    $('.quantity-editable').on('change', function(){
        var id = $(this).attr('data-id');
        var quantity = parseInt(removeNonDigit($(this).val()));
        var price = parseInt(removeNonDigit($('#price_'+id).text()));
        var info_total_quantity = 0;
        $.each($('.quantity-editable'), function(key, item){
            info_total_quantity += parseInt(removeNonDigit($(item).val()));
        });

        $(this).val(addCommas(quantity));
        $('#total_price_'+id).text(addCommas(quantity * price));
        $('#info-total-quantity').text(addCommas(info_total_quantity));
        $('#info-total-price').text(addCommas(info_total_quantity * price));
    });
</script>
@endsection
@section('content')
<div class="row">
    <div class="col-lg-12">
        <div class="ibox float-e-margins pl-15 pr-15">
            @include('admin._partials._alert')
            <form role="form" method="POST" id="mainForm" action="{{route('admin.transport_warehouse.received')}}"
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
                <div class="col-md-4 relative-section">
                    <div class="ibox-content">
                        <div class="row" style="margin-bottom: 30px;">
                            <div class="col-md-12">
                                <h2 class="section-title">Thông tin chuyển kho</h2>
                            </div>
                            @if(isset($data->id))<div class="right-conner"><a href="#" class="btn btn-default" onclick="print({{$data->id}});"><i class="fa fa-print" aria-hidden="true"></i> In</a></div>@endif
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

                            <div class="row">
                                <div class="col-md-12">
                                    <div class="text-right">
                                        <a href="{{route('admin.transport_warehouse.index')}}" class="btn btn-default"><i class="fa fa-arrow-circle-o-left"></i> Trở lại</a>
                                        @if($data->status == TRANSPORT_TRANSPORTING)
                                        <button name="action" class="btn btn-primary" value="save_complete" @if(!$all_received)disabled="disabled"@endif><i
                                            class="fa fa-save"></i> Nhận hàng xong
                                        </button>
                                        @endif
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
@include('admin._partials._transport_warehouse_receive')
@endsection