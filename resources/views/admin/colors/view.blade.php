@extends('admin.layouts.inspinia.master')

@section('title', $title)

@section('js')
<!-- Page-Level Scripts -->
<script>
    $(document).ready(function () {
        $("#bt-reset").click(function () {
            $("#mainForm")[0].reset();
        })

        $("#i-color-code").colorpicker({popover:false});

        if($(".c-ratio-photo").is(":checked")){
                $(".c-photo-group").show();
                $(".c-code-group").hide();
            }else{
                $(".c-photo-group").hide();
                $(".c-code-group").show();
            }

            
            $('input[type=radio][name=color]').on('change', function(){
                switch($(this).val()){
                    case 'photo' :
                    $(".c-photo-group").show();
                    $(".c-code-group").hide();
                    break;
                    case 'code' :
                    $(".c-code-group").show();
                    $(".c-photo-group").hide();
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
            <form role="form" method="POST" id="mainForm" action="{{route('admin.colors.store')}}" enctype="multipart/form-data">
                {{ csrf_field() }}
                @if (isset($data->id))
                <input type="hidden" name="id" value="{{$data->id}}"/>
                @endif
                <div class="ibox-content">
                    <div class="row">
                        <div class="form-group">
                            <label class="col-md-2 control-label">Hình ảnh/Mã màu (<span
                                class="text-danger">*</span>)</label>
                                <div class="col-md-5">
                                    @if(isset($data->photo) && !empty($data->photo))
                                    <label for="photo" class="mr-10 lbl-ratio">
                                        <input type="radio" checked name="color" placeholder="" class="form-control m-b c-ratio-color c-ratio-photo" value="photo"/>Hình ảnh
                                    </label>
                                    @else
                                     <label for="photo" class="mr-10 lbl-ratio">
                                        <input type="radio" name="color" placeholder="" class="form-control m-b c-ratio-color c-ratio-photo" value="photo"/>Hình ảnh
                                    </label>
                                    @endif
                                    @if(isset($data->code) && !empty($data->code))
                                    <label for="code" class="lbl-ratio">
                                        <input type="radio" checked name="color" placeholder="" class="form-control m-b c-ratio-color c-ratio-code" value="code"/>Mã màu
                                    </label>
                                    @else
                                    <label for="code" class="lbl-ratio">
                                        <input type="radio" name="color" placeholder="" class="form-control m-b c-ratio-color c-ratio-code" value="code"/>Mã màu
                                    </label>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="form-group c-photo-group">
                                <label class="col-md-2 control-label">Hình ảnh (<span
                                    class="text-danger">*</span>)</label>
                                    <div class="col-md-5">
                                        <div class="fileinput fileinput-new" data-provides="fileinput">
                                            <div class="fileinput-new thumbnail" style="width: 140px; height: 140px;">
                                             @if(isset($data->photo) && !empty($data->photo))
                                             <img alt="400x400" id="preview" data-src="holder.js/400x400" style="margin-bottom: 10px;width: 140px; height: 140px;" class="img-thumbnail" src="{{asset('storage/' .$data->photo)}}" data-holder-rendered="true">
                                             @else
                                             <img src="data:image/svg+xml;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0iVVRGLTgiIHN0YW5kYWxvbmU9InllcyI/PjxzdmcgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIiB3aWR0aD0iMTQwIiBoZWlnaHQ9IjE0MCIgdmlld0JveD0iMCAwIDE0MCAxNDAiIHByZXNlcnZlQXNwZWN0UmF0aW89Im5vbmUiPjwhLS0KU291cmNlIFVSTDogaG9sZGVyLmpzLzE0MHgxNDAKQ3JlYXRlZCB3aXRoIEhvbGRlci5qcyAyLjYuMC4KTGVhcm4gbW9yZSBhdCBodHRwOi8vaG9sZGVyanMuY29tCihjKSAyMDEyLTIwMTUgSXZhbiBNYWxvcGluc2t5IC0gaHR0cDovL2ltc2t5LmNvCi0tPjxkZWZzPjxzdHlsZSB0eXBlPSJ0ZXh0L2NzcyI+PCFbQ0RBVEFbI2hvbGRlcl8xNjRjN2E0MDNlNSB0ZXh0IHsgZmlsbDojQUFBQUFBO2ZvbnQtd2VpZ2h0OmJvbGQ7Zm9udC1mYW1pbHk6QXJpYWwsIEhlbHZldGljYSwgT3BlbiBTYW5zLCBzYW5zLXNlcmlmLCBtb25vc3BhY2U7Zm9udC1zaXplOjEwcHQgfSBdXT48L3N0eWxlPjwvZGVmcz48ZyBpZD0iaG9sZGVyXzE2NGM3YTQwM2U1Ij48cmVjdCB3aWR0aD0iMTQwIiBoZWlnaHQ9IjE0MCIgZmlsbD0iI0VFRUVFRSIvPjxnPjx0ZXh0IHg9IjQ0LjA1NDY4NzUiIHk9Ijc0LjUiPjE0MHgxNDA8L3RleHQ+PC9nPjwvZz48L3N2Zz4=" data-src="holder.js/100%x100%" alt=".">
                                             @endif
                                         </div>
                                         <div class="fileinput-preview fileinput-exists thumbnail"
                                         style="max-width: 140px; max-height: 140px;"></div>
                                         <div>
                                            <span class="btn btn-default btn-file">
                                                <span class="fileinput-new">Select image</span>
                                                <span class="fileinput-exists">Change</span>
                                                <input type="file" name="photo">
                                            </span>
                                            <a href="#" class="btn btn-default fileinput-exists"
                                            data-dismiss="fileinput">Remove</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="form-group c-code-group">
                                <label class="col-md-2 control-label">Mã màu (<span
                                    class="text-danger">*</span>)</label>
                                    <div class="col-md-5">
                                        <input id="i-color-code" type="text" name="code" placeholder="" class="form-control m-b"
                                        value="@if(isset($data->code)){{$data->code}}@else{{old('code')}}@endif"/>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="form-group">
                                    <label class="col-md-2 control-label">Tên màu sắc (<span
                                        class="text-danger">*</span>)</label>
                                        <div class="col-md-5">
                                            <input type="text" name="name" placeholder="" class="form-control m-b"
                                            value="@if(isset($data->name)){{$data->name}}@else{{old('name')}}@endif"/>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="form-group">
                                        <label class="col-md-2 control-label">Thứ tự</label>
                                        <div class="col-md-5">
                                            <input type="text" name="order" placeholder="" class="form-control m-b"
                                            value="@if(isset($data->order)){{$data->order}}@else{{old('order')}}@endif"/>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="form-group">
                                        <label class="col-md-2 control-label">Trạng thái</label>
                                        <div class="col-md-3">
                                            <select class="form-control" name="active">
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

                                <div class="text-right">
                                    <a href="{{route('admin.colors.index')}}" class="btn btn-default"><i class="fa fa-arrow-circle-o-left"></i> Trở lại</a>
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