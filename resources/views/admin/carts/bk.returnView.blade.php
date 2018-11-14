@extends('admin.layouts.inspinia.master')

@section('title', $title)

@section('css')
<link href="{{asset('themes/inspinia/css/plugins/select2/select2.min.css')}}" rel="stylesheet">
<style type="text/css">
.select-wrapper{
    margin-bottom: 10px;
}
</style>
@endsection

@section('js')
<script src="{{asset('themes/inspinia/js/plugins/select2/select2.full.min.js')}}"></script>
<!-- Page-Level Scripts -->
<script>
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

    $('input[name=return_date]').datepicker();

    var return_details = ($('input[name="return_details"]').val()) ? jQuery.parseJSON($('input[name="return_details"]').val()) : [];
    var default_image = '{{asset(NO_PHOTO)}}';

    // Function to add row with form edit/create to table details
    function htmlEditCreateRowProductDetail(data, key){
        // Image
        var html_detail_photo = '<span class="img-wrapper"><img class="img-thumbnail" style="width: 80px; height: 60px;" src="' + data.product_image + '" title="' + data.product_name.name + '"/></span>';
        // Name
        var html_detail_label_name = '<label>' + data.product_name.name + '</label>';
        // Quantity
        var html_detail_input_quantity = '<input type="number" min="0" value="'+data['product_quantity']+'" id="detail_quantity_'+key+'" class="detail_quantity form-control" >';
        // Size
        var html_detail_label_size = '<label>' + data.product_size.name + '</label>';
        // Color
        var html_detail_label_color = '<label>' + data.product_color.name + '</label>';

        //---> Transport Warehouse
        var html_detail_warehouse = '<label>' + data.warehouse.name + '</label>';

        // Row html
        var html = '<td>'+html_detail_photo+'</td>\
        <td>'+html_detail_label_name+'</td>\
        <td class="c-quantity">'+html_detail_input_quantity+'</td>\
        <td>'+html_detail_label_size+'</td>\
        <td>'+html_detail_label_color+'</td>\
        <td>'+html_detail_warehouse+'</td>\
        <td><a href="javascript:;" onclick="deleteCartDetailItem('+key+');" class="bt-delete btn btn-xs btn-danger"><i class="fa fa-trash-o" aria-hidden="true"></i></a></td>';
        return html;
    }

    // When "Xóa" on row is clicked -> Remove current row, add delete:true to item on array details
    function deleteCartDetailItem(key) {
        return_details[key].delete = true;
        $('#return_detail_'+key).remove();
        $('input[name="return_details"]').val(JSON.stringify(return_details));
    }

    // First time show details to table
    function printTableTransportDetails(){
        $.each(return_details, function(key, value){
            var html = htmlEditCreateRowProductDetail(value, key);

            $('#i-cart-info tbody').append('<tr class="child" id="return_detail_'+key+'">'+html+'</tr>');
        });
    }

    // When detail quantity, color, size change will count total again
    $(document.body).delegate('.detail_quantity', 'change', function() {
        updateTranslationDetailsData();
    });

    // Function update cart details data
    function updateTranslationDetailsData(){
        $.each(return_details, function(key, value){
            if (value.delete != true) {
                return_details[key].product_quantity = parseInt($('#detail_quantity_'+key).val());
            }
        });
        $('input[name="return_details"]').val(JSON.stringify(return_details));
    }


    $(document).ready(function(){
        printTableTransportDetails();

        $("#mainForm").validate();

        $('button[value="save"]').click(function(event){
            event.preventDefault();
            swal({
                title: "Cảnh Báo!",
                text: "Thông tin này sẽ ảnh hưởng tới đơn hàng, doanh thu và không thể thay đổi lại được. Bạn có chắc muốn tạo trả hàng ?",
                html: true,
                type: "warning",
                showCancelButton: true,
                confirmButtonClass: "btn-danger",
                confirmButtonText: "Chắc chắn!",
                cancelButtonText: "Không",
                closeOnConfirm: true
            },
            function (isConfirm) {
                if (isConfirm) {
                    if (!validateReturnProductDetailEmpty()) {
                        return false;
                    }else{
                        $("#mainForm").submit();
                    }
                } else {
                }
            });

        });

        var navigationFn = {
            goToSection: function(id) {
                $('html, body').animate({
                    scrollTop: $(id).offset().top
                }, 100);
            }
        }

        function validateReturnProductDetailEmpty(){
            var status = false;
            if (!jQuery.isEmptyObject(return_details)) {
                $.each(return_details, function(key, value){
                    if (value.delete != true && value.product_quantity > 0) {
                        status = true;
                    }
                });
            }

            if (status == false) {
                if ($("#cart-details-empty-error").hasClass("hidden")) {
                    $("#cart-details-empty-error").removeClass("hidden");
                    $("#cart-details-empty-error").css("display","inline-block!important");
                    navigationFn.goToSection('#cart-details-empty-error');
                }
            }else{
                if (!$("#cart-details-empty-error").hasClass("hidden")) {
                    $("#cart-details-empty-error").addClass("hidden");
                    $("#cart-details-empty-error").css("display","none!important");
                }
            }
            return status;
        }

        // Init select2
        var url_get_carts = '{{route("admin.carts.getCartsAjax")}}';
        $('select[name="cart_id"]').select2({
            placeholder: '-- Chọn đơn hàng --',
            ajax: {//---> Retrieve post data
                url: url_get_carts,
                dataType: 'json',
                delay: 250, //---> Delay in ms while typing when to perform a AJAX search
                data: function (params) {
                    return {
                        q: params.term, //---> Search query
                        action: 'mishagetposts', // AJAX action for admin-ajax.php
                    };
                },
                processResults: function( data ) {
                    return {
                        results: data
                    };
                },
                cache: true,
            }
        });

        // When "Thêm" button is clicked -> Add new item to array return_details, append new row to table
        $('#add_details').click(function(){
            if (validateProductInfo($("select[name=product_name]").val(), "name") && validateProductInfo($("select[name=product_color]").val(), "color") && validateProductInfo($("select[name=product_size]").val(), "size") && validateNumberProduct()) {
                // $('#add_details').removeAttr('disabled');
                $('button[value="save"]').removeAttr('disabled');
                $('button[value="save_print"]').removeAttr('disabled');

                if (validateUniqueDetail()) {
                    var path_img_folder = window.location.origin + '/storage/';
                    $.ajax({
                        url: "{{route('admin.carts.returnView')}}",
                        data:{
                            cart_id:$('select[name="cart_id"]').val(),
                            product_id:$('select[name="product_name"]').val(),
                            color_id:$('select[name="product_color"]').val(),
                            size_id:$('select[name="product_size"]').val(),
                            warehouse_id:$('select[name="warehouse_id"]').val(),
                            get_data:true
                        },
                        dataType:'json'
                    }).done(function(data) {
                        if (!$.isEmptyObject(data)) {
                            // $('#add_details').prop('disabled', true);
                            return_details.push({
                                'product_image':(data.product.photo) ? path_img_folder + data.product.photo : default_image,
                                'product_name':{id:$('select[name="product_name"]').val(), name:$('select[name="product_name"] option[value="'+$('select[name="product_name"]').val()+'"]').text()},
                                'product_detail':(data.product_detail) ? data.product_detail : null,
                                'product_quantity':parseInt($('input[name="product_quantity"]').val()),
                                'product_size':{id:$('select[name="product_size"]').val(), name:$('select[name="product_size"] option[value="'+$('select[name="product_size"]').val()+'"]').text()},
                                'product_color':{id:$('select[name="product_color"]').val(), name:$('select[name="product_color"] option[value="'+$('select[name="product_color"]').val()+'"]').text()},
                                'warehouse': {id:$('select[name="warehouse_id"]').val(), name:$('select[name="warehouse_id"] option[value="'+$('select[name="warehouse_id"]').val()+'"]').text()},
                                'warehouse_product_id': data.warehouse_product_id,
                                'cart_detail_id':data.cart_detail_id
                            });

                            var key = return_details.length-1;
                            var html = htmlEditCreateRowProductDetail(return_details[key], key);

                            $('#i-cart-info tbody').append('<tr class="child" id="return_detail_'+key+'">'+html+'</tr>');
                            $('input[name="return_details"]').val(JSON.stringify(return_details));
                        }else{

                        }
                    }).fail(function(jqXHR, textStatus){
                        alert('Có lỗi xảy ra, xin hãy làm mới trình duyệt');
                    });
                }

            }else{
                $('#add_details').prop('disabled', true);
                $('button[value="save"]').prop('disabled', true);
                $('button[value="save_print"]').prop('disabled', true);
            }
        });

        $('select[name="cart_id"]').on('change', function(){
            var cart_id = $(this).val();
            $.ajax({
                url: "{{route('admin.carts.returnView')}}",
                data:{
                    cart_id:$(this).val()
                },
                dataType:'json'
            }).done(function(data) {
                if (!$.isEmptyObject(data.products)) {
                    html_product_options = '<option value="0"> -- Chọn sản phẩm -- </option>' + generate_options(data.products);
                    $('input[name="cart"]').val(cart_id);
                    $('select[name="cart_id"]').select2({disabled: true});
                }else{
                    html_product_options = '<option value="0"> -- Chọn sản phẩm -- </option>';
                }
                $('select[name="product_name"]').html(html_product_options);
                html_color_options = '<option value="0"> -- Chọn màu sắc -- </option>';
                $('select[name="product_color"]').html(html_color_options);
                html_sizes_options = '<option value="0"> -- Chọn kích thước -- </option>';
                $('select[name="product_size"]').html(html_sizes_options);
                $('input[name="product_quantity"]').removeAttr('max_avaiable');
                $('input[name="product_quantity"]').val(0);
                $('input[name="warehouse_product_id"]').val(0);

                $('.col-md-8 .ibox-content .error').each(function(){
                    if (!$(this).hasClass('hidden')) {
                        $(this).addClass('hidden');
                    }
                });
            });
        });

        // Load data for color options when product select is changed
        $('select[name="product_name"]').on('change', function(){
            //---> validatae product
            validateProductInfo($(this).val(), "name");
            $.ajax({
                url: "{{route('admin.carts.returnView')}}",
                data:{
                    cart_id:$('select[name="cart_id"]').val(),
                    product_id:$(this).val()
                },
                dataType:'json'
            }).done(function(data) {
                if (!$.isEmptyObject(data.colors)) {
                    html_color_options = '<option value="0"> -- Chọn màu sắc -- </option>' + generate_options(data.colors);
                    $('select[name="product_color"]').html(html_color_options);
                    html_sizes_options = '<option value="0"> -- Chọn kích thước -- </option>';
                    $('select[name="product_size"]').html(html_sizes_options);
                    $('input[name="product_quantity"]').removeAttr('max_avaiable');
                    $('input[name="product_quantity"]').val(0);
                    $('input[name="warehouse_product_id"]').val(0);
                    
                    $('.col-md-8 .ibox-content .error').each(function(){
                        if (!$(this).hasClass('hidden')) {
                            $(this).addClass('hidden');
                        }
                    });
                }else{
                    html_sizes_options = '<option value="0"> -- Chọn kích thước -- </option>';
                    html_color_options = '<option value="0"> -- Chọn màu sắc -- </option>';
                    $('select[name="product_color"]').html(html_color_options);
                    $('select[name="product_size"]').html(html_sizes_options);
                    $('input[name="product_quantity"]').removeAttr('max_avaiable');
                    $('input[name="product_quantity"]').val(0);
                    $('input[name="warehouse_product_id"]').val(0);

                    $('.col-md-8 .ibox-content .error').each(function(){
                        if (!$(this).hasClass('hidden')) {
                            $(this).addClass('hidden');
                        }
                    });
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
                url: "{{route('admin.carts.returnView')}}",
                data:{
                    cart_id:$('select[name="cart_id"]').val(),
                    product_id:$('select[name="product_name"]').val(),
                    color_id:$(this).val()
                },
                dataType:'json'
            }).done(function(data) {
                if (!$.isEmptyObject(data.sizes)) {
                    html_sizes_options = '<option value="0"> -- Chọn kích thước -- </option>' + generate_options(data.sizes);
                    $('select[name="product_size"]').html(html_sizes_options);
                    $('input[name="product_quantity"]').removeAttr('max_avaiable');
                    $('input[name="product_quantity"]').val(0);
                    $('input[name="warehouse_product_id"]').val(0);

                }else{
                    html_sizes_options = '<option value="0"> -- Chọn kích thước -- </option>';
                    $('select[name="product_size"]').html(html_sizes_options);
                    $('input[name="product_quantity"]').removeAttr('max_avaiable');
                    $('input[name="product_quantity"]').val(0);
                    $('input[name="warehouse_product_id"]').val(0);

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
                url: "{{route('admin.carts.returnView')}}",
                data:{
                    cart_id:$('select[name="cart_id"]').val(),
                    product_id:$('select[name="product_name"]').val(),
                    color_id:$('select[name="product_color"]').val(),
                    size_id:$(this).val()
                },
                dataType:'json'
            }).done(function(data) {
                if (!$.isEmptyObject(data)) {
                    html_warehouses_options = '<option value="0"> -- Chọn nhà kho -- </option>' + generate_options(data.warehourses);
                    $('select[name="warehouse_id"]').html(html_warehouses_options);
                    // $('input[name="product_quantity"]').attr('max_avaiable', data.quantity);
                    // $('input[name="warehouse_product_id"]').val(data.product_detail.id);
                }else{
                    $('input[name="product_quantity"]').removeAttr('max_avaiable');
                    $('input[name="product_quantity"]').val(0);
                    $('input[name="warehouse_product_id"]').val(0);

                }
            }).fail(function(jqXHR, textStatus){
                alert('Có lỗi xảy ra, xin hãy làm mới trình duyệt');
            })
        });

        //---> Warehouse select change
        $('select[name="warehouse_id"]').on('change', function(){
            //---> validatae color
            validateProductInfo($(this).val(), "warehouse");

            $.ajax({
                url: "{{route('admin.carts.returnView')}}",
                data:{
                    cart_id:$('select[name="cart_id"]').val(),
                    product_id: $('select[name="product_name"]').val(),
                    color_id: $('select[name="product_color"]').val(),
                    size_id: $('select[name="product_size"]').val(),
                    warehouse_id: $(this).val()
                },
                dataType:'json'
            }).done(function(data) {
                if (!$.isEmptyObject(data)) {
                    $('input[name="product_quantity"]').attr('max_avaiable', data.quantity);
                    $('input[name="cart_detail_id"]').val(data.cart_detail_id);
                }else{
                    $('input[name="product_quantity"]').removeAttr('max_avaiable');
                    $('input[name="product_quantity"]').val(0);
                    $('input[name="cart_detail_id"]').val(0);

                }
            }).fail(function(jqXHR, textStatus){
                alert('Có lỗi xảy ra, xin hãy làm mới trình duyệt');
            })

        });

        // Number product changed
        $('input[name="product_quantity"]').on('change', function(){
            validateNumberProduct();
        });

        function validateUniqueDetail(){
            var status = true;
            $.each(return_details, function(key, value){
                if (value.delete != true && value.cart_detail_id == $('input[name="cart_detail_id"]').val() ) {
                    status = false;
                }
            });

            if (status == false) {
                if ($("#product-detail-id-error").hasClass("hidden")) {
                    $("#product-detail-id-error").removeClass("hidden");
                    $("#product-detail-id-error").css("display","inline-block!important");
                }
            }else{
                if (!$("#product-detail-id-error").hasClass("hidden")) {
                    $("#product-detail-id-error").addClass("hidden");
                    $("#product-detail-id-error").css("display","none!important");
                }
            }
            return status;
        }

        function validateNumberProduct(){
            var status = true;
            var max = parseInt($('input[name="product_quantity"]').attr('max_avaiable'));
            var val = parseInt($('input[name="product_quantity"]').val());

            if (val <= 0 || val > max) {
                if ($("#product-quantity-error").hasClass("hidden")) {
                    $("#product-quantity-error").removeClass("hidden");
                    $("#product-quantity-error").css("display","inline-block!important");
                }
                if (val <= 0) {
                    $("#product-quantity-error").html('Số lượng sản phẩm phải lớn hơn 0');
                }else{
                    $("#product-quantity-error").html('Sản phẩm này chỉ còn '+max+' sản phẩm');
                }
                $('#add_details').prop('disabled', true);
                status = false;
            }else{
                if (!$("#product-quantity-error").hasClass("hidden")) {
                    $("#product-quantity-error").addClass("hidden");
                    $("#product-quantity-error").css("display","none!important");
                }

                $('#add_details').removeAttr('disabled');
                status = true;
            }
            return status;
        }

        function validateProductInfo(elSelectVal, objectName){
            var status = true;
            if (elSelectVal == 0) {
                if ($("#product-"+objectName+"-error").hasClass("hidden")) {
                    $("#product-"+objectName+"-error").removeClass("hidden");
                    $("#product-"+objectName+"-error").css("display","inline-block!important");
                }
                status = false;
            }else{
                if (!$("#product-"+objectName+"-error").hasClass("hidden")) {
                    $("#product-"+objectName+"-error").addClass("hidden");
                    $("#product-"+objectName+"-error").css("display","none!important");
                }

                status = true;
            }
            return status;
        }

        // Function to generate color options
        function generate_options(data){
            var html = '';
            $.each(data, function(key, value){
                html += '<option value='+value.id+'>'+value.name+'</option>';
            });

            return html;
        }
    });
</script>
@endsection
@section('content')
<div class="row">
    <div class="col-lg-12">
        <div class="ibox float-e-margins pl-15 pr-15">
            @include('admin._partials._alert')
            <form role="form" method="POST" id="mainForm" action="{{route('admin.carts.returnStore')}}">
                {{ csrf_field() }}
                
                <input type="hidden" name="id" value="@if(isset($data->id)){{$data->id}}@else{{null}}@endif" />
                
                <input type="hidden" name="cart" value="" />

                <input type="hidden" name="return_details" value="@if(isset($return_details)){{$return_details}}@endif"/>
                
                <div class="row">
                    <div class="col-md-12">
                        <div class="ibox-content">

                            <div class="row">
                                <div class="col-md-12">
                                    <h2>{{ $title }}</h2>
                                </div>
                            </div>

                            <div class="row xs-12-mg-bt-mobile m-b">
                                <div class="col-md-3 col-sm-3 col-xs-12 select-wrapper">
                                    <select name="cart_id" class="form-control">
                                        <option value="0"> -- Chọn đơn hàng -- </option>
                                    </select>
                                    <label id="-product-cart-error" class="error hidden" for="cart_id1">Vui lòng chọn.</label>
                                </div>
                                <div class="col-md-3 col-sm-3 col-xs-12 select-wrapper">
                                    <select name="product_name" class="form-control">
                                        <option value="0"> -- Chọn sản phẩm -- </option>
                                    </select>
                                    <label id="product-name-error" class="error hidden" for="product_name1">Vui lòng chọn.</label>
                                </div>
                                <div class="col-md-3 col-sm-3 col-xs-12 select-wrapper">
                                    <select name="product_color" class="form-control">
                                        <option value="0"> -- Chọn màu sắc -- </option>
                                    </select>
                                    <label id="product-color-error" class="error hidden" for="product_color1">Vui lòng chọn.</label>
                                </div>
                                <div class="col-md-3 col-sm-3 col-xs-12 select-wrapper">
                                    <select name="product_size" class="form-control">
                                        <option value="0"> -- Chọn kích thước -- </option>
                                    </select>
                                    <label id="product-size-error" class="error hidden" for="product_size1">Vui lòng chọn.</label>
                                </div>
                                <!-- BEGIN: Select Warehouse -->
                                <div class="col-md-3 col-sm-3 col-xs-12 select-wrapper">
                                    <select name="warehouse_id" class="form-control">
                                        <option value="0"> -- Chọn kho xuất -- </option>
                                    </select>
                                    <label id="product-warehouse-error" class="error hidden" for="product_warehouse1">Vui lòng chọn.</label>
                                </div>
                                <div class="col-md-3 col-sm-3 col-xs-12 select-wrapper">
                                    <input name="product_quantity" type="text" placeholder="Nhập số lượng" class="form-control m-b"
                                    value="0"/>
                                    <label id="product-quantity-error" class="error hidden" for="product_quantity1">Vui lòng nhập vào số lượng.</label>
                                </div>
                                <!-- END: Select Warehouse -->
                                <div class="col-md-3 col-sm-3 col-xs-12 select-wrapper">
                                    <button type="button" class="btn btn-success pull-left c-add-info" id="add_details" disabled="true">Thêm</button>
                                </div>
                                
                                <div class="col-md-12">
                                    <input type="hidden" name="cart_detail_id">
                                    <label id="product-detail-id-error" class="error hidden" for="cart_detail_id">Sản phẩm giống nhau không thể thêm nhiều lần</label>
                                    <label id="cart-details-empty-error" class="error hidden" for="cart_detail_id">Phải có ít nhất 1 sản phẩm</label>
                                </div>
                            </div>

                            <div class="row">
                                <div class="form-group">
                                    <div class="col-md-12">
                                        <div class="table-responsive">
                                            <table id="i-cart-info" class="table">
                                                <thead>
                                                    <tr>
                                                        <th>Hình ảnh</th>
                                                        <th>Tên sản phẩm</th>
                                                        <th>Số lượng</th>
                                                        <th>Size</th>
                                                        <th>Màu</th>
                                                        <th>Kho</th>
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

                            <div class="row">
                                <div class="col-md-12">
                                    <div class="text-right">
                                        <a href="{{route('admin.carts.returnIndex')}}" class="btn btn-default"><i class="fa fa-arrow-circle-o-left"></i> Trở lại</a>
                                        @if( !isset($data->id) )
                                        <button type="submit" name="action" class="btn btn-success" value="save"><i class="fa fa-save"></i> Lưu</button>
                                        @endif
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