<div id="print-section">
    <div class="col-sm-12">
        <div class="table-wrapper">
            <table class="table bordered tbl-list-product table-borderless">
                <thead>
                    <tr>
                        <th colspan="6">
                            @include('admin._partials._print_header')

                            <div class="row" style="border-top: 2px solid #333; margin-top: 10px;">
                                <div class="col-md-12">
                                    <h2 class="text-center font-bold text-uppercase">đơn hàng</h2>
                                </div>
                            </div>
                            <div style="margin-top: 10px;">
                                <div class="row">
                                    <div class="col-sm-6" >
                                        <div class="form-group">
                                            <label class="col-sm-4 control-label font-bold">Tên khách hàng:</label>
                                            <label class="col-sm-8 control-label lbl-customer-name"></label>
                                        </div>
                                    </div>
                                    <div class="col-sm-6" >
                                        <div class="form-group">
                                            <label class="col-sm-4 control-label font-bold">Ngày mua:</label>
                                            <label class="col-sm-8 control-label lbl-customer-created"></label>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label class="col-sm-4 control-label font-bold">Số điện thoại:</label>
                                            <label class="col-sm-8 control-label lbl-customer-phone"></label>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label class="col-sm-4 control-label font-bold">Mã đơn hàng:</label>
                                            <label class="col-sm-8 control-label lbl-customer-code"></label>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label class="col-sm-4 control-label font-bold">Địa chỉ:</label>
                                            <label class="col-sm-8 control-label lbl-customer-address"></label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </th>
                    </tr>
                    <tr style="border: 3px solid #333" class="table-body-head">
                        <th>Tên sản phẩm</th>
                        <th>Mã sản phẩm</th>
                        <th>Hình ảnh</th>
                        <th style="text-align: right;">Đơn giá</th>
                        <th style="text-align: right;">Số lượng</th>
                        <th width="15%" style="text-align: right;">Thành tiền</th>
                        <!-- <th>Tổng giá</th> -->
                    </tr>
                </thead>
                <tfoot>
                </tfoot>
                <tbody style="border: 3px solid #333; position: relative; margin-bottom: 50px;">
                    <tr style="border-top:3px solid #333;">
                        <th colspan="5" style="position: relative;">
                            <div class="row" style="margin-top: 20px;">
                                <div class="col-sm-7">
                                </div>
                                <div class="col-sm-5">
                                    <div class="form-group">
                                        <label class="col-sm-8 control-label font-bold">Tổng cộng:</label>
                                        <label class="col-sm-4 control-label lbl-total-price" style="text-align: right;"></label>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-7">
                                </div>
                                <div class="col-sm-5">
                                    <div class="form-group">
                                        <label class="col-sm-8 control-label font-bold">Tổng số lượng:</label>
                                        <label class="col-sm-4 control-label lbl-total-quantity" style="text-align: right;"></label>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-7">
                                </div>
                                <div class="col-sm-5">
                                    <div class="form-group">
                                        <label class="col-sm-8 control-label font-bold">Chiết khấu:</label>
                                        <label class="col-sm-4 control-label lbl-discount-amount" style="text-align: right;"></label>
                                    </div>
                                </div>
                            </div>
                            <div class="row" style="margin-bottom: 70px;">
                                <div class="col-sm-7">
                                </div>
                                <div class="col-sm-5">
                                    <div class="form-group">
                                        <label class="col-sm-8 control-label font-bold">Phí vận chuyển:</label>
                                        <label class="col-sm-4 control-label lbl-shipping-fee" style="text-align: right;"></label>
                                    </div>
                                </div>
                            </div>
                            <div class="row" style="position: absolute; bottom: 0; right: 70px; width: 150px;">
                                <div class="form-group">
                                    <h4 class="col-sm-8 control-label font-bold">Thành tiền:</h4>
                                    <h4 class="col-sm-4 control-label lbl-price" style="text-align: right;"></h4>
                                </div>
                            </div>
                        </th>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
<!-- END: Print cart