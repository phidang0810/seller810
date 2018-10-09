@extends('admin.layouts.inspinia.master')

@section('title', $title)

@section('css')
<link href="{{asset('themes/inspinia/css/plugins/select2/select2.min.css')}}" rel="stylesheet">
<style type="text/css">
</style>
@endsection

@section('js')
<script src="{{asset('themes/inspinia/js/plugins/select2/select2.full.min.js')}}"></script>
<!-- Page-Level Scripts -->
<script>
    $('input[name=transport_date]').datepicker();

    var transport_details = ($('input[name="transport_details"]').val()) ? jQuery.parseJSON($('input[name="transport_details"]').val()) : [];
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
        var html_detail_from_warehouse = '<label>' + data.from_warehouse.name + '</label>';

        //---> Receive Warehouse
        var html_detail_receive_warehouse = '<label>' + data.receive_warehouse.name + '</label>';

        // Row html
        var html = '<td>'+html_detail_photo+'</td>\
        <td>'+html_detail_label_name+'</td>\
        <td class="c-quantity">'+html_detail_input_quantity+'</td>\
        <td>'+html_detail_label_size+'</td>\
        <td>'+html_detail_label_color+'</td>\
        <td>'+html_detail_from_warehouse+'</td>\
        <td>'+html_detail_receive_warehouse+'</td>\
        <td><a href="javascript:;" onclick="deleteCartDetailItem('+key+');" class="bt-delete btn btn-xs btn-danger"><i class="fa fa-trash-o" aria-hidden="true"></i></a></td>';
        return html;
    }

    // When "Xóa" on row is clicked -> Remove current row, add delete:true to item on array details
    function deleteCartDetailItem(key) {
        transport_details[key].delete = true;
        $('#transport_detail_'+key).remove();
        $('input[name="transport_details"]').val(JSON.stringify(transport_details));
    }

    // First time show details to table
    function printTableTransportDetails(){
        $.each(transport_details, function(key, value){
            var html = htmlEditCreateRowProductDetail(value, key);

            $('#i-cart-info tbody').append('<tr class="child" id="transport_detail_'+key+'">'+html+'</tr>');
        });
    }

    // When detail quantity, color, size change will count total again
    $(document.body).delegate('.detail_quantity', 'change', function() {
        updateTranslationDetailsData();
    });

    // Function update cart details data
    function updateTranslationDetailsData(){
        $.each(transport_details, function(key, value){
            if (value.delete != true) {
                transport_details[key].product_quantity = parseInt($('#detail_quantity_'+key).val());
            }
        });
        $('input[name="transport_details"]').val(JSON.stringify(transport_details));
    }


    $(document).ready(function(){
        printTableTransportDetails();

        // Init select2
        var url_get_products = '{{route("admin.carts.getProductAjax")}}';
        $('select[name="product_name"]').select2({
            placeholder: '-- Chọn sản phẩm --',
            ajax: {//---> Retrieve post data
                url: url_get_products,
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

        // When "Thêm" button is clicked -> Add new item to array transport_details, append new row to table
        $('#add_details').click(function(){
            if (validateProductInfo($("select[name=product_name]").val(), "name") && validateProductInfo($("select[name=product_color]").val(), "color") && validateProductInfo($("select[name=product_size]").val(), "size") && validateNumberProduct()) {
                // $('#add_details').removeAttr('disabled');
                $('button[value="save"]').removeAttr('disabled');
                $('button[value="save_print"]').removeAttr('disabled');

                if (validateUniqueDetail()) {
                    var path_img_folder = window.location.origin + '/storage/';
                    $.ajax({
                        url: "{{route('admin.transport_warehouse.view')}}",
                        data:{
                            product_id:$('select[name="product_name"]').val(),
                            color_id:$('select[name="product_color"]').val(),
                            size_id:$('select[name="product_size"]').val(),
                            warehouse_id:$('select[name="from_warehouse_id"]').val(),
                            get_data:true
                        },
                        dataType:'json'
                    }).done(function(data) {
                        if (!$.isEmptyObject(data)) {
                            // $('#add_details').prop('disabled', true);
                            transport_details.push({
                                'product_image':(data.product.photo) ? path_img_folder + data.product.photo : default_image,
                                'product_name':{id:$('select[name="product_name"]').val(), name:$('select[name="product_name"] option[value="'+$('select[name="product_name"]').val()+'"]').text()},
                                'product_detail':(data.product_detail) ? data.product_detail : null,
                                'product_quantity':parseInt($('input[name="product_quantity"]').val()),
                                'product_size':{id:$('select[name="product_size"]').val(), name:$('select[name="product_size"] option[value="'+$('select[name="product_size"]').val()+'"]').text()},
                                'product_color':{id:$('select[name="product_color"]').val(), name:$('select[name="product_color"] option[value="'+$('select[name="product_color"]').val()+'"]').text()},
                                'from_warehouse': {id:$('select[name="from_warehouse_id"]').val(), name:$('select[name="from_warehouse_id"] option[value="'+$('select[name="from_warehouse_id"]').val()+'"]').text()},
                                'receive_warehouse': {id:$('select[name="receive_warehouse_id"]').val(), name:$('select[name="receive_warehouse_id"] option[value="'+$('select[name="receive_warehouse_id"]').val()+'"]').text()}
                            });

                            var key = transport_details.length-1;
                            var html = htmlEditCreateRowProductDetail(transport_details[key], key);

                            $('#i-cart-info tbody').append('<tr class="child" id="transport_detail_'+key+'">'+html+'</tr>');
                            $('input[name="transport_details"]').val(JSON.stringify(transport_details));
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

        // Load data for color options when product select is changed
        $('select[name="product_name"]').on('change', function(){
            //---> validatae product
            validateProductInfo($(this).val(), "name");
            $.ajax({
                url: "{{route('admin.transport_warehouse.view')}}",
                data:{
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
                    $('input[name="product_detail_id"]').val(0);
                    
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
                    $('input[name="product_detail_id"]').val(0);

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
                url: "{{route('admin.transport_warehouse.view')}}",
                data:{
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
                    $('input[name="product_detail_id"]').val(0);

                }else{
                    html_sizes_options = '<option value="0"> -- Chọn kích thước -- </option>';
                    $('select[name="product_size"]').html(html_sizes_options);
                    $('input[name="product_quantity"]').removeAttr('max_avaiable');
                    $('input[name="product_quantity"]').val(0);
                    $('input[name="product_detail_id"]').val(0);

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
                url: "{{route('admin.transport_warehouse.view')}}",
                data:{
                    product_id:$('select[name="product_name"]').val(),
                    color_id:$('select[name="product_color"]').val(),
                    size_id:$(this).val()
                },
                dataType:'json'
            }).done(function(data) {
                if (!$.isEmptyObject(data)) {
                    html_warehouses_options = '<option value="0"> -- Chọn nhà kho -- </option>' + generate_options(data.warehourses);
                    $('select[name="from_warehouse_id"]').html(html_warehouses_options);
                    // $('input[name="product_quantity"]').attr('max_avaiable', data.quantity);
                    $('input[name="product_detail_id"]').val(data.detail_id);
                }else{
                    $('input[name="product_quantity"]').removeAttr('max_avaiable');
                    $('input[name="product_quantity"]').val(0);
                    $('input[name="product_detail_id"]').val(0);

                }
            }).fail(function(jqXHR, textStatus){
                alert('Có lỗi xảy ra, xin hãy làm mới trình duyệt');
            })
        });

        //---> Warehouse select change
        $('select[name="from_warehouse_id"]').on('change', function(){
            //---> validatae color
            validateProductInfo($(this).val(), "warehouse");

            $.ajax({
                url: "{{route('admin.transport_warehouse.view')}}",
                data:{
                    product_id: $('select[name="product_name"]').val(),
                    color_id: $('select[name="product_color"]').val(),
                    size_id: $('select[name="product_size"]').val(),
                    warehouse_id: $(this).val()
                },
                dataType:'json'
            }).done(function(data) {
                if (!$.isEmptyObject(data)) {
                    $('input[name="product_quantity"]').attr('max_avaiable', data.quantity);
                    $('input[name="product_detail_id"]').val(data.detail_id);
                }else{
                    $('input[name="product_quantity"]').removeAttr('max_avaiable');
                    $('input[name="product_quantity"]').val(0);
                    $('input[name="product_detail_id"]').val(0);

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
            $.each(transport_details, function(key, value){
                if (value.delete != true && value.product_detail.id == $('input[name="product_detail_id"]').val() ) {
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
            <form role="form" method="POST" id="mainForm" action="{{route('admin.transport_warehouse.store')}}">
                {{ csrf_field() }}
                
                <input type="hidden" name="id" value="@if(isset($data->id)){{$data->id}}@else{{null}}@endif" />

                <input type="hidden" name="transport_details" value="@if(isset($transport_details)){{$transport_details}}@endif"/>
                
                <div class="row">
                    <div class="col-md-8">
                        <div class="ibox-content">

                            <div class="row">
                                <div class="col-md-12">
                                    <h2>{{ $title }}</h2>
                                </div>
                            </div>

                            <div class="row xs-12-mg-bt-mobile m-b">
                                <div class="col-md-4 col-sm-4 col-xs-12">
                                    <select name="product_name" class="form-control">
                                        <option value="0"> -- Chọn sản phẩm -- </option>
                                    </select>
                                    <label id="product-name-error" class="error hidden" for="product_name1">Vui lòng chọn.</label>
                                </div>
                                <div class="col-md-3 col-sm-3 col-xs-12">
                                    <select name="product_color" class="form-control">
                                        <option value="0"> -- Chọn màu sắc -- </option>
                                    </select>
                                    <label id="product-color-error" class="error hidden" for="product_color1">Vui lòng chọn.</label>
                                </div>
                                <div class="col-md-3 col-sm-3 col-xs-12">
                                    <select name="product_size" class="form-control">
                                        <option value="0"> -- Chọn kích thước -- </option>
                                    </select>
                                    <label id="product-size-error" class="error hidden" for="product_size1">Vui lòng chọn.</label>
                                </div>
                                
                                <div class="col-md-12">
                                    <input type="hidden" name="product_detail_id">
                                    <label id="product-detail-id-error" class="error hidden" for="product_detail_id">Sản phẩm giống nhau không thể thêm nhiều lần</label>
                                    <label id="cart-details-empty-error" class="error hidden" for="product_detail_id">Phải có ít nhất 1 sản phẩm</label>
                                </div>
                            </div>
                            <div class="row xs-12-mg-bt-mobile">
                                <!-- BEGIN: Select Warehouse -->
                                <div class="col-md-4 col-sm-4 col-xs-12">
                                    <select name="from_warehouse_id" class="form-control">
                                        <option value="0"> -- Chọn kho xuất -- </option>
                                    </select>
                                    <label id="product-warehouse-error" class="error hidden" for="product_warehouse1">Vui lòng chọn.</label>
                                </div>
                                <div class="col-md-2 col-sm-2 col-xs-12">
                                    <input name="product_quantity" type="text" placeholder="Nhập số lượng" class="form-control m-b"
                                    value="0"/>
                                    <label id="product-quantity-error" class="error hidden" for="product_quantity1">Vui lòng nhập vào số lượng.</label>
                                </div>
                                <div class="col-md-4 col-sm-4 col-xs-12">
                                    <select name="receive_warehouse_id" class="form-control">
                                        <option value="0"> -- Chọn kho nhận -- </option>
                                        {!! $warehouse_options !!}
                                    </select>
                                    <label id="product-warehouse-error" class="error hidden" for="product_warehouse1">Vui lòng chọn.</label>
                                </div>
                                <!-- END: Select Warehouse -->
                                <div class="col-md-2 col-sm-2 col-xs-12">
                                    <button type="button" class="btn btn-success pull-left c-add-info" id="add_details" disabled="true">Thêm</button>
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
                                                        <th>Chuyển từ</th>
                                                        <th>Chuyển đến</th>
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
                    </div>
                    <div class="col-md-4">
                        <div class="ibox-content">

                            <div class="row">
                                <div class="col-md-12">
                                    <h2>Thông tin chuyển kho</h2>
                                </div>
                            </div>

                            <div class="row">
                                <div class="form-group clearfix">
                                    <label class="col-md-4 control-label">Tên người phụ trách</label>
                                    <div class="col-md-8">
                                        <select name="transport_staff_id" class="form-control required m-b">
                                            <option value="" selected>-- Chọn người phụ trách --</option>
                                            {!! $transport_staff_options !!}
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="form-group clearfix">
                                    <label class="col-md-4 font-normal">Ngày chuyển</label>
                                    <div class="col-md-8">
                                        <div class="input-group date">
                                            <span class="input-group-addon"><i class="fa fa-calendar"></i></span><input type="text" name="transport_date" class="form-control required" value="@if(isset($data->transport_date)){{$data->transport_date}}@endif">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-12">
                                    <div class="text-right">
                                        <button type="button" class="btn btn-default" id="bt-reset"><i class="fa fa-refresh"></i> Làm mới</button>
                                        <button type="submit" name="action" class="btn btn-success" value="save"><i class="fa fa-save"></i> Lưu</button>
                                        <button type="submit" name="action" class="btn btn-primary" value="save_print"><i class="fa fa-save"></i> Lưu &amp; In</button>
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
@include('admin._partials._cart_view_print')
@endsection