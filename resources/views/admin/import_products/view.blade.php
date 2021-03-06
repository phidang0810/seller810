@extends('admin.layouts.inspinia.master')

@section('title', $title)

@section('css')
<style type="text/css">
span.select2.select2-container.select2-container--default {
    width: 100%!important;
}
</style>
@endsection

@section('js')
<!-- Page-Level Scripts -->
<script src="{{asset('fck/ckeditor/ckeditor.js')}}"></script>
<script>

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    var importDetails = ($('input[name="importDetails"]').val()) ? jQuery.parseJSON($('input[name="importDetails"]').val()) : [];
    var colors = ($('input[name="colors"]').val()) ? jQuery.parseJSON($('input[name="colors"]').val()) : [];
    var sizes = ($('input[name="sizes"]').val()) ? jQuery.parseJSON($('input[name="sizes"]').val()) : [];
    var photos = ($('input[name="photos"]').val()) ? jQuery.parseJSON($('input[name="photos"]').val()) : [];
    var product_id = ($('input[name="product_id"]').val()) ? jQuery.parseJSON($('input[name="product_id"]').val()) : 0;
    var files = [];

    // When detail quantity, color, size change will count total again
    $(document.body).delegate('.detail_quantity', 'change', function() {
        printDetailTotalQuantities();
        validateImportProductDetail();
    });
    $(document.body).delegate('.select_detail_size', 'change', function() {
        updateDetailsData();
        validateImportProductDetail();
    });
    $(document.body).delegate('.select_detail_color', 'change', function() {
        updateDetailsData();
        validateImportProductDetail();
    });

    // When photo color, name, order change will update photos data
    $(document.body).delegate('.select_photo_color', 'change', function() {
        updatePhotosData();
    });
    $(document.body).delegate('.input_photo_name', 'change', function() {
        updatePhotosData();
    });
    $(document.body).delegate('.input_photo_order', 'change', function() {
        updatePhotosData();
    });
    $(document.body).delegate('select[name=product_id]', 'change', function() {
        var product_id = $('select[name=product_id]').val();
        $('input[name=name]').val($('select[name=product_id] option:selected').text());

        $.ajax({
            url: "{{route('admin.import_products.view')}}",
            data:{
                product_id:product_id,
            },
            dataType:'json'
        }).done(function(data) {
            loadProductDatas(data);
        })
    });

    function validateImportProductDetail(){
        var result = true;
        $('#mainForm button[type="submit"]').removeAttr("disabled");
        $.each(importDetails, function(key, value){
            if (value.delete != true) {
                $('#product_detail_'+key).removeClass('error');
                var empty = false;
                if ($('#select_detail_color_'+key).val() == 0 || $('#select_detail_size_'+key).val() == 0 || $('#detail_quantity_'+key).val() <= 0 ) {
                    $('#product_detail_'+key).addClass('error');
                    $('#mainForm button[type="submit"]').prop('disabled', true);
                    empty = true;
                    result = false;
                }
            }

            if (empty == false) {
                importDetails[key].color_code = {id:$('#select_detail_color_'+key).val(), name:$('#select_detail_color_'+key+' option[value="'+$('#select_detail_color_'+key).val()+'"]').text()};
                importDetails[key].size = {id:$('#select_detail_size_'+key).val(), name:$('#select_detail_size_'+key+' option[value="'+$('#select_detail_size_'+key).val()+'"]').text()};
                importDetails[key].quantity = parseInt($('#detail_quantity_'+key).val());
            }
        });

        $('input[name="importDetails"]').val(JSON.stringify(importDetails));
        return result;
    }

    function loadProductDatas(data = null){
        if (data == null) {
            $('select[name=product_id]').val(0);
            $('select[name=brand_id]').val("");
            $('input[name=price]').val("");
            $('input[name=sell_price]').val("");
            $('textarea[name=description]').val("");
            $('textarea[name=content]').val("");
            $('input[name=order]').val("");
            $('select[name=active]').val("");
            // metas
            $('input[name=meta_keyword]').val("");
            $('input[name=meta_description]').val("");
            $('input[name=meta_robot]').val("");
            // photos
            photos = [];
            $('#i-product-photos tbody').html("");
            // photo
            $('.fileinput').addClass('fileinput-new');
            $('.fileinput').removeClass('fileinput-exists');
            // categories
            $("input[type=checkbox]").prop("checked",false);
        }else{
            // if ( !$('input[name=id]') || $('input[name=id]') == null ) {
                $('select[name=brand_id]').val(data.product.brand_id);
                $('select[name=supplier_id]').val(data.product.supplier_id);
                $('input[name=price]').val(data.product.price);
                $('input[name=sell_price]').val(data.product.sell_price);
                $('textarea[name=description]').html(decodeURI(data.product.description));
                $('textarea[name=content]').html(decodeURI(data.product.description));
                $('input[name=order]').val(data.product.order);
                $('select[name=active]').val(data.product.active);
                // metas
                $('input[name=meta_keyword]').val(data.product.meta_keyword);
                $('input[name=meta_description]').val(data.product.meta_description);
                $('input[name=meta_robot]').val(data.product.meta_robot);

                CKEDITOR.instances['editor-desc'].insertHtml(decodeURI(data.product.description));
                CKEDITOR.instances['editor-content'].insertHtml(decodeURI(data.product.description));
            // }
            // photo
            if (data.product.photo) {
                // if ($('.fileinput').hasClass('fileinput-new')) {
                    var origin   = window.location.origin;
                    var srcImage = origin + '/storage/' + data.product.photo;
                    $('.fileinput').removeClass('fileinput-new');
                    $('.fileinput').addClass('fileinput-exists');
                    $('.fileinput-preview.fileinput-exists.thumbnail').html('<img data-src="holder.js/100%x100%" alt="'+data.product.name+'" src="'+srcImage+'" data-holder-rendered="true">');
                // }
            } else{
                $('.fileinput').addClass('fileinput-new');
                $('.fileinput').removeClass('fileinput-exists');
                $('.fileinput-preview.fileinput-exists.thumbnail').html('<img style="" data-src="holder.js/100%x100%" alt="..." src="data:image/png;base64,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">');
            }
            // photos
            photos = data.product.photos;
            printTablePhotos();
            // categories
            if (data.product.category_ids != null) {
                var categories = data.product.category_ids.split(',');
                $.each(categories, function(key, value){
                    $("input[type=checkbox][value="+value+"]").prop("checked",true);
                });
            }else {
                $("input[type=checkbox]").each(function() {
                  $( this ).prop("checked",false);
              });
            }
        }
    }

    // Function print total quantities to tfoot
    function printDetailTotalQuantities(){
        var sum = 0;
        $.each(importDetails, function(key, value){
            sum += ($('#detail_quantity_'+key).val()) ? parseInt($('#detail_quantity_'+key).val()) : 0;
        });

        var html = '<tr id="product_details_quantity"><td></td><td></td><td></td><td>Tổng số lượng: <span class="c-total-quantities">'+sum+'</span></td></tr>';
        // $('#i-product-info tfoot').html(html);
        $('input[name="import_quantity"]').val(sum);
    }

    // First time show details to table
    function printTableDetails(){
        var html = '';
        $.each(importDetails, function(key, value){
            if (value.delete != true) {
                html += '<tr class="child" id="product_detail_'+key+'">'+htmlEditCreateRowDetail(importDetails[key], key)+'</tr>';
            }
        });
        $('#i-product-info tbody').html(html);
        printDetailTotalQuantities();
    }

    // When "Thêm" button is clicked -> Add new item to array details, append new row to table
    $('#add_details').click(function(){
        importDetails.push({
            'color_code':{'id':0, 'name':''},
            'size':{'id':0, 'name':''},
            'quantity':0
        });

        var key = importDetails.length-1;
        var html = htmlEditCreateRowDetail(importDetails[key], key);

        $('#i-product-info tbody').append('<tr class="child" id="product_detail_'+key+'">'+html+'</tr>');
        printDetailTotalQuantities();
    });

    // When "Xóa" on row is clicked -> Remove current row, add delete:true to item on array details
    function deleteProductInfoItem(key) {
        importDetails[key].delete = true;
        $('#product_detail_'+key).remove();
        updateDetailsData();
        printDetailTotalQuantities();
    }

    // Function to add row with form edit/create to table details
    function htmlEditCreateRowDetail(data, key){
        var html_detail_color_option = '<select id="select_detail_color_'+key+'" class="select_detail_color form-control">';
        html_detail_color_option += '<option value="0">Chọn màu</option>';
        $.each(colors, function(key_color, value_color){
            if (value_color.id == data.color_code.id) {
                html_detail_color_option += '<option value="'+value_color.id+'" selected>'+value_color.name+'</option>';
            }else{
                html_detail_color_option += '<option value="'+value_color.id+'">'+value_color.name+'</option>';
            }
        });
        html_detail_color_option += '</select>';

        var html_detail_size_option = '<select id="select_detail_size_'+key+'" class="select_detail_size form-control">';
        html_detail_size_option += '<option value="0">Chọn size</option>';
        $.each(sizes, function(key_size, value_size){
            if (value_size.id == data.size.id) {
                html_detail_size_option += '<option value="'+value_size.id+'" selected>'+value_size.name+'</option>';
            }else{
                html_detail_size_option += '<option value="'+value_size.id+'">'+value_size.name+'</option>';
            }
        });
        html_detail_size_option += '</select>';

        var html_detail_input_quantity = '<input type="number" min="0" value="'+data['quantity']+'" id="detail_quantity_'+key+'" class="detail_quantity form-control" >';

        var html = '<td>'+html_detail_color_option+'</td><td>'+html_detail_size_option+'</td><td class="c-quantity">'+html_detail_input_quantity+'</td><td><a href="javascript:;" onclick="deleteProductInfoItem('+key+');" class="bt-delete btn btn-xs btn-danger"><i class="fa fa-trash-o" aria-hidden="true"></i></a></td>';
        return html;
    }

    // First time show photos to table
    function printTablePhotos(){
        $.each(photos, function(key, value){
            htmlEditCreateRowPhoto(photos[key], key);
        });
    }
    // When add new photo -> Add new item to array photos, append new row to table
    $('.c-mutiple-input').on('change', addNewProductPhoto);

    function addNewProductPhoto(event){
        tmp_files = event.target.files;
        $.each(tmp_files, function(key, value){
            photos.push({
                'file':value,
                'file_name':value.name,
                'color_code':{'id':0,'name':''},
                'name':value.name,
                'order':'0',
                'delete':false,
            });
            files.push(value);

            key = photos.length-1;
            htmlEditCreateRowPhoto(photos[key], key);
        });
    }

    // When "Xóa" on row is clicked -> Remove current row, add delete:true to item on array photos
    function deleteProductPhotoItem(key) {
        photos[key].delete = true;
        $('#product_photo_row_'+key).remove();
    }

    // Function update photos data
    function updatePhotosData(){
        $.each(photos, function(key, value){
            photos[key].color_code = {id:$('#select_color'+key).val(), name:$('#select_color'+key+' option[value="'+$('#select_color'+key).val()+'"]').text()};
            photos[key].name = ($('#photo_name_'+key).val()) ? $('#photo_name_'+key).val() : photos[key].name;
            photos[key].order = ($('#photo_order_'+key).val()) ? $('#photo_order_'+key).val() : photos[key].order;
        });
        $('input[name="photos"]').val(JSON.stringify(photos));
    }

    // Function to add row with form edit/create to table photos
    function htmlEditCreateRowPhoto(data, key){
        if (data.file) {
            var fileReader = new FileReader();
            fileReader.onload = (function(e) {
                html_photo_color_option = '<select id="select_color'+key+'" class="select_photo_color form-control">';
                html_photo_color_option += '<option value="0">Chọn màu</option>';
                $.each(colors, function(key_color, value_color){
                    html_photo_color_option += '<option value="'+value_color.id+'">'+value_color.name+'</option>';
                });
                html_photo_color_option += '</select>';

                html_photo_input_name = '<input type="text" value="'+data['name']+'" id="photo_name_'+key+'" class="input_photo_name form-control">';

                html_photo_input_order = '<input type="text" value="'+data['order']+'" id="photo_order_'+key+'" class="input_photo_order form-control">';

                html = '<tr class="child" id="product_photo_row_'+key+'"><td><span class="img-wrapper"><img class="imageThumb" src="' + e.target.result + '" title="' + data.name + '"/></span></td><td>'+html_photo_color_option+'</td><td>'+html_photo_input_name+'</td><td class="c-quantity">'+html_photo_input_order+'</td><td><a href="javascript:;" onclick="deleteProductPhotoItem('+key+');" class="bt-delete btn btn-xs btn-danger"><i class="fa fa-trash-o" aria-hidden="true"></i></a></td></tr>';
                

                $(html).appendTo('#i-product-photos tbody');
            });
            fileReader.readAsDataURL(data.file);
        }else{
            html_photo_color_option = '<select id="select_color'+key+'" class="select_photo_color form-control">';
            html_photo_color_option += '<option value="0">Chọn màu</option>';
            $.each(colors, function(key_color, value_color){
                if (value_color.id == data.color_code.id) {
                    html_photo_color_option += '<option value="'+value_color.id+'" selected>'+value_color.name+'</option>';
                }else{
                    html_photo_color_option += '<option value="'+value_color.id+'">'+value_color.name+'</option>';
                }
            });
            html_photo_color_option += '</select>';

            html_photo_input_name = '<input type="text" value="'+data['name']+'" id="photo_name_'+key+'" class="input_photo_name form-control">';

            html_photo_input_order = '<input type="text" value="'+data['order']+'" id="photo_order_'+key+'" class="input_photo_order form-control">';

            html = '<tr class="child" id="product_photo_row_'+key+'"><td><span class="img-wrapper"><img class="imageThumb" src="' + data.origin_url + '" title="' + data.name + '"/></span></td><td>'+html_photo_color_option+'</td><td>'+html_photo_input_name+'</td><td class="c-quantity">'+html_photo_input_order+'</td><td><a href="javascript:;" onclick="deleteProductPhotoItem('+key+');" class="bt-delete btn btn-xs btn-danger"><i class="fa fa-trash-o" aria-hidden="true"></i></a></td></tr>';

            $(html).appendTo('#i-product-photos tbody');
        }
    }

    function synchronize_child_and_parent_category($) {

    }

    $('.validate-ajax').focusout(function(){
        var value = $(this).val();
        var name = $(this).attr('name');
        var current_id = $('input[name="id"]').val();
        $.ajax({
            url: "{{route('admin.products.view')}}",
            data:{
                value:value,
                name:name,
                id:current_id
            },
            dataType:'json'
        }).done(function(data) {
            if (data.result == false) {
                var valMessage = data.message;
                var elInput = $("input[name="+name+"]");
                elInput.after('<label id="name-error" class="error error-product-name" for="'+name+'">'+valMessage+'</label>');
                elInput.addClass("error");
                $('#mainForm button[type="submit"]').prop('disabled', true);
            }else{
                $('#mainForm button[type="submit"]').removeAttr("disabled");
            }
        })
    });

    function print_barcode(){
        var print_el = $("#barcode_image");
        print_el.printThis({
            header: null,
        });
    }

    // Function update details data
    function updateDetailsData(){
        var result = true;
        $('#mainForm button[type="submit"]').removeAttr("disabled");
        $.each(importDetails, function(key, value){
            if (value.delete != true) {
                $('#product_detail_'+key).removeClass('error');
                var existed = false;
                $.each(importDetails, function(key_detail, value_detail){
                    if ($('product_detail_'+key) && value_detail.delete != true) {
                        if (key_detail != key) {
                            if ($('#select_detail_color_'+key_detail).val() == $('#select_detail_color_'+key).val() && $('#select_detail_size_'+key_detail).val() == $('#select_detail_size_'+key).val()) {
                                $('#product_detail_'+key).addClass('error');
                                $('#mainForm button[type="submit"]').prop('disabled', true);
                                existed = true;
                                result = false;
                            }
                        }
                    }
                });
                if (existed == false) {
                    importDetails[key].color_code = {id:$('#select_detail_color_'+key).val(), name:$('#select_detail_color_'+key+' option[value="'+$('#select_detail_color_'+key).val()+'"]').text()};
                    importDetails[key].size = {id:$('#select_detail_size_'+key).val(), name:$('#select_detail_size_'+key+' option[value="'+$('#select_detail_size_'+key).val()+'"]').text()};
                    importDetails[key].quantity = parseInt($('#detail_quantity_'+key).val());
                }
            }
        });
        $('input[name="importDetails"]').val(JSON.stringify(importDetails));
        return result;
    }

    function validateCategories(searchIDs) {
        var result = (searchIDs.length <= 0) ? false : true;

        if (!result) {
            if ($("#categories-empty-error").hasClass("hidden")) {
                $("#categories-empty-error").removeClass("hidden");
                $("#categories-empty-error").css("display","inline-block!important");
                navigationFn.goToSection('#categories-empty-error');
            }
        }else{
            if (!$("#categories-empty-error").hasClass("hidden")) {
                $("#categories-empty-error").addClass("hidden");
                $("#categories-empty-error").css("display","none!important");
            }
        }

        return result;
    }    

    var navigationFn = {
        goToSection: function(id) {
            $('html, body').animate({
                scrollTop: $(id).offset().top
            }, 100);
        }
    } 

    $(document).ready(function ($) {

        if($('#editor-content').length != 0) {
            CKEDITOR.replace('editor-content', {
                allowedContent: true,
                toolbar: [
                {
                    name: 'styles',
                    items: ['Styles', 'Format', 'Font', 'FontSize', 'Cut', 'Copy', 'Paste', 'PasteText', 'PasteFromWord', '-', 'Undo', 'Redo']
                },
                { name: 'basicstyles', items : [ 'Bold','Italic','Underline','Strike','Subscript','Superscript','-','RemoveFormat' ] },
                {name: 'colors', items: ['TextColor', 'BGColor']},
                {
                    name: 'paragraph',
                    groups: ['list', 'indent', 'blocks', 'align', 'bidi'],
                    items: ['NumberedList', 'BulletedList', '-', 'Outdent', 'Indent', '-', 'Blockquote', '-', 'JustifyLeft', 'JustifyCenter', 'JustifyRight', 'JustifyBlock', '-', 'Image', 'Flash', 'Table', 'Source']
                },

                ]
            });
        }

        if($('#editor-desc').length != 0) {
            CKEDITOR.replace('editor-desc', {
                allowedContent: true,
                toolbar: [
                {
                    name: 'styles',
                    items: ['Styles', 'Format', 'Font', 'FontSize', 'Cut', 'Copy', 'Paste', 'PasteText', 'PasteFromWord', '-', 'Undo', 'Redo']
                },
                { name: 'basicstyles', items : [ 'Bold','Italic','Underline','Strike','Subscript','Superscript','-','RemoveFormat' ] },
                {name: 'colors', items: ['TextColor', 'BGColor']},
                {
                    name: 'paragraph',
                    groups: ['list', 'indent', 'blocks', 'align', 'bidi'],
                    items: ['NumberedList', 'BulletedList', '-', 'Outdent', 'Indent', '-', 'Blockquote', '-', 'JustifyLeft', 'JustifyCenter', 'JustifyRight', 'JustifyBlock', '-', 'Image', 'Flash', 'Table', 'Source']
                },

                ]
            });
        }

        // When "Lưu", "Lưu và thoát" on form are clicked -> Update data for array photos
        // When "Lưu", "Lưu và thoát" on form are clicked -> Update data for array details
        $( "#mainForm" ).submit(function( event ) {
            $('#mainForm button[type="submit"]').hide();
            updatePhotosData();
            var searchIDs = $("#mainForm .list-tree-section input:checkbox:checked").map(function(){
              return $(this).val();
          }).get();
            var boolValidateDetails = updateDetailsData();
            var boolValidateCategories = validateCategories(searchIDs);
            if (boolValidateDetails == false || boolValidateCategories == false) {
                setTimeout(function(){
                    $('#mainForm button[type="submit"]').show();
                }, 2000);
                event.preventDefault();
            }else{
                $('input[name="categories"]').val(searchIDs);
                $('#mainForm button[type="submit"]').prop('disabled', true);
                setTimeout(function(){
                    $('#mainForm button[type="submit"]').prop('disabled', false);
                }, 1000);
            }

        });      


        // Init select2
        var url_get_products = '{{route("admin.products.getProductEmptiableAjax")}}';
        $('select[name="product_id"]').select2({
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

        $("#bt-reset").click(function () {
            $("#mainForm")[0].reset();
        });

        $("#mainForm").validate({
            rules:{
                price:{
                    // min: 0,
                    number: true
                },
                sell_price:{
                    // min: 0,
                    number: true
                }
            }
        });
        
        printTableDetails();
        printTablePhotos();
        // print_table_photos(photos);
        synchronize_child_and_parent_category($);

        $('button[value="save"]').click(function(event){
            $('input[name=action]').val($('button[value="save"]').val());
            if (!validateImportProductDetailEmpty() || !validateImportProductDetail()) {
                return false;
            }
        });

        $('button[value="save_quit"]').click(function(event){
            $('input[name=action]').val($('button[value="save_quit"]').val());
            if (!validateImportProductDetailEmpty() || !validateImportProductDetail()) {
                return false;
            }
        });

        $('button[value="save_complete"]').click(function(event){
            $('input[name=action]').val($('button[value="save_complete"]').val());
            if (!validateImportProductDetailEmpty() || !validateImportProductDetail()) {
                return false;
            }
        });

        function validateImportProductDetailEmpty(){
            var status = true;
            if (jQuery.isEmptyObject(importDetails)) {status = false;}

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


        //---> Handling checkbox for categories list
        $('.list-tree input[type=checkbox]').click(function () {
            var sibs = false;
            $(this).closest('ul').children('li').each(function () {
                if($('input[type=checkbox]', this).is(':checked')) sibs=true;
            })
            $(this).parents('ul').prev().prop('checked', sibs);
        });

        new Cleave('.input-sell-price', {
            numeral: true,
            numeralThousandsGroupStyle: 'thousand'
        });

        new Cleave('.input-price', {
            numeral: true,
            numeralThousandsGroupStyle: 'thousand'
        });

        $("a.fileinput-exists").click(function () {
            $('input[name="delete_photo"]').val(true);
        });

        if (product_id != 0) {
            $('select[name=product_id]').val(product_id);
            $.ajax({
                url: "{{route('admin.import_products.view')}}",
                data:{
                    product_id:product_id,
                },
                dataType:'json'
            }).done(function(data) {
                loadProductDatas(data);
            })
        }

        $('span.select2.select2-container.select2-container--default').hide();

        $('input[type=radio][name=product_option]').on('change', function(){
            switch($(this).val()){
                case 'new' :
                $("input[name=name]").show();
                $('span.select2.select2-container.select2-container--default').hide();
                loadProductDatas();
                break;
                case 'old' :
                $("input[name=name]").hide();
                $('span.select2.select2-container.select2-container--default').show();
                break;
            }            
        });
    });
</script>
@endsection
@section('content')
<div class="row">
    <div class="col-lg-12">
        <div class="ibox float-e-margins pl-15 pr-15">
            @include('admin._partials._alert')
            <form role="form" method="POST" id="mainForm" action="{{route('admin.import_products.store')}}"
            enctype="multipart/form-data">
            {{ csrf_field() }}
            @if (isset($data->id))
            <input type="hidden" name="id" value="{{$data->id}}"/>
            <input type="hidden" name="product_id" value="@if(isset($data->product_id)){{$data->product_id}}@endif"/>
            @endif
            <input type="hidden" name="categories" value="@if(isset($categories)){{$categories}}@endif"/>
            <input type="hidden" name="importDetails" value="@if(isset($details)){{$details}}@endif"/>
            <input type="hidden" name="photos" value="@if(isset($photos)){{$photos}}@endif"/>
            <input type="hidden" name="colors" value="@if(isset($colors)){{$colors}}@endif"/>
            <input type="hidden" name="sizes" value="@if(isset($sizes)){{$sizes}}@endif"/>
            <input type="hidden" name="delete_photo" value=""/>
            <div class="ibox-content">

                <div class="row">
                    <div class="col-md-12">
                        <div class="tabs-container m-b">
                            <ul class="nav nav-tabs">
                                <li class="active"><a data-toggle="tab" href="#tab-3" aria-expanded="true">Thông tin sản phẩm</a></li>
                            </ul>
                            <div class="tab-content">
                                <div id="tab-3" class="tab-pane active">
                                    <div class="panel-body">
                                        <div class="row">
                                            <div class="col-md-8">

                                                <div class="row">
                                                    <div class="form-group">
                                                        <div class="col-md-5 import-product-radios">
                                                            <label for="photo" class="mr-10 lbl-ratio">
                                                                <input type="radio" @if (!isset($data->product_id)) checked @endif name="product_option" placeholder="" class="form-control required m-b" value="new" />Nhập hàng mới
                                                            </label>
                                                            <label for="code" class="lbl-ratio">
                                                                <input type="radio" @if (isset($data->product_id)) checked @endif  name="product_option" placeholder="" class="form-control required m-b" value="old" />Nhập hàng đã tồn tại
                                                            </label>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="row">
                                                    <div class="form-group">
                                                        <label class="col-md-3 control-label">Người nhập hàng (<span class="text-danger">*</span>)</label>
                                                        <div class="col-md-9">
                                                            <select name="import_staff_id" class="form-control required m-b">
                                                                <option value="" selected>-- Chọn người nhập hàng --</option>
                                                                {!! $import_staff_options !!}
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="row">
                                                    <div class="form-group">
                                                        <label class="col-md-3 control-label">Kho hàng (<span class="text-danger">*</span>)</label>
                                                        <div class="col-md-9">
                                                            <select name="warehouse_id" class="form-control required m-b">
                                                                <option value="" selected>-- Chọn kho hàng --</option>
                                                                {!! $warehouse_options !!}
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="row">
                                                    <div class="form-group clearfix">
                                                        <label class="col-md-3 control-label">Tên sản phẩm (<span class="text-danger">*</span>)</label>
                                                        <div class="col-md-9 clearfix">
                                                            <input type="text" name="name" placeholder="" class="form-control required m-b validate-ajax"
                                                            value="@if(isset($data->name)){{$data->name}}@else{{old('name')}}@endif" @if (isset($data->product_id)) style="display: none;" @endif/>
                                                            <select name="product_id" class="form-control m-b" @if (!isset($data->product_id)) style="display: none;" @endif>
                                                                <option value="0" selected>-- Chọn sản phẩm --</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="row">
                                                    <div class="form-group">
                                                        <label class="col-md-3 control-label">Số lượng</label>
                                                        <div class="col-md-3">
                                                            <input readonly type="text" name="import_quantity" placeholder="0" class="form-control m-b c-quatity-input"
                                                            value="@if(isset($data->quantity)){{$data->quantity}}@else{{old('quantity')}}@endif"/>
                                                        </div>
                                                        
                                                        <label class="col-md-2 control-label">Số lượng bán ít nhất (<span class="text-danger">*</span>)</label>
                                                        <div class="col-md-3">
                                                            <input type="text" name="min_quantity_sell" placeholder="" class="form-control required m-b" value="@if(isset($data->min_quantity_sell)){{$data->min_quantity_sell}}@else{{1}}@endif"/>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="row">
                                                    <div class="form-group">
                                                        <label class="col-md-3 control-label">Thương hiệu</label>
                                                        <div class="col-md-9">
                                                            <select name="brand_id" class="form-control m-b">
                                                                <option value="" selected>-- Chọn thương hiệu --</option>
                                                                {!! $brand_options !!}
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="row">
                                                    <div class="form-group">
                                                        <label class="col-md-3 control-label">Nhà cung cấp</label>
                                                        <div class="col-md-9">
                                                            <select name="supplier_id" class="form-control m-b">
                                                                <option value="" selected>-- Chọn nhà cung cấp --</option>
                                                                {!! $supplier_options !!}
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="row">
                                                    <div class="form-group">
                                                        <label class="col-md-3 control-label">Giá nhập (<span class="text-danger">*</span>)</label>
                                                        <div class="col-md-3">
                                                            <input type="text" name="price" placeholder="" class="form-control required m-b input-price"
                                                            value="@if(isset($data->price)){{$data->price}}@else{{old('price')}}@endif"/>
                                                        </div>
                                                        <label class="col-md-2 control-label">Giá bán (<span class="text-danger">*</span>)</label>
                                                        <div class="col-md-3">
                                                            <input type="text" name="sell_price" placeholder="" class="form-control required m-b input-sell-price" value="@if(isset($data->sell_price)){{$data->sell_price}}@else{{old('sell_price')}}@endif"/>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="row">
                                                    <div class="form-group">
                                                        <label class="col-md-3 control-label">Sơ lược</label>
                                                        <div class="col-md-9">
                                                            <textarea name="description" id="editor-desc" cols="30" rows="10"  class="form-control m-b">@if(isset($data->description)){!!$data->description!!}@else{{old('description')}}@endif</textarea>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="row">
                                                    <div class="form-group">
                                                        <label class="col-md-3 control-label">Mô tả</label>
                                                        <div class="col-md-9">
                                                            <textarea name="content" id="editor-content" cols="30" rows="10"  class="summernote form-control m-b">@if(isset($data->content)){!!$data->content!!}@else{{old('content')}}@endif</textarea>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="row">
                                                    <div class="form-group">
                                                        <label class="col-md-3 control-label">Thứ tự</label>
                                                        <div class="col-md-3">
                                                            <input type="text" name="order" placeholder="" class="form-control m-b"
                                                            value="@if(isset($data->order)){{$data->order}}@else{{old('order')}}@endif"/>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="row">
                                                    <div class="form-group">
                                                        <label class="col-md-3 control-label">Chú thích nhập hàng</label>
                                                        <div class="col-md-9">
                                                            <textarea name="note" id="" cols="30" rows="10"  class="form-control m-b">@if(isset($data->note)){{$data->note}}@else{{old('note')}}@endif</textarea>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="row">
                                                    <div class="form-group">
                                                        <div class="col-md-3">
                                                        </div>
                                                        <div class="col-md-9">
                                                            <label id="cart-details-empty-error" class="error hidden" for="warehouse_product_id">Phải có ít nhất 1 sản phẩm</label>
                                                            <div class="table-responsive">
                                                                <table id="i-product-info" class="table">
                                                                    <thead>
                                                                        <tr>
                                                                            <th>Màu sắc</th>
                                                                            <th>Kích thước</th>
                                                                            <th>Số lượng</th>
                                                                            <th></th>
                                                                        </tr>
                                                                    </thead>
                                                                    <tbody>
                                                                    </tbody>
                                                                    <tfoot>
                                                                        <tr>
                                                                            <th></th>
                                                                            <th></th>
                                                                            <th></th>
                                                                            <th><button type="button" class="btn btn-success btn-ball c-add-info" id="add_details"><i class="fa fa-plus" aria-hidden="true"></i></button></th>
                                                                        </tr>
                                                                    </tfoot>
                                                                </table>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <!-- BEGIN: Product photo -->
                                                <div class="c-product-photo">
                                                    @if(!isset($data->photo) || empty($data->photo) || $data->photo === '' )
                                                    <div class="fileinput fileinput-new" data-provides="fileinput">
                                                        <div class="fileinput-new thumbnail" style="width: 100%;">
                                                            <img style="" data-src="holder.js/100%x100%" alt="..." src="data:image/png;base64,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">
                                                        </div>
                                                        <div class="fileinput-preview fileinput-exists thumbnail" style="width: 100%;"></div>
                                                        <div>
                                                            <span class="btn btn-default btn-file"><span class="fileinput-new">Select image</span><span class="fileinput-exists">Change</span><input type="file" name="photo"></span>
                                                            <a href="#" class="btn btn-default fileinput-exists" data-dismiss="fileinput">Remove</a>
                                                        </div>
                                                    </div>
                                                    @else
                                                    <div class="fileinput fileinput-exists" data-provides="fileinput">
                                                        <div class="fileinput-new thumbnail" style="width: 100%;">
                                                            <img style="" data-src="holder.js/100%x100%" alt="..." src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAyAAAAMgBAMAAAApXhtbAAAAG1BMVEXMzMyWlpbFxcWxsbGjo6OcnJy3t7e+vr6qqqrLdpw6AAAACXBIWXMAAA7EAAAOxAGVKw4bAAAITElEQVR4nO3dzXMbSRkHYK1sST6uIAaOFkXgiii4R1nCOSIUcIwpKPa4CgXF0a5id/9tMpqvluY32VjJbZ7n5LSUfivvtN/u6fnIbAYAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAMCo+W9f3f7+N2eN/321/uvbs7bV//a3332+ELG7FHdilvt15c8njf+omm5/Hb745nOFiN2luBOz2q1rfykaf1E33d4VbfPmi3/8PCFidynu1BzWrbdd23y/Hmbr+frSbKUQsbsYd2KWXbLWP+4a22SV2eqS9eRsxRCxuxR3avrRW4zfXdf0ovviddf27HOEiN2luFOzL7L1o6atGNI/6b54H7J6eYjYXYw7MTfVv/67n8//tS/G6qb6+Z/zv1ef3TVt8+oPf/jml7syq5eHiN2luFNT5eBl9cNxEfpN3bhrhm21Bn1ovnjdDNvqez/79BCxuxR3arZdPp53WahG70+rH1bF8N20yTw8dfimELG7GHdq9t38WS17vjz+dNXV9ft+XbRt63pV6J904pZCxO5i3ImZ90WkysJxfM4WXal/X1hum0/7tL4vLK8/NUTsLsadmGWx6ly0w7JP26pL5rJP66H7+PIQsbsUd2puitpw1WbusT8N2LVF/6ZP6/XTZvUYInaX4k7NdTHaV22d2PenBvdtPVn0pwbLp9WTGCJ2l+JOzaJYzsybzFRFv23btJ9vuun4+Pndp4WI3cW4U7PoEzObNQkpi/51O5Lvi92M3ZPO1VOI2F2MOzVfFNlqR+hNkYyrtsA/Fgdhe17gq22QF/3fOJ3zU4jYXYw7NameLEbmlW7Zczgv8NfFQVicn2XHkpW6i3GnpkxCWzLikC7qezkB1KpVapu/7fkMk0LE7mLcqSmXsO1SdFP+Auzr9M7LEbsYzLiP3Tl2lcnTk+wUInaX4k7OTZGZTZPKQ1lzdnVtWZU1/XpwZljtTL1uOzyrZylE7C7FnZxyY2rbJOa+TEwz+y7LDN4MlkBX3RWMzfp8CZZCxO5S3OlZn2xXHMf2ttw8bP5wVRaiq+HW3779NXgcXlAMIWJ3Ke70PHbXrw/t8uhkcDbD9uSXYjlck943vwar80VvDhG7S3Gn59AWm6vuMt1J+T7UZxgn08Zq+FuwaK5gXIdLSyFE7C7FnZ7j9dU37+rrq/Wo3ZULnP6A9AurcEBWzeIqXb0KIWJ3Ke4E9Td6tCk4WXFu6taTle483Hiyq4/EPl1ZGoaI3aW4E3TTJ6uZGM4TU03D5xkcnEZvjrWqWlEN79oahojdpbhT1N+P81A3XHRAbo6TyGKw6M0hHJAP6LP1sm7Yl7sWX3QHpNgtCQdkfhz+27wFNQgRu0txJ6ioJ83idH2WmC9np/tMZ99ovD8W63f7eHV3GCJ2l+JO0GMx49bl/7IDUlWrr9ZxKh6GcEBGFaM37cR+/AFpbwO9+5gQDsioY3n/6t38P8e16duq6aSWb8Ickib1dnEbLiuFELG7FHd63mehfozm+FDN66bprvv8Y1dZ7T3u4Up4CGGVNWbZVZFjZTmeR194QOrKNNwRTCEckDHXxTT8+IEz5h88U2/uZw8HKoVwpj5mUzys9LyZki/ZXKxs1/FWkRTC5uKY+yKHy2bKvfSAVJNIWBmlEA7ImMeyVK/rrY1LrodUqik73LOeQrgeMubx7LLpi9llVwxn9Q58Og1JIVwxHLM7G5UvZhddU68sjgfkYdCeQrimPuZkZXOoi8tJ+e7vOumnjeFdJ5X7kfOQFCJ2l+JOTlr7X3BfVvPNdZrtUwj3ZY35oX2rj7tzsXLVbFYNxnXcokrduXNxNsxWc1b+xHt7K9Ulw3+vwycphHt7x6R6cvbIU3f3e7fq2aa5uzoJX6ZTw7gjkrqLcaemHKntdsVFz4fUjzTvwsBOITwfMmZXLvebZc5FT1DVO1aHsL2YQniCaszJWVt7Mra/4BnDw3E+vw4L3xjCM4Yj7ssEtmv/xwuewt0fG1fhElUM4SncEZtiIu1KxiE8L148OX4I54XL5lfjcVjOYojYXYo7NYsigd2oveBNDu2DbJv1YJc2hvAmhxFXRQIPbV2/4F0n2ybrN+vBr08MEbvzrpO6hjy7O/7YPbxxwduA5u3cUf1wO/xsEMLbgMZURb9+e+u2H6rvG2/fzur9wofmizfNF5dh3m5uJG17efsRIWJ3Ke7UHF87+ebd7FffFmO7Gr7P/pbeKHeX3yi36TK9GO6exBCxuxR3albrQlv9n/zOxV2XweqvnpX/GMI7F8dsi2w9tI27rulF98UPvJW0fVynsh+O7hjCW0lHXPXJ6qeGJ763t32grRLqfwzhvb1j+vH70LU98c3W1UFoV8LlazY+FMKbrcd0L2YvX5XfvIO9Wa7W2pe1vxx0sS/WuqtQ02KI2F2KOznLOjOn/03B746D9/QE8Go//N4nhIjdpbiTM//61fpP3581fr0f/j8ey2/Xl/7/ISlE7C7FBQAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAgMv8H4Jxvf8xwCA2AAAAAElFTkSuQmCC">
                                                        </div>
                                                        <div class="fileinput-preview fileinput-exists thumbnail" style="width: 100%;">
                                                            <img data-src="holder.js/100%x100%" alt="{{$data->name}}" src="{{asset('storage/' .$data->photo)}}" data-holder-rendered="true">
                                                        </div>
                                                        <div>
                                                            <span class="btn btn-default btn-file"><span class="fileinput-new">Select image</span><span class="fileinput-exists">Change</span><input type="file" name="photo"></span>
                                                            <a href="#" class="btn btn-default fileinput-exists" data-dismiss="fileinput">Remove</a>
                                                        </div>
                                                    </div>
                                                    @endif
                                                </div>
                                                <!-- END: Product photo -->
                                                <h5>Danh mục sản phẩm</h5>
                                                <label id="categories-empty-error" class="custom-error hidden">Phải có ít nhất 1 danh mục</label>
                                                <div class="list-tree-section m-b">
                                                    {!! $categoriesTree !!}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="text-right">
                            <input type="hidden" name="action" value="">
                            <a href="{{route('admin.import_products.index')}}" class="btn btn-default"><i class="fa fa-arrow-circle-o-left"></i> Trở lại</a>
                            <button type="button" class="btn btn-default" id="bt-reset"><i class="fa fa-refresh"></i>
                                Làm mới
                            </button>
                            <button type="submit" name="action" class="btn btn-primary" value="save"><i
                                class="fa fa-save"></i> Lưu
                            </button>
                            <button type="submit" name="action" class="btn btn-warning" value="save_quit"><i
                                class="fa fa-save"></i> Lưu &amp; Thoát
                            </button>
                            <button type="submit" name="action" class="btn btn-primary" value="save_complete"><i
                                class="fa fa-save"></i> Nhập đơn xong
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
</div>
@endsection