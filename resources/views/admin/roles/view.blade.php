@extends('admin.layouts.inspinia.master')

@section('title', $title)

@section('js')
    <script></script>
    <!-- Page-Level Scripts -->
    <script>
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
            })

            $("#mainForm").validate({
                rules: {
                    password_confirmation: {
                        equalTo: "#password"
                    },
                    password:{
                        minlength:3
                    }
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
            <form role="form" method="POST" id="mainForm" action="{{route('admin.roles.store')}}" enctype="multipart/form-data">
                {{ csrf_field() }}
                @if (isset($data->id))
                    <input type="hidden" name="id" value="{{$data->id}}" />
                @endif
                <div class="ibox-content" style="padding: 20px;">

                    <div class="row m-b">
                        <div class="form-group">
                            <label class="col-md-2 control-label">Tên Phòng Ban (<span class="text-danger">*</span>)</label>
                            <div class="col-md-5">
                                <input type="text" name="name" placeholder="Nhập tên phòng ban" class="form-control required" value="@if(isset($data->name)){{$data->name}}@else{{old('name')}}@endif"/>
                            </div>
                        </div>
                    </div>

                    <div class="row m-b">
                        <div class="form-group">
                            <label class="col-md-2 control-label">Chọn Quyền (<span class="text-danger">*</span>)</label>
                            <div class="col-md-10">
                                <div class="row">
                                    @foreach($permissions as $permission)
                                        <div class="col-md-4">
                                            <input type="checkbox" name="permissions[]" value="{{$permission->id}}" @if(in_array($permission->id, $myPermissions)) checked @endif /> <label>{{$permission->name}}</label>
                                        </div>
                                    @endforeach
                                </div>
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
                        <a href="{{route('admin.roles.index')}}" class="btn btn-default"><i class="fa fa-arrow-circle-o-left"></i> Trở lại</a>
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