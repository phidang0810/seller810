@extends('admin.layouts.inspinia.master')

@section('title', $title)

@section('js')
    <script src="{{asset('fck/ckeditor/ckeditor.js')}}"></script>
    <script src="{{asset('js/holder.min.js')}}"></script>
<!-- Page-Level Scripts -->
<script>
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    function readURL(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();

            reader.onload = function(e) {
                $('#preview').attr('src', e.target.result);
            };

            reader.readAsDataURL(input.files[0]);
        }
    }

    $(document).ready(function() {
        $("#bt-reset").click(function(){
            $("#mainForm")[0].reset();
        });

        $("#mainForm").validate();

    });
</script>
@endsection
@section('content')
<div class="row">
    <div class="col-lg-12">
        <div class="ibox float-e-margins pl-15 pr-15">
            @include('admin._partials._alert')
            <form role="form" method="POST" id="mainForm" action="{{route('admin.photos.store')}}" enctype="multipart/form-data">
                {{ csrf_field() }}
                @if (isset($data->id))
                <input type="hidden" name="id" value="{{$data->id}}" />
                @endif
                <div class="ibox-content" style="padding: 20px;">

                    <div class="row m-b">
                        <div class="form-group">
                            <label class="col-md-2 control-label">Loại (<span class="text-danger">*</span>)</label>
                            <div class="col-md-5">
                                <select class="form-control" name="type">
                                    <option @if(isset($data->type) && $data->type === PHOTO_BANNER || old('type') === PHOTO_BANNER) selected @endif value="{{PHOTO_BANNER}}">Slides</option>
                                    <option @if(isset($data->type) && $data->type === PHOTO_AD || old('type') === PHOTO_AD) selected @endif value="{{PHOTO_AD}}">Quảng Cáo</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="row m-b">
                        <div class="form-group">
                            <label class="col-md-2 control-label">Ảnh Bải Viết</label>
                            <div class="col-md-5">
                                @if(!isset($data->photo) || empty($data->photo) || $data->photo === '' )
                                    <div class="fileinput fileinput-new" data-provides="fileinput" style="width:200px">
                                        <div class="fileinput-new thumbnail" style="width: 100%;">
                                            <img style="" src="{{asset(NO_PHOTO)}}">
                                        </div>
                                        <div class="fileinput-preview fileinput-exists thumbnail" style="width: 100%;"></div>
                                        <div>
                                            <span class="btn btn-default btn-file"><span class="fileinput-new">Select image</span><span class="fileinput-exists">Change</span><input type="file" name="photo"></span>
                                            <a href="#" class="btn btn-default fileinput-exists" data-dismiss="fileinput">Remove</a>
                                        </div>
                                    </div>
                                @else
                                    <div class="fileinput fileinput-exists" data-provides="fileinput" style="width:200px">
                                        <div class="fileinput-new thumbnail" style="width: 100%;">
                                            <img src="{{asset(NO_PHOTO)}}">
                                        </div>
                                        <div class="fileinput-preview fileinput-exists thumbnail" style="width: 100%;">
                                            <img alt="{{$data->name}}" src="{{asset('storage/' .$data->photo)}}" data-holder-rendered="true">
                                        </div>
                                        <div>
                                            <span class="btn btn-default btn-file"><span class="fileinput-new">Select image</span><span class="fileinput-exists">Change</span><input type="file" name="photo"></span>
                                            <a href="#" class="btn btn-default fileinput-exists" data-dismiss="fileinput">Remove</a>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="row m-b">
                        <div class="form-group">
                            <label class="col-md-2 control-label">Trạng Thái</label>
                            <div class="col-md-3">
                                <select class="form-control" name="active">
                                    <option @if(isset($data->active) && $data->active === ACTIVE || old('active') === ACTIVE) selected @endif value="{{ACTIVE}}">Đã kích hoạt</option>
                                    <option @if(isset($data->active) && $data->active === INACTIVE || old('active') === INACTIVE) selected @endif value="{{INACTIVE}}">Chưa kích hoạt</option>
                                </select>
                            </div>
                        </div>
                    </div>


                    <div class="text-right">
                        <a href="{{route('admin.photos.index')}}" class="btn btn-default"><i class="fa fa-arrow-circle-o-left"></i> Trở lại</a>
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