@include('admin._partials._print_header')
<!--  BEGIN: Print transport warehourse receive -->
<div id="print-section">
    <div class="row" >
        <!-- BEGIN: Logo -->
        <div class="col-sm-3">
            <img src="{{asset('themes/inspinia/img/logo.png')}}" alt="" width="100%" height="auto">
        </div>
        <!-- END: Logo -->
        <!-- BEGIN: Info company -->
        <div class="col-sm-3">
            <h3>Email</h3>
            <div class="c-email">rampion@gmail.com</div>
        </div>
        <div class="col-sm-3">
            <h3>Số điện thoại</h3>
            <div class="c-phone">0963.755.835</div>
        </div>
        <div class="col-sm-3">
            <h3>Địa chỉ</h3>
            <div class="c-address">11B Nguyễn Kiệm, P.3, Q. Gò Vấp</div>
        </div>
        <!-- END: Address company -->
    </div>
    <hr class="horizontal-line" size="5">
    <div class="row">
        <div class="col-sm-12">
            <h2 class="text-center font-bold text-uppercase" style="font-size: 30px;">phiếu trả hàng</h2>
        </div>
    </div>
    <div style="margin-top: 20px;">
        <div class="row">
            <div class="col-sm-6" >
                <div class="form-group">
                    <label class="col-sm-6 control-label font-bold">Tên người phụ trách:</label>
                    <label class="col-sm-6 control-label lbl-customer-name"></label>
                </div>
            </div>
            <div class="col-sm-6" >
                <div class="form-group">
                    <label class="col-sm-6 control-label font-bold">Ngày trả:</label>
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
                    <label class="col-sm-6 control-label font-bold">Mã trả hàng:</label>
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
        </div>
    </div>
    <div class="col-sm-12" style="border: 3px solid #333; margin-top: 30px;">
        <div class="table-wrapper">
            <table class="table bordered tbl-list-product table-borderless">
                <thead style="border-bottom: 3px solid #333;">
                    <tr class="table-body-head">
                        <th>Tên sản phẩm</th>
                        <th>Mã sản phẩm</th>
                        <th>Màu sắc</th>
                        <th>Kích thước</th>
                        <th style="text-align: right;">Số lượng</th>
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
                    <label class="col-sm-8 control-label font-bold">Tổng số lượng trả:</label>
                    <label class="col-sm-4 control-label lbl-transport-quantity" style="text-align: right;"></label>
                </div>
            </div>
        </div>
        <div class="row" style="margin-bottom: 70px;">
            <div class="col-sm-7">
            </div>
            <div class="col-sm-5">
                <div class="form-group">
                    <label class="col-sm-8 control-label font-bold">Chi phí vận chuyển:</label>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-12">
        <div class="row" style="text-align: center; margin-top: 30px;">
            <div class="col-sm-4">
                <div class="form-group">
                    <label class="col-sm-12 control-label font-bold">Người trả hàng</label>
                    <label class="col-sm-12 control-label">(Ký, ghi rõ họ tên)</label>
                </div>
            </div>
            <div class="col-sm-4">
                <div class="form-group">
                    <label class="col-sm-12 control-label font-bold">Người nhận hàng</label>
                    <label class="col-sm-12 control-label">(Ký, ghi rõ họ tên)</label>
                </div>
            </div>
            <div class="col-sm-4">
                <div class="form-group">
                    <label class="col-sm-12 control-label font-bold">Người lập</label>
                    <label class="col-sm-12 control-label">(Ký, ghi rõ họ tên)</label>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- END: Print transport warehourse receive