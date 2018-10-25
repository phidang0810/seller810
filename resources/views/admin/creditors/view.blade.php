@extends('admin.layouts.inspinia.master')

@section('title', $title)

@section('js')
    <script></script>
    <!-- Page-Level Scripts -->
    <script>

        $(document).ready(function() {

            $("#bt-reset").click(function(){
                $("#mainForm")[0].reset();
            });

            $("#mainForm").validate();

            new Cleave('.input-phone', {
                phone: true,
                phoneRegionCode: 'VN'
            });

            new Cleave('.input-price', {
                numeral: true,
                numeralThousandsGroupStyle: 'thousand'
            });
            new Cleave('.input-price2', {
                numeral: true,
                numeralThousandsGroupStyle: 'thousand'
            });

            $('.date_picker').datepicker({
                keyboardNavigation: false,
                forceParse: false,
                autoclose: true,
                format: 'dd/mm/yyyy'
            });

            $('select[name="supplier_id"]').change(function(){
                $('input[name="supplier_code"]').val($(this).find('option:selected').attr('data-code'));
                $('input[name="supplier_address"]').val($(this).find('option:selected').attr('data-address'));
            });
            $('input[name="supplier_code"]').val($('select[name="supplier_id"]').find('option:selected').attr('data-code'));
            $('input[name="supplier_address"]').val($('select[name="supplier_id"]').find('option:selected').attr('data-address'));
            $('#full_paid').click(function(){
                $('input[name="paid"]').val($('input[name="total"]').val());
            })
        });
    </script>
@endsection
@section('content')
<div class="row">
    <div class="col-lg-12">
        <div class="ibox float-e-margins pl-15 pr-15">
            @include('admin._partials._alert')
            <form role="form" method="POST" id="mainForm" action="{{route('admin.creditors.store')}}" enctype="multipart/form-data">
                {{ csrf_field() }}
                @if (isset($data->id))
                    <input type="hidden" name="id" value="{{$data->id}}" />
                @endif
                <div class="ibox-content" style="padding: 20px;">
                    @if(isset($data))
                        <div class="row m-b">
                            <div class="form-group">
                                <label class="col-md-2 control-label">Mã Phiếu Nợ</label>
                                <div class="col-md-5">
                                    <input type="text" readonly name="code" class="form-control required" value="@if(isset($data->code)){{$data->code}}@else{{old('code')}}@endif"/>
                                </div>
                            </div>
                        </div>
                    @endif

                    <div class="row m-b">
                        <div class="form-group">
                            <label class="col-md-2 control-label">Nhà Cung Cấp (<span class="text-danger">*</span>)</label>
                            <div class="col-md-5">
                                <select class="form-control" name="supplier_id">
                                    <option value="0">-- Chọn nhà khách hàng --</option>
                                    @foreach($suppliers as $item)
                                        <option value="{{$item->id}}" @if(isset($data->supplier_id) && $data->supplier_id === $item->id) selected @endif data-code="{{$item->code}}" data-address="{{$item->address}}">{{$item->name}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="row m-b">
                        <div class="form-group">
                            <label class="col-md-2 control-label">Mã Số Thuế</label>
                            <div class="col-md-5">
                                <input type="text" name="supplier_code" readonly class="form-control" value=""/>
                            </div>
                        </div>
                    </div>

                    <div class="row m-b">
                        <div class="form-group">
                            <label class="col-md-2 control-label">Địa Chỉ</label>
                            <div class="col-md-5">
                                <input type="text" name="supplier_address" readonly class="form-control" value=""/>
                            </div>
                        </div>
                    </div>

                    <div class="row m-b">
                        <div class="form-group">
                            <label class="col-md-2 control-label">Tên Người Trả (<span class="text-danger">*</span>)</label>
                            <div class="col-md-5">
                                <input type="text" name="full_name" placeholder="Nhập tên người đại diện" class="form-control required" value="@if(isset($data->full_name)){{$data->full_name}}@else{{old('full_name')}}@endif"/>
                            </div>
                        </div>
                    </div>

                    <div class="row m-b">
                        <div class="form-group">
                            <label class="col-md-2 control-label">Điện Thoại (<span class="text-danger">*</span>)</label>
                            <div class="col-md-5">
                                <input type="text" name="phone" placeholder="VD: 090 934 128" class="form-control input-phone required" value="@if(isset($data->phone)){{$data->phone}}@else{{old('phone')}}@endif"/>
                            </div>
                        </div>
                    </div>

                    <div class="row m-b">
                        <div class="form-group">
                            <label class="col-md-2 control-label">Tổng Nợ (VND) (<span class="text-danger">*</span>)</label>
                            <div class="col-md-3">
                                <input type="text" name="total" placeholder="VD: 10.000" @if(isset($data)) readonly @endif class="form-control input-price required" value="@if(isset($data->total)){{$data->total}}@else{{old('total')}}@endif"/>
                            </div>
                        </div>
                    </div>

                    <div class="row m-b">
                        <div class="form-group">
                            <label class="col-md-2 control-label">Số Tiền Đã Trả (VND) (<span class="text-danger">*</span>)</label>
                            <div class="col-md-3">
                                <div class="input-group">
                                    <input type="text" name="paid" placeholder="VD: 10.000" class="form-control input-price2 required" value="@if(isset($data->paid)){{$data->paid}}@else{{old('paid')}}@endif"/>
                                    <span class="input-group-btn">
                                    <button id="full_paid" type="button" class="btn btn-default"><i class="fa fa-check" aria-hidden="true"></i> Trả đủ</button>
                                  </span>
                                </div>

                            </div>
                        </div>
                    </div>

                    <div class="row m-b">
                        <div class="form-group">
                            <label class="col-md-2 control-label">Ngày Nợ (<span class="text-danger">*</span>)</label>
                            <div class="col-md-3">
                                <input type="text" name="date" class="form-control date_picker required" @if(isset($data)) readonly @endif value="@if(isset($data->date)){{\Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $data->date)->format('d/m/Y')}}@else{{old('date')}}@endif"/>
                            </div>
                        </div>
                    </div>

                    <div class="row m-b">
                        <div class="form-group">
                            <label class="col-md-2 control-label">Ngày Trả (<span class="text-danger">*</span>)</label>
                            <div class="col-md-3">
                                <input type="text" name="paid_date" class="form-control date_picker required" value="@if(isset($data->paid_date)){{\Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $data->paid_date)->format('d/m/Y')}}@else{{old('paid_date')}}@endif"/>
                            </div>
                        </div>
                    </div>

                    <div class="row m-b">
                        <div class="form-group">
                            <label class="col-md-2 control-label">Ghi Chú</label>
                            <div class="col-md-5">
                                <textarea name="note" rows="5" class="form-control">@if(isset($data->note)){{$data->note}}@else{{old('note')}}@endif</textarea>
                            </div>
                        </div>
                    </div>

                    <div class="text-right">
                        <a href="{{route('admin.creditors.index')}}" class="btn btn-default"><i class="fa fa-arrow-circle-o-left"></i> Trở lại</a>
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