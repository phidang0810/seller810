@extends('admin.layouts.inspinia.master')

@section('title', $title)

@section('js')
<!-- Page-Level Scripts -->
<script>
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    $(document).ready(function () {
        $("#bt-reset").click(function () {
            $("#mainForm")[0].reset();
        })

        $("#mainForm").validate();
    });
</script>
@endsection
@section('content')
<div class="row">
    <div class="col-lg-12">
        <div class="ibox float-e-margins pl-15 pr-15">
            @include('admin._partials._alert')
            <form role="form" method="POST" id="mainForm" action="{{route('admin.brands.store')}}" enctype="multipart/form-data">
                {{ csrf_field() }}
                @if (isset($data->id))
                <input type="hidden" name="id" value="{{$data->id}}"/>
                @endif
                <div class="ibox-content">

                    <div class="row">
                        <div class="form-group">
                            <label class="col-md-2 control-label">Tên Thương Hiệu (<span class="text-danger">*</span>)</label>
                            <div class="col-md-5">
                                <input type="text" name="name" placeholder="" class="form-control required m-b"
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
                        <a href="{{route('admin.warehouses.index')}}" class="btn btn-default"><i class="fa fa-arrow-circle-o-left"></i> Trở lại</a>
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