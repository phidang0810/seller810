@extends('admin.layouts.inspinia.master')

@section('title', $title)

@section('js')
<script type="text/javascript">
    var url_print = "{{route('admin.import_products.print')}}";

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    function addCommas(nStr)
    {
        nStr += '';
        x = nStr.split('.');
        x1 = x[0];
        x2 = x.length > 1 ? '.' + x[1] : '';
        var rgx = /(\d+)(\d{3})/;
        while (rgx.test(x1)) {
            x1 = x1.replace(rgx, '$1' + ',' + '$2');
        }
        return x1 + x2;
    }

    // Function remove all character non-digit
    function removeNonDigit(str){
        return str.replace(/\D/g,'');
    }

    function print(id) {
        var data = {
            id: id
        };

        var import_quantity = 0;
        var import_total_price = 0;

        $.ajax({
            url: url_print,
            type: 'get',
            data: data,
            dataType:'json',
            success: function(response) {
                if (response.success) {
                    resetDataPrint();
                    $('label.lbl-customer-name').text(response.import_product.staff.full_name);
                    $('label.lbl-customer-created').text(response.import_product.created_at);
                    $('label.lbl-customer-phone').text(response.import_product.staff.phone);
                    $('label.lbl-customer-email').text(response.import_product.staff.email);
                    $('label.lbl-customer-code').text(response.import_product.code);
                    if(response.import_product.supplier){$('label.lbl-customer-address').text(response.import_product.supplier.name);}
                    if (response.import_product.details.length > 0) {
                        $('table.tbl-list-product tbody').html(printTableRows(response.import_product));
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

    function printTableRows(import_product){
        html = "";
        $.each(import_product.details, function(key, detail){
            html_product_name = import_product.name;
            html_product_code = import_product.barcode_text;
            html_quantity = addCommas(detail.quantity);
            html_color = detail.color.name;
            html_size = detail.size.name;
            html_price = addCommas(import_product.price);
            html_total_price = addCommas(parseInt(import_product.price)*parseInt(detail.quantity));
            html += '<tr><th>'+html_product_name+'</th><th>'+html_product_code+'</th><th style="text-align: right;">'+html_quantity+'</th><th>'+html_color+'</th><th>'+html_size+'</th><th style="text-align: right;">'+html_price+'</th><th style="text-align: right;">'+html_total_price+'</th></tr>';
            import_quantity += parseInt(detail.quantity);
            import_total_price += parseInt(import_product.price)*parseInt(detail.quantity);
        });
        $('label.lbl-transport-total-quantity').text(addCommas(import_quantity));
        $('label.lbl-transport-total-price').text(addCommas(import_total_price));
        $('h4.lbl-transport-total').text(addCommas(import_total_price));
        return html;
    }
    function resetDataPrint(){
        $('label.lbl-customer-name').text("");
        $('label.lbl-customer-created').text("");
        $('label.lbl-customer-phone').text("");
        $('label.lbl-customer-email').text("");
        $('label.lbl-customer-code').text("");
        $('label.lbl-customer-address').text("");
        $('table.tbl-list-product tbody').html("");
        import_quantity = 0;
        import_total_price = 0;
    }

    function performDetail(id){
        $('#btn-'+id).hide();
        $.ajax({
            url: "{{route('admin.import_products.confirm')}}",
            data:{
                id:id,
                quantity:$('input[name=quantity-'+id+']').val()
            },
            dataType:'json'
        }).done(function(data) {
            if (data.all_confirmed == "true") {
                $('button[name=action][value=save_complete]').removeAttr('disabled');
            }
        }).fail(function() {
            alert( "error" );
            $('#btn-'+id).show();
        });
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

    $(document).ready(function(){
        $.each($('.number-input'), function(key, item){
            $(item).val(addCommas($(item).val()));
        });

        $.each($('.number-label'), function(key, item){
            $(item).text(addCommas($(item).text()));
        });
    });
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
                                    <td class="text-right" colspan="1">
                                        <input type="text" name="quantity-{{$detail->id}}" class="quantity-editable number-input" value="{{$detail->quantity}}" data-id="{{$detail->id}}">
                                    </td>
                                    <td class="text-right number-label" colspan="1" id="price_{{$detail->id}}">{{$data->price}}</td>
                                    <td class="text-right number-label" colspan="1" id="total_price_{{$detail->id}}">{{$data->price * $detail->quantity}}</td>
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
                <div class="col-md-4 relative-section">
                    <div class="ibox-content">
                        <div class="row" style="margin-bottom: 30px;">
                            <div class="col-md-12">
                                <h2 class="section-title">Thông tin đơn hàng nhập</h2>
                            </div>
                            @if(isset($data->id))<div class="right-conner"><a href="#" class="btn btn-default" onclick="print({{$data->id}});"><i class="fa fa-print" aria-hidden="true"></i> In</a></div>@endif
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
                                    <label class="col-md-5 control-label font-bold">Mã nhập hàng</label>
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
                                        <label id="info-total-quantity" class="number-label">@if(isset($data)) {{$data->quantity}} @endif</label>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="form-group">
                                    <label class="col-md-5 control-label font-bold">Đơn giá</label>
                                    <div class="col-md-7">
                                        <label class="number-label">@if(isset($data)) {{$data->price}} @endif</label>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="form-group">
                                    <label class="col-md-5 control-label font-bold">Tổng cộng</label>
                                    <div class="col-md-7">
                                        <label id="info-total-price" class="number-label">@if(isset($data)) {{$data->total_price}} @endif</label>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-12">
                                    <div class="text-right">
                                        <a href="{{route('admin.import_products.receive')}}" class="btn btn-default"><i class="fa fa-arrow-circle-o-left"></i> Trở lại</a>
                                        @if($data->status == IMPORT_IMPORTED)
                                        <button name="action" class="btn btn-primary" value="save_complete" @if(!$all_confirmed)disabled="disabled"@endif> Kiểm hàng xong
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
@include('admin._partials._import_warehouse_receive')
@endsection