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
            });

            $.validator.addMethod("valueNotEquals", function(value, element, arg){
                return arg !== value;
            }, "Value must not equal arg.");

            $("#mainForm").validate({
                rules: {
                    group_id: { valueNotEquals: "0" }
                },
                messages: {
                    group_id: { valueNotEquals: "Vui lòng chọn!" }
                }
            });

            new Cleave('.input-price', {
                numeral: true,
                numeralThousandsGroupStyle: 'thousand'
            });
        });
    </script>
@endsection
@section('content')
<div class="row">
    <div class="col-lg-12">
        <div class="ibox float-e-margins pl-15 pr-15">
            @include('admin._partials._alert')
            <form role="form" method="POST" id="mainForm" action="{{route('admin.payslips.store')}}" enctype="multipart/form-data">
                {{ csrf_field() }}
                @if (isset($data->id))
                    <input type="hidden" name="id" value="{{$data->id}}" />
                @endif
                <div class="ibox-content" style="padding: 20px;">
                    <div class="row m-b">
                        <div class="form-group">
                            <label class="col-md-2 control-label">Loại Phiếu Chi (<span class="text-danger">*</span>)</label>
                            <div class="col-md-5">
                                <select name="group_id" class="form-control">
                                    <option value="0">-- Chọn loại phiếu chi --</option>
                                    @foreach($groups as $group)
                                        <option value="{{$group->id}}" @if(isset($data->group_id) && $data->group_id === $group->id) selected @endif>{{$group->name}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    @if(isset($data))
                    <div class="row m-b">
                        <div class="form-group">
                            <label class="col-md-2 control-label">Mã Phiếu Chi (<span class="text-danger">*</span>)</label>
                            <div class="col-md-3">
                                <input type="text" name="code" readonly class="form-control" value="@if(isset($data->code)){{$data->code}}@else{{old('code')}}@endif"/>
                            </div>
                        </div>
                    </div>
                    @endif

                    <div class="row m-b">
                        <div class="form-group">
                            <label class="col-md-2 control-label">Chi Phí (VND)</label>
                            <div class="col-md-3">
                                <input type="text" name="price" placeholder="VD: 10.000" class="form-control input-price" value="@if(isset($data->price)){{$data->price}}@else{{old('price')}}@endif"/>
                            </div>
                        </div>
                    </div>

                    <div class="row m-b">
                        <div class="form-group">
                            <label class="col-md-2 control-label">Mô Tả</label>
                            <div class="col-md-5">
                                <textarea cols="6" rows="4" name="description" placeholder="Nhập mô tả" class="form-control">@if(isset($data->description)){{$data->description}}@else{{old('description')}}@endif</textarea>
                            </div>
                        </div>
                    </div>

                    <div class="row m-b">
                        <div class="form-group">
                            <label class="col-md-2 control-label">Tình Trạng</label>
                            <div class="col-md-3">
                                <select class="form-control" name="status">
                                    @foreach($status as $key => $item)
                                        <option value="{{$key}}" @if(isset($data->status) && $data->status == $key) selected @endif>{{$item}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="text-right">
                        <a href="{{route('admin.payslips.index')}}" class="btn btn-default"><i class="fa fa-arrow-circle-o-left"></i> Trở lại</a>
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