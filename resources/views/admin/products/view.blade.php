@extends('admin.layouts.inspinia.master')

@section('title', $title)

@section('js')
    <!-- Page-Level Scripts -->
    <script>
        function deleteProductInfoItem(elRow) {
            var tblRow = elRow.closest('tr');
            if (tblRow !== null) {
                tblRow.remove();
                calculateQuantity();
            }
        }

        var sum = 0;
        function calculateQuantity(){
            // iterate through each td based on class and add the values
            $(".c-quantity").each(function() {

                var value = $(this).text();
                // add only if the value is number
                if(!isNaN(value) && value.length != 0) {
                    sum += parseFloat(value);
                }

            });
            console.log(sum);
            $('.c-total-quantities').text(sum);
            $('.c-quatity-input').val(sum);
            sum = 0;
        }

        $(document).ready(function () {
            $("#bt-reset").click(function () {
                $("#mainForm")[0].reset();
            })

            //---> Init summer note
            $('.summernote').summernote();

            //---> Add row for table
            var elColorVal = "";
            var elSizeVal = "";
            var elQuantityVal = "";

            function resetVal(){
                $("#i-color-selection").val("");
                $("#i-size-selection").val("");
                $("#i-quantity-input").val("");
                elColorVal = "";
                elSizeVal = "";
                elQuantityVal = "";
            }

            $("#i-color-selection").change(function(){
                elColorVal = $(this).val();
                console.log(elColorVal);
            });

            $("#i-size-selection").change(function(){
                elSizeVal = $(this).val();
                console.log(elSizeVal);
            });

            $("#i-quantity-input").change(function(){
                elQuantityVal = $(this).val();
                console.log(elQuantityVal);
            });
            
            var index = 0;
            $('.c-add-info').click(function(){
                if (elColorVal != "" && elSizeVal != "" && elQuantityVal != "") {
                    $('#i-product-info tbody').prepend('<tr class="child '+index+'"><td><a class="'+index+'" href="javascript:;" onclick="deleteProductInfoItem(this);">Delete</a></td><td>'+elColorVal+'</td><td>'+elSizeVal+'</td><td class="c-quantity">'+elQuantityVal+'</td></tr>');
                    calculateQuantity();
                    index++;
                    resetVal();
                }else{
                    alert("Nhập sai rồi Phước ơi");
                }
            });

            $('.c-total-quantities').text(sum);
            
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
                    <div class="ibox-content">
                        <div class="row">
                            <div class="form-group">
                                <label class="col-md-2 control-label">Tên sản phẩm (<span
                                            class="text-danger">*</span>)</label>
                                <div class="col-md-5">
                                    <input type="text" name="name" placeholder="" class="form-control m-b"
                                           value="@if(isset($data->name)){{$data->name}}@else{{old('name')}}@endif"/>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="form-group">
                                <label class="col-md-2 control-label">Mã sản phẩm</label>
                                <div class="col-md-5">
                                    <input type="text" name="code" placeholder="" class="form-control m-b"
                                           value="@if(isset($data->code)){{$data->code}}@else{{old('code')}}@endif"/>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="form-group">
                                <label class="col-md-2 control-label">Số lượng</label>
                                <div class="col-md-5">
                                    <input disabled type="text" name="quantity" placeholder="0" class="form-control m-b c-quatity-input"
                                           value="@if(isset($data->quantity)){{$data->quantity}}@else{{old('quantity')}}@endif"/>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="form-group">
                                <label class="col-md-2 control-label">Barcode</label>
                                <div class="col-md-5">
                                    <input type="text" name="barcode" placeholder="" class="form-control m-b"
                                           value="@if(isset($data->barcode)){{$data->barcode}}@else{{old('barcode')}}@endif"/>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="form-group">
                                <label class="col-md-2 control-label">Đơn giá</label>
                                <div class="col-md-5">
                                    <input type="text" name="price" placeholder="" class="form-control m-b"
                                           value="@if(isset($data->price)){{$data->price}}@else{{old('price')}}@endif"/>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="form-group">
                                <label class="col-md-2 control-label">Giá bán</label>
                                <div class="col-md-5">
                                    <input type="text" name="sell_price" placeholder="" class="form-control m-b"
                                           value="@if(isset($data->sell_price)){{$data->sell_price}}@else{{old('sell_price')}}@endif"/>
                                </div>
                            </div>
                        </div>

                        <div class="row hidden">
                            <div class="form-group">
                                <label class="col-md-2 control-label">Kích thước</label>
                                <div class="col-md-5">
                                    <select name="sizes" id="" class="form-control m-b">
                                        <option value="">-- Chọn kích thước --</option>
                                        <option value="">Size XL</option>
                                        <option value="">Size L</option>
                                        <option value="">Size M</option>
                                        <option value="">Size S</option>
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
                                <label class="col-md-2 control-label">Nội dung</label>
                                <div class="col-md-5">
                                    <div class="summernote form-control m-b">@if(isset($data->content)){{$data->content}}@else{{old('content')}}@endif</div>
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
                                <div class="col-md-2">
                                    <button type="button" class="btn btn-success pull-right c-add-info">Thêm</button>
                                </div>
                                <div class="col-md-2">
                                    <select name="color" id="i-color-selection" class="form-control">
                                        <option value="" disabled selected>-- Chọn màu --</option>
                                        <option value="Đỏ">Đỏ</option>
                                        <option value="Trắng">Trắng</option>
                                        <option value="Vàng">Vàng</option>
                                        <option value="Hồng">Hồng</option>
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <select name="sizes" id="i-size-selection" class="form-control">
                                        <option value="" disabled selected>-- Chọn kích thước --</option>
                                        <option value="Lớn">Lớn</option>
                                        <option value="Nhỏ">Nhỏ</option>
                                        <option value="Vừa">Vừa</option>
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <input id="i-quantity-input" type="text" name="quantity" placeholder="0" class="form-control m-b"
                                           value=""/>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="form-group">
                                <div class="col-md-offset-2 col-md-6">
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
                                            <tr>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td>Tổng số lượng: <span class="c-total-quantities"></span></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <div class="text-right">
                            <a href="{{route('admin.products.index')}}" class="btn btn-default"><i
                                        class="fa fa-arrow-circle-o-left"></i> Trở lại</a>
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
                </form>
            </div>
        </div>
    </div>
@endsection