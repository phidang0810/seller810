@include('admin._partials._print_header')
<!--  BEGIN: Print transport warehourse receive -->
<div id="print-section">
    <hr class="horizontal-line" size="5">
    <div class="row">
        <div class="col-sm-12">
            <h2 class="text-center font-bold text-uppercase" style="font-size: 30px;">Phiếu nhập hàng</h2>
        </div>
    </div>
    <div style="margin-top: 20px;">
        <div class="row">
            <div class="col-sm-6" >
                <div class="form-group">
                    <label class="col-sm-6 control-label font-bold">Tên người nhập:</label>
                    <label class="col-sm-6 control-label lbl-customer-name"></label>
                </div>
            </div>
            <div class="col-sm-6" >
                <div class="form-group">
                    <label class="col-sm-6 control-label font-bold">Ngày nhập:</label>
                    <label class="col-sm-6 control-label lbl-customer-created"></label>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-6">
                <div class="form-group">
                    <label class="col-sm-6 control-label font-bold">Số điện thoại:</label>
                    <label class="col-sm-6 control-label lbl-customer-phone"></label>
                </div>
            </div>
            <div class="col-sm-6">
                <div class="form-group">
                    <label class="col-sm-6 control-label font-bold">Mã đơn hàng:</label>
                    <label class="col-sm-6 control-label lbl-customer-code"></label>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-6">
                <div class="form-group">
                    <label class="col-sm-6 control-label font-bold">Email:</label>
                    <label class="col-sm-6 control-label lbl-customer-email"></label>
                </div>
            </div>
            <div class="col-sm-6">
                <div class="form-group">
                    <label class="col-sm-6 control-label font-bold">Nhà cung cấp:</label>
                    <label class="col-sm-6 control-label lbl-customer-address"></label>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-12" style="border: 3px solid #333; margin-top: 30px; position: relative;">
        <div class="table-wrapper">
            <table class="table bordered tbl-list-product table-borderless">
                <thead style="border-bottom: 3px solid #333;">
                    <tr class="table-body-head">
                        <th>Tên sản phẩm</th>
                        <th>Mã sản phẩm</th>
                        <th style="text-align: right;">Số lượng</th>
                        <th>Màu sắc</th>
                        <th>Kích thước</th>
                        <th style="text-align: right;">Giá</th>
                        <th style="text-align: right;">Tổng giá</th>
                    </tr>
                </thead>
                <tfoot>
                </tfoot>
                <tbody>
                </tbody>
            </table>
        </div>
        <div class="row">
            <div class="col-sm-7">
            </div>
            <div class="col-sm-5">
                <div class="form-group">
                    <label class="col-sm-8 control-label font-bold">Tổng cộng:</label>
                    <label class="col-sm-4 control-label lbl-transport-total-price" style="text-align: right;"></label>
                </div>
            </div>
        </div>
        <div class="row" style="margin-bottom: 70px;">
            <div class="col-sm-7">
            </div>
            <div class="col-sm-5">
                <div class="form-group">
                    <label class="col-sm-8 control-label font-bold">Tổng số lượng:</label>
                    <label class="col-sm-4 control-label lbl-transport-total-quantity" style="text-align: right;"></label>
                </div>
            </div>
        </div>
        <div class="row" style="position: absolute; bottom: 0; right: 70px; width: 150px;">
            <div class="form-group">
                <h4 class="col-sm-8 control-label font-bold">Thành tiền:</h4>
                <h4 class="col-sm-4 control-label lbl-transport-total" style="text-align: right;"></h4>
            </div>
        </div>
    </div>
    <div class="col-sm-12">
        <div class="row" style="text-align: center; margin-top: 30px;">
            <div class="col-sm-4">
                <div class="form-group">
                    <label class="col-sm-12 control-label font-bold">Người nhập</label>
                    <label class="col-sm-12 control-label">(Ký, ghi rõ họ tên)</label>
                </div>
            </div>
            <div class="col-sm-4">
                <div class="form-group">
                    <label class="col-sm-12 control-label font-bold">Người nhập kho</label>
                    <label class="col-sm-12 control-label">(Ký, ghi rõ họ tên)</label>
                </div>
            </div>
            <div class="col-sm-4">
                <div class="form-group">
                    <label class="col-sm-12 control-label font-bold">Giám đốc</label>
                    <label class="col-sm-12 control-label">(Ký, ghi rõ họ tên)</label>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- END: Print transport warehourse receive