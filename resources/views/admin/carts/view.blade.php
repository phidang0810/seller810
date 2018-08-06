@extends('admin.layouts.inspinia.master')

@section('title', $title)

@section('css')
<style type="text/css">
@media (min-width: 768px){
    #page-wrapper {
        margin: 0 0 0 0px;
    }
}
</style>
@endsection

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
            <form role="form" method="POST" id="mainForm" action="{{route('admin.carts.store')}}">
                {{ csrf_field() }}
                @if (isset($data->id))
                <input type="hidden" name="id" value="{{$data->id}}" />
                @endif
                <div class="row">
                    <div class="col-md-8">
                        <div class="ibox-content">

                            <div class="row">
                                <div class="col-md-12">
                                    <h2>{{ $title }}</h2>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-4">
                                    <select name="product_name" class="form-control">
                                        <option value="0"> -- Chọn sản phẩm -- </option>
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <select name="product_color" class="form-control">
                                        <option value="0"> -- Chọn màu sắc -- </option>
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <select name="product_size" class="form-control">
                                        <option value="0"> -- Chọn kích thước -- </option>
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <input type="text" name="quantity" placeholder="Nhập số lượng" class="form-control m-b"
                                        value=""/>
                                </div>
                                <div class="col-md-2">
                                    <button type="button" class="btn btn-success pull-left c-add-info" id="add_cart_details">Thêm</button>
                                </div>
                            </div>

                            <div class="row">
                                <div class="form-group">
                                    <div class="col-md-12">
                                        <table id="i-cart-info" class="table">
                                            <thead>
                                                <tr>
                                                    <th>Hình ảnh</th>
                                                    <th>Tên sản phẩm</th>
                                                    <th>Số lượng</th>
                                                    <th>Mã sản phẩm</th>
                                                    <th>Size</th>
                                                    <th>Giá</th>
                                                    <th>Giá tùy chỉnh</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                            </tbody>
                                            <tfoot>
                                            </tfoot>
                                        </table>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="ibox-content">

                            <div class="row">
                                <div class="col-md-12">
                                    <h2>Thông tin khách hàng</h2>
                                </div>
                            </div>

                            <div class="row">
                                <div class="form-group">
                                    <label class="col-md-4 control-label">Số điện thoại (<span class="text-danger">*</span>)</label>
                                    <div class="col-md-8">
                                        <select name="customer_phone" class="form-control required m-b">
                                            <option value="" selected>-- Chọn số điện thoại --</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="form-group">
                                    <label class="col-md-4 control-label">Tên khách hàng (<span class="text-danger">*</span>)</label>
                                    <div class="col-md-8">
                                        <input type="text" name="customer_name" placeholder="" class="form-control required m-b"
                                        value=""/>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="form-group">
                                    <label class="col-md-4 control-label">Email</label>
                                    <div class="col-md-8">
                                        <input type="text" name="customer_email" placeholder="" class="form-control m-b"
                                        value=""/>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="form-group">
                                    <label class="col-md-4 control-label">Địa chỉ (<span class="text-danger">*</span>)</label>
                                    <div class="col-md-8">
                                        <input type="text" name="customer_address" placeholder="" class="form-control required m-b"
                                        value=""/>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="form-group">
                                    <label class="col-md-4 control-label">Thành phố (<span class="text-danger">*</span>)</label>
                                    <div class="col-md-8">
                                        <input type="text" name="customer_city" placeholder="" class="form-control required m-b"
                                        value=""/>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-12">
                                    <h2>Thông tin khác</h2>
                                </div>
                            </div>

                            <div class="row">
                                <div class="form-group">
                                    <label class="col-md-4 control-label">Dịch vụ vận chuyển</label>
                                    <div class="col-md-8">
                                        <select name="transporting_service" class="form-control m-b">
                                            <option value="" selected>-- Chọn dịch vụ vận chuyển --</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="form-group">
                                    <label class="col-md-4 control-label">Phí vận chuyển</label>
                                    <div class="col-md-8">
                                        <input type="text" name="shipping_fee" placeholder="" class="form-control m-b"
                                        value=""/>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="form-group">
                                    <label class="col-md-4 control-label">Cộng tác viên</label>
                                    <div class="col-md-8">
                                        <select name="partner" class="form-control m-b">
                                            <option value="" selected>-- Chọn cộng tác viên --</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="form-group">
                                    <label class="col-md-4 control-label">Chiết khấu cộng tác viên</label>
                                    <div class="col-md-8">
                                        <input type="text" name="partner_discount_percent" placeholder="" class="form-control m-b"
                                        value="" readonly="readonly" />
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="form-group">
                                    <label class="col-md-4 control-label">Chiết khấu khách hàng</label>
                                    <div class="col-md-8">
                                        <input type="text" name="customer_discount_percent" placeholder="" class="form-control m-b"
                                        value="" readonly="readonly" />
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="form-group">
                                    <label class="col-md-4 control-label">Thuế</label>
                                    <div class="col-md-8">
                                        <input type="text" name="vat_percent" placeholder="" class="form-control m-b"
                                        value="10%" readonly="readonly" />
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="form-group">
                                    <label class="col-md-4 control-label">Khách hàng trả trước</label>
                                    <div class="col-md-8">
                                        <input type="text" name="prepaid" placeholder="" class="form-control m-b"
                                        value="" />
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="form-group">
                                    <label class="col-md-4 control-label">Ghi chú</label>
                                    <div class="col-md-8">
                                        <textarea class="form-control" name="notes"></textarea>
                                    </div>
                                </div>
                            </div>

                            <div style="margin-top: 40px;"></div>

                            <div class="row">
                                <div class="form-group">
                                    <label class="col-md-4 control-label">Tổng cộng</label>
                                    <div class="col-md-8">
                                        <input type="text" name="total_amount" placeholder="" class="form-control m-b"
                                        value="" readonly="readonly" />
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="form-group">
                                    <label class="col-md-4 control-label">Trả trước</label>
                                    <div class="col-md-8">
                                        <input type="text" name="prepaid" placeholder="" class="form-control m-b"
                                        value="" readonly="readonly" />
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="form-group">
                                    <label class="col-md-4 control-label">Tổng số lượng</label>
                                    <div class="col-md-8">
                                        <input type="text" name="total_quantity" placeholder="" class="form-control m-b"
                                        value="" readonly="readonly" />
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="form-group">
                                    <label class="col-md-4 control-label">Phí vận chuyển</label>
                                    <div class="col-md-8">
                                        <input type="text" name="shipping_fee" placeholder="" class="form-control m-b"
                                        value="" readonly="readonly" />
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="form-group">
                                    <label class="col-md-4 control-label">Thuế</label>
                                    <div class="col-md-8">
                                        <input type="text" name="vat_amount" placeholder="" class="form-control m-b"
                                        value="10%" readonly="readonly" />
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="form-group">
                                    <label class="col-md-4 control-label">Tổng chiết khấu</label>
                                    <div class="col-md-8">
                                        <input type="text" name="total_discount_amount" placeholder="" class="form-control m-b"
                                        value="" readonly="readonly" />
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="form-group">
                                    <label class="col-md-4 control-label">Thành tiền</label>
                                    <div class="col-md-8">
                                        <input type="text" name="price" placeholder="" class="form-control m-b"
                                        value="" readonly="readonly" />
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-12">
                                    <div class="text-right">
                                        <button type="button" class="btn btn-default" id="bt-reset"><i class="fa fa-refresh"></i> Làm mới</button>
                                        <button type="submit" name="action" class="btn btn-success" value="save"><i class="fa fa-save"></i> Lưu</button>
                                        <button type="submit" name="action" class="btn btn-primary" value="save_quit"><i class="fa fa-save"></i> Lưu &amp; In</button>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection