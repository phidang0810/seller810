@extends('admin.layouts.inspinia.master')

@section('title', $title)

@section('js')
    <!-- Page-Level Scripts -->
    <script>
        $(document).ready(function() {
            $("#bt-reset").click(function(){
                $("#mainForm")[0].reset();
            })
        });
    </script>
@endsection
@section('content')
<div class="row">
    <div class="col-lg-12">
        <div class="ibox float-e-margins">
            @include('admin._partials._alert')
            <form role="form" method="POST" id="mainForm" action="{{route('admin.users.store')}}">
                {{ csrf_field() }}
                @if (isset($data->id))
                    <input type="hidden" name="id" value="{{$data->id}}" />
                @endif
                <div class="ibox-content">
                    <div class="row">
                        <div class="form-group">
                            <label class="col-md-2 control-label">Role</label>
                            <div class="col-md-3">
                                <select class="form-control m-b" name="role_id">
                                @foreach($roles as $role)
                                    <option value="{{$role->id}}" @if (isset($data->role_id) && $data->role_id === $role->id || old('role_id') === $role->id) selected @endif >{{$role->name}}</option>
                                @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="form-group">
                            <label class="col-md-2 control-label">Email (<span class="text-danger">*</span>)</label>
                            <div class="col-md-5">
                                <input type="email" name="email" placeholder="example@yopmail.com" class="form-control m-b" value="@if(isset($data->email)){{$data->email}}@else{{old('email')}}@endif"/>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="form-group">
                            <label class="col-md-2 control-label">Name (<span class="text-danger">*</span>)</label>
                            <div class="col-md-2">
                                <input type="text" name="first_name" placeholder="First name" class="form-control m-b" value="@if(isset($data->first_name)){{$data->first_name}}@else{{old('first_name')}}@endif"/>
                            </div>
                            <div class="col-md-3">
                                <input type="text" name="last_name" placeholder="Last name" class="form-control m-b" value="@if(isset($data->last_name)){{$data->last_name}}@else{{old('last_name')}}@endif"/>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="form-group">
                            <label class="col-md-2 control-label">Password (<span class="text-danger">*</span>)</label>
                            <div class="col-md-5">
                                <input type="password" name="password" placeholder="password" class="form-control m-b" @if(isset($data->password)) value="{{$data->password}}" @endif/>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="form-group">
                            <label class="col-md-2 control-label">Status</label>
                            <div class="col-md-3">
                                <select class="form-control" name="active">
                                    <option @if(isset($data->active) && $data->active === ACTIVE || old('active') === ACTIVE) selected @endif value="{{ACTIVE}}">Active</option>
                                    <option @if(isset($data->active) && $data->active === INACTIVE || old('active') === INACTIVE) selected @endif value="{{INACTIVE}}">Inactive</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="text-right">
                        <a href="{{route('admin.users.index')}}" class="btn btn-default"><i class="fa fa-arrow-circle-o-left"></i> Back to List</a>
                        <button type="button" class="btn btn-default" id="bt-reset"><i class="fa fa-refresh"></i> Reset</button>
                        <button type="submit" class="btn btn-primary"><i class="fa fa-save"></i> Save</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection