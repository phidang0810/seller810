@extends('admin.layouts.inspinia.master')

@section('title', $title)

@section('js')
<!-- Page-Level Scripts -->
<script>
    $(document).ready(function() {
        $("#bt-reset").click(function(){
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
            <form role="form" method="POST" id="mainForm" action="{{route('admin.categories.store')}}">
                {{ csrf_field() }}
                @if (isset($data->id))
                <input type="hidden" name="id" value="{{$data->id}}" />
                @endif
                <div class="ibox-content">
                    <div class="row">
                        <div class="col-md-8">
                            <div class="row">
                                <div class="form-group">
                                    <label class="col-md-2 control-label">Tên danh mục (<span class="text-danger">*</span>)</label>
                                    <div class="col-md-5">
                                        <input type="text" name="name" placeholder="" class="form-control required m-b" value="@if(isset($data->name)){{$data->name}}@else{{old('name')}}@endif"/>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="form-group">
                                    <label class="col-md-2 control-label">Danh mục cha</label>
                                    <div class="col-md-5">
                                        <select class="form-control m-b" name="parent_id">
                                            <option value="0">Chọn danh mục cha</option>
                                            {!! $categoriesTree !!}
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="form-group">
                                    <label class="col-md-2 control-label">Mô tả</label>
                                    <div class="col-md-5">
                                        <textarea name="description" id="" cols="30" rows="10"  class="form-control m-b">@if(isset($data->description)){{$data->description}}@else{{old('description')}}@endif</textarea>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="form-group">
                                    <label class="col-md-2 control-label">Thứ tự</label>
                                    <div class="col-md-5">
                                        <input type="text" name="order" placeholder="" class="form-control m-b" value="@if(isset($data->order)){{$data->order}}@else{{old('order')}}@endif"/>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="form-group">
                                    <label class="col-md-2 control-label">Trạng thái</label>
                                    <div class="col-md-3">
                                        <select class="form-control" name="active">
                                            <option @if(isset($data->active) && $data->active === ACTIVE || old('active') === ACTIVE) selected @endif value="{{ACTIVE}}">Đã kích hoạt</option>
                                            <option @if(isset($data->active) && $data->active === INACTIVE || old('active') === INACTIVE) selected @endif value="{{INACTIVE}}">Chưa kích hoạt</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <h5>Danh mục sản phẩm</h5>
                            <div class="list-tree-section m-b">
                                {!! $categoriesTreeList !!}
                            </div>
                        </div>

                        <div class="col-md-12">
                            <div class="text-right">
                                <a href="{{route('admin.categories.index')}}" class="btn btn-default"><i class="fa fa-arrow-circle-o-left"></i> Trở lại</a>
                                <button type="button" class="btn btn-default" id="bt-reset"><i class="fa fa-refresh"></i> Làm mới</button>
                                <button type="submit" name="action" class="btn btn-primary" value="save"><i class="fa fa-save"></i> Lưu</button>
                                <button type="submit" name="action" class="btn btn-warning" value="save_quit"><i class="fa fa-save"></i> Lưu &amp; Thoát</button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection