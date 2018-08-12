<form class="form-horizontal">
    <div class="form-group">
        <label class="col-lg-5 control-label" style="text-align: left; padding-right: 0; width: 33.666667%;">Tên khách hàng:</label>
        <label id="customer_name" class="col-lg-7 text-left control-label" style="padding-left: 0; text-align: left;">{{$result['cart']->customer_name}}</label>
    </div>

    <div class="form-group">
        <label class="col-lg-5 control-label" style="text-align: left; padding-right: 0; width: 33.666667%;">Số điện thoại:</label>
        <label id="customer_phone" class="col-lg-7 text-left control-label" style="padding-left: 0; text-align: left;">{{$result['cart']->customer_phone}}</label>
    </div>

    <div class="form-group">
        <label class="col-lg-5 control-label" style="text-align: left; padding-right: 0; width: 33.666667%;">Email:</label>
        <label id="customer_email" class="col-lg-7 text-left control-label" style="padding-left: 0; text-align: left;">{{$result['cart']->customer_email}}</label>
    </div>

    <div class="form-group">
        <label class="col-lg-5 control-label" style="text-align: left; padding-right: 0; width: 33.666667%;">Địa chỉ:</label>
        <label id="customer_address" class="col-lg-7 text-left control-label" style="padding-left: 0; text-align: left;">{{$result['cart']->customer_address}}</label>
    </div>

    <div class="form-group">
        <label class="col-lg-5 control-label" style="text-align: left; padding-right: 0; width: 33.666667%;">Ngày mua:</label>
        <label id="cart_created" class="col-lg-7 text-left control-label" style="padding-left: 0; text-align: left;">{{$result['cart']->created_at->format('H:i d/m/y')}}</label>
    </div>

    <div class="form-group">
        <label class="col-lg-5 control-label" style="text-align: left; padding-right: 0; width: 33.666667%;">Nguồn đơn:</label>
        <div class="col-lg-7" style="padding-left: 0; text-align: left;">
            <select id="i-payment-status-list" name="platform_name" class="form-control">
                <option value="{{NOT_PAYING}}">{{NOT_PAYING_TEXT}}</option>
                <option value="{{PAYING_NOT_ENOUGH}}">{{PAYING_NOT_ENOUGH_TEXT}}</option>
                <option value="{{PAYING_OFF}}">{{PAYING_OFF_TEXT}}</option>
                <option value="{{RECEIVED_PAYMENT}}">{{RECEIVED_PAYMENT_TEXT}}</option>
            </select>
        </div>
    </div>

    <div class="form-group">
        <label class="col-lg-5 control-label" style="text-align: left; padding-right: 0; width: 33.666667%;">Nhà vận chuyển:</label>
        <label id="transport_name" class="col-lg-7 text-left control-label" style="padding-left: 0; text-align: left;">{{$result['cart']->transport_name}}</label>
    </div>

    <div class="form-group">
        <label class="col-lg-5 control-label" style="text-align: left; padding-right: 0; width: 33.666667%;">Mã vận đơn:</label>
        <label id='transport_id' class="col-lg-7 text-left control-label" style="padding-left: 0; text-align: left;">{{$result['cart']->transport_id}}</label>
    </div>

    <div class="form-group">
        <label class="col-lg-5 control-label" style="text-align: left; padding-right: 0; width: 33.666667%;">Mã đơn hàng:</label>
        <label id="code" class="col-lg-7 text-left control-label" style="padding-left: 0; text-align: left;">{{$result['cart']->code}}</label>
    </div>

    <div class="form-group">
        <label class="col-lg-5 control-label" style="text-align: left; padding-right: 0; width: 33.666667%;">Tình trạng thanh toán:</label>
        <div class="col-lg-7" style="padding-left: 0; text-align: left;">
            <select id="i-payment-status-list" name="payment_status" class="form-control m-b">
                {!! $payment_status !!}}
            </select>
        </div>
    </div>

    <div class="form-group">
        <label class="col-lg-5 control-label" style="text-align: left; padding-right: 0; width: 33.666667%;">Tình trạng:</label>
        <div class="col-lg-7" style="padding-left: 0; text-align: left;">
            <select id="i-status-list" class="form-control m-b" name="status">
                {!! $cart_status !!}}
            </select>
        </div>
    </div>

    <div class="form-group">
        <label class="col-lg-5 control-label" style="text-align: left; padding-right: 0; width: 33.666667%;">Tổng cộng:</label>
        <label id="total_price" class="col-lg-7 text-left control-label" style="padding-left: 0; text-align: left;">{{$result['cart']->total_price}}</label>
    </div>

    <div class="form-group">
        <label class="col-lg-5 control-label" style="text-align: left; padding-right: 0; width: 33.666667%;">Phí vận chuyển:</label>
        <label id="shipping_fee" class="col-lg-7 text-left control-label" style="padding-left: 0; text-align: left;">{{$result['cart']->shipping_fee}}</label>
    </div>

    <div class="form-group">
        <label class="col-lg-5 control-label" style="text-align: left; padding-right: 0; width: 33.666667%;">Thuế:</label>
        <label id="vat_amount" class="col-lg-7 text-left control-label" style="padding-left: 0; text-align: left;">{{$result['cart']->vat_amount}}</label>
    </div>

    <div class="form-group">
        <label class="col-lg-5 control-label" style="text-align: left; padding-right: 0; width: 33.666667%;">Tổng chiết khấu:</label>
        <label id="total_discount_amount" class="col-lg-7 text-left control-label" style="padding-left: 0; text-align: left;">{{$result['cart']->total_discount_amount}}</label>
    </div>

    <div class="form-group">
        <label class="col-lg-5 control-label" style="text-align: left; padding-right: 0; width: 33.666667%;">Thành tiền:</label>
        <label id="needed_paid" class="col-lg-7 text-left control-label" style="padding-left: 0; text-align: left;">{{$result['cart']->needed_paid }}</label>
    </div>

    <div class="text-left">
        <h3 class="text-uppercase">thông tin đơn hàng</h3>
    </div>
    <div class="hr-line-dashed"></div>
    <div class="ibox-content m-b">
        <table class="table">
            <thead>
                <tr>
                    <th>Mã đơn hàng</th>
                    <th>Mã sản phẩm</th>
                    <th>Đơn giá</th>
                    <th>Số lượng</th>
                </tr>
            </thead>
            <tfoot>
                <tr>
                    <td colspan="3">Tổng cộng</td>
                    <td><span class="c-total-money"></span></td>
                </tr>
                <tr>
                    <td colspan="3">Phí vận chuyển</td>
                    <td><span class="c-shipping-fee"></span></td>
                </tr>
                <tr>
                    <td colspan="3" style="font-weight: 700;">Thành tiền</td>
                    <td><span class="c-amount"></span></td>
                </tr>
            </tfoot>
            <tbody class="cart-detail-wrapper">
                @foreach ($result['cart_details'] as $cart_detail)
                <tr>
                    <td>{{$cart_detail->code}}</td>
                    <td>{{$cart_detail->product_code}}</td>
                    <td>{{$cart_detail->price}}</td>
                    <td>{{$cart_detail->quantity}}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="form-group">
        <div class="col-lg-offset-2 col-lg-10 text-right">
            <button id="save-cart-info" class="btn btn-sm btn-primary" type="button" onclick="updateCartStatus();">Lưu</button>
        </div>
    </div>
</form>
