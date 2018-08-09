@extends('admin.layouts.inspinia.master')

@section('title', $title)

@section('css')
<link href="{{asset('themes/inspinia/css/plugins/select2/select2.min.css')}}" rel="stylesheet">
<style type="text/css">
@media (min-width: 768px){
    #page-wrapper {
        margin: 0 0 0 0px;
    }
}
</style>
@endsection

@section('js')
<script src="{{asset('themes/inspinia/js/plugins/select2/select2.full.min.js')}}"></script>
<!-- Page-Level Scripts -->
<script>
    var cart_details = ($('input[name="cart_details"]').val()) ? jQuery.parseJSON($('input[name="cart_details"]').val()) : [];
    var default_image = '{{asset(NO_PHOTO)}}';

    // Function to add row with form edit/create to table details
    function htmlEditCreateRowProductDetail(data, key){
        // Image
        var html_detail_photo = '<span class="img-wrapper"><img class="img-thumbnail" style="width: 80px; height: 60px;" src="' + data.product_image + '" title="' + data.product_name.name + '"/></span>';
        // Name
        var html_detail_label_name = '<label>' + data.product_name.name + '</label>';
        // Quantity
        var html_detail_input_quantity = '<input type="number" min="0" value="'+data['product_quantity']+'" id="detail_quantity_'+key+'" class="detail_quantity form-control" >';
        // Code
        var html_detail_label_code = '<label>' + data.product_code + '</label>';
        // Size
        var html_detail_label_size = '<label>' + data.product_size.name + '</label>';
        // Color
        var html_detail_label_color = '<label>' + data.product_color.name + '</label>';
        // Price
        var html_detail_label_price = '<label>' + data.product_price + '</label>';
        // Editable price
        var html_detail_input_price = '<input type="number" min="0" value="'+data['product_editable_price']+'" id="detail_editable_quantity_'+key+'" class="detail_editable_quantity form-control" >';
        // Count price
        var html_detail_input_count_price = '<input type="number" value="' + data.total_price + '" id="detail_count_price_'+key+'" class="detail_count_price form-control" readonly="readonly">';

        // Row html
        var html = '<td>'+html_detail_photo+'</td>\
        <td>'+html_detail_label_name+'</td>\
        <td class="c-quantity">'+html_detail_input_quantity+'</td>\
        <td>'+html_detail_label_code+'</td>\
        <td>'+html_detail_label_size+'</td>\
        <td>'+html_detail_label_color+'</td>\
        <td>'+html_detail_label_price+'</td>\
        <td>'+html_detail_input_count_price+'</td>\
        <td><a href="javascript:;" onclick="deleteCartDetailItem('+key+');" class="bt-delete btn btn-xs btn-danger"><i class="fa fa-trash-o" aria-hidden="true"></i></a></td>';
        return html;
    }

    // When "Xóa" on row is clicked -> Remove current row, add delete:true to item on array details
    function deleteCartDetailItem(key) {
        cart_details[key].delete = true;
        $('#cart_detail_'+key).remove();
        $('input[name="cart_details"]').val(JSON.stringify(cart_details));
        updateCartTotalInfo();
    }

    // Function to generate color options
    function generate_options(data){
        var html = '';
        $.each(data, function(key, value){
            html += '<option value='+value.id+'>'+value.name+'</option>';
        });

        return html;
    }

    // Function update cart details data
    function updateCartDetailsData(){
        $.each(cart_details, function(key, value){
            if (value.delete != true) {
                cart_details[key].product_quantity = parseInt($('#detail_quantity_'+key).val());
                cart_details[key].total_price = cart_details[key].product_quantity * cart_details[key].product_price;
                $('#detail_count_price_'+key).val(cart_details[key].total_price);
            }
        });
        updateCartTotalInfo();
    }

    // Function update cart total info
    function updateCartTotalInfo(){
        total_price = 0;
        total_quantity = 0;
        $.each(cart_details, function(key, value){
            if (value.delete != true) {
                total_price += value.total_price;
                total_quantity += value.product_quantity;
            }
        });
        $('.cart-total-info input[name="total_price"]').val(total_price);
        $('.cart-total-info input[name="quantity"]').val(total_quantity);
        vat_amount = parseInt($('.cart-total-info input[name="total_price"]').val()) * parseInt($('input[name="vat_percent"]').val()) / 100;
        $('.cart-total-info input[name="vat_amount"]').val(vat_amount);

        // Count price
        shipping_fee = ($('.cart-total-info input[name="shipping_fee"]').val()) ? parseInt($('.cart-total-info input[name="shipping_fee"]').val()) : 0;
        total_discount_amount = ($('.cart-total-info input[name="total_discount_amount"]').val()) ? parseInt($('.cart-total-info input[name="total_discount_amount"]').val()) : 0;
        prepaid_amount = ($('.cart-total-info input[name="prepaid_amount"]').val()) ? parseInt($('.cart-total-info input[name="prepaid_amount"]').val()) : 0;
        price = total_price + vat_amount + shipping_fee - total_discount_amount;
        $('.cart-total-info input[name="price"]').val(price);

        $('.cart-total-info input[name="needed_paid"]').val(price - prepaid_amount);
        $('input[name="cart_details"]').val(JSON.stringify(cart_details));
    }

    // When detail quantity, color, size change will count total again
    $(document.body).delegate('.detail_quantity', 'change', function() {
        updateCartDetailsData();
    });

    // Function update total amount
    function updateTotalDiscountAmount(){
        customer_discount = (parseInt($('input[name="customer_discount_amount"]').val())) ? parseInt($('input[name="customer_discount_amount"]').val()) : 0;
        partner_discount = (parseInt($('input[name="partner_discount_amount"]').val())) ? parseInt($('input[name="partner_discount_amount"]').val()) : 0;
        $('.cart-total-info input[name="total_discount_amount"]').val(customer_discount + partner_discount);
        updateCartTotalInfo();
    }

    // First time show details to table
    function printTableCartDetails(){
        $.each(cart_details, function(key, value){
            var html = htmlEditCreateRowProductDetail(value, key);

            $('#i-cart-info tbody').append('<tr class="child" id="cart_detail_'+key+'">'+html+'</tr>');
        });
    }

    $(document).ready(function() {
        //---> Show menu on horizontal bar
        var url_create = "{{route('admin.carts.create')}}";
        var url_detail = "{{route('admin.carts.view')}}";
        // console.log(url_detail+"  ---  "+location.href);
        if (location.href == url_detail || location.href == url_create) {
            // console.log(url_detail+"  ---  "+location.href);
            $(".cart-menu-wrapper").show();
            $(".cart-menu-wrapper .cart-detail").addClass("active");
        }

        printTableCartDetails();

        $("#bt-reset").click(function(){
            $("#mainForm")[0].reset();
        })

        $("#mainForm").validate();

        // Init select2
        $('select[name="product_name"]').select2();
        $('select[name="customer_phone"').select2({
            tags:true
        });
        $('select[name="partner"]').select2();

        // Load data for color options when product select is changed
        $('select[name="product_name"]').on('change', function(){
            //---> validatae product
            validateProductInfo($(this).val(), "name");
            $.ajax({
                url: "{{route('admin.carts.view')}}",
                data:{
                    product_id:$(this).val()
                },
                dataType:'json'
            }).done(function(data) {
                if (!$.isEmptyObject(data.colors)) {
                    html_color_options = '<option value="0"> -- Chọn màu sắc -- </option>' + generate_options(data.colors);
                    $('select[name="product_color"]').html(html_color_options);
                }else{
                }
            }).fail(function(jqXHR, textStatus){
                alert('Có lỗi xảy ra, xin hãy làm mới trình duyệt');
            })
        });

        // Load data for size options when color select is changed
        $('select[name="product_color"]').on('change', function(){
            //---> validatae color
            validateProductInfo($(this).val(), "color");

            $('select[name="product_size"]').html('<option value="0"> -- Chọn kích thước -- </option>');
            $.ajax({
                url: "{{route('admin.carts.view')}}",
                data:{
                    product_id:$('select[name="product_name"]').val(),
                    color_id:$(this).val()
                },
                dataType:'json'
            }).done(function(data) {
                if (!$.isEmptyObject(data.sizes)) {
                    html_sizes_options = '<option value="0"> -- Chọn kích thước -- </option>' + generate_options(data.sizes);
                    $('select[name="product_size"]').html(html_sizes_options);
                }else{
                }
            }).fail(function(jqXHR, textStatus){
                alert('Có lỗi xảy ra, xin hãy làm mới trình duyệt');
            })
        });

        // Load data for quantity product when size select is changed
        $('select[name="product_size"]').on('change', function(){
            //---> validatae color
            validateProductInfo($(this).val(), "size");

            $.ajax({
                url: "{{route('admin.carts.view')}}",
                data:{
                    product_id:$('select[name="product_name"]').val(),
                    color_id:$('select[name="product_color"]').val(),
                    size_id:$(this).val()
                },
                dataType:'json'
            }).done(function(data) {
                if (!$.isEmptyObject(data)) {
                    $('#add_cart_details').prop('disabled', false);
                    $('input[name="product_quantity"]').attr('max', data.quantity);
                }else{
                }
            }).fail(function(jqXHR, textStatus){
                alert('Có lỗi xảy ra, xin hãy làm mới trình duyệt');
            })
        });

        function validateProductInfo(elSelectVal, objectName){
            var status = true;
            if (elSelectVal == 0) {
                console.log("#product-"+objectName+"-error");
                if ($("#product-"+objectName+"-error").hasClass("hidden")) {
                    console.log("remove hidden");
                    $("#product-"+objectName+"-error").removeClass("hidden");
                    $("#product-"+objectName+"-error").css("display","inline-block!important");
                }
                status = false;
            }else{
                if (!$("#product-"+objectName+"-error").hasClass("hidden")) {
                    $("#product-"+objectName+"-error").addClass("hidden");
                    $("#product-"+objectName+"-error").css("display","none!important");
                }

                $('#add_cart_details').removeAttr('disabled');
                status = true;
            }
            return status;
        }

        // When "Thêm" button is clicked -> Add new item to array cart_details, append new row to table
        $('#add_cart_details').click(function(){
            if (validateProductInfo($("select[name=product_name]").val(), "name") && validateProductInfo($("select[name=product_color]").val(), "color") && validateProductInfo($("select[name=product_size]").val(), "size")) {
                // $('#add_cart_details').removeAttr('disabled');
                $('button[value="save"]').removeAttr('disabled');
                $('button[value="save_quit"]').removeAttr('disabled');

                var path_img_folder = window.location.origin + '/storage/';
                $.ajax({
                    url: "{{route('admin.carts.view')}}",
                    data:{
                        product_id:$('select[name="product_name"]').val(),
                        color_id:$('select[name="product_color"]').val(),
                        size_id:$('select[name="product_size"]').val(),
                        get_data:true
                    },
                    dataType:'json'
                }).done(function(data) {
                    if (!$.isEmptyObject(data)) {
                        // $('#add_cart_details').prop('disabled', true);
                        cart_details.push({
                            'product_image':(data.product.photo) ? path_img_folder + data.product.photo : default_image,
                            'product_code':data.product.code,
                            'product_price':data.product.sell_price,
                            'product_editable_price':data.product.sell_price,
                            'product_name':{id:$('select[name="product_name"]').val(), name:$('select[name="product_name"] option[value="'+$('select[name="product_name"]').val()+'"]').text()},
                            'product_quantity':parseInt($('input[name="product_quantity"]').val()),
                            'product_size':{id:$('select[name="product_size"]').val(), name:$('select[name="product_size"] option[value="'+$('select[name="product_size"]').val()+'"]').text()},
                            'product_color':{id:$('select[name="product_color"]').val(), name:$('select[name="product_color"] option[value="'+$('select[name="product_color"]').val()+'"]').text()},
                            'total_price':data.product.sell_price*$('input[name="product_quantity"]').val(),
                            'product_detail':data.product_detail
                        });

                        var key = cart_details.length-1;
                        var html = htmlEditCreateRowProductDetail(cart_details[key], key);

                        $('#i-cart-info tbody').append('<tr class="child" id="cart_detail_'+key+'">'+html+'</tr>');
                        updateCartTotalInfo();
                    }else{
                    }
                }).fail(function(jqXHR, textStatus){
                    alert('Có lỗi xảy ra, xin hãy làm mới trình duyệt');
                });
            }else{
                $('#add_cart_details').prop('disabled', true);
                $('button[value="save"]').prop('disabled', true);
                $('button[value="save_quit"]').prop('disabled', true);
                console.log("Vui lòng nhập đủ thông tin");
            }
            
        });

        // Load customer data when customer phone select is changed
        $('select[name="customer_phone"]').on('change', function(){
            if ($(this).find('option[value="'+$(this).val()+'"]').attr('data-select2-tag') != 'true') {
                $.ajax({
                    url: "{{route('admin.carts.view')}}",
                    data:{
                        customer_phone:$(this).val()
                    },
                    dataType:'json'
                }).done(function(data) {
                    if (!$.isEmptyObject(data)) {
                        $('input[name="customer_name"]').val(data.customer.name);
                        $('input[name="customer_email"]').val(data.customer.email);
                        $('input[name="customer_address"]').val(data.customer.address);
                        if (!$.isEmptyObject(data.customer.city)) {
                            $('select[name="customer_city"]').val(data.customer.city.id);
                        }
                        if (!$.isEmptyObject(data.customer.group)) {
                            $('input[name="customer_discount_amount"]').val(data.customer.group.discount_amount);
                        }
                        updateTotalDiscountAmount();
                    }else{
                    }
                }).fail(function(jqXHR, textStatus){
                    alert('Có lỗi xảy ra, xin hãy làm mới trình duyệt');
                })
            }
        });

        // Load discount amount for partner
        $('select[name="partner"]').on('change', function(){
            $.ajax({
                url: "{{route('admin.carts.view')}}",
                data:{
                    partner_id:$(this).val()
                },
                dataType:'json'
            }).done(function(data) {
                if (!$.isEmptyObject(data)) {
                    $('input[name="partner_discount_amount"]').val(data.partner.discount_amount);
                    updateTotalDiscountAmount();
                }else{
                }
            }).fail(function(jqXHR, textStatus){
                alert('Có lỗi xảy ra, xin hãy làm mới trình duyệt');
            })
        });

        // Update value for cart total info prepaid field when prepaid field update
        $('input[name="prepaid_amount"]').on('change', function(){
            $('.cart-total-info input[name="prepaid_amount"]').val($(this).val());
            updateCartTotalInfo();
        });

        // Update value for cart total info shipping fee field when shipping fee field update
        $('input[name="shipping_fee"]').on('change', function(){
            $('.cart-total-info input[name="shipping_fee"]').val($(this).val());
            updateCartTotalInfo();
        });
    });
</script>
@endsection
@section('content')
<div class="row">
    <div class="col-lg-12">
        <div class="ibox float-e-margins pl-15 pr-15">
            @include('admin._partials._alert')
            <form role="form" method="POST" id="mainForm" action="{{route('admin.carts.store')}}">
                {{ csrf_field() }}
                @if (isset($data->id))
                <input type="hidden" name="id" value="{{$data->id}}" />
                @endif
                <input type="hidden" name="cart_details" value="@if(isset($cart_details)){{$cart_details}}@endif"/>
                <div class="row">
                    <div class="col-md-8">
                        <div class="ibox-content">

                            <div class="row">
                                <div class="col-md-12">
                                    <h2>{{ $title }}</h2>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-4">
                                    <select name="product_name" class="form-control">
                                        <option value="0"> -- Chọn sản phẩm -- </option>
                                        {!! $product_options !!}
                                    </select>
                                    <label id="product-name-error" class="error hidden" for="product_name1">Vui lòng chọn.</label>
                                </div>
                                <div class="col-md-2">
                                    <select name="product_color" class="form-control">
                                        <option value="0"> -- Chọn màu sắc -- </option>
                                    </select>
                                    <label id="product-color-error" class="error hidden" for="product_color1">Vui lòng chọn.</label>
                                </div>
                                <div class="col-md-2">
                                    <select name="product_size" class="form-control">
                                        <option value="0"> -- Chọn kích thước -- </option>
                                    </select>
                                    <label id="product-size-error" class="error hidden" for="product_size1">Vui lòng chọn.</label>
                                </div>
                                <div class="col-md-2">
                                    <input name="product_quantity" type="text" placeholder="Nhập số lượng" class="form-control m-b"
                                    value=""/>
                                </div>
                                <div class="col-md-2">
                                    <button type="button" class="btn btn-success pull-left c-add-info" id="add_cart_details">Thêm</button>
                                </div>
                            </div>

                            <div class="row">
                                <div class="form-group">
                                    <div class="col-md-12">
                                        <table id="i-cart-info" class="table">
                                            <thead>
                                                <tr>
                                                    <th>Hình ảnh</th>
                                                    <th>Tên sản phẩm</th>
                                                    <th>Số lượng</th>
                                                    <th>Mã sản phẩm</th>
                                                    <th>Size</th>
                                                    <th>Màu</th>
                                                    <th>Đơn Giá</th>
                                                    <th>Thành tiền</th>
                                                    <th></th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                            </tbody>
                                            <tfoot>
                                            </tfoot>
                                        </table>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="ibox-content">

                            <div class="row">
                                <div class="col-md-12">
                                    <h2>Thông tin khách hàng</h2>
                                </div>
                            </div>

                            <div class="row">
                                <div class="form-group clearfix">
                                    <label class="col-md-4 control-label">Số điện thoại (<span class="text-danger">*</span>)</label>
                                    <div class="col-md-8">
                                        <select name="customer_phone" class="form-control required m-b">
                                            <option value="" selected>-- Chọn số điện thoại --</option>
                                            {!! $customer_options !!}
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="form-group">
                                    <label class="col-md-4 control-label">Tên khách hàng (<span class="text-danger">*</span>)</label>
                                    <div class="col-md-8">
                                        <input type="text" name="customer_name" placeholder="" class="form-control required m-b"
                                        value="@if(isset($data->customer) && isset($data->customer->name)){{$data->customer->name}}@else{{old('customer_name')}}@endif"/>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="form-group">
                                    <label class="col-md-4 control-label">Email</label>
                                    <div class="col-md-8">
                                        <input type="text" name="customer_email" placeholder="" class="form-control m-b"
                                        value="@if(isset($data->customer) && isset($data->customer->email)){{$data->customer->email}}@else{{old('customer_email')}}@endif"/>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="form-group">
                                    <label class="col-md-4 control-label">Địa chỉ (<span class="text-danger">*</span>)</label>
                                    <div class="col-md-8">
                                        <input type="text" name="customer_address" placeholder="" class="form-control required m-b"
                                        value="@if(isset($data->customer) && isset($data->customer->address)){{$data->customer->address}}@else{{old('customer_address')}}@endif"/>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="form-group">
                                    <label class="col-md-4 control-label">Thành phố (<span class="text-danger">*</span>)</label>
                                    <div class="col-md-8">
                                        <select name="customer_city" class="form-control required m-b">
                                            <option value="" selected>-- Chọn thành phố --</option>
                                            {!! $city_options !!}
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="form-group">
                                    <label class="col-md-4 control-label">Nguồn đơn</label>
                                    <div class="col-md-8">
                                        <select name="platform" class="form-control m-b">
                                            <option value="" selected>-- Chọn nguồn đơn --</option>
                                            {!! $platform_options !!}
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-12">
                                    <h2>Thông tin khác</h2>
                                </div>
                            </div>

                            <div class="row">
                                <div class="form-group">
                                    <label class="col-md-4 control-label">Dịch vụ vận chuyển</label>
                                    <div class="col-md-8">
                                        <select name="transporting_service" class="form-control m-b">
                                            <option value="" selected>-- Chọn dịch vụ vận chuyển --</option>
                                            {!! $transport_options !!}
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="form-group">
                                    <label class="col-md-4 control-label">Phí vận chuyển</label>
                                    <div class="col-md-8">
                                        <input type="text" name="shipping_fee" placeholder="" class="form-control m-b"
                                        value="@if(isset($data->shipping_fee)){{$data->shipping_fee}}@else{{old('shipping_fee')}}@endif"/>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="form-group clearfix">
                                    <label class="col-md-4 control-label">Cộng tác viên</label>
                                    <div class="col-md-8">
                                        <select name="partner" class="form-control m-b">
                                            <option value="" selected>-- Chọn cộng tác viên --</option>
                                            {!! $partner_options !!}
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="form-group">
                                    <label class="col-md-4 control-label">Chiết khấu cộng tác viên</label>
                                    <div class="col-md-8">
                                        <input type="text" name="partner_discount_amount" placeholder="" class="form-control m-b"
                                        value="@if(isset($data->partner_discount_amount)){{$data->partner_discount_amount}}@else{{old('partner_discount_amount')}}@endif" readonly="readonly" />
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="form-group">
                                    <label class="col-md-4 control-label">Chiết khấu khách hàng</label>
                                    <div class="col-md-8">
                                        <input type="text" name="customer_discount_amount" placeholder="" class="form-control m-b"
                                        value="@if(isset($data->customer_discount_amount)){{$data->customer_discount_amount}}@else{{old('customer_discount_amount')}}@endif" readonly="readonly" />
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="form-group">
                                    <label class="col-md-4 control-label">Thuế (%)</label>
                                    <div class="col-md-8">
                                        <input type="text" name="vat_percent" placeholder="" class="form-control m-b"
                                        value="10" readonly="readonly" />
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="form-group">
                                    <label class="col-md-4 control-label">Khách hàng trả trước</label>
                                    <div class="col-md-8">
                                        <input type="text" name="prepaid_amount" placeholder="" class="form-control m-b"
                                        value="@if(isset($data->prepaid_amount)){{$data->prepaid_amount}}@else{{old('prepaid_amount')}}@endif" />
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="form-group clearfix">
                                    <label class="col-md-4 control-label">Ghi chú</label>
                                    <div class="col-md-8">
                                        <textarea class="form-control" name="descritption">@if(isset($data->descritption)){{$data->descritption}}@endif</textarea>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="form-group clearfix">
                                    <label class="col-md-4 control-label">Trạng thái thanh toán</label>
                                    <div class="col-md-8">
                                        <select name="payment_status" class="form-control m-b">
                                            <option value="" selected>-- Chọn trạng thái --</option>
                                            {!! $payment_options !!}
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="form-group clearfix">
                                    <label class="col-md-4 control-label">Trạng thái</label>
                                    <div class="col-md-8">
                                        <select name="status" class="form-control m-b">
                                            <option value="" selected>-- Chọn trạng thái --</option>
                                            {!! $status_options !!}
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="cart-total-info" style="margin-top: 40px;">
                                <div class="row">
                                    <div class="form-group">
                                        <label class="col-md-4 control-label">Tổng cộng</label>
                                        <div class="col-md-8">
                                            <input type="text" name="total_price" placeholder="" class="form-control m-b"
                                            value="@if(isset($data->total_price)){{$data->total_price}}@else{{old('total_price')}}@endif" readonly="readonly" />
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="form-group">
                                        <label class="col-md-4 control-label">Tổng số lượng</label>
                                        <div class="col-md-8">
                                            <input type="text" name="quantity" placeholder="" class="form-control m-b"
                                            value="@if(isset($data->quantity)){{$data->quantity}}@else{{old('quantity')}}@endif" readonly="readonly" />
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="form-group">
                                        <label class="col-md-4 control-label">Phí vận chuyển</label>
                                        <div class="col-md-8">
                                            <input type="text" name="shipping_fee" placeholder="" class="form-control m-b"
                                            value="@if(isset($data->shipping_fee)){{$data->shipping_fee}}@else{{old('shipping_fee')}}@endif" readonly="readonly" />
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="form-group">
                                        <label class="col-md-4 control-label">Thuế</label>
                                        <div class="col-md-8">
                                            <input type="text" name="vat_amount" placeholder="" class="form-control m-b"
                                            value="@if(isset($data->vat_amount)){{$data->vat_amount}}@else{{old('vat_amount')}}@endif" readonly="readonly" />
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="form-group">
                                        <label class="col-md-4 control-label">Tổng chiết khấu</label>
                                        <div class="col-md-8">
                                            <input type="text" name="total_discount_amount" placeholder="" class="form-control m-b"
                                            value="@if(isset($data->total_discount_amount)){{$data->total_discount_amount}}@else{{old('total_discount_amount')}}@endif" readonly="readonly" />
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="form-group">
                                        <label class="col-md-4 control-label">Thành tiền</label>
                                        <div class="col-md-8">
                                            <input type="text" name="price" placeholder="" class="form-control m-b"
                                            value="@if(isset($data->price)){{$data->price}}@else{{old('price')}}@endif" readonly="readonly" />
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="form-group">
                                        <label class="col-md-4 control-label">Trả trước</label>
                                        <div class="col-md-8">
                                            <input type="text" name="prepaid_amount" placeholder="" class="form-control m-b"
                                            value="@if(isset($data->prepaid_amount)){{$data->prepaid_amount}}@else{{old('prepaid_amount')}}@endif" readonly="readonly" />
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="form-group">
                                        <label class="col-md-4 control-label">Số tiền cần thanh toán</label>
                                        <div class="col-md-8">
                                            <input type="text" name="needed_paid" placeholder="" class="form-control m-b"
                                            value="@if(isset($data->needed_paid)){{$data->needed_paid}}@else{{old('needed_paid')}}@endif" readonly="readonly" />
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-12">
                                    <div class="text-right">
                                        <button type="button" class="btn btn-default" id="bt-reset"><i class="fa fa-refresh"></i> Làm mới</button>
                                        <button type="submit" name="action" class="btn btn-success" value="save"><i class="fa fa-save"></i> Lưu</button>
                                        <button type="submit" name="action" class="btn btn-primary" value="save_quit"><i class="fa fa-save"></i> Lưu &amp; In</button>
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