<!--  BEGIN: Print cart -->
<div id="print-section">
    <div class="row">
        <div class="col-md-12">
            <!-- BEGIN: Logo -->
            <div class="col-md-8">
                <div class="c-header-logo-wrapper">
                    <h1>
                        <img src="{{asset('themes/inspinia/img/logo.png')}}" alt="">
                    </h1>
                </div>
            </div>
            <!-- END: Logo -->
            <!-- BEGIN: Info company -->
            <div class="col-md-4">
                <div class="c-company-info" style="float: right;">
                    <div class="c-email">rampion@gmail.com</div>
                    <div class="c-phone">0963.755.835</div>
                    <div class="c-address">11B Nguyễn Kiệm, P.3, Q. Gò Vấp</div>
                </div>
            </div>
            <!-- END: Address company -->
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <h2 class="text-center font-bold text-uppercase">đơn hàng</h2>
        </div>
    </div>
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
    <div class="table-wrapper">
        <table class="table bordered tbl-list-product table-borderless">
            <thead>
                <tr>
                    <th>Tên sản phẩm</th>
                    <th>Mã sản phẩm</th>
                    <th>Số lượng</th>
                    <th width="15%" style="text-align: right;">Giá</th>
                    <!-- <th>Tổng giá</th> -->
                </tr>
            </thead>
            <tfoot>
            </tfoot>
            <tbody>
            </tbody>
        </table>
    </div>
</div>
<!-- END: Print cart