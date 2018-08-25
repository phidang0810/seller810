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
        <label id="cart_created" class="col-lg-7 text-left control-label" style="padding-left: 0; text-align: left;">{{$result['cart']->created_at}}</label>
    </div>

    <div class="form-group">
        <label class="col-lg-5 control-label" style="text-align: left; padding-right: 0; width: 33.666667%;">Nguồn đơn:</label>
        <label id="platform_name" class="col-lg-7 text-left control-label" style="padding-left: 0; text-align: left;">{{$result['cart']->platform_name}}</label>
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
        <label class="col-lg-5 control-label" style="text-align: left; padding-right: 0; width: 33.666667%;">Tình trạng:</label>
        <div class="col-lg-7" style="padding-left: 0; text-align: left;">
            <select id="i-status-list" class="form-control m-b" name="status">
                {!! $cart_status !!}}
            </select>
        </div>
    </div>

    <div class="text-left">
        <h3 class="text-uppercase">thông tin đơn hàng</h3>
    </div>
    <div class="hr-line-dashed"></div>
    <div class="ibox-content m-b">
        <style>
        .table-borderless > tbody > tr > td,
        .table-borderless > tbody > tr > th,
        .table-borderless > tfoot > tr > td,
        .table-borderless > tfoot > tr > th,
        .table-borderless > thead > tr > td,
        .table-borderless > thead > tr > th {
            border: none;
        }

        .table-borderless > tbody > tr:last-child{
            border-bottom: 1px solid #ccc;
        }
    </style>
    <table class="table table-borderless">
        <thead>
            <tr>
                <th>Mã sản phẩm</th>
                <th>Đơn giá</th>
                <th>Số lượng</th>
                <th>Thành tiền</th>
            </tr>
        </thead>
        <tfoot>
            <tr>
                <td colspan="1">Tổng cộng</td>
                <td colspan="3" class="text-right">
                        <!-- <input type="text" name="total_price" placeholder="" class="form-control thousand-number money m-b"
                            value="{{$result['cart']->total_price}}" readonly="readonly" /> -->
                            <span class="thousand-number money m-b">{{($result['cart']->total_price) ? $result['cart']->total_price : 0}}</span>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="1">Thuế</td>
                        <td colspan="3" class="text-right">
                       <!--  <input type="text" name="vat_amount" placeholder="" class="form-control thousand-number money m-b"
                        value="{{$result['cart']->vat_amount}}" readonly="readonly" /> -->
                        <span class="thousand-number money m-b">{{($result['cart']->vat_amount) ? $result['cart']->vat_amount : 0}}</span>
                    </td>
                </tr>
                <tr>
                    <td colspan="1">Phí vận chuyển</td>
                    <td colspan="3" class="text-right">
                        <!-- <input type="text" name="shipping_fee" placeholder="" class="form-control thousand-number money m-b"
                            value="{{$result['cart']->shipping_fee}}" readonly="readonly" /> -->
                            <span class="thousand-number money m-b">{{($result['cart']->shipping_fee) ? $result['cart']->shipping_fee : 0}}</span>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="1">Tổng chiết khấu</td>
                        <td colspan="3" class="text-right">
                        <!-- <input type="text" name="total_discount_amount" placeholder="" class="form-control thousand-number money m-b"
                            value="{{$result['cart']->total_discount_amount}}" readonly="readonly" /> -->
                            <span class="thousand-number money m-b">{{($result['cart']->total_discount_amount) ? $result['cart']->total_discount_amount : 0}}</span>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="1" style="font-weight: 700;">Thành tiền</td>
                        <td colspan="3" class="text-right">
                        <!-- <input type="text" name="price" placeholder="" class="form-control thousand-number money m-b"
                            value="{{$result['cart']->price}}" readonly="readonly" /> -->
                            <span class="thousand-number money m-b" style="font-weight: 700;">{{($result['cart']->price) ? $result['cart']->price : 0}}</span>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="1" style="font-weight: 700;">Đã thanh toán</td>
                        <td colspan="3" class="text-right">
                        <!-- <input type="text" name="paid_amount" placeholder="" class="form-control thousand-number money m-b"
                            value="{{$result['cart']->paid_amount}}" readonly="readonly" /> -->
                            <span class="thousand-number money m-b" style="font-weight: 700;">{{($result['cart']->paid_amount) ? $result['cart']->paid_amount : 0}}</span>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="1" style="font-weight: 700;">Thanh toán thêm</td>
                        <td colspan="3" class="text-right">
                            <input type="text" name="pay_amount" placeholder="" class="form-control input-thousand-number money m-b"
                            value=""/>
                            <!-- <span class="thousand-number money m-b">{{$result['cart']->total_price}}</span> -->
                        </td>
                    </tr>
                    <tr>
                        <td colspan="1">Còn lại</td>
                        <td colspan="3" class="text-right">
                        <!-- <input type="text" name="needed_paid" placeholder="" class="form-control thousand-number money m-b"
                            value="{{$result['cart']->needed_paid}}" readonly="readonly" /> -->
                            <span class="thousand-number money m-b" id="needed_paid">{{$result['cart']->needed_paid}}</span>
                        </td>
                    </tr>
                </tfoot>
                <tbody class="cart-detail-wrapper">
                    @foreach ($result['cart_details'] as $cart_detail)
                    <tr>
                        <td colspan="1">{{$cart_detail->product_code}}</td>
                        <td class="thousand-number money text-right" colspan="1">{{$cart_detail->price}}</td>
                        <td class="thousand-number text-right" colspan="1">{{$cart_detail->quantity}}</td>
                        <td class="thousand-number money text-right" colspan="1">{{$cart_detail->total_price}}</td>
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
    <script type="text/javascript">
        var paid_amount = parseInt('{{($result['cart']->paid_amount) ? $result['cart']->paid_amount : 0}}');
        var price = parseInt('{{$result['cart']->price}}');

        function formatPrice(){
            // Format prices
            $('.input-thousand-number money').toArray().forEach(function(field){
                new Cleave(field, {
                    numeral: true,
                    numeralThousandsGroupStyle: 'thousand'
                });
            });
        }
        $(document).ready(function(){
            formatPrice();
            
            $('input[name="pay_amount"]').on('change', function(){
                var pay_amount = ($('input[name="pay_amount"]').val()) ? $('input[name="pay_amount"]').val() : 0;
                console.log(pay_amount);
                if (pay_amount) {
                    if (pay_amount.includes(",")) {
                        pay_amount = parseInt(pay_amount.replace(/\,/g, ""));
                    }else{
                        pay_amount = parseInt(pay_amount);
                    }
                }
                
                
                $('#needed_paid').text(price - paid_amount - pay_amount);
                $('#needed_paid').simpleMoneyFormat();
                if($('#needed_paid').text() != '' || $('#needed_paid').text() != null){
                    $('#needed_paid').append(" VNĐ");
                }
            });
            // formatPrice();
            $('.thousand-number').simpleMoneyFormat();
            if($('.thousand-number.money').text() != '' || $('.thousand-number.money').text() != null){
                $('.thousand-number.money').append(" VNĐ");
            }
            // $('.thousand-number money').closest('td').css("text-align", "right");

        });
    </script>