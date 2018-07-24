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
            $("#photo").change(function() {

            });

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
            <form role="form" method="POST" id="mainForm" action="{{route('admin.users.store')}}" enctype="multipart/form-data">
                {{ csrf_field() }}
                @if (isset($data->id))
                    <input type="hidden" name="id" value="{{$data->id}}" />
                @endif
                <div class="ibox-content" style="padding: 20px;">
                    <div class="row">
                        <div class="form-group">
                            <label class="col-md-2 control-label">Chọn quyền</label>
                            <div class="col-md-3">
                                <select class="form-control m-b" name="role_id">
                                @foreach($roles as $role)
                                    <option value="{{$role->id}}" @if (isset($data->role_id) && $data->role_id === $role->id || old('role_id') === $role->id) selected @endif >{{$role->name}}</option>
                                @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="row m-b">
                        <div class="form-group">
                            <label class="col-md-2 control-label">Ảnh đại diện</label>
                            <div class="col-md-5">
                                @if(isset($data->avatar))
                                    <img alt="400x400" id="preview" data-src="holder.js/400x400" style="margin-bottom: 10px;width: 140px; height: 140px;" class="img-thumbnail" src="{{asset('storage/' .$data->avatar)}}" data-holder-rendered="true">
                                @else
                                    <img alt="400x400" id="preview" data-src="holder.js/400x400" style="margin-bottom: 10px;width: 140px; height: 140px;" class="img-thumbnail" src="data:image/svg+xml;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0iVVRGLTgiIHN0YW5kYWxvbmU9InllcyI/PjxzdmcgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIiB3aWR0aD0iMTQwIiBoZWlnaHQ9IjE0MCIgdmlld0JveD0iMCAwIDE0MCAxNDAiIHByZXNlcnZlQXNwZWN0UmF0aW89Im5vbmUiPjwhLS0KU291cmNlIFVSTDogaG9sZGVyLmpzLzE0MHgxNDAKQ3JlYXRlZCB3aXRoIEhvbGRlci5qcyAyLjYuMC4KTGVhcm4gbW9yZSBhdCBodHRwOi8vaG9sZGVyanMuY29tCihjKSAyMDEyLTIwMTUgSXZhbiBNYWxvcGluc2t5IC0gaHR0cDovL2ltc2t5LmNvCi0tPjxkZWZzPjxzdHlsZSB0eXBlPSJ0ZXh0L2NzcyI+PCFbQ0RBVEFbI2hvbGRlcl8xNjRjN2E0MDNlNSB0ZXh0IHsgZmlsbDojQUFBQUFBO2ZvbnQtd2VpZ2h0OmJvbGQ7Zm9udC1mYW1pbHk6QXJpYWwsIEhlbHZldGljYSwgT3BlbiBTYW5zLCBzYW5zLXNlcmlmLCBtb25vc3BhY2U7Zm9udC1zaXplOjEwcHQgfSBdXT48L3N0eWxlPjwvZGVmcz48ZyBpZD0iaG9sZGVyXzE2NGM3YTQwM2U1Ij48cmVjdCB3aWR0aD0iMTQwIiBoZWlnaHQ9IjE0MCIgZmlsbD0iI0VFRUVFRSIvPjxnPjx0ZXh0IHg9IjQ0LjA1NDY4NzUiIHk9Ijc0LjUiPjE0MHgxNDA8L3RleHQ+PC9nPjwvZz48L3N2Zz4=" data-holder-rendered="true">
                                @endif
                                    <input type="file" onchange="readURL(this)" id="photo" name="avatar" class="form-control"/>
                            </div>
                        </div>
                    </div>

                    <div class="row m-b">
                        <div class="form-group">
                            <label class="col-md-2 control-label">Email (<span class="text-danger">*</span>)</label>
                            <div class="col-md-5">
                                <input type="email" name="email" placeholder="example@yopmail.com" class="form-control required email" value="@if(isset($data->email)){{$data->email}}@else{{old('email')}}@endif"/>
                            </div>
                        </div>
                    </div>

                    <div class="row m-b">
                        <div class="form-group">
                            <label class="col-md-2 control-label">Họ Tên (<span class="text-danger">*</span>)</label>
                            <div class="col-md-5">
                                <input type="text" name="full_name" placeholder="Nhập tên của bạn" class="form-control required" value="@if(isset($data->full_name)){{$data->full_name}}@else{{old('full_name')}}@endif"/>
                            </div>
                        </div>
                    </div>

                    <div class="row m-b">
                        <div class="form-group">
                            <label class="col-md-2 control-label">Mật Khẩu (<span class="text-danger">*</span>)</label>
                            <div class="col-md-5">
                                <input type="password" id="password" name="password" placeholder="password" class="form-control required" @if(isset($data->password)) value="{{$data->password}}" @endif/>
                            </div>
                        </div>
                    </div>

                    <div class="row m-b">
                        <div class="form-group">
                            <label class="col-md-2 control-label">Nhập Lại Mật Khẩu (<span class="text-danger">*</span>)</label>
                            <div class="col-md-5">
                                <input type="password" name="password_confirmation" placeholder="password" class="form-control required" @if(isset($data->password)) value="{{$data->password}}" @endif/>
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
                        <a href="{{route('admin.users.index')}}" class="btn btn-default"><i class="fa fa-arrow-circle-o-left"></i> Trở lại</a>
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