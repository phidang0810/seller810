@extends('admin.layouts.inspinia.master')

@section('title', $title)

@section('js')
<!-- Page-Level Scripts -->
<script>

    var details = ($('input[name="details"]').val()) ? jQuery.parseJSON($('input[name="details"]').val()) : [];
    var colors = ($('input[name="colors"]').val()) ? jQuery.parseJSON($('input[name="colors"]').val()) : [];
    var photos = ($('input[name="photos"]').val()) ? jQuery.parseJSON($('input[name="photos"]').val()) : [];

    function print_table_details(arr_details){
        var sum = 0;
        var html = '';
        $.each(arr_details, function(key, value){
            html += '<tr class="child" data-index="'+key+'"><td><a href="javascript:;" onclick="deleteProductInfoItem('+key+');">Delete</a></td><td>'+value['color']['name']+'</td><td>'+value['size']['name']+'</td><td class="c-quantity">'+value['quantity']+'</td></tr>';
            sum += parseInt(value['quantity']);
        });
        html += '<tr><td></td><td></td><td></td><td>Tổng số lượng: <span class="c-total-quantities">'+sum+'</span></td></tr>';
        $('#i-product-info tbody').html(html);

        $('input[name="details"]').val(JSON.stringify(details));
        $('input[name="quantity"]').val(sum);

    }

    function deleteProductInfoItem(key) {
        details.splice( $.inArray(key, details), 1 );
        print_table_details(details);
    }

    // When button add details is clicked
    $('#add_details').click(function(){
        details.push({
            'color':{'id':$("#i-color-selection").val(),'name':$("#i-color-selection option[value='"+$("#i-color-selection").val()+"']").text()},
            'size':{'id':$("#i-size-selection").val(),'name':$("#i-size-selection option[value='"+$("#i-size-selection").val()+"']").text()},
            'quantity':$("#i-quantity-input").val()
        });

        print_table_details(details);
    });


    var files = [];

    $('.c-mutiple-input').on('change', preparePhotosTable);

    function preparePhotosTable(event)
    {
        tmp_files = event.target.files;
        $.each(tmp_files, function(key, value){
            photos.push({
                'file':value,
                'file_name':value.name,
                'color_code':0,
                'name':value.name,
                'order':'0',
                'delete':false
            });
            files.push(value);
        });
        print_table_photos(photos);
    }

    // function updatePhotosData(){
    //     $.each(photos, function(key, value){
    //         photos[key].color_code = ($('#select_color'+key).val() != 0) ? $('#select_color'+key).val() : photos[key].color_code;
    //         photos[key].name = ($('#photo_name_'+key).val()) ? $('#photo_name_'+key).val() : photos[key].name;
    //         photos[key].order = ($('#photo_order_'+key).val()) ? $('#photo_order_'+key).val() : photos[key].order;
    //     });
    // }

    function print_table_photos(arr_photos){
        var html = '';
        $('#i-product-photos tbody').html(html);
        $.each(arr_photos, function(key, value){

            if (value.delete != true) {
                if (value.file) {
                    var fileReader = new FileReader();
                    fileReader.onload = (function(e) {
                        html_photo_color_option = '<select id="select_color'+key+'">';
                        html_photo_color_option += '<option value="0">Chọn màu</option>';
                        $.each(colors, function(key_color, value_color){
                            if (value_color.id == value.color_code) {
                                html_photo_color_option += '<option value="'+value_color.id+'" selected>'+value_color.name+'</option>';
                            }else{
                                html_photo_color_option += '<option value="'+value_color.id+'">'+value_color.name+'</option>';
                            }
                        });
                        html_photo_color_option += '</select>';

                        html_photo_input_name = '<input type="text" value="'+value['name']+'" id="photo_name_'+key+'">';

                        html_photo_input_order = '<input type="text" value="'+value['order']+'" id="photo_order_'+key+'">';

                        html = '<tr class="child" id="photo_row_'+key+'"><td><a href="javascript:;" onclick="deletePhoto('+key+');">Xóa</a> <a href="javascript:;" onclick="updatePhoto('+key+');">Sửa</a></td><td><span class="img-wrapper"><img class="imageThumb" src="' + e.target.result + '" title="' + value.name + '"/></span></td><td>'+html_photo_color_option+'</td><td>'+html_photo_input_name+'</td><td class="c-quantity">'+html_photo_input_order+'</td></tr>';
                        $(html).appendTo('#i-product-photos tbody');
                    });
                    fileReader.readAsDataURL(value.file);
                }else{
                    html_photo_color_option = '<select id="select_color'+key+'">';
                    html_photo_color_option += '<option value="0">Chọn màu</option>';
                    $.each(colors, function(key_color, value_color){
                        if (value_color.id == value.color_code) {
                            html_photo_color_option += '<option value="'+value_color.id+'" selected>'+value_color.name+'</option>';
                        }else{
                            html_photo_color_option += '<option value="'+value_color.id+'">'+value_color.name+'</option>';
                        }
                    });
                    html_photo_color_option += '</select>';

                    html_photo_input_name = '<input type="text" value="'+value['name']+'" id="photo_name_'+key+'">';

                    html_photo_input_order = '<input type="text" value="'+value['order']+'" id="photo_order_'+key+'">';

                    html = '<tr class="child" id="photo_row_'+key+'"><td><a href="javascript:;" onclick="deletePhoto('+key+');">Xóa</a> <a href="javascript:;" onclick="updatePhoto('+key+');">Sửa</a></td><td><span class="img-wrapper"><img class="imageThumb" src="' + value.origin_url + '" title="' + value.name + '"/></span></td><td>'+html_photo_color_option+'</td><td>'+html_photo_input_name+'</td><td class="c-quantity">'+html_photo_input_order+'</td></tr>';
                    $(html).appendTo('#i-product-photos tbody');
                }
            }

        });
        // updatePhotosData();
        $('input[name="photos"]').val(JSON.stringify(photos));
    }

    function deletePhoto(key){
        photos[key].delete = true;
        print_table_photos(photos);
    }

    function updatePhoto(key){
        photos[key].color_code = ($('#select_color'+key).val()) ? $('#select_color'+key).val() : photos[key].color_code;
        photos[key].name = ($('#photo_name_'+key).val()) ? $('#photo_name_'+key).val() : photos[key].name;
        photos[key].order = ($('#photo_order_'+key).val()) ? $('#photo_order_'+key).val() : photos[key].order;
        print_table_photos(photos);
    }

    $(document).ready(function () {
        $( "#mainForm" ).submit(function( event ) {
            var searchIDs = $("#mainForm .list-tree-section input:checkbox:checked").map(function(){
              return $(this).val();
          }).get();
            $('input[name="categories"]').val(searchIDs);
        });

        $("#bt-reset").click(function () {
            $("#mainForm")[0].reset();
        });

        $("#mainForm").validate();

        //---> Init summer note
        $('.summernote').summernote();

        print_table_details(details);
        print_table_photos(photos);

    });
</script>
@endsection
@section('content')
<div class="row">
    <div class="col-lg-12">
        <div class="ibox float-e-margins pl-15 pr-15">
            @include('admin._partials._alert')
            <form role="form" method="POST" id="mainForm" action="{{route('admin.products.store')}}"
            enctype="multipart/form-data">
            {{ csrf_field() }}
            @if (isset($data->id))
            <input type="hidden" name="id" value="{{$data->id}}"/>
            @endif
            <input type="hidden" name="categories" value="@if(isset($categories)){{$categories}}@endif"/>
            <input type="hidden" name="details" value="@if(isset($details)){{$details}}@endif"/>
            <input type="hidden" name="photos" value="@if(isset($photos)){{$photos}}@endif"/>
            <input type="hidden" name="colors" value="@if(isset($colors)){{$colors}}@endif"/>
            <div class="ibox-content">

                <div class="row">
                    <div class="col-md-12">
                        <div class="tabs-container m-b">
                            <ul class="nav nav-tabs">
                                <li class="active"><a data-toggle="tab" href="#tab-3" aria-expanded="true">Thông tin sản phẩm</a></li>
                                <li class=""><a data-toggle="tab" href="#tab-4" aria-expanded="false">Bộ sưu tập</a></li>
                                <li class=""><a data-toggle="tab" href="#tab-5" aria-expanded="false">Meta SEO</a></li>
                            </ul>
                            <div class="tab-content">
                                <div id="tab-3" class="tab-pane active">
                                    <div class="panel-body">
                                        <div class="row">
                                            <div class="col-md-8">
                                                <div class="row">
                                                    <div class="form-group">
                                                        <label class="col-md-3 control-label">Tên sản phẩm (<span class="text-danger">*</span>)</label>
                                                        <div class="col-md-9">
                                                            <input type="text" name="name" placeholder="" class="form-control required m-b"
                                                            value="@if(isset($data->name)){{$data->name}}@else{{old('name')}}@endif"/>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="row">
                                                    <div class="form-group">
                                                        <label class="col-md-3 control-label">Mã sản phẩm</label>
                                                        <div class="col-md-9">
                                                            <input type="text" name="code" placeholder="" class="form-control m-b"
                                                            value="@if(isset($data->code)){{$data->code}}@else{{old('code')}}@endif"/>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="row">
                                                    <div class="form-group">
                                                        <label class="col-md-3 control-label">Barcode</label>
                                                        <div class="col-md-9">
                                                            <input type="text" name="barcode" placeholder="" class="form-control m-b"
                                                            value="@if(isset($data->barcode)){{$data->barcode}}@else{{old('barcode')}}@endif"/>
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
                                                        <label class="col-md-3 control-label">Số lượng</label>
                                                        <div class="col-md-9">
                                                            <input readonly type="text" name="quantity" placeholder="0" class="form-control m-b c-quatity-input"
                                                            value="@if(isset($data->quantity)){{$data->quantity}}@else{{old('quantity')}}@endif"/>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="row">
                                                    <div class="form-group">
                                                        <label class="col-md-3 control-label">Đơn giá</label>
                                                        <div class="col-md-9">
                                                            <input type="text" name="price" placeholder="" class="form-control m-b"
                                                            value="@if(isset($data->price)){{$data->price}}@else{{old('price')}}@endif"/>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="row">
                                                    <div class="form-group">
                                                        <label class="col-md-3 control-label">Giá bán</label>
                                                        <div class="col-md-9">
                                                            <input type="text" name="sell_price" placeholder="" class="form-control m-b"
                                                            value="@if(isset($data->sell_price)){{$data->sell_price}}@else{{old('sell_price')}}@endif"/>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="row">
                                                    <div class="form-group">
                                                        <label class="col-md-3 control-label">Mô tả (<span class="text-danger">*</span>)</label>
                                                        <div class="col-md-9">
                                                            <textarea name="description" id="" cols="30" rows="10"  class="form-control required m-b">@if(isset($data->description)){{$data->description}}@else{{old('description')}}@endif</textarea>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="row">
                                                    <div class="form-group">
                                                        <label class="col-md-3 control-label">Nội dung</label>
                                                        <div class="col-md-9">
                                                            <textarea name="content" id="" cols="30" rows="10"  class="summernote form-control m-b">@if(isset($data->content)){{$data->content}}@else{{old('content')}}@endif</textarea>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="row">
                                                    <div class="form-group">
                                                        <label class="col-md-3 control-label">Thứ tự</label>
                                                        <div class="col-md-9">
                                                            <input type="text" name="order" placeholder="" class="form-control m-b"
                                                            value="@if(isset($data->order)){{$data->order}}@else{{old('order')}}@endif"/>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="row">
                                                    <div class="form-group">
                                                        <label class="col-md-3 control-label">Trạng thái</label>
                                                        <div class="col-md-3">
                                                            <select class="form-control m-b" name="active">
                                                                <option @if(isset($data->active) && $data->active === ACTIVE || old('active') === ACTIVE) selected
                                                                    @endif value="{{ACTIVE}}">Đã kích hoạt
                                                                </option>
                                                                <option @if(isset($data->active) && $data->active === INACTIVE || old('active') === INACTIVE) selected
                                                                    @endif value="{{INACTIVE}}">Chưa kích hoạt
                                                                </option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="row">
                                                    <div class="form-group">
                                                        <div class="col-md-3">
                                                            <button type="button" class="btn btn-success pull-right c-add-info" id="add_details">Thêm</button>
                                                        </div>
                                                        <div class="col-md-3">
                                                            <select id="i-color-selection" class="form-control">
                                                                <option value="" disabled selected>-- Chọn màu --</option>
                                                                {!! $color_options !!}
                                                            </select>
                                                        </div>
                                                        <div class="col-md-3">
                                                            <select id="i-size-selection" class="form-control">
                                                                <option value="" disabled selected>-- Chọn kích thước --</option>
                                                                {!! $size_options !!}
                                                            </select>
                                                        </div>
                                                        <div class="col-md-3">
                                                            <input id="i-quantity-input" type="text" placeholder="0" class="form-control m-b"
                                                            value=""/>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="row">
                                                    <div class="form-group">
                                                        <div class="col-md-offset-3 col-md-9">
                                                            <table id="i-product-info" class="table">
                                                                <thead>
                                                                    <tr>
                                                                        <th></th>
                                                                        <th>Màu sắc</th>
                                                                        <th>Kích thước</th>
                                                                        <th>Số lượng</th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody>
                                                                </tbody>
                                                            </table>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <h5>Danh mục sản phẩm</h5>
                                                <div class="list-tree-section m-b">
                                                    {!! $categoriesTree !!}
                                                </div>
                                                <!-- BEGIN: Product photo -->
                                                <div class="c-product-photo">
                                                    @if(!isset($data->photo) || empty($data->photo) || $data->photo === '' )
                                                    <div class="fileinput fileinput-new" data-provides="fileinput">
                                                        <div class="fileinput-new thumbnail" style="width: 140px; height: 140px;">
                                                            <img data-src="holder.js/100%x100%" alt="..." src="data:image/svg+xml;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0iVVRGLTgiIHN0YW5kYWxvbmU9InllcyI/PjxzdmcgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIiB3aWR0aD0iMTQwIiBoZWlnaHQ9IjE0MCIgdmlld0JveD0iMCAwIDE0MCAxNDAiIHByZXNlcnZlQXNwZWN0UmF0aW89Im5vbmUiPjwhLS0KU291cmNlIFVSTDogaG9sZGVyLmpzLzE0MHgxNDAKQ3JlYXRlZCB3aXRoIEhvbGRlci5qcyAyLjYuMC4KTGVhcm4gbW9yZSBhdCBodHRwOi8vaG9sZGVyanMuY29tCihjKSAyMDEyLTIwMTUgSXZhbiBNYWxvcGluc2t5IC0gaHR0cDovL2ltc2t5LmNvCi0tPjxkZWZzPjxzdHlsZSB0eXBlPSJ0ZXh0L2NzcyI+PCFbQ0RBVEFbI2hvbGRlcl8xNjRjN2E0MDNlNSB0ZXh0IHsgZmlsbDojQUFBQUFBO2ZvbnQtd2VpZ2h0OmJvbGQ7Zm9udC1mYW1pbHk6QXJpYWwsIEhlbHZldGljYSwgT3BlbiBTYW5zLCBzYW5zLXNlcmlmLCBtb25vc3BhY2U7Zm9udC1zaXplOjEwcHQgfSBdXT48L3N0eWxlPjwvZGVmcz48ZyBpZD0iaG9sZGVyXzE2NGM3YTQwM2U1Ij48cmVjdCB3aWR0aD0iMTQwIiBoZWlnaHQ9IjE0MCIgZmlsbD0iI0VFRUVFRSIvPjxnPjx0ZXh0IHg9IjQ0LjA1NDY4NzUiIHk9Ijc0LjUiPjE0MHgxNDA8L3RleHQ+PC9nPjwvZz48L3N2Zz4=">
                                                        </div>
                                                        <div class="fileinput-preview fileinput-exists thumbnail" style="max-width: 140px; max-height: 140px;"></div>
                                                        <div>
                                                            <span class="btn btn-default btn-file"><span class="fileinput-new">Select image</span><span class="fileinput-exists">Change</span><input type="file" name="photo"></span>
                                                            <a href="#" class="btn btn-default fileinput-exists" data-dismiss="fileinput">Remove</a>
                                                        </div>
                                                    </div>
                                                    @else
                                                    <div class="fileinput fileinput-exists" data-provides="fileinput">
                                                        <div class="fileinput-new thumbnail" style="width: 140px; height: 140px;">
                                                            <img data-src="holder.js/100%x100%" alt="..." src="">
                                                        </div>
                                                        <div class="fileinput-preview fileinput-exists thumbnail" style="width: 140px; height: 140px;">
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
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div id="tab-4" class="tab-pane">
                                    <div class="panel-body">
                                        <div class="collection-photos">
                                            <input class="c-mutiple-input" id="product_photos" name="product_photos[]" type="file" accept="image/*" multiple value="" />
                                            <div class="row">
                                                <div class="col-md-12 c-gallery-preview"></div>
                                            </div>

                                            <div class="row">
                                                <div class="form-group">
                                                    <div class="col-md-9">
                                                        <table id="i-product-photos" class="table">
                                                            <thead>
                                                                <tr>
                                                                    <th></th>
                                                                    <th>Hình</th>
                                                                    <th>Màu</th>
                                                                    <th>Tên</th>
                                                                    <th>Số thứ tự</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>


                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div id="tab-5" class="tab-pane">
                                    <div class="panel-body">
                                        <div class="col-md-12">
                                            <div class="row">
                                                <div class="form-group">
                                                    <label class="col-md-2 control-label">Keyword</label>
                                                    <div class="col-md-8">
                                                        <input type="text" name="keyword" placeholder="" class="form-control m-b"
                                                        value="@if(isset($data->keyword)){{$data->keyword}}@else{{old('keyword')}}@endif"/>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="form-group">
                                                    <label class="col-md-2 control-label">Meta Description</label>
                                                    <div class="col-md-8">
                                                        <input type="text" name="meta_description" placeholder="" class="form-control m-b"
                                                        value="@if(isset($data->meta_description)){{$data->meta_description}}@else{{old('meta_description')}}@endif"/>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="form-group">
                                                    <label class="col-md-2 control-label">Meta Robot</label>
                                                    <div class="col-md-8">
                                                        <input type="text" name="meta_robot" placeholder="" class="form-control m-b"
                                                        value="@if(isset($data->meta_robot)){{$data->meta_robot}}@else{{old('meta_robot')}}@endif"/>
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
                <div class="row">
                    <div class="col-md-12">
                        <div class="text-right">
                            <a href="{{route('admin.products.index')}}" class="btn btn-default"><i class="fa fa-arrow-circle-o-left"></i> Trở lại</a>
                            <button type="button" class="btn btn-default" id="bt-reset"><i class="fa fa-refresh"></i>
                                Làm mới
                            </button>
                            <button type="submit" name="action" class="btn btn-primary" value="save"><i
                                class="fa fa-save"></i> Lưu
                            </button>
                            <button type="submit" name="action" class="btn btn-warning" value="save_quit"><i
                                class="fa fa-save"></i> Lưu &amp; Thoát
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